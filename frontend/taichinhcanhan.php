
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Chính Cá Nhân</title>
    <link rel="stylesheet" href="d.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
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
                        <a href="#">Truyện thiếu Nhi</a>
                        <div class="mega-menu">
                            <a href="truyenthieunhi.php">Truyện thiếu nhi</a>
                            <a href="truyennguoilon.php">Truyện người lớn</a>
                        </div>
                    </li>
                    <li>
                        <a href="#">Truyện kinh dị</a>
                        <div class="mega-menu">
                            <a href="contact.html">Ma</a>
                            <a href="contact.html">Trinh thám</a>
                        </div>
                    </li>
                   
                </ul>
            </nav>
            <div class="buttons">
                <input type="text" placeholder="Search...">
                <button type="submit">Tìm Kiếm</button>
                <button type="button">Đăng nhập</button>
                <button type="button">Đăng kí</button>  

            </div>

        </div>
    </header>
    <section class="hero">
    <div class="hero-left">
        <p class="tag">WEKE ĐỀ XUẤT</p>
        <h1>Tự do tài chính từ bên trong</h1>
        <p class="desc">
            Học cách quản lý tiền bạc, kiểm soát tài chính cá nhân và sống thoải mái hơn.
        </p>
        <button class="read-btn">📖 Đọc sách</button>
    </div>

    <div class="swiper mySwiper">
    <div class="swiper-wrapper">

        <div class="swiper-slide">
            <img src="sach2.jpg">
        </div>

        <div class="swiper-slide">
            <img src="sach2.jpg">
        </div>

        <div class="swiper-slide">
            <img src="sach2.jpg">
        </div>

        <div class="swiper-slide">
            <img src="sach2.jpg">
        </div>

    </div>
</div>
</section>
<section class="content">
    <div class="welcome">
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
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    centeredSlides: true,
    loop: true,
    spaceBetween: 30,
    effect: "coverflow",
    coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 100,
        modifier: 2,
        slideShadows: false,
    },
});
</script>
</body>
</html>