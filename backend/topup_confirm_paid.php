<?php
// topup_confirm_paid.php — Xác nhận thanh toán demo, cộng coin
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';
include_once 'payment_config.php';
require_once __DIR__ . '/require_auth.php';
require_active_user($_SERVER['HTTP_REFERER'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['order_id'])) {
    header("Location: ../frontend/napcoin.php?err=invalid");
    exit();
}

$user_id  = intval($_SESSION['user_id']);
$order_id = mysqli_real_escape_string($con, trim($_POST['order_id']));

$order = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT * FROM topup_orders WHERE order_id='$order_id' AND user_id=$user_id LIMIT 1"
));

if (!$order) {
    header("Location: ../frontend/napcoin.php?err=invalid_order");
    exit();
}

if ($order['status'] === 'paid') {
    $params = "success=1&coins=" . intval($order['coins']);
    if (!empty($order['chapter_id'])) {
        $params .= "&chapter_id=" . intval($order['chapter_id']);
    }
    header("Location: ../frontend/napcoin.php?$params");
    exit();
}

if ($order['status'] !== 'pending') {
    header("Location: ../frontend/napcoin.php?err=invalid_order");
    exit();
}

$coins      = intval($order['coins']);
$vnd        = intval($order['vnd_amount']);
$chapter_id = intval($order['chapter_id'] ?? 0);
$order_pk   = intval($order['id']);

mysqli_begin_transaction($con);
try {
    mysqli_query($con, "UPDATE users SET coins = coins + $coins WHERE id=$user_id");

    $note = "Nạp $coins coin ($vnd VND) — đơn $order_id";
    $note = mysqli_real_escape_string($con, $note);
    mysqli_query($con,
        "INSERT INTO coin_transactions (user_id, amount, vnd_amount, type, note)
         VALUES ($user_id, $coins, $vnd, 'topup', '$note')"
    );

    mysqli_query($con,
        "UPDATE topup_orders SET status='paid', paid_at=NOW() WHERE id=$order_pk AND status='pending'"
    );

    if (mysqli_affected_rows($con) === 0) {
        throw new Exception('Order already processed');
    }

    mysqli_commit($con);

    $u = mysqli_fetch_assoc(mysqli_query($con, "SELECT coins FROM users WHERE id=$user_id"));
    $_SESSION['coins'] = intval($u['coins'] ?? 0);

    $params = "success=1&coins=$coins";
    if ($chapter_id > 0) {
        $params .= "&chapter_id=$chapter_id";
    }
    header("Location: ../frontend/napcoin.php?$params");
    exit();
} catch (Exception $e) {
    mysqli_rollback($con);
    header("Location: ../frontend/napcoin.php?err=server");
    exit();
}
