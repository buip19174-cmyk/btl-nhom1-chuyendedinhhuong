<?php
header('Content-Type: application/json');
require_once __DIR__ . '/require_admin.php';
require_admin_api('Bạn không có quyền admin.');
include_once '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['status'=>'error','message'=>'Thiếu ID user']);
        exit;
    }

    if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $id) {
        echo json_encode(['status'=>'error','message'=>'Bạn không thể xóa tài khoản đang đăng nhập']);
        exit;
    }

    $target = mysqli_fetch_assoc(mysqli_query($con, "SELECT id, role FROM users WHERE id=$id LIMIT 1"));
    if (!$target) {
        echo json_encode(['status'=>'error','message'=>'User không tồn tại']);
        exit;
    }

    if (($target['role'] ?? '') === 'admin') {
        $admin_count = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM users WHERE role='admin'"))['c'] ?? 0);
        if ($admin_count <= 1) {
            echo json_encode(['status'=>'error','message'=>'Không thể xóa admin cuối cùng']);
            exit;
        }
    }

    mysqli_begin_transaction($con);
    try {
        $stmt1 = mysqli_prepare($con, "DELETE FROM user_stories WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt1, "i", $id);
        mysqli_stmt_execute($stmt1);

        $stmt2 = mysqli_prepare($con, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $id);
        mysqli_stmt_execute($stmt2);

        if (mysqli_affected_rows($con) === 0) {
            throw new Exception('Không xóa được user');
        }

        mysqli_commit($con);
        echo json_encode(['status'=>'success','message'=>'Xóa người dùng thành công']);
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status'=>'error','message'=>'Lỗi xóa người dùng: ' . $e->getMessage()]);
    }
}
?>
