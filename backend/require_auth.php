<?php
/**
 * Guard đăng nhập + kiểm tra user còn active (không bị banned).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../frontend/includes/paths.php';

function destroy_user_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Bắt buộc đăng nhập và status=active. Redirect về home modal login nếu chưa login/bị ban.
 */
function require_active_user(?string $redirectAfterLogin = null): void
{
    if (!isset($_SESSION['user_id'])) {
        $redirect = $redirectAfterLogin ?? ($_SERVER['REQUEST_URI'] ?? '');
        header('Location: ' . app_login_url($redirect));
        exit();
    }

    global $con;
    if (!isset($con)) {
        include_once __DIR__ . '/../database/connect.php';
    }

    $uid = intval($_SESSION['user_id']);
    $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT status, role, username FROM users WHERE id=$uid LIMIT 1"));

    if (!$row || ($row['status'] ?? '') === 'banned') {
        destroy_user_session();
        header('Location: ' . app_login_url($redirectAfterLogin ?? '') . '&banned=1');
        exit();
    }

    $_SESSION['role'] = $row['role'];
    $_SESSION['username'] = $row['username'];
}
