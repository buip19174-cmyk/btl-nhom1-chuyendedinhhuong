<?php
$host = 'localhost';
$db   = 'db_BTL5';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dbname = "db_BTL5";

// Khởi tạo kết nối MySQLi
$con = new mysqli($host, $user, $pass, $db);

// Kiểm tra kết nối
if ($con->connect_error) {
    die("LỖI KẾT NỐI DATABASE: " . $con->connect_error);
}

// Thiết lập tiếng Việt
$con->set_charset($charset);
