<?php
header('Content-Type: application/json');
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
include_once '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    $status = trim($_POST['status'] ?? 'active');

    if (!$username || !$sdt || !$email || !$password) {
        echo json_encode(['status'=>'error','message'=>'Thiếu dữ liệu']);
        exit;
    }

    // Kiểm tra username/email đã tồn tại chưa
    $check = mysqli_prepare($con, "SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    mysqli_stmt_bind_param($check, "ss", $username, $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if (mysqli_stmt_num_rows($check) > 0) {
        echo json_encode(['status'=>'error','message'=>'Username hoặc email đã tồn tại']);
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (!in_array($role, ['user', 'admin'], true)) $role = 'user';
    if (!in_array($status, ['active', 'banned'], true)) $status = 'active';

    $stmt = mysqli_prepare($con, "INSERT INTO users (username, sdt, email, password, role, status) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $username, $sdt, $email, $password_hash, $role, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>'Thêm người dùng thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi thêm người dùng']);
    }
}
?>
