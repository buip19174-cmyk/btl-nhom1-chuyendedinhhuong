<?php
// API toggle lưu/bỏ lưu truyện — trả về JSON
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit();
}

$user_id  = intval($_SESSION['user_id']);
$story_id = intval($_POST['story_id'] ?? 0);

if (!$story_id) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu story_id']);
    exit();
}

// Kiểm tra đã lưu chưa
$check = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id FROM user_stories WHERE user_id=$user_id AND story_id=$story_id"
));

if ($check) {
    // Đã lưu → bỏ lưu
    mysqli_query($con, "DELETE FROM user_stories WHERE user_id=$user_id AND story_id=$story_id");
    echo json_encode(['status' => 'unsaved', 'message' => 'Đã bỏ lưu truyện']);
} else {
    // Chưa lưu → lưu
    mysqli_query($con, "INSERT INTO user_stories (user_id, story_id) VALUES ($user_id, $story_id)");
    echo json_encode(['status' => 'saved', 'message' => 'Lưu truyện thành công']);
}
