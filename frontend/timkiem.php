<?php
session_start();
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $keyword ? 'Tìm: ' . htmlspecialchars($keyword) : 'Tìm kiếm' ?> — KEWE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/search-ajax.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:      #0a0a0a;
    --surface: #111;
    --card:    #161616;
    --border:  #222;
    --green:   #00d084;
    --gold:    #f5c518;
    --text:    #e0e0e0;
    --dim:     #666;
    --radius:  12px;
}
body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', Roboto, sans-serif; min-height: 100vh; }

/* ── TOPBAR ── */
.topbar {
    position: sticky; top: 0; z-index: 100;
    background: rgba(10,10,10,.92); backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
    height: 56px; padding: 0 28px;
    display: flex; align-items: center; gap: 10px;
}
.topbar a { color: var(--dim); text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 6px; transition: .15s; }
.topbar a:hover { color: var(--text); background: #1a1a1a; }
.topbar .sep { color: #333; }
.topbar .current { color: var(--text); font-size: 13px; font-weight: 600; }
.topbar .logo { font-size: 20px; font-weight: 900; color: var(--green); text-decoration: none; margin-right: 12px; letter-spacing: -1px; }

/* ── SEARCH HERO ── */
.search-hero {
    background: linear-gradient(135deg, #0f0f0f, #161616);
    border-bottom: 1px solid var(--border);
    padding: 48px 28px 40px;
    text-align: center;
}
.search-hero h1 {
    font-size: 28px; font-weight: 800; color: #fff; margin-bottom: 8px;
}
.search-hero p { font-size: 14px; color: var(--dim); margin-bottom: 28px; }

.search-form {
    max-width: 600px; margin: 0 auto;
    display: flex; align-items: center;
    background: var(--card); border: 2px solid var(--border);
    border-radius: 30px; padding: 6px 6px 6px 22px;
    transition: .2s;
}
.search-form:focus-within { border-color: var(--green); box-shadow: 0 0 20px rgba(0,208,132,.1); }
.search-form i { color: var(--dim); font-size: 16px; margin-right: 12px; }
.search-form input {
    flex: 1; background: none; border: none; outline: none;
    color: var(--text); font-size: 15px;
}
.search-form input::placeholder { color: #444; }
.search-form button {
    background: var(--green); border: none; border-radius: 24px;
    padding: 10px 24px; color: #000; font-size: 14px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: 6px; transition: .2s;
}
.search-form button:hover { background: #00b872; }

/* ── RESULTS ── */
.results-wrap {
    max-width: 1200px; margin: 0 auto;
    padding: 32px 28px 60px;
}

.results-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 24px; padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}
.results-header h2 {
    font-size: 16px; font-weight: 700; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.results-header h2 .kw { color: var(--green); }
.results-header .count {
    font-size: 13px; color: var(--dim);
    background: #1a1a1a; border: 1px solid var(--border);
    padding: 3px 12px; border-radius: 14px;
}

/* Grid */
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: 20px;
}

.result-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: var(--radius); overflow: hidden;
    transition: .2s; position: relative;
}
.result-card:hover { border-color: #333; transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.5); }

.result-card img {
    width: 100%; aspect-ratio: 2/3; object-fit: cover; display: block;
    transition: .3s;
}
.result-card:hover img { filter: brightness(1.1); }

.result-card-body { padding: 10px 12px 6px; }
.result-card-title {
    font-size: 13px; font-weight: 600; color: var(--text);
    line-height: 1.4; display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    text-decoration: none; display: block; margin-bottom: 4px;
}
.result-card-title:hover { color: var(--green); }
.result-card-desc {
    font-size: 11px; color: var(--dim); line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.result-card-footer {
    padding: 0 12px 10px;
    display: flex; gap: 6px;
}
.btn-read {
    flex: 1; padding: 7px; background: var(--green); color: #000;
    border: none; border-radius: 6px; font-size: 11px; font-weight: 700;
    text-align: center; text-decoration: none; cursor: pointer; transition: .2s;
    display: flex; align-items: center; justify-content: center; gap: 4px;
}
.btn-read:hover { background: #00b872; color: #000; }
.btn-save {
    padding: 7px 10px; background: #1a0a0a; color: #e74c3c;
    border: 1px solid #2a1010; border-radius: 6px; font-size: 11px;
    cursor: pointer; transition: .2s;
}
.btn-save:hover { background: #2b0d0d; }

/* ── EMPTY STATE ── */
.empty-state {
    text-align: center; padding: 80px 20px; color: var(--dim);
}
.empty-state i { font-size: 56px; display: block; margin-bottom: 16px; opacity: .15; }
.empty-state h3 { font-size: 20px; color: #444; margin-bottom: 8px; }
.empty-state p { font-size: 14px; margin-bottom: 24px; }
.btn-home {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 24px; background: var(--green); color: #000;
    border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; transition: .2s;
}
.btn-home:hover { background: #00b872; color: #000; }

/* ── SUGGESTIONS ── */
.suggestions {
    margin-top: 40px; padding-top: 28px; border-top: 1px solid var(--border);
}
.suggestions h3 {
    font-size: 15px; font-weight: 700; color: var(--dim); margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.suggestions h3 i { color: var(--green); }
.suggest-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.suggest-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; background: #1a1a1a; border: 1px solid var(--border);
    border-radius: 20px; color: #aaa; font-size: 13px; text-decoration: none; transition: .15s;
}
.suggest-chip:hover { background: #222; color: #fff; border-color: #333; }

@media (max-width: 600px) {
    .results-grid { grid-template-columns: repeat(2, 1fr); }
    .search-hero { padding: 32px 16px 28px; }
    .search-hero h1 { font-size: 22px; }
}
</style>
</head>
<body>

<!-- TOPBAR -->
<nav class="topbar">
    <a href="home.php" class="logo">KEWE</a>
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <span class="current"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</span>
</nav>

<!-- SEARCH HERO -->
<section class="search-hero">
    <h1><i class="fa-solid fa-magnifying-glass" style="color:var(--green);font-size:24px"></i> Tìm kiếm</h1>
    <p>Tìm sách, truyện theo tên — kho tàng hàng nghìn đầu sách đang chờ bạn</p>
    <form id="ajax-search-form" action="timkiem.php" method="GET" class="search-form">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" placeholder="Nhập tên sách hoặc truyện..."
               value="<?= htmlspecialchars($keyword) ?>" autofocus autocomplete="off">
        <button type="submit"><i class="fa-solid fa-search"></i> Tìm</button>
    </form>
</section>

<!-- RESULTS -->
<div class="results-wrap">

    <div id="ajax-results-header" class="results-header" style="<?= $keyword !== '' ? '' : 'display:none' ?>">
        <h2>
            <i class="fa-solid fa-filter" style="color:var(--green);font-size:14px"></i>
            Kết quả cho "<span class="kw" id="ajax-result-keyword"><?= htmlspecialchars($keyword) ?></span>"
        </h2>
        <span class="count" id="ajax-result-count"></span>
    </div>

    <div id="ajax-search-status" class="search-ajax-status"></div>
    <div id="ajax-search-results">
        <?php if ($keyword === ''): ?>
        <div class="empty-state">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3>Bắt đầu tìm kiếm</h3>
            <p>Nhập tên sách hoặc truyện — kết quả cập nhật ngay khi gõ.</p>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-spinner fa-spin" style="opacity:.3;font-size:40px"></i>
            <p style="margin-top:12px">Đang tải kết quả...</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Gợi ý danh mục -->
    <div class="suggestions">
        <h3><i class="fa-solid fa-compass"></i> Gợi ý danh mục</h3>
        <div class="suggest-chips">
            <a href="tho_tanvan.php" class="suggest-chip"><i class="fa-solid fa-feather"></i> Thơ - Tản văn</a>
            <a href="trinhtham.php" class="suggest-chip"><i class="fa-solid fa-magnifying-glass"></i> Trinh thám</a>
            <a href="taichinhcanhan.php" class="suggest-chip"><i class="fa-solid fa-coins"></i> Tài chính</a>
            <a href="pt_canhan.php" class="suggest-chip"><i class="fa-solid fa-seedling"></i> Phát triển cá nhân</a>
            <a href="doanh_nhan.php" class="suggest-chip"><i class="fa-solid fa-briefcase"></i> Doanh nhân</a>
            <a href="khoahoc_congnghe.php" class="suggest-chip"><i class="fa-solid fa-flask"></i> Khoa học</a>
            <a href="tamlinh.php" class="suggest-chip"><i class="fa-solid fa-yin-yang"></i> Tâm linh</a>
            <a href="suckhoe_lamdep.php" class="suggest-chip"><i class="fa-solid fa-heart-pulse"></i> Sức khỏe</a>
        </div>
    </div>

</div>

<script src="js/search-ajax.js"></script>
</body>
</html>
