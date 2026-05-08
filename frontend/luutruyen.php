<?php
include 'db_connect.php';

$user_id = 1; // 👈 fix cứng để test
$story_id = (int)$_POST['story_id'];

/* Kiểm tra đã lưu chưa */
$sqlCheck = "SELECT * FROM user_stories WHERE user_id=? AND story_id=?";
$stmt = mysqli_prepare($con, $sqlCheck);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $story_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0){
    $sqlDelete = "DELETE FROM user_stories WHERE user_id=? AND story_id=?";
    $stmt = mysqli_prepare($con, $sqlDelete);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $story_id);
    mysqli_stmt_execute($stmt);

    echo "removed";
}else{
    // ✅ Chưa có → thêm
    $sqlInsert = "INSERT INTO user_stories (user_id, story_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sqlInsert);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $story_id);
    mysqli_stmt_execute($stmt);

    echo "saved";
}