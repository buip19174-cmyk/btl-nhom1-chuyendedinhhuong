<?php

session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';

$adminUrl = app_url('frontend/admin/index.php');


if (isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    $r_sync = mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE id=" . intval($_SESSION['user_id'])));
    if ($r_sync) $_SESSION['role'] = $r_sync['role'];
}

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
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:#0a0a0a; --surface:#111; --card:#161616; --border:#222;
    --green:#00d084; --gold:#f5c518; --text:#e0e0e0; --dim:#666; --radius:12px;
}
body { background:var(--bg); color:var(--text); font-family:'Segoe UI',Roboto,sans-serif; }

/* ── HEADER ── */
header {
    position:sticky; top:0; z-index:1000;
    display:flex; align-items:center; justify-content:space-between;
    padding:0 36px; height:62px;
    background:rgba(10,10,10,.92); backdrop-filter:blur(16px);
    border-bottom:1px solid var(--border);
}
.logo a { font-size:24px; font-weight:900; color:var(--green); letter-spacing:-1px; text-decoration:none; }
nav { flex:1; display:flex; justify-content:center; }
nav ul { list-style:none; display:flex; gap:4px; }
nav ul li { position:relative; }
nav ul li > a {
    color:#ccc; text-decoration:none; font-size:14px; font-weight:500;
    padding:8px 14px; border-radius:8px; display:block; transition:.2s;
}
nav ul li > a:hover { color:#fff; background:#1a1a1a; }
nav ul li::after { content:''; position:absolute; top:100%; left:0; right:0; height:10px; }
.mega-menu {
    position:absolute; top:100%; left:50%; transform:translateX(-50%);
    width:680px; background:#141414; border:1px solid var(--border);
    padding:28px 20px 20px; display:none; grid-template-columns:repeat(3,1fr);
    gap:4px; border-radius:14px; z-index:9999;
    box-shadow:0 20px 60px rgba(0,0,0,.6);
}
nav ul li:hover .mega-menu { display:grid; }
.mega-menu .item a {
    display:flex; align-items:center; gap:8px;
    padding:9px 12px; border-radius:8px; color:#bbb;
    font-size:13px; text-decoration:none; transition:.15s;
}
.mega-menu .item a:hover { background:#1e1e1e; color:#fff; }
.mega-menu .item a i { color:var(--green); }
.mega-menu .item span {
    background:var(--green); color:#000; font-size:9px;
    font-weight:800; padding:1px 6px; border-radius:8px; text-transform:uppercase;
}
.search-form {
    position:relative;
    display:flex; align-items:center;
    background:#1a1a1a; border:1px solid var(--border);
    border-radius:24px; padding:6px 6px 6px 16px; gap:6px; transition:.2s;
}
.search-form:focus-within { border-color:var(--green); }
.search-form input {
    background:none; border:none; outline:none;
    color:var(--text); font-size:13px; width:160px;
}
.search-form input::placeholder { color:var(--dim); }
.btn-timkiem {
    background:var(--green); border:none; border-radius:20px;
    padding:6px 14px; color:#000; font-size:12px; font-weight:700;
    cursor:pointer; display:flex; align-items:center; gap:5px; transition:.2s;
}
.btn-timkiem:hover { background:#00b872; }
.search-dropdown {
    position:absolute; top:calc(100% + 6px); left:0; right:0;
    background:#141414; border:1px solid var(--border); border-radius:12px;
    max-height:400px; overflow-y:auto; display:none; z-index:9999;
    box-shadow:0 12px 40px rgba(0,0,0,.6);
}
.search-dropdown.open { display:block; }
.search-dropdown .sd-item {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px; text-decoration:none; color:var(--text);
    transition:.12s; border-bottom:1px solid #1a1a1a;
}
.search-dropdown .sd-item:last-child { border-bottom:none; }
.search-dropdown .sd-item:hover { background:#1a1a1a; }
.search-dropdown .sd-item img { width:36px; height:50px; object-fit:cover; border-radius:4px; flex-shrink:0; }
.search-dropdown .sd-title { font-size:13px; font-weight:600; }
.search-dropdown .sd-empty { padding:20px; text-align:center; color:var(--dim); font-size:13px; }
.search-dropdown .sd-footer { padding:10px 14px; text-align:center; border-top:1px solid var(--border); }
.search-dropdown .sd-footer a { color:var(--green); font-size:12px; font-weight:700; text-decoration:none; }

.user-area { display:flex; align-items:center; gap:10px; }
.btn-dangky { background:var(--green); color:#000; border:none; padding:8px 18px; border-radius:20px; font-size:13px; font-weight:700; cursor:pointer; transition:.2s; }
.btn-dangky:hover { background:#00b872; }
.btn-dangnhap { background:transparent; color:var(--green); border:1.5px solid var(--green); padding:7px 18px; border-radius:20px; font-size:13px; font-weight:600; cursor:pointer; transition:.2s; }
.btn-dangnhap:hover { background:rgba(0,208,132,.08); }

/* ── HERO BANNER ── */
.cat-banner { width:100%; position:relative; height:420px; overflow:hidden; }
.cat-banner .swiper { width:100%; height:100%; }
.cat-banner .swiper-slide { position:relative; overflow:hidden; }
.cat-banner .swiper-slide img {
    width:100%; height:100%; object-fit:cover; display:block;
    filter:brightness(.42) blur(2px); transform:scale(1.08); transition:transform 7s ease;
}
.cat-banner .swiper-slide-active img { transform:scale(1.13); filter:brightness(.48) blur(1px); }
.slide-overlay {
    position:absolute; inset:0;
    background:linear-gradient(to right, rgba(0,0,0,.82) 0%, rgba(0,0,0,.4) 55%, transparent 100%);
    display:flex; align-items:center; padding:0 60px;
}
.slide-content { max-width:520px; opacity:0; transform:translateY(24px); transition:opacity .7s ease .15s, transform .7s ease .15s; }
.swiper-slide-active .slide-content { opacity:1; transform:translateY(0); }
.slide-tag {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(0,208,132,.14); border:1px solid rgba(0,208,132,.35);
    color:var(--green); font-size:11px; font-weight:700;
    text-transform:uppercase; letter-spacing:1px;
    padding:5px 14px; border-radius:20px; margin-bottom:18px;
}
.slide-title { font-size:36px; font-weight:900; color:#fff; line-height:1.15; margin-bottom:12px; text-shadow:0 2px 16px rgba(0,0,0,.5); }
.slide-desc { font-size:14px; color:rgba(255,255,255,.65); line-height:1.7; margin-bottom:28px; text-align:left; }
.slide-actions { display:flex; align-items:center; gap:12px; }
.slide-btn {
    display:inline-flex; align-items:center; gap:8px;
    padding:12px 28px; background:var(--green); color:#000;
    font-size:14px; font-weight:700; border-radius:30px;
    text-decoration:none; transition:.2s; box-shadow:0 4px 20px rgba(0,208,132,.35);
}
.slide-btn:hover { background:#00b872; transform:translateY(-2px); color:#000; }
.slide-btn-outline {
    display:inline-flex; align-items:center; gap:8px;
    padding:12px 22px; background:rgba(255,255,255,.08);
    color:#fff; font-size:14px; font-weight:700; border-radius:30px;
    border:2px solid rgba(255,255,255,.25); cursor:pointer; transition:.2s;
}
.slide-btn-outline:hover { background:rgba(255,255,255,.16); border-color:rgba(255,255,255,.5); }
.banner-prev, .banner-next {
    position:absolute; top:50%; transform:translateY(-50%); z-index:10;
    width:44px; height:44px; background:rgba(255,255,255,.08);
    backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.12);
    border-radius:50%; display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:14px; cursor:pointer; transition:.2s;
}
.banner-prev { left:20px; } .banner-next { right:20px; }
.banner-prev:hover, .banner-next:hover { background:rgba(255,255,255,.2); }
.banner-pagination { position:absolute; bottom:18px; left:50%; transform:translateX(-50%); z-index:10; display:flex; gap:8px; }
.banner-pagination .swiper-pagination-bullet { width:8px; height:8px; background:rgba(255,255,255,.35); border-radius:4px; transition:.3s; opacity:1; }
.banner-pagination .swiper-pagination-bullet-active { width:28px; background:var(--green); }

/* ── BOOK GRID ── */
.home-section { padding:36px; max-width:1200px; margin:0 auto; }
.section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.section-header h2 { font-size:20px; font-weight:800; display:flex; align-items:center; gap:10px; }
.section-header h2 i { color:var(--green); font-size:17px; }
.section-header .count { font-size:13px; color:var(--dim); background:#1a1a1a; border:1px solid var(--border); padding:3px 12px; border-radius:14px; }

.book-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(148px, 1fr)); gap:20px; }
.book-card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; transition:.2s; display:flex; flex-direction:column; }
.book-card:hover { border-color:#333; transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,.5); }
.book-card img { width:100%; aspect-ratio:2/3; object-fit:cover; display:block; transition:.3s; }
.book-card:hover img { filter:brightness(1.1); }
.book-card-body { padding:10px 12px 6px; flex:1; }
.book-card-title { font-size:13px; font-weight:600; color:var(--text); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-decoration:none; display:block; margin-bottom:6px; }
.book-card-title:hover { color:var(--green); }
.book-card-footer { padding:0 12px 10px; display:flex; gap:6px; align-items:center; margin-top:auto; }
.book-card-footer form { display:flex; }
.btn-read-sm { flex:1; padding:6px; background:var(--green); color:#000; border:none; border-radius:6px; font-size:11px; font-weight:700; text-align:center; text-decoration:none; cursor:pointer; transition:.2s; display:flex; align-items:center; justify-content:center; gap:4px; }
.btn-read-sm:hover { background:#00b872; color:#000; }
.btn-save-sm { padding:6px 10px; background:transparent; color:#e74c3c; border:1.5px solid #e74c3c; border-radius:6px; font-size:11px; cursor:pointer; transition:.2s; }
.btn-save-sm:hover { background:rgba(231,76,60,.1); }
.btn-save-sm.saved { background:#e74c3c; color:#fff; border-color:#e74c3c; }
.btn-save-sm.saved:hover { background:#c0392b; }

.empty-books { grid-column:1/-1; text-align:center; padding:60px; color:var(--dim); }
.empty-books i { font-size:48px; display:block; margin-bottom:12px; opacity:.2; }

/* ── FOOTER ── */
footer { background:#0d0d0d; border-top:1px solid var(--border); padding:48px 36px 24px; }
.footer-inner { display:grid; grid-template-columns:1.5fr 1fr 1fr 1fr; gap:40px; margin-bottom:40px; }
.footer-brand .brand-name { font-size:28px; font-weight:900; color:var(--green); letter-spacing:-1px; margin-bottom:10px; }
.footer-brand p { font-size:13px; color:var(--dim); line-height:1.7; margin-bottom:6px; }
.footer-brand p i { color:var(--green); margin-right:6px; }
.footer-col h4 { font-size:13px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.8px; margin-bottom:14px; }
.footer-col a { display:block; font-size:13px; color:var(--dim); text-decoration:none; margin-bottom:8px; transition:.15s; }
.footer-col a:hover { color:var(--text); }
.footer-bottom { border-top:1px solid var(--border); padding-top:20px; font-size:12px; color:var(--dim); }
.footer-bottom span { color:var(--green); }

@media (max-width:900px) {
    .slide-overlay { padding:0 24px; }
    .slide-title { font-size:24px; }
    .home-section { padding:24px 16px; }
    .footer-inner { grid-template-columns:1fr 1fr; }
    .book-grid { grid-template-columns:repeat(3,1fr); }
}
@media (max-width:560px) { .book-grid { grid-template-columns:repeat(2,1fr); } .footer-inner { grid-template-columns:1fr; } }
</style>

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
                <a href="#">Sách <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5"></i></a>
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
                <a href="#">Truyện <i class="fa-solid fa-chevron-down" style="font-size:10px;opacity:.5"></i></a>
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
    <div class="buttons" style="position:relative">
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
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <li><a href="<?= $adminUrl ?>"><i class="fas fa-shield-halved"></i> Quản trị viên</a></li>
                        <?php else: ?>
                            <li><a href="tusach.php"><i class="fas fa-book"></i> Tủ sách cá nhân</a></li>
                            <li><a href="napcoin.php"><i class="fas fa-coins"></i> Nạp Coin</a></li>
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

<!-- HERO BANNER — text trái + swiper phải -->
<section class="cat-banner" style="height:auto;display:flex;align-items:center;padding:50px 60px;gap:40px;background:linear-gradient(135deg,#0f0f0f,#161616);border-bottom:1px solid var(--border);min-height:340px;overflow:hidden;position:relative">
    <div style="position:absolute;top:-80px;left:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,208,132,.08) 0%,transparent 70%);pointer-events:none"></div>

    <!-- Text bên trái -->
    <div style="flex:1;max-width:520px;position:relative;z-index:2">
        <span class="slide-tag"><i class="fa-solid fa-bookmark"></i> <?= htmlspecialchars($page_title) ?></span>
        <h1 class="slide-title" id="heroTitle" style="font-size:32px;margin-bottom:12px;transition:opacity .25s,transform .25s">
            <?= htmlspecialchars($first_book['title'] ?? $page_title) ?>
        </h1>
        <p class="slide-desc"><?= htmlspecialchars($hero_desc) ?></p>
        <div class="slide-actions">
            <a href="<?= $first_book ? '../backend/read_story.php?story_id=' . $first_book['id'] : '#' ?>" class="slide-btn" id="heroReadBtn">
                <i class="fa-solid fa-book-open"></i> Đọc ngay
            </a>
            <?php if ($first_book): ?>
            <form method="POST" action="luutruyen.php" style="display:inline">
                <input type="hidden" name="story_id" value="<?= $first_book['id'] ?>" id="heroSaveId">
                <button type="submit" class="slide-btn-outline"><i class="fa-solid fa-heart"></i> Lưu</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Swiper ảnh bìa bên phải -->
    <div style="width:800px;flex-shrink:0;position:relative;z-index:2">
        <?php if (!empty($hero_books)): ?>
        <div class="swiper heroSwiper" style="padding:30px 0">
            <div class="swiper-wrapper">
                <?php foreach ($hero_books as $b): ?>
                <div class="swiper-slide" style="display:flex;justify-content:center"
                     data-title="<?= htmlspecialchars($b['title'], ENT_QUOTES) ?>"
                     data-story-id="<?= $b['id'] ?>"
                     data-url="../backend/read_story.php?story_id=<?= $b['id'] ?>">
                    <a href="../backend/read_story.php?story_id=<?= $b['id'] ?>">
                        <img src="<?= htmlspecialchars(cover_url($b['cover'])) ?>"
                             alt="<?= htmlspecialchars($b['title']) ?>"
                             onerror="this.src='img/sach2.jpg'"
                             style="width:250px;height:360px;object-fit:cover;border-radius:10px;transition:.4s;box-shadow:0 8px 24px rgba(22, 29, 22, 0.5)">
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.heroSwiper .swiper-slide img { opacity:1 !important; filter:none !important; }
.heroSwiper .swiper-slide-active img { transform:scale(1.08) !important; box-shadow:0 16px 40px rgba(0,0,0,.7) !important; }
.heroSwiper .swiper-slide-shadow-left,
.heroSwiper .swiper-slide-shadow-right { display:none !important; }
</style>

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
                    <img src="<?= htmlspecialchars(cover_url($book['cover'])) ?>" alt="<?= htmlspecialchars($book['title']) ?>" onerror="this.src='img/sach2.jpg'">
                </a>
                <div class="book-card-body">
                    <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="book-card-title"><?= htmlspecialchars($book['title']) ?></a>
                </div>
                <div class="book-card-footer">
                    <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="btn-read-sm"><i class="fa-solid fa-book-open"></i> Đọc</a>
                    <?php if (($_SESSION['role'] ?? '') !== 'admin'):
                        $is_saved_book = in_array($book['id'], $saved_story_ids); ?>
                        <form action="luutruyen.php" method="POST">
                            <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn-save-sm <?= $is_saved_book ? 'saved' : '' ?>" title="<?= $is_saved_book ? 'Đã lưu' : 'Lưu' ?>">
                                <i class="fa-<?= $is_saved_book ? 'solid' : 'regular' ?> fa-heart"></i>
                            </button>
                        </form>
                    <?php endif; ?>
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
<div id="registerModal" class="modal" style="display:none"><?php include 'dangky_form.php'; ?></div>
<div id="loginModal" class="modal" style="display:none"><?php include 'dangnhap_form.php'; ?></div>

<div id="site-toast" style="display:none;position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;border-radius:8px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.5);max-width:320px;font-size:14px"></div>
<script>
(function(){
    function showToast(msg, isError) {
        var t = document.getElementById('site-toast');
        t.textContent = msg;
        t.style.background = isError ? '#e74c3c' : '#1ed760';
        t.style.color = isError ? '#fff' : '#000';
        t.style.display = 'block';
        setTimeout(function(){ t.style.display = 'none'; }, 4000);
    }
    <?php if (!empty($register_message)): ?>
    showToast("<?= addslashes($register_message) ?>", <?= strpos(strtolower($register_message), 'thành công') !== false ? 'false' : 'true' ?>);
    <?php endif; ?>
    <?php if (!empty($message)): ?>
    showToast("<?= addslashes($message) ?>", <?= strpos(strtolower($message), 'thành công') !== false ? 'false' : 'true' ?>);
    <?php endif; ?>
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
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

const searchInput = document.getElementById('searchInput');
const dropdown    = document.getElementById('searchDropdown');
let debounceTimer;

searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 1) { dropdown.classList.remove('open'); dropdown.innerHTML = ''; return; }

    debounceTimer = setTimeout(() => {
        fetch('../backend/search_ajax.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    dropdown.innerHTML = '<div class="sd-empty"><i class="fa-solid fa-search" style="opacity:.3;margin-right:6px"></i>Không tìm thấy "' + q + '"</div>';
                } else {
                    let html = '';
                    data.forEach(item => {
                        html += '<a href="../backend/read_story.php?story_id=' + item.id + '" class="sd-item">';
                        html += '<img src="' + item.cover + '" onerror="this.src=\'img/sach2.jpg\'">';
                        html += '<span class="sd-title">' + item.title + '</span>';
                        html += '</a>';
                    });
                    html += '<div class="sd-footer"><a href="timkiem.php?q=' + encodeURIComponent(q) + '">Xem tất cả kết quả →</a></div>';
                    dropdown.innerHTML = html;
                }
                dropdown.classList.add('open');
            })
            .catch(() => { dropdown.classList.remove('open'); });
    }, 250);
});

// Đóng dropdown khi click ngoài
document.addEventListener('click', function(e) {
    if (!document.getElementById('searchForm').contains(e.target)) {
        dropdown.classList.remove('open');
    }
});

// Mở lại khi focus vào input có text
searchInput.addEventListener('focus', function() {
    if (this.value.trim().length >= 1 && dropdown.innerHTML) dropdown.classList.add('open');
});
  
</script>
<script src="js/search-ajax.js"></script>
<script src="../backend/script.js"></script>
</body>
</html>
