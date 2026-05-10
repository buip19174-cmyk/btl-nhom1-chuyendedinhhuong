<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$sdt || !$email || !$password) {
        echo json_encode(['status'=>'error','message'=>'Thiếu dữ liệu']);
        exit;
    }

    // Kiểm tra email đã tồn tại chưa
    $check = mysqli_prepare($con, "SELECT id FROM users WHERE email=?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if (mysqli_stmt_num_rows($check) > 0) {
        echo json_encode(['status'=>'error','message'=>'Email đã tồn tại']);
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($con, "INSERT INTO users (username, sdt, email, password) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $sdt, $email, $password_hash);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>'Thêm người dùng thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi thêm người dùng']);
    }
}
?>
