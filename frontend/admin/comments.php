<?php
require_once '../../backend/require_admin.php';
require_admin();
include_once '../../database/connect.php';
/** @var mysqli $con */

// Xử lý hành động Xóa bình luận vĩnh viễn bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delete_id = (int)$_POST['comment_id'];
    if ($delete_id > 0) {
        $stmt = mysqli_prepare($con, "DELETE FROM comments WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: comments.php" . (isset($_GET['page']) ? "?page=" . (int)$_GET['page'] : ""));
    exit;
}

// Xử lý hành động Khóa / Mở khóa tài khoản người dùng bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_ban') {
    $target_user_id = (int)$_POST['target_user_id'];
    $current_user_status = $_POST['current_user_status'];
    $new_status = ($current_user_status === 'banned') ? 'active' : 'banned';

    if ($target_user_id > 0) {
        $stmt = mysqli_prepare($con, "UPDATE users SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $target_user_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: comments.php" . (isset($_GET['page']) ? "?page=" . (int)$_GET['page'] : ""));
    exit;
}

// Lấy các tham số tìm kiếm và bộ lọc từ URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$story_filter = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;

// Xây dựng điều kiện WHERE cho câu lệnh SQL dựa trên bộ lọc
$where_clauses = [];
if ($search !== '') {
    $search_safe = mysqli_real_escape_string($con, $search);
    $where_clauses[] = "(c.content LIKE '%$search_safe%' OR u.username LIKE '%$search_safe%')";
}
if ($story_filter > 0) {
    $where_clauses[] = "c.story_id = $story_filter";
}
$where_sql = !empty($where_clauses) ? "WHERE " . implode(' AND ', $where_clauses) : "";

// Cấu hình hệ thống phân trang dữ liệu
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Lấy tổng số lượng bình luận sau khi đã áp dụng bộ lọc tìm kiếm
$total_comments = 0;
$res_count = mysqli_query($con, "
    SELECT COUNT(*) AS total 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    $where_sql
");
if ($res_count) {
    $total_comments = (int)(mysqli_fetch_assoc($res_count)['total'] ?? 0);
}
$total_pages = ceil($total_comments / $limit);

// Lấy danh sách tất cả các truyện để hiển thị vào thanh tùy chọn bộ lọc
$stories_list = mysqli_query($con, "SELECT id, title FROM stories ORDER BY title ASC");

// Lấy danh sách bình luận kèm theo trạng thái tài khoản của người dùng (u.status)
$sql = mysqli_query($con, "
    SELECT 
        c.id, c.content, c.created_at, c.user_id,
        u.username, u.status AS user_status, 
        s.title AS story_title
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN stories s ON c.story_id = s.id
    $where_sql
    ORDER BY c.created_at DESC
    LIMIT $limit OFFSET $offset
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bình luận</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
    </style>
</head>

<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    <div class="topbar">
        <div>
            <h1>Quản lý bình luận</h1><br>
            <p>Xem và quản lý comment của người dùng</p>
        </div>
    </div>

    <form method="GET" action="" class="filter-bar">
        <input type="text" name="search" placeholder="Tìm theo nội dung bình luận hoặc tên tài khoản..." value="<?= htmlspecialchars($search) ?>">
        
        <select name="story_id">
            <option value="0">-- Tất cả các truyện --</option>
            <?php if ($stories_list): ?>
                <?php while ($st_item = mysqli_fetch_assoc($stories_list)): ?>
                    <option value="<?= $st_item['id'] ?>" <?= $story_filter === (int)$st_item['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($st_item['title']) ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
        
        <button type="submit">Tìm kiếm</button>
        <?php if ($search !== '' || $story_filter > 0): ?>
            <a href="comments.php" class="clear-btn">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <div class="box">
        <div class="title">Danh sách bình luận (<?= number_format($total_comments) ?>)</div>

        <?php if ($sql && mysqli_num_rows($sql) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($sql)): 
                $is_banned = ($row['user_status'] === 'banned');
            ?>
                <div class="comment-box <?= $is_banned ? 'user-banned' : '' ?>">
                    <div class="comment-top">
                        <div>
                            <div class="comment-user">
                                <?= htmlspecialchars($row['username'] ?? 'Ẩn danh') ?>
                                <?php if ($is_banned): ?>
                                    <span class="user-status-badge">Đã khóa tài khoản</span>
                                <?php endif; ?>
                            </div>
                            <div class="comment-story">
                                Truyện: <?= htmlspecialchars($row['story_title'] ?? 'Không xác định') ?>
                            </div>
                        </div>

                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="comment-time">
                                <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                            </div>
                            
                            <div class="action-forms">
                                <?php if (isset($row['user_id']) && $row['user_id'] > 0): ?>
                                    <form method="POST" action="" onsubmit="return confirm('<?= $is_banned ? 'Mở khóa tài khoản này?' : 'Khóa vĩnh viễn tài khoản này?' ?>');">
                                        <input type="hidden" name="action" value="toggle_ban">
                                        <input type="hidden" name="target_user_id" value="<?= $row['user_id'] ?>">
                                        <input type="hidden" name="current_user_status" value="<?= htmlspecialchars($row['user_status']) ?>">
                                        <button type="submit" class="<?= $is_banned ? 'unban-btn' : 'ban-btn' ?>">
                                            <?= $is_banned ? 'Mở khóa' : 'Khóa User' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="comment_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="delete-btn" style="cursor: pointer;">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="comment-content">
                        <?= nl2br(htmlspecialchars($row['content'])) ?>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if ($total_pages > 1): 
                $query_string = http_build_query(array_filter(['search' => $search, 'story_id' => $story_filter]));
                $url_prefix = $query_string ? $query_string . '&' : '';
            ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?= $url_prefix ?>page=<?= $page - 1 ?>">&laquo; Trước</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= $url_prefix ?>page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?<?= $url_prefix ?>page=<?= $page + 1 ?>">Sau &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty">Không tìm thấy bình luận nào khớp với bộ lọc</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>