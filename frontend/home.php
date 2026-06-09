<?php
session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';

// Banner â€” 4 truyá»‡n Ä‘áº§u
$banner_result = mysqli_query($con, "SELECT id, title, cover FROM stories WHERE description = 'home' LIMIT 4");
$banner_books  = [];
while ($r = mysqli_fetch_assoc($banner_result)) $banner_books[] = $r;

// Grid sÃ¡ch â€” 18 cuá»‘n
$result = mysqli_query($con, "SELECT id, title, cover FROM stories WHERE description = 'home' LIMIT 21");
$books  = [];
while ($r = mysqli_fetch_assoc($result)) $books[] = $r;

// Láº¥y danh sÃ¡ch truyá»‡n Ä‘Ã£ lÆ°u cá»§a user
$saved_story_ids = [];
if (isset($_SESSION['user_id'])) {
    $sv_q = mysqli_query($con, "SELECT story_id FROM user_stories WHERE user_id=" . intval($_SESSION['user_id']));
    while ($sv = mysqli_fetch_assoc($sv_q)) $saved_story_ids[] = $sv['story_id'];
}

// Danh má»¥c ná»•i báº­t (láº¥y 1 áº£nh Ä‘áº¡i diá»‡n má»—i danh má»¥c)
$categories = [
    ['key'=>'tho',       'label'=>'ThÆ¡ - Táº£n vÄƒn',          'icon'=>'fa-feather',         'url'=>'tho_tanvan.php'],
    ['key'=>'trinhtham', 'label'=>'Trinh thÃ¡m - Kinh dá»‹',   'icon'=>'fa-magnifying-glass', 'url'=>'trinhtham.php'],
    ['key'=>'taichinh',  'label'=>'TÃ i chÃ­nh cÃ¡ nhÃ¢n',      'icon'=>'fa-coins',           'url'=>'taichinhcanhan.php'],
    ['key'=>'ptcanhan',  'label'=>'PhÃ¡t triá»ƒn cÃ¡ nhÃ¢n',     'icon'=>'fa-seedling',        'url'=>'pt_canhan.php'],
    ['key'=>'doanhnhan', 'label'=>'Doanh nhÃ¢n',             'icon'=>'fa-briefcase',       'url'=>'doanh_nhan.php'],
    ['key'=>'suckhoe',   'label'=>'Sá»©c khá»e - LÃ m Ä‘áº¹p',    'icon'=>'fa-heart-pulse',     'url'=>'suckhoe_lamdep.php'],
    ['key'=>'khoahoc',   'label'=>'Khoa há»c - CÃ´ng nghá»‡',   'icon'=>'fa-flask',           'url'=>'khoahoc_congnghe.php'],
    ['key'=>'tamlinh',   'label'=>'TÃ¢m linh - TÃ´n giÃ¡o',   'icon'=>'fa-yin-yang',        'url'=>'tamlinh.php'],
    ['key'=>'giaoduc',   'label'=>'GiÃ¡o dá»¥c & VÄƒn hÃ³a',    'icon'=>'fa-graduation-cap',  'url'=>'giaoduc_vanhoa.php'],
    ['key'=>'chungkhoan','label'=>'Chá»©ng khoÃ¡n - BÄS',     'icon'=>'fa-building-columns','url'=>'chungkhoan_bds_dautu.php'],
    ['key'=>'nghethuat', 'label'=>'Nghá»‡ thuáº­t sá»‘ng',        'icon'=>'fa-palette',         'url'=>'nghe_thuat_song.php'],
    ['key'=>'tuduy',     'label'=>'TÆ° duy sÃ¡ng táº¡o',       'icon'=>'fa-lightbulb',       'url'=>'tuduy_sangtao.php'],
    // Truyá»‡n
    ['key'=>'nam',       'label'=>'Truyá»‡n Nam',             'icon'=>'fa-mars',            'url'=>'nam.php'],
    ['key'=>'nu',        'label'=>'Truyá»‡n Ná»¯',             'icon'=>'fa-venus',           'url'=>'nu.php'],
    ['key'=>'xuyenkhong','label'=>'XuyÃªn khÃ´ng',            'icon'=>'fa-clock-rotate-left','url'=>'xuyenkhong.php'],
    ['key'=>'truyenma',  'label'=>'Truyá»‡n ma',              'icon'=>'fa-ghost',           'url'=>'truyenma.php'],
    ['key'=>'tinhcam',   'label'=>'TÃ¬nh cáº£m',              'icon'=>'fa-heart',           'url'=>'tinhcam.php'],
    ['key'=>'ngungon',   'label'=>'Ngá»¥ ngÃ´n',              'icon'=>'fa-dragon',          'url'=>'ngungon.php'],
    ['key'=>'codai',     'label'=>'Cá»• Ä‘áº¡i',                'icon'=>'fa-scroll',          'url'=>'codai.php'],
    ['key'=>'thieunhi',  'label'=>'Thiáº¿u nhi',             'icon'=>'fa-child',           'url'=>'thieunhi.php'],
    ['key'=>'haihuoc',   'label'=>'HÃ i hÆ°á»›c',              'icon'=>'fa-face-laugh',      'url'=>'haihuoc.php'],
    ['key'=>'hanhdong',  'label'=>'HÃ nh Ä‘á»™ng',             'icon'=>'fa-bolt',            'url'=>'hanhdong.php'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEWE â€” Äá»c sÃ¡ch & Truyá»‡n online</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/search-ajax.css">
</head>
<body>

<!-- â•â• HEADER â•â• -->
<header>
    <div class="logo"><a href="home.php">KEWE</a></div>

    <nav>
        <ul>
            <li>
                <a href="#">SÃ¡ch <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5"></i></a>
                <div class="mega-menu">
                    <div class="item"><a href="tho_tanvan.php"><i class="fa-solid fa-feather" style="color:#00d084"></i>ThÆ¡ - Táº£n vÄƒn</a></div>
                    <div class="item"><a href="trinhtham.php"><i class="fa-solid fa-magnifying-glass" style="color:#00d084"></i>Trinh thÃ¡m - Kinh dá»‹</a> <span>NEW</span></div>
                    <div class="item"><a href="mkt_banhang.php"><i class="fa-solid fa-chart-line" style="color:#00d084"></i>Marketing - BÃ¡n hÃ ng</a> <span>NEW</span></div>
                    <div class="item"><a href="taichinhcanhan.php"><i class="fa-solid fa-coins" style="color:#00d084"></i>TÃ i chÃ­nh cÃ¡ nhÃ¢n</a> <span>NEW</span></div>
                    <div class="item"><a href="pt_canhan.php"><i class="fa-solid fa-seedling" style="color:#00d084"></i>PhÃ¡t triá»ƒn cÃ¡ nhÃ¢n</a> <span>NEW</span></div>
                    <div class="item"><a href="doanh_nhan.php"><i class="fa-solid fa-briefcase" style="color:#00d084"></i>Doanh nhÃ¢n - BÃ i há»c KD</a> <span>NEW</span></div>
                    <div class="item"><a href="suckhoe_lamdep.php"><i class="fa-solid fa-heart-pulse" style="color:#00d084"></i>Sá»©c khá»e - LÃ m Ä‘áº¹p</a></div>
                    <div class="item"><a href="khoahoc_congnghe.php"><i class="fa-solid fa-flask" style="color:#00d084"></i>Khoa há»c - CÃ´ng nghá»‡</a></div>
                    <div class="item"><a href="tuduy_sangtao.php"><i class="fa-solid fa-lightbulb" style="color:#00d084"></i>TÆ° duy sÃ¡ng táº¡o</a> <span>NEW</span></div>
                    <div class="item"><a href="giaoduc_vanhoa.php"><i class="fa-solid fa-graduation-cap" style="color:#00d084"></i>GiÃ¡o dá»¥c - VÄƒn hÃ³a</a></div>
                    <div class="item"><a href="nghe_thuat_song.php"><i class="fa-solid fa-palette" style="color:#00d084"></i>Nghá»‡ thuáº­t sá»‘ng</a> <span>NEW</span></div>
                    <div class="item"><a href="tamlinh.php"><i class="fa-solid fa-yin-yang" style="color:#00d084"></i>TÃ¢m linh - TÃ´n giÃ¡o</a></div>
                    <div class="item"><a href="chungkhoan_bds_dautu.php"><i class="fa-solid fa-building-columns" style="color:#00d084"></i>Chá»©ng khoÃ¡n - BÄS</a></div>
                    <div class="item"><a href="sach_ngoai_van.php"><i class="fa-solid fa-globe" style="color:#00d084"></i>SÃ¡ch Ngoáº¡i vÄƒn</a> <span>NEW</span></div>
                </div>
            </li>
            <li>
                <a href="#">Truyá»‡n <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5"></i></a>
                <div class="mega-menu">
                    <div class="item"><a href="nam.php"><i class="fa-solid fa-mars" style="color:#00d084"></i>Nam</a></div>
                    <div class="item"><a href="nu.php"><i class="fa-solid fa-venus" style="color:#00d084"></i>Ná»¯</a></div>
                    <div class="item"><a href="xuyenkhong.php"><i class="fa-solid fa-clock-rotate-left" style="color:#00d084"></i>XuyÃªn khÃ´ng</a></div>
                    <div class="item"><a href="truyenma.php"><i class="fa-solid fa-ghost" style="color:#00d084"></i>Truyá»‡n ma</a></div>
                    <div class="item"><a href="tinhcam.php"><i class="fa-solid fa-heart" style="color:#00d084"></i>TÃ¬nh cáº£m</a></div>
                    <div class="item"><a href="ngungon.php"><i class="fa-solid fa-dragon" style="color:#00d084"></i>Ngá»¥ ngÃ´n</a></div>
                    <div class="item"><a href="codai.php"><i class="fa-solid fa-scroll" style="color:#00d084"></i>Cá»• Ä‘áº¡i</a></div>
                    <div class="item"><a href="thieunhi.php"><i class="fa-solid fa-child" style="color:#00d084"></i>Thiáº¿u nhi</a></div>
                    <div class="item"><a href="haihuoc.php"><i class="fa-solid fa-face-laugh" style="color:#00d084"></i>HÃ i hÆ°á»›c</a></div>
                    <div class="item"><a href="hanhdong.php"><i class="fa-solid fa-bolt" style="color:#00d084"></i>HÃ nh Ä‘á»™ng</a></div>
                </div>
            </li>
        </ul>
    </nav>

    <div class="buttons">
        <div class="search-form-wrap">
            <form action="timkiem.php" method="GET" class="search-form" data-ajax-search>
                <input type="text" name="q" placeholder="TÃ¬m sÃ¡ch, truyá»‡n..." autocomplete="off">
                <button type="submit" class="btn-timkiem">
                    <i class="fas fa-search"></i> TÃ¬m
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
                        <li><a href="taikhoan.php"><i class="fas fa-user-cog"></i> TÃ i khoáº£n</a></li>
                        <li><a href="tusach.php"><i class="fas fa-book"></i> Tá»§ sÃ¡ch cÃ¡ nhÃ¢n</a></li>
                        <li><a href="napcoin.php"><i class="fas fa-coins"></i> Náº¡p Coin</a></li>
                        <hr>
                        <li><a href="../backend/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> ÄÄƒng xuáº¥t</a></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <button class="btn-dangky" id="openRegisterModal">ÄÄƒng kÃ½</button>
            <button class="btn-dangnhap" id="openRegisterModal2">ÄÄƒng nháº­p</button>
        <?php endif; ?>
    </div>
</header>

<!-- â•â• BANNER â•â• -->
<section class="banner">
    <div class="swiper bannerSwiper">
        <div class="swiper-wrapper">
        <?php if (!empty($banner_books)):
            $icons  = ['fa-fire','fa-star','fa-bolt','fa-crown'];
            $labels = ['Ná»•i báº­t','Äá» xuáº¥t','Má»›i nháº¥t','TiÃªu biá»ƒu'];
            foreach ($banner_books as $i => $b):
                $url = '../backend/read_story.php?story_id=' . $b['id'];
        ?>
            <div class="swiper-slide">
                <img src="../code/images/<?= htmlspecialchars($b['cover']) ?>" alt="<?= htmlspecialchars($b['title']) ?>" onerror="this.src='img/ba1.webp'">
                <div class="slide-overlay">
                    <div class="slide-content">
                        <span class="slide-tag"><i class="fa-solid <?= $icons[$i%4] ?>"></i> <?= $labels[$i%4] ?></span>
                        <h2 class="slide-title"><?= htmlspecialchars($b['title']) ?></h2>
                        <p class="slide-desc">KhÃ¡m phÃ¡ ngay cÃ¢u chuyá»‡n háº¥p dáº«n nÃ y trÃªn KEWE â€” Ä‘á»c miá»…n phÃ­ 3 chÆ°Æ¡ng Ä‘áº§u.</p>
                        <div class="slide-actions">
                            <a href="<?= $url ?>" class="slide-btn"><i class="fa-solid fa-book-open"></i> Äá»c ngay</a>
                            <form method="POST" action="luutruyen.php" style="display:inline">
                                <input type="hidden" name="story_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="slide-btn-outline"><i class="fa-solid fa-heart"></i> LÆ°u</button>
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
                        <span class="slide-tag"><i class="fa-solid fa-fire"></i> Ná»•i báº­t</span>
                        <h2 class="slide-title">KhÃ¡m phÃ¡ kho sÃ¡ch<br>khá»•ng lá»“ cá»§a KEWE</h2>
                        <p class="slide-desc">HÃ ng nghÃ¬n Ä‘áº§u sÃ¡ch & truyá»‡n hay Ä‘ang chá» báº¡n.</p>
                        <div class="slide-actions">
                            <a href="#books-section" class="slide-btn"><i class="fa-solid fa-book-open"></i> Äá»c ngay</a>
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

<!-- â•â• CATEGORIES STRIP â•â• -->
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

<!-- â•â• SÃCH Ná»”I Báº¬T â•â• -->
<section class="home-section" id="books-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-fire"></i> SÃ¡ch ná»•i báº­t</h2>
        <a href="tatca.php">Xem táº¥t cáº£ <i class="fa-solid fa-arrow-right"></i></a>
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
                        <i class="fa-solid fa-book-open"></i> Äá»c
                    </a>
                    <form action="luutruyen.php" method="POST">
                        <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                        <?php $is_saved_book = in_array($book['id'], $saved_story_ids ?? []); ?>
                        <button type="submit" class="btn-save-sm <?= $is_saved_book ? 'saved' : '' ?>" title="<?= $is_saved_book ? 'ÄÃ£ lÆ°u' : 'LÆ°u vÃ o tá»§ sÃ¡ch' ?>">
                            <i class="fa-<?= $is_saved_book ? 'solid' : 'regular' ?> fa-heart"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-books">
                <i class="fa-solid fa-book-open"></i>
                <p>ChÆ°a cÃ³ sÃ¡ch nÃ o</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- â•â• FOOTER â•â• -->
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="brand-name">KEWE</div>
            <p>Ná»n táº£ng Ä‘á»c sÃ¡ch & truyá»‡n online hÃ ng Ä‘áº§u Viá»‡t Nam.</p>
            <p><i class="fa fa-phone"></i> 0877 736 289</p>
            <p><i class="fa fa-envelope"></i> support@kewe.vn</p>
            <p><i class="fa fa-location-dot"></i> HÃ  Ná»™i, Viá»‡t Nam</p>
        </div>
        <div class="footer-col">
            <h4>Thá»ƒ loáº¡i sÃ¡ch</h4>
            <a href="taichinhcanhan.php">TÃ i chÃ­nh cÃ¡ nhÃ¢n</a>
            <a href="pt_canhan.php">PhÃ¡t triá»ƒn cÃ¡ nhÃ¢n</a>
            <a href="doanh_nhan.php">Doanh nhÃ¢n</a>
            <a href="khoahoc_congnghe.php">Khoa há»c - CÃ´ng nghá»‡</a>
            <a href="tamlinh.php">TÃ¢m linh - TÃ´n giÃ¡o</a>
        </div>
        <div class="footer-col">
            <h4>Thá»ƒ loáº¡i truyá»‡n</h4>
            <a href="tinhcam.php">TÃ¬nh cáº£m</a>
            <a href="trinhtham.php">Trinh thÃ¡m - Kinh dá»‹</a>
            <a href="xuyenkhong.php">XuyÃªn khÃ´ng</a>
            <a href="hanhdong.php">HÃ nh Ä‘á»™ng</a>
            <a href="codai.php">Cá»• Ä‘áº¡i</a>
        </div>
        <div class="footer-col">
            <h4>TÃ i khoáº£n</h4>
            <a href="taikhoan.php">ThÃ´ng tin tÃ i khoáº£n</a>
            <a href="tusach.php">Tá»§ sÃ¡ch cÃ¡ nhÃ¢n</a>
            <a href="napcoin.php">Náº¡p Coin</a>
            <a href="dangky_form.php">ÄÄƒng kÃ½</a>
            <a href="dangnhap_form.php">ÄÄƒng nháº­p</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>Â© 2025 KEWE</span> â€” CÃ´ng ty Cá»• pháº§n SÃ¡ch Ä‘iá»‡n tá»­ Kewe â€“ HÃ  Ná»™i
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
<script>alert("ÄÄƒng nháº­p thÃ nh cÃ´ng!");</script>
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
<script src="../backend/script.js"></script>
</body>
</html>

