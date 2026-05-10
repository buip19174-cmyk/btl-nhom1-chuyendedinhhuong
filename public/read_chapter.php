<?php
require_once dirname(__DIR__) . '/includes/db_connection.php';

if (!isset($_GET['chapter_id'])) {
    die("Thiếu ID chương");
}

$chapter_id = intval($_GET['chapter_id']);


mysqli_query($con, "UPDATE chapters SET view_count = view_count + 1 WHERE id = $chapter_id");

$sql = "
    SELECT c.*, s.title AS story_title
    FROM chapters c
    JOIN stories s ON c.story_id = s.id
    WHERE c.id = $chapter_id
";
$q = mysqli_query($con, $sql);
$chapter = mysqli_fetch_assoc($q);

if (!$chapter) {
    die("Chương không tồn tại");
}

$story_id = $chapter['story_id'];
$current_no = $chapter['chapter_number'];

// Tìm ID chương trước
$prev_q = mysqli_query($con, "SELECT id FROM chapters WHERE story_id = $story_id AND chapter_number < $current_no ORDER BY chapter_number DESC LIMIT 1");
$prev_chap = mysqli_fetch_assoc($prev_q);

// Tìm ID chương sau
$next_q = mysqli_query($con, "SELECT id FROM chapters WHERE story_id = $story_id AND chapter_number > $current_no ORDER BY chapter_number ASC LIMIT 1");
$next_chap = mysqli_fetch_assoc($next_q);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $chapter['story_title']; ?> - Chương <?php echo $chapter['chapter_number']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/read_chaper.css">
</head>
<body>

<nav class="navbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span>/</span>
    <a href="read_story.php?story_id=<?php echo $chapter['story_id']; ?>"><?php echo $chapter['story_title']; ?></a>
</nav>

<div class="container">
    <div class="header-section">
        <h1><?php echo htmlspecialchars($chapter['story_title']); ?></h1>
        <h2>Chương <?php echo $chapter['chapter_number']; ?>: <?php echo htmlspecialchars($chapter['title']); ?></h2>

        <div class="navigation-tools">
            <a href="read_chapter.php?chapter_id=<?php echo $prev_chap['id'] ?? '#'; ?>" 
               class="btn-nav <?php echo !$prev_chap ? 'disabled' : ''; ?>">
               <i class="fa-solid fa-chevron-left"></i> Chương trước
            </a>

            <a href="read_chapter.php?chapter_id=<?php echo $next_chap['id'] ?? '#'; ?>" 
               class="btn-nav <?php echo !$next_chap ? 'disabled' : ''; ?>">
               Chương sau <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <div class="chapter-content">
        <?php echo nl2br(htmlspecialchars($chapter['content'])); ?>
    </div>

    <div class="header-section" style="border-top: 1px solid #222; border-bottom: none; margin-top: 40px; padding-top: 20px;">
        <div class="navigation-tools">
            <a href="read_chapter.php?chapter_id=<?php echo $prev_chap['id'] ?? '#'; ?>" 
               class="btn-nav <?php echo !$prev_chap ? 'disabled' : ''; ?>">
               Chương trước
            </a>
            <a href="read_chapter.php?chapter_id=<?php echo $next_chap['id'] ?? '#'; ?>"
               class="btn-nav <?php echo !$next_chap ? 'disabled' : ''; ?>">
               Chương sau
            </a>
        </div>
    </div>
</div>

</body>
</html>