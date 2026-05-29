<?php
/**
 * Tính base path của project trên localhost (vd: /btl-nhom1-chuyendedinhhuong).
 */
function app_project_base(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (preg_match('#^(.*)/frontend(?:/|$)#', $script, $m)) {
        return $m[1];
    }
    if (preg_match('#^(.*)/backend(?:/|$)#', $script, $m)) {
        return $m[1];
    }
    return '';
}

/** URL tuyệt đối từ root project, vd app_url('frontend/home.php') */
function app_url(string $relativeFromProjectRoot): string
{
    $base = app_project_base();
    $path = '/' . trim(str_replace('\\', '/', $relativeFromProjectRoot), '/');
    return $base . $path;
}

/** URL trang chủ kèm mở modal đăng nhập */
function app_login_url(?string $redirect = null): string
{
    $url = app_url('frontend/home.php') . '?open=login';
    if ($redirect !== null && $redirect !== '') {
        $url .= '&redirect=' . urlencode($redirect);
    }
    return $url;
}

/**
 * Trả về URL ảnh bìa sách.
 * - Nếu cover bắt đầu bằng "uploads/" → file do admin upload → trong backend/uploads/
 * - Ngược lại → ảnh tĩnh trong code/images/
 */
function cover_url(string $cover): string
{
    if ($cover === '') return '';
    if (preg_match('#^https?://#i', $cover)) return $cover;
    if (strpos($cover, 'uploads/') === 0) {
        return app_url('backend/' . $cover);
    }
    return app_url('code/images/' . $cover);
}

/** Chỉ cho phép redirect nội bộ sau đăng nhập */
function app_safe_redirect(?string $target): string
{
    $target = trim((string)$target);
    if ($target === '') {
        return app_url('frontend/home.php') . '?login=success';
    }
    if (preg_match('#^(javascript|data):#i', $target)) {
        return app_url('frontend/home.php') . '?login=success';
    }
    if ($target[0] === '/') {
        return $target;
    }
    if (preg_match('#/(frontend|backend)/#', $target)) {
        return $target;
    }
    return app_url('frontend/home.php') . '?login=success';
}
