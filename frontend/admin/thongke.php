<?php
require_once '../../backend/require_admin.php';
require_admin();
include_once '../../database/connect.php';
/** @var mysqli $con */

function get_count(mysqli $con, string $query): int {
    $res = mysqli_query($con, $query);
    return $res ? (int)(mysqli_fetch_assoc($res)['total'] ?? 0) : 0;
}

// Lấy tổng số lượng người dùng
$total_users = get_count($con, "SELECT COUNT(*) AS total FROM users");

// Lấy tổng số lượng truyện trên hệ thống
$total_stories = get_count($con, "SELECT COUNT(*) AS total FROM stories");

// Lấy tổng lượt xem của tất cả các truyện
$total_views = get_count($con, "SELECT SUM(luot_xem) AS total FROM stories");

// Lấy tổng số lượng bình luận
$total_comments = get_count($con, "SELECT COUNT(*) AS total FROM comments");

// Lấy tổng doanh thu nạp tiền từ trước đến nay
$total_revenue = get_count(
    $con,
    "SELECT SUM(vnd_amount) AS total 
     FROM coin_transactions 
     WHERE type = 'topup'"
);

// Lấy tổng số lượng xu người dùng đã tiêu
$total_coins_spent = get_count(
    $con,
    "SELECT SUM(coins_spent) AS total 
     FROM purchased_chapters"
);

// Lấy tổng số lượt mở khóa chương truyện
$total_chapter_unlocks = get_count(
    $con,
    "SELECT COUNT(*) AS total 
     FROM purchased_chapters"
);

// Xác định bộ lọc thời gian doanh thu (mặc định là 7 ngày)
$filter = $_GET['filter'] ?? '7_days';
$custom_start = $_GET['custom_start'] ?? '';
$custom_end = $_GET['custom_end'] ?? '';
$valid_filters = ['7_days', 'this_month', 'this_year', 'custom'];
if (!in_array($filter, $valid_filters, true)) {
    $filter = '7_days';
}

function thongke_valid_date(string $date): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt && $dt->format('Y-m-d') === $date;
}

// Cấu hình khoảng thời gian và định dạng SQL/Hiển thị dựa theo bộ lọc
switch ($filter) {
    case 'this_month':
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $interval_format = 'Y-m-d';
        $display_format = 'd/m';
        break;
    case 'this_year':
        $start_date = date('Y-01-01');
        $end_date = date('Y-12-31');
        $interval_format = 'Y-m';
        $display_format = 'm/Y';
        break;
    case 'custom':
        if (thongke_valid_date($custom_start) && thongke_valid_date($custom_end)) {
            $start_date = $custom_start;
            $end_date = $custom_end;
            if ($start_date > $end_date) {
                [$start_date, $end_date] = [$end_date, $start_date];
                [$custom_start, $custom_end] = [$start_date, $end_date];
            }
        } else {
            $start_date = date('Y-m-d', strtotime('-6 days'));
            $end_date = date('Y-m-d');
            $filter = '7_days';
            $custom_start = '';
            $custom_end = '';
        }
        $interval_format = 'Y-m-d';
        $display_format = 'd/m';
        break;
    case '7_days':
    default:
        $start_date = date('Y-m-d', strtotime('-6 days'));
        $end_date = date('Y-m-d');
        $interval_format = 'Y-m-d';
        $display_format = 'd/m';
        break;
}

// Nếu khoảng cách ngày ở chế độ custom > 366 ngày, tự động chuyển sang gom nhóm theo tháng để tránh quá tải biểu đồ
$diff_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
if ($filter === 'custom' && $diff_days > 366) {
    $group_by_sql = "DATE_FORMAT(created_at, '%Y-%m')";
    $interval_format = 'Y-m';
    $display_format = 'm/Y';
} else {
    $group_by_sql = ($filter === 'this_year') ? "DATE_FORMAT(created_at, '%Y-%m')" : "DATE(created_at)";
}

// Truy vấn lấy dữ liệu doanh thu thực tế từ cơ sở dữ liệu
$query_revenue = "
    SELECT 
        $group_by_sql AS revenue_period,
        SUM(vnd_amount) AS total_revenue
    FROM coin_transactions
    WHERE type = 'topup'
      AND created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
    GROUP BY $group_by_sql
