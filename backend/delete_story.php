<?php
header('Content-Type: application/json');
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
include_once '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status'=>'error','message'=>'Thiếu ID']);
        exit;
    }

    $res = mysqli_query($con, "SELECT cover FROM stories WHERE id=$id LIMIT 1");
    $row = mysqli_fetch_assoc($res);
    if (!$row) {
        echo json_encode(['status'=>'error','message'=>'Truyện không tồn tại']);
        exit;
    }

    mysqli_begin_transaction($con);
    try {
        $stmt1 = mysqli_prepare($con, "DELETE FROM chapters WHERE story_id = ?");
        mysqli_stmt_bind_param($stmt1, "i", $id);
        mysqli_stmt_execute($stmt1);

        $stmt2 = mysqli_prepare($con, "DELETE FROM comments WHERE story_id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $id);
        mysqli_stmt_execute($stmt2);

        $stmt3 = mysqli_prepare($con, "DELETE FROM user_stories WHERE story_id = ?");
        mysqli_stmt_bind_param($stmt3, "i", $id);
        mysqli_stmt_execute($stmt3);

        $stmt4 = mysqli_prepare($con, "DELETE FROM stories WHERE id = ?");
        mysqli_stmt_bind_param($stmt4, "i", $id);
        mysqli_stmt_execute($stmt4);

        if (mysqli_affected_rows($con) === 0) {
            throw new Exception('Không xóa được truyện');
        }

        mysqli_commit($con);

        if (!empty($row['cover']) && file_exists($row['cover'])) {
            @unlink($row['cover']);
        }

        echo json_encode(['status'=>'success','message'=>'Xóa truyện thành công']);
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status'=>'error','message'=>'Lỗi xóa truyện']);
    }
}
?>
