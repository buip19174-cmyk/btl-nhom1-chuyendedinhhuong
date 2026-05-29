<?php
require_once '../../backend/require_admin.php';
require_admin();
include '../../database/connect.php';
/** @var mysqli $con */

// Nhận tham số Tìm kiếm, Trạng thái và Trang hiện tại từ URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Xây dựng điều kiện WHERE cho câu lệnh SQL dựa trên bộ lọc dữ liệu
$where_clauses = [];
if ($search !== '') {
    $search_safe = mysqli_real_escape_string($con, $search);
    $where_clauses[] = "title LIKE '%$search_safe%'";
}
if ($status_filter !== '' && $status_filter !== 'all') {
    $status_safe = mysqli_real_escape_string($con, $status_filter);
    $where_clauses[] = "status = '$status_safe'";
}
$where_sql = !empty($where_clauses) ? "WHERE " . implode(' AND ', $where_clauses) : "";

// Cấu hình hệ thống phân trang dữ liệu (Mỗi trang hiện 15 truyện)
$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Tính toán tổng số lượng truyện và tổng số trang sau khi lọc
$total_stories = 0;
$res_count = mysqli_query($con, "SELECT COUNT(*) AS total FROM stories $where_sql");
if ($res_count) {
    $total_comments = (int)(mysqli_fetch_assoc($res_count)['total'] ?? 0);
    $total_stories = $total_comments;
}
$total_pages = ceil($total_stories / $limit);

// Lấy danh sách truyện giới hạn theo phân trang và bộ lọc
$sql = "SELECT * FROM stories $where_sql ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stories = [];
$res = mysqli_query($con, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $stories[] = $row;
    }
}

// Hàm định nghĩa class CSS hiển thị màu sắc tương ứng với trạng thái truyện
function get_status_class(string $status): string {
    return match ($status) {
        'updating' => 'good',    
        'completed' => 'normal', 
        'hidden' => 'hot',       
        default => 'normal',
    };
}

