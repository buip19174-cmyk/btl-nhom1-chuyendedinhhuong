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
                <p>
                    Tổng quan hoạt động hệ thống
                </p>
            </div>
            <input
            class="search"
            type="text"
            placeholder="Tìm kiếm...">
        </div>
        <!-- Cards -->
        <div class="cards">
            <div class="card">
                <h3>Người dùng</h3>
                <p>1,245</p>
            </div>
            <div class="card">
                <h3>Truyện</h3>
                <p>328</p>
            </div>
            <div class="card">
                <h3>Lượt xem</h3>
                <p>52K</p>
            </div>
            <div class="card">
                <h3>Bình luận</h3>
                <p>5,231</p>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid">
            <!-- LEFT -->
            <div>
                <!-- Line Chart -->
                <div class="box">

                    <div class="title">
                        Lượt truy cập 7 ngày gần đây
                    </div>

                    <svg
                    class="line-chart"
                    viewBox="0 0 600 260">

                        <!-- Grid -->

                        <line
                        x1="0"
                        y1="50"
                        x2="600"
                        y2="50"
                        stroke="#e5e7eb"/>

                        <line
                        x1="0"
                        y1="120"
                        x2="600"
                        y2="120"
                        stroke="#e5e7eb"/>

                        <line
                        x1="0"
                        y1="190"
                        x2="600"
                        y2="190"
                        stroke="#e5e7eb"/>

                        <!-- Area -->

                        <path
                        d="
                        M0 180
                        L80 160
                        L160 120
                        L240 130
                        L320 90
                        L400 110
                        L480 70
                        L560 40
                        L560 260
                        L0 260
                        Z"
                        fill="rgba(139,92,246,0.12)"/>

                        <!-- Line -->

                        <polyline
                        fill="none"
                        stroke="#8b5cf6"
                        stroke-width="5"
                        stroke-linecap="round"
                        points="
                        0,180
                        80,160
                        160,120
                        240,130
                        320,90
                        400,110
                        480,70
                        560,40"
                        />

                    </svg>

                </div>

                <!-- Table -->

                <div class="box"
                style="margin-top:20px;">

                    <div class="title">
                        Truyện nổi bật
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Tên truyện</th>
                                <th>Lượt xem</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Solo Leveling</td>
                                <td>12K</td>
                                <td>
                                    <span class="status hot">
                                        Hot
                                    </span>
                                </td>

                            </tr>

                            <tr>
                                <td>Blue Lock</td>
                                <td>8.4K</td>
                                <td>
                                    <span class="status good">
                                        Tăng
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>One Piece</td>
                                <td>15K</td>
                                <td>
                                    <span class="status hot">
                                        Hot
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Chainsaw Man</td>
                                <td>6.1K</td>
                                <td>
                                    <span class="status normal">
                                        Ổn định
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- RIGHT -->
            <div>
                <!-- Donut -->
                <div class="box">
                    <div class="title">
                        Người dùng hoạt động
                    </div>
                    <div class="donut-wrap">
                        <div class="donut">
                            <div class="donut-text">
                                72%
                            </div>
                        </div>
                        <div class="legend">
                            <div class="legend-item">
                                <span>Hoạt động</span>
                                <span>72%</span>
                            </div>
                            <div class="legend-item">
                                <span>Không hoạt động</span>
                                <span>28%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Progress -->
                <div class="box"
                style="margin-top:20px;">
                    <div class="title">
                        Tài nguyên hệ thống
                    </div>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span>CPU</span>
                            <span>68%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill cpu"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span>RAM</span>
                            <span>45%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill ram"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-top"
                            <span>Bộ nhớ</span>
                            <span>81%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill storage"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>