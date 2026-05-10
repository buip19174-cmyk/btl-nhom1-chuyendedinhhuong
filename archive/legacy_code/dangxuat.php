<?php
session_start(); // Khởi động session để hệ thống biết bạn là ai

// Xóa tất cả các biến trong session
$_SESSION = array();

// Nếu muốn xóa hoàn toàn cookie session (tăng tính bảo mật)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy bỏ toàn bộ session trên máy chủ
session_destroy();

// Chuyển hướng người dùng về trang chủ sau khi đăng xuất
header("Location: home.php");
exit();
?>