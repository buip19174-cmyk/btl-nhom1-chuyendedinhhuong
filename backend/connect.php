<?php
$host = 'localhost';
$db   = 'db_BTL5';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dbname = "db_BTL5";

// Khởi tạo kết nối MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("LỖI KẾT NỐI DATABASE: " . $conn->connect_error);
}

// Thiết lập tiếng Việt
$conn->set_charset($charset);

// Tạo database nếu chưa có
$sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
mysqli_query($conn, $sql);

// Chọn database
mysqli_select_db($conn, $dbname);

// Tạo bảng users
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    sdt VARCHAR(20),
    email VARCHAR(100),
    password VARCHAR(255)
)ENGINE=InnoDB");

// Tạo bảng stories
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    cover VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=InnoDB");

// Tạo bảng chapters
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT,
    title VARCHAR(255),
    content LONGTEXT,
    chapter_number INT
)");

// Tạo bảng user_stories
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    story_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (story_id) REFERENCES stories(id)
)ENGINE=InnoDB");

?>


