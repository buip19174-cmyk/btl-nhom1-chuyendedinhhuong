<?php
header('Content-Type: application/json');
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status'=>'error','message'=>'Thiếu ID user']);
        exit;
    }

    $stmt = mysqli_prepare($con, "DELETE FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>'Xóa người dùng thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi xóa người dùng']);
    }
}
?>
