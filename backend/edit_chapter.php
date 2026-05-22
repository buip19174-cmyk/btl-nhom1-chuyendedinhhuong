<?php
include 'connect.php';

// Nhận dữ liệu
$id = $_POST['id'] ?? '';
$story_id = $_POST['story_id'];
$title = mysqli_real_escape_string($con, $_POST['title']);
$content = mysqli_real_escape_string($con, $_POST['content']);
// ===== UPDATE =====
if($id != ""){
    $sql = "UPDATE chapters 
            SET title = '$title',
                content = '$content'
            WHERE id = $id";
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