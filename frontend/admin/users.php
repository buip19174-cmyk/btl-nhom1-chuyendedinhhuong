<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, sans-serif;
        }

        body{
            display:flex;
            background:#f3f4f6;
            min-height:100vh;
        }

        /* Sidebar */
        .sidebar{
            width:250px;
            background:#111827;
            color:white;
            padding:20px;
        }

        .sidebar h2{
            margin-bottom:30px;
            text-align:center;
        }

        .menu{
            list-style:none;
        }

        .menu li{
            padding:14px;
            margin-bottom:10px;
            border-radius:10px;
            cursor:pointer;
            transition:0.3s;
        }

        .menu li:hover{
            background:#1f2937;
        }

        /* Main */
        .main{
            flex:1;
            padding:20px;
        }

        .topbar{
            background:white;
            padding:20px;
            border-radius:15px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }

        .search-box input{
            padding:10px 15px;
            border:1px solid #ccc;
            border-radius:10px;
            outline:none;
            width:250px;
        }

        /* Action */
        .action-bar{
            background:white;
            padding:20px;
            border-radius:15px;
            margin-bottom:20px;

            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:10px;
        }

        .left-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .left-actions input,
        .left-actions select{
            padding:10px;
            border:1px solid #ccc;
            border-radius:10px;
        }

        .add-btn{
            background:black;
            color:white;
            border:none;
            padding:10px 18px;
            border-radius:10px;
            cursor:pointer;
        }

        /* Table */
        .table-container{
            background:white;
            border-radius:15px;
            overflow:hidden;
            overflow-x:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th,
        table td{
            padding:15px;
            border-bottom:1px solid #eee;
            text-align:left;
        }

        table th{
            background:#f9fafb;
        }

        .avatar{
            width:45px;
            height:45px;
            border-radius:50%;
            object-fit:cover;
        }

        .status{
            padding:5px 10px;
            border-radius:20px;
            font-size:14px;
            display:inline-block;
        }

        .active{
            background:#dcfce7;
            color:#166534;
        }

        .banned{
            background:#fee2e2;
            color:#991b1b;
        }

        .btn{
            padding:8px 14px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            margin-right:5px;
        }

        .edit{
            background:#dbeafe;
        }

        .delete{
            background:#fee2e2;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">

        <h2>Trang Quản Trị</h2>

        <ul class="menu">
            <li>Tổng Quan</li>
            <li>Người Dùng</li>
            <li>Truyện</li>
            <li>Chương</li>
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