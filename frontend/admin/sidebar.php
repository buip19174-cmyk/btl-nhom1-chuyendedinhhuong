
<?php
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
        <li class="<?php echo ($current_page == 'setting.php') ? 'active-menu' : ''; ?>">
          <a href="stories.php">Cài đặt</a>
        </li>
        </ul>
    </ul> 
  </div>
