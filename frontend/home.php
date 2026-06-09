<?php
session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';

// Banner — 4 truyện đầu
$banner_result = mysqli_query($con, "SELECT id, title, cover FROM stories WHERE description = 'home' LIMIT 4");
$banner_books  = [];
while ($r = mysqli_fetch_assoc($banner_result)) $banner_books[] = $r;

// Grid sách — 21 cuốn
$result = mysqli_query($con, "SELECT id, title, cover FROM stories WHERE description = 'home' LIMIT 21");
$books  = [];
while ($r = mysqli_fetch_assoc($result)) $books[] = $r;

// Lấy danh sách truyện đã lưu của user
$saved_story_ids = [];
if (isset($_SESSION['user_id'])) {
    $sv_q = mysqli_query($con, "SELECT story_id FROM user_stories WHERE user_id=" . intval($_SESSION['user_id']));
    while ($sv = mysqli_fetch_assoc($sv_q)) $saved_story_ids[] = $sv['story_id'];
}

// Danh mục nổi bật (lấy 1 ảnh đại diện mỗi danh mục)
$categories = [
    ['key'=>'tho',       'label'=>'Thơ - Tản văn',          'icon'=>'fa-feather',         'url'=>'tho_tanvan.php'],
    ['key'=>'trinhtham', 'label'=>'Trinh thám - Kinh dị',   'icon'=>'fa-magnifying-glass', 'url'=>'trinhtham.php'],
    ['key'=>'taichinh',  'label'=>'Tài chính cá nhân',      'icon'=>'fa-coins',           'url'=>'taichinhcanhan.php'],
    ['key'=>'ptcanhan',  'label'=>'Phát triển cá nhân',     'icon'=>'fa-seedling',        'url'=>'pt_canhan.php'],
    ['key'=>'doanhnhan', 'label'=>'Doanh nhân',             'icon'=>'fa-briefcase',       'url'=>'doanh_nhan.php'],
    ['key'=>'suckhoe',   'label'=>'Sức khỏe - Làm đẹp',    'icon'=>'fa-heart-pulse',     'url'=>'suckhoe_lamdep.php'],
    ['key'=>'khoahoc',   'label'=>'Khoa học - Công nghệ',   'icon'=>'fa-flask',           'url'=>'khoahoc_congnghe.php'],
    ['key'=>'tamlinh',   'label'=>'Tâm linh - Tôn giáo',   'icon'=>'fa-yin-yang',        'url'=>'tamlinh.php'],
    ['key'=>'giaoduc',   'label'=>'Giáo dục & Văn hóa',    'icon'=>'fa-graduation-cap',  'url'=>'giaoduc_vanhoa.php'],
    ['key'=>'chungkhoan','label'=>'Chứng khoán - BĐS',     'icon'=>'fa-building-columns','url'=>'chungkhoan_bds_dautu.php'],
    ['key'=>'nghethuat', 'label'=>'Nghệ thuật sống',        'icon'=>'fa-palette',         'url'=>'nghe_thuat_song.php'],
    ['key'=>'tuduy',     'label'=>'Tư duy sáng tạo',       'icon'=>'fa-lightbulb',       'url'=>'tuduy_sangtao.php'],
    // Truyện
    ['key'=>'nam',       'label'=>'Truyện Nam',             'icon'=>'fa-mars',            'url'=>'nam.php'],
    ['key'=>'nu',        'label'=>'Truyện Nữ',             'icon'=>'fa-venus',           'url'=>'nu.php'],
    ['key'=>'xuyenkhong','label'=>'Xuyên không',            'icon'=>'fa-clock-rotate-left','url'=>'xuyenkhong.php'],
    ['key'=>'truyenma',  'label'=>'Truyện ma',              'icon'=>'fa-ghost',           'url'=>'truyenma.php'],
    ['key'=>'tinhcam',   'label'=>'Tình cảm',              'icon'=>'fa-heart',           'url'=>'tinhcam.php'],
    ['key'=>'ngungon',   'label'=>'Ngụ ngôn',              'icon'=>'fa-dragon',          'url'=>'ngungon.php'],
    ['key'=>'codai',     'label'=>'Cổ đại',                'icon'=>'fa-scroll',          'url'=>'codai.php'],
    ['key'=>'thieunhi',  'label'=>'Thiếu nhi',             'icon'=>'fa-child',           'url'=>'thieunhi.php'],
    ['key'=>'haihuoc',   'label'=>'Hài hước',              'icon'=>'fa-face-laugh',      'url'=>'haihuoc.php'],
    ['key'=>'hanhdong',  'label'=>'Hành động',             'icon'=>'fa-bolt',            'url'=>'hanhdong.php'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEWE — Đọc sách & Truyện online</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/search-ajax.css">
</head>
<body>

<!-- ══ HEADER ══ -->
<header>
    <div class="logo"><a href="home.php">KEWE</a></div>

    <nav>
        <ul>
            <li>
                <a href="#">Sách <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5"></i></a>
                <div class="mega-menu">
                    <div class="item"><a href="tho_tanvan.php"><i class="fa-solid fa-feather" style="color:#00d084"></i>Thơ - Tản văn</a></div>
                    <div class="item"><a href="trinhtham.php"><i class="fa-solid fa-magnifying-glass" style="color:#00d084"></i>Trinh thám - Kinh dị</a> <span>NEW</span></div>
                    <div class="item"><a href="mkt_banhang.php"><i class="fa-solid fa-chart-line" style="color:#00d084"></i>Marketing - Bán hàng</a> <span>NEW</span></div>
                    <div class="item"><a href="taichinhcanhan.php"><i class="fa-solid fa-coins" style="color:#00d084"></i>Tài chính cá nhân</a> <span>NEW</span></div>
                    <div class="item"><a href="pt_canhan.php"><i class="fa-solid fa-seedling" style="color:#00d084"></i>Phát triển cá nhân</a> <span>NEW</span></div>
                    <div class="item"><a href="doanh_nhan.php"><i class="fa-solid fa-briefcase" style="color:#00d084"></i>Doanh nhân - Bài học KD</a> <span>NEW</span></div>
                    <div class="item"><a href="suckhoe_lamdep.php"><i class="fa-solid fa-heart-pulse" style="color:#00d084"></i>Sức khỏe - Làm đẹp</a></div>
                    <div class="item"><a href="khoahoc_congnghe.php"><i class="fa-solid fa-flask" style="color:#00d084"></i>Khoa học - Công nghệ</a></div>
                    <div class="item"><a href="tuduy_sangtao.php"><i class="fa-solid fa-lightbulb" style="color:#00d084"></i>Tư duy sáng tạo</a> <span>NEW</span></div>
                    <div class="item"><a href="giaoduc_vanhoa.php"><i class="fa-solid fa-graduation-cap" style="color:#00d084"></i>Giáo dục - Văn hóa</a></div>
                    <div class="item"><a href="nghe_thuat_song.php"><i class="fa-solid fa-palette" style="color:#00d084"></i>Nghệ thuật sống</a> <span>NEW</span></div>
                    <div class="item"><a href="tamlinh.php"><i class="fa-solid fa-yin-yang" style="color:#00d084"></i>Tâm linh - Tôn giáo</a></div>
                    <div class="item"><a href="chungkhoan_bds_dautu.php"><i class="fa-solid fa-building-columns" style="color:#00d084"></i>Chứng khoán - BĐS</a></div>
                    <div class="item"><a href="sach_ngoai_van.php"><i class="fa-solid fa-globe" style="color:#00d084"></i>Sách Ngoại văn</a> <span>NEW</span></div>
                </div>
            </li>
            <li>
                <a href="#">Truyện <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5"></i></a>
                <div class="mega-menu">
                    <div class="item"><a href="nam.php"><i class="fa-solid fa-mars" style="color:#00d084"></i>Nam</a></div>
                    <div class="item"><a href="nu.php"><i class="fa-solid fa-venus" style="color:#00d084"></i>Nữ</a></div>
                    <div class="item"><a href="xuyenkhong.php"><i class="fa-solid fa-clock-rotate-left" style="color:#00d084"></i>Xuyên không</a></div>
                    <div class="item"><a href="truyenma.php"><i class="fa-solid fa-ghost" style="color:#00d084"></i>Truyện ma</a></div>
                    <div class="item"><a href="tinhcam.php"><i class="fa-solid fa-heart" style="color:#00d084"></i>Tình cảm</a></div>
                    <div class="item"><a href="ngungon.php"><i class="fa-solid fa-dragon" style="color:#00d084"></i>Ngụ ngôn</a></div>
                    <div class="item"><a href="codai.php"><i class="fa-solid fa-scroll" style="color:#00d084"></i>Cổ đại</a></div>
                    <div class="item"><a href="thieunhi.php"><i class="fa-solid fa-child" style="color:#00d084"></i>Thiếu nhi</a></div>
                    <div class="item"><a href="haihuoc.php"><i class="fa-solid fa-face-laugh" style="color:#00d084"></i>Hài hước</a></div>
                    <div class="item"><a href="hanhdong.php"><i class="fa-solid fa-bolt" style="color:#00d084"></i>Hành động</a></div>
                </div>
            </li>
        </ul>
    </nav>

    <div class="buttons">
        <div class="search-form-wrap">
            <form action="timkiem.php" method="GET" class="search-form" data-ajax-search>
                <input type="text" name="q" placeholder="Tìm sách, truyện..." autocomplete="off">
                <button type="submit" class="btn-timkiem">
                    <i class="fas fa-search"></i> Tìm
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

<!-- ══ BANNER ══ -->
<section class="banner">
    <div class="swiper bannerSwiper">
        <div class="swiper-wrapper">
        <?php if (!empty($banner_books)):
            $icons  = ['fa-fire','fa-star','fa-bolt','fa-crown'];
            $labels = ['Nổi bật','Đề xuất','Mới nhất','Tiêu biểu'];
            foreach ($banner_books as $i => $b):
                $url = '../backend/read_story.php?story_id=' . $b['id'];
        ?>
            <div class="swiper-slide">
                <img src="../code/images/<?= htmlspecialchars($b['cover']) ?>" alt="<?= htmlspecialchars($b['title']) ?>" onerror="this.src='img/ba1.webp'">
                <div class="slide-overlay">
                    <div class="slide-content">
                        <span class="slide-tag"><i class="fa-solid <?= $icons[$i%4] ?>"></i> <?= $labels[$i%4] ?></span>
                        <h2 class="slide-title"><?= htmlspecialchars($b['title']) ?></h2>
                        <p class="slide-desc">Khám phá ngay câu chuyện hấp dẫn này trên KEWE — đọc miễn phí 3 chương đầu.</p>
                        <div class="slide-actions">
                            <a href="<?= $url ?>" class="slide-btn"><i class="fa-solid fa-book-open"></i> Đọc ngay</a>
                            <form method="POST" action="luutruyen.php" style="display:inline">
                                <input type="hidden" name="story_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="slide-btn-outline"><i class="fa-solid fa-heart"></i> Lưu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="swiper-slide">
                <img src="img/ba1.webp" alt="Banner">
                <div class="slide-overlay">
                    <div class="slide-content">
                        <span class="slide-tag"><i class="fa-solid fa-fire"></i> Nổi bật</span>
                        <h2 class="slide-title">Khám phá kho sách<br>khổng lồ của KEWE</h2>
                        <p class="slide-desc">Hàng nghìn đầu sách & truyện hay đang chờ bạn.</p>
                        <div class="slide-actions">
                            <a href="#books-section" class="slide-btn"><i class="fa-solid fa-book-open"></i> Đọc ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <div class="banner-prev"><i class="fa-solid fa-chevron-left"></i></div>
        <div class="banner-next"><i class="fa-solid fa-chevron-right"></i></div>
        <div class="banner-pagination"></div>
    </div>
</section>

<!-- ══ CATEGORIES STRIP ══ -->
<div class="categories-strip">
    <div class="cat-list">
        <?php foreach ($categories as $cat): ?>
        <a href="<?= $cat['url'] ?>" class="cat-chip">
            <i class="fa-solid <?= $cat['icon'] ?>"></i>
            <?= $cat['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- ══ SÁCH NỔI BẬT ══ -->
<section class="home-section" id="books-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-fire"></i> Sách nổi bật</h2>
        <a href="tatca.php">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="book-grid">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
            <div class="book-card">
                <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>">
                    <img src="../code/images/<?= htmlspecialchars($book['cover']) ?>"
                         alt="<?= htmlspecialchars($book['title']) ?>"
                         onerror="this.src='img/sach2.jpg'">
                </a>
                <div class="book-card-body">
                    <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="book-card-title">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </div>
                <div class="book-card-footer">
                    <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="btn-read-sm">
                        <i class="fa-solid fa-book-open"></i> Đọc
                    </a>
                    <?php $is_saved_book = in_array($book['id'], $saved_story_ids ?? []); ?>
                    <button type="button"
                            class="btn-save-sm <?= $is_saved_book ? 'saved' : '' ?>"
                            data-story-id="<?= $book['id'] ?>"
                            onclick="toggleSave(this)"
                            title="<?= $is_saved_book ? 'Bỏ lưu' : 'Lưu' ?>">
                        <i class="fa-<?= $is_saved_book ? 'solid' : 'regular' ?> fa-heart"></i>
                    </button>
                </div>
            </div>
            
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-books">
                <i class="fa-solid fa-book-open"></i>
                <p>Chưa có sách nào</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ══ FOOTER ══ -->
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="brand-name">KEWE</div>
            <p>Nền tảng đọc sách & truyện online hàng đầu Việt Nam.</p>
            <p><i class="fa fa-phone"></i> 0877 736 289</p>
            <p><i class="fa fa-envelope"></i> support@kewe.vn</p>
            <p><i class="fa fa-location-dot"></i> Hà Nội, Việt Nam</p>
        </div>
        <div class="footer-col">
            <h4>Thể loại sách</h4>
            <a href="taichinhcanhan.php">Tài chính cá nhân</a>
            <a href="pt_canhan.php">Phát triển cá nhân</a>
            <a href="doanh_nhan.php">Doanh nhân</a>
            <a href="khoahoc_congnghe.php">Khoa học - Công nghệ</a>
            <a href="tamlinh.php">Tâm linh - Tôn giáo</a>
        </div>
        <div class="footer-col">
            <h4>Thể loại truyện</h4>
            <a href="tinhcam.php">Tình cảm</a>
            <a href="trinhtham.php">Trinh thám - Kinh dị</a>
            <a href="xuyenkhong.php">Xuyên không</a>
            <a href="hanhdong.php">Hành động</a>
            <a href="codai.php">Cổ đại</a>
        </div>
        <div class="footer-col">
            <h4>Tài khoản</h4>
            <a href="taikhoan.php">Thông tin tài khoản</a>
            <a href="tusach.php">Tủ sách cá nhân</a>
            <a href="napcoin.php">Nạp Coin</a>
            <a href="dangky_form.php">Đăng ký</a>
            <a href="dangnhap_form.php">Đăng nhập</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© 2025 KEWE</span> — Công ty Cổ phần Sách điện tử Kewe – Hà Nội
    </div>
</footer>

<!-- Modals -->
<div id="registerModal" class="modal" style="display:none"><?php include 'dangky_form.php'; ?></div>
<div id="loginModal"   class="modal" style="display:none"><?php include 'dangnhap_form.php'; ?></div>

<?php if (!empty($register_message)): ?>
<script>alert("<?= addslashes($register_message) ?>");</script>
<?php endif; ?>

<?php if (!empty($message)): ?>
<script>alert("<?= addslashes($message) ?>");</script>
<?php endif; ?>

<?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
<script>alert("Đăng nhập thành công!");</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
new Swiper(".bannerSwiper", {
    loop: true,
    autoplay: { delay: 4500, disableOnInteraction: false },
    speed: 900,
    effect: 'fade',
    fadeEffect: { crossFade: true },
    navigation: { prevEl: '.banner-prev', nextEl: '.banner-next' },
    pagination: { el: '.banner-pagination', clickable: true },
});
</script>
<script src="js/search-ajax.js"></script>
<script>
// Toggle save AJAX
function toggleSave(btn) {
    const storyId = btn.dataset.storyId;
    if (!storyId) return;
    fetch('../backend/toggle_save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'story_id=' + storyId
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'error') { alert('Bạn cần đăng nhập để lưu truyện!'); return; }
        const icon = btn.querySelector('i');
        if (data.status === 'saved') {
            btn.classList.add('saved'); icon.className = 'fa-solid fa-heart'; btn.title = 'Bỏ lưu';
        } else {
            btn.classList.remove('saved'); icon.className = 'fa-regular fa-heart'; btn.title = 'Lưu';
        }
    })
    .catch(() => alert('Có lỗi xảy ra!'));
}
</script>
<script src="../backend/script.js"></script>
</body>
</html>
