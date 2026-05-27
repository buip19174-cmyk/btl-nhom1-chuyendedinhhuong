<?php
header('Content-Type: application/json');
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
include_once '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LẤY DỮ LIỆU TỪ FORM
    $id = intval($_POST['userId'] ?? 0);          // ❗ dùng userId
    $username = trim($_POST['username'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $status = trim($_POST['status'] ?? '');

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

    $stmt_user = mysqli_prepare($con, "SELECT id FROM users WHERE username=? AND id!=?");
    mysqli_stmt_bind_param($stmt_user, "si", $username, $id);
    mysqli_stmt_execute($stmt_user);
    mysqli_stmt_store_result($stmt_user);
    if (mysqli_stmt_num_rows($stmt_user) > 0) {
        echo json_encode(['status'=>'error','message'=>'Username đã tồn tại']);
        exit;
    }

    // Chuẩn hóa role/status (nếu gửi lên)
    if ($role !== '' && !in_array($role, ['user', 'admin'], true)) {
        echo json_encode(['status'=>'error','message'=>'Role không hợp lệ']);
        exit;
    }
    if ($status !== '' && !in_array($status, ['active', 'banned'], true)) {
        echo json_encode(['status'=>'error','message'=>'Status không hợp lệ']);
        exit;
    }

    // Không cho tự khóa chính mình (tránh tự lock admin đang đăng nhập)
    if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $id && $status === 'banned') {
        echo json_encode(['status'=>'error','message'=>'Bạn không thể tự khóa tài khoản đang đăng nhập']);
        exit;
    }

    $current = mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM users WHERE id=$id LIMIT 1"));
    if ($current && ($current['role'] ?? '') === 'admin' && $role === 'user') {
        $admin_count = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM users WHERE role='admin'"))['c'] ?? 0);
        if ($admin_count <= 1) {
            echo json_encode(['status'=>'error','message'=>'Không thể hạ quyền admin cuối cùng']);
            exit;
        }
    }

    // Nếu có nhập password mới → hash và cập nhật
    if ($password !== '') {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($con,
            "UPDATE users SET username=?, sdt=?, email=?, password=?, role=COALESCE(NULLIF(?,''), role), status=COALESCE(NULLIF(?,''), status) WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $username, $sdt, $email, $password_hash, $role, $status, $id);
    } else {
        // Nếu không nhập password → giữ password cũ
        $stmt = mysqli_prepare($con,
            "UPDATE users SET username=?, sdt=?, email=?, role=COALESCE(NULLIF(?,''), role), status=COALESCE(NULLIF(?,''), status) WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $sdt, $email, $role, $status, $id);
    }

    // Thực thi
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>'Cập nhật người dùng thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi cập nhật người dùng']);
    }
}
?>
