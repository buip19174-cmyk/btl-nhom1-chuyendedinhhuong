<?php
header('Content-Type: application/json');
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
include_once '../database/connect.php'; // file connect.php chứa $con

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['storyId'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $cover_old = trim($_POST['cover_old'] ?? '');

    if (!$id || !$title) {
        echo json_encode(['status'=>'error','message'=>'Thiếu dữ liệu']);
        exit;
    }

    // Handle cover upload
    $cover_path = $cover_old; // mặc định giữ cover cũ
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time().'_'.basename($_FILES['cover']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetFile)) {
            $cover_path = $targetFile;
        } else {
            echo json_encode(['status'=>'error','message'=>'Upload cover thất bại']);
            exit;
        }
    }

    // Update story
    // Update story
    $stmt = mysqli_prepare($con, "UPDATE stories SET title=?, `description`=?, cover=?, `status`=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $cover_path, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>'Cập nhật truyện thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi cập nhật truyện']);
    }
}
?>
