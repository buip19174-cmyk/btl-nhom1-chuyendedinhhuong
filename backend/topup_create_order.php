<?php
// topup_create_order.php — Tạo đơn nạp coin, chuyển sang trang thanh toán QR
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';
include_once 'payment_config.php';
require_once __DIR__ . '/require_auth.php';
require_active_user($_SERVER['REQUEST_URI'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['coins']) || !is_numeric($_POST['coins'])) {
    header("Location: ../frontend/napcoin.php?err=invalid");
    exit();
}

$user_id    = intval($_SESSION['user_id']);
$coins      = intval($_POST['coins']);
$chapter_id = isset($_POST['chapter_id']) ? intval($_POST['chapter_id']) : 0;

if (!in_array($coins, payment_valid_packs(), true)) {
    header("Location: ../frontend/napcoin.php?err=invalid_pack");
    exit();
}

$vnd      = payment_vnd_for_coins($coins);
$order_id = payment_generate_order_id();
$chapter_sql = $chapter_id > 0 ? $chapter_id : 'NULL';

mysqli_query($con,
    "INSERT INTO topup_orders (order_id, user_id, coins, vnd_amount, status, chapter_id)
     VALUES ('" . mysqli_real_escape_string($con, $order_id) . "', $user_id, $coins, $vnd, 'pending', $chapter_sql)"
);

if (mysqli_errno($con)) {
    header("Location: ../frontend/napcoin.php?err=server");
    exit();
}

header("Location: ../frontend/thanhtoan.php?order_id=" . urlencode($order_id));
exit();
