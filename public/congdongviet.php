<?php
session_start();
include 'dangky_logic.php'; 
include 'dangnhap_logic.php';
?>
<!DOCTYPE html>
<php lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/user.css">
    <link rel="stylesheet" href="assets/css/d.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
   <header>
  <div class="logo"><a href="home.php">WEKE</a></div>
  <nav>
    <ul>
      <li>
        <a href="#">Sách điện tử</a>
        <div class="mega-menu">
            <h3>Sách điện tử</h3>
            <div class="item"><a href="tho_tanvan.php">Thơ - Tản văn</a> </div>
            <div class="item"><a href="trinhtham.php">Trinh thám - Kinh dị</a> <span>NEW</span></div>
            <div class="item"><a href="mkt_banhang.php">Marketing - Bán hàng</a> <span>NEW</span></div>
            <div class="item"><a href="taichinh_canhan.php">Tài chính cá nhân</a> <span>NEW</span></div>
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
        <a href="#">Sách hiệu sồi</a>
        <div class="mega-menu">
            <h3>Sách hiệu </h3>
            <div class="item"><a href="hiendai.php">Hiện đại</a></div>
            <div class="item"><a href="codaii.php">Cổ đại</a></div>
            <div class="item"><a href="huyenhuyen.php">Huyền huyễn</a></div>
        </div>
      </li>
    <li>
        <a href="">Sách nói</a>
        <div class="mega-menu">
            <h3>Sách nói</h3>
            <div class="item"><a href="ngontinh.php">Ngôn tình</a></div>
            <div class="item"><a href="tt_kinhdi.php">Trinh thám - Kinh dị</a></div>
            <div class="item"><a href="phattrien.php">Phát triển cá nhân </a></div>
            <div class="item"><a href="vientuong.php">Viễn tưởng giả tưởng </a></div>
            <div class="item"><a href="tpkinhdien.php">Tác phẩm kinh điển</a></div>
            <div class="item"><a href="tamly_suckhoe_tinhthan.php">Tâm lý-Sức khỏe-Tinh thần</a></div>
            <div class="item"><a href="tamlinh_tongiao.php">Tâm linh-Tôn giáo</a></div>
            <div class="item"><a href="sachthieunhi.php">Sách thiếu nhi</a></div>
            <div class="item"><a href="truyen_tieuthuyet.php">Truyện-Tiểu thuyết</a></div>
            <div class="item"><a href="marketing_banhang.php">Marketing-Bán hàng</a></div>
            <div class="item"><a href="chungkhoan_bds_dautu.php">Chứng khoán-Bất động sản-Đầu tư</a></div>
    </li>
    <li>
        <a href="#">Truyện tranh</a>
        <div class="mega-menu">
            <h3>Truyện tranh</h3>
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
 <section class="ranking-section">
    <div class="ranking-header">
      <h2>Bảng xếp hạng</h2>
      <nav class="ranking-tabs">
        <a href="docnhieu.php" class="active">Đọc nhiều</a>
        <a href="nghenhieu.php">Nghe nhiều</a>
        <a href="postcard.php">Podcast</a>
        <a href="congdongviet.php">Cộng đồng viết</a>
      </nav>
    </div>
      <div class="propose">
        <div class="propose-container">
          <div class="book">
            <a href="read_story.php?story_id=444"
              ><img src="v1.jpg" alt="Bìa sách" />
              
              <div class="title">Âm thanh và cuồng nộ
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="444">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=445"
              ><img src="v2.jpg" alt="Bìa sách" />
              
              <div class="title">Ác mộng trong đêm
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="445">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=446"
              ><img src="v3.png" alt="Bìa sách" />
              
              <div class="title">Dặm xanh
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="446">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=447"
              ><img src="v4.jpg" alt="Bìa sách" />
              
              <div class="title">451 độ F
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="447">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=448"
              ><img src="v5.png" alt="Bìa sách" />
             
              <div class="title">Cỗ máy thời gian
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="448">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
           
          </div>

          <div class="book">
            <a href="read_story.php?story_id=449"
              ><img src="v6.jpg" alt="Bìa sách" />
              
              <div class="title">Thời khắc
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="449">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=450">
              <img src="v7.jpg" alt="Bìa sách" />
              
              <div class="title">Người sao chổi
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="450">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=451">
              <img src="p7.png" alt="Bìa sách" />
              
              <div class="title">Yêu đúng người sẽ được hạnh phúc
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="451">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>

          <div class="book">
            <a href="read_story.php?story_id=452"
              ><img src="p10.png" alt="Bìa sách" />
              
              <div class="title">Giác ngộ tài chính
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="452">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
          
          </div>

          <div class="book">
            <a href="read_story.php?story_id=453"
              ><img src="p9.jpg" alt="Bìa sách" />
              
              <div class="title">Làm chủ thời gian sống
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="453">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
            
          </div>
        </div>
      </div>
    </section>
    <div class="latest">
      <div class="propose">
        <div class="propose-header">
          <h2>Weke đề xuất</h2>
        </div>
        <div class="propose-container">
          <div class="book">
            <a href="read_story.php?story_id=454"
              ><img src="m3.jpg" alt="Bìa sách" />
              
              <div class="title">Miếu ba cô
                <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="454">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=455"
              ><img src="n2.jpg" alt="Bìa sách" />
              
              <div class="title">Mượn em một lần yêu
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="455">
              <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=456"
              ><img src="t2.jpg" alt="Bìa sách" />
              
              <div class="title">Trò chơi đoạt mạng
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="456">
              <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=457"
              ><img src="s4.jpg" alt="Bìa sách" />
              
              <div class="title">Cảm xúc cùng lúc là trí khôn
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="457">
              <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=458"
              ><img src="t6.jpg" alt="Bìa sách" />
              
              <div class="title">Ác duyên
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="458">
              <button type="submit" class="btn-save">
                 <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=459"
              ><img src="d6.png" alt="Bìa sách" />
              
              <div class="title">Mười người da đen nhỏ
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="459">
              <button type="submit" class="btn-save">
                 <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=460">
              <img src="v7.jpg" alt="Bìa sách" />
              
              <div class="title">Người sao chổi
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="460">
              <button type="submit" class="btn-save">
                 <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=461">
              <img src="v2.jpg" alt="Bìa sách" />
             
              <div class="title">Ác mộng trong đêm
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="461">
              <button type="submit" class="btn-save">
                 <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=462"
              ><img src="n1.png" alt="Bìa sách" />
             
              <div class="title">Để người thấu hiểu lòng tôi
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="462">
              <button type="submit" class="btn-save">
                 <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>

          <div class="book">
            <a href="read_story.php?story_id=463"
              ><img src="m2.jpg" alt="Bìa sách" />
              
              <div class="title">Sức mạnh của chánh niệm
              <form action="luutruyen.php" method="POST">
              <input type="hidden" name="story_id" value="463">
              <button type="submit" class="btn-save">
                 <i class="fa-solid fa-heart"></i>
              </button>
              </form>
              </div></a
            >
          </div>
        </div>
      </div>
      <footer>
        <div class="footer-top">
          <!-- Logo + Contact -->
          <div class="footer-logo">
            <h2>WEKE</h2>
            <p>Công ty cổ phần sách điện tử Weke</p>
            <p><i class="fa fa-phone"></i> 0877736289</p>
            <p><i class="fa fa-envelope"></i> Support@weke.vn</p>
          </div>

          
        </div>

        <!-- Bottom text -->
        <div class="footer-bottom">
          Công ty Cổ phần Sách điện tử Weke – Tầng 6, Tháp văn phòng quốc tế Hòa
          Bình, số 106 đường Hoàng Quốc Việt, Phường Nghĩa Đô, Thành phố Hà Nội,
          Việt Nam.<br />
          ĐKKD số 0108796796 do SKHĐT TP Hà Nội cấp lần đầu ngày 24/06/2019.<br />
          Giấy xác nhận Đăng ký hoạt động phát hành xuất bản phẩm điện tử số
          8132/XN-CXBIPH do Cục Xuất bản, In và Phát hành cấp ngày
          31/12/2019.<br />
          Giấy chứng nhận Đăng ký kết nối để cung cấp dịch vụ nội dung thông tin
          trên mạng viễn thông di động số 91/GCN-CVT cấp ngày 24/03/2025.<br />
        </div>
      </footer>
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

<script src="script.js"></script>
  </body>
</html>
