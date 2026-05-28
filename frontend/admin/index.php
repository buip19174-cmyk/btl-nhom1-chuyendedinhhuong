<?php
require_once '../../backend/require_admin.php';
require_admin();

include_once '../../database/connect.php';
/** @var mysqli $con */

// Cards
$total_users = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0);
$total_stories = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM stories"))['total'] ?? 0);
$total_chapters = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM chapters"))['total'] ?? 0);
$total_comments = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM comments"))['total'] ?? 0);

// Recent users (top 5)
$users_recent = mysqli_query($con, "
  SELECT id, username, email, role, status
  FROM users
  ORDER BY id DESC
  LIMIT 5
");

// Top stories by views (top 5)
$stories_recent = mysqli_query($con, "
  SELECT id, title, status, luot_xem, description
  FROM stories
  ORDER BY luot_xem DESC, id DESC
  LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main -->
  <div class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div>
        <h1>Tổng Quan</h1>
        <br>
        <p>Xin chào, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong></p>
      </div>
    </div>

    <!-- Cards -->
    <div class="cards">
      <div class="card">
        <h3>Tổng Người Dùng</h3>
        <p><?= number_format($total_users) ?></p>
      </div>

      <div class="card">
        <h3>Truyện</h3>
        <p><?= number_format($total_stories) ?></p>
      </div>

      <div class="card">
        <h3>Chương</h3>
        <p><?= number_format($total_chapters) ?></p>
      </div>

      <div class="card">
        <h3>Bình luận</h3>
        <p><?= number_format($total_comments) ?></p>
      </div>
    </div>

    <!-- Recent users -->
    <div class="table-container" style="margin-top:18px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <h3 style="margin:0;">User mới</h3>
        <a href="users.php" style="font-size:13px;">Xem tất cả</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($users_recent && mysqli_num_rows($users_recent) > 0): ?>
            <?php while($u = mysqli_fetch_assoc($users_recent)): ?>
              <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role'] ?? 'user') ?></td>
                <td><?= htmlspecialchars($u['status'] ?? 'active') ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">Chưa có user</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Recent stories -->
    <div class="table-container" style="margin-top:18px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <h3 style="margin:0;">Top 5 truyện được xem nhiều nhất</h3>
        <a href="stories.php" style="font-size:13px;">Xem tất cả</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Trạng thái</th>
            <th>View</th>
            <th>Danh mục</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($stories_recent && mysqli_num_rows($stories_recent) > 0): ?>
            <?php while($s = mysqli_fetch_assoc($stories_recent)): ?>
              <tr>
                <td><?= (int)$s['id'] ?></td>
                <td><?= htmlspecialchars($s['title']) ?></td>
                <td><?= htmlspecialchars($s['status'] ?? 'ongoing') ?></td>
                <td><?= number_format((int)($s['luot_xem'] ?? 0)) ?></td>
                <td><?= htmlspecialchars($s['description'] ?? '') ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">Chưa có truyện</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</body>
</html>