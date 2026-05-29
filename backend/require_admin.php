<?php
// require_admin.php — Guard cho khu vực admin (pages + API).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../frontend/includes/paths.php';

function admin_verify_active(): bool
{
    if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        return false;
    }
    global $con;
    if (!isset($con)) {
        include_once __DIR__ . '/../database/connect.php';
    }
    $uid = intval($_SESSION['user_id']);
    $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT status, role FROM users WHERE id=$uid LIMIT 1"));
    if (!$row || ($row['status'] ?? '') !== 'active' || ($row['role'] ?? '') !== 'admin') {
        require_once __DIR__ . '/require_auth.php';
        destroy_user_session();
        return false;
    }
    return true;
}

/**
 * Chặn truy cập nếu không phải admin.
 */
function require_admin(string $redirectTo = ''): void
{
    if (admin_verify_active()) {
        return;
    }

    if ($redirectTo === '') {
        $redirectTo = app_url('frontend/home.php') . '?need_admin=1';
    }

    header('Location: ' . $redirectTo);
    exit();
}

/**
 * Guard cho API: trả JSON lỗi thay vì redirect.
 */
function require_admin_api(string $message = 'Forbidden'): void
{
    if (admin_verify_active()) {
        return;
    }

    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit();
}
