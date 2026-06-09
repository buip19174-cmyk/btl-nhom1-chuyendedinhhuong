<?php
/**
 * Template dùng chung cho tất cả trang danh mục
 * Biến cần khai báo trước khi include:
 *   $page_title   — tiêu đề trang
 *   $category_key — giá trị description trong DB
 *   $hero_desc    — mô tả ngắn hiển thị trong hero
 */
session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';

$cat = mysqli_real_escape_string($con, $category_key);
$sql = "SELECT id, title, cover FROM stories WHERE description = '$cat' LIMIT 18";
$result = mysqli_query($con, $sql);
$books = [];
while ($row = mysqli_fetch_assoc($result)) $books[] = $row;

$hero_books = array_slice($books, 0, 5);
$first_book = $books[0] ?? null;

$saved_story_ids = [];
if (isset($_SESSION['user_id'])) {
    $sv_q = mysqli_query($con, "SELECT story_id FROM user_stories WHERE user_id=" . intval($_SESSION['user_id']));
    while ($sv = mysqli_fetch_assoc($sv_q)) $saved_story_ids[] = $sv['story_id'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?> — KEWE</title>
<link rel="stylesheet" href="css/user.css">
<link rel="stylesheet" href="css/d.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/category.css">
<link rel="stylesheet" href="css/search-ajax.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo"><a href="home.php">KEWE</a></div>
    <nav>
        <ul>
            <li>
                <a href="#">Sách <i class="fa-solid fa-chevron-down nav-chevron"></i></a>
                <div class="mega-menu">
                    <div class="item"><a href="tho_tanvan.php"><i class="fa-solid fa-feather"></i>Thơ - Tản văn</a></div>
                    <div class="item"><a href="trinhtham.php"><i class="fa-solid fa-magnifying-glass"></i>Trinh thám - Kinh dị</a> <span>NEW</span></div>
                    <div class="item"><a href="mkt_banhang.php"><i class="fa-solid fa-chart-line"></i>Marketing - Bán hàng</a> <span>NEW</span></div>
                    <div class="item"><a href="taichinhcanhan.php"><i class="fa-solid fa-coins"></i>Tài chính cá nhân</a></div>
                    <div class="item"><a href="pt_canhan.php"><i class="fa-solid fa-seedling"></i>Phát triển cá nhân</a></div>
                    <div class="item"><a href="doanh_nhan.php"><i class="fa-solid fa-briefcase"></i>Doanh nhân - Bài học KD</a></div>
                    <div class="item"><a href="suckhoe_lamdep.php"><i class="fa-solid fa-heart-pulse"></i>Sức khỏe - Làm đẹp</a></div>
                    <div class="item"><a href="khoahoc_congnghe.php"><i class="fa-solid fa-flask"></i>Khoa học - Công nghệ</a></div>
                    <div class="item"><a href="tuduy_sangtao.php"><i class="fa-solid fa-lightbulb"></i>Tư duy sáng tạo</a></div>
                    <div class="item"><a href="giaoduc_vanhoa.php"><i class="fa-solid fa-graduation-cap"></i>Giáo dục - Văn hóa</a></div>
                    <div class="item"><a href="nghe_thuat_song.php"><i class="fa-solid fa-palette"></i>Nghệ thuật sống</a></div>
                    <div class="item"><a href="tamlinh.php"><i class="fa-solid fa-yin-yang"></i>Tâm linh - Tôn giáo</a></div>
                    <div class="item"><a href="chungkhoan_bds_dautu.php"><i class="fa-solid fa-building-columns"></i>Chứng khoán - BĐS</a></div>
                    <div class="item"><a href="sach_ngoai_van.php"><i class="fa-solid fa-globe"></i>Sách Ngoại văn</a></div>
                </div>
            </li>
            <li>
                <a href="#">Truyện <i class="fa-solid fa-chevron-down nav-chevron"></i></a>
                <div class="mega-menu">
                    <div class="item"><a href="nam.php"><i class="fa-solid fa-mars"></i>Nam</a></div>
                    <div class="item"><a href="nu.php"><i class="fa-solid fa-venus"></i>Nữ</a></div>
                    <div class="item"><a href="xuyenkhong.php"><i class="fa-solid fa-clock-rotate-left"></i>Xuyên không</a></div>
                    <div class="item"><a href="truyenma.php"><i class="fa-solid fa-ghost"></i>Truyện ma</a></div>
                    <div class="item"><a href="tinhcam.php"><i class="fa-solid fa-heart"></i>Tình cảm</a></div>
                    <div class="item"><a href="ngungon.php"><i class="fa-solid fa-dragon"></i>Ngụ ngôn</a></div>
                    <div class="item"><a href="codai.php"><i class="fa-solid fa-scroll"></i>Cổ đại</a></div>
                    <div class="item"><a href="thieunhi.php"><i class="fa-solid fa-child"></i>Thiếu nhi</a></div>
                    <div class="item"><a href="haihuoc.php"><i class="fa-solid fa-face-laugh"></i>Hài hước</a></div>
                    <div class="item"><a href="hanhdong.php"><i class="fa-solid fa-bolt"></i>Hành động</a></div>
                </div>
            </li>
        </ul>
    </nav>
    <div class="buttons header-actions">
         <div class="buttons">
        <div class="search-form-wrap">
            <form action="timkiem.php" method="GET" class="search-form" data-ajax-search>
                <input type="text" name="q" placeholder="Tìm sách, truyện..." autocomplete="off">
                <button type="submit" class="btn-timkiem"><i class="fas fa-search"></i> Tìm</button>
            </form>
        </div>
    </div>
    <div class="user-area">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="user-profile" id="userProfile">
                <i class="fas fa-caret-down"></i>
                <i class="fa-solid fa-user"></i>
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-info">
                        <div class="info-text"><strong><?= $_SESSION['username'] ?></strong></div>
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <ul class="dropdown-menu-list">
                        <li><a href="taikhoan.php"><i class="fas fa-user-cog"></i> Tài khoản</a></li>
                        <li><a href="tusach.php"><i class="fas fa-book"></i> Tủ sách cá nhân</a></li>
                        <li><a href="napcoin.php"><i class="fas fa-coins"></i> Nạp Coin</a></li>
                        <hr>
                        <li><a href="../backend/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <button class="btn-dangky" id="openRegisterModal">Đăng ký</button>
            <button class="btn-dangnhap" id="openRegisterModal2">Đăng nhập</button>
        <?php endif; ?>
    </div>
</header>

<!-- HERO BANNER - text trái + swiper phải -->
<section class="cat-banner">
    <div class="cat-banner-bg"></div>
    <div class="cat-banner-inner">
        <div class="cat-banner-copy">
            <span class="slide-tag"><i class="fa-solid fa-bookmark"></i> <?= htmlspecialchars($page_title) ?></span>
            <h1 class="slide-title" id="heroTitle">
                <?= htmlspecialchars($first_book['title'] ?? $page_title) ?>
            </h1>
            <p class="slide-desc"><?= htmlspecialchars($hero_desc) ?></p>
            <div class="slide-actions">
                <a href="<?= $first_book ? '../backend/read_story.php?story_id=' . $first_book['id'] : '#' ?>" class="slide-btn" id="heroReadBtn">
                    <i class="fa-solid fa-book-open"></i> Đọc ngay
                </a>
                <?php if ($first_book): ?>
                <form method="POST" action="luutruyen.php" class="cat-save-form">
                    <input type="hidden" name="story_id" value="<?= $first_book['id'] ?>" id="heroSaveId">
                    <button type="submit" class="slide-btn-outline"><i class="fa-solid fa-heart"></i> Lưu</button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Swiper ảnh bìa bên phải -->
        <div class="cat-banner-showcase">
            <?php if (!empty($hero_books)): ?>
            <div class="swiper heroSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($hero_books as $b): ?>
                    <div class="swiper-slide"
                         data-title="<?= htmlspecialchars($b['title'], ENT_QUOTES) ?>"
                         data-story-id="<?= $b['id'] ?>"
                         data-url="../backend/read_story.php?story_id=<?= $b['id'] ?>">
                        <a href="../backend/read_story.php?story_id=<?= $b['id'] ?>">
                            <img src="../code/images/<?= htmlspecialchars($b['cover']) ?>"
                                 alt="<?= htmlspecialchars($b['title']) ?>"
                                 onerror="this.src='img/sach2.jpg'">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- BOOK GRID -->
<section class="home-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-layer-group"></i> <?= htmlspecialchars($page_title) ?></h2>
        <span class="count"><?= count($books) ?> đầu sách</span>
    </div>

    <div class="book-grid">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
            <div class="book-card">
                <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>">
                    <img src="../code/images/<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" onerror="this.src='img/sach2.jpg'">
                </a>
                <div class="book-card-body">
                    <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="book-card-title"><?= htmlspecialchars($book['title']) ?></a>
                </div>
                <div class="book-card-footer">
                    <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="btn-read-sm"><i class="fa-solid fa-book-open"></i> Đọc</a>
                    <form action="luutruyen.php" method="POST">
                        <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                        <?php $is_saved_book = in_array($book['id'], $saved_story_ids); ?>
                        <button type="submit" class="btn-save-sm <?= $is_saved_book ? 'saved' : '' ?>" title="<?= $is_saved_book ? 'Đã lưu' : 'Lưu' ?>">
                            <i class="fa-<?= $is_saved_book ? 'solid' : 'regular' ?> fa-heart"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-books">
                <i class="fa-solid fa-book-open"></i>
                <p>Chưa có sách trong danh mục này</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="brand-name">KEWE</div>
            <p>Nền tảng đọc sách & truyện online hàng đầu Việt Nam.</p>
            <p><i class="fa fa-phone"></i> 0877 736 289</p>
            <p><i class="fa fa-envelope"></i> support@kewe.vn</p>
        </div>
        <div class="footer-col">
            <h4>Thể loại sách</h4>
            <a href="taichinhcanhan.php">Tài chính cá nhân</a>
            <a href="pt_canhan.php">Phát triển cá nhân</a>
            <a href="doanh_nhan.php">Doanh nhân</a>
            <a href="khoahoc_congnghe.php">Khoa học - Công nghệ</a>
        </div>
        <div class="footer-col">
            <h4>Thể loại truyện</h4>
            <a href="tinhcam.php">Tình cảm</a>
            <a href="trinhtham.php">Trinh thám</a>
            <a href="xuyenkhong.php">Xuyên không</a>
            <a href="hanhdong.php">Hành động</a>
        </div>
        <div class="footer-col">
            <h4>Tài khoản</h4>
            <a href="taikhoan.php">Thông tin tài khoản</a>
            <a href="tusach.php">Tủ sách cá nhân</a>
            <a href="napcoin.php">Nạp Coin</a>
        </div>
    </div>
    <div class="footer-bottom"><span>© 2025 KEWE</span> — Công ty Cổ phần Sách điện tử Kewe – Hà Nội</div>
</footer>

<!-- Modals -->
<div id="registerModal" class="modal hidden-modal"><?php include 'dangky_form.php'; ?></div>
<div id="loginModal" class="modal hidden-modal"><?php include 'dangnhap_form.php'; ?></div>

<?php if (!empty($register_message)): ?>
<script>alert("<?= addslashes($register_message) ?>");</script>
<?php endif; ?>
<?php if (!empty($message)): ?>
<script>alert("<?= addslashes($message) ?>");</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
if (document.querySelector(".heroSwiper")) {
new Swiper(".heroSwiper", {
    slidesPerView: 3,
    centeredSlides: true,
    loop: <?= count($hero_books) >= 3 ? 'true' : 'false' ?>,
    spaceBetween: 20,
    autoplay: { delay: 2500, disableOnInteraction: false },
    effect: "coverflow",
    grabCursor: true,
    coverflowEffect: { rotate: 0, stretch: 0, depth: 120, modifier: 2, slideShadows: false },
    on: {
        slideChange: function () {
            const activeSlide = this.slides[this.activeIndex];
            if (!activeSlide) return;
            const title   = activeSlide.dataset.title   || '';
            const storyId = activeSlide.dataset.storyId || '';
            const url     = activeSlide.dataset.url     || '#';
            const titleEl = document.getElementById('heroTitle');
            const btnEl   = document.getElementById('heroReadBtn');
            const saveId  = document.getElementById('heroSaveId');
            if (titleEl && title) {
                titleEl.style.opacity = '0';
                titleEl.style.transform = 'translateY(8px)';
                setTimeout(() => {
                    titleEl.textContent = title;
                    titleEl.style.opacity = '1';
                    titleEl.style.transform = 'translateY(0)';
                }, 200);
            }
            if (btnEl && url !== '#') btnEl.href = url;
            if (saveId && storyId) saveId.value = storyId;
        }
    }
});
}
</script>

<script src="js/search-ajax.js"></script>
<script src="../backend/script.js"></script>
</body>
</html>
