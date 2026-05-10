<?php
include 'db_connect.php';

if (!isset($_GET['story_id'])) {
    die("Thiếu ID truyện");
}

$story_id = intval($_GET['story_id']);

// Lấy thông tin truyện
$story_sql = "SELECT * FROM stories WHERE id = $story_id";
$story_q = mysqli_query($con, $story_sql);
$story = mysqli_fetch_assoc($story_q);

if (!$story) {
    die("Truyện không tồn tại");
}

// Lấy danh sách chương
$chap_sql = "
    SELECT * FROM chapters
    WHERE story_id = $story_id
    ORDER BY chapter_number ASC
";
$chapters = mysqli_query($con, $chap_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $story['title']; ?> </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #0b0b0b;
            --bg-card: #141414;
            --green: #1ed760;
            --text: #e0e0e0;
            --text-dim: #909090;
            --border: #222;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            display: flex;
        }

        /* SIDEBAR SIÊU GỌN */
        .sidebar {
            width: 70px; /* Thu hẹp sidebar */
            background: #000;
            height: 100vh;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            border-right: 1px solid var(--border);
        }

        .sidebar a {
            color: var(--text-dim);
            font-size: 20px;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .sidebar a:hover, .sidebar a.active { color: var(--green); }

        /* MAIN CONTENT */
        .main {
            flex: 1;
            padding: 30px 50px;
            max-width: 1200px;
        }

        /* HEADER GỌN GÀNG */
        .story-info-header {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
            align-items: flex-end;
        }

        .cover-mini {
            width: 160px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .details h1 {
            font-size: 28px;
            margin: 0 0 10px 0;
        }

        .meta {
            color: var(--text-dim);
            font-size: 13px;
            margin-bottom: 15px;
        }

        .desc-short {
            font-size: 14px;
            color: var(--text-dim);
            max-width: 600px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn-action {
            background: var(--green);
            color: #000;
            padding: 8px 25px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
        }

        /* LIST CHƯƠNG TỐI GIẢN */
        .chapter-container {
            margin-top: 20px;
        }

        .chapter-header {
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--text-dim);
            display: flex;
            justify-content: space-between;
        }

        .chapter-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 10px;
            border-bottom: 1px solid #181818;
            text-decoration: none;
            color: var(--text);
            font-size: 14px;
            transition: 0.2s;
        }

        .chapter-row:hover {
            background: #1a1a1a;
            color: var(--green);
        }

        .chapter-row span:last-child {
            color: var(--text-dim);
            font-size: 12px;
        }

        /* Loại bỏ dấu chấm tròn list */
        .list-clean { padding: 0; margin: 0; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="home.php" title="Trang chủ"><i class="fa-solid fa-house"></i></a>
        <a href="tusach.php" class="active" title="Tủ sách"><i class="fa-solid fa-book"></i></a>
        <a href="taikhoan.php" title="Tài khoản"><i class="fa-solid fa-circle-user"></i></a>
    </aside>

    <main class="main">
        <div class="story-info-header">
            <img src="<?php echo htmlspecialchars($story['cover']); ?>" class="cover-mini">
            <div class="details">
               
                <h1><?php echo htmlspecialchars($story['title']); ?></h1>
                <div class="desc-short">
                    <?php echo mb_strimwidth(htmlspecialchars($story['description']), 0, 200, "..."); ?>
                </div>
                
                <?php 
                    mysqli_data_seek($chapters, 0);
                    $first = mysqli_fetch_assoc($chapters);
                    if ($first): 
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
                        <span><i class="fa-regular fa-clock"></i> Cập nhật</span>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

</body>
</html>