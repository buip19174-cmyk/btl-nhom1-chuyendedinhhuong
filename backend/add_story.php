<?php
header('Content-Type: application/json');
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['storyId'] ?? ''; // Nếu có thì là sửa
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cover = '';

    if (!$title) {
        echo json_encode(['status'=>'error','message'=>'Tên truyện không được để trống']);
        exit;
    }

    // Xử lý cover upload nếu có
    if (isset($_FILES['cover']) && $_FILES['cover']['name'] != '') {
        $targetDir = "uploads/";
        if(!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time().'_'.basename($_FILES['cover']['name']);
        $targetFile = $targetDir.$filename;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetFile)) {
            $cover = $targetFile;
        } else {
            echo json_encode(['status'=>'error','message'=>'Upload cover thất bại']);
            exit;
        }
    } elseif (isset($_POST['cover_old'])) {
        $cover = $_POST['cover_old']; // giữ cover cũ nếu sửa
    }

    if ($id) {
        // Update
        $stmt = mysqli_prepare($con, "UPDATE stories SET title=?, description=?, cover=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $cover, $id);
        $msg = 'Cập nhật truyện thành công';
    } else {
        // Insert mới
        $stmt = mysqli_prepare($con, "INSERT INTO stories (title, description, cover) VALUES (?,?,?)");
        mysqli_stmt_bind_param($stmt, "sss", $title, $description, $cover);
        $msg = 'Thêm truyện thành công';
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status'=>'success','message'=>$msg]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi lưu truyện']);
    }
}
?>
