<?php
header('Content-Type: application/json');
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    if (!$id) {
        echo json_encode(['status'=>'error','message'=>'Thiếu ID']);
        exit;
    }

    // Lấy cover để xóa file nếu cần
    $res = mysqli_query($con, "SELECT cover FROM stories WHERE id='$id'");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['cover'] && file_exists($row['cover'])) {
        unlink($row['cover']);
    }

    if (mysqli_query($con, "DELETE FROM stories WHERE id='$id'")) {
        echo json_encode(['status'=>'success','message'=>'Xóa truyện thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Lỗi xóa truyện']);
    }
}
?>
