<?php
session_start();
include 'db_connect.php';

// Chưa đăng nhập thì quay về trang chủ
if (!isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

$username = $_SESSION['username'];

// Lấy thông tin người dùng (KHÔNG có created_at)
$sql = "SELECT username, email, sdt 
        FROM users 
        WHERE username = '$username' 
        LIMIT 1";

$result = mysqli_query($con, $sql);

if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($con));
}

if (mysqli_num_rows($result) == 0) {
    die("Không tìm thấy thông tin người dùng");
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản của tôi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* General Body Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #070707ff;
    margin: 0;
    padding: 0;
}

/* Account Page Styling */
.account-page {
    width: 100%;
    max-width: 600px;
    margin: 30px auto;
    padding: 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Heading */
.account-page h2 {
    text-align: center;
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
}

/* Account Box Styling */
.account-box {
    padding: 20px;
    background-color: #d6cacaff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Paragraphs in Account Box */
.account-box p {
    font-size: 16px;
    color: #555;
    line-height: 1.6;
    margin: 8px 0;
}

.account-box p strong {
    color: #333;
}

/* Links / Buttons */
.btn {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 20px;
    font-size: 16px;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

/* Button for Tủ sách */
.btn {
    background-color: #4CAF50;
    color: white !important;
}

.btn:hover {
    background-color: #45a049;
    
}

/* Button for Logout */
.btn.logout {
    background-color: #e53935; 
    color: #ffffff !important; 
    font-weight: 600;
    opacity: 1 !important;
}


.btn.logout:hover {
    background-color: #e53935;
}



    


/* Separator Line */
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .account-page {
        width: 90%;
    }
}

    </style>
</head>
<body>

<div class="account-page">
    <h2>👤 Thông tin tài khoản</h2>

    <div class="account-box">
        <p><strong>Tên đăng nhập:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Số điện thoại:</strong> <?php echo $user['sdt'] ?: 'Chưa cập nhật'; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email'] ?: 'Chưa cập nhật'; ?></p>

        <hr>

        <a href="tusach.php" class="btn">📚 Tủ sách cá nhân</a>
        <a href="dangxuat.php" class="btn logout">🚪 Đăng xuất</a>
        <a href="home.php" class="btn home" ><i class="fa-solid fa-house-chimney"></i>Trang chủ</a>
    </div>
</div>

</body>
</html>
