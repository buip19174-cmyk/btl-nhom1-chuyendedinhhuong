<?php
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
require_once '../database/connect.php';

header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu ID chương'
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "DELETE FROM chapters WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
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
