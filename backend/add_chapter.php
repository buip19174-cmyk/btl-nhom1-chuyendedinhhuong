<?php
require 'db_connect.php';

$story_id = $_POST['story_id'] ?? '';
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$chapter_number = $_POST['chapter_number'] ?? '';

if ($story_id == '' || $title == '' || $chapter_number == '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng nhập đầy đủ thông tin'
    ]);
    exit;
}

$sql = "INSERT INTO chapters (story_id, title, content, chapter_number)
        VALUES ('$story_id', '$title', '$content', '$chapter_number')";

if (mysqli_query($con, $sql)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Thêm chương thành công'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi thêm chương'
    ]);
}