// Hàm hiển thị tên tiếng Việt trực quan của trạng thái truyện
function get_status_label(string $status): string {
    return match ($status) {
        'updating' => 'Đang cập nhật',
        'completed' => 'Hoàn thành',
        'hidden' => 'Đang ẩn',
        default => $status,
    };
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý truyện</title>
    <link rel="stylesheet" href="../../frontend/admin/style.css">
    <style>
        .action-bar { margin-bottom: 20px; }
        .filter-form { display: flex; gap: 10px; align-items: center; }
        .filter-form input, .filter-form select { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .filter-form input { width: 250px; }
        .filter-form button { padding: 8px 15px; background: #111; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
        .filter-form .clear-btn { background: #6b7280; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-size: 14px; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 20px; padding: 10px; }
        .pagination a, .pagination span { padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; font-size: 14px; }
        .pagination .active { background-color: #111; color: white; border-color: #111; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <h2>Quản lý truyện</h2>
            <button class="add-btn" onclick="openModal()">+ Thêm truyện</button>
        </div>

        <div class="action-bar">
            <form method="GET" action="" class="filter-form">
                <input type="text" name="search" placeholder="Tìm truyện theo tên..." value="<?= htmlspecialchars($search) ?>">
                
                <select name="status">
                    <option value="all" <?= $status_filter === 'all' || $status_filter === '' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                    <option value="updating" <?= $status_filter === 'updating' ? 'selected' : '' ?>>Đang cập nhật</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="hidden" <?= $status_filter === 'hidden' ? 'selected' : '' ?>>Ẩn</option>
                </select>
                
                <button type="submit">Lọc dữ liệu</button>
                <?php if ($search !== '' || ($status_filter !== '' && $status_filter !== 'all')): ?>
                    <a href="stories.php" class="clear-btn">Xóa bộ lọc</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên truyện</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($stories)): ?>
                    <?php foreach($stories as $s): ?>
                    <tr data-story="<?= htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8') ?>">
                        <td><?= $s['id'] ?></td>
                        <td><strong><?= htmlspecialchars($s['title']) ?></strong></td>
                        <td>
                            <span class="status <?= get_status_class($s['status']) ?>">
                                <?= get_status_label($s['status']) ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn edit" onclick="openEditStory(this)">Sửa</button>
                            <button class="btn delete" onclick="deleteStory(<?= (int)$s['id'] ?>)">Xóa</button>
                            <a href="chapter.php?id=<?= $s['id'] ?>">
                                <button class="btn">Chương</button>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; color: #999; padding: 20px;">Không tìm thấy truyện nào phù hợp.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): 
                $query_string = http_build_query(array_filter(['search' => $search, 'status' => $status_filter]));
                $url_prefix = $query_string ? $query_string . '&' : '';

                // Thuật toán giới hạn hiển thị tối đa 3 số trang
                $max_links = 3;
                $start_p = max(1, $page - 1);
                $end_p = min($total_pages, $start_p + $max_links - 1);

                // Cân bằng lại nếu trang hiện tại ở sát cuối danh sách
                if ($end_p - $start_p + 1 < $max_links) {
                    $start_p = max(1, $end_p - $max_links + 1);
                }
            ?>
                <div class="pagination">
                    <?php if ($page > 2): ?>
                        <a href="?<?= $url_prefix ?>page=1" title="Trang đầu">&laquo; Đầu</a>
                    <?php endif; ?>

                    <?php if ($page > 1): ?>
                        <a href="?<?= $url_prefix ?>page=<?= $page - 1 ?>">Trước</a>
                    <?php endif; ?>

                    <?php for ($i = $start_p; $i <= $end_p; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= $url_prefix ?>page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?<?= $url_prefix ?>page=<?= $page + 1 ?>">Sau</a>
                    <?php endif; ?>

                    <?php if ($page < $total_pages - 1): ?>
                        <a href="?<?= $url_prefix ?>page=<?= $total_pages ?>" title="Trang cuối">Cuối &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="storyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); z-index: 999;">
        <div style="background:white; width:560px; max-width:92vw; margin:6% auto; padding:18px 18px 14px; border-radius:10px;">
            <h3 id="modalTitle" style="margin-bottom:10px;">Thêm truyện</h3>

            <input type="hidden" id="storyId" value="">
            <input type="hidden" id="coverOld" value="">

            <label style="display:block; font-size:13px; margin-top:10px;">Tên truyện</label>
            <input id="title" placeholder="Tên truyện" style="width:100%; padding:10px; margin-top:6px; border: 1px solid #ccc; border-radius:4px;">

            <label style="display:block; font-size:13px; margin-top:10px;">Mã danh mục (home, tho, tinhcam…)</label>
            <textarea id="description" placeholder="Chỉ nhập mã danh mục ngắn, không nhập mô tả dài" style="width:100%; padding:10px; margin-top:6px; min-height:60px; border: 1px solid #ccc; border-radius:4px;"></textarea>
            <p style="font-size:12px; color:#666; margin-top:4px;">Dùng để phân loại truyện trên trang chủ. Không phải phần giới thiệu nội dung.</p>

            <label style="display:block; font-size:13px; margin-top:10px;">Trạng thái</label>
            <select id="status" style="width:100%; padding:10px; margin-top:6px; border: 1px solid #ccc; border-radius:4px;">
                <option value="updating">Đang cập nhật</option>
                <option value="completed">Hoàn thành</option>
                <option value="hidden">Ẩn</option>
            </select>

            <label style="display:block; font-size:13px; margin-top:10px;">Ảnh bìa (tùy chọn)</label>
            <input id="cover" type="file" accept="image/*" style="width:100%; margin-top:6px;">

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
                <button onclick="closeStoryModal()" style="padding:10px 12px; border: 1px solid #ccc; background: #fff; border-radius: 4px; cursor: pointer;">Đóng</button>
                <button onclick="saveStory()" style="padding:10px 12px; background:#111; color:#fff; border:0; border-radius:6px; cursor: pointer;">Lưu</button>
            </div>
        </div>
    </div>

    <script>
    function openModal() {
        openAddStory();
    }

    function openAddStory() {
        document.getElementById('modalTitle').innerText = 'Thêm truyện';
        document.getElementById('storyId').value = '';
        document.getElementById('coverOld').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        document.getElementById('status').value = 'updating';
        document.getElementById('cover').value = '';
        document.getElementById('storyModal').style.display = 'block';
    }

    function openEditStory(btn) {
        const tr = btn.closest('tr');
        const raw = tr.getAttribute('data-story');
        const story = JSON.parse(raw);

        document.getElementById('modalTitle').innerText = 'Sửa truyện';
        document.getElementById('storyId').value = story.id || '';
        document.getElementById('coverOld').value = story.cover || '';
        document.getElementById('title').value = story.title || '';
        document.getElementById('description').value = story.description || '';
        document.getElementById('status').value = story.status || 'updating';
        document.getElementById('cover').value = '';
        document.getElementById('storyModal').style.display = 'block';
    }

    function closeStoryModal() {
        document.getElementById('storyModal').style.display = 'none';
    }

    async function saveStory() {
        const storyId = document.getElementById('storyId').value.trim();
        const fd = new FormData();
        if (storyId) fd.append('storyId', storyId);
        fd.append('title', document.getElementById('title').value.trim());
        fd.append('description', document.getElementById('description').value.trim());
        fd.append('status', document.getElementById('status').value);
        const coverOld = document.getElementById('coverOld').value;
        if (coverOld) fd.append('cover_old', coverOld);
        const file = document.getElementById('cover').files[0];
        if (file) fd.append('cover', file);

        const url = storyId ? '../../backend/edit_story.php' : '../../backend/add_story.php';
        const res = await fetch(url, { method: 'POST', body: fd });
        const data = await res.json().catch(() => null);
        if (!data) {
            alert('Có lỗi xảy ra (phản hồi không hợp lệ).');
            return;
        }
        alert(data.message || (data.status === 'success' ? 'Thành công' : 'Thất bại'));
        if (data.status === 'success') location.reload();
    }

    async function deleteStory(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa truyện này?')) return;
        const fd = new FormData();
        fd.append('id', id);
        const res = await fetch('../../backend/delete_story.php', { method: 'POST', body: fd });
        const data = await res.json().catch(() => null);
        if (!data) {
            alert('Có lỗi xảy ra (phản hồi không hợp lệ).');
            return;
        }
        alert(data.message || (data.status === 'success' ? 'Đã xóa' : 'Xóa thất bại'));
        if (data.status === 'success') location.reload();
    }
    </script>
</body>
</html>