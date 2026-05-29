<?php
// dangnhap_logic.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Tìm người dùng trong database theo Username
    $stmt = $con->prepare("SELECT id, username, `password`, `role` FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // 2. Kiểm tra mật khẩu (Sử dụng password_verify cho mật khẩu đã mã hóa)
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $message = "Đăng nhập thành công! Chào mừng " . $user['username'];

            // 2. Logic phân quyền và điều hướng
            if ($user['role'] === 'admin') {
                header("Location: ../frontend/admin/index.php");
                exit();
            } else {
                $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '';

if (!empty($redirect)) {
    header("Location: " . $redirect);
} else {
    header("Location: ../frontend/home.php?login=success");
}
exit();
            }

        } else {
            $message = "Sai mật khẩu, vui lòng thử lại!";
        }
    } else {
        $message = "Tài khoản không tồn tại! Bạn cần đăng ký.";
    }
    $stmt->close();
}
?>
