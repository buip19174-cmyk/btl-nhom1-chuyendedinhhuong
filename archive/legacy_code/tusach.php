<?php
session_start();
include 'db_connect.php';

/* --- LOGIC PHP GIỮ NGUYÊN --- */
if (!isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

$username = $_SESSION['username'];

$sqlUser = "SELECT id FROM users WHERE username = ?";
$stmtUser = mysqli_prepare($con, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $user_id);
mysqli_stmt_fetch($stmtUser);
mysqli_stmt_close($stmtUser);

if (!$user_id) {
    die("User không tồn tại");
}

if (isset($_POST['delete_story_id'])) {
    $story_id = (int)$_POST['delete_story_id'];
    $sqlDelete = "DELETE FROM user_stories WHERE user_id = ? AND story_id = ?";
    $stmtDelete = mysqli_prepare($con, $sqlDelete);
    mysqli_stmt_bind_param($stmtDelete, "ii", $user_id, $story_id);
    mysqli_stmt_execute($stmtDelete);
    mysqli_stmt_close($stmtDelete);
    $message = "✅ Đã xóa truyện khỏi tủ sách";
}

$sql = "
    SELECT s.id, s.title, s.description, s.cover
    FROM stories s
    JOIN user_stories us ON s.id = us.story_id
    WHERE us.user_id = ?
    ORDER BY us.created_at DESC
";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tủ sách cá nhân </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #0f0f0f;
            --bg-sidebar: #161616;
            --bg-card: #1e1e1e;
            --primary-green: #1ed760;
            --primary-hover: #1db954;
            --text-main: #ffffff;
            --text-muted: #b3b3b3;
            --border-color: #2a2a2a;
            --danger: #ff4d4d;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR TỐI ƯU */
        .sidebar {
            width: 260px;
            background-color: var(--bg-sidebar);
            padding: 40px 15px;
            border-right: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            height: 100vh;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .user-profile {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--primary-green);
            border: 2px solid var(--border-color);
        }

        .user-username {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
            word-break: break-all;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu-item {
            padding: 12px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .menu-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        .menu-item:hover {
            background: #252525;
            color: var(--text-main);
        }

        .menu-item.active {
            background: rgba(30, 215, 96, 0.1);
            color: var(--primary-green);
        }

        /* MAIN CONTENT TỐI ƯU */
        .main-content {
            flex: 1;
            padding: 40px 50px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .tabs {
            display: flex;
            gap: 30px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 25px;
        }

        .tab {
            padding-bottom: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            font-size: 1rem;
            transition: color 0.2s;
        }

        .tab:hover { color: var(--text-main); }

        .tab.active {
            color: var(--primary-green);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-green);
            border-radius: 10px 10px 0 0;
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 30px;
            background: #252525;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            cursor: pointer;
            font-weight: 500;
            transition: 0.2s;
        }

        .filter-btn.active {
            background: var(--text-main);
            color: #000;
            border-color: var(--text-main);
        }

        /* GRID SÁCH TỐI ƯU */
        .bookshelf {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 30px;
        }

        .book-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 10px;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            border: 1px solid transparent;
        }

        .book-card:hover {
            transform: translateY(-8px);
            background: #282828;
            border-color: #333;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }

        .book-cover-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 2/3;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .book-card:hover .book-cover {
            transform: scale(1.05);
        }

        .book-title {
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.4;
            color: var(--text-main);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0 5px;
        }

        /* NÚT XÓA TINH TẾ */
        .delete-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.2s ease;
        }

        .book-card:hover .delete-overlay {
            opacity: 1;
            transform: scale(1);
        }

        .btn-del {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            color: var(--text-main);
            border: 1px solid rgba(255,255,255,0.1);
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-del:hover {
            background: var(--danger);
            color: white;
            transform: rotate(90deg);
        }

        .empty-msg {
            grid-column: 1 / -1;
            text-align: center;
            padding: 100px 0;
            color: var(--text-muted);
        }

        .empty-msg i {
            font-size: 60px;
            margin-bottom: 20px;
            display: block;
            opacity: 0.2;
        }

        .alert-msg {
            background: rgba(30, 215, 96, 0.15);
            color: var(--primary-green);
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid rgba(30, 215, 96, 0.3);
            display: inline-block;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="user-profile">
            <div class="avatar-circle">
                <i class="fa-regular fa-user"></i>
            </div>
            <div class="user-username">
                <?php echo htmlspecialchars($username); ?>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="#" class="menu-item"><i class="fa-solid fa-circle-user"></i> Tài khoản</a>
            <a href="#" class="menu-item active"><i class="fa-solid fa-book-bookmark"></i> Tủ sách của tôi</a>
            <a href="home.php" class="menu-item"><i class="fa-solid fa-house-chimney"></i> Trang chủ</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div class="tabs">
                <a href="#" class="tab active">Yêu Thích</a>
            </div>

            <div class="filters">
                <button class="filter-btn active">Tất cả truyện</button>
            </div>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="bookshelf">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="book-card">
                        <div class="delete-overlay">
                            <form method="POST" onsubmit="return confirm('Bạn muốn bỏ truyện này khỏi tủ sách?');">
                                <input type="hidden" name="delete_story_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-del" title="Xóa khỏi tủ sách">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>

                        <a href="read_story.php?story_id=<?php echo $row['id']; ?>" style="text-decoration:none;">
                            <div class="book-cover-wrapper">
                                <img src="<?php echo htmlspecialchars($row['cover']); ?>" alt="Bìa truyện" class="book-cover">
                            </div>
                            <div class="book-title" title="<?php echo htmlspecialchars($row['title']); ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-msg">
                    <i class="fa-solid fa-ghost"></i>
                    <p>Tủ sách của bạn hiện đang trống trải...</p>
                    <a href="home.php" style="color: var(--primary-green); text-decoration: none; font-weight: bold;">Khám phá ngay!</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>