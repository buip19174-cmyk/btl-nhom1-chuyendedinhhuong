

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: #f3f4f6;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #111827;
      color: white;
      padding: 20px;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      text-align: center;
    }

    .menu {
      list-style: none;
    }

    .menu li {
      padding: 14px;
      margin-bottom: 10px;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .menu li:hover {
      background: #1f2937;
    }

    /* Main */
    .main {
      flex: 1;
      padding: 20px;
    }

    /* Topbar */
    .topbar {
      background: white;
      padding: 20px;
      border-radius: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .search-box input {
      padding: 10px 15px;
      border: 1px solid #ccc;
      border-radius: 10px;
      outline: none;
    }

    /* Cards */
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .card h3 {
      color: gray;
      margin-bottom: 10px;
    }

    .card p {
      font-size: 30px;
      font-weight: bold;
    }

    /* Table */
    .table-container {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th,
    table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    table th {
      background: #f9fafb;
    }

    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 14px;
      background: #e5e7eb;
    }

    .btn {
      padding: 8px 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-right: 5px;
    }

    .edit {
      background: #dbeafe;
    }

    .delete {
      background: #fee2e2;
    }

    /* Upload */
    .upload-box {
      margin-top: 20px;
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .drop-zone {
      border: 2px dashed #ccc;
      padding: 40px;
      text-align: center;
      border-radius: 15px;
      margin-top: 15px;
      color: gray;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Trang Quản trị</h2>

    <ul class="menu">
      <li>Tổng Quan</li>
      <li>Người Dùng</li>
      <li>Truyện</li>
      <li>Chương</li>
      <li>Bình luận</li>
      <li>Báo cáo</li>
      <li>Thông báo</li>
      <li>Thống kê</li>
      <li>Cài đặt</li>
    </ul>
  </div>

  <!-- Main -->
  <div class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div>
        <h1>Tổng Quan</h1>
        <p>Chào mừng quản trị viên quay trở lại</p>
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