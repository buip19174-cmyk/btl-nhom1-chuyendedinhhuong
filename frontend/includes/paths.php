<?php

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

/** Loại bỏ tham số toast khỏi URL redirect */
function app_strip_toast_param(string $url): string
{
    $url = preg_replace('/([?&])toast=[^&]*(&|$)/', '$1', $url);
    $url = rtrim(preg_replace('/\?&/', '?', $url), '?&');
    return $url;
}

/** Redirect kèm mã toast (saved, exists, unsaved, not_saved) */
function app_redirect_with_toast(string $url, string $toastCode): void
{
    $url = app_strip_toast_param($url);
    $sep = (strpos($url, '?') !== false) ? '&' : '?';
    header('Location: ' . $url . $sep . 'toast=' . rawurlencode($toastCode));
    exit();
}
