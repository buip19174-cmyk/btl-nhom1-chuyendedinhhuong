<?php
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
include_once '../database/connect.php';

// Nhận dữ liệu
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$story_id = isset($_POST['story_id']) ? intval($_POST['story_id']) : 0;
$title_raw = trim($_POST['title'] ?? '');
$content_raw = trim($_POST['content'] ?? '');

if ($story_id <= 0) {
    echo "Thiếu story_id hợp lệ!";
    exit;
}
if ($title_raw === '') {
    echo "Vui lòng nhập tiêu đề chapter!";
    exit;
}
if (mb_strlen(strip_tags($content_raw)) < 10) {
    echo "Nội dung quá ngắn (tối thiểu 10 ký tự)!";
    exit;
}

$title = mysqli_real_escape_string($con, $title_raw);
$content = mysqli_real_escape_string($con, $content_raw);
// ===== UPDATE =====
if($id > 0){
    $sql = "UPDATE chapters 
            SET title = '$title',
                content = '$content'
            WHERE id = $id AND story_id = $story_id";
    if(mysqli_query($con, $sql)){
        echo "Cập nhật thành công!";
    }else{
        echo "Lỗi: " . mysqli_error($con);
    }
}else{
    // ===== INSERT =====
    // Tự động tính chapter_number tiếp theo
    $result = mysqli_query(
        $con,
        "SELECT MAX(chapter_number) as max_num 
         FROM chapters 
         WHERE story_id = $story_id"
    );
    $row = mysqli_fetch_assoc($result);
    $next_number = ($row['max_num'] ?? 0) + 1;
    $sql = "INSERT INTO chapters
            (story_id, chapter_number, title, content)
            VALUES
            ('$story_id', '$next_number', '$title', '$content')";
    if(mysqli_query($con, $sql)){
        echo "Thêm chương thành công!";
    }else{
        echo "Lỗi: " . mysqli_error($con);
    }
}
?>