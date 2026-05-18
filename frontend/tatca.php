<?php
session_start();
include '../database/connect.php';

// Phân trang: 3 hàng x 6 = 18 truyện mỗi trang
$per_page = 18;
$page     = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset   = ($page - 1) * $per_page;

// Tổng số truyện
$total_q = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM stories"));
$total   = $total_q['cnt'] ?? 0;
$total_pages = max(1, ceil($total / $per_page));

// Lấy truyện trang hiện tại
$result = mysqli_query($con, "SELECT id, title, cover FROM stories ORDER BY id DESC LIMIT $per_page OFFSET $offset");
$books  = [];
while ($r = mysqli_fetch_assoc($result)) $books[] = $r;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tất cả sách — Trang <?= $page ?> — KEWE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg: #0a0a0a; --surface: #111; --card: #161616; --border: #222;
    --green: #00d084; --gold: #f5c518; --text: #e0e0e0; --dim: #666; --radius: 12px;
}
body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', Roboto, sans-serif; min-height: 100vh; }

/* Topbar */
.topbar {
    position: sticky; top: 0; z-index: 100;
    background: rgba(10,10,10,.92); backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
    height: 56px; padding: 0 28px;
    display: flex; align-items: center; gap: 10px;
}
.topbar .logo { font-size: 20px; font-weight: 900; color: var(--green); text-decoration: none; margin-right: 12px; letter-spacing: -1px; }
.topbar a { color: var(--dim); text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 6px; transition: .15s; }
.topbar a:hover { color: var(--text); background: #1a1a1a; }
.topbar .sep { color: #333; }
.topbar .current { color: var(--text); font-size: 13px; font-weight: 600; }

/* Page */
.page-wrap { max-width: 1200px; margin: 0 auto; padding: 32px 28px 60px; }

/* Header */
.page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border);
}
.page-header h1 { font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
.page-header h1 i { color: var(--green); font-size: 18px; }
.page-header .info { font-size: 13px; color: var(--dim); }

/* Grid 6 cột */
.book-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}
.book-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: var(--radius); overflow: hidden; transition: .2s;
}
.book-card:hover { border-color: #333; transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.5); }
.book-card img { width: 100%; aspect-ratio: 2/3; object-fit: cover; display: block; transition: .3s; }
.book-card:hover img { filter: brightness(1.1); }
.book-card-body { padding: 10px 10px 6px; }
.book-card-title {
    font-size: 12px; font-weight: 600; color: var(--text); line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    text-decoration: none; display: block; margin-bottom: 6px;
}
.book-card-title:hover { color: var(--green); }
.book-card-footer { padding: 0 10px 10px; display: flex; gap: 5px; }
.btn-read {
    flex: 1; padding: 6px; background: var(--green); color: #000;
    border: none; border-radius: 6px; font-size: 11px; font-weight: 700;
    text-align: center; text-decoration: none; cursor: pointer; transition: .2s;
    display: flex; align-items: center; justify-content: center; gap: 4px;
}
.btn-read:hover { background: #00b872; color: #000; }
.btn-save {
    padding: 6px 9px; background: #1a0a0a; color: #e74c3c;
    border: 1px solid #2a1010; border-radius: 6px; font-size: 11px; cursor: pointer; transition: .2s;
}
.btn-save:hover { background: #2b0d0d; }

/* Pagination */
.pagination {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    flex-wrap: wrap;
}
.pagination a, .pagination span {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 38px; height: 38px; padding: 0 12px;
    border-radius: 8px; font-size: 14px; font-weight: 600;
    text-decoration: none; transition: .15s;
}
.pagination a {
    background: var(--card); border: 1px solid var(--border); color: var(--text);
}
.pagination a:hover { border-color: var(--green); color: var(--green); }
.pagination .active {
    background: var(--green); color: #000; border: 1px solid var(--green);
}
.pagination .dots { color: var(--dim); background: none; border: none; }
.pagination .nav-btn { font-size: 13px; gap: 5px; }
.pagination .nav-btn.disabled { opacity: .3; pointer-events: none; }

/* Empty */
.empty { text-align: center; padding: 80px 20px; color: var(--dim); }
.empty i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .2; }

@media (max-width: 900px) { .book-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 500px) { .book-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
</head>
<body>

<nav class="topbar">
    <a href="home.php" class="logo">KEWE</a>
    <a href="home.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <span class="sep">/</span>
    <span class="current">Tất cả sách</span>
</nav>

<div class="page-wrap">

    <div class="page-header">
        <h1><i class="fa-solid fa-layer-group"></i> Tất cả sách</h1>
        <span class="info"><?= $total ?> đầu sách · Trang <?= $page ?>/<?= $total_pages ?></span>
    </div>

    <?php if (!empty($books)): ?>
    <div class="book-grid">
        <?php foreach ($books as $book): ?>
        <div class="book-card">
            <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>">
                <img src="../code/images/<?= htmlspecialchars($book['cover']) ?>"
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='img/sach2.jpg'">
            </a>
            <div class="book-card-body">
                <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="book-card-title">
                    <?= htmlspecialchars($book['title']) ?>
                </a>
            </div>
            <div class="book-card-footer">
                <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>" class="btn-read">
                    <i class="fa-solid fa-book-open"></i> Đọc
                </a>
                <form action="luutruyen.php" method="POST">
                    <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn-save" title="Lưu"><i class="fa-solid fa-heart"></i></button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- PAGINATION -->
    <div class="pagination">
        <!-- Prev -->
        <a href="tatca.php?page=<?= $page - 1 ?>" class="nav-btn <?= $page <= 1 ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i> Trước
        </a>

        <?php
        // Hiển thị số trang
        $range = 2; // Số trang hiển thị xung quanh trang hiện tại
        for ($i = 1; $i <= $total_pages; $i++):
            if ($i == 1 || $i == $total_pages || abs($i - $page) <= $range):
        ?>
            <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="tatca.php?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php
            elseif ($i == 2 && $page > $range + 2):
                echo '<span class="dots">...</span>';
            elseif ($i == $total_pages - 1 && $page < $total_pages - $range - 1):
                echo '<span class="dots">...</span>';
            endif;
        endfor;
        ?>

        <!-- Next -->
        <a href="tatca.php?page=<?= $page + 1 ?>" class="nav-btn <?= $page >= $total_pages ? 'disabled' : '' ?>">
            Tiếp <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>

    <?php else: ?>
    <div class="empty">
        <i class="fa-solid fa-book-open"></i>
        <p>Chưa có sách nào trong hệ thống.</p>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
