<?php
require_once dirname(__DIR__) . '/db_connect.php';

$id = $_POST['id'] ?? '';
$story_id = $_POST['story_id'] ?? '';
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$chapter_number = $_POST['chapter_number'] ?? '';

if ($id == '' || $story_id == '' || $title == '' || $chapter_number == '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu dữ liệu'
    ]);
    exit;
}

$sql = "UPDATE chapters SET
            story_id = '$story_id',
            title = '$title',
            content = '$content',
            chapter_number = '$chapter_number'
        WHERE id = '$id'";

if (mysqli_query($con, $sql)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Cập nhật chương thành công'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi cập nhật chương'
    ]);
}
