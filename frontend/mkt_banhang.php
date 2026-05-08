
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Chính Cá Nhân</title>
    <link rel="stylesheet" href="css/d.css">
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
                    <div class="item"><a href="tho_tanvan.php">Thơ - Tản văn</a> </div>
                <div class="item"><a href="trinhtham.php">Trinh thám - Kinh dị</a> <span>NEW</span></div>
                <div class="item"><a href="mkt_banhang.php">Marketing - Bán hàng</a> <span>NEW</span></div>
                <div class="item"><a href="taichinhcanhan.php">Tài chính cá nhân</a> <span>NEW</span></div>
                <div class="item"><a href="pt_canhan.php">Phát triển cá nhân</a> <span>NEW</span></div>
                <div class="item"><a href="doanh_nhan.php">Doanh nhân - Bài học KD</a> <span>NEW</span></div>
                <div class="item"><a href="suckhoe_lamdep.php">Sức khỏe - Làm đẹp</a></div>
                <div class="item"><a href="khoahoc_congnghe.php">Khoa học - Công nghệ</a></div>
                <div class="item"><a href="tuduy_sangtao.php">Tư duy sáng tạo</a> <span>NEW</span></div>
                <div class="item"><a href="giaoduc_vanhoa.php">Giáo dục - Văn hóa & Xã</a></div>
                <div class="item"><a href="nghe_thuat_song.php">Nghệ thuật sống</a> <span>NEW</span></div>
                <div class="item"><a href="tamlinh.php">Tâm linh - Tôn giáo</a></div>
                <div class="item"><a href="chungkhoan_bds_dautu.php">Chứng khoán - BĐS - Đầu tư</a></div>
                <div class="item"><a href="sach_ngoai_van.php">Sách Ngoại văn</a> <span>NEW</span></div>
                </div>
            </li>

            <li>
                <a href="#">Truyện</a>
                <div class="mega-menu">
                    <div class="item"><a href="nam.php">Nam</a></div>
                <div class="item"><a href="nu.php">Nữ</a></div>
                <div class="item"><a href="xuyenkhong.php">Xuyên không</a></div>
                <div class="item"><a href="truyenma.php">Truyện ma</a></div>
                <div class="item"><a href="tinhcam.php">Tình cảm</a></div>
                <div class="item"><a href="ngungon.php">Ngụ ngôn</a></div>
                <div class="item"><a href="codai.php">Cổ đại</a></div>
                <div class="item"><a href="thieunhi.php">Thiếu nhi</a></div>
                <div class="item"><a href="haihuoc.php">Hài </a></div>
                <div class="item"><a href="hanhdong.php">Hành động</a></div>
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