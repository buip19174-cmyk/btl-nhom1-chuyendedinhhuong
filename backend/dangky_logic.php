<?php
include_once '../database/connect.php';
$register_message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Lấy và làm sạch dữ liệu đầu vào
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- 2. XÁC THỰC DỮ LIỆU ĐẦU VÀO ---
    
    // Kiểm tra mật khẩu ≥6 ký tự
    if(strlen($password) < 6){
        $register_message = "Mật khẩu phải có ít nhất 6 ký tự!";
    } 
    // Kiểm tra số điện thoại đúng 10 chữ số
    else if(!preg_match('/^\d{10}$/', $sdt)){
        $register_message = "Số điện thoại phải gồm đúng 10 chữ số!";
    } 
    else {
        
        $stmt_check = $con->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
        
       
        if (!$stmt_check) {
            die("Lỗi SQL (Prepare check): " . $con->error);
        }

        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $register_message = "Username hoặc email đã tồn tại!";
        } else {
            

           
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            
            $stmt_insert = $con->prepare("INSERT INTO users (username, sdt, email, password) VALUES (?, ?, ?, ?)");
            
            if (!$stmt_insert) {
                die("Lỗi SQL (Prepare insert): " . $con->error);
            }

            $stmt_insert->bind_param("ssss", $username, $sdt, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                $register_message = "Đăng ký thành công!";
                $_POST = array(); 
            } else {
                $register_message = "Lỗi khi đăng ký: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>