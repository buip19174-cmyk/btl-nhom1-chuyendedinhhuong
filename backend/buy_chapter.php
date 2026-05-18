<?php
// buy_chapter.php - Xử lý mua chương bằng coin
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';

const COINS_PER_CHAPTER = 3;
const FREE_CHAPTERS     = 3;

// Phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/dangnhap_form.php?redirect=" . urlencode($_SERVER['HTTP_REFERER'] ?? ''));
    exit();
}

if (!isset($_POST['chapter_id']) || !is_numeric($_POST['chapter_id'])) {
    die("Yêu cầu không hợp lệ.");
}

$chapter_id = intval($_POST['chapter_id']);
$user_id    = intval($_SESSION['user_id']);

// Lấy thông tin chương
$ch = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT c.*, s.title AS story_title
     FROM chapters c JOIN stories s ON c.story_id = s.id
     WHERE c.id = $chapter_id"
));
if (!$ch) die("Chương không tồn tại.");

// Chương free thì không cần mua
if ($ch['chapter_number'] <= FREE_CHAPTERS) {
    header("Location: read_chapter.php?chapter_id=$chapter_id");
    exit();
}

// Kiểm tra đã mua chưa
$already = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id FROM purchased_chapters WHERE user_id=$user_id AND chapter_id=$chapter_id"
));
if ($already) {
    header("Location: read_chapter.php?chapter_id=$chapter_id");
    exit();
}

// Lấy số coin hiện tại
$u = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT coins FROM users WHERE id=$user_id"
));
if (!$u) die("Không tìm thấy tài khoản.");

if ($u['coins'] < COINS_PER_CHAPTER) {
    // Không đủ coin → chuyển sang trang nạp
    header("Location: ../frontend/napcoin.php?chapter_id=$chapter_id&need=" . COINS_PER_CHAPTER);
    exit();
}

// Trừ coin và lưu lịch sử mua — dùng transaction để an toàn
mysqli_begin_transaction($con);
try {
    mysqli_query($con,
        "UPDATE users SET coins = coins - " . COINS_PER_CHAPTER . " WHERE id=$user_id AND coins >= " . COINS_PER_CHAPTER
    );
    if (mysqli_affected_rows($con) === 0) {
        throw new Exception("Không đủ coin.");
    }

    mysqli_query($con,
        "INSERT INTO purchased_chapters (user_id, chapter_id, coins_spent)
         VALUES ($user_id, $chapter_id, " . COINS_PER_CHAPTER . ")"
    );

    mysqli_query($con,
        "INSERT INTO coin_transactions (user_id, amount, vnd_amount, type, note)
         VALUES ($user_id, " . COINS_PER_CHAPTER . ", " . (COINS_PER_CHAPTER * 10) . ", 'spend',
         'Mua chương " . $ch['chapter_number'] . " - " . mysqli_real_escape_string($con, $ch['story_title']) . "')"
    );

    mysqli_commit($con);
    header("Location: read_chapter.php?chapter_id=$chapter_id");
    exit();
} catch (Exception $e) {
    mysqli_rollback($con);
    header("Location: ../frontend/napcoin.php?chapter_id=$chapter_id&need=" . COINS_PER_CHAPTER . "&err=" . urlencode($e->getMessage()));
    exit();
}
