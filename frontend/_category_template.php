<?php
/**
 * Template dùng chung cho tất cả trang danh mục
 * Biến cần khai báo trước khi include:
 *   $page_title   — tiêu đề trang, vd: "Thơ - Tản văn"
 *   $category_key — giá trị description trong DB, vd: 'tho'
 *   $hero_desc    — mô tả ngắn hiển thị trong hero
 */

session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';
require_once __DIR__ . '/includes/paths.php';
/** @var mysqli $con */

$adminUrl = app_url('frontend/admin/index.php');

// Lấy sách theo danh mục
$cat = mysqli_real_escape_string($con, $category_key);
$sql = "SELECT id, title, cover FROM stories WHERE description = '$cat' LIMIT 21";
$result = mysqli_query($con, $sql);
$books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
}

// Lấy sách cho hero swiper (tối đa 6)
$hero_books = array_slice($books, 0, 6);
$first_book = $books[0] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — KEWE</title>
    <link rel="stylesheet" href="css/d.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/category.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/search-ajax.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo"><a href="home.php">KEWE</a></div>
    <nav>
        <ul>
            <li>
                <a href="#">Sách</a>
                <div class="mega-menu">
                    <div class="item"><a href="tho_tanvan.php">Thơ - Tản văn</a></div>
                    <div class="item"><a href="trinhtham.php">Trinh thám - Kinh dị</a> <span>NEW</span></div>
                    <div class="item"><a href="mkt_banhang.php">Marketing - Bán hàng</a> <span>NEW</span></div>
                    <div class="item"><a href="taichinhcanhan.php">Tài chính cá nhân</a> <span>NEW</span></div>
                    <div class="item"><a href="pt_canhan.php">Phát triển cá nhân</a> <span>NEW</span></div>
                    <div class="item"><a href="doanh_nhan.php">Doanh nhân - Bài học KD</a> <span>NEW</span></div>
                    <div class="item"><a href="suckhoe_lamdep.php">Sức khỏe - Làm đẹp</a></div>
                    <div class="item"><a href="khoahoc_congnghe.php">Khoa học - Công nghệ</a></div>
                    <div class="item"><a href="tuduy_sangtao.php">Tư duy sáng tạo</a> <span>NEW</span></div>
                    <div class="item"><a href="giaoduc_vanhoa.php">Giáo dục - Văn hóa & Xã hội</a></div>
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
                    <div class="item"><a href="haihuoc.php">Hài hước</a></div>
                    <div class="item"><a href="hanhdong.php">Hành động</a></div>
                </div>
            </li>
        </ul>
    </nav>
    <div class="buttons" style="position:relative">
        <div class="search-form-wrap">
            <form action="timkiem.php" method="GET" class="search-form" data-ajax-search>
                <input type="text" name="q" placeholder="Tìm tên truyện..." autocomplete="off">
                <button type="submit" class="btn-timkiem">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
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
                        <?php if (($_SESSION['role'] ?? '') !== 'admin'): ?>
                            <li><a href="tusach.php"><i class="fas fa-book"></i> Tủ sách cá nhân</a></li>
                        <?php endif; ?>
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <li><a href="<?= htmlspecialchars($adminUrl) ?>"><i class="fa-solid fa-shield-halved"></i> Quản trị</a></li>
                        <?php endif; ?>
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
<section class="cat-banner" style="--cat-cover:url('../code/images/<?= htmlspecialchars($first_book['cover'] ?? 'sach2.jpg', ENT_QUOTES) ?>')">
    <div class="cat-banner-bg"></div>
    <div class="cat-banner-inner">

    <!-- Text bên trái -->
    <div class="cat-banner-copy">
        <span class="slide-tag">KEWE ĐỀ XUẤT</span>
        <h1 class="slide-title" id="heroTitle">
            <?= htmlspecialchars($first_book['title'] ?? $page_title) ?>
        </h1>
        <p class="slide-desc"><?= htmlspecialchars($hero_desc) ?></p>
        <div class="slide-actions">
            <a href="<?= $first_book ? '../backend/read_story.php?story_id=' . $first_book['id'] : '#' ?>" class="slide-btn" id="heroReadBtn">
                <i class="fa-solid fa-book-open"></i> Đọc sách

            </a>
            <?php if ($first_book && (($_SESSION['role'] ?? '') !== 'admin')): ?>
                <form method="POST" action="luutruyen.php" id="heroSaveForm">
                    <input type="hidden" name="story_id" value="<?= $first_book['id'] ?>" id="heroSaveId">
                    <button type="submit" class="cat-btn-save">
                        <i class="fa-solid fa-heart"></i> Lưu
                    </button>
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


<!-- DANH SÁCH SÁCH -->
<section class="content" id="content-section">
    <div class="cat-section-header">
        <h2><i class="fa-solid fa-layer-group"></i> Tất cả sách — <?= htmlspecialchars($page_title) ?></h2>
        <span class="cat-count"><?= count($books) ?> đầu sách</span>
    </div>

    <div class="container">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
            <div class="book">
                <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>">
                    <img src="../code/images/<?= htmlspecialchars($book['cover']) ?>"
                         onerror="this.src='img/sach2.jpg'">
                    <div class="title"><?= htmlspecialchars($book['title']) ?></div>
                </a>
                <?php if (($_SESSION['role'] ?? '') !== 'admin'): ?>
                    <form action="luutruyen.php" method="POST">
                        <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="cat-empty">
                <i class="fa-solid fa-book-open"></i>
                <p>Chưa có sách trong danh mục này</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-top">
        <div class="footer-logo">
            <h2>KEWE</h2>
            <p>Công ty cổ phần sách điện tử Kewe</p>
            <p><i class="fa fa-phone"></i> 0877736289</p>
            <p><i class="fa fa-envelope"></i> Support@kewe.vn</p>
        </div>
    </div>
    <div class="footer-bottom">Công ty Cổ phần Sách điện tử Kewe – Hà Nội</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const mySwiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    centeredSlides: true,
    loop: <?= count($hero_books) >= 3 ? 'true' : 'false' ?>,
    spaceBetween: -74,
    autoplay: { delay: 2500, disableOnInteraction: false },
    effect: "coverflow",
    grabCursor: true,
    coverflowEffect: { rotate: 0, stretch: 138, depth: 280, modifier: 1.2, slideShadows: false },
    on: {
        slideChange: function () {
            // Lấy slide đang active (realIndex để tránh lỗi với loop)
            const activeSlide = this.slides[this.activeIndex];
            if (!activeSlide) return;

            const title   = activeSlide.dataset.title   || '';
            const storyId = activeSlide.dataset.storyId || '';
            const url     = activeSlide.dataset.url     || '#';

            // Cập nhật text hero
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

<script src="js/search-ajax.js"></script>
<script src="../backend/script.js"></script>
<script>
(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('open') === 'login') {
        const loginModal = document.getElementById('loginModal');
        if (loginModal) loginModal.style.setProperty('display', 'flex', 'important');
        const redirectInput = document.querySelector('#loginModal input[name="redirect"]');
        if (redirectInput && params.get('redirect')) redirectInput.value = params.get('redirect');
    }
})();
</script>
</body>
</html>
