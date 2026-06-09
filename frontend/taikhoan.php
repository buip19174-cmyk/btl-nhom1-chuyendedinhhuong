<?php
session_start();
include '../database/connect.php';
require_once __DIR__ . '/../backend/story_config.php';
require_once __DIR__ . '/includes/paths.php';
require_once __DIR__ . '/../backend/require_auth.php';
require_active_user($_SERVER['REQUEST_URI']);

$username = $_SESSION['username'];
$is_admin = (($_SESSION['role'] ?? '') === 'admin');

$stmt = mysqli_prepare($con, "SELECT username, email, sdt, coins FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) die("Lỗi truy vấn: " . mysqli_error($con));
if (mysqli_num_rows($result) == 0) die("Không tìm thấy thông tin người dùng");

$user = mysqli_fetch_assoc($result);
$coins = $user['coins'] ?? 0;

// Lấy 5 giao dịch gần nhất
$user_id = $_SESSION['user_id'] ?? 0;
$txHistory = null;
if ($user_id) {
    $txHistory = mysqli_query($con,
        "SELECT * FROM coin_transactions WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5"
    );
}

// Lấy số chương đã mua
$bought_count = 0;
if ($user_id) {
    $bc = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COUNT(*) as cnt FROM purchased_chapters WHERE user_id=$user_id"
    ));
    $bought_count = $bc['cnt'] ?? 0;
}

