<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';
require_once __DIR__ . '/story_config.php';
require_once __DIR__ . '/../frontend/includes/paths.php';

if (!isset($_GET['story_id'])) die("Thiếu ID truyện");
$story_id = intval($_GET['story_id']);

$story = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM stories WHERE id = $story_id"));
if (!$story) die("Truyện không tồn tại");

require_once __DIR__ . '/require_auth.php';
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $st = mysqli_fetch_assoc(mysqli_query($con, "SELECT status FROM users WHERE id=$uid LIMIT 1"));
    if (!$st || ($st['status'] ?? '') === 'banned') {
        destroy_user_session();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt_views = mysqli_prepare($con, "UPDATE stories SET luot_xem = luot_xem + 1 WHERE id = ?");
    if ($stmt_views) {
        mysqli_stmt_bind_param($stmt_views, "i", $story_id);
        mysqli_stmt_execute($stmt_views);
        mysqli_stmt_close($stmt_views);
        $story['luot_xem'] = (int)($story['luot_xem'] ?? 0) + 1;
    }
}

$chapters = mysqli_query($con, "SELECT * FROM chapters WHERE story_id = $story_id ORDER BY chapter_number ASC");
$total    = mysqli_num_rows($chapters);

// Chương đầu để nút "Bắt đầu đọc"
mysqli_data_seek($chapters, 0);
$first_chap = mysqli_fetch_assoc($chapters);

// Kiểm tra đã lưu chưa
$user_id    = $_SESSION['user_id'] ?? null;
$username   = $_SESSION['username'] ?? null;
$is_admin   = (($_SESSION['role'] ?? '') === 'admin');
$is_saved   = false;
$user_coins = 0;
$bought_ids = [];

if ($user_id) {
    $sv = mysqli_fetch_assoc(mysqli_query($con, "SELECT id FROM user_stories WHERE user_id=$user_id AND story_id=$story_id"));
    $is_saved = (bool)$sv;
    $uc = mysqli_fetch_assoc(mysqli_query($con, "SELECT coins FROM users WHERE id=$user_id"));
    $user_coins = $uc['coins'] ?? 0;
    $bq = mysqli_query($con, "SELECT chapter_id FROM purchased_chapters WHERE user_id=$user_id");
    while ($br = mysqli_fetch_assoc($bq)) $bought_ids[] = $br['chapter_id'];
}

// Tìm chương đang đọc dở
$continue_chap = $first_chap;
if ($user_id && !empty($bought_ids)) {
    $last_bought = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT id, chapter_number, title FROM chapters
         WHERE story_id=$story_id AND id IN (" . implode(',', $bought_ids) . ")
         ORDER BY chapter_number DESC LIMIT 1"
    ));
    if ($last_bought) $continue_chap = $last_bought;
}

// ── XỬ LÝ BÌNH LUẬN ──────────────────────────────────────────
$comment_error   = '';
$comment_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_action'])) {
    if (!$user_id) {
        $comment_error = 'Bạn cần đăng nhập để bình luận.';
    } else {
        $action = $_POST['comment_action'];

        if ($action === 'post') {
            $content = trim($_POST['content'] ?? '');
            $parent_id = null;
            if (isset($_POST['parent_id']) && $_POST['parent_id'] !== '' && is_numeric($_POST['parent_id'])) {
                $parent_id = intval($_POST['parent_id']);
            }
            if (mb_strlen($content) < 2) {
                $comment_error = 'Bình luận quá ngắn.';
            } elseif (mb_strlen($content) > 1000) {
                $comment_error = 'Bình luận tối đa 1000 ký tự.';
            } elseif ($parent_id !== null) {
                $chk = mysqli_prepare($con, "SELECT id FROM comments WHERE id = ? AND story_id = ? LIMIT 1");
                mysqli_stmt_bind_param($chk, "ii", $parent_id, $story_id);
                mysqli_stmt_execute($chk);
                $chk_res = mysqli_stmt_get_result($chk);
                if (!$chk_res || mysqli_num_rows($chk_res) === 0) {
                    $comment_error = 'Bình luận gốc không hợp lệ.';
                } else {
                    $stmt_c = mysqli_prepare($con, "INSERT INTO comments (story_id, user_id, parent_id, content) VALUES (?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt_c, "iiis", $story_id, $user_id, $parent_id, $content);
                    mysqli_stmt_execute($stmt_c);
                    mysqli_stmt_close($stmt_c);
                    header("Location: read_story.php?story_id=$story_id#comments");
                    exit();
                }
            } else {
                $stmt_c = mysqli_prepare($con, "INSERT INTO comments (story_id, user_id, parent_id, content) VALUES (?, ?, NULL, ?)");
                mysqli_stmt_bind_param($stmt_c, "iis", $story_id, $user_id, $content);
                mysqli_stmt_execute($stmt_c);
                mysqli_stmt_close($stmt_c);
                header("Location: read_story.php?story_id=$story_id#comments");
                exit();
            }
        } elseif ($action === 'delete') {
            $cid = intval($_POST['comment_id'] ?? 0);
            $stmt_del = mysqli_prepare($con,
                "DELETE FROM comments WHERE story_id = ? AND user_id = ? AND (id = ? OR parent_id = ?)"
            );
            mysqli_stmt_bind_param($stmt_del, "iiii", $story_id, $user_id, $cid, $cid);
            mysqli_stmt_execute($stmt_del);
            mysqli_stmt_close($stmt_del);
            header("Location: read_story.php?story_id=$story_id#comments");
            exit();
        }
    }
}

