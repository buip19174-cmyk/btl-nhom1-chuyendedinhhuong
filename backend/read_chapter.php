<?php
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';

if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';
require_once __DIR__ . '/story_config.php';
require_once __DIR__ . '/../frontend/includes/paths.php';

if (!isset($_GET['chapter_id'])) die("Thiếu ID chương");
$chapter_id = intval($_GET['chapter_id']);

$q       = mysqli_query($con, "SELECT c.*, s.title AS story_title, s.cover FROM chapters c JOIN stories s ON c.story_id = s.id WHERE c.id = $chapter_id");
$chapter = mysqli_fetch_assoc($q);
if (!$chapter) die("Chương không tồn tại");

$story_id   = $chapter['story_id'];
$current_no = $chapter['chapter_number'];

// Paywall
$is_free   = ($current_no <= FREE_CHAPTERS);
$is_locked = false;
$user_id   = $_SESSION['user_id'] ?? null;
$is_admin  = (($_SESSION['role'] ?? '') === 'admin');
$user_coins = 0;

if (!$is_free) {
    if (!$user_id) {
        $is_locked = true;
    } else {
        $bought = mysqli_fetch_assoc(mysqli_query($con, "SELECT id FROM purchased_chapters WHERE user_id=$user_id AND chapter_id=$chapter_id"));
        if (!$bought) {
            $is_locked = true;
            $u = mysqli_fetch_assoc(mysqli_query($con, "SELECT coins FROM users WHERE id=$user_id"));
            $user_coins = $u['coins'] ?? 0;
        }
    }
}

if ($is_admin) {
    $is_locked = false;
}

// Nav
$prev_chap = mysqli_fetch_assoc(mysqli_query($con, "SELECT id,chapter_number,title FROM chapters WHERE story_id=$story_id AND chapter_number < $current_no ORDER BY chapter_number DESC LIMIT 1"));
$next_chap = mysqli_fetch_assoc(mysqli_query($con, "SELECT id,chapter_number,title FROM chapters WHERE story_id=$story_id AND chapter_number > $current_no ORDER BY chapter_number ASC LIMIT 1"));

// Tổng số chương
$total_q = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM chapters WHERE story_id=$story_id"));
$total_chapters = $total_q['cnt'] ?? 0;

