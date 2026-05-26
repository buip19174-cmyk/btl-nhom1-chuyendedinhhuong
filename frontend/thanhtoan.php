<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../database/connect.php';
include '../backend/payment_config.php';
require_once __DIR__ . '/includes/paths.php';
require_once __DIR__ . '/../backend/require_auth.php';
require_active_user($_SERVER['REQUEST_URI']);

$user_id  = intval($_SESSION['user_id']);
$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';

if ($order_id === '') {
    header("Location: napcoin.php?err=invalid");
    exit();
}

$order_id_esc = mysqli_real_escape_string($con, $order_id);
$order = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT * FROM topup_orders WHERE order_id='$order_id_esc' AND user_id=$user_id LIMIT 1"
));

if (!$order) {
    header("Location: napcoin.php?err=invalid_order");
    exit();
}

if ($order['status'] === 'paid') {
    $params = "success=1&coins=" . intval($order['coins']);
    if (!empty($order['chapter_id'])) {
        $params .= "&chapter_id=" . intval($order['chapter_id']);
    }
    header("Location: napcoin.php?$params");
    exit();
}

if ($order['status'] !== 'pending') {
    header("Location: napcoin.php?err=invalid_order");
    exit();
}

$coins      = intval($order['coins']);
$vnd        = intval($order['vnd_amount']);
$add_info   = 'TOPUP_' . $order_id;
$qr_url     = payment_vietqr_image_url($vnd, $add_info);
$chapter_id = intval($order['chapter_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán QR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #0b0b0b;
            --card: #141414;
            --gold: #f5c518;
            --green: #1ed760;
            --text: #e0e0e0;
            --dim: #888;
            --border: #222;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        .topbar {
            background: #000;
            padding: 14px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid var(--border);
        }
        .topbar a { color: var(--dim); text-decoration: none; font-size: 14px; }
        .topbar a:hover { color: var(--text); }
        .topbar .sep { color: var(--border); }

        .wrapper {
            max-width: 520px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        .pay-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        .pay-head {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }
        .pay-head h1 {
            font-size: 18px;
            margin-bottom: 6px;
        }
        .pay-head p {
            font-size: 13px;
            color: var(--dim);
        }

        .pay-amount {
            padding: 24px;
            text-align: center;
            background: linear-gradient(135deg, #1a1500, #2b2000);
            border-bottom: 1px solid var(--border);
        }
        .pay-amount .label {
            font-size: 12px;
            color: #a08020;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .pay-amount .vnd {
            font-size: 36px;
            font-weight: 800;
            color: var(--gold);
            margin: 8px 0;
        }
        .pay-amount .coins {
            font-size: 14px;
            color: var(--dim);
        }

        .qr-wrap {
            padding: 28px 24px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }
        .qr-wrap img {
            width: 260px;
            height: 260px;
            border-radius: 12px;
            background: #fff;
            padding: 8px;
        }
        .qr-hint {
            margin-top: 14px;
            font-size: 13px;
            color: var(--dim);
        }

        .bank-info {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            font-size: 14px;
            border-bottom: 1px solid #181818;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row .lbl { color: var(--dim); flex-shrink: 0; }
        .info-row .val {
            text-align: right;
            font-weight: 600;
            word-break: break-all;
        }
        .info-row .val.highlight { color: var(--green); }

        .pay-actions {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-confirm {
            width: 100%;
            padding: 14px;
            background: var(--green);
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-confirm:hover { background: #17c653; }

        .btn-cancel {
            display: block;
            text-align: center;
            padding: 12px;
            color: var(--dim);
            text-decoration: none;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .btn-cancel:hover { color: var(--text); border-color: #444; }

        .demo-note {
            margin-top: 20px;
            padding: 14px 16px;
            background: #1a1a0a;
            border: 1px solid #3a3a10;
            border-radius: 8px;
            font-size: 12px;
            color: #aaa;
            line-height: 1.6;
        }
        .demo-note i { color: var(--gold); margin-right: 6px; }
    </style>
</head>
<body>

<nav class="topbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <a href="napcoin.php">Nạp Coin</a>
    <span class="sep">/</span>
    <span style="color:var(--gold)">Thanh toán QR</span>
</nav>

<div class="wrapper">
    <div class="pay-card">
        <div class="pay-head">
            <h1><i class="fa-solid fa-qrcode"></i> Quét mã QR để thanh toán</h1>
            <p>Mã đơn: <strong><?= htmlspecialchars($order_id) ?></strong></p>
        </div>

        <div class="pay-amount">
            <div class="label">Số tiền cần thanh toán</div>
            <div class="vnd"><?= number_format($vnd) ?> VND</div>
            <div class="coins"><i class="fa-solid fa-coins"></i> Nhận <?= number_format($coins) ?> coin</div>
        </div>

        <div class="qr-wrap">
            <img src="<?= htmlspecialchars($qr_url) ?>" alt="Mã QR VietQR" width="260" height="260">
            <div class="qr-hint">Mở app ngân hàng → Quét QR → Xác nhận chuyển khoản</div>
        </div>

        <div class="bank-info">
            <div class="info-row">
                <span class="lbl">Ngân hàng</span>
                <span class="val"><?= htmlspecialchars(PAYMENT_BANK) ?></span>
            </div>
            <div class="info-row">
                <span class="lbl">Số tài khoản</span>
                <span class="val"><?= htmlspecialchars(PAYMENT_ACCOUNT) ?></span>
            </div>
            <div class="info-row">
                <span class="lbl">Chủ tài khoản</span>
                <span class="val"><?= htmlspecialchars(PAYMENT_ACCOUNT_NAME) ?></span>
            </div>
            <div class="info-row">
                <span class="lbl">Nội dung CK</span>
                <span class="val highlight"><?= htmlspecialchars($add_info) ?></span>
            </div>
        </div>

        <div class="pay-actions">
            <form method="POST" action="../backend/topup_confirm_paid.php">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
                <button type="submit" class="btn-confirm">
                    <i class="fa-solid fa-circle-check"></i> Tôi đã thanh toán — Nhận coin
                </button>
            </form>
            <a href="napcoin.php<?= $chapter_id > 0 ? '?chapter_id=' . $chapter_id : '' ?>" class="btn-cancel">
                Hủy và quay lại
            </a>
        </div>
    </div>

    <div class="demo-note">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Demo đồ án:</strong> Đây là luồng thanh toán mô phỏng. QR lấy từ VietQR (img.vietqr.io).
        Sau khi quét/chuyển khoản (hoặc bỏ qua bước đó), bấm nút xác nhận để hệ thống cộng coin vào tài khoản.
    </div>
</div>

</body>
</html>
