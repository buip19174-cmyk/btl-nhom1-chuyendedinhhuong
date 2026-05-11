<?php
require 'connect.php';

$id = $_POST['id'] ?? '';

if ($id == '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu ID chương'
    ]);
    exit;
}

$sql = "DELETE FROM chapters WHERE id = '$id'";

if (mysqli_query($con, $sql)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Xóa chương thành công'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi xóa chương'
    ]);
}
