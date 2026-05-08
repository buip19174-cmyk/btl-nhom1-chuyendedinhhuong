<?php
include 'db_connect.php';

if (!isset($_GET['story_id'])) {
    die("Thiếu ID truyện");
}

$story_id = intval($_GET['story_id']);

// 1. Cập nhật lượt xem
mysqli_query($con, "UPDATE stories SET view_count = view_count + 1 WHERE id = $story_id");

// 2. Lấy thông tin truyện + Tên tác giả (Sửa lỗi JOIN)
$story_sql = "SELECT s.*, a.name as author_name 
              FROM stories s 
              JOIN authors a ON s.author_id = a.id 
              WHERE s.id = $story_id";
$story_q = mysqli_query($con, $story_sql);
$story = mysqli_fetch_assoc($story_q);

if (!$story) {
    die("Truyện không tồn tại");
}

// 3. Lấy danh sách chương
$chap_sql = "SELECT * FROM chapters WHERE story_id = $story_id ORDER BY chapter_number ASC";
$chapters = mysqli_query($con, $chap_sql);
?>

<main class="main">
        <div class="story-info-header">
            <img src="<?php echo htmlspecialchars($story['cover_image']); ?>" class="cover-mini">
            <div class="details">
                <h1><?php echo htmlspecialchars($story['title']); ?></h1>
                <p class="meta">Tác giả: <?php echo htmlspecialchars($story['author_name']); ?> | Trạng thái: <?php echo $story['status']; ?></p>
                
                <div class="desc-short">
                    <?php echo mb_strimwidth(htmlspecialchars($story['description']), 0, 200, "..."); ?>
                </div>
                
                <?php 
                    if (mysqli_num_rows($chapters) > 0): 
                        mysqli_data_seek($chapters, 0);
                        $first = mysqli_fetch_assoc($chapters);
                ?>
                    <a href="read_chapter.php?chapter_id=<?php echo $first['id']; ?>" class="btn-action">Bắt đầu đọc</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="chapter-container">
            <div class="chapter-header">
                <span>DANH SÁCH CHƯƠNG (<?php echo mysqli_num_rows($chapters); ?>)</span>
                <span>MỚI NHẤT <i class="fa-solid fa-arrow-down-short-wide"></i></span>
            </div>

            <div class="list-clean">
                <?php 
                mysqli_data_seek($chapters, 0);
                while ($ch = mysqli_fetch_assoc($chapters)): 
                ?>
                    <a href="read_chapter.php?chapter_id=<?php echo $ch['id']; ?>" class="chapter-row">
                        <span>Chương <?php echo $ch['chapter_number']; ?>: <?php echo htmlspecialchars($ch['title']); ?></span>
                        <span><i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($ch['updated_at'])); ?></span>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </main>