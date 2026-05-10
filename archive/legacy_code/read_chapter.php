<?php
include 'db_connect.php';

if (!isset($_GET['chapter_id'])) {
    die("Thiếu ID chương");
}

$chapter_id = intval($_GET['chapter_id']);

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

// Logic lấy ID chương trước và chương sau để điều hướng
$story_id = $chapter['story_id'];
$current_no = $chapter['chapter_number'];

$prev_q = mysqli_query($con, "SELECT id FROM chapters WHERE story_id = $story_id AND chapter_number < $current_no ORDER BY chapter_number DESC LIMIT 1");
$prev_chap = mysqli_fetch_assoc($prev_q);

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
    <style>
        :root {
            --bg-page:black; /* Màu giấy cổ điển giống ảnh mẫu */
            --text-color:white;
            --btn-green: #5cb85c;
            --border-color: #b5b3acff;
        }

        body {
            background-color:black;
            margin: 0;
            font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;
            color: var(--text-color);
        }

        /* Thanh điều hướng trên cùng */
        .navbar {
            background: #101112ff;
            padding: 20px 20px;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 14px;
        }

        .navbar a { color: #dfe3e5ff; text-decoration: none; }
        .navbar a:hover { color: white; }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: var(--bg-page);
            padding: 40px 60px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        .header-section {
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 30px;
            padding-bottom: 20px;
        }

        .header-section h1 {
            color: #27ae60;
            margin: 0;
            text-transform: uppercase;
            font-size: 24px;
        }

        .header-section h2 {
            font-size: 18px;
            font-weight: normal;
            margin: 10px 0;
            color: #7f8c8d;
        }

        /* Nút điều hướng */
        .navigation-tools {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .btn-nav {
            background: gray;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-nav:hover { background: #449d44; }
        .btn-nav.disabled { background: #ccc; pointer-events: none; }
        
        
        /* Nội dung truyện */
        .chapter-content {
            font-size: 22px; /* Kích thước chữ tối ưu để đọc */
            line-height: 1.8;
            text-align: justify;
            word-wrap: break-word;
        }

        /* Decor họa tiết giống ảnh mẫu */
        .ornament {
            text-align: center;
            margin: 30px 0;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container { padding: 20px; margin: 0; }
            .chapter-content { font-size: 18px; }
        }
    </style>
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

    <div class="header-section" style="border-top: 1px solid var(--border-color); border-bottom: none; margin-top: 40px; padding-top: 20px;">
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