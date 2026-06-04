<?php
// buy_chapter.php - Xử lý mua chương bằng coin
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';
require_once __DIR__ . '/story_config.php';
require_once __DIR__ . '/require_auth.php';
require_once __DIR__ . '/../frontend/includes/paths.php';
require_active_user($_SERVER['HTTP_REFERER'] ?? '');

if (!isset($_POST['chapter_id']) || !is_numeric($_POST['chapter_id'])) {
    die("Yêu cầu không hợp lệ.");
}

$chapter_id = intval($_POST['chapter_id']);
$user_id    = intval($_SESSION['user_id']);

$ch = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT c.*, s.title AS story_title
     FROM chapters c JOIN stories s ON c.story_id = s.id
     WHERE c.id = $chapter_id"
));
if (!$ch) die("Chương không tồn tại.");

$read_url   = app_url('backend/read_chapter.php');
$napcoin_url = app_url('frontend/napcoin.php');

if ($ch['chapter_number'] <= FREE_CHAPTERS) {
    header("Location: $read_url?chapter_id=$chapter_id");
    exit();
}

$already = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id FROM purchased_chapters WHERE user_id=$user_id AND chapter_id=$chapter_id"
));
if ($already) {
    header("Location: $read_url?chapter_id=$chapter_id");
    exit();
}

$u = mysqli_fetch_assoc(mysqli_query($con, "SELECT coins FROM users WHERE id=$user_id"));
if (!$u) die("Không tìm thấy tài khoản.");

if ($u['coins'] < COINS_PER_CHAPTER) {
    header("Location: $napcoin_url?chapter_id=$chapter_id&need=" . COINS_PER_CHAPTER);
    exit();
}

mysqli_begin_transaction($con);
try {
    mysqli_query($con,
        "UPDATE users SET coins = coins - " . COINS_PER_CHAPTER . " WHERE id=$user_id AND coins >= " . COINS_PER_CHAPTER
    );
    if (mysqli_affected_rows($con) === 0) {
        throw new Exception("Không đủ coin.");
    }

    if (!mysqli_query($con,
        "INSERT INTO purchased_chapters (user_id, chapter_id, coins_spent)
         VALUES ($user_id, $chapter_id, " . COINS_PER_CHAPTER . ")"
    )) {
        throw new Exception("Không thể ghi nhận mua chương.");
    }

    $note = 'Mua chương ' . $ch['chapter_number'] . ' - ' . mysqli_real_escape_string($con, $ch['story_title']);
    if (!mysqli_query($con,
        "INSERT INTO coin_transactions (user_id, amount, vnd_amount, type, note)
         VALUES ($user_id, " . COINS_PER_CHAPTER . ", " . (COINS_PER_CHAPTER * 10) . ", 'spend', '$note')"
    )) {
        throw new Exception("Không thể ghi lịch sử giao dịch.");
    }

    mysqli_commit($con);
    header("Location: $read_url?chapter_id=$chapter_id");
    exit();
} catch (Exception $e) {
    mysqli_rollback($con);
    header("Location: $napcoin_url?chapter_id=$chapter_id&need=" . COINS_PER_CHAPTER . "&err=" . urlencode($e->getMessage()));
    exit();
}