// Avatar chữ cái đầu
$avatar_letter = mb_strtoupper(mb_substr($user['username'], 0, 1));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản — <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0a0a0a;
            --surface:  #111111;
            --card:     #161616;
            --border:   #242424;
            --gold:     #f5c518;
            --gold-dim: #7a6010;
            --green:    #1ed760;
            --red:      #e74c3c;
            --text:     #e8e8e8;
            --dim:      #666;
            --radius:   14px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10,10,10,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 56px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .topbar a {
            color: var(--dim);
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 8px;
            transition: .2s;
        }
        .topbar a:hover { color: var(--text); background: #1a1a1a; }
        .topbar .sep { color: #333; font-size: 12px; }
        .topbar .current { color: var(--text); font-size: 13px; font-weight: 600; }

        /* ── LAYOUT ── */
        .page {
            max-width: 900px;
            margin: 36px auto;
            padding: 0 20px 60px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 20px;
            align-items: start;
        }

        /* ── LEFT PANEL ── */
        .left-panel { display: flex; flex-direction: column; gap: 16px; }

        /* Profile card */
        .profile-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 24px;
            text-align: center;
        }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a3a2a, #0d2b1a);
            border: 3px solid var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: var(--green);
            margin: 0 auto 14px;
            box-shadow: 0 0 20px rgba(30,215,96,.15);
        }
        .profile-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .profile-role {
            font-size: 12px;
            color: var(--dim);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Coin card */
        .coin-card {
            background: linear-gradient(135deg, #1a1500, #2b2000);
            border: 1px solid var(--gold-dim);
            border-radius: var(--radius);
            padding: 20px 24px;
        }
        .coin-card-label {
            font-size: 11px;
            color: #a08020;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .coin-amount {
            font-size: 36px;
            font-weight: 800;
            color: var(--gold);
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1;
        }
        .coin-amount i { font-size: 28px; }
        .coin-vnd {
            font-size: 13px;
            color: #a08020;
            margin-top: 6px;
        }
        .btn-topup {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 16px;
            padding: 10px;
            background: var(--gold);
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: .2s;
        }
        .btn-topup:hover { background: #e6b800; }

        /* Nav links */
        .nav-links { display: flex; flex-direction: column; gap: 4px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--dim);
            font-size: 14px;
            transition: .2s;
            border: 1px solid transparent;
        }
        .nav-link:hover { background: #1a1a1a; color: var(--text); border-color: var(--border); }
        .nav-link i { width: 18px; text-align: center; }
        .nav-link.danger { color: #c0392b; }
        .nav-link.danger:hover { background: #1a0808; border-color: #3a1010; color: var(--red); }

        /* ── RIGHT PANEL ── */
        .right-panel { display: flex; flex-direction: column; gap: 16px; }

        .section-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .section-head {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            font-weight: 600;
            color: var(--dim);
            text-transform: uppercase;
            letter-spacing: .8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Info rows */
        .info-row {
            display: flex;
            align-items: center;
            padding: 15px 22px;
            border-bottom: 1px solid #0f0f0f;
            gap: 14px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dim);
            font-size: 14px;
            flex-shrink: 0;
        }
        .info-label { font-size: 11px; color: var(--dim); margin-bottom: 3px; }
        .info-value { font-size: 14px; color: var(--text); font-weight: 500; }
        .info-value.empty { color: #444; font-style: italic; }

        /* Stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }
        .stat-item {
            padding: 20px;
            text-align: center;
            border-right: 1px solid var(--border);
        }
        .stat-item:last-child { border-right: none; }
        .stat-val {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 6px;
        }
        .stat-lbl { font-size: 11px; color: var(--dim); text-transform: uppercase; letter-spacing: .5px; }

        /* Transaction history */
        .tx-row {
            display: flex;
            align-items: center;
            padding: 13px 22px;
            border-bottom: 1px solid #0f0f0f;
            gap: 14px;
        }
        .tx-row:last-child { border-bottom: none; }
        .tx-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .tx-icon.topup { background: #0d2b1a; color: var(--green); }
        .tx-icon.spend { background: #2b0d0d; color: var(--red); }
        .tx-note { flex: 1; font-size: 13px; color: var(--text); }
        .tx-date { font-size: 11px; color: var(--dim); margin-top: 2px; }
        .tx-amount { font-size: 14px; font-weight: 700; }
        .tx-amount.topup { color: var(--green); }
        .tx-amount.spend { color: var(--red); }

        .empty-state {
            padding: 30px;
            text-align: center;
            color: var(--dim);
            font-size: 13px;
        }
        .empty-state i { font-size: 28px; display: block; margin-bottom: 8px; opacity: .4; }

        /* ── RESPONSIVE ── */
        @media (max-width: 700px) {
            .page { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<nav class="topbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <span class="current">Tài khoản</span>
</nav>

<div class="page">

    <!-- ── LEFT ── -->
    <aside class="left-panel">

        <!-- Profile -->
        <div class="profile-card">
            <div class="avatar"><?php echo $avatar_letter; ?></div>
            <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
            <div class="profile-role">Thành viên</div>
        </div>

        <?php if (!$is_admin): ?>
            <!-- Coin -->
            <div class="coin-card">
                <div class="coin-card-label"><i class="fa-solid fa-coins"></i> Số dư coin</div>
                <div class="coin-amount">
                    <i class="fa-solid fa-coins"></i>
                    <?php echo number_format($coins); ?>
                </div>
                <div class="coin-vnd">≈ <?php echo number_format($coins * 100); ?> VND &nbsp;·&nbsp; 1 coin = 100 VND</div>
                <a href="napcoin.php" class="btn-topup">
                    <i class="fa-solid fa-plus"></i> Nạp coin
                </a>
            </div>
        <?php endif; ?>

        <!-- Nav -->
        <div class="nav-links">
            <?php if (!$is_admin): ?>
                <a href="tusach.php" class="nav-link">
                    <i class="fa-solid fa-book-open"></i> Tủ sách cá nhân
                </a>
                <a href="napcoin.php" class="nav-link">
                    <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử giao dịch
                </a>
            <?php endif; ?>
            <a href="../backend/logout.php" class="nav-link danger">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </div>

    </aside>

    <!-- ── RIGHT ── -->
    <main class="right-panel">

        <!-- Stats -->
        <div class="section-card">
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-val" style="color:var(--gold)"><?php echo number_format($coins); ?></div>
                    <div class="stat-lbl">Coin hiện có</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" style="color:var(--green)"><?php echo $bought_count; ?></div>
                    <div class="stat-lbl">Chương đã mua</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" style="color:#888"><?= (int) FREE_CHAPTERS ?></div>
                    <div class="stat-lbl">Chương miễn phí</div>
                </div>
            </div>
        </div>

        <!-- Thông tin cá nhân -->
        <div class="section-card">
            <div class="section-head">
                <i class="fa-solid fa-circle-user"></i> Thông tin cá nhân
            </div>

            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-user"></i></div>
                <div>
                    <div class="info-label">Tên đăng nhập</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value <?php echo $user['email'] ? '' : 'empty'; ?>">
                        <?php echo $user['email'] ? htmlspecialchars($user['email']) : 'Chưa cập nhật'; ?>
                    </div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                <div>
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value <?php echo $user['sdt'] ? '' : 'empty'; ?>">
                        <?php echo $user['sdt'] ? htmlspecialchars($user['sdt']) : 'Chưa cập nhật'; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$is_admin): ?>
            <!-- Lịch sử giao dịch -->
            <div class="section-card">
                <div class="section-head">
                    <i class="fa-solid fa-receipt"></i> Giao dịch gần đây
                </div>

                <?php if ($txHistory && mysqli_num_rows($txHistory) > 0): ?>
                    <?php while ($tx = mysqli_fetch_assoc($txHistory)): ?>
                    <div class="tx-row">
                        <div class="tx-icon <?php echo $tx['type']; ?>">
                            <i class="fa-solid fa-<?php echo $tx['type'] === 'topup' ? 'plus' : 'minus'; ?>"></i>
                        </div>
                        <div style="flex:1">
                            <div class="tx-note"><?php echo htmlspecialchars($tx['note']); ?></div>
                            <div class="tx-date"><?php echo date('d/m/Y H:i', strtotime($tx['created_at'])); ?></div>
                        </div>
                        <div class="tx-amount <?php echo $tx['type']; ?>">
                            <?php echo $tx['type'] === 'topup' ? '+' : '-'; ?><?php echo $tx['amount']; ?> coin
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <div style="padding:12px 22px;border-top:1px solid var(--border)">
                        <a href="napcoin.php" style="font-size:13px;color:var(--dim);text-decoration:none">
                            Xem tất cả <i class="fa-solid fa-arrow-right" style="font-size:11px"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-receipt"></i>
                        Chưa có giao dịch nào
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
