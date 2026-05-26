<?php
require_once '../../backend/require_admin.php';
require_admin();
include '../../database/connect.php';

$sql = "SELECT * FROM stories";
$stories = [];
$res = mysqli_query($con, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $stories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="../../frontend/admin/style.css">
    </head>
    <body>
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main -->
        <div class="main">

            <div class="topbar">
                <h2>Quản lý truyện</h2>
                <button class="add-btn" onclick="openModal()">+ Thêm truyện</button>
            </div>

        <!-- Action -->
            <div class="action-bar">
                <div class="left-actions">
                    <input type="text" id="searchInput" placeholder="Tìm truyện...">
                    <select id="filterSelect">
                        <option>Tất cả</option>
                        <option>Đang cập nhật</option>
                        <option>Hoàn thành</option>
                        <option>Ẩn</option>
                    </select>
                    <div id="resultContainer"> <!-- Kết quả sẽ đổ vào đây --> </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Tên truyện</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>

                    <tbody>
                    <?php foreach($stories as $s): ?>
                    <tr data-story="<?= htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8') ?>">
                        <td><?= $s['id'] ?></td>
                        <td><?= $s['title'] ?></td>
                        <td>
                            <span class="status good">
                                <?= $s['status'] ?>
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
                    </tbody>

                </table>
            </div>

        </div>

        <!-- MODAL ADD/EDIT STORY -->
        <div id="storyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55);">
            <div style="background:white; width:560px; max-width:92vw; margin:6% auto; padding:18px 18px 14px; border-radius:10px;">
                <h3 id="modalTitle" style="margin-bottom:10px;">Thêm truyện</h3>

                <input type="hidden" id="storyId" value="">
                <input type="hidden" id="coverOld" value="">

                <label style="display:block; font-size:13px; margin-top:10px;">Tên truyện</label>
                <input id="title" placeholder="Tên truyện" style="width:100%; padding:10px; margin-top:6px;">

                <label style="display:block; font-size:13px; margin-top:10px;">Mã danh mục (home, tho, tinhcam…)</label>
                <textarea id="description" placeholder="Chỉ nhập mã danh mục ngắn, không nhập mô tả dài" style="width:100%; padding:10px; margin-top:6px; min-height:60px;"></textarea>
                <p style="font-size:12px; color:#666; margin-top:4px;">Dùng để phân loại truyện trên trang chủ. Không phải phần giới thiệu nội dung.</p>

                <label style="display:block; font-size:13px; margin-top:10px;">Trạng thái</label>
                <select id="status" style="width:100%; padding:10px; margin-top:6px;">
                    <option value="ongoing">ongoing</option>
                    <option value="completed">completed</option>
                    <option value="hidden">hidden</option>
                </select>

                <label style="display:block; font-size:13px; margin-top:10px;">Ảnh bìa (tùy chọn)</label>
                <input id="cover" type="file" accept="image/*" style="width:100%; margin-top:6px;">

                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
                    <button onclick="closeStoryModal()" style="padding:10px 12px;">Đóng</button>
                    <button onclick="saveStory()" style="padding:10px 12px; background:#111; color:#fff; border:0; border-radius:6px;">Lưu</button>
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
            document.getElementById('status').value = 'ongoing';
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
            document.getElementById('status').value = story.status || 'ongoing';
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

        const statusMap = {
            'Tất cả': '',
            'Đang cập nhật': 'ongoing',
            'Hoàn thành': 'completed',
            'Ẩn': 'hidden'
        };

        function filterStories() {
            const q = (document.getElementById('searchInput').value || '').trim().toLowerCase();
            const statusFilter = statusMap[document.getElementById('filterSelect').value] || '';
            document.querySelectorAll('.table-container tbody tr').forEach(function(tr) {
                const title = (tr.children[1]?.textContent || '').toLowerCase();
                const status = (tr.children[2]?.textContent || '').trim().toLowerCase();
                const matchTitle = !q || title.includes(q);
                const matchStatus = !statusFilter || status === statusFilter;
                tr.style.display = (matchTitle && matchStatus) ? '' : 'none';
            });
        }

        document.getElementById('searchInput').addEventListener('input', filterStories);
        document.getElementById('filterSelect').addEventListener('change', filterStories);
        </script>

    </body>
</html>