// Lấy tất cả comment (kể cả reply), sắp xếp theo thời gian
$comments_q = mysqli_query($con,
    "SELECT c.*, u.username
     FROM comments c
     JOIN users u ON c.user_id = u.id
     WHERE c.story_id = $story_id
     ORDER BY c.created_at ASC"
);
$all_comments = [];
$comment_map  = []; // id => index
while ($cm = mysqli_fetch_assoc($comments_q)) {
    $all_comments[] = $cm;
    $comment_map[$cm['id']] = $cm;
}
$comment_count = count($all_comments);

// Nhóm: root comments và replies
$roots   = array_filter($all_comments, fn($c) => $c['parent_id'] === null);
$replies = [];
foreach ($all_comments as $c) {
    if ($c['parent_id'] !== null) $replies[$c['parent_id']][] = $c;
}
// ─────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($story['title']) ?> — KEWE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:      #0a0a0a;
    --surface: #111;
    --card:    #161616;
    --border:  #222;
    --green:   #00d084;
    --gold:    #f5c518;
    --red:     #e74c3c;
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
    height: 54px; padding: 0 28px;
    display: flex; align-items: center; gap: 10px;
}
.topbar a { color: var(--dim); text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 6px; transition: .15s; }
.topbar a:hover { color: var(--text); background: #1a1a1a; }
.topbar .sep { color: #333; }
.topbar .story-name { color: var(--text); font-weight: 600; font-size: 13px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.coin-pill {
    margin-left: auto; background: rgba(245,197,24,.1); border: 1px solid rgba(245,197,24,.25);
    border-radius: 20px; padding: 4px 12px; color: var(--gold); font-size: 12px; font-weight: 700;
    display: flex; align-items: center; gap: 5px; text-decoration: none;
}
.coin-pill:hover { border-color: var(--gold); color: var(--gold) !important; }

/* ── HERO ── */
.story-hero {
    position: relative; overflow: hidden;
    min-height: 380px; display: flex; align-items: flex-end;
}
.hero-bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    filter: blur(18px) brightness(.35) saturate(1.2);
    transform: scale(1.08);
}
.hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(10,10,10,1) 0%, rgba(10,10,10,.5) 60%, transparent 100%);
}
.hero-content {
    position: relative; z-index: 2;
    display: flex; gap: 32px; align-items: flex-end;
    padding: 40px 36px 36px; width: 100%;
}
.cover-img {
    width: 150px; flex-shrink: 0;
    border-radius: 10px;
    box-shadow: 0 16px 48px rgba(0,0,0,.7);
    aspect-ratio: 2/3; object-fit: cover;
}
.hero-info { flex: 1; }
.hero-category {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px; color: var(--green); margin-bottom: 10px;
}
.hero-title {
    font-size: 32px; font-weight: 900; color: #fff;
    line-height: 1.2; margin-bottom: 12px;
}
.hero-stats {
    display: flex; gap: 20px; margin-bottom: 16px;
    font-size: 13px; color: var(--dim);
}
.hero-stats span { display: flex; align-items: center; gap: 5px; }
.hero-stats i { color: var(--green); font-size: 11px; }
.hero-desc {
    font-size: 14px; color: rgba(255,255,255,.6);
    line-height: 1.65; max-width: 600px; margin-bottom: 24px;
}
.hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-start {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px; background: var(--green); color: #000;
    border-radius: 8px; font-size: 14px; font-weight: 700;
    text-decoration: none; transition: .2s;
    box-shadow: 0 4px 18px rgba(0,208,132,.3);
}
.btn-start:hover { background: #00b872; transform: translateY(-1px); color: #000; }
.btn-save {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; background: rgba(255,255,255,.08);
    color: #fff; border: 1.5px solid rgba(255,255,255,.2);
    border-radius: 8px; font-size: 14px; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: .2s;
}
.btn-save:hover { background: rgba(255,255,255,.14); border-color: rgba(255,255,255,.4); }
.btn-save.saved { color: #ff6b8a; border-color: rgba(255,107,138,.4); background: rgba(255,107,138,.08); }

/* ── MAIN LAYOUT ── */
.page-body {
    max-width: 1100px; margin: 0 auto;
    padding: 36px 28px 80px;
    display: grid; grid-template-columns: 1fr 300px; gap: 28px;
    align-items: start;
}

/* ── CHAPTER LIST ── */
.chapter-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
.panel-head {
    padding: 16px 20px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.panel-head h3 { font-size: 14px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 8px; }
.panel-head h3 i { color: var(--green); }
.chap-count { font-size: 12px; color: var(--dim); background: #1a1a1a; border: 1px solid var(--border); padding: 2px 10px; border-radius: 12px; }

/* Search chapters */
.chap-search {
    padding: 12px 16px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 8px;
    background: #0f0f0f;
}
.chap-search input {
    flex: 1; background: none; border: none; outline: none;
    color: var(--text); font-size: 13px;
}
.chap-search input::placeholder { color: var(--dim); }
.chap-search i { color: var(--dim); font-size: 13px; }

.chapter-list { max-height: 600px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #333 transparent; }
.chapter-row {
    display: flex; align-items: center;
    padding: 13px 20px; border-bottom: 1px solid #0f0f0f;
    text-decoration: none; color: var(--text);
    gap: 12px; transition: .15s;
}
.chapter-row:last-child { border-bottom: none; }
.chapter-row:hover { background: #1a1a1a; }
.chapter-row:hover .chap-name { color: var(--green); }

.chap-num {
    width: 32px; height: 32px; border-radius: 6px;
    background: #1a1a1a; display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: var(--dim); flex-shrink: 0;
}
.chap-num.free  { background: rgba(0,208,132,.1); color: var(--green); }
.chap-num.paid  { background: rgba(245,197,24,.1); color: var(--gold); }
.chap-num.owned { background: rgba(0,208,132,.15); color: var(--green); }

.chap-info { flex: 1; min-width: 0; }
.chap-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.chap-sub  { font-size: 11px; color: var(--dim); margin-top: 2px; }

.badge {
    font-size: 10px; font-weight: 700; padding: 2px 8px;
    border-radius: 10px; flex-shrink: 0; white-space: nowrap;
}
.badge-free  { background: rgba(0,208,132,.12); color: var(--green); border: 1px solid rgba(0,208,132,.25); }
.badge-coin  { background: rgba(245,197,24,.12); color: var(--gold);  border: 1px solid rgba(245,197,24,.25); }
.badge-owned { background: rgba(0,208,132,.12); color: var(--green); border: 1px solid rgba(0,208,132,.25); }

/* ── RIGHT SIDEBAR ── */
.right-sidebar { display: flex; flex-direction: column; gap: 16px; }

.info-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
.info-card-head { padding: 14px 18px; border-bottom: 1px solid var(--border); font-size: 12px; font-weight: 700; color: var(--dim); text-transform: uppercase; letter-spacing: .8px; }
.info-row { display: flex; align-items: center; gap: 12px; padding: 13px 18px; border-bottom: 1px solid #0f0f0f; }
.info-row:last-child { border-bottom: none; }
.info-icon { width: 32px; height: 32px; border-radius: 7px; background: #1a1a1a; display: flex; align-items: center; justify-content: center; color: var(--green); font-size: 13px; flex-shrink: 0; }
.info-lbl { font-size: 11px; color: var(--dim); margin-bottom: 2px; }
.info-val { font-size: 13px; font-weight: 600; color: var(--text); }

/* Coin card */
.coin-card { background: linear-gradient(135deg, #1a1500, #2b2000); border: 1px solid rgba(245,197,24,.2); border-radius: var(--radius); padding: 20px 18px; }
.coin-card-lbl { font-size: 11px; color: #a08020; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 8px; }
.coin-amount { font-size: 30px; font-weight: 800; color: var(--gold); display: flex; align-items: center; gap: 8px; line-height: 1; }
.coin-sub { font-size: 12px; color: #a08020; margin-top: 6px; }
.btn-topup {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; margin-top: 14px; padding: 9px;
    background: var(--gold); color: #000; border: none; border-radius: 7px;
    font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; transition: .2s;
}
.btn-topup:hover { background: #e6b800; color: #000; }

/* ── EMPTY ── */
.empty-chap { padding: 40px; text-align: center; color: var(--dim); }
.empty-chap i { font-size: 36px; display: block; margin-bottom: 10px; opacity: .25; }

@media (max-width: 800px) {
    .page-body { grid-template-columns: 1fr; }
    .hero-content { flex-direction: column; align-items: flex-start; padding: 24px 20px; }
    .cover-img { width: 110px; }
    .hero-title { font-size: 22px; }
    .right-sidebar { order: -1; }
}

/* ── COMMENTS ── */
.comments-section {
    max-width: 1100px; margin: 0 auto;
    padding: 0 28px 60px;
}
.comments-section .section-title {
    font-size: 18px; font-weight: 800; color: var(--text);
    display: flex; align-items: center; gap: 10px;
    padding-bottom: 16px; border-bottom: 1px solid var(--border);
    margin-bottom: 24px;
}
.comments-section .section-title i { color: var(--green); }
.comments-section .section-title .cnt {
    font-size: 13px; font-weight: 600; color: var(--dim);
    background: #1a1a1a; border: 1px solid var(--border);
    padding: 2px 10px; border-radius: 12px;
}

/* Form gửi comment */
.comment-form-box {
    background: var(--card); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 20px;
    margin-bottom: 28px;
}
.comment-form-box .form-header {
    display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
}
.avatar-sm {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #1a3a2a, #0d2b1a);
    border: 2px solid var(--green);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700; color: var(--green);
    flex-shrink: 0;
}
.form-username { font-size: 14px; font-weight: 600; color: var(--text); }
.comment-textarea {
    width: 100%; background: #0f0f0f; border: 1px solid var(--border);
    border-radius: 8px; padding: 12px 14px; color: var(--text);
    font-size: 14px; font-family: 'Segoe UI', sans-serif;
    resize: vertical; min-height: 90px; outline: none; transition: .2s;
    line-height: 1.6;
}
.comment-textarea:focus { border-color: var(--green); }
.comment-textarea::placeholder { color: var(--dim); }
.form-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 10px;
}
.char-count { font-size: 12px; color: var(--dim); }
.btn-submit-comment {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 22px; background: var(--green); color: #000;
    border: none; border-radius: 7px; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: .2s;
}
.btn-submit-comment:hover { background: #00b872; }
.btn-submit-comment:disabled { background: #1a4a30; color: #0d2b1a; cursor: not-allowed; }

/* Login prompt */
.login-prompt {
    background: var(--card); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 24px;
    text-align: center; margin-bottom: 28px;
    color: var(--dim); font-size: 14px;
}
.login-prompt a {
    color: var(--green); font-weight: 700; text-decoration: none;
}
.login-prompt a:hover { text-decoration: underline; }

/* Alert */
.alert-err {
    background: #1a0808; border: 1px solid #3a1010; color: #e74c3c;
    border-radius: 8px; padding: 10px 14px; font-size: 13px;
    margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
}

/* Comment item */
.comment-list { display: flex; flex-direction: column; gap: 0; }
.comment-item {
    padding: 18px 0;
    border-bottom: 1px solid #111;
}
.comment-item:last-child { border-bottom: none; }
.comment-item.reply {
    margin-left: 48px;
    padding: 14px 0 14px 16px;
    border-left: 2px solid var(--border);
    border-bottom: none;
}
.comment-head {
    display: flex; align-items: center; gap: 10px; margin-bottom: 8px;
}
.comment-username {
    font-size: 13px; font-weight: 700; color: var(--text);
}
.comment-username.is-me { color: var(--green); }
.comment-time {
    font-size: 11px; color: var(--dim); margin-left: 4px;
}
.comment-body {
    font-size: 14px; color: rgba(255,255,255,.75);
    line-height: 1.65; word-break: break-word;
    white-space: pre-wrap;
}
.comment-actions {
    display: flex; gap: 12px; margin-top: 8px;
}
.btn-reply, .btn-delete {
    background: none; border: none; cursor: pointer;
    font-size: 12px; display: flex; align-items: center; gap: 4px;
    padding: 3px 0; transition: .15s;
}
.btn-reply  { color: var(--dim); }
.btn-reply:hover  { color: var(--green); }
.btn-delete { color: #3a1010; }
.btn-delete:hover { color: var(--red); }

/* Inline reply form */
.reply-form-box {
    margin-top: 12px; margin-left: 48px;
    background: #0f0f0f; border: 1px solid var(--border);
    border-radius: 8px; padding: 14px; display: none;
}
.reply-form-box.open { display: block; }
.reply-form-box textarea {
    width: 100%; background: transparent; border: none; outline: none;
    color: var(--text); font-size: 13px; font-family: 'Segoe UI', sans-serif;
    resize: none; min-height: 70px; line-height: 1.6;
}
.reply-form-box textarea::placeholder { color: var(--dim); }
.reply-form-footer {
    display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;
    border-top: 1px solid var(--border); padding-top: 8px;
}
.btn-cancel-reply {
    background: none; border: 1px solid var(--border); color: var(--dim);
    padding: 6px 14px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: .15s;
}
.btn-cancel-reply:hover { color: var(--text); border-color: #444; }
.btn-send-reply {
    background: var(--green); color: #000; border: none;
    padding: 6px 16px; border-radius: 6px; font-size: 12px;
    font-weight: 700; cursor: pointer; transition: .15s;
}
.btn-send-reply:hover { background: #00b872; }

/* Empty comments */
.no-comments {
    text-align: center; padding: 48px 20px; color: var(--dim);
}
.no-comments i { font-size: 40px; display: block; margin-bottom: 12px; opacity: .2; }
.no-comments p { font-size: 14px; }
</style>
</head>
<body>

<!-- TOPBAR -->
<nav class="topbar">
    <a href="../frontend/home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <span class="story-name"><?= htmlspecialchars($story['title']) ?></span>

    <?php if ($user_id && !$is_admin): ?>
        <a href="../frontend/napcoin.php" class="coin-pill">
            <i class="fa-solid fa-coins"></i> <?= number_format($user_coins) ?> coin
        </a>
    <?php else: ?>
        <a href="<?= htmlspecialchars(app_login_url($_SERVER['REQUEST_URI'])) ?>" class="coin-pill" style="color:#555;border-color:#333;background:transparent">
            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
        </a>
    <?php endif; ?>
</nav>

<!-- HERO -->
<section class="story-hero">
    <div class="hero-bg" style="background-image:url('<?= htmlspecialchars(cover_url($story['cover'])) ?>')"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <img src="<?= htmlspecialchars(cover_url($story['cover'])) ?>"
             class="cover-img"
             alt="<?= htmlspecialchars($story['title']) ?>"
             onerror="this.src='../frontend/img/sach2.jpg'">
        <div class="hero-info">
            <div class="hero-category"><i class="fa-solid fa-bookmark"></i> KEWE · Đọc truyện</div>
            <h1 class="hero-title"><?= htmlspecialchars($story['title']) ?></h1>
            <div class="hero-stats">
                <span><i class="fa-solid fa-list"></i> <?= $total ?> chương</span>
                <span><i class="fa-solid fa-unlock"></i> <?= FREE_CHAPTERS ?> chương miễn phí</span>
                <span><i class="fa-solid fa-coins"></i> <?= COINS_PER_CHAPTER ?> coin / chương</span>
                <span><i class="fa-solid fa-eye"></i> <?= number_format((int)($story['luot_xem'] ?? 0)) ?> lượt xem</span>
            </div>
            <?php
            $cat_code = trim($story['description'] ?? '');
            $cat_label = story_category_label($cat_code);
            ?>
            <p class="hero-desc">
                <?php if ($cat_code !== ''): ?>
                    <strong>Thể loại:</strong> <?= htmlspecialchars($cat_label) ?>
                    <?php if ($cat_label !== $cat_code): ?>
                        <span style="color:var(--dim);font-size:12px;">(<?= htmlspecialchars($cat_code) ?>)</span>
                    <?php endif; ?>
                <?php else: ?>
                    Khám phá câu chuyện trên KEWE — đọc miễn phí <?= FREE_CHAPTERS ?> chương đầu.
                <?php endif; ?>
            </p>
            <div class="hero-actions">
                <?php if ($first_chap): ?>
                <a href="read_chapter.php?chapter_id=<?= $first_chap['id'] ?>" class="btn-start">
                    <i class="fa-solid fa-book-open"></i>
                    <?= $continue_chap && $continue_chap['id'] != $first_chap['id'] ? 'Đọc tiếp Ch.'.$continue_chap['chapter_number'] : 'Bắt đầu đọc' ?>
                </a>
                <?php endif; ?>

                <?php if ($user_id && !$is_admin): ?>
                <form method="POST" action="../frontend/luutruyen.php">
                    <input type="hidden" name="story_id" value="<?= $story_id ?>">
                    <button type="submit" class="btn-save <?= $is_saved ? 'saved' : '' ?>">
                        <i class="fa-<?= $is_saved ? 'solid' : 'regular' ?> fa-heart"></i>
                        <?= $is_saved ? 'Đã lưu' : 'Lưu truyện' ?>
                    </button>
                </form>
                <?php else: ?>
                <a href="<?= htmlspecialchars(app_login_url($_SERVER['REQUEST_URI'])) ?>" class="btn-save">
                    <i class="fa-regular fa-heart"></i> Lưu truyện
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- BODY -->
<div class="page-body">

    <!-- LEFT: Chapter list -->
    <div class="chapter-panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-list-ol"></i> Danh sách chương</h3>
            <span class="chap-count"><?= $total ?> chương</span>
        </div>
        <div class="chap-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="chapSearch" placeholder="Tìm chương..." oninput="filterChapters(this.value)">
        </div>
        <div class="chapter-list" id="chapterList">
            <?php
            mysqli_data_seek($chapters, 0);
            while ($ch = mysqli_fetch_assoc($chapters)):
                $is_free_chap = ($ch['chapter_number'] <= FREE_CHAPTERS);
                $is_owned     = in_array($ch['id'], $bought_ids);
                $num_class    = $is_free_chap ? 'free' : ($is_owned ? 'owned' : 'paid');
            ?>
            <a href="read_chapter.php?chapter_id=<?= $ch['id'] ?>" class="chapter-row" data-title="chương <?= $ch['chapter_number'] ?> <?= strtolower($ch['title']) ?>">
                <div class="chap-num <?= $num_class ?>">
                    <?php if ($is_free_chap || $is_owned): ?>
                        <i class="fa-solid fa-unlock" style="font-size:10px"></i>
                    <?php else: ?>
                        <?= $ch['chapter_number'] ?>
                    <?php endif; ?>
                </div>
                <div class="chap-info">
                    <div class="chap-name">Chương <?= $ch['chapter_number'] ?>: <?= htmlspecialchars($ch['title']) ?></div>
                    <div class="chap-sub">
                        <?php if ($is_free_chap): ?>Miễn phí
                        <?php elseif ($is_owned): ?>Đã mua
                        <?php else: ?><?= (int) COINS_PER_CHAPTER ?> coin để mở khoá
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($is_free_chap): ?>
                    <span class="badge badge-free">FREE</span>
                <?php elseif ($is_owned): ?>
                    <span class="badge badge-owned"><i class="fa-solid fa-check"></i> Đã mua</span>
                <?php else: ?>
                    <span class="badge badge-coin"><i class="fa-solid fa-coins"></i> <?= (int) COINS_PER_CHAPTER ?></span>
                <?php endif; ?>
            </a>
            <?php endwhile; ?>
            <?php if ($total === 0): ?>
            <div class="empty-chap">
                <i class="fa-solid fa-book-open"></i>
                <p>Chưa có chương nào</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RIGHT: Sidebar -->
    <aside class="right-sidebar">

        <!-- Coin card (chỉ hiện khi đăng nhập) -->
        <?php if ($user_id && !$is_admin): ?>
        <div class="coin-card">
            <div class="coin-card-lbl"><i class="fa-solid fa-coins"></i> Số dư coin</div>
            <div class="coin-amount"><i class="fa-solid fa-coins"></i> <?= number_format($user_coins) ?></div>
            <div class="coin-sub">≈ <?= number_format($user_coins * 10) ?> VND</div>
            <a href="../frontend/napcoin.php" class="btn-topup"><i class="fa-solid fa-plus"></i> Nạp coin</a>
        </div>
        <?php endif; ?>

        <!-- Thông tin truyện -->
        <div class="info-card">
            <div class="info-card-head">Thông tin</div>
            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-list"></i></div>
                <div><div class="info-lbl">Tổng chương</div><div class="info-val"><?= $total ?> chương</div></div>
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-unlock"></i></div>
                <div><div class="info-lbl">Miễn phí</div><div class="info-val"><?= (int) FREE_CHAPTERS ?> chương đầu</div></div>
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-coins"></i></div>
                <div><div class="info-lbl">Giá mỗi chương</div><div class="info-val" style="color:var(--gold)"><?= (int) COINS_PER_CHAPTER ?> coin = <?= (int)(COINS_PER_CHAPTER * 10) ?> VND</div></div>
            </div>
            <?php if ($user_id && !empty($bought_ids)): ?>
            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-check-circle"></i></div>
                <div><div class="info-lbl">Đã mua</div><div class="info-val" style="color:var(--green)"><?= count($bought_ids) ?> chương</div></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Nav nhanh -->
        <div class="info-card">
            <div class="info-card-head">Điều hướng</div>
            <?php if ($first_chap): ?>
            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-play"></i></div>
                <div>
                    <div class="info-lbl">Bắt đầu từ</div>
                    <a href="read_chapter.php?chapter_id=<?= $first_chap['id'] ?>" style="font-size:13px;font-weight:600;color:var(--green);text-decoration:none">
                        Chương 1: <?= htmlspecialchars(mb_strimwidth($first_chap['title'],0,30,'...')) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-house"></i></div>
                <div>
                    <div class="info-lbl">Quay về</div>
                    <a href="../frontend/home.php" style="font-size:13px;font-weight:600;color:var(--dim);text-decoration:none">Trang chủ</a>
                </div>
            </div>
            <?php if (!$is_admin): ?>
                <div class="info-row">
                    <div class="info-icon"><i class="fa-solid fa-book"></i></div>
                    <div>
                        <div class="info-lbl">Tủ sách</div>
                        <a href="../frontend/tusach.php" style="font-size:13px;font-weight:600;color:var(--dim);text-decoration:none">Xem tủ sách</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </aside>
</div>

</div><!-- /page-body -->

<!-- ══ BÌNH LUẬN ══ -->
<section class="comments-section" id="comments">
    <div class="section-title">
        <i class="fa-solid fa-comments"></i>
        Bình luận
        <span class="cnt"><?= $comment_count ?></span>
    </div>

    <?php if ($comment_error): ?>
    <div class="alert-err"><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($comment_error) ?></div>
    <?php endif; ?>

    <!-- Form gửi comment -->
    <?php if ($user_id): ?>
    <div class="comment-form-box">
        <div class="form-header">
            <div class="avatar-sm"><?= mb_strtoupper(mb_substr($username, 0, 1)) ?></div>
            <span class="form-username"><?= htmlspecialchars($username) ?></span>
        </div>
        <form method="POST" action="read_story.php?story_id=<?= $story_id ?>#comments">
            <input type="hidden" name="comment_action" value="post">
            <input type="hidden" name="parent_id" value="">
            <textarea name="content" class="comment-textarea" id="mainCommentBox"
                      placeholder="Chia sẻ cảm nhận của bạn về truyện này..."
                      maxlength="1000" oninput="updateCharCount(this,'mainCount')"></textarea>
            <div class="form-footer">
                <span class="char-count"><span id="mainCount">0</span> / 1000</span>
                <button type="submit" class="btn-submit-comment">
                    <i class="fa-solid fa-paper-plane"></i> Gửi bình luận
                </button>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="login-prompt">
        <i class="fa-regular fa-comment" style="font-size:28px;display:block;margin-bottom:10px;opacity:.3"></i>
        <a href="<?= htmlspecialchars(app_login_url($_SERVER['REQUEST_URI'])) ?>">Đăng nhập</a>
        để tham gia bình luận
    </div>
    <?php endif; ?>

    <!-- Danh sách comment -->
    <div class="comment-list">
        <?php if (empty($roots)): ?>
        <div class="no-comments">
            <i class="fa-regular fa-comments"></i>
            <p>Chưa có bình luận nào. Hãy là người đầu tiên!</p>
        </div>
        <?php else: ?>
            <?php foreach ($roots as $cm): ?>
            <div class="comment-item" id="cm-<?= $cm['id'] ?>">
                <div class="comment-head">
                    <div class="avatar-sm" style="width:32px;height:32px;font-size:12px">
                        <?= mb_strtoupper(mb_substr($cm['username'], 0, 1)) ?>
                    </div>
                    <span class="comment-username <?= ($cm['user_id'] == $user_id) ? 'is-me' : '' ?>">
                        <?= htmlspecialchars($cm['username']) ?>
                        <?php if ($cm['user_id'] == $user_id): ?>
                            <span style="font-size:10px;color:var(--green);font-weight:400"> · Bạn</span>
                        <?php endif; ?>
                    </span>
                    <span class="comment-time"><?= date('d/m/Y H:i', strtotime($cm['created_at'])) ?></span>
                </div>
                <div class="comment-body"><?= htmlspecialchars($cm['content']) ?></div>
                <div class="comment-actions">
                    <?php if ($user_id): ?>
                    <button class="btn-reply" onclick="toggleReply(<?= $cm['id'] ?>)">
                        <i class="fa-solid fa-reply"></i> Trả lời
                        <?php if (!empty($replies[$cm['id']])): ?>
                            (<?= count($replies[$cm['id']]) ?>)
                        <?php endif; ?>
                    </button>
                    <?php endif; ?>
                    <?php if ($cm['user_id'] == $user_id): ?>
                    <form method="POST" action="read_story.php?story_id=<?= $story_id ?>#comments"
                          style="display:inline" onsubmit="return confirm('Xoá bình luận này?')">
                        <input type="hidden" name="comment_action" value="delete">
                        <input type="hidden" name="comment_id" value="<?= $cm['id'] ?>">
                        <button type="submit" class="btn-delete">
                            <i class="fa-solid fa-trash"></i> Xoá
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Replies -->
                <?php if (!empty($replies[$cm['id']])): ?>
                <div id="replies-<?= $cm['id'] ?>">
                    <?php foreach ($replies[$cm['id']] as $rp): ?>
                    <div class="comment-item reply" id="cm-<?= $rp['id'] ?>">
                        <div class="comment-head">
                            <div class="avatar-sm" style="width:28px;height:28px;font-size:11px">
                                <?= mb_strtoupper(mb_substr($rp['username'], 0, 1)) ?>
                            </div>
                            <span class="comment-username <?= ($rp['user_id'] == $user_id) ? 'is-me' : '' ?>">
                                <?= htmlspecialchars($rp['username']) ?>
                            </span>
                            <span class="comment-time"><?= date('d/m/Y H:i', strtotime($rp['created_at'])) ?></span>
                        </div>
                        <div class="comment-body"><?= htmlspecialchars($rp['content']) ?></div>
                        <?php if ($rp['user_id'] == $user_id): ?>
                        <div class="comment-actions">
                            <form method="POST" action="read_story.php?story_id=<?= $story_id ?>#comments"
                                  style="display:inline" onsubmit="return confirm('Xoá bình luận này?')">
                                <input type="hidden" name="comment_action" value="delete">
                                <input type="hidden" name="comment_id" value="<?= $rp['id'] ?>">
                                <button type="submit" class="btn-delete">
                                    <i class="fa-solid fa-trash"></i> Xoá
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Inline reply form -->
                <?php if ($user_id): ?>
                <div class="reply-form-box" id="reply-form-<?= $cm['id'] ?>">
                    <form method="POST" action="read_story.php?story_id=<?= $story_id ?>#cm-<?= $cm['id'] ?>">
                        <input type="hidden" name="comment_action" value="post">
                        <input type="hidden" name="parent_id" value="<?= $cm['id'] ?>">
                        <textarea name="content" placeholder="Trả lời <?= htmlspecialchars($cm['username']) ?>..."
                                  maxlength="1000" oninput="updateCharCount(this,'rc<?= $cm['id'] ?>')"></textarea>
                        <div class="reply-form-footer">
                            <span style="font-size:11px;color:var(--dim);margin-right:auto">
                                <span id="rc<?= $cm['id'] ?>">0</span>/1000
                            </span>
                            <button type="button" class="btn-cancel-reply" onclick="toggleReply(<?= $cm['id'] ?>)">Huỷ</button>
                            <button type="submit" class="btn-send-reply"><i class="fa-solid fa-paper-plane"></i> Gửi</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
function filterChapters(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#chapterList .chapter-row').forEach(row => {
        const title = row.dataset.title || '';
        row.style.display = (!q || title.includes(q)) ? '' : 'none';
    });
}
function toggleReply(id) {
    const box = document.getElementById('reply-form-' + id);
    if (!box) return;
    box.classList.toggle('open');
    if (box.classList.contains('open')) box.querySelector('textarea').focus();
}
function updateCharCount(el, countId) {
    const el2 = document.getElementById(countId);
    if (el2) el2.textContent = el.value.length;
}
</script>
</body>
</html>
