<?php
include '../../backend/connect.php'; // Đảm bảo trong này là $con
$story_id = $_GET['id'];
$sql = "SELECT * FROM chapters 
        WHERE story_id = $story_id 
        ORDER BY chapter_number ASC";

$chapters = mysqli_query($con, $sql);
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
        <button class="add-btn" onclick="addNewChapterForm()">+ Chapter mới</button>
    </div>

    <div class="chapter-layout">
        <!-- LEFT: list chapter -->
        <div class="box">
            <div class="title">Danh sách chapter</div>
            <?php foreach($chapters as $c): ?>
                <div class="chapter-item" style="padding:10px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                    <span onclick="loadChapter(<?= htmlspecialchars(json_encode($c)) ?>)" style="cursor:pointer; flex:1;">
                        <?= $c['title'] ?>
                    </span>
                    <!-- Nút xóa -->
                    <button onclick="deleteChapter(<?= $c['id'] ?>)" style="background:red; color:white; border:none; padding:5px; cursor:pointer; border-radius:3px;">Xóa</button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- RIGHT: editor -->
        <div class="box">
            <div class="title" id="editorTitle">Thêm chương mới</div>
            <!-- Hidden input để giữ ID chương khi sửa -->
            <input type="hidden" id="chapterId" value="">
            
            <input id="chapterTitle" style="width:100%; padding:10px;" placeholder="Tiêu đề chapter">

            <div id="editor" contenteditable="true" style="min-height:300px; border:1px solid #ddd; padding:10px; margin-top:10px; background: white;">
                Viết nội dung ở đây...
            </div>

            <br>
            <button onclick="saveChapter()" id="btnSave" style="background:#28a745; color:white; padding:10px 20px; border:none; cursor:pointer;">💾 Lưu chương</button>
        </div>
    </div>
</div>

<script>
// Hàm khi ấn vào chương bên trái
function loadChapter(chapterData) {
    document.getElementById("editorTitle").innerText = "Chỉnh sửa chương";
    document.getElementById("chapterId").value = chapterData.id;
    document.getElementById("chapterTitle").value = chapterData.title;
    document.getElementById("editor").innerHTML = chapterData.content;
    document.getElementById("btnSave").innerText = "💾 Cập nhật chương";
}

// Hàm để reset form về trạng thái "Thêm mới"
function addNewChapterForm() {
    document.getElementById("editorTitle").innerText = "Thêm chương mới";
    document.getElementById("chapterId").value = "";
    document.getElementById("chapterTitle").value = "";
    document.getElementById("editor").innerHTML = "";
    document.getElementById("btnSave").innerText = "💾 Lưu chương mới";
}

function saveChapter() {
    let id = document.getElementById("chapterId").value;
    let title = document.getElementById("chapterTitle").value;
    let content = document.getElementById("editor").innerHTML;
    let story_id = "<?= $story_id ?>";

    if (title.trim() === "") {
        alert("Vui lòng nhập tiêu đề chương!");
        return;
    }

    let formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);
    formData.append('story_id', story_id);

    // Quyết định gửi đến file ADD hay EDIT
    let url = '/btl-nhom1-chuyendedinhhuong/backend/add_chapter.php';
    if (id !== "") {
        formData.append('id', id);
        url = '/btl-nhom1-chuyendedinhhuong/backend/edit_chapter.php'; // Đổi tên cho khớp file của bạn
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert("Thông báo: " + data);
        location.reload();
    })
    .catch(error => alert("Lỗi kết nối!"));
}

function deleteChapter(id) {
    if (confirm("Bạn có chắc chắn muốn xóa chương này không?")) {
        let formData = new FormData();
        formData.append('id', id);

        fetch('/btl-nhom1-chuyendedinhhuong/backend/delete_chapter.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        });
    }
}
</script>

</body>
</html>