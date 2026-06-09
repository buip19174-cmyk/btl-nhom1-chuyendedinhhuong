<?php
session_start();
include_once '../database/connect.php';
require_once __DIR__ . '/includes/paths.php';
require_once __DIR__ . '/../backend/require_auth.php';
require_active_user();
/** @var mysqli $con */

if (($_SESSION['role'] ?? '') === 'admin') {
    header('Location: home.php');
    exit();
}

if (!isset($_POST['story_id'])) {
    die('Thiếu ID truyện');
}

$story_id = (int) $_POST['story_id'];
$action   = ($_POST['action'] ?? 'save') === 'unsave' ? 'unsave' : 'save';
$username = $_SESSION['username'];

$stmtUser = mysqli_prepare($con, 'SELECT id FROM users WHERE username = ?');
mysqli_stmt_bind_param($stmtUser, 's', $username);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $user_id);
mysqli_stmt_fetch($stmtUser);
mysqli_stmt_close($stmtUser);

if (!$user_id) {
    die('User không tồn tại');
}

$affected = 0;

if ($action === 'unsave') {
    $stmt = mysqli_prepare($con, 'DELETE FROM user_stories WHERE user_id = ? AND story_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $story_id);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    $toast = $affected > 0 ? 'unsaved' : 'not_saved';
} else {
    $sqlSave = '
        INSERT INTO user_stories (user_id, story_id)
        SELECT ?, ?
        WHERE NOT EXISTS (
            SELECT 1 FROM user_stories WHERE user_id = ? AND story_id = ?
        )
    ';
    $stmtSave = mysqli_prepare($con, $sqlSave);
    mysqli_stmt_bind_param($stmtSave, 'iiii', $user_id, $story_id, $user_id, $story_id);
    mysqli_stmt_execute($stmtSave);
    $affected = mysqli_stmt_affected_rows($stmtSave);
    mysqli_stmt_close($stmtSave);
    $toast = $affected > 0 ? 'saved' : 'exists';
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
if ($referer === '' || !preg_match('#/frontend/#i', $referer)) {
    $referer = app_url('frontend/home.php');
}

app_redirect_with_toast($referer, $toast);
