<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Trang Thống Kê</title>

<style>

:root{

    --bg:#f5f5f5;

    --sidebar:#111111;

    --card:#ffffff;

    --border:#e5e7eb;

    --text:#111111;

    --sub:#6b7280;

    --primary:#8b5cf6;

    --blue:#60a5fa;

    --green:#34d399;

    --orange:#f59e0b;

}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    display:flex;
    background:var(--bg);
    color:var(--text);
    min-height:100vh;
}

/* Sidebar */

.sidebar{

    width:250px;

    background:var(--sidebar);

    border-right:1px solid #222;

    padding:25px;

}

.sidebar h2{
    font-size:24px;
    margin-bottom:35px;
    color:white;
}

.menu{
    list-style:none;
}

.menu li{

    padding:14px;

    border-radius:12px;

    margin-bottom:8px;

    cursor:pointer;

    transition:0.2s;

    color:#d1d5db;

}

.menu li:hover{

    background:#1f1f1f;

    color:white;

}

.active-menu{

    background:black;

    color:white !important;

}

/* Main */

.main{
    flex:1;
    padding:25px;
}

/* Topbar */

.topbar{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

}

.topbar h1{
    font-size:34px;
}

.topbar p{

    color:var(--sub);

    margin-top:5px;

}

.search{

    padding:12px 16px;

    border-radius:14px;

    border:1px solid var(--border);

    outline:none;

    background:var(--card);

    color:var(--text);

}

.search::placeholder{
    color:var(--sub);
}

/* Cards */

.cards{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));

    gap:18px;

    margin-bottom:25px;

}

.card{

    background:var(--card);

    border:1px solid var(--border);

    border-radius:18px;

    padding:22px;

    position:relative;

    overflow:hidden;

}

.card::before{

    content:'';

    position:absolute;

    left:0;
    top:0;

    width:5px;
    height:100%;

}

.card:nth-child(1)::before{
    background:var(--primary);
}

.card:nth-child(2)::before{
    background:var(--blue);
}

.card:nth-child(3)::before{
    background:var(--green);
}

.card:nth-child(4)::before{
    background:var(--orange);
}

.card h3{

    color:var(--sub);

    font-size:15px;

    margin-bottom:12px;

    font-weight:normal;

}

.card p{

    font-size:34px;

    font-weight:bold;

}

/* Grid */

.grid{

    display:grid;

    grid-template-columns:2fr 1fr;

    gap:20px;

}

/* Box */

.box{

    background:var(--card);

    border:1px solid var(--border);

    border-radius:18px;

    padding:22px;

}

.title{

    font-size:18px;

    margin-bottom:20px;

}

/* Line Chart */

.line-chart{
    width:100%;
    height:280px;
}

/* Donut */

.donut-wrap{

    display:flex;

    flex-direction:column;

    align-items:center;

}

.donut{

    width:180px;

    height:180px;

    border-radius:50%;

    background:
    conic-gradient(
        var(--primary) 0% 72%,
        #e5e7eb 72% 100%
    );

    position:relative;

    margin-bottom:20px;

}

.donut::before{

    content:'';

    width:110px;

    height:110px;

    border-radius:50%;

    background:var(--card);

    position:absolute;

    top:50%;
    left:50%;

    transform:translate(-50%,-50%);

}

.donut-text{

    position:absolute;

    top:50%;
    left:50%;

    transform:translate(-50%,-50%);

    font-size:30px;

    font-weight:bold;

}

.legend{
    width:100%;
}

.legend-item{

    display:flex;

    justify-content:space-between;

    margin-bottom:12px;

    color:var(--sub);

}

/* Progress */

.progress-item{
    margin-bottom:24px;
}

.progress-top{

    display:flex;

    justify-content:space-between;

    margin-bottom:10px;

}

.progress-top span{
    color:var(--sub);
}

.progress-bar{

    width:100%;

    height:10px;

    background:#ececec;

    border-radius:20px;

    overflow:hidden;

}

.progress-fill{

    height:100%;

    border-radius:20px;

}

.cpu{
    width:68%;
    background:var(--primary);
}

.ram{
    width:45%;
    background:var(--blue);
}

.storage{
    width:81%;
    background:var(--green);
}

/* Table */

table{
    width:100%;
    border-collapse:collapse;
}

table th{

    text-align:left;

    padding-bottom:15px;

    color:var(--sub);

    font-weight:normal;

}

table td{

    padding:15px 0;

    border-top:1px solid var(--border);

}

/* Status */

.status{

    padding:6px 12px;

    border-radius:20px;

    font-size:13px;

    display:inline-block;

}

.hot{
    background:rgba(139,92,246,0.12);
    color:#7c3aed;
}

.good{
    background:rgba(52,211,153,0.12);
    color:#059669;
}

.normal{
    background:rgba(96,165,250,0.12);
    color:#2563eb;
}

/* Responsive */

@media(max-width:1000px){

    .grid{
        grid-template-columns:1fr;
    }

    .sidebar{
        display:none;
    }

}

</style>

</head>
<body>

<!-- Sidebar -->

<div class="sidebar">

    <h2>Quản Trị</h2>

    <ul class="menu">

        <li>Tổng Quan</li>

        <li>Người Dùng</li>

        <li>Truyện</li>

        <li>Chương</li>

        <li>Bình Luận</li>

        <li class="active-menu">
            Thống Kê
        </li>

        <li>Cài Đặt</li>

    </ul>

</div>

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

                    <div class="progress-top">

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