<?php
if (!function_exists('app_url')) {
    require_once __DIR__ . '/../includes/paths.php';
}
// Lấy tên file hiện tại (ví dụ: index.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
  <div class="sidebar">
    <h2>Trang Quản Trị</h2>

    <ul class="menu">
        <li class="<?php echo ($current_page == 'index.php') ? 'active-menu' : ''; ?>">
            <a href="index.php">Tổng quan</a>
        </li>
        <li class="<?php echo ($current_page == 'users.php') ? 'active-menu' : ''; ?>">
            <a href="users.php">Người dùng</a>
        </li>
        <li class="<?php echo ($current_page == 'stories.php') ? 'active-menu' : ''; ?>">
            <a href="stories.php">Truyện</a>
        </li>
        <li class="<?php echo ($current_page == 'thongke.php') ? 'active-menu' : ''; ?>">
          <a href="thongke.php">Thống kê</a>
        </li>
        <li>
          <a href="<?= htmlspecialchars(app_url('frontend/home.php')) ?>" target="_blank">Xem website</a>
        </li>
        <li>
          <a href="<?= htmlspecialchars(app_url('backend/logout.php')) ?>">Đăng xuất</a>
        </li>
    </ul>
  </div>
