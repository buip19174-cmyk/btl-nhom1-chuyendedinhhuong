<?php
$story_id = $_GET['id'] ?? 1;

// fake chapters
$chapters = [
    ["id"=>1, "title"=>"Chapter 1", "content"=>"Nội dung chapter 1..."],
    ["id"=>2, "title"=>"Chapter 2", "content"=>"Nội dung chapter 2..."]
];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chapter Editor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="main">

    <div class="topbar">
        <h2>Quản lý Chapter</h2>
        <button class="add-btn" onclick="addChapter()">+ Chapter mới</button>
    </div>

    <div class="grid">

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
            <div class="title">Editor</div>

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

function loadChapter(id){
    alert("Load chapter " + id);
}

function saveChapter(){
    let content = document.getElementById("editor").innerHTML;
    let title = document.getElementById("chapterTitle").value;

    console.log(title, content);
    alert("Saved!");
}

function addChapter(){
    document.getElementById("chapterTitle").value = "";
    document.getElementById("editor").innerHTML = "";
}

</script>

</body>
</html>