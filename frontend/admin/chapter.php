<?php
include '../../backend/connect.php';
$story_id = $_GET['id'];
$sql = "SELECT * FROM chapters 
        WHERE story_id = $story_id 
        ORDER BY chapter_number ASC";

$chapters = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chapter Editor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
    
    <div class="topbar">
        <h2>Quản lý Chương</h2>
        <button class="add-btn" onclick="addChapter()">+ Chapter mới</button>
    </div>

    <div class="chapter-layout">

        <!-- LEFT: list chapter -->
        <div class="box">
            <div class="title">Danh sách chapter</div>

            <?php foreach($chapters as $c): ?>
                <div style="padding:10px; border-bottom:1px solid #eee; cursor:pointer"
                     onclick="loadChapter(<?= $c['id'] ?>)">
                    <?= $c['title'] ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- RIGHT: editor -->
        <div class="box">
            <div class="title">Thêm chương mới</div>

            <input id="chapterTitle" style="width:100%; padding:10px;" placeholder="Tiêu đề chapter">

            <div id="editor"
                 contenteditable="true"
                 style="min-height:300px; border:1px solid #ddd; padding:10px; margin-top:10px;">
                Viết nội dung ở đây...
            </div>

            <br>

            <button onclick="saveChapter()">💾 Lưu</button>
        </div>

    </div>

</div>

<script>

let autoSaveTimer;

// auto save
document.getElementById("editor").addEventListener("input", function(){
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(()=>{
        console.log("Auto saving...");
        // call API save
    }, 2000);
});

function loadChapter(chapter_number){
    alert("Load chapter " + chapter_number);
}

function saveChapter(){
    let content = document.getElementById("editor").innerHTML;
    let title = document.getElementById("chapterTitle").value;
    let story_id = "<?= $story_id ?>"; // Lấy ID truyện từ PHP truyền xuống
    if (title.trim() === "") {
        alert("Vui lòng nhập tiêu đề chương!");
        return;
    }
    let formData = new FormData();
    formData.append('story_id', story_id);
    formData.append('title', title);
    formData.append('content', content);

    // Gửi dữ liệu bằng fetch (AJAX)
    fetch('/btl-nhom1-chuyendedinhhuong/backend/add_chapter.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Hiển thị thông báo "Thành công!"
        location.reload(); // Load lại trang để thấy chương mới hiện bên danh sách trái
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Có lỗi xảy ra khi lưu!");
    });
}

function addChapter(){
    document.getElementById("chapterTitle").value = "";
    document.getElementById("editor").innerHTML = "";
}

</script>

</body>
</html>