<?php
session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';
require_once __DIR__ . '/includes/paths.php';
/** @var mysqli $con */

$adminUrl = app_url('frontend/admin/index.php');

// #region agent log
$_dbg_log_path = __DIR__ . '/../debug-7416fa.log';
$_dbg_data = json_encode(['sessionId'=>'7416fa','hypothesisId'=>'A','location'=>'home.php:top','message'=>'saved_story_ids check','data'=>['saved_story_ids_defined'=>isset($saved_story_ids),'saved_story_ids_type'=>gettype($saved_story_ids ?? null),'user_logged_in'=>isset($_SESSION['username'])],'timestamp'=>round(microtime(true)*1000)]);
file_put_contents($_dbg_log_path, $_dbg_data."\n", FILE_APPEND);
// #endregion

// Lấy danh sách truyện đã lưu của user
$saved_story_ids = [];
if (isset($_SESSION['user_id'])) {
    $_uid = (int) $_SESSION['user_id'];
    $_saved_q = mysqli_query($con, "SELECT story_id FROM user_stories WHERE user_id = $_uid");
    if ($_saved_q) {
        while ($_sr = mysqli_fetch_assoc($_saved_q)) {
            $saved_story_ids[] = $_sr['story_id'];
        }
    }
}

// Banner — 4 truyện đầu
$banner_result = mysqli_query($con, "SELECT id, title, cover FROM stories WHERE description = 'home' LIMIT 4");
$banner_books  = [];
while ($r = mysqli_fetch_assoc($banner_result)) $banner_books[] = $r;

