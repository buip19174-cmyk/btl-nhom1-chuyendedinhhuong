<?php
require_once '../../backend/require_admin.php';
require_admin();
include '../../database/connect.php';

// Tối ưu bảo mật: Ép kiểu INT để phòng chống lỗi SQL Injection qua tham số 'id' trên URL
$story_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
                <!-- LEFT: Danh sách chapter -->
                <div class="box">
                    <div class="title"><b>Danh sách chapter</b></div>

                    <?php if ($chapters && mysqli_num_rows($chapters) > 0): ?>
                        <?php foreach($chapters as $c): ?>
                            <!-- Thuộc tính data-* kết hợp ENT_QUOTES để chứa dữ liệu JSON an toàn -->
                            <div class="chapter-item" 
                                 style="padding:10px; border-bottom:1px solid #eee; cursor:pointer"
                                 data-chapter="<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>"
                                 onclick="handleChapterClick(this)">
                                <?= htmlspecialchars($c['title']) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:10px; color:#999;">Truyện chưa có chương nào.</div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT: Trình biên tập nội dung -->
                <div class="box">
                    <div class="title" id="editorTitle">Thêm chương mới</div>
                    
                    <!-- Hidden input để giữ ID chương khi sửa/xóa -->
                    <input type="hidden" id="chapterId" value="">
                    
                    <input id="chapterTitle" style="width:100%; padding:10px; box-sizing: border-box;" placeholder="Tiêu đề chapter">

                    <div id="editor"
                        contenteditable="true"
                        data-placeholder="Viết nội dung ở đây..."></div>

                    <br>
                    <div>
                        <button onclick="saveChapter()" id="btnSave" class="btn">Lưu chương</button>
                        <a id="btnPreview" class="btn" style="display:none; text-decoration:none;" target="_blank">Xem trước</a>
                        <button onclick="deleteChapter()" class="btn" style="background:red; color:white;">Xóa</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let autoSaveTimer;

        // XỬ LÝ PASTE AN TOÀN: Loại bỏ mã rác và giữ đúng cấu trúc xuống dòng chuẩn
        document.getElementById("editor").addEventListener("paste", function(e) {
            e.preventDefault(); // Chặn hành vi dán mặc định chứa HTML/CSS bẩn
            
            // Lấy văn bản thuần túy từ clipboard
            let text = (e.originalEvent || e).clipboardData.getData('text/plain');
            
            // Sử dụng Selection API để chèn văn bản sạch vào đúng vị trí con trỏ
            const selection = window.getSelection();
            if (!selection.rangeCount) return;
            selection.deleteFromDocument();
            
            // Tạo một Text Node chứa nội dung thuần túy (Trình duyệt sẽ tự xử lý xuống dòng an toàn)
            const textNode = document.createTextNode(text);
            selection.getRangeAt(0).insertNode(textNode);
            
            // Di chuyển con trỏ chuột về phía sau văn bản vừa dán
            selection.collapseToEnd();
            
            // Kích hoạt hàm tính toán lưu tự động vì cấu trúc nội dung vừa thay đổi
            triggerAutoSave();
        });

        // Xử lý sự kiện Auto-save (Tự động lưu ngầm khi người dùng ngừng gõ 3 giây)
        document.getElementById("editor").addEventListener("input", triggerAutoSave);
        document.getElementById("chapterTitle").addEventListener("input", triggerAutoSave);

        function triggerAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                let id = document.getElementById("chapterId").value;
                let title = document.getElementById("chapterTitle").value;
                
                if (id && title.trim() !== "") {
                    let contentText = document.getElementById("editor").innerText || '';
                    if (contentText.trim().length < 10) return;
                    console.log("Hệ thống đang tự động lưu chương ID: " + id);
                    
                    let content = document.getElementById("editor").innerHTML;
                    let story_id = "<?= $story_id ?>";

                    let formData = new FormData();
                    formData.append('id', id);
                    formData.append('story_id', story_id);
                    formData.append('title', title);
                    formData.append('content', content);

                    fetch('../../backend/edit_chapter.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log("Kết quả Auto-save:", data);
                    })
                    .catch(error => console.error('Lỗi hệ thống Auto-save:', error));
                }
            }, 3000); 
        }

        // Hàm trích xuất dữ liệu an toàn tránh lỗi cú pháp JavaScript khi click vào chương
        function handleChapterClick(element) {
            const rawData = element.getAttribute('data-chapter');
            try {
                const chapterData = JSON.parse(rawData);
                loadChapter(chapterData);
            } catch (e) {
                console.error("Không thể phân tích dữ liệu JSON của chương: ", e);
            }
        }

        // Đổ dữ liệu chương đã chọn vào khu vực chỉnh sửa
        function loadChapter(chapterData) {
            document.getElementById("editorTitle").innerText = "Chỉnh sửa chương";
            document.getElementById("chapterId").value = chapterData.id;
            document.getElementById("chapterTitle").value = chapterData.title;
            document.getElementById("editor").innerHTML = chapterData.content ? chapterData.content : "";
            document.getElementById("btnSave").innerText = "Cập nhật chương";

            const preview = document.getElementById("btnPreview");
            preview.style.display = "inline-block";
            preview.href = "../../backend/read_chapter.php?chapter_id=" + chapterData.id;
        }

        // Reset toàn bộ trường nhập liệu về trạng thái thêm mới
        function addNewChapterForm() {
            document.getElementById("editorTitle").innerText = "Thêm chương mới";
            document.getElementById("chapterId").value = "";
            document.getElementById("chapterTitle").value = "";
            document.getElementById("editor").innerHTML = "";
            document.getElementById("btnSave").innerText = "Lưu chương mới";
            document.getElementById("btnPreview").style.display = "none";
        }

        // Gửi dữ liệu bằng fetch (AJAX) theo phương thức thủ công khi click nút Lưu
        function saveChapter(){
            let id = document.getElementById("chapterId").value;
            let content = document.getElementById("editor").innerHTML;
            let title = document.getElementById("chapterTitle").value;
            let story_id = "<?= $story_id ?>"; 

            if (title.trim() === "") {
                alert("Vui lòng nhập tiêu đề chương!");
                return;
            }
            let contentText = document.getElementById("editor").innerText || '';
            if (contentText.trim().length < 10) {
                alert("Nội dung quá ngắn (tối thiểu 10 ký tự)!");
                return;
            }

            let formData = new FormData();
            formData.append('id', id);
            formData.append('story_id', story_id);
            formData.append('title', title);
            formData.append('content', content);
            
            fetch('../../backend/edit_chapter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || (data.status === 'success' ? 'Thành công' : 'Thất bại'));
                if (data.status === 'success') location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Có lỗi xảy ra khi tiến hành lưu!");
            });
        }

        // Xử lý yêu cầu xóa chương đang chọn
        function deleteChapter() {
            let id = document.getElementById("chapterId").value;
            if (id === ""){
                alert("Bạn chưa chọn chapter nào để xóa!");
                return;
            }
            if (confirm("Bạn có chắc chắn muốn xóa vĩnh viễn chương này không?")) {
                let formData = new FormData();
                formData.append('id', id);

                fetch('../../backend/delete_chapter.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || (data.status === 'success' ? 'Đã xóa' : 'Xóa thất bại'));
                    if (data.status === 'success') location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }
        </script>
    </body>
</html>