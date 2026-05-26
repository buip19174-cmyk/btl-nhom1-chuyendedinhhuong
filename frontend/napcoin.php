<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap_form.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$user_id    = intval($_SESSION['user_id']);
$chapter_id = isset($_GET['chapter_id']) ? intval($_GET['chapter_id']) : 0;
$need       = isset($_GET['need'])       ? intval($_GET['need'])       : 0;

// Lấy số coin hiện tại
$u = mysqli_fetch_assoc(mysqli_query($con, "SELECT coins FROM users WHERE id=$user_id"));
$current_coins = $u['coins'] ?? 0;

// Lịch sử giao dịch gần nhất
$history = mysqli_query($con,
    "SELECT * FROM coin_transactions WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 10"
);

$success = isset($_GET['success']) && $_GET['success'] == 1;
$err     = $_GET['err'] ?? '';
$added   = isset($_GET['coins']) ? intval($_GET['coins']) : 0;

$packs = [
    ['coins' => 10,  'vnd' => 1000,   'label' => 'Starter'],
    ['coins' => 30,  'vnd' => 3000,   'label' => 'Phổ biến'],
    ['coins' => 50,  'vnd' => 5000,   'label' => 'Tiết kiệm'],
    ['coins' => 100, 'vnd' => 10000,  'label' => 'Giá trị'],
    ['coins' => 200, 'vnd' => 20000,  'label' => 'Super'],
    ['coins' => 500, 'vnd' => 50000,  'label' => 'VIP'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nạp Coin</title>
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
            --red: #e74c3c;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        /* NAV */
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

        /* WRAPPER */
        .wrapper {
            max-width: 860px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* COIN BALANCE */
        .balance-card {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border: 1px solid #2a2a4a;
            border-radius: 12px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }
        .balance-left h2 { font-size: 14px; color: var(--dim); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .balance-amount { font-size: 42px; font-weight: 700; color: var(--gold); display: flex; align-items: center; gap: 10px; }
        .balance-amount i { font-size: 32px; }
        .balance-sub { font-size: 13px; color: var(--dim); margin-top: 6px; }

        /* ALERT */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #0d2b1a; border: 1px solid #1ed760; color: #1ed760; }
        .alert-warning { background: #2b1a0d; border: 1px solid #f39c12; color: #f39c12; }
        .alert-error   { background: #2b0d0d; border: 1px solid var(--red); color: var(--red); }

        /* SECTION TITLE */
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        /* PACKS GRID */
        .packs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 40px;
        }

        .pack-card {
            background: var(--card);
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .pack-card:hover { border-color: var(--gold); transform: translateY(-2px); }
        .pack-card.popular { border-color: var(--green); }
        .pack-badge {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--green);
            color: #000;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .pack-coins { font-size: 28px; font-weight: 700; color: var(--gold); }
        .pack-coins i { font-size: 22px; margin-right: 4px; }
        .pack-label { font-size: 11px; color: var(--dim); text-transform: uppercase; margin: 4px 0 10px; }
        .pack-vnd { font-size: 15px; color: var(--text); font-weight: 600; }
        .pack-rate { font-size: 11px; color: var(--dim); margin-top: 4px; }

        .btn-buy {
            display: block;
            width: 100%;
            margin-top: 14px;
            padding: 9px;
            background: var(--gold);
            color: #000;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-buy:hover { background: #e6b800; }

        /* HISTORY */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .history-table th {
            text-align: left;
            padding: 10px 12px;
            color: var(--dim);
            border-bottom: 1px solid var(--border);
            font-weight: 500;
        }
        .history-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #181818;
            color: var(--text);
        }
        .badge-topup { color: var(--green); font-weight: 600; }
        .badge-spend { color: var(--red); font-weight: 600; }

        /* BACK LINK */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--dim);
            text-decoration: none;
            font-size: 13px;
            margin-top: 30px;
        }
        .back-link:hover { color: var(--text); }

        @media (max-width: 600px) {
            .packs-grid { grid-template-columns: repeat(2, 1fr); }
            .balance-card { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>

<nav class="topbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <a href="taikhoan.php">Tài khoản</a>
    <span class="sep">/</span>
    <span style="color:var(--gold)">Nạp Coin</span>
</nav>

<div class="wrapper">

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        Nạp thành công <strong><?= $added ?> coin</strong>!
        <?php if ($chapter_id > 0): ?>
            <a href="../backend/buy_chapter.php" style="color:inherit;margin-left:8px;text-decoration:underline"
               onclick="document.getElementById('buyForm<?= $chapter_id ?>').submit(); return false;">
               → Mua chương ngay
            </a>
            <form id="buyForm<?= $chapter_id ?>" method="POST" action="../backend/buy_chapter.php" style="display:none">
                <input type="hidden" name="chapter_id" value="<?= $chapter_id ?>">
            </form>
        <?php endif; ?>
    </div>
    <?php elseif ($need > 0): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Bạn cần <strong><?= $need ?> coin</strong> để mở chương này. Hãy nạp thêm coin bên dưới.
    </div>
    <?php elseif ($err): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-xmark"></i>
        Có lỗi xảy ra. Vui lòng thử lại.
    </div>
    <?php endif; ?>

    <!-- BALANCE -->
    <div class="balance-card">
        <div class="balance-left">
            <h2>Số dư hiện tại</h2>
            <div class="balance-amount">
                <i class="fa-solid fa-coins"></i>
                <?= number_format($current_coins) ?>
            </div>
            <div class="balance-sub">≈ <?= number_format($current_coins * 10) ?> VND &nbsp;|&nbsp; 1 coin = 10 VND</div>
        </div>
        <div style="text-align:right;color:var(--dim);font-size:13px;">
            <div>📖 3 chương đầu <strong style="color:var(--green)">MIỄN PHÍ</strong></div>
            <div style="margin-top:6px;">💰 Từ chương 4: <strong style="color:var(--gold)">3 coin / chương</strong></div>
        </div>
    </div>

    <!-- PACKS -->
    <div class="section-title"><i class="fa-solid fa-bolt" style="color:var(--gold)"></i> Chọn gói nạp</div>
    <div class="packs-grid">
        <?php foreach ($packs as $i => $pack): ?>
        <div class="pack-card <?= $i === 1 ? 'popular' : '' ?>">
            <?php if ($i === 1): ?><div class="pack-badge">Phổ biến</div><?php endif; ?>
            <div class="pack-coins"><i class="fa-solid fa-coins"></i><?= $pack['coins'] ?></div>
            <div class="pack-label"><?= $pack['label'] ?></div>
            <div class="pack-vnd"><?= number_format($pack['vnd']) ?> VND</div>
            <div class="pack-rate">= <?= $pack['coins'] ?> chương trả phí</div>
            <form method="POST" action="../backend/topup_coin.php">
                <input type="hidden" name="coins" value="<?= $pack['coins'] ?>">
                <?php if ($chapter_id > 0): ?>
                <input type="hidden" name="chapter_id" value="<?= $chapter_id ?>">
                <?php endif; ?>
                <button type="submit" class="btn-buy">Nạp ngay</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- HISTORY -->
    <?php if (mysqli_num_rows($history) > 0): ?>
    <div class="section-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--dim)"></i> Lịch sử giao dịch</div>
    <table class="history-table">
        <thead>
            <tr>
                <th>Thời gian</th>
                <th>Loại</th>
                <th>Coin</th>
                <th>VND</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($tx = mysqli_fetch_assoc($history)): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                <td>
                    <?php if ($tx['type'] === 'topup'): ?>
                        <span class="badge-topup"><i class="fa-solid fa-plus"></i> Nạp</span>
                    <?php else: ?>
                        <span class="badge-spend"><i class="fa-solid fa-minus"></i> Chi</span>
                    <?php endif; ?>
                </td>
                <td><?= $tx['type'] === 'topup' ? '+' : '-' ?><?= $tx['amount'] ?></td>
                <td><?= number_format($tx['vnd_amount']) ?></td>
                <td style="color:var(--dim)"><?= htmlspecialchars($tx['note']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <a href="taikhoan.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại tài khoản</a>
</div>

</body>
</html>