// Grid sách — 18 cuốn
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
    ['key'=>'tho',       'label'=>'Thơ - Tản văn',          'icon'=>'fa-feather',        'url'=>'tho_tanvan.php'],
    ['key'=>'trinhtham', 'label'=>'Trinh thám - Kinh dị',    'icon'=>'fa-magnifying-glass','url'=>'trinhtham.php'],
    ['key'=>'taichinh',  'label'=>'Tài chính cá nhân',       'icon'=>'fa-coins',          'url'=>'taichinhcanhan.php'],
    ['key'=>'ptcanhan',  'label'=>'Phát triển cá nhân',      'icon'=>'fa-seedling',       'url'=>'pt_canhan.php'],
    ['key'=>'doanhnhan', 'label'=>'Doanh nhân',              'icon'=>'fa-briefcase',      'url'=>'doanh_nhan.php'],
    ['key'=>'suckhoe',   'label'=>'Sức khỏe - Làm đẹp',     'icon'=>'fa-heart-pulse',    'url'=>'suckhoe_lamdep.php'],
    ['key'=>'khoahoc',   'label'=>'Khoa học - Công nghệ',   'icon'=>'fa-flask',          'url'=>'khoahoc_congnghe.php'],
    ['key'=>'tamlinh',   'label'=>'Tâm linh - Tôn giáo',    'icon'=>'fa-yin-yang',       'url'=>'tamlinh.php'],
    ['key'=>'giaoduc',   'label'=>'Giáo dục & Văn hóa',    'icon'=>'fa-graduation-cap', 'url'=>'giaoduc_vanhoa.php'],
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
    <link rel="stylesheet" href="css/d.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/search-ajax.css">
    <style>
    /* ── RESET & BASE ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
        --bg:      #0a0a0a;
        --surface: #111;
        --card:    #161616;
        --border:  #222;
        --green:   #00d084;
        --gold:    #f5c518;
        --text:    #e8e8e8;
        --dim:     #666;
        --radius:  12px;
    }
    body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', Roboto, sans-serif; }

    /* ── HEADER ── */
    header {
        position: sticky; top: 0; z-index: 1000;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 36px; height: 62px;
        background: rgba(10,10,10,.92);
        backdrop-filter: blur(16px);
        border-bottom: 1px solid var(--border);
    }
    .logo a { font-size: 24px; font-weight: 900; color: var(--green); letter-spacing: -1px; text-decoration: none; }

    /* nav */
    nav { flex: 1; display: flex; justify-content: center; }
    nav ul { list-style: none; display: flex; gap: 4px; }
    nav ul li { position: relative; }
    nav ul li > a {
        color: #ccc; text-decoration: none; font-size: 14px; font-weight: 500;
        padding: 8px 14px; border-radius: 8px; display: block; transition: .2s;
    }
    nav ul li > a:hover { color: #fff; background: #1a1a1a; }
    .mega-menu {
        position: absolute; top: 100%; left: 50%; transform: translateX(-50%);
        width: 680px; background: #141414; border: 1px solid var(--border);
        padding: 20px; display: none; grid-template-columns: repeat(3,1fr);
        gap: 4px; border-radius: 14px; z-index: 9999;
        box-shadow: 0 20px 60px rgba(0,0,0,.6);
        padding-top: 28px;
    }
    /* Tạo vùng hover liền mạch giữa link và menu */
    nav ul li { position: relative; }
    nav ul li::after {
        content: ''; position: absolute;
        top: 100%; left: 0; right: 0; height: 10px;
    }
    nav ul li:hover .mega-menu { display: grid; }
    .mega-menu .item a {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 12px; border-radius: 8px; color: #bbb;
        font-size: 13px; text-decoration: none; transition: .15s;
    }
    .mega-menu .item a:hover { background: #1e1e1e; color: #fff; }
    .mega-menu .item span {
        background: var(--green); color: #000; font-size: 9px;
        font-weight: 800; padding: 1px 6px; border-radius: 8px; text-transform: uppercase;
    }

    .buttons { position: relative; }

    /* search */
    .search-form {
        display: flex; align-items: center;
        background: #1a1a1a; border: 1px solid var(--border);
        border-radius: 24px; padding: 6px 6px 6px 16px; gap: 6px;
        transition: .2s;
    }
    .search-form:focus-within { border-color: var(--green); }
    .search-form input {
        background: none; border: none; outline: none;
        color: var(--text); font-size: 13px; width: 160px;
    }
    .search-form input::placeholder { color: var(--dim); }
    .btn-timkiem {
        background: var(--green); border: none; border-radius: 20px;
        padding: 6px 14px; color: #000; font-size: 12px; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; gap: 5px; transition: .2s;
    }
    .btn-timkiem:hover { background: #00b872; }

    /* user area */
    .user-area { position: relative; display: flex; align-items: center; gap: 10px; }
    .btn-dangky {
        background: var(--green); color: #000; border: none;
        padding: 8px 18px; border-radius: 20px; font-size: 13px;
        font-weight: 700; cursor: pointer; transition: .2s;
    }
    .btn-dangky:hover { background: #00b872; }
    .btn-dangnhap {
        background: transparent; color: var(--green);
        border: 1.5px solid var(--green); padding: 7px 18px;
        border-radius: 20px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: .2s;
    }
    .btn-dangnhap:hover { background: rgba(0,208,132,.08); }

    /* ── BANNER ── */
    .banner { width: 100%; position: relative; }
    .banner .swiper { width: 100%; height: 520px; }
    .banner .swiper-slide { position: relative; overflow: hidden; }
    .banner .swiper-slide img {
        width: 100%; height: 100%; object-fit: cover; object-position: center;
        display: block; filter: brightness(0.42) blur(2px);
        transform: scale(1.08); transition: transform 7s ease, filter .8s ease;
    }
    .banner .swiper-slide-active img { transform: scale(1.13); filter: brightness(0.48) blur(1px); }
    .slide-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to right, rgba(0,0,0,.82) 0%, rgba(0,0,0,.4) 55%, transparent 100%);
        display: flex; align-items: center; padding: 0 80px;
    }
    .slide-content { max-width: 540px; opacity: 0; transform: translateY(24px); transition: opacity .7s ease .15s, transform .7s ease .15s; }
    .swiper-slide-active .slide-content { opacity: 1; transform: translateY(0); }
    .slide-tag {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(0,208,132,.14); border: 1px solid rgba(0,208,132,.35);
        color: var(--green); font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1px;
        padding: 5px 14px; border-radius: 20px; margin-bottom: 18px;
    }
    .slide-title {
        font-size: 42px; font-weight: 900; color: #fff; line-height: 1.15;
        margin-bottom: 14px; text-shadow: 0 2px 16px rgba(0,0,0,.5);
    }
    .slide-desc { font-size: 15px; color: rgba(255,255,255,.65); line-height: 1.7; margin-bottom: 30px; text-align: left; }
    .slide-actions { display: flex; align-items: center; gap: 12px; }
    .slide-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 30px; background: var(--green); color: #000;
        font-size: 14px; font-weight: 700; border-radius: 30px;
        text-decoration: none; transition: .2s;
        box-shadow: 0 4px 20px rgba(0,208,132,.35);
    }
    .slide-btn:hover { background: #00b872; transform: translateY(-2px); color: #000; }
    .slide-btn-outline {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 24px; background: rgba(255,255,255,.08);
        color: #fff; font-size: 14px; font-weight: 700;
        border-radius: 30px; border: 2px solid rgba(255,255,255,.25);
        cursor: pointer; transition: .2s; backdrop-filter: blur(4px);
    }
    .slide-btn-outline:hover { background: rgba(255,255,255,.16); border-color: rgba(255,255,255,.5); }
    .banner-prev, .banner-next {
        position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
        width: 46px; height: 46px; background: rgba(255,255,255,.08);
        backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 15px; cursor: pointer; transition: .2s;
    }
    .banner-prev { left: 24px; } .banner-next { right: 24px; }
    .banner-prev:hover, .banner-next:hover { background: rgba(255,255,255,.2); }
    .banner-pagination { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); z-index: 10; display: flex; gap: 8px; }
    .banner-pagination .swiper-pagination-bullet { width: 8px; height: 8px; background: rgba(255,255,255,.35); border-radius: 4px; transition: .3s; opacity: 1; }
    .banner-pagination .swiper-pagination-bullet-active { width: 28px; background: var(--green); }

    /* ── CATEGORIES STRIP ── */
    .categories-strip {
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 0 36px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .categories-strip::-webkit-scrollbar { display: none; }
    .cat-list { display: flex; gap: 4px; padding: 10px 0; white-space: nowrap; }
    .cat-chip {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 16px; border-radius: 20px;
        background: #1a1a1a; border: 1px solid var(--border);
        color: #aaa; font-size: 13px; text-decoration: none;
        transition: .2s; flex-shrink: 0;
    }
    .cat-chip i { font-size: 12px; color: var(--green); }
    .cat-chip:hover { background: #222; color: #fff; border-color: #333; }

    /* ── SECTION WRAPPER ── */
    .home-section { padding: 40px 36px; }
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 24px;
    }
    .section-header h2 {
        font-size: 20px; font-weight: 800; color: var(--text);
        display: flex; align-items: center; gap: 10px;
    }
    .section-header h2 i { color: var(--green); font-size: 17px; }
    .section-header a {
        font-size: 13px; color: var(--green); text-decoration: none;
        display: flex; align-items: center; gap: 5px;
    }
    .section-header a:hover { text-decoration: underline; }

    /* ── BOOK GRID ── */
    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(148px, 1fr));
        gap: 20px;
    }
    .book-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        transition: .2s; display: flex; flex-direction: column;
    }
    .book-card:hover { border-color: #333; transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.5); }
    .book-card img {
        width: 100%; aspect-ratio: 2/3; object-fit: cover; display: block;
        transition: .3s;
    }
    .book-card:hover img { filter: brightness(1.1); }
    .book-card-body { padding: 10px 12px 6px; flex: 1; }
    .book-card-title {
        font-size: 13px; font-weight: 600; color: var(--text);
        line-height: 1.4; display: -webkit-box;
        -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        text-decoration: none; display: block; margin-bottom: 8px;
    }
    .book-card-title:hover { color: var(--green); }
    .book-card-footer {
        padding: 0 12px 10px;
        display: flex; gap: 6px; align-items: center; margin-top: auto;
    }
    .book-card-footer form { display: flex; }
    .btn-read-sm {
        flex: 1; padding: 6px; background: var(--green); color: #000;
        border: none; border-radius: 6px; font-size: 11px; font-weight: 700;
        text-align: center; text-decoration: none; cursor: pointer; transition: .2s;
        display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .btn-read-sm:hover { background: #00b872; color: #000; }
    .btn-save-sm {
        padding: 6px 10px; background: transparent; color: #e74c3c;
        border: 1.5px solid #e74c3c; border-radius: 6px; font-size: 11px;
        cursor: pointer; transition: .2s;
    }
    .btn-save-sm:hover { background: rgba(231,76,60,.1); }
    .btn-save-sm.saved {
        background: #e74c3c; color: #fff; border-color: #e74c3c;
    }
    .btn-save-sm.saved:hover { background: #c0392b; }

    /* ── EMPTY ── */
    .empty-books { grid-column: 1/-1; text-align: center; padding: 60px; color: var(--dim); }
    .empty-books i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .2; }

    /* ── FOOTER ── */
    footer {
        background: #0d0d0d; border-top: 1px solid var(--border);
        padding: 48px 36px 24px;
    }
    .footer-inner {
        display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr;
        gap: 40px; margin-bottom: 40px;
    }
    .footer-brand .brand-name {
        font-size: 28px; font-weight: 900; color: var(--green);
        letter-spacing: -1px; margin-bottom: 10px;
    }
    .footer-brand p { font-size: 13px; color: var(--dim); line-height: 1.7; margin-bottom: 6px; }
    .footer-brand p i { color: var(--green); margin-right: 6px; }
    .footer-col h4 { font-size: 13px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 14px; }
    .footer-col a { display: block; font-size: 13px; color: var(--dim); text-decoration: none; margin-bottom: 8px; transition: .15s; }
    .footer-col a:hover { color: var(--text); }
    .footer-bottom {
        border-top: 1px solid var(--border); padding-top: 20px;
        display: flex; align-items: center; justify-content: space-between;
        font-size: 12px; color: var(--dim);
    }
    .footer-bottom span { color: var(--green); }

    /* ── SEARCH DROPDOWN ── */
    .search-form { position: relative; }
    .search-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0; right: 0;
        background: #141414;
        border: 1px solid var(--border);
        border-radius: 12px;
        max-height: 400px;
        overflow-y: auto;
        display: none;
        z-index: 9999;
        box-shadow: 0 12px 40px rgba(0,0,0,.6);
    }
    .search-dropdown.open { display: block; }
    .search-dropdown .sd-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        text-decoration: none;
        color: var(--text);
        transition: .12s;
        border-bottom: 1px solid #1a1a1a;
    }
    .search-dropdown .sd-item:last-child { border-bottom: none; }
    .search-dropdown .sd-item:hover { background: #1a1a1a; }
    .search-dropdown .sd-item img {
        width: 36px; height: 50px;
        object-fit: cover; border-radius: 4px; flex-shrink: 0;
    }
    .search-dropdown .sd-title {
        font-size: 13px; font-weight: 600; line-height: 1.3;
    }
    .search-dropdown .sd-empty {
        padding: 20px; text-align: center;
        color: var(--dim); font-size: 13px;
    }
    .search-dropdown .sd-footer {
        padding: 10px 14px; text-align: center;
        border-top: 1px solid var(--border);
    }
    .search-dropdown .sd-footer a {
        color: var(--green); font-size: 12px; font-weight: 700;
        text-decoration: none;
    }
    .search-dropdown .sd-footer a:hover { text-decoration: underline; }

    @media (max-width: 900px) {
        .slide-overlay { padding: 0 30px; }
        .slide-title { font-size: 28px; }
        .home-section { padding: 28px 16px; }
        .footer-inner { grid-template-columns: 1fr 1fr; }
        .book-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 560px) {
        .book-grid { grid-template-columns: repeat(2, 1fr); }
        .footer-inner { grid-template-columns: 1fr; }
    }
    </style>
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
                    <?php if (($_SESSION['role'] ?? '') !== 'admin'): ?>
                        <form action="luutruyen.php" method="POST">
                            <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                            <?php
                    // #region agent log
                    $_dbg_data2 = json_encode(['sessionId'=>'7416fa','hypothesisId'=>'A','location'=>'home.php:495','message'=>'in_array call','data'=>['book_id'=>$book['id'],'saved_story_ids_isset'=>isset($saved_story_ids),'saved_story_ids_val'=>($saved_story_ids ?? 'UNDEFINED')],'timestamp'=>round(microtime(true)*1000)]);
                    file_put_contents($_dbg_log_path, $_dbg_data2."\n", FILE_APPEND);
                    // #endregion
                    $is_saved_book = in_array($book['id'], $saved_story_ids ?? []); ?>
                            <button type="submit" class="btn-save-sm <?= $is_saved_book ? 'saved' : '' ?>" title="<?= $is_saved_book ? 'Đã lưu' : 'Lưu vào tủ sách' ?>">
                                <i class="fa-<?= $is_saved_book ? 'solid' : 'regular' ?> fa-heart"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                    <form action="luutruyen.php" method="POST">
                        <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                        <?php $is_saved_book = in_array($book['id'], $saved_story_ids ?? []); ?>
                        <button type="submit" class="btn-save-sm <?= $is_saved_book ? 'saved' : '' ?>" title="<?= $is_saved_book ? 'Đã lưu' : 'Lưu vào tủ sách' ?>">
                            <i class="fa-<?= $is_saved_book ? 'solid' : 'regular' ?> fa-heart"></i>
                        </button>
                    </form>
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

<<<<<<< HEAD
=======
<?php if (!empty($message)): ?>
<script>alert("<?= addslashes($message) ?>");</script>
<?php endif; ?>

<?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
<script>alert("Đăng nhập thành công!");</script>
<?php endif; ?>

>>>>>>> 12e755f37db22e451acc4f09cc3ed189d7acbba5
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
<script>
// #region agent log
(function(){
    // Hypothesis B: Check duplicate loginModal IDs
    var loginModals = document.querySelectorAll('#loginModal');
    fetch('http://127.0.0.1:7712/ingest/31a9d562-844d-4898-9010-0ebea3408a39',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'7416fa'},body:JSON.stringify({sessionId:'7416fa',hypothesisId:'B',location:'home.php:script',message:'loginModal duplicate check',data:{loginModalCount:loginModals.length,loginModalNested:loginModals.length>1},timestamp:Date.now()})}).catch(function(){});

    // Hypothesis D: Check footer link handlers
    var footerReg = document.getElementById('footer-open-register');
    var footerLogin = document.getElementById('footer-open-login');
    fetch('http://127.0.0.1:7712/ingest/31a9d562-844d-4898-9010-0ebea3408a39',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'7416fa'},body:JSON.stringify({sessionId:'7416fa',hypothesisId:'D',location:'home.php:script',message:'footer links check',data:{footerRegExists:!!footerReg,footerLoginExists:!!footerLogin,footerRegHasClick:footerReg?footerReg.onclick!==null:false,footerLoginHasClick:footerLogin?footerLogin.onclick!==null:false},timestamp:Date.now()})}).catch(function(){});

    // Hypothesis E: Check ?open=login param handling
    var urlParams = new URLSearchParams(window.location.search);
    var openParam = urlParams.get('open');
    fetch('http://127.0.0.1:7712/ingest/31a9d562-844d-4898-9010-0ebea3408a39',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'7416fa'},body:JSON.stringify({sessionId:'7416fa',hypothesisId:'E',location:'home.php:script',message:'open param check',data:{openParam:openParam,loginModalVisible:document.getElementById('loginModal')?getComputedStyle(document.getElementById('loginModal')).display:'N/A'},timestamp:Date.now()})}).catch(function(){});

    // Hypothesis C: Check categories strip links
    var catChips = document.querySelectorAll('.cat-chip');
    var catData = [];
    catChips.forEach(function(c){catData.push({text:c.textContent.trim(),href:c.getAttribute('href')});});
    fetch('http://127.0.0.1:7712/ingest/31a9d562-844d-4898-9010-0ebea3408a39',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'7416fa'},body:JSON.stringify({sessionId:'7416fa',hypothesisId:'C',location:'home.php:script',message:'categories check',data:{categories:catData},timestamp:Date.now()})}).catch(function(){});
})();
// #endregion
</script>
</body>
</html>