";
$res_revenue_chart = mysqli_query($con, $query_revenue);

// Đổ dữ liệu doanh thu thực tế vào mảng tạm để đối chiếu
$real_data = [];
if ($res_revenue_chart) {
    while ($row = mysqli_fetch_assoc($res_revenue_chart)) {
        $real_data[$row['revenue_period']] = (int)$row['total_revenue'];
    }
}

// Tạo trục thời gian đầy đủ để điền giá trị 0 cho những ngày không có doanh thu
$revenue_chart = [];
$period_start = new DateTime($start_date);
$period_end = new DateTime($end_date);
$period_end->modify('+1 day');
$interval_step = ($filter === 'this_year' || ($filter === 'custom' && $diff_days > 366)) ? new DateInterval('P1M') : new DateInterval('P1D');
$period = new DatePeriod($period_start, $interval_step, $period_end);

foreach ($period as $date) {
    $key = $date->format($interval_format);
    $label = $date->format($display_format);
    $revenue_chart[] = [
        'label' => $label,
        'total' => $real_data[$key] ?? 0
    ];
}

// Lấy danh sách Top 5 truyện mang lại doanh thu (tiêu xu) cao nhất
$top_revenue_stories = [];
$res_rev_stories = mysqli_query($con, "
    SELECT s.title, s.status, SUM(pc.coins_spent) AS total_coins
    FROM purchased_chapters pc
    JOIN chapters c ON pc.chapter_id = c.id
    JOIN stories s ON c.story_id = s.id
    GROUP BY s.id
    ORDER BY total_coins DESC
    LIMIT 5
");
if ($res_rev_stories) {
    while ($row = mysqli_fetch_assoc($res_rev_stories)) {
        $top_revenue_stories[] = $row;
    }
}

// Lấy danh sách Top 5 người dùng nạp nhiều tiền nhất hệ thống
$top_spenders = [];
$res_spenders = mysqli_query($con, "
    SELECT u.id, u.username, u.email, SUM(ct.vnd_amount) AS total_deposited
    FROM coin_transactions ct
    JOIN users u ON ct.user_id = u.id
    WHERE ct.type = 'topup'
    GROUP BY u.id
    ORDER BY total_deposited DESC
    LIMIT 5
");
if ($res_spenders) {
    while ($row = mysqli_fetch_assoc($res_spenders)) {
        $top_spenders[] = $row;
    }
}

// Lấy danh sách Top 4 truyện có lượt xem cao nhất hiển thị ở bảng
$res_top_stories = mysqli_query(
    $con,
    "SELECT title, luot_xem, status
     FROM stories
     ORDER BY luot_xem DESC
     LIMIT 4"
);

// Lấy số lượng người dùng đang ở trạng thái hoạt động
$active_users = get_count(
    $con,
    "SELECT COUNT(*) AS total 
     FROM users 
     WHERE status='active'"
);

// Tính toán số lượng người dùng không hoạt động
$inactive_users = max(0, $total_users - $active_users);

// Lấy danh sách cơ cấu thể loại truyện dựa trên mô tả
$top_categories = [];
$res_cats = mysqli_query(
    $con,
    "SELECT 
        COALESCE(NULLIF(description,''),'(trống)') AS cat,
        COUNT(*) AS cnt
     FROM stories
     GROUP BY cat
     ORDER BY cnt DESC
     LIMIT 4"
);
if ($res_cats) {
    while ($r = mysqli_fetch_assoc($res_cats)) {
        $top_categories[] = [
            'cat' => $r['cat'],
            'cnt' => (int)$r['cnt'],
            'pct' => $total_stories > 0 ? ($r['cnt'] / $total_stories) * 100 : 0,
        ];
    }
}

// Lấy danh sách 5 lượt mở khóa chương truyện gần đây nhất (Tiêu Xu)
$recent_purchases = [];
$res_purchases = mysqli_query(
    $con,
    "SELECT u.username, pc.chapter_id, pc.coins_spent, pc.purchased_at
     FROM purchased_chapters pc
     JOIN users u ON pc.user_id = u.id
     ORDER BY pc.purchased_at DESC
     LIMIT 5"
);
if ($res_purchases) {
    while ($row = mysqli_fetch_assoc($res_purchases)) {
        $recent_purchases[] = $row;
    }
}

// Lấy danh sách 5 giao dịch nạp tiền gần đây nhất (Nạp Xu)
$recent_deposits = [];
$res_deposits = mysqli_query(
    $con,
    "SELECT u.username, ct.vnd_amount, ct.amount, ct.created_at
     FROM coin_transactions ct
     JOIN users u ON ct.user_id = u.id
     WHERE ct.type = 'topup'
     ORDER BY ct.created_at DESC
     LIMIT 5"
);
if ($res_deposits) {
    while ($row = mysqli_fetch_assoc($res_deposits)) {
        $recent_deposits[] = $row;
    }
}

// Hàm trả về tên lớp CSS hiển thị màu theo trạng thái truyện
function thongke_status_class(string $status): string
{
    return match ($status) {
        'ongoing' => 'good',
        'completed' => 'normal',
        'hidden' => 'hot',
        default => 'normal',
    };
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Thống Kê Hệ Thống</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    <div class="topbar">
        <div>
            <h1>Thống Kê Hệ Thống</h1><br>
            <p>Quản lý truyện, người dùng và doanh thu</p>
        </div>
    </div>

    <div class="box" style="margin-bottom:30px;">
        <div class="title">
            <b>Thống kê nội dung truyện</b>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Tổng truyện</h3>
                <p><?= number_format($total_stories) ?></p>
            </div>
            <div class="card">
                <h3>Tổng lượt xem</h3>
                <p><?= number_format($total_views) ?></p>
            </div>
            <div class="card">
                <h3>Bình luận</h3>
                <p><?= number_format($total_comments) ?></p>
            </div>
            <div class="card">
                <h3>Lượt mở khóa</h3>
                <p><?= number_format($total_chapter_unlocks) ?></p>
            </div>
        </div>

        <div class="grid">
            <div>
                <div class="box">
                    <div class="title">Truyện nổi bật (Lượt xem)</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Tên truyện</th>
                                <th>Lượt xem</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($res_top_stories && mysqli_num_rows($res_top_stories) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($res_top_stories)): $st = (string)($row['status'] ?? ''); ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= number_format((int)$row['luot_xem']) ?></td>
                                    <td><span class="status <?= thongke_status_class($st) ?>"><?= htmlspecialchars($st) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">Không có dữ liệu</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="box">
                    <div class="title">Top truyện doanh thu cao nhất (Tiêu Xu)</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Tên truyện</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Tổng xu tiêu</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($top_revenue_stories)): ?>
                            <tr><td colspan="3" style="text-align:center; color:#999; padding: 15px;">Chưa có dữ liệu tiêu xu.</td></tr>
                        <?php else: ?>
                            <?php foreach ($top_revenue_stories as $rev_story): $st = (string)($rev_story['status'] ?? ''); ?>
                                <tr>
                                    <td><?= htmlspecialchars($rev_story['title']) ?></td>
                                    <td><span class="status <?= thongke_status_class($st) ?>"><?= htmlspecialchars($st) ?></span></td>
                                    <td style="text-align: right; font-weight: bold; color: #ea580c;"><?= number_format($rev_story['total_coins']) ?> Xu</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box" style="margin-bottom:30px;">
        <div class="title">
            <b>Thống kê thành viên</b>
        </div>
        <div class="cards">
            <div class="card">
                <h3>Tổng người dùng</h3>
                <p><?= number_format($total_users) ?></p>
            </div>
            <div class="card">
                <h3>Đang hoạt động</h3>
                <p><?= number_format($active_users) ?></p>
            </div>
            <div class="card">
                <h3>Không hoạt động</h3>
                <p><?= number_format($inactive_users) ?></p>
            </div>
        </div>

        <div class="grid">
            <div>
                <div class="box">
                    <div class="title">Người dùng hoạt động</div>
                    <div id="userActiveDonutApex" style="min-height: 250px; padding: 10px 0;"></div>
                </div>
            </div>

            <div>
                <div class="box">
                    <div class="title">Cơ cấu thể loại yêu thích</div>
                    <?php
                    $colors = ['var(--primary)', 'var(--blue)', 'var(--green)', 'var(--orange)'];
                    if (empty($top_categories)) {
                        echo '<div style="padding:10px;color:#999;">Chưa có dữ liệu.</div>';
                    } else {
                        foreach ($top_categories as $i => $cat) {
                            $color = $colors[$i % count($colors)];
                            $pct = max(0, min(100, (float)$cat['pct']));
                            echo '<div class="progress-item">';
                            echo '<div class="progress-top"><span>' . htmlspecialchars($cat['cat']) . '</span><span>' . (int)$cat['cnt'] . ' truyện</span></div>';
                            echo '<div class="progress-bar"><div class="progress-fill" style="width:' . $pct . '%; background:' . $color . ';"></div></div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="box" style="margin-top: 20px;">
            <div class="title">
                <b>Top 5 người dùng nạp tiền nhiều nhất</b>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Hạng</th>
                        <th>Tài khoản</th>
                        <th>Email</th>
                        <th style="text-align: right;">Tổng nạp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($top_spenders)): ?>
                        <tr><td colspan="4" style="text-align:center; color:#999; padding: 20px;">Chưa có dữ liệu nạp tiền.</td></tr>
                    <?php else: ?>
                        <?php foreach ($top_spenders as $index => $spender): ?>
                            <tr>
                                <td><span class="status <?php echo match($index) { 0 => 'hot', 1 => 'good', 2 => 'normal', default => 'normal' }; ?>">#<?= $index + 1 ?></span></td>
                                <td><strong><?= htmlspecialchars($spender['username']) ?></strong></td>
                                <td style="color: #666; font-size: 0.9em;"><?= htmlspecialchars($spender['email']) ?></td>
                                <td style="text-align: right; font-weight: bold; color: #10b981;"><?= number_format($spender['total_deposited']) ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="box" style="margin-bottom:30px;">
        <div class="title">
            <b>Doanh thu & Quản lý ví Xu</b>
        </div>

        <div class="cards">
            <div class="card" style="border-left: 4px solid #10b981;">
                <h3>Doanh thu nạp</h3>
                <p><?= number_format($total_revenue) ?> đ</p>
            </div>
            <div class="card" style="border-left: 4px solid #f59e0b;">
                <h3>Xu đã tiêu</h3>
                <p><?= number_format($total_coins_spent) ?> Xu</p>
            </div>
            <div class="card" style="border-left: 4px solid #3b82f6;">
                <h3>Lượt mua chương</h3>
                <p><?= number_format($total_chapter_unlocks) ?></p>
            </div>
        </div>

        <div class="box" style="margin-top:20px; margin-bottom:20px;">
            <div class="title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <b>Biến động doanh thu hệ thống</b>
                
                <form method="GET" action="" id="filterForm" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div id="customDateInputs" style="display: <?= $filter === 'custom' ? 'flex' : 'none' ?>; gap: 5px; align-items: center;">
                        <input type="date" name="custom_start" id="custom_start" value="<?= htmlspecialchars($custom_start) ?>" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9em;">
                        <span>đến</span>
                        <input type="date" name="custom_end" id="custom_end" value="<?= htmlspecialchars($custom_end) ?>" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9em;">
                        <button type="submit" style="padding: 5px 12px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; font-weight: bold;">Lọc</button>
                    </div>

                    <select name="filter" id="filterSelect" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9em;">
                        <option value="7_days" <?= $filter === '7_days' ? 'selected' : '' ?>>7 ngày gần đây</option>
                        <option value="this_month" <?= $filter === 'this_month' ? 'selected' : '' ?>>Tháng này</option>
                        <option value="this_year" <?= $filter === 'this_year' ? 'selected' : '' ?>>Cả năm nay</option>
                        <option value="custom" <?= $filter === 'custom' ? 'selected' : '' ?>>Khoảng ngày tùy chọn...</option>
                    </select>
                </form>
            </div>
            <div id="revenueChartApex" style="min-height: 250px; padding: 15px 0;"></div>
        </div>

        <div class="grid">
            <div>
                <div class="box">
                    <div class="title">Nhật ký nạp tiền gần đây</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Số tiền</th>
                                <th>Xu nhận</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recent_deposits)): ?>
                            <tr><td colspan="4" style="text-align:center; color:#999;">Chưa có giao dịch nạp tiền nào</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_deposits as $deposit): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($deposit['username'] ?? 'Ẩn danh') ?></strong></td>
                                <td style="color:#10b981; font-weight:bold;">+<?= number_format($deposit['vnd_amount']) ?> đ</td>
                                <td style="color:#3b82f6; font-weight:bold;"><?= number_format($deposit['amount']) ?> Xu</td>
                                <td style="color:#666; font-size:0.85em;"><?= date('d/m H:i', strtotime($deposit['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="box">
                    <div class="title">Lịch sử mở khóa gần đây</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Chương ID</th>
                                <th>Xu tiêu</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recent_purchases)): ?>
                            <tr><td colspan="4" style="text-align:center; color:#999;">Chưa có lượt mua nào</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_purchases as $purchase): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($purchase['username'] ?? 'Ẩn danh') ?></strong></td>
                                <td>#<?= htmlspecialchars($purchase['chapter_id']) ?></td>
                                <td style="color:#ea580c; font-weight:bold;">-<?= number_format($purchase['coins_spent']) ?></td>
                                <td style="color:#666; font-size:0.85em;"><?= date('d/m H:i', strtotime($purchase['purchased_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$chart_labels = array_column($revenue_chart, 'label');
$chart_values = array_column($revenue_chart, 'total');
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // === ĐIỀU KHIỂN ĐỔI CHẾ ĐỘ LỌC NGÀY THÁNG LỊCH ===
    var filterSelect = document.getElementById('filterSelect');
    var customDateInputs = document.getElementById('customDateInputs');
    var form = document.getElementById('filterForm');

    filterSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateInputs.style.display = 'flex';
        } else {
            customDateInputs.style.display = 'none';
            form.submit();
        }
    });

    form.addEventListener('submit', function(e) {
        if (filterSelect.value === 'custom') {
            var start = document.getElementById('custom_start').value;
            var end = document.getElementById('custom_end').value;
            if (!start || !end) {
                e.preventDefault();
                alert('Vui lòng chọn đầy đủ cả Ngày bắt đầu và Ngày kết thúc!');
                return;
            }
            if (start > end) {
                e.preventDefault();
                alert('Ngày bắt đầu không được lớn hơn Ngày kết thúc!');
            }
        }
    });

    // === BIỂU ĐỒ DOANH THU HỆ THỐNG APEXCHARTS ===
    var revenueOptions = {
        series: [{
            name: 'Doanh thu',
            data: <?= json_encode($chart_values) ?>
        }],
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
                borderRadius: 4,
                dataLabels: { position: 'top' }
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) { return val > 0 ? val.toLocaleString('vi-VN') + 'đ' : ''; },
            offsetY: -20,
            style: { fontSize: '10px', colors: ["#304758"] }
        },
        colors: ['#10b981'],
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: {
            categories: <?= json_encode($chart_labels) ?>,
        },
        yaxis: {
            title: { text: 'Số tiền (VND)' },
            labels: { formatter: function (val) { return val.toLocaleString('vi-VN') + ' đ'; } }
        },
        fill: { opacity: 1 },
        tooltip: {
            y: { formatter: function (val) { return val.toLocaleString('vi-VN') + " đ"; } }
        }
    };
    var revenueChart = new ApexCharts(document.querySelector("#revenueChartApex"), revenueOptions);
    revenueChart.render();

    // === BIỂU ĐỒ TRÒN USER HOẠT ĐỘNG APEXCHARTS ===
    var donutOptions = {
        series: [<?= (int)$active_users ?>, <?= (int)$inactive_users ?>],
        chart: {
            type: 'donut',
            height: 260,
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        labels: ['Hoạt động', 'Không hoạt động'],
        colors: ['#10b981', '#e5e7eb'],
        legend: {
            position: 'bottom',
            fontFamily: 'inherit'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) { return Math.round(val) + "%"; }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Tổng User',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('vi-VN');
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) { return val.toLocaleString('vi-VN') + " thành viên"; }
            }
        }
    };
    var donutChart = new ApexCharts(document.querySelector("#userActiveDonutApex"), donutOptions);
    donutChart.render();
});
</script>
</body>
</html>