// Coin hiển thị
$display_coins = 0;
if ($user_id) {
    $uc = mysqli_fetch_assoc(mysqli_query($con, "SELECT coins FROM users WHERE id=$user_id"));
    $display_coins = $uc['coins'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($chapter['story_title']) ?> · Chương <?= $current_no ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../frontend/css/style.css">
<style>
/* ── THEMES ── */
:root {
    --bg:        #0f0f0f;
    --surface:   #161616;
    --border:    #252525;
    --text:      #d4c9b8;
    --text-dim:  #666;
    --green:     #00d084;
    --gold:      #f5c518;
    --red:       #e74c3c;
    --font:      'Georgia', 'Times New Roman', serif;
    --font-size: 20px;
    --line-h:    1.95;
    --max-w:     720px;
}
body.theme-sepia  { --bg:#f5efe0; --surface:#ede3cc; --border:#d4c4a0; --text:#3d2b1f; }
body.theme-light  { --bg:#ffffff; --surface:#f5f5f5; --border:#e0e0e0; --text:#1a1a1a; }
body.theme-dark   { --bg:#0f0f0f; --surface:#161616; --border:#252525; --text:#d4c9b8; }

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font);
    font-size: var(--font-size);
    line-height: var(--line-h);
    transition: background .3s, color .3s;
    min-height: 100vh;
}

/* ── READING PROGRESS BAR ── */
#progress-bar {
    position: fixed; top: 0; left: 0; z-index: 9999;
    height: 3px; width: 0%; background: var(--green);
    transition: width .1s linear;
    box-shadow: 0 0 8px rgba(0,208,132,.6);
}

/* ── TOP NAV ── */
.top-nav {
    position: sticky; top: 0; z-index: 100;
    background: rgba(15,15,15,.92);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
    padding: 0 24px;
    height: 52px;
    display: flex; align-items: center; gap: 12px;
    font-size: 13px;
    transition: background .3s;
}
body.theme-light .top-nav  { background: rgba(255,255,255,.92); }
body.theme-sepia .top-nav  { background: rgba(245,239,224,.92); }

.top-nav a { color: var(--text-dim); text-decoration: none; display: flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 6px; transition: .15s; }
.top-nav a:hover { color: var(--text); background: rgba(255,255,255,.06); }
.top-nav .sep { color: var(--border); }
.top-nav .story-link { color: var(--text); font-weight: 600; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.coin-pill {
    margin-left: auto;
    background: rgba(245,197,24,.1);
    border: 1px solid rgba(245,197,24,.25);
    border-radius: 20px; padding: 4px 12px;
    color: var(--gold); font-size: 12px; font-weight: 700;
    display: flex; align-items: center; gap: 5px;
    text-decoration: none;
}
.coin-pill:hover { border-color: var(--gold); background: rgba(245,197,24,.15); color: var(--gold) !important; }

/* ── READER WRAPPER ── */
.reader-wrap {
    max-width: var(--max-w);
    margin: 0 auto;
    padding: 48px 24px 80px;
}

/* ── CHAPTER HEADER ── */
.chap-header { text-align: center; margin-bottom: 48px; }
.chap-story-title {
    font-size: 13px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 2px; color: var(--green); margin-bottom: 12px;
    text-decoration: none; display: inline-block;
}
.chap-story-title:hover { text-decoration: underline; }
.chap-number {
    font-size: 13px; color: var(--text-dim); margin-bottom: 8px;
    font-family: 'Segoe UI', sans-serif;
}
.chap-title {
    font-size: 26px; font-weight: 700; color: var(--text);
    line-height: 1.3; margin-bottom: 20px;
}
.chap-meta {
    display: flex; align-items: center; justify-content: center; gap: 16px;
    font-size: 12px; color: var(--text-dim); font-family: 'Segoe UI', sans-serif;
}
.chap-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.badge-free { background: rgba(0,208,132,.12); color: var(--green); border: 1px solid rgba(0,208,132,.3); }
.badge-paid { background: rgba(245,197,24,.12); color: var(--gold);  border: 1px solid rgba(245,197,24,.3); }

.chap-divider {
    display: flex; align-items: center; gap: 16px;
    margin: 32px 0;
}
.chap-divider::before, .chap-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}
.chap-divider span { color: var(--text-dim); font-size: 18px; }

/* ── CHAPTER CONTENT ── */
.chapter-content {
    font-size: var(--font-size);
    line-height: var(--line-h);
    color: var(--text);
    text-align: justify;
    word-break: break-word;
}
.chapter-content p { margin-bottom: 1.4em; }

/* ── NAV BUTTONS ── */
.nav-row {
    display: flex; justify-content: center; align-items: center;
    gap: 12px; margin: 48px 0 0;
    font-family: 'Segoe UI', sans-serif;
}
.btn-nav {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px;
    background: var(--surface); color: var(--text);
    border: 1px solid var(--border); border-radius: 8px;
    text-decoration: none; font-size: 13px; font-weight: 600;
    transition: .2s;
}
.btn-nav:hover { border-color: var(--green); color: var(--green); }
.btn-nav.disabled { opacity: .3; pointer-events: none; }
.btn-nav-primary {
    background: var(--green); color: #000; border-color: var(--green);
}
.btn-nav-primary:hover { background: #00b872; color: #000; border-color: #00b872; }

/* ── FLOATING TOOLBAR ── */
.toolbar {
    position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%);
    z-index: 200;
    background: rgba(20,20,20,.95);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 40px;
    padding: 8px 16px;
    display: flex; align-items: center; gap: 4px;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    font-family: 'Segoe UI', sans-serif;
}
body.theme-light .toolbar { background: rgba(255,255,255,.95); border-color: rgba(0,0,0,.1); box-shadow: 0 8px 32px rgba(0,0,0,.15); }
body.theme-sepia .toolbar { background: rgba(237,227,204,.97); border-color: rgba(0,0,0,.1); }

.tb-btn {
    width: 38px; height: 38px; border-radius: 50%;
    background: none; border: none; cursor: pointer;
    color: #aaa; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: .15s;
}
.tb-btn:hover { background: rgba(255,255,255,.1); color: #fff; }
body.theme-light .tb-btn { color: #555; }
body.theme-light .tb-btn:hover { background: rgba(0,0,0,.08); color: #000; }
body.theme-sepia .tb-btn { color: #6b4c2a; }

.tb-sep { width: 1px; height: 22px; background: rgba(255,255,255,.12); margin: 0 4px; }
body.theme-light .tb-sep { background: rgba(0,0,0,.12); }

.tb-label { font-size: 12px; color: #888; padding: 0 6px; min-width: 32px; text-align: center; }
body.theme-light .tb-label { color: #555; }

/* Settings panel */
.settings-panel {
    position: fixed; bottom: 84px; left: 50%; transform: translateX(-50%);
    z-index: 199;
    background: rgba(20,20,20,.97);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 16px;
    padding: 20px 24px;
    width: 300px;
    display: none;
    font-family: 'Segoe UI', sans-serif;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
}
body.theme-light .settings-panel { background: rgba(255,255,255,.97); border-color: rgba(0,0,0,.1); }
body.theme-sepia .settings-panel { background: rgba(237,227,204,.97); border-color: rgba(0,0,0,.1); }
.settings-panel.open { display: block; }

.sp-label { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 10px; }
.sp-row { margin-bottom: 18px; }

/* Theme buttons */
.theme-btns { display: flex; gap: 8px; }
.theme-btn {
    flex: 1; padding: 8px; border-radius: 8px; border: 2px solid transparent;
    cursor: pointer; font-size: 12px; font-weight: 600; transition: .15s;
}
.theme-btn.dark  { background: #1a1a1a; color: #d4c9b8; border-color: #333; }
.theme-btn.sepia { background: #f5efe0; color: #3d2b1f; border-color: #d4c4a0; }
.theme-btn.light { background: #fff;    color: #1a1a1a; border-color: #ddd; }
.theme-btn.active { border-color: var(--green) !important; }

/* Font family */
.font-btns { display: flex; gap: 8px; }
.font-btn {
    flex: 1; padding: 8px; border-radius: 8px; border: 2px solid #333;
    background: #1a1a1a; color: #aaa; cursor: pointer; font-size: 13px; transition: .15s;
}
body.theme-light .font-btn { background: #f0f0f0; border-color: #ddd; color: #555; }
body.theme-sepia .font-btn { background: #ede3cc; border-color: #c4b490; color: #5a3e28; }
.font-btn.active { border-color: var(--green) !important; color: var(--green) !important; }

/* ── PAYWALL ── */
.paywall-wrap {
    font-family: 'Segoe UI', sans-serif;
}
.content-preview {
    font-size: var(--font-size); line-height: var(--line-h);
    color: var(--text); text-align: justify;
    max-height: 220px; overflow: hidden;
    -webkit-mask-image: linear-gradient(to bottom, black 20%, transparent 100%);
    mask-image: linear-gradient(to bottom, black 20%, transparent 100%);
    pointer-events: none; user-select: none;
    margin-bottom: 0;
}
.paywall-box {
    background: linear-gradient(180deg, transparent 0%, var(--bg) 30%);
    text-align: center; padding: 60px 24px 40px;
    margin-top: -40px; position: relative;
}
.paywall-icon { font-size: 44px; color: var(--gold); margin-bottom: 14px; }
.paywall-box h3 { font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.paywall-box p  { font-size: 14px; color: var(--text-dim); margin-bottom: 24px; line-height: 1.6; }

.coin-stats {
    display: inline-flex; align-items: center; gap: 0;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 12px; overflow: hidden; margin-bottom: 24px;
}
.cs-item { padding: 14px 22px; text-align: center; }
.cs-item + .cs-item { border-left: 1px solid var(--border); }
.cs-val { font-size: 22px; font-weight: 800; color: var(--gold); line-height: 1; }
.cs-lbl { font-size: 10px; color: var(--text-dim); text-transform: uppercase; letter-spacing: .5px; margin-top: 4px; }

.paywall-actions { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
.btn-unlock {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 26px; background: var(--gold); color: #000;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 700;
    cursor: pointer; text-decoration: none; transition: .2s;
}
.btn-unlock:hover { background: #e6b800; transform: translateY(-1px); }
.btn-topup {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px; background: transparent; color: var(--gold);
    border: 2px solid rgba(245,197,24,.4); border-radius: 8px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    text-decoration: none; transition: .2s;
}
.btn-topup:hover { border-color: var(--gold); background: rgba(245,197,24,.08); }
.btn-login {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 26px; background: var(--green); color: #000;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 700;
    text-decoration: none; transition: .2s;
}
.btn-login:hover { background: #00b872; }

.reader-modal-content {
    position: relative;
    width: min(420px, calc(100vw - 32px));
}
.reader-modal-content form { width: 100%; }

@media (max-width: 600px) {
    .reader-wrap { padding: 28px 16px 100px; }
    .chap-title { font-size: 20px; }
    .toolbar { bottom: 16px; padding: 6px 12px; }
    .settings-panel { width: calc(100vw - 32px); }
}
</style>
</head>
<body class="theme-dark" id="readerBody">
<div id="progress-bar"></div>

<!-- TOP NAV -->
<nav class="top-nav">
    <a href="../frontend/home.php"><i class="fa-solid fa-house"></i></a>
    <span class="sep">/</span>
    <a href="read_story.php?story_id=<?= $story_id ?>" class="story-link">
        <?= htmlspecialchars($chapter['story_title']) ?>
    </a>
    <span class="sep">/</span>
    <span style="color:var(--text-dim);font-size:12px">Ch.<?= $current_no ?></span>

    <?php if ($user_id && !$is_admin): ?>
        <a href="../frontend/napcoin.php" class="coin-pill">
            <i class="fa-solid fa-coins"></i> <?= number_format($display_coins) ?>
        </a>
    <?php else: ?>

        <a href="#" class="coin-pill js-open-login" style="color:#666;border-color:#333;background:transparent">
            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
        </a>
    <?php endif; ?>
</nav>

<!-- READER -->
<div class="reader-wrap">

    <!-- Chapter header -->
    <div class="chap-header">
        <a href="read_story.php?story_id=<?= $story_id ?>" class="chap-story-title">
            <?= htmlspecialchars($chapter['story_title']) ?>
        </a>
        <div class="chap-number">Chương <?= $current_no ?> / <?= $total_chapters ?></div>
        <h1 class="chap-title"><?= htmlspecialchars($chapter['title']) ?></h1>
        <div class="chap-meta">
            <?php if ($is_free): ?>
                <span class="chap-badge badge-free"><i class="fa-solid fa-unlock"></i> Miễn phí</span>
            <?php else: ?>
                <span class="chap-badge badge-paid"><i class="fa-solid fa-coins"></i> <?= COINS_PER_CHAPTER ?> coin</span>
            <?php endif; ?>
            <span><i class="fa-regular fa-clock"></i> ~<?= max(1, round(mb_strlen(strip_tags($chapter['content'])) / 1000)) ?> phút đọc</span>
        </div>
    </div>

    <?php if (!$is_locked): ?>
    <!-- Nav top -->
    <div class="nav-row" style="margin-top:0;margin-bottom:40px">
        <a href="<?= $prev_chap ? 'read_chapter.php?chapter_id='.$prev_chap['id'] : '#' ?>"
           class="btn-nav <?= !$prev_chap ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i> Trước
        </a>
        <a href="read_story.php?story_id=<?= $story_id ?>" class="btn-nav">
            <i class="fa-solid fa-list"></i> Mục lục
        </a>
        <a href="<?= $next_chap ? 'read_chapter.php?chapter_id='.$next_chap['id'] : '#' ?>"
           class="btn-nav <?= !$next_chap ? 'disabled' : '' ?> btn-nav-primary">
            Tiếp <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>

    <div class="chap-divider"><span>✦</span></div>

    <!-- Content -->
    <div class="chapter-content" id="chapterContent">
        <?php
        $paragraphs = explode("\n", $chapter['content']);
        foreach ($paragraphs as $p) {
            $p = trim($p);
            if ($p !== '') echo '<p>' . htmlspecialchars($p) . '</p>';
        }
        ?>
    </div>

    <div class="chap-divider" style="margin-top:48px"><span>✦</span></div>

    <!-- Nav bottom -->
    <div class="nav-row">
        <a href="<?= $prev_chap ? 'read_chapter.php?chapter_id='.$prev_chap['id'] : '#' ?>"
           class="btn-nav <?= !$prev_chap ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i>
            <?= $prev_chap ? 'Ch.'.$prev_chap['chapter_number'] : 'Đầu truyện' ?>
        </a>
        <a href="read_story.php?story_id=<?= $story_id ?>" class="btn-nav">
            <i class="fa-solid fa-list"></i> Mục lục
        </a>
        <a href="<?= $next_chap ? 'read_chapter.php?chapter_id='.$next_chap['id'] : '#' ?>"
           class="btn-nav <?= !$next_chap ? 'disabled' : '' ?> btn-nav-primary">
            <?= $next_chap ? 'Ch.'.$next_chap['chapter_number'] : 'Hết truyện' ?>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>

    <?php else: ?>
    <!-- PAYWALL -->
    <div class="paywall-wrap">
        <div class="content-preview">
            <?php
            $preview = mb_substr($chapter['content'], 0, 400);
            $pars = explode("\n", $preview);
            foreach ($pars as $p) { $p = trim($p); if ($p) echo '<p>'.htmlspecialchars($p).'</p>'; }
            ?>
        </div>
        <div class="paywall-box">
            <div class="paywall-icon"><i class="fa-solid fa-lock"></i></div>
            <h3>Nội dung trả phí</h3>
            <p>3 chương đầu miễn phí. Từ chương 4 cần <strong style="color:var(--gold)"><?= COINS_PER_CHAPTER ?> coin</strong> để mở khoá.</p>

            <?php if ($user_id): ?>
                <div class="coin-stats">
                    <div class="cs-item">
                        <div class="cs-val"><?= number_format($user_coins) ?></div>
                        <div class="cs-lbl">Coin của bạn</div>
                    </div>
                    <div class="cs-item">
                        <div class="cs-val"><?= COINS_PER_CHAPTER ?></div>
                        <div class="cs-lbl">Cần để mở</div>
                    </div>
                    <div class="cs-item">
                        <div class="cs-val" style="color:<?= $user_coins >= COINS_PER_CHAPTER ? 'var(--green)' : 'var(--red)' ?>">
                            <?= $user_coins >= COINS_PER_CHAPTER ? 'Đủ ✓' : '-'.( COINS_PER_CHAPTER - $user_coins) ?>
                        </div>
                        <div class="cs-lbl">Trạng thái</div>
                    </div>
                </div>
                <div class="paywall-actions">
                    <?php if ($user_coins >= COINS_PER_CHAPTER): ?>
                        <form method="POST" action="buy_chapter.php">
                            <input type="hidden" name="chapter_id" value="<?= $chapter_id ?>">
                            <button type="submit" class="btn-unlock">
                                <i class="fa-solid fa-coins"></i> Dùng <?= COINS_PER_CHAPTER ?> coin để đọc
                            </button>
                        </form>
                        <?php if (!$is_admin): ?>
                            <a href="../frontend/napcoin.php" class="btn-topup"><i class="fa-solid fa-plus"></i> Nạp thêm</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!$is_admin): ?>
                            <a href="../frontend/napcoin.php?chapter_id=<?= $chapter_id ?>&need=<?= COINS_PER_CHAPTER ?>" class="btn-unlock">
                                <i class="fa-solid fa-coins"></i> Nạp coin để đọc
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="paywall-actions">
                    <a href="#" class="btn-login js-open-login">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập để đọc
                    </a>
                    <?php if (!$is_admin): ?>
                        <a href="../frontend/napcoin.php" class="btn-topup"><i class="fa-solid fa-circle-info"></i> Về coin</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /reader-wrap -->
<?php if (!$user_id): ?>
    <div id="loginModal" class="modal" style="display:none">
        <?php include '../frontend/dangnhap_form.php'; ?>
    </div>
<?php endif; ?>

<!-- FLOATING TOOLBAR -->
<div class="toolbar" id="toolbar">
    <!-- Prev -->
    <a href="<?= $prev_chap ? 'read_chapter.php?chapter_id='.$prev_chap['id'] : '#' ?>"
       class="tb-btn <?= !$prev_chap ? 'disabled' : '' ?>" title="Chương trước" style="text-decoration:none;color:inherit">
        <i class="fa-solid fa-chevron-left"></i>
    </a>

    <div class="tb-sep"></div>

    <!-- Font size -->
    <button class="tb-btn" onclick="changeFontSize(-1)" title="Giảm cỡ chữ"><i class="fa-solid fa-minus"></i></button>
    <span class="tb-label" id="fontSizeLabel">20</span>
    <button class="tb-btn" onclick="changeFontSize(1)"  title="Tăng cỡ chữ"><i class="fa-solid fa-plus"></i></button>

    <div class="tb-sep"></div>

    <!-- Settings -->
    <button class="tb-btn" onclick="toggleSettings()" title="Cài đặt hiển thị" id="settingsBtn">
        <i class="fa-solid fa-sliders"></i>
    </button>

    <!-- Bookmark -->
    <button class="tb-btn" onclick="toggleBookmark()" title="Đánh dấu" id="bookmarkBtn">
        <i class="fa-regular fa-bookmark"></i>
    </button>

    <div class="tb-sep"></div>

    <!-- Next -->
    <a href="<?= $next_chap ? 'read_chapter.php?chapter_id='.$next_chap['id'] : '#' ?>"
       class="tb-btn <?= !$next_chap ? 'disabled' : '' ?>" title="Chương sau" style="text-decoration:none;color:inherit">
        <i class="fa-solid fa-chevron-right"></i>
    </a>
</div>

<!-- SETTINGS PANEL -->
<div class="settings-panel" id="settingsPanel">
    <div class="sp-row">
        <div class="sp-label">Giao diện</div>
        <div class="theme-btns">
            <button class="theme-btn dark  active" onclick="setTheme('dark')" >🌙 Tối</button>
            <button class="theme-btn sepia"        onclick="setTheme('sepia')">📜 Sepia</button>
            <button class="theme-btn light"        onclick="setTheme('light')">☀️ Sáng</button>
        </div>
    </div>
    <div class="sp-row">
        <div class="sp-label">Font chữ</div>
        <div class="font-btns">
            <button class="font-btn active" onclick="setFont('serif','this')"  style="font-family:Georgia">Serif</button>
            <button class="font-btn"        onclick="setFont('sans','this')"   style="font-family:sans-serif">Sans</button>
            <button class="font-btn"        onclick="setFont('mono','this')"   style="font-family:monospace">Mono</button>
        </div>
    </div>
</div>

<script>
// ── Progress bar ──
window.addEventListener('scroll', () => {
    const doc  = document.documentElement;
    const pct  = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
    document.getElementById('progress-bar').style.width = pct + '%';
});

// ── Font size ──
let fontSize = parseInt(localStorage.getItem('reader_fs') || '20');
applyFontSize(fontSize);

function applyFontSize(s) {
    document.documentElement.style.setProperty('--font-size', s + 'px');
    document.getElementById('fontSizeLabel').textContent = s;
}
function changeFontSize(d) {
    fontSize = Math.min(32, Math.max(14, fontSize + d));
    applyFontSize(fontSize);
    localStorage.setItem('reader_fs', fontSize);
}

// ── Theme ──
const savedTheme = localStorage.getItem('reader_theme') || 'dark';
setTheme(savedTheme, true);

function setTheme(t, init) {
    document.getElementById('readerBody').className = 'theme-' + t;
    document.querySelectorAll('.theme-btn').forEach(b => b.classList.remove('active'));
    const btn = document.querySelector('.theme-btn.' + t);
    if (btn) btn.classList.add('active');
    if (!init) localStorage.setItem('reader_theme', t);
}

// ── Font family ──
const fonts = { serif: "'Georgia','Times New Roman',serif", sans: "'Segoe UI',Roboto,sans-serif", mono: "'Courier New',monospace" };
const savedFont = localStorage.getItem('reader_font') || 'serif';
setFont(savedFont, null, true);

function setFont(f, el, init) {
    document.documentElement.style.setProperty('--font', fonts[f] || fonts.serif);
    document.querySelectorAll('.font-btn').forEach(b => b.classList.remove('active'));
    const idx = Object.keys(fonts).indexOf(f);
    const btns = document.querySelectorAll('.font-btn');
    if (btns[idx]) btns[idx].classList.add('active');
    if (!init) localStorage.setItem('reader_font', f);
}

// ── Settings panel ──
function toggleSettings() {
    document.getElementById('settingsPanel').classList.toggle('open');
}
document.addEventListener('click', e => {
    const panel = document.getElementById('settingsPanel');
    const btn   = document.getElementById('settingsBtn');
    if (!panel.contains(e.target) && !btn.contains(e.target)) panel.classList.remove('open');
});

// ── Bookmark ──
const bmKey = 'bookmark_<?= $story_id ?>';
const bmBtn = document.getElementById('bookmarkBtn');
function toggleBookmark() {
    const cur = localStorage.getItem(bmKey);
    if (cur == '<?= $chapter_id ?>') {
        localStorage.removeItem(bmKey);
        bmBtn.innerHTML = '<i class="fa-regular fa-bookmark"></i>';
        bmBtn.title = 'Đánh dấu';
    } else {
        localStorage.setItem(bmKey, '<?= $chapter_id ?>');
        bmBtn.innerHTML = '<i class="fa-solid fa-bookmark" style="color:var(--gold)"></i>';
        bmBtn.title = 'Đã đánh dấu';
    }
}
// Khởi tạo bookmark state
if (localStorage.getItem(bmKey) == '<?= $chapter_id ?>') {
    bmBtn.innerHTML = '<i class="fa-solid fa-bookmark" style="color:var(--gold)"></i>';
    bmBtn.title = 'Đã đánh dấu';
}
const loginModal = document.getElementById('loginModal');
document.querySelectorAll('.js-open-login').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        if (loginModal) loginModal.style.setProperty('display', 'flex', 'important');
    });
});
</script>
<script src="script.js"></script>
</body>
</html>
