<?php
session_start();
include 'dangky_logic.php'; 
include 'dangnhap_logic.php';
?>
<!DOCTYPE php>
<php lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="d.css">
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
      <div class="propose">
        <div class="propose-header">
          <h2>Trinh thám - Kinh dị</h2>
        </div>
        <div class="propose-container">
		<!-- Book 1 -->
          <div class="book">
            <a href="read_story.php?story_id=145"><img src="t1.jpg" alt="Bìa sách" />
            <div class="title">Con thú mù
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="145">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
	<!-- Book 2-->
         <div class="book">
            <a href="read_story.php?story_id=146"><img src="t2.jpg" alt="Bìa sách" />
            <div class="title">Trò chơi đoạt mạng
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="146">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 3 -->
          <div class="book">
            <a href="read_story.php?story_id=147"><img src="t3.jpg" alt="Bìa sách" />
            <div class="title">Cổ vật huyền bí
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="147">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 4 -->
          <div class="book">
            <a href="read_story.php?story_id=148"><img src="t4.jpg" alt="Bìa sách" />
            <div class="title">Cầu cơ
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="148">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 5 -->
          <div class="book">
            <a href="read_story.php?story_id=149"><img src="t5.jpg" alt="Bìa sách" />
            <div class="title">Đi đêm lắm có ngày gặp ma
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="149">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 6 -->
          <div class="book">
            <a href="read_story.php?story_id=150"><img src="t6.jpg" alt="Bìa sách" />
            <div class="title">Ác duyên
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="150">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 7 -->
          <div class="book">
            <a href="read_story.php?story_id=151"><img src="t7.jpg" alt="Bìa sách" />
            <div class="title">Tổng tài kể chuyên lúc 0h
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="151">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 8 -->
          <div class="book">
            <a href="read_story.php?story_id=152"><img src="t8.jpg" alt="Bìa sách" />
            <div class="title">Tịch chiếu
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="152">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 9 -->
          <div class="book">
            <a href="read_story.php?story_id=153"><img src="t9.jpg" alt="Bìa sách" />
            <div class="title">Trùng tang
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="153">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
		</div>
<!-- Book 10 -->
          <div class="book">
            <a href="read_story.php?story_id=154"><img src="t10.jpg" alt="Bìa sách" />
            <div class="title">Nhật ký pháp y
              <form action="luutruyen.php" method="POST">
                <input type="hidden" name="story_id" value="154">
                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-heart"></i>
                </button>
              </form>
			
			</div></a>
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
    </div>
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
</html>
