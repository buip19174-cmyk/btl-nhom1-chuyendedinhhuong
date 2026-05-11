
<?php
session_start();
include '../backend/dangky_logic.php';
include '../backend/dangnhap_logic.php';
require '../database/connect.php'; // file kết nối DB

// LẤY DANH SÁCH TRUYỆN
$sql = "SELECT id, title, cover FROM stories 
        WHERE description = 'tt_kinhdi'
        LIMIT 6";
$result = mysqli_query($con, $sql);

$books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trinh thám - Kinh dị</title>
    <link rel="stylesheet" href="css/d.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo"><a href="home.php">KEWE</a></div>
           <nav>
        <ul>
            <li>
                <a href="#">Sách</a>
                <div class="mega-menu">
                    <div class="item"><a href="tho_tanvan.php">Thơ - Tản văn</a> </div>
                <div class="item"><a href="trinhtham.php">Trinh thám - Kinh dị</a> <span>NEW</span></div>
                <div class="item"><a href="mkt_banhang.php">Marketing - Bán hàng</a> <span>NEW</span></div>
                <div class="item"><a href="taichinhcanhan.php">Tài chính cá nhân</a> <span>NEW</span></div>
                <div class="item"><a href="pt_canhan.php">Phát triển cá nhân</a> <span>NEW</span></div>
                <div class="item"><a href="doanh_nhan.php">Doanh nhân - Bài học KD</a> <span>NEW</span></div>
                <div class="item"><a href="suckhoe_lamdep.php">Sức khỏe - Làm đẹp</a></div>
                <div class="item"><a href="khoahoc_congnghe.php">Khoa học - Công nghệ</a></div>
                <div class="item"><a href="tuduy_sangtao.php">Tư duy sáng tạo</a> <span>NEW</span></div>
                <div class="item"><a href="giaoduc_vanhoa.php">Giáo dục - Văn hóa & Xã</a></div>
                <div class="item"><a href="nghe_thuat_song.php">Nghệ thuật sống</a> <span>NEW</span></div>
                <div class="item"><a href="tamlinh.php">Tâm linh - Tôn giáo</a></div>
                <div class="item"><a href="chungkhoan_bds_dautu.php">Chứng khoán - BĐS - Đầu tư</a></div>
                <div class="item"><a href="sach_ngoai_van.php">Sách Ngoại văn</a> <span>NEW</span></div>
                </div>
            </li>

            <li>
                <a href="#">Truyện</a>
                <div class="mega-menu">
                    <div class="item"><a href="nam.php">Nam</a></div>
                <div class="item"><a href="nu.php">Nữ</a></div>
                <div class="item"><a href="xuyenkhong.php">Xuyên không</a></div>
                <div class="item"><a href="truyenma.php">Truyện ma</a></div>
                <div class="item"><a href="tinhcam.php">Tình cảm</a></div>
                <div class="item"><a href="ngungon.php">Ngụ ngôn</a></div>
                <div class="item"><a href="codai.php">Cổ đại</a></div>
                <div class="item"><a href="thieunhi.php">Thiếu nhi</a></div>
                <div class="item"><a href="haihuoc.php">Hài </a></div>
                <div class="item"><a href="hanhdong.php">Hành động</a></div>
                </div>
            </li>

            
        </ul>
    </nav>
              <div class="buttons">
    <form action="timkiem.php" method="GET" class="search-form">
        <input type="text" name="q" placeholder="Tìm tên truyện..." required>
        <button type="submit" class="btn-timkiem">
            <i class="fas fa-search"></i> Tìm kiếm
        </button>
    </form>
</div>
<div class="user-area">
    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-profile" id="userProfile">
            
            <i class="fas fa-caret-down"></i>
                 <i class="fa-solid fa-user"></i>
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-info">
                    <div class="info-text">
                        <strong><?php echo $_SESSION['username']; ?></strong>
                        
                    </div>
                    <i class="fa-solid fa-user"></i>
                </div>
                
                
                <ul class="dropdown-menu-list">
                    <li><a href="taikhoan.php"><i class="fas fa-user-cog"></i>  Tài khoản</a></li>
                    <li><a href="tusach.php"><i class="fas fa-book"></i> Tủ sách cá nhân</a></li>
                    <hr>
                    <li>
                    <a href="dangxuat.php" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php else: ?>
      <button class="btn-dangky" id="openRegisterModal">Đăng ký</button> 
        
      <button class="btn-dangnhap" id="openRegisterModal2">Đăng nhập</button> 
    <?php endif; ?>
</div>
    </header>
    <section class="hero">
    <div class="hero-left">
        <p class="tag">WEKE ĐỀ XUẤT</p>
        <h1>Tự do tài chính từ bên trong</h1>
        <p class="desc">
            Học cách quản lý tiền bạc, kiểm soát tài chính cá nhân và sống thoải mái hơn.
        </p>
        <button class="read-btn">📖 Đọc sách</button>
    </div>

    <div class="swiper mySwiper">
    <div class="swiper-wrapper">

        <?php foreach ($books as $book): ?>
            <div class="swiper-slide">
                <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>">
                            <img src="../code/images/<?= $book['cover'] ?>">

                </a>
            </div>
        <?php endforeach; ?>

    </div>
</div>
</section>
<section class="content">
    <div class="welcome">
           <div class="welcome">
        <div class="container">

            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): ?>

                    <div class="book">

                        <!-- link đọc -->
                            <a href="../backend/read_story.php?story_id=<?= $book['id'] ?>">
                            <img src="../code/images/<?= $book['cover'] ?>">
                            <div class="title">
                                <?= htmlspecialchars($book['title']) ?>
                            </div>
                        </a>

                        <!-- nút lưu -->
                        <form action="luutruyen.php" method="POST">
                            <input type="hidden" name="story_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </form>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <p>Không có truyện</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    centeredSlides: true,
    loop: true,
    spaceBetween: 30,

    autoplay: {
        delay: 2000,
        disableOnInteraction: false,
    },

    effect: "coverflow",
    grabCursor: true,

    coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 100,
        modifier: 2,
        slideShadows: false,
    },
});
</script>
<div id="registerModal" class="modal" style="display: none;"> 
    <?php include 'dangky_form.php'; ?> 
</div>

<div id="loginModal" class="modal" style="display: none;"> 
    <?php include 'dangnhap_form.php'; ?> 
</div>

<?php if (!empty($message)): ?>
    <script>
        alert("<?php echo addslashes($message); ?>");
    </script>
<?php endif; ?>

<script src="../backend/script.js"></script>
</body>
</html>