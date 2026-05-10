<?php
$con = mysqli_connect("localhost", "root", "", "db_");
$keyword = isset($_GET['q']) ? mysqli_real_escape_string($con, $_GET['q']) : '';

// Truy vấn
$sql = "SELECT * FROM stories WHERE title LIKE '%$keyword%'";
$result = mysqli_query($con, $sql);
$count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm: <?php echo htmlspecialchars($keyword); ?></title>
    <link rel="stylesheet" href="search.css"> </head>
<body>

<div class="container">
    <div class="search-box">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Tìm kiếm truyện..." value="<?php echo htmlspecialchars($keyword); ?>">
        </form>
    </div>

    <div class="result-info">
        <h1>Kết quả tìm kiếm cho từ "<span><?php echo htmlspecialchars($keyword); ?></span>"</h1>
        <p style="color: #a1a1aa; margin-bottom: 25px;">Tìm được <?php echo $count; ?> kết quả</p>
    </div>

    <div class="tabs">
        <div class="tab-item active">Tất cả</div>
        <div class="tab-item">Sách </div>
        <div class="tab-item">Truyện</div>
    </div>

    <div class="story-grid">
        <?php if ($count > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="story-card">
                    <div class="cover-wrapper">
                        <img src="<?php echo $row['cover']; ?>" alt="Cover">
                        
                    </div>
                    <div class="story-title">
                        <?php echo $row['title']; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Không tìm thấy kết quả nào.</p>
        <?php endif; ?>
    </div>
    <div class="back-home">
        <a href="home.php">
             Quay lại Trang chủ
        </a>
    </div>
</div>

</body>
</html>