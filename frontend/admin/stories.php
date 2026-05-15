<?php
include '../../backend/connect.php';
$sql = "SELECT * FROM stories";
$stories = mysqli_query($con, $sql); // Đảm bảo có dấu $ trước conn
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
<div class="main">

    <div class="topbar">
        <h2>Quản lý truyện</h2>
        <button class="add-btn" onclick="openModal()">+ Thêm truyện</button>
    </div>

    <!-- Action -->
    <div class="action-bar">
        <div class="left-actions">
            <input type="text" id="searchInput" placeholder="Tìm truyện...">
            <select id="filterSelect">
                <option>Tất cả</option>
                <option>Đang cập nhật</option>
                <option>Hoàn thành</option>
                <option>Ẩn</option>
            </select>
            <div id="resultContainer"> <!-- Kết quả sẽ đổ vào đây --> </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Tên truyện</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>

            <?php foreach($stories as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= $s['title'] ?></td>
                <td>
                    <span class="status good">
                        <?= $s['status'] ?>
                    </span>
                </td>
                <td>
                    <button class="btn edit" onclick="editStory(<?= $s['id'] ?>)">Sửa</button>
                    <button class="btn delete">Xóa</button>
                    <a href="chapter.php?id=<?= $s['id'] ?>">
                        <button class="btn">Chương</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>

</div>

<!-- MODAL ADD STORY -->
<div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:white; width:500px; margin:10% auto; padding:20px; border-radius:10px;">
        <h3>Thêm truyện</h3>

        <input id="title" placeholder="Tên truyện" style="width:100%; padding:10px; margin:10px 0;">
        
        <select id="status" style="width:100%; padding:10px;">
            <option>ongoing</option>
            <option>completed</option>
            <option>hidden</option>
        </select>

        <br><br>

        <button onclick="saveStory()">Lưu</button>
        <button onclick="closeModal()">Đóng</button>
    </div>
</div>

<script>
function openModal(){
    document.getElementById("modal").style.display="block";
}
function closeModal(){
    document.getElementById("modal").style.display="none";
}

function saveStory(){
    alert("Gọi API thêm truyện (PHP backend)");
}
function editStory(id){
    alert("Sửa truyện " + id);
    
}

function deleteStory(id){
    alert("Xóa truyện " + id);
    
}

</script>

</body>
</html>

