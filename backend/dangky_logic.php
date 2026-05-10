<?php
// dangky_logic.php

// 1. Phải nạp file kết nối ngay tại đây để biến $con có hiệu lực
include_once '../database/connect.php';
$message = ''; // Khởi tạo biến message để tránh lỗi undefined

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy và làm sạch dữ liệu đầu vào
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- 2. XÁC THỰC DỮ LIỆU ĐẦU VÀO ---
    
    // Kiểm tra mật khẩu ≥6 ký tự
    if(strlen($password) < 6){
        $message = "Mật khẩu phải có ít nhất 6 ký tự!";
    } 
    // Kiểm tra số điện thoại đúng 10 chữ số
    else if(!preg_match('/^\d{10}$/', $sdt)){
        $message = "Số điện thoại phải gồm đúng 10 chữ số!";
    } 
    else {
        // --- 3. KIỂM TRA TỒN TẠI (DÙNG PREPARED STATEMENTS) ---

        // Chuẩn bị câu lệnh SQL
        $stmt_check = $con->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
        
        // Kiểm tra xem prepare có thành công không (nếu sai tên cột sẽ lỗi ở đây)
        if (!$stmt_check) {
            die("Lỗi SQL (Prepare check): " . $con->error);
        }

        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $message = "Username hoặc email đã tồn tại!";
        } else {
            // --- 4. THỰC HIỆN ĐĂNG KÝ ---

            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Chuẩn bị câu lệnh SQL để chèn dữ liệu
            $stmt_insert = $con->prepare("INSERT INTO users (username, sdt, email, password) VALUES (?, ?, ?, ?)");
            
            if (!$stmt_insert) {
                die("Lỗi SQL (Prepare insert): " . $con->error);
            }

            $stmt_insert->bind_param("ssss", $username, $sdt, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                $message = "Đăng ký thành công! Bạn có thể đăng nhập.";
                $_POST = array(); 
            } else {
                $message = "Lỗi khi đăng ký: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>