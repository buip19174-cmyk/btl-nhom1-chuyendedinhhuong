<?php

$toast_codes = [
    'saved'     => ['msg' => 'Lưu truyện thành công!', 'error' => false],
    'exists'    => ['msg' => 'Truyện đã có trong tủ sách!', 'error' => false],
    'unsaved'   => ['msg' => 'Đã bỏ lưu truyện.', 'error' => false],
    'not_saved' => ['msg' => 'Truyện không có trong tủ sách.', 'error' => true],
    'removed'   => ['msg' => 'Đã xóa truyện khỏi tủ sách.', 'error' => false],
];
?>
<div id="site-toast" style="display:none;position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;border-radius:8px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.5);max-width:320px;font-size:14px"></div>
<script>
(function() {
    window.showToast = function(msg, isError) {
        var t = document.getElementById('site-toast');
        if (!t) return;
        t.textContent = msg;
        t.style.background = isError ? '#e74c3c' : '#1ed760';
        t.style.color = isError ? '#fff' : '#000';
        t.style.display = 'block';
        setTimeout(function() { t.style.display = 'none'; }, 4000);
    };
    <?php if (!empty($register_message ?? '')): ?>
    showToast(<?= json_encode($register_message, JSON_UNESCAPED_UNICODE) ?>, <?= strpos(mb_strtolower($register_message), 'thành công') !== false ? 'false' : 'true' ?>);
    <?php endif; ?>
    <?php
    $login_msg = $login_message ?? ($message ?? '');
    if (!empty($login_msg)):
    ?>
    showToast(<?= json_encode($login_msg, JSON_UNESCAPED_UNICODE) ?>, <?= strpos(mb_strtolower($login_msg), 'thành công') !== false ? 'false' : 'true' ?>);
    <?php endif; ?>
    <?php if (isset($_GET['banned']) && $_GET['banned'] === '1'): ?>
    showToast('Tài khoản đã bị khóa.', true);
    <?php endif; ?>
    <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
    showToast('Đăng nhập thành công!', false);
    <?php endif; ?>
    <?php if (isset($_GET['removed']) && $_GET['removed'] === '1'): ?>
    showToast('Đã xóa truyện khỏi tủ sách.', false);
    <?php endif; ?>
    <?php
    $tc = $_GET['toast'] ?? '';
    if ($tc !== '' && isset($toast_codes[$tc])):
        $ti = $toast_codes[$tc];
    ?>
    showToast(<?= json_encode($ti['msg'], JSON_UNESCAPED_UNICODE) ?>, <?= $ti['error'] ? 'true' : 'false' ?>);
    <?php endif; ?>
})();
</script>
