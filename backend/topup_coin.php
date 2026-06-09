<?php
// topup_coin.php - Xử lý nạp coin
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';
require_once __DIR__ . '/../frontend/includes/paths.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . app_login_url($_SERVER['REQUEST_URI'] ?? ''));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['coins']) || !is_numeric($_POST['coins'])) {
    header("Location: ../frontend/napcoin.php?err=invalid");
    exit();
}

$user_id    = intval($_SESSION['user_id']);
$coins      = intval($_POST['coins']);
$chapter_id = isset($_POST['chapter_id']) ? intval($_POST['chapter_id']) : 0;

// Gói nạp hợp lệ: 10, 30, 50, 100, 200, 500
$valid_packs = [10, 30, 50, 100, 200, 500];
if (!in_array($coins, $valid_packs)) {
    header("Location: ../frontend/napcoin.php?err=invalid_pack");
    exit();
}

$vnd = $coins * 100; // 1 coin = 100 VND

mysqli_begin_transaction($con);
try {
    mysqli_query($con, "UPDATE users SET coins = coins + $coins WHERE id=$user_id");

    mysqli_query($con,
        "INSERT INTO coin_transactions (user_id, amount, vnd_amount, type, note)
         VALUES ($user_id, $coins, $vnd, 'topup', 'Nạp $coins coin ($vnd VND)')"
    );

    mysqli_commit($con);

    // Cập nhật session
    $_SESSION['coins'] = ($u['coins'] ?? 0) + $coins;

    if ($chapter_id > 0) {
        header("Location: ../frontend/napcoin.php?success=1&coins=$coins&chapter_id=$chapter_id");
    } else {
        header("Location: ../frontend/napcoin.php?success=1&coins=$coins");
    }
    exit();
} catch (Exception $e) {
    mysqli_rollback($con);
    header("Location: ../frontend/napcoin.php?err=server");
    exit();
}
