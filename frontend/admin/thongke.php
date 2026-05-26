<?php
require_once '../../backend/require_admin.php';
require_admin();
include_once '../../database/connect.php';
/** @var mysqli $con */

// 1. Tổng người dùng
$total_users = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0);

// 2. Tổng truyện
$total_stories = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM stories"))['total'] ?? 0);

// 3. Tổng lượt xem
$total_views = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(luot_xem) AS total FROM stories"))['total'] ?? 0);

// 4. Tổng bình luận
$total_comments = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM comments"))['total'] ?? 0);

// 5. Top 4 truyện (bảng)
$res_top_stories = mysqli_query($con, "SELECT title, luot_xem, status FROM stories ORDER BY luot_xem DESC LIMIT 4");

// 6. Top 7 truyện (biểu đồ)
$chart_stories = [];
$res_chart = mysqli_query($con, "SELECT title, luot_xem FROM stories ORDER BY luot_xem DESC LIMIT 7");
if ($res_chart) {
    while ($r = mysqli_fetch_assoc($res_chart)) {
        $chart_stories[] = $r;
    }
}
$max_views = 1;
foreach ($chart_stories as $cs) {
    $max_views = max($max_views, (int)($cs['luot_xem'] ?? 0));
}

// 7. % user hoạt động
$active_users = (int)(mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM users WHERE status='active'"))['total'] ?? 0);
$active_percent = $total_users > 0 ? (int)round(($active_users / $total_users) * 100) : 0;

// 8. Cơ cấu thể loại
$top_categories = [];
$res_cats = mysqli_query($con, "SELECT COALESCE(NULLIF(description,''),'(trống)') AS cat, COUNT(*) AS cnt
                         FROM stories
                         GROUP BY cat
                         ORDER BY cnt DESC
                         LIMIT 4");
if ($res_cats) {
    while ($r = mysqli_fetch_assoc($res_cats)) {
        $top_categories[] = [
            'cat' => $r['cat'],
            'cnt' => (int)$r['cnt'],
            'pct' => $total_stories > 0 ? ($r['cnt'] / $total_stories) * 100 : 0,
        ];
    }
}

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
    <title>Trang Thống Kê</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main">
    <div class="topbar">
        <div>
            <h1>Thống Kê</h1>
            <p>Tổng quan hoạt động hệ thống</p>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Người dùng</h3>
            <p><?= number_format($total_users) ?></p>
        </div>
        <div class="card">
            <h3>Truyện</h3>
            <p><?= number_format($total_stories) ?></p>
        </div>
        <div class="card">
            <h3>Lượt xem</h3>
            <p><?= number_format($total_views) ?></p>
        </div>
        <div class="card">
            <h3>Bình luận</h3>
            <p><?= number_format($total_comments) ?></p>
        </div>
    </div>

    <div class="grid">
        <div>
            <div class="box">
                <div class="title">Top truyện theo lượt xem</div>
                <?php if (empty($chart_stories)): ?>
                    <p style="color:#999;padding:20px 0;">Chưa có dữ liệu truyện.</p>
                <?php else: ?>
                    <div class="bar-chart">
                        <?php
                        $chart_h = 200;
                        $bar_w = 56;
                        $gap = 18;
                        $i = 0;
                        foreach ($chart_stories as $cs):
                            $views = (int)($cs['luot_xem'] ?? 0);
                            $h = (int)round(($views / $max_views) * $chart_h);
                            $label = mb_strimwidth($cs['title'], 0, 12, '…');
                        ?>
                        <div class="bar-col" style="width:<?= $bar_w ?>px">
                            <span class="bar-val"><?= number_format($views) ?></span>
                            <div class="bar-track" style="height:<?= $chart_h ?>px">
                                <div class="bar-fill" style="height:<?= max(4, $h) ?>px"></div>
                            </div>
                            <span class="bar-label" title="<?= htmlspecialchars($cs['title']) ?>"><?= htmlspecialchars($label) ?></span>
                        </div>
                        <?php $i++; endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

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
                        <?php if ($res_top_stories && mysqli_num_rows($res_top_stories) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($res_top_stories)): ?>
                                <?php $st = (string)($row['status'] ?? ''); ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= number_format((int)$row['luot_xem']) ?></td>
                                    <td>
                                        <span class="status <?= thongke_status_class($st) ?>">
                                            <?= htmlspecialchars($st) ?>
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

        <div>
            <div class="box">
                <div class="title">Người dùng hoạt động</div>
                <div class="donut-wrap">
                    <div class="donut" style="background: conic-gradient(var(--primary) 0% <?= $active_percent ?>%, #e5e7eb <?= $active_percent ?>% 100%);">
                        <div class="donut-text"><?= $active_percent ?>%</div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:12px; height:12px; background:var(--primary); border-radius:3px;"></div>
                                <span>Hoạt động</span>
                            </div>
                            <span><?= $active_percent ?>%</span>
                        </div>
                        <div class="legend-item">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:12px; height:12px; background:#e5e7eb; border-radius:3px;"></div>
                                <span>Không hoạt động</span>
                            </div>
                            <span><?= max(0, 100 - $active_percent) ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box" style="margin-top:20px;">
                <div class="title">Cơ cấu thể loại</div>
                <?php
                $colors = ['var(--primary)', 'var(--blue)', 'var(--green)', 'var(--orange)'];
                if (empty($top_categories)) {
                    echo '<div style="padding:10px;color:#999;">Chưa có dữ liệu thể loại.</div>';
                } else {
                    foreach ($top_categories as $i => $cat) {
                        $color = $colors[$i % count($colors)];
                        $pct = max(0, min(100, (float)$cat['pct']));
                        echo '<div class="progress-item">';
                        echo '  <div class="progress-top">';
                        echo '    <span>' . htmlspecialchars($cat['cat']) . '</span>';
                        echo '    <span>' . (int)$cat['cnt'] . ' truyện</span>';
                        echo '  </div>';
                        echo '  <div class="progress-bar">';
                        echo '    <div class="progress-fill" style="width: ' . $pct . '%; background: ' . $color . ';"></div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
