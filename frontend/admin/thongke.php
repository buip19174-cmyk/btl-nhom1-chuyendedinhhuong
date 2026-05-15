<?php
// Giả sử bạn có mảng dữ liệu lượt xem theo ngày
$views_data = [180, 160, 120, 130, 90, 110, 70, 40]; 
$points = "";
$x = 0;
foreach($views_data as $v) {
    $points .= $x . "," . $v . " ";
    $x += 80;
}
?>
<?php
// Giả sử bạn truy vấn từ database để lấy số lượng truyện theo thể loại
// $sql_action = "SELECT COUNT(*) as total FROM stories WHERE the_loai = 'Hành động'";
$action_count = 120; // Dữ liệu giả lập
$romance_count = 85;
$comedy_count = 45;
$total_stories = 250; // Tổng số truyện

// Tính %
$action_pct = ($action_count / $total_stories) * 100;
$romance_pct = ($romance_count / $total_stories) * 100;
$comedy_pct = ($comedy_count / $total_stories) * 100;
?>

<?php 
include '../../backend/connect.php'; 

// 1. Lấy tổng số người dùng
$sql_users = "SELECT COUNT(*) as total FROM users";
$res_users = $con->query($sql_users);
$total_users = $res_users->fetch_assoc()['total'];

// 2. Lấy tổng số truyện
$sql_stories = "SELECT COUNT(*) as total FROM stories";
$res_stories = $con->query($sql_stories);
$total_stories = $res_stories->fetch_assoc()['total'];

// 3. Lấy danh sách truyện nổi bật (Top 4 truyện nhiều view nhất)
$sql_top_stories = "SELECT title, luot_xem, status FROM stories ORDER BY luot_xem DESC LIMIT 4";
$res_top_stories = $con->query($sql_top_stories);
// 4. Lấy tổng số truyện
$sql_views = "SELECT SUM(luot_xem) AS total FROM stories;";
$res_views = $con->query($sql_views);
$total_views = $res_views->fetch_assoc()['total'];
// 5. Giả sử lấy % người dùng hoạt động (logic tùy bạn thiết kế)
// Ví dụ: (số người login trong 30 ngày / tổng số người) * 100
$active_percent = 72; // Bạn có thể viết SQL để tính con số này
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trang Thống Kê</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div>
            <h1>Thống Kê</h1>
            <p>Tổng quan hoạt động hệ thống</p>
        </div>
        <input class="search" type="text" placeholder="Tìm kiếm...">
    </div>

    <!-- Cards -->
    <div class="cards">
        <div class="card">
            <h3>Người dùng</h3>
            <p><?php echo number_format($total_users); ?></p>
        </div>
        <div class="card">
            <h3>Truyện</h3>
            <p><?php echo number_format($total_stories); ?></p>
        </div>
        <div class="card">
            <h3>Lượt xem</h3>
            <p><?php echo number_format($total_views); ?></p>
        </div>
        <div class="card">
            <h3>Bình luận</h3>
            <p>Chưa có</p>
        </div>
    </div>

    <!-- Grid -->
    <div class="grid">
        <!-- LEFT -->
        <div>
            <!-- Line Chart -->
            <div class="box">
                <div class="title">Lượt truy cập 7 ngày gần đây</div>
                <svg class="line-chart" viewBox="0 0 600 260">
                    <!-- Grid Lines -->
                    <line x1="0" y1="50" x2="600" y2="50" stroke="#e5e7eb" stroke-dasharray="4"/>
                    <line x1="0" y1="120" x2="600" y2="120" stroke="#e5e7eb" stroke-dasharray="4"/>
                    <line x1="0" y1="190" x2="600" y2="190" stroke="#e5e7eb" stroke-dasharray="4"/>

                    <!-- Area Fill -->
                    <path d="M0 180 L80 160 L160 120 L240 130 L320 90 L400 110 L480 70 L560 40 L560 260 L0 260 Z" 
                          fill="rgba(139,92,246,0.1)"/>

                    <!-- Main Line -->
                    <polyline points="<?php echo trim($points); ?>" ... />
                    <!-- Data Points -->
                    <circle cx="80" cy="160" r="4" fill="#8b5cf6" />
                    <circle cx="160" cy="120" r="4" fill="#8b5cf6" />
                    <circle cx="240" cy="130" r="4" fill="#8b5cf6" />
                    <circle cx="320" cy="90" r="4" fill="#8b5cf6" />
                    <circle cx="400" cy="110" r="4" fill="#8b5cf6" />
                    <circle cx="480" cy="70" r="4" fill="#8b5cf6" />
                    <circle cx="560" cy="40" r="4" fill="#8b5cf6" />
                </svg>
            </div>

            <!-- Table -->
            <div class="box" style="margin-top:20px;">
                <div class="title">Truyện nổi bật</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tên truyện</th>
                            <th>Lượt xem</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_top_stories->num_rows > 0): ?>
                            <?php while($row = $res_top_stories->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['title']; ?></td>
                                    <td><?php echo number_format($row['luot_xem']); ?></td>
                                    <td>
                                        <?php 
                                            $status_class = 'normal';
                                            if($row['status'] == 'Hot') $status_class = 'hot';
                                            if($row['status'] == 'Tăng') $status_class = 'good';
                                        ?>
                                        <span class="status <?php echo $status_class; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">Không có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT -->
        <div>
            <!-- Donut -->
            <div class="box">
                <div class="title">Người dùng hoạt động</div>
                <div class="donut-wrap">
                    <div class="donut" style="background: conic-gradient(var(--primary) 0% <?php echo $active_percent; ?>%, #e5e7eb <?php echo $active_percent; ?>% 100%);">
                        <div class="donut-text"><?php echo $active_percent; ?>%</div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:12px; height:12px; background:var(--primary); border-radius:3px;"></div>
                                <span>Hoạt động</span>
                            </div>
                            <span>72%</span>
                        </div>
                        <div class="legend-item">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:12px; height:12px; background:#e5e7eb; border-radius:3px;"></div>
                                <span>Không hoạt động</span>
                            </div>
                            <span>28%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div class="box" style="margin-top:20px;">
                <div class="title">Cơ cấu thể loại</div>
                
                <div class="progress-item">
                    <div class="progress-top">
                        <span>Hành động</span>
                        <span><?php echo $action_count; ?> truyện</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $action_pct; ?>%; background: var(--primary);"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-top">
                        <span>Tình cảm</span>
                        <span><?php echo $romance_count; ?> truyện</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $romance_pct; ?>%; background: var(--blue);"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-top">
                        <span>Hài hước</span>
                        <span><?php echo $comedy_count; ?> truyện</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $comedy_pct; ?>%; background: var(--green);"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-top">
                        <span>Kinh dị</span>
                        <span>15 truyện</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 10%; background: var(--orange);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>