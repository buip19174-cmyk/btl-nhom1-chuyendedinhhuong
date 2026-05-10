<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Quản Trị Truyện</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;}
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#2c3e50;color:white;padding:20px;box-shadow:2px 0 10px rgba(0,0,0,0.1);}
.logo{font-size:24px;font-weight:bold;margin-bottom:30px;text-align:center;padding:15px;background:#34495e;border-radius:10px;}
.user-info{background:#34495e;padding:15px;border-radius:10px;margin-bottom:30px;text-align:center;}
.user-role{display:inline-block;background:#3498db;padding:5px 15px;border-radius:20px;font-size:12px;margin-top:10px;}
.menu-item{padding:15px;margin:10px 0;cursor:pointer;border-radius:8px;transition:all 0.3s;display:flex;align-items:center;gap:10px;}
.menu-item:hover{background:#34495e;transform:translateX(5px);}
.menu-item.active{background:#3498db;}
.main-content{flex:1;padding:30px;overflow-y:auto;}
.header{background:white;padding:20px 30px;border-radius:15px;margin-bottom:30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.header h1{color:#2c3e50;}


.content-section{display:none;background:white;padding:30px;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.content-section.active{display:block;animation:fadeIn 0.3s;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.btn{background:#3498db;color:white;border:none;padding:12px 25px;border-radius:8px;cursor:pointer;font-size:16px;margin:5px;transition:all 0.3s;}
.btn:hover{background:#2980b9;transform:translateY(-2px);}
.btn-success{background:#27ae60;}
.btn-success:hover{background:#229954;}
.btn-danger{background:#e74c3c;}
.btn-danger:hover{background:#c0392b;}
.btn-warning{background:#f39c12;}
.btn-warning:hover{background:#e67e22;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:15px;text-align:left;border-bottom:1px solid #ecf0f1;}
th{background:#34495e;color:white;font-weight:600;}
tr:hover{background:#f8f9fa;}
.form-group{margin-bottom:20px;}
label{display:block;margin-bottom:8px;color:#2c3e50;font-weight:600;}
input,select,textarea{width:100%;padding:12px;border:2px solid #ecf0f1;border-radius:8px;font-size:16px;transition:all 0.3s;}
input:focus,select:focus,textarea:focus{outline:none;border-color:#3498db;}
textarea{resize:vertical;min-height:100px;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:1000;animation:fadeIn 0.3s;}
.modal.active{display:flex;justify-content:center;align-items:center;}
.modal-content{background:white;padding:30px;border-radius:15px;width:90%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 5px 30px rgba(0,0,0,0.3);}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #ecf0f1;}
.close-btn{background:none;border:none;font-size:28px;cursor:pointer;color:#7f8c8d;transition:all 0.3s;}
.close-btn:hover{color:#e74c3c;transform:rotate(90deg);}
img.cover-img{width:50px; height:50px; object-fit:cover; border-radius:5px;}
.search-input{
    width:100%;
    max-width:300px;
    padding:10px 15px;
    margin-bottom:15px;
    border:2px solid #ecf0f1;
    border-radius:8px;
    font-size:15px;
}
.search-input:focus{
    outline:none;
    border-color:#3498db;
}

</style>
</head>
<body>
<div class="container">
<div class="sidebar">
<div class="logo">📚 Comic Admin</div>
<div class="user-info">
<div id="userName">Admin User</div>
<span class="user-role" id="userRole">ADMIN</span>
</div>
<div class="menu-item active" onclick="showSection('dashboard')">📊 Dashboard</div>
<div class="menu-item" onclick="showSection('stories')">📖 Quản Lí Truyện</div>
<div class="menu-item" onclick="showSection('chapters')">📝 Quản Lí Chương</div>
<div class="menu-item" onclick="showSection('users')">👥 Quản Lí Người Dùng</div>
</div>

<div class="main-content">
<div class="header">
<h1 id="pageTitle">Dashboard</h1>

</div>

<div id="dashboard" class="content-section active">
<h2>Chào mừng đến trang quản trị!</h2>
<p style="color: #7f8c8d;">Sử dụng menu bên trái để quản lý hệ thống.</p>
</div>

<!-- STORIES SECTION -->
<div id="stories" class="content-section">
<button class="btn btn-success" onclick="openStoryModal()">+ Thêm Truyện</button>
<input type="text" class="search-input" placeholder="🔍 Tìm truyện..."
onkeyup="searchTable(this, 'storiesTable')">
<table id="storiesTable">
<thead>
<tr><th>ID</th><th>Tên Truyện</th><th>Mô Tả</th><th>Cover</th><th>Ngày Tạo</th><th>Thao Tác</th></tr>
</thead>
<tbody></tbody>
</table>
</div>

<!-- USERS SECTION -->
<div id="users" class="content-section">
<button class="btn btn-success" onclick="openUserModal()">+ Thêm Người Dùng</button>
<input type="text" class="search-input" placeholder="🔍 Tìm người dùng..."
onkeyup="searchTable(this, 'usersTable')">
<table id="usersTable">
<thead>
<tr><th>ID</th><th>Tên</th><th>SĐT</th><th>Email</th><th>Thao Tác</th></tr>
</thead>
<tbody></tbody>
</table>
</div>

<!-- CHAPTERS SECTION -->
<div id="chapters" class="content-section">
    <button class="btn btn-success" onclick="openChapterModal()">+ Thêm Chương</button>
    <input type="text" class="search-input" placeholder="🔍 Tìm chương..."
onkeyup="searchTable(this, 'chaptersTable')">
    <table id="chaptersTable">
    <thead>
    <tr>
    <th>ID</th>
    <th>ID Truyện</th>
    <th>Số Chương</th>
    <th>Tên Chương</th>
    <th>Thao Tác</th>
    </tr>
    </thead>
    <tbody></tbody>
    </table>
    </div>
    
</div>
</div>

<!-- USER MODAL -->
<div id="userModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2 id="userModalTitle">Thêm Người Dùng</h2>
<button class="close-btn" onclick="closeModal('userModal')">&times;</button>
</div>
<form id="userForm">
<input type="hidden" id="userId" name="userId">
<div class="form-group"><label>Tên</label><input type="text" id="userNameInput" name="username" required></div>
<div class="form-group"><label>SĐT</label><input type="text" id="userSdt" name="sdt" required></div>
<div class="form-group"><label>Email</label><input type="email" id="userEmail" name="email" required></div>
<div class="form-group"><label>Password</label><input type="password" id="userPassword" name="password" required></div>
<button type="submit" class="btn btn-success">Lưu</button>
<button type="button" class="btn" onclick="closeModal('userModal')">Hủy</button>
</form>
</div>
</div>

<!-- STORY MODAL -->
<div id="storyModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2 id="storyModalTitle">Thêm Truyện</h2>
<button class="close-btn" onclick="closeModal('storyModal')">&times;</button>
</div>
<form id="storyForm" enctype="multipart/form-data">
<input type="hidden" id="storyId" name="storyId">
<input type="hidden" id="storyCoverOld" name="cover_old">
<div class="form-group"><label>Tên Truyện</label><input type="text" id="storyTitle" name="title" required></div>
<div class="form-group"><label>Mô Tả</label><textarea id="storyDescription" name="description"></textarea></div>
<div class="form-group"><label>Cover</label><input type="file" name="cover"></div>
<button type="submit" class="btn btn-success">Lưu</button>
<button type="button" class="btn" onclick="closeModal('storyModal')">Hủy</button>
</form>
</div>
</div>

<!-- CHAPTER MODAL -->
<div id="chapterModal" class="modal">
    <div class="modal-content">
    <div class="modal-header">
    <h2 id="chapterModalTitle">Thêm Chương</h2>
    <button class="close-btn" onclick="closeModal('chapterModal')">&times;</button>
    </div>
    
    <form id="chapterForm">
    <input type="hidden" id="chapterId" name="id">
    
    <div class="form-group">
    <label>ID Truyện</label>
    <input type="number" id="chapterStoryId" name="story_id" required>
    </div>
    
    <div class="form-group">
    <label>Số Chương</label>
    <input type="number" id="chapterNumber" name="chapter_number" required>
    </div>
    
    <div class="form-group">
    <label>Tên Chương</label>
    <input type="text" id="chapterTitle" name="title" required>
    </div>
    
    <div class="form-group">
    <label>Nội Dung</label>
    <textarea id="chapterContent" name="content"></textarea>
    </div>
    
    <button type="submit" class="btn btn-success">Lưu</button>
    <button type="button" class="btn" onclick="closeModal('chapterModal')">Hủy</button>
    </form>
    </div>
    </div>
    

<script>
const titles = {dashboard:'Dashboard', stories:'Quản Lí Truyện', users:'Quản Lí Người Dùng'};
function showSection(section){
    document.querySelectorAll('.content-section').forEach(s=>s.classList.remove('active'));
    document.getElementById(section).classList.add('active');
    document.getElementById('pageTitle').textContent=titles[section];
}
function logout(){alert("Đã đăng xuất!"); location.reload();}
function closeModal(id){document.getElementById(id).classList.remove('active');}
window.onclick=function(e){if(e.target.classList.contains('modal')) e.target.classList.remove('active');}

// ------------------- USER -------------------
function openUserModal(user=null){
    document.getElementById('userForm').reset();
    if(user){
        document.getElementById('userModalTitle').textContent='Sửa Người Dùng';
        document.getElementById('userId').value=user.id;
        document.getElementById('userNameInput').value=user.username;
        document.getElementById('userSdt').value=user.sdt;
        document.getElementById('userEmail').value=user.email;
        document.getElementById('userPassword').required = false;
    }else{
        document.getElementById('userModalTitle').textContent='Thêm Người Dùng';
        document.getElementById('userId').value='';
        document.getElementById('userPassword').required = true;
    }
    document.getElementById('userModal').classList.add('active');
}

function loadUsers(){
    fetch('user_list.php').then(res=>res.json()).then(data=>{
        const tbody = document.querySelector("#usersTable tbody");
        tbody.innerHTML='';
        data.forEach(u=>{
            tbody.innerHTML+=`<tr>
<td>${u.id}</td><td>${u.username}</td><td>${u.sdt}</td><td>${u.email}</td>
<td>
<button class="btn btn-warning" onclick='openUserModal(${JSON.stringify(u)})'>Sửa</button>
<button class="btn btn-danger" onclick='deleteUser(${u.id})'>Xóa</button>
</td>
</tr>`;
        });
    });
}

// document.getElementById('userForm').addEventListener('submit', function(e){
//     e.preventDefault();
//     const formData = new FormData(this);
//     fetch('add_user.php',{method:'POST',body:formData})
//     .then(res=>res.json())
//     .then(data=>{
//         alert(data.message);
//         if(data.status==='success'){closeModal('userModal'); loadUsers();}
//     }).catch(err=>{console.error(err); alert('Có lỗi xảy ra');});
// });
document.getElementById('userForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);
    const userId = document.getElementById('userId').value; // Lấy userId hiện tại

    // Nếu userId tồn tại → gửi tới edit_user.php
    // Nếu không → gửi tới add_user.php
    const url = userId ? 'edit_user.php' : 'add_user.php';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            closeModal('userModal');
            loadUsers();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Có lỗi xảy ra khi lưu user');
    });
});


function deleteUser(id){
    if(confirm('Bạn có chắc muốn xóa?')){
        const fd = new FormData(); fd.append('id',id);
        fetch('delete_user.php',{method:'POST',body:fd})
        .then(res=>res.json())
        .then(data=>{
            alert(data.message);
            if(data.status==='success') loadUsers();
        });
    }
}

// ------------------- STORIES -------------------
function openStoryModal(story=null){
    document.getElementById('storyForm').reset();
    if(story){
        document.getElementById('storyModalTitle').textContent='Sửa Truyện';
        document.getElementById('storyId').value=story.id;
        document.getElementById('storyTitle').value=story.title;
        document.getElementById('storyDescription').value=story.description;
        document.getElementById('storyCoverOld').value=story.cover;
    }else{
        document.getElementById('storyModalTitle').textContent='Thêm Truyện';
        document.getElementById('storyId').value='';
        document.getElementById('storyCoverOld').value='';
    }
    document.getElementById('storyModal').classList.add('active');
}

function loadStories(){
    fetch('story_list.php').then(res=>res.json()).then(data=>{
        const tbody = document.querySelector("#storiesTable tbody");
        tbody.innerHTML='';
        data.forEach(story=>{
            tbody.innerHTML+=`<tr>
<td>${story.id}</td>
<td>${story.title}</td>
<td>${story.description}</td>
<td>${story.cover ? `<img class="cover-img" src="${story.cover}">` : ''}</td>
<td>${story.created_at}</td>
<td>
<button class="btn btn-warning" onclick='openStoryModal(${JSON.stringify(story)})'>Sửa</button>
<button class="btn btn-danger" onclick='deleteStory(${story.id})'>Xóa</button>
</td>
</tr>`;
        });
    });
}

document.getElementById('storyForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('add_story.php',{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{
        alert(data.message);
        if(data.status==='success'){closeModal('storyModal'); loadStories();}
    }).catch(err=>{console.error(err); alert('Có lỗi xảy ra');});
});

function deleteStory(id){
    if(confirm('Bạn có chắc muốn xóa?')){
        const fd = new FormData(); fd.append('id',id);
        fetch('delete_story.php',{method:'POST',body:fd})
        .then(res=>res.json())
        .then(data=>{
            alert(data.message);
            if(data.status==='success') loadStories();
        });
    }
}

// ------------------- CHAPTERS -------------------
function openChapterModal(chapter = null){
    document.getElementById('chapterForm').reset();

    if(chapter){
        document.getElementById('chapterModalTitle').textContent = 'Sửa Chương';
        document.getElementById('chapterId').value = chapter.id;
        document.getElementById('chapterStoryId').value = chapter.story_id;
        document.getElementById('chapterNumber').value = chapter.chapter_number;
        document.getElementById('chapterTitle').value = chapter.title;
        document.getElementById('chapterContent').value = chapter.content;
    }else{
        document.getElementById('chapterModalTitle').textContent = 'Thêm Chương';
        document.getElementById('chapterId').value = '';
    }

    document.getElementById('chapterModal').classList.add('active');
}

function loadChapters(){
    fetch('chapter_list.php')
    .then(res => res.json())
    .then(data => {
        const tbody = document.querySelector('#chaptersTable tbody');
        tbody.innerHTML = '';

        data.forEach(c => {
            tbody.innerHTML += `
<tr>
<td>${c.id}</td>
<td>${c.story_id}</td>
<td>${c.chapter_number}</td>
<td>${c.title}</td>
<td>
<button class="btn btn-warning" onclick='openChapterModal(${JSON.stringify(c)})'>Sửa</button>
<button class="btn btn-danger" onclick='deleteChapter(${c.id})'>Xóa</button>
</td>
</tr>`;
        });
    });
}

document.getElementById('chapterForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);
    const chapterId = document.getElementById('chapterId').value;
    const url = chapterId ? 'edit_chapter.php' : 'add_chapter.php';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.status === 'success'){
            closeModal('chapterModal');
            loadChapters();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Có lỗi khi lưu chương');
    });
});

function deleteChapter(id){
    if(confirm('Bạn có chắc muốn xóa chương này?')){
        const fd = new FormData();
        fd.append('id', id);

        fetch('delete_chapter.php', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') loadChapters();
        });
    }
}

function searchTable(input, tableId){
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const trs = table.getElementsByTagName('tr');

    for(let i = 1; i < trs.length; i++){
        let text = trs[i].textContent.toLowerCase();
        trs[i].style.display = text.includes(filter) ? '' : 'none';
    }
}


// ------------------- LOAD -------------------
window.onload = function(){
    loadUsers();
    loadStories();
    loadChapters();
}

</script>
</body>
</html> 