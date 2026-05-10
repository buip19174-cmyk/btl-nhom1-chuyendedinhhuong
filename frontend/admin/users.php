<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">

        <h2>Trang Quản Trị</h2>

        <ul class="menu">
            <li>Tổng Quan</li>
            <li class="active-menu"\>Người Dùng</li>
            <li>Truyện</li>
            <li>Bình Luận</li>
            <li>Báo Cáo</li>
            <li>Thông Báo</li>
            <li>Cài Đặt</li>
        </ul>

    </div>

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

            <button class="add-btn">+ Thêm Người Dùng</button>

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

                    <tr>
                        <td>1</td>

                        <td>
                            <img class="avatar"
                            src="https://i.pravatar.cc/100?img=1">
                        </td>

                        <td>Nguyễn Văn A</td>
                        <td>a@gmail.com</td>
                        <td>Admin</td>

                        <td>
                            <span class="status active">
                                Hoạt động
                            </span>
                        </td>

                        <td>
                            <button class="btn edit">
                                Sửa
                            </button>

                            <button class="btn delete">
                                Xoá
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>

                        <td>
                            <img class="avatar"
                            src="https://i.pravatar.cc/100?img=2">
                        </td>

                        <td>Trần Thị B</td>
                        <td>b@gmail.com</td>
                        <td>Thành viên</td>

                        <td>
                            <span class="status banned">
                                Đã khoá
                            </span>
                        </td>

                        <td>
                            <button class="btn edit">
                                Sửa
                            </button>

                            <button class="btn delete">
                                Xoá
                            </button>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>