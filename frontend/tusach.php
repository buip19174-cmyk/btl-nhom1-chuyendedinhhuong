<?php
session_start();
include '../database/connect.php';
require_once __DIR__ . '/includes/paths.php';
require_once __DIR__ . '/../backend/require_auth.php';
require_active_user($_SERVER['REQUEST_URI']);

$is_admin = (($_SESSION['role'] ?? '') === 'admin');
if ($is_admin) {
    header("Location: home.php");
    exit();
}

$username = $_SESSION['username'];

// Lấy user_id
$u = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id, coins FROM users WHERE username = '" . mysqli_real_escape_string($con, $username) . "' LIMIT 1"
));
if (!$u) { header("Location: home.php"); exit(); }
$user_id = $u['id'];
$coins   = $u['coins'] ?? 0;

// ── Xử lý xoá truyện khỏi tủ ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_story'])) {
    $sid = intval($_POST['remove_story']);
    mysqli_query($con, "DELETE FROM user_stories WHERE user_id=$user_id AND story_id=$sid");
    header("Location: tusach.php?tab=saved&removed=1");
    exit();
}

$tab = $_GET['tab'] ?? 'saved'; // saved | bought

// ── Truyện đã lưu ──
$saved_stories = mysqli_query($con, "
    SELECT s.id, s.title, s.cover, s.description,
           us.created_at AS saved_at,
           (SELECT COUNT(*) FROM chapters WHERE story_id = s.id) AS total_chapters
    FROM user_stories us
    JOIN stories s ON us.story_id = s.id
    WHERE us.user_id = $user_id
    ORDER BY us.created_at DESC
");

// ── Chương đã mua ──
$bought_chapters = mysqli_query($con, "
    SELECT c.id AS chapter_id, c.chapter_number, c.title AS chapter_title,
           s.id AS story_id, s.title AS story_title, s.cover,
           pc.purchased_at, pc.coins_spent
    FROM purchased_chapters pc
    JOIN chapters c ON pc.chapter_id = c.id
    JOIN stories s ON c.story_id = s.id
    WHERE pc.user_id = $user_id
    ORDER BY pc.purchased_at DESC
");

$saved_count  = mysqli_num_rows($saved_stories);
$bought_count = mysqli_num_rows($bought_chapters);
$avatar_letter = mb_strtoupper(mb_substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tủ sách — <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #0a0a0a;
            --surface: #111;
            --card:    #161616;
            --border:  #242424;
            --gold:    #f5c518;
            --green:   #1ed760;
            --red:     #e74c3c;
            --text:    #e8e8e8;
            --dim:     #666;
            --radius:  12px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10,10,10,.9);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            height: 56px;
            padding: 0 28px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .topbar a {
            color: var(--dim); text-decoration: none; font-size: 13px;
            display: flex; align-items: center; gap: 6px;
            padding: 6px 10px; border-radius: 8px; transition: .2s;
        }
        .topbar a:hover { color: var(--text); background: #1a1a1a; }
        .topbar .sep { color: #333; }
        .topbar .current { color: var(--text); font-size: 13px; font-weight: 600; }
        .topbar .coin-pill {
            margin-left: auto;
            background: #1a1500;
            border: 1px solid #3a3000;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 13px;
            color: var(--gold);
            display: flex; align-items: center; gap: 6px;
            text-decoration: none;
        }
        .topbar .coin-pill:hover { border-color: var(--gold); background: #2b2000; }

        /* ── PAGE ── */
        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px 60px;
        }

        /* ── HERO ── */
        .hero {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
        }
        .hero-avatar {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a3a2a, #0d2b1a);
            border: 2px solid var(--green);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 700; color: var(--green);
            flex-shrink: 0;
        }
        .hero-info h1 { font-size: 22px; font-weight: 700; }
        .hero-info p  { font-size: 13px; color: var(--dim); margin-top: 4px; }
        .hero-stats {
            margin-left: auto;
            display: flex; gap: 24px;
        }
        .hs-item { text-align: center; }
        .hs-val  { font-size: 22px; font-weight: 800; line-height: 1; }
        .hs-lbl  { font-size: 11px; color: var(--dim); text-transform: uppercase; margin-top: 3px; }

        /* ── TABS ── */
        .tabs {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 24px;
        }
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--dim);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            display: flex; align-items: center; gap: 8px;
            text-decoration: none;
            margin-bottom: -1px;
        }
        .tab-btn:hover { color: var(--text); }
        .tab-btn.active { color: var(--green); border-bottom-color: var(--green); }
        .tab-count {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 1px 8px;
            font-size: 11px;
            color: var(--dim);
        }
        .tab-btn.active .tab-count { background: #0d2b1a; color: var(--green); }

        /* ── ALERT ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #0d2b1a; border: 1px solid var(--green); color: var(--green); }

        /* ── GRID TRUYỆN ── */
        .story-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 20px;
        }

        .story-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: .2s;
            position: relative;
        }
        .story-card:hover { border-color: #333; transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.4); }

        .story-cover {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            display: block;
        }
        .story-cover-placeholder {
            width: 100%; aspect-ratio: 2/3;
            background: linear-gradient(135deg, #1a1a1a, #222);
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 40px;
        }

        .story-body { padding: 12px; }
        .story-title {
            font-size: 13px; font-weight: 600; color: var(--text);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 6px;
            text-decoration: none;
        }
        .story-title:hover { color: var(--green); }
        .story-meta { font-size: 11px; color: var(--dim); }

        .story-actions {
            display: flex; gap: 6px;
            padding: 0 12px 12px;
        }
        .btn-read {
            flex: 1;
            padding: 7px;
            background: var(--green);
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: .2s;
        }
        .btn-read:hover { background: #17b84d; }
        .btn-remove {
            padding: 7px 10px;
            background: #1a0808;
            color: var(--red);
            border: 1px solid #3a1010;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: .2s;
        }
        .btn-remove:hover { background: #2b0d0d; }

        /* ── BOUGHT CHAPTERS LIST ── */
        .bought-group { margin-bottom: 28px; }
        .bought-group-header {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 12px;
        }
        .bought-cover {
            width: 44px; height: 60px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .bought-story-title {
            font-size: 15px; font-weight: 700;
            text-decoration: none; color: var(--text);
        }
        .bought-story-title:hover { color: var(--green); }
        .bought-story-sub { font-size: 12px; color: var(--dim); margin-top: 3px; }

        .chapter-list { display: flex; flex-direction: column; gap: 6px; }
        .chapter-item {
            display: flex; align-items: center;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            gap: 14px;
            text-decoration: none;
            color: var(--text);
            transition: .2s;
        }
        .chapter-item:hover { border-color: var(--gold); background: #1a1500; }
        .chapter-num {
            width: 32px; height: 32px;
            background: #2b1f00;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: var(--gold);
            flex-shrink: 0;
        }
        .chapter-info { flex: 1; }
        .chapter-name { font-size: 13px; font-weight: 600; }
        .chapter-date { font-size: 11px; color: var(--dim); margin-top: 2px; }
        .chapter-cost {
            font-size: 11px; color: var(--gold);
            display: flex; align-items: center; gap: 4px;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--dim);
        }
        .empty-state i { font-size: 52px; display: block; margin-bottom: 16px; opacity: .25; }
        .empty-state h3 { font-size: 18px; color: #444; margin-bottom: 8px; }
        .empty-state p  { font-size: 14px; margin-bottom: 24px; }
        .btn-explore {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 24px;
            background: var(--green);
            color: #000;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: .2s;
        }
        .btn-explore:hover { background: #17b84d; }

        @media (max-width: 600px) {
            .hero-stats { display: none; }
            .story-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<nav class="topbar">
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <a href="taikhoan.php">Tài khoản</a>
    <span class="sep">/</span>
    <span class="current">Tủ sách</span>
    <a href="napcoin.php" class="coin-pill">
        <i class="fa-solid fa-coins"></i> <?php echo number_format($coins); ?> coin
    </a>
</nav>

<div class="page">

    <!-- HERO -->
    <div class="hero">
        <div class="hero-avatar"><?php echo $avatar_letter; ?></div>
        <div class="hero-info">
            <h1>Tủ sách của <?php echo htmlspecialchars($username); ?></h1>
            <p>Quản lý truyện đã lưu và chương đã mua</p>
        </div>
        <div class="hero-stats">
            <div class="hs-item">
                <div class="hs-val" style="color:var(--green)"><?php echo $saved_count; ?></div>
                <div class="hs-lbl">Đã lưu</div>
            </div>
            <div class="hs-item">
                <div class="hs-val" style="color:var(--gold)"><?php echo $bought_count; ?></div>
                <div class="hs-lbl">Đã mua</div>
            </div>
            <div class="hs-item">
                <div class="hs-val" style="color:#888"><?php echo number_format($coins); ?></div>
                <div class="hs-lbl">Coin</div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['removed'])): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> Đã xoá truyện khỏi tủ sách.
    </div>
    <?php endif; ?>

    <!-- TABS -->
    <div class="tabs">
        <a href="tusach.php?tab=saved"
           class="tab-btn <?php echo $tab === 'saved' ? 'active' : ''; ?>">
            <i class="fa-solid fa-heart"></i> Truyện đã lưu
            <span class="tab-count"><?php echo $saved_count; ?></span>
        </a>
        <a href="tusach.php?tab=bought"
           class="tab-btn <?php echo $tab === 'bought' ? 'active' : ''; ?>">
            <i class="fa-solid fa-coins"></i> Chương đã mua
            <span class="tab-count"><?php echo $bought_count; ?></span>
        </a>
    </div>

    <!-- ══ TAB: TRUYỆN ĐÃ LƯU ══ -->
    <?php if ($tab === 'saved'): ?>

        <?php if ($saved_count > 0): ?>
        <div class="story-grid">
            <?php while ($s = mysqli_fetch_assoc($saved_stories)): ?>
            <div class="story-card">
                <?php if ($s['cover']): ?>
                    <img src="../code/images/<?php echo htmlspecialchars($s['cover']); ?>"
                         class="story-cover"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="story-cover-placeholder" style="display:none">
                        <i class="fa-solid fa-book"></i>
                    </div>
                <?php else: ?>
                    <div class="story-cover-placeholder">
                        <i class="fa-solid fa-book"></i>
                    </div>
                <?php endif; ?>

                <div class="story-body">
                    <a href="../backend/read_story.php?story_id=<?php echo $s['id']; ?>"
                       class="story-title">
                        <?php echo htmlspecialchars($s['title']); ?>
                    </a>
                    <div class="story-meta">
                        <i class="fa-solid fa-list" style="font-size:10px"></i>
                        <?php echo $s['total_chapters']; ?> chương
                    </div>
                </div>

                <div class="story-actions">
                    <a href="../backend/read_story.php?story_id=<?php echo $s['id']; ?>"
                       class="btn-read">
                        <i class="fa-solid fa-book-open"></i> Đọc
                    </a>
                    <form method="POST" action="tusach.php?tab=saved"
                          onsubmit="return confirm('Xoá truyện này khỏi tủ sách?')">
                        <input type="hidden" name="remove_story" value="<?php echo $s['id']; ?>">
                        <button type="submit" class="btn-remove" title="Xoá khỏi tủ">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-heart"></i>
            <h3>Tủ sách trống</h3>
            <p>Bạn chưa lưu truyện nào. Hãy khám phá và nhấn ❤️ để lưu.</p>
            <a href="home.php" class="btn-explore">
                <i class="fa-solid fa-compass"></i> Khám phá truyện
            </a>
        </div>
        <?php endif; ?>

    <?php endif; ?>

    <!-- ══ TAB: CHƯƠNG ĐÃ MUA ══ -->
    <?php if ($tab === 'bought'): ?>

        <?php if ($bought_count > 0):
            // Nhóm theo truyện
            $groups = [];
            mysqli_data_seek($bought_chapters, 0);
            while ($bc = mysqli_fetch_assoc($bought_chapters)) {
                $sid = $bc['story_id'];
                if (!isset($groups[$sid])) {
                    $groups[$sid] = [
                        'story_title' => $bc['story_title'],
                        'cover'       => $bc['cover'],
                        'story_id'    => $sid,
                        'chapters'    => []
                    ];
                }
                $groups[$sid]['chapters'][] = $bc;
            }
        ?>

        <?php foreach ($groups as $g): ?>
        <div class="bought-group">
            <div class="bought-group-header">
                <?php if ($g['cover']): ?>
                    <img src="../code/images/<?php echo htmlspecialchars($g['cover']); ?>"
                         class="bought-cover"
                         onerror="this.style.display='none'">
                <?php endif; ?>
                <div>
                    <a href="../backend/read_story.php?story_id=<?php echo $g['story_id']; ?>"
                       class="bought-story-title">
                        <?php echo htmlspecialchars($g['story_title']); ?>
                    </a>
                    <div class="bought-story-sub">
                        <?php echo count($g['chapters']); ?> chương đã mua
                    </div>
                </div>
            </div>

            <div class="chapter-list">
                <?php foreach ($g['chapters'] as $ch): ?>
                <a href="../backend/read_chapter.php?chapter_id=<?php echo $ch['chapter_id']; ?>"
                   class="chapter-item">
                    <div class="chapter-num"><?php echo $ch['chapter_number']; ?></div>
                    <div class="chapter-info">
                        <div class="chapter-name">
                            Chương <?php echo $ch['chapter_number']; ?>:
                            <?php echo htmlspecialchars($ch['chapter_title']); ?>
                        </div>
                        <div class="chapter-date">
                            Mua lúc <?php echo date('d/m/Y H:i', strtotime($ch['purchased_at'])); ?>
                        </div>
                    </div>
                    <div class="chapter-cost">
                        <i class="fa-solid fa-coins"></i> <?php echo $ch['coins_spent']; ?> coin
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:var(--dim);font-size:11px"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-coins"></i>
            <h3>Chưa mua chương nào</h3>
            <p>3 chương đầu mỗi truyện miễn phí. Từ chương 4 cần 3 coin để mở khoá.</p>
            <a href="home.php" class="btn-explore">
                <i class="fa-solid fa-book-open"></i> Bắt đầu đọc
            </a>
        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>
</body>
</html>
