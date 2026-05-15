<?php
include '../../backend/connect.php';

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
                <input type="text" placeholder="Tìm kiếm người dùng...">
            </div>

        </div>

        <!-- Action -->
        <div class="action-bar">

            <div class="left-actions">

                <input type="text" placeholder="Tên người dùng">

                <select>
                    <option>Tất cả vai trò</option>
                    <option>Admin</option>
                    <option>Thành viên</option>
                </select>

                <select>
                    <option>Tất cả trạng thái</option>
                    <option>Hoạt động</option>
                    <option>Đã khoá</option>
                </select>

            </div>

            <button class="add-btn" onclick="add_user(<?= $s['id'] ?>)">+ Thêm Người Dùng</button>

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

<tr>
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
                onclick="edit_user(<?= $row['id'] ?>)">
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

</body>
</html>