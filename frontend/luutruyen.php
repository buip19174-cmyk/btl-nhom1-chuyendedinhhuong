<?php
session_start();
include_once '../database/connect.php';

/* Chưa đăng nhập → quay về home */
if (!isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

/* Thiếu story_id */
if (!isset($_POST['story_id'])) {
    die("Thiếu ID truyện");
}

$story_id = (int)$_POST['story_id'];
$username = $_SESSION['username'];

/* Lấy user_id */
$sqlUser = "SELECT id FROM users WHERE username = ?";
$stmtUser = mysqli_prepare($con, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $user_id);
mysqli_stmt_fetch($stmtUser);
mysqli_stmt_close($stmtUser);

if (!$user_id) {
    die("User không tồn tại");
}

/* Lưu truyện – không trùng */
$sqlSave = "
    INSERT INTO user_stories (user_id, story_id)
    SELECT ?, ?
    WHERE NOT EXISTS (
        SELECT 1 FROM user_stories WHERE user_id = ? AND story_id = ?
    )
";
$stmtSave = mysqli_prepare($con, $sqlSave);
mysqli_stmt_bind_param($stmtSave, "iiii", $user_id, $story_id, $user_id, $story_id);
mysqli_stmt_execute($stmtSave);
mysqli_stmt_close($stmtSave);

// Thay vì dùng header location của PHP, chúng ta dùng Meta Refresh để hiển thị giao diện trong 1.5 giây
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang lưu vào tủ sách...</title>
    <meta http-equiv="refresh" content="1.5;url=home.php?saved=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #121212; /* Màu nền tối Waka */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Roboto, sans-serif;
            color: #ffffff;
        }

        .process-container {
            text-align: center;
            width: 300px;
        }

        .icon-box {
            font-size: 50px;
            margin-bottom: 20px;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }

        .status-text {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        /* Thanh Progress Bar */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #282828;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #1ed760, #1db954);
            border-radius: 10px;
            animation: fillProgress 1.5s ease-in-out forwards;
            box-shadow: 0 0 10px rgba(30, 215, 96, 0.5);
        }

        @keyframes fillProgress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        .sub-text {
            margin-top: 15px;
            color: #a7a7a7;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div class="process-container">
        <div class="icon-box">📚</div>
        <div class="status-text">Đang thêm vào tủ sách...</div>
        
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

        <div class="sub-text">Hệ thống đang xử lý, vui lòng chờ...</div>
    </div>

</body>
</html>