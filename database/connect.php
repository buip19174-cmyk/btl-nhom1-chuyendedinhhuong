<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "db_BTL5";

$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con) {
    echo "Không thể kết nối database: " . mysqli_connect_error();
    exit();
}

mysqli_set_charset($con, "utf8mb4");

// ── Auto-migration: thêm cột coins nếu chưa có ──
$_col = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'coins'");
if ($_col && mysqli_num_rows($_col) === 0) {
    mysqli_query($con, "ALTER TABLE users ADD COLUMN coins INT NOT NULL DEFAULT 0");
}

// ── Thêm cột role nếu chưa có ──
$_col_role = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'role'");
if ($_col_role && mysqli_num_rows($_col_role) === 0) {
    mysqli_query($con, "ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
}

// ── Thêm cột status cho users nếu chưa có (active/banned) ──
$_col_user_status = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'status'");
if ($_col_user_status && mysqli_num_rows($_col_user_status) === 0) {
    mysqli_query($con, "ALTER TABLE users ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active'");
}

// ── Thêm cột status, luot_xem cho stories nếu chưa có ──
$_col_story_status = mysqli_query($con, "SHOW COLUMNS FROM stories LIKE 'status'");
if ($_col_story_status && mysqli_num_rows($_col_story_status) === 0) {
    mysqli_query($con, "ALTER TABLE stories ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'ongoing'");
}
$_col_story_views = mysqli_query($con, "SHOW COLUMNS FROM stories LIKE 'luot_xem'");
if ($_col_story_views && mysqli_num_rows($_col_story_views) === 0) {
    mysqli_query($con, "ALTER TABLE stories ADD COLUMN luot_xem INT NOT NULL DEFAULT 0");
}

// ── Tạo bảng purchased_chapters nếu chưa có ──
mysqli_query($con, "CREATE TABLE IF NOT EXISTS purchased_chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    chapter_id INT NOT NULL,
    coins_spent INT NOT NULL DEFAULT 3,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_purchase (user_id, chapter_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE
) ENGINE=InnoDB");

// ── Tạo bảng coin_transactions nếu chưa có ──
mysqli_query($con, "CREATE TABLE IF NOT EXISTS coin_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount INT NOT NULL,
    vnd_amount INT NOT NULL,
    type ENUM('topup','spend') NOT NULL,
    note VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB");

// ── Tạo bảng topup_orders nếu chưa có ──
mysqli_query($con, "CREATE TABLE IF NOT EXISTS topup_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(32) NOT NULL,
    user_id INT NOT NULL,
    coins INT NOT NULL,
    vnd_amount INT NOT NULL,
    status ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
    chapter_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_order_id (order_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB");

// ── Tạo bảng comments nếu chưa có ──
mysqli_query($con, "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES stories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB");
?>
