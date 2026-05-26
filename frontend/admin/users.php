<?php
require_once '../../backend/require_admin.php';
require_admin();
include '../../database/connect.php';

$sql = "SELECT * FROM users";
$users = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <!-- Main -->
    <div class="main">

        <!-- Topbar -->
        <div class="topbar">
            <div>
                <h1>Quản Lý Người Dùng</h1>
                <p>Danh sách người dùng hệ thống</p>
            </div>
            <div class="search-box">
                <input type="text" id="userSearchInput" placeholder="Tìm kiếm người dùng...">
            </div>

        </div>

        <!-- Action -->
        <div class="action-bar">

            <div class="left-actions">

                <input type="text" id="userNameFilter" placeholder="Tên người dùng">

                <select id="userRoleFilter">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin">Admin</option>
                    <option value="user">Thành viên</option>
                </select>

                <select id="userStatusFilter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="banned">Đã khoá</option>
                </select>

            </div>

            <button class="add-btn" onclick="openAddUser()">+ Thêm Người Dùng</button>

        </div>

        <!-- Table -->
        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>

<?php while($row = $users->fetch_assoc()): ?>

<tr data-user="<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>">
    <td><?= $row['id'] ?></td>

    <td>
        <img class="avatar"
             src="https://i.pravatar.cc/100?img=<?= $row['id'] ?>">
    </td>

    <td><?= $row['username'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['role'] ?></td>

    <td>
        <?php if($row['status'] == 'active'): ?>
            <span class="status active">Hoạt động</span>
        <?php else: ?>
            <span class="status banned">Đã khoá</span>
        <?php endif; ?>
    </td>

    <td>
        <button class="btn edit"
                onclick="openEditUser(this)">
            Sửa
        </button>

        <button class="btn delete"
                onclick="delete_user(<?= $row['id'] ?>)">
            Xoá
        </button>
    </td>
</tr>

<?php endwhile; ?>

</tbody>

            </table>

        </div>

    </div>

    <!-- MODAL ADD/EDIT USER -->
    <div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55);">
        <div style="background:white; width:560px; max-width:92vw; margin:6% auto; padding:18px 18px 14px; border-radius:10px;">
            <h3 id="userModalTitle" style="margin-bottom:10px;">Thêm người dùng</h3>

            <input type="hidden" id="userId" value="">

            <label style="display:block; font-size:13px; margin-top:10px;">Tên đăng nhập</label>
            <input id="username" style="width:100%; padding:10px; margin-top:6px;">

            <label style="display:block; font-size:13px; margin-top:10px;">Số điện thoại</label>
            <input id="sdt" style="width:100%; padding:10px; margin-top:6px;">

            <label style="display:block; font-size:13px; margin-top:10px;">Email</label>
            <input id="email" style="width:100%; padding:10px; margin-top:6px;">

            <label style="display:block; font-size:13px; margin-top:10px;">Mật khẩu (để trống nếu không đổi)</label>
            <input id="password" type="password" style="width:100%; padding:10px; margin-top:6px;">

            <label style="display:block; font-size:13px; margin-top:10px;">Vai trò</label>
            <select id="role" style="width:100%; padding:10px; margin-top:6px;">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>

            <label style="display:block; font-size:13px; margin-top:10px;">Trạng thái</label>
            <select id="status" style="width:100%; padding:10px; margin-top:6px;">
                <option value="active">active</option>
                <option value="banned">banned</option>
            </select>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
                <button onclick="closeUserModal()" style="padding:10px 12px;">Đóng</button>
                <button onclick="saveUser()" style="padding:10px 12px; background:#111; color:#fff; border:0; border-radius:6px;">Lưu</button>
            </div>
        </div>
    </div>

    <script>
    function openAddUser() {
        document.getElementById('userModalTitle').innerText = 'Thêm người dùng';
        document.getElementById('userId').value = '';
        document.getElementById('username').value = '';
        document.getElementById('sdt').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('role').value = 'user';
        document.getElementById('status').value = 'active';
        document.getElementById('userModal').style.display = 'block';
    }

    function openEditUser(btn) {
        const tr = btn.closest('tr');
        const raw = tr.getAttribute('data-user');
        const user = JSON.parse(raw);

        document.getElementById('userModalTitle').innerText = 'Sửa người dùng';
        document.getElementById('userId').value = user.id || '';
        document.getElementById('username').value = user.username || '';
        document.getElementById('sdt').value = user.sdt || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('password').value = '';
        document.getElementById('role').value = user.role || 'user';
        document.getElementById('status').value = user.status || 'active';
        document.getElementById('userModal').style.display = 'block';
    }

    function closeUserModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    async function saveUser() {
        const userId = document.getElementById('userId').value.trim();
        const fd = new FormData();

        fd.append('username', document.getElementById('username').value.trim());
        fd.append('sdt', document.getElementById('sdt').value.trim());
        fd.append('email', document.getElementById('email').value.trim());
        fd.append('password', document.getElementById('password').value);
        fd.append('role', document.getElementById('role').value);
        fd.append('status', document.getElementById('status').value);

        let url = '../../backend/add_user.php';
        if (userId) {
            url = '../../backend/edit_user.php';
            fd.append('userId', userId);
        }

        const res = await fetch(url, { method: 'POST', body: fd });
        const data = await res.json().catch(() => null);
        if (!data) return alert('Có lỗi xảy ra (phản hồi không hợp lệ).');
        alert(data.message || (data.status === 'success' ? 'Thành công' : 'Thất bại'));
        if (data.status === 'success') location.reload();
    }

    async function delete_user(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) return;
        const fd = new FormData();
        fd.append('id', id);
        const res = await fetch('../../backend/delete_user.php', { method: 'POST', body: fd });
        const data = await res.json().catch(() => null);
        if (!data) return alert('Có lỗi xảy ra (phản hồi không hợp lệ).');
        alert(data.message || (data.status === 'success' ? 'Đã xóa' : 'Xóa thất bại'));
        if (data.status === 'success') location.reload();
    }

    function filterUsers() {
        const q = (document.getElementById('userSearchInput').value || document.getElementById('userNameFilter').value || '').trim().toLowerCase();
        const role = document.getElementById('userRoleFilter').value;
        const status = document.getElementById('userStatusFilter').value;
        document.querySelectorAll('.table-container tbody tr').forEach(function(tr) {
            const username = (tr.children[2]?.textContent || '').toLowerCase();
            const email = (tr.children[3]?.textContent || '').toLowerCase();
            const userRole = (tr.children[4]?.textContent || '').trim().toLowerCase();
            const statusText = (tr.children[5]?.textContent || '').trim();
            const userStatus = statusText.includes('khoá') || statusText.includes('khoa') ? 'banned' : 'active';
            const matchText = !q || username.includes(q) || email.includes(q);
            const matchRole = !role || userRole === role;
            const matchStatus = !status || userStatus === status;
            tr.style.display = (matchText && matchRole && matchStatus) ? '' : 'none';
        });
    }

    ['userSearchInput', 'userNameFilter', 'userRoleFilter', 'userStatusFilter'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', filterUsers);
        if (el && el.tagName === 'SELECT') el.addEventListener('change', filterUsers);
    });
    </script>

</body>
</html>