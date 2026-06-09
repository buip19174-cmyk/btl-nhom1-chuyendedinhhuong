<?php


if (session_status() === PHP_SESSION_NONE) {

    session_start();

}

require_once __DIR__ . '/../frontend/includes/paths.php';
if (!isset($con)) {
    include_once __DIR__ . '/../database/connect.php';
}

$login_message = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');

    $password = $_POST['password'] ?? '';



    $stmt = $con->prepare("SELECT id, username, `password`, `role`, `status` FROM users WHERE username = ?");

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($user = $result->fetch_assoc()) {

        if (password_verify($password, $user['password'])) {

            if (($user['status'] ?? 'active') === 'banned') {

                $login_message = "Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.";

            } else {

                $_SESSION['user_id'] = $user['id'];

                $_SESSION['username'] = $user['username'];

                $_SESSION['role'] = $user['role'];



                $redirect = trim($_POST['redirect'] ?? '');

                header('Location: ' . app_safe_redirect($redirect));

                exit();

            }
        } else {

            $login_message = "Sai mật khẩu, vui lòng thử lại!";

        }

    } else {

        $login_message = "Tài khoản không tồn tại! Bạn cần đăng ký.";

    }

    $stmt->close();

}

?>
