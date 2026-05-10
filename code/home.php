<?php
session_start();
// 1. Chạy logic xử lý đăng ký trước khi php bắt đầu
include 'dangky_logic.php'; 
include 'dangnhap_logic.php';
?>

<!DOCTYPE html>
<html lang="en">
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
    <div class="ranking-header">
      <h2>Bảng xếp hạng</h2>
      <nav class="ranking-tabs">
        <a href="docnhieu.php" class="active">Đọc nhiều</a>
        <a href="nghenhieu.php">Nghe nhiều</a>
        <a href="postcard.php">Podcast</a>
        <a href="congdongviet.php">Cộng đồng viết</a>
      </nav>
    </div>

    <div class="ranking-container">
      <!-- Book 1 -->
      <div class="book">
        <a href="read_story.php?story_id=344"><img src="images/1.jpg" alt="">
        <div class="title">Mưa đỏ
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="344">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         </div></a>
        <div class="rank-number">1</div>
        
      </div>
      <!-- Book 2 -->
      <div class="book">
        <a href="read_story.php?story_id=345"><img src="2.png" alt="">
        <div class="title">Tiểu yêu tinh của Bạc gia
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="345">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         </div></a>
          <div class="rank-number">2</div>
        
      </div>
      <!-- Book 3 -->
      <div class="book">
        <a href="read_story.php?story_id=346"><img src="3.png" alt="">
        <div class="title">Cô vợ nhỏ của ngài Phó
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="346">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">3</div>
        
      </div>
      <!-- Book 4 -->
      <div class="book">
        <a href="read_story.php?story_id=347"><img src="images/4.jpg" alt="">
        <div class="title">Siêu cấp cưng chiều
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="347">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">4</div>
        
      </div>
      <!-- Book 5 -->
      <div class="book">
         <a href="read_story.php?story_id=348"><img src="5.png" alt="">
        <div class="title">Bảo bối của ngài tống
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="348">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">5</div>
        
      </div>

      <div class="book">
         <a href="read_story.php?story_id=349"><img src="images/b.jpg" alt="">
        <div class="title">Cưng chiều cô vợ nhỏ quân nhân
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="349">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">6</div>
        
      </div>

      <div class="book">
        <a href="read_story.php?story_id=350"><img src="images/c.jpg" alt="">
        <div class="title">Tính cách con người
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="350">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">7</div>
        
      </div>

      <div class="book">
        <a href="read_story.php?story_id=351"><img src="images/a.jpg" alt="">
        <div class="title">Đắc nhân tâm
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="351">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">8</div>
        
      </div>

      <div class="book">
        <a href="read_story.php?story_id=352"><img src="images/d.jpg" alt="">
        <div class="title">[Sách ngoại văn] Steppenwolf
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="352">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">9</div>
        
      </div>
      
      <div class="book">
        <a href="read_story.php?story_id=353"><img src="images/e.jpg" alt="">
        <div class="title">Bắt đầu cho câu hỏi tại sao
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="353">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        <div class="rank-number">10</div>
        
      </div>
      
    </div>
  </section>
  <div class="latest">
  <div class="latest-header">
    <h2>Mới nhất </h2>
  </div>
    <div class="latest-container">
    <div class="book">
        <a href="read_story.php?story_id=354"><img src="6.png" alt="">
        <div class="title">Lấy chàng kĩ sư
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="354">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        
        
    </div>

    <div class="book">
        <a href="read_story.php?story_id=354"><img src="images/7.jpg" alt="">
        <div class="title">Lấy chàng kĩ sư
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="354">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        
        
    </div>

    <div class="book">
        <a href="read_story.php?story_id=355"><img src="images/8.jpg" alt="">
        <div class="title">Peter Pan & Wendy
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="355">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        
    </div>

    
      <div class="book">
        <a href="read_story.php?story_id=357"><img src="images/9.jpg" alt="">
        <div class="title">Nơi vết thương ánh sáng rọi vào
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="357">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        
    </div>
    
      <div class="book">
        <a href="read_story.php?story_id=358"><img src="10.png" alt="">
        <div class="title">Tiểu yêu tinh của Bạc gia
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="358">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
        
        
    </div>
      
      <div class="book">
         <a href="read_story.php?story_id=359"><img src="images/11.jpg" alt="">
        <div class="title">Lớn lên con muốn làm tỉ phú
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="359">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
         <a href="read_story.php?story_id=360"><img src="images/12.jpg" alt="">
        <div class="title">Người lạ với chính ta
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="360">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
         <a href="read_story.php?story_id=361"><img src="images/13.jpg" alt="">
        <div class="title">A Journal of the Plague
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="361">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
         <a href="read_story.php?story_id=362"><img src="images/14.jpg" alt="">
        <div class="title">Anne of Anonlea
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="362">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=363"><img src="images/15.jpg" alt="">
        <div class="title">Hell mode-Triệu hồi sư khởi 
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="363">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>
  </div>
  <div class="propose">
    <div class="propose-header">
      <h2>WEKE đề xuất </h2>
    </div>
    <div class="propose-container ">
      <div class="book">
      <a href="read_story.php?story_id=364"><img src="images/a1.jpg" alt="">
        <div class="title">Wabi Sabi
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="364">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=365"><img src="images/a2.jpg" alt="">
        <div class="title">Mặt dày tâm đen
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="365">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=366"><img src="images/a3.jpg" alt="">
        <div class="title">Tư duy lởm khởm
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="366">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=367"><img src="images/a22.jpg" alt="">
        <div class="title">Đam mê không để làm cảnh, đam mê là để ra tiền
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="367">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
       <a href="read_story.php?story_id=368"><img src="a5.png" alt="">
        <div class="title">Những lời giáo huấn của Epictetus
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="368">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=369"><img src="images/a6.jpg" alt="">
        <div class="title">Nguồi cội
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="369">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=370"><img src="images/a8.jpg" alt="">
        <div class="title">Cất tiền làm điếng thế gian
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="370">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=371"><img src="images/a9.jpg" alt="">
        <div class="title">Cậu chuyện từ trái tim 
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="371">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=372"><img src="images/a10.jpg" alt="">
        <div class="title">Tâm lượng như biển
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="372">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=373"><img src="images/a11.jpg" alt="">
        <div class="title">Con đường tỷ phú
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="373">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>
      
    </div>
    </div>
    <div class="propose">
      <div class="propose-header">
        <h2>Sống tối giản cùng WEKE</h2>
    </div>

    <div class="propose-container ">
      <div class="book">
        <a href="read_story.php?story_id=374"><img src="images/e1.jpg" alt="">
        <div class="title">Thiết kế một cuộc đời 
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="374">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=375"><img src="images/e2.jpg" alt="">
        <div class="title"> Sống nhẹ nhàng hạnh phúc đơn giản
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="375">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=376"><img src="images/e3.jpg" alt="">
        <div class="title">Sống tối giản tối thiểu để được tối đa
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="376">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=377"><img src="images/e7.jpg" alt="">
        <div class="title">Bàn về cách sống
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="377">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=378"><img src="images/e5.jpg" alt="">
        <div class="title">Hạnh phúc từ những điều nhỏ bé
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="378">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=379"><img src="images/e6.jpg" alt="">
        <div class="title">Dọn dẹp tối giản
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="379">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
       <a href="read_story.php?story_id=380"><img src="images/e8.jpg" alt="">
        <div class="title">Chữa lành lối
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="380">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
       <a href="read_story.php?story_id=381"><img src="images/e9.jpg" alt="">
        <div class="title">Tại sao cần đơn giản
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="381">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=382"><img src="images/e10.jpg" alt="">
        <div class="title">Một cuốn sách về chủ nghĩ tối giản
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="382">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
        </button>
        </form>
         
        </div></a>
      </div>

      <div class="book">
        <a href="read_story.php?story_id=383"><img src="images/e11.jpg" alt="">
        <div class="title">Nhà không rác
        <form action="luutruyen.php" method="POST" >
        <input type="hidden" name="story_id" value="383">
        <button type="submit" class="btn-save">
            <i class="fa-solid fa-heart" ></i>
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
        Công ty Cổ phần Sách điện tử Weke – Tầng 6, Tháp văn phòng quốc tế Hòa Bình, số 106 đường Hoàng Quốc Việt, Phường Nghĩa Đô, Thành phố Hà Nội, Việt Nam.<br>
        ĐKKD số 0108796796 do SKHĐT TP Hà Nội cấp lần đầu ngày 24/06/2019.<br>
        Giấy xác nhận Đăng ký hoạt động phát hành xuất bản phẩm điện tử số 8132/XN-CXBIPH do Cục Xuất bản, In và Phát hành cấp ngày 31/12/2019.<br>
        Giấy chứng nhận Đăng ký kết nối để cung cấp dịch vụ nội dung thông tin trên mạng viễn thông di động số 91/GCN-CVT cấp ngày 24/03/2025.<br>
      
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
