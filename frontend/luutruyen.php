<?php
session_start();
include_once '../database/connect.php';

/* Chưa đăng nhập → quay về home */
if (!isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

/* Thiếu story_id */
if (!isset($_POST['story_id'])) {
    die("Thiếu ID truyện");
}

$story_id = (int)$_POST['story_id'];
$username = $_SESSION['username'];

/* Lấy user_id */
$sqlUser = "SELECT id FROM users WHERE username = ?";
$stmtUser = mysqli_prepare($con, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "s", $username);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $user_id);
mysqli_stmt_fetch($stmtUser);
mysqli_stmt_close($stmtUser);

if (!$user_id) {
    die("User không tồn tại");
}

/* Lưu truyện – không trùng */
$sqlSave = "
    INSERT INTO user_stories (user_id, story_id)
    SELECT ?, ?
    WHERE NOT EXISTS (
        SELECT 1 FROM user_stories WHERE user_id = ? AND story_id = ?
    )
";
$stmtSave = mysqli_prepare($con, $sqlSave);
mysqli_stmt_bind_param($stmtSave, "iiii", $user_id, $story_id, $user_id, $story_id);
mysqli_stmt_execute($stmtSave);
$affected = mysqli_stmt_affected_rows($stmtSave);
mysqli_stmt_close($stmtSave);

// Quay lại trang trước + thông báo
$referer = $_SERVER['HTTP_REFERER'] ?? 'home.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lưu truyện</title>
    <script>
        alert("<?= $affected > 0 ? 'Lưu truyện thành công!' : 'Truyện đã có trong tủ sách!' ?>");
        window.location.href = "<?= htmlspecialchars($referer) ?>";
    </script>
</head>
<body></body>
</html>