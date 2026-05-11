<?php
include 'connect.php';

// Nhận dữ liệu từ AJAX
$story_id = $_POST['story_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$content = mysqli_real_escape_string($conn, $_POST['content']);

// Tự động tính số chương tiếp theo (chapter_number)
$result = mysqli_query($conn, "SELECT MAX(chapter_number) as max_num FROM chapters WHERE story_id = $story_id");
$row = mysqli_fetch_assoc($result);
$next_number = $row['max_num'] + 1;

$sql = "INSERT INTO chapters (story_id, chapter_number, title, content) 
        VALUES ('$story_id', '$next_number', '$title', '$content')";

if (mysqli_query($conn, $sql)) {
    echo "Thành công!";
} else {
    echo "Lỗi: " . mysqli_error($conn);
}
?>