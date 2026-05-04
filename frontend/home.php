

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="stylesheet" href="d.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- thêm icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header>
    <div class="logo"><a href="home.php">KEWE</a></div>

    <nav>
        <ul>
            <li>
                <a href="#">Sách</a>
                <div class="mega-menu">
                    <a href="taichinhcanhan.php">Tài chính cá nhân</a>
                    <a href="Trinhtham.php">Trinh thám</a>
                    <a href="tuyduysangtao.php">Tư duy sáng tạo</a>
                </div>
            </li>

            <li>
                <a href="#">Truyện thiếu nhi</a>
                <div class="mega-menu">
                    <a href="truyenthieunhi.php">Truyện thiếu nhi</a>
                    <a href="truyennguoilon.php">Truyện người lớn</a>
                </div>
            </li>

            <li>
                <a href="#">Truyện kinh dị</a>
                <div class="mega-menu">
                    <a href="#">Ma</a>
                    <a href="#">Trinh thám</a>
                </div>
            </li>
        </ul>
    </nav>

    <div class="buttons">

        <form action="timkiem.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Tìm tên truyện..." required>
            <button type="submit" class="btn-timkiem">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </form>

        <?php if (isset($_SESSION['user'])): ?>
            <span>Xin chào <?= $_SESSION['user'] ?></span>
        <?php else: ?>
            <button class="btn-dangky" id="openRegisterModal">Đăng ký</button> 
            <button class="btn-dangnhap" id="openRegisterModal2">Đăng nhập</button> 
        <?php endif; ?>

    </div>
</header>

<!-- banner -->
<section class="banner">
    <div class="swiper bannerSwiper">
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <img src="ba2.webp">
            </div>

            <div class="swiper-slide">
                <img src="ba.webp">
            </div>

            <div class="swiper-slide">
                <img src="sach2.jpg">
            </div>

        </div>
    </div>
</section>

<!-- content -->
<section class="content">
    <h2>Tất cả sách</h2>

    <div class="welcome">
        <div class="container">

            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): ?>

                    <div class="book">

                        <!-- link đọc -->
                        <a href="read_story.php?story_id=<?= $book['id'] ?>">
                            <img src="<?= $book['cover'] ?>">
                            <div class="title">
                                <?= htmlspecialchars($book['title']) ?>
                            </div>
                        </a>

                        <!-- nút lưu -->
                        <form action="luutruyen.php" method="POST">
                            <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </form>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <p>Không có truyện</p>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- footer -->
<footer>
    <div class="footer-top">
        <div class="footer-logo">
            <h2>WEKE</h2>
            <p>Công ty cổ phần sách điện tử Weke</p>
            <p><i class="fa fa-phone"></i> 0877736289</p>
            <p><i class="fa fa-envelope"></i> Support@weke.vn</p>
        </div>
    </div>

    <div class="footer-bottom">
        Công ty Cổ phần Sách điện tử Weke – Hà Nội
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
const bannerSwiper = new Swiper(".bannerSwiper", {
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    speed: 800,
});
</script>

</body>
</html>