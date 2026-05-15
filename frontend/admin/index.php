<?php
session_start();
// Nếu không phải admin, đá về trang chủ hoặc trang đăng nhập
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main -->
  <div class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div>
        <h1>Tổng Quan</h1>
        <p>Chào mừng quản trị viên quay trở lại</p>
      </div>
      <div class="notify">
        <i class="fa-solid fa-bell"></i>
        <span class="badge">3</span>
      </div>
      <div class="search-box">
        <input type="text" placeholder="Tìm kiếm...">
      </div>
    </div>

    <!-- Cards -->
    <div class="cards">
      <div class="card">
        <h3>Tổng Người Dùng</h3>
        <p>1,245</p>
      </div>

      <div class="card">
        <h3>Truyện</h3>
        <p>328</p>
      </div>

      <div class="card">
        <h3>Bình Luận</h3>
        <p>5,231</p>
      </div>

      <div class="card">
        <h3>Báo cáo</h3>
        <p>12</p>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>1</td>
            <td>Nguyen Van A</td>
            <td>Quản trị viên</td>
            <td><span class="status">Đang hoạt động</span></td>
            <td>
              <button class="btn edit">Sửa</button>
              <button class="btn delete">Xóa</button>
            </td>
          </tr>

          <tr>
            <td>2</td>
            <td>Tran Thi B</td>
            <td>Member</td>
            <td><span class="status">Banned</span></td>
            <td>
              <button class="btn edit">Sửa</button>
              <button class="btn delete">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Upload -->
    <div class="upload-box">
      <h2>Tải Ảnh Bìa Lên</h2>

      <div class="drop-zone">
        Kéo & thả ảnh vào đây
      </div>
    </div>

  </div>

</body>
</html>