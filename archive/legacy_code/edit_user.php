<?php
header('Content-Type: application/json');
include('db_connect.php'); // Hoặc include('connect.php') nếu bạn dùng biến $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LẤY DỮ LIỆU TỪ FORM
    $id = intval($_POST['userId'] ?? 0);          // ❗ dùng userId
    $username = trim($_POST['username'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Kiểm tra dữ liệu bắt buộc
    if (!$id || !$username || !$sdt || !$email) {
        echo json_encode(['status'=>'error','message'=>'Thiếu dữ liệu (Tên, SĐT, Email)']);
        exit;
    }

    // Kiểm tra email đã tồn tại với user khác
    $stmt_check = mysqli_prepare($con, "SELECT id FROM users WHERE email=? AND id!=?");
    mysqli_stmt_bind_param($stmt_check, "si", $email, $id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        echo json_encode(['status'=>'error','message'=>'Email đã tồn tại']);
        exit;
    }

    // Nếu có nhập password mới → hash và cập nhật
    if ($password !== '') {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($con, 
            "UPDATE users SET username=?, sdt=?, email=?, password=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $sdt, $email, $password_hash, $id);
    } else {
        // Nếu không nhập password → giữ password cũ
        $stmt = mysqli_prepare($con, 
            "UPDATE users SET username=?, sdt=?, email=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $username, $sdt, $email, $id);
    }

    // Thực thi
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>'Cập nhật người dùng thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi cập nhật người dùng']);
    }
}
?>
