# KEWE — Nền tảng đọc sách & truyện online

Website đọc sách/truyện trực tuyến xây dựng bằng **PHP thuần** và **MySQL**, phục vụ đồ án / BTL môn **Lập trình Web**. Hệ thống cho phép người dùng đọc truyện theo chương, mua chương trả phí bằng coin, nạp coin qua QR (demo), lưu truyện vào tủ sách, bình luận; quản trị viên quản lý truyện, chương, người dùng và xem thống kê.

---

## Mục lục

1. [Tính năng](#tính-năng)
2. [Công nghệ](#công-nghệ)
3. [Cấu trúc thư mục](#cấu-trúc-thư-mục)
4. [Cơ sở dữ liệu](#cơ-sở-dữ-liệu)
5. [Luồng nghiệp vụ](#luồng-nghiệp-vụ)
6. [Cài đặt & chạy](#cài-đặt--chạy)
7. [Tài khoản & phân quyền](#tài-khoản--phân-quyền)
8. [Cấu hình](#cấu-hình)
9. [API & endpoint chính](#api--endpoint-chính)
10. [Hạn chế & ghi chú](#hạn-chế--ghi-chú)

---

## Tính năng

### Người đọc (`role = user`)

| Chức năng | Mô tả |
|-----------|--------|
| Đăng ký / đăng nhập / đăng xuất | Mật khẩu mã hóa `password_hash`; tài khoản `banned` không đăng nhập được |
| Trang chủ & danh mục | Banner, lọc theo thể loại (Thơ, Trinh thám, Tài chính, …) |
| Đọc truyện | Trang chi tiết (`read_story.php`) + đọc chương (`read_chapter.php`) |
| Paywall | **3 chương đầu miễn phí**, từ chương 4 mua bằng **3 coin/chương** |
| Nạp coin | Chọn gói → tạo đơn → quét QR VietQR (demo) → xác nhận cộng coin |
| Tủ sách | Lưu / bỏ lưu truyện yêu thích |
| Tìm kiếm | Gợi ý AJAX trên header + trang `timkiem.php` |
| Bình luận | Bình luận gốc & trả lời; xóa comment của chính mình |
| Tài khoản | Xem coin, lịch sử giao dịch, thông tin cá nhân |

### Quản trị viên (`role = admin`)

| Chức năng | Mô tả |
|-----------|--------|
| Dashboard | Tổng user, truyện, lượt xem, bình luận |
| Thống kê | Top truyện theo `luot_xem`, % user active, cơ cấu thể loại |
| Quản lý truyện | CRUD, upload ảnh bìa, lọc theo tên/trạng thái |
| Quản lý chương | Thêm/sửa/xóa chương theo truyện |
| Quản lý user | CRUD, khóa (`banned`) / mở khóa, phân quyền |
| Bypass paywall | Admin đọc mọi chương trả phí **không cần mua** (chỉ để kiểm duyệt nội dung) |

> **Lưu ý:** Admin **không** dùng Nạp coin / Tủ sách — chỉ quản lý hệ thống.

---

## Công nghệ

| Thành phần | Công nghệ |
|------------|-----------|
| Backend | PHP 7.4+ (mysqli, prepared statements) |
| Database | MySQL / MariaDB, `utf8mb4` |
| Frontend | HTML5, CSS3, JavaScript (Fetch API) |
| Thư viện | Font Awesome 6, Swiper 11 |
| Thanh toán demo | VietQR (`img.vietqr.io`) — không tích hợp cổng thật |

---

## Cấu trúc thư mục

```
btl chuyen de/
├── backend/                    # Logic xử lý, API, trang đọc
│   ├── require_admin.php       # Guard admin (pages + API JSON)
│   ├── require_auth.php        # Guard user đăng nhập + status active
│   ├── story_config.php        # FREE_CHAPTERS, COINS_PER_CHAPTER, mã danh mục
│   ├── payment_config.php      # Cấu hình VietQR, gói nạp coin
│   ├── dangnhap_logic.php      # Xử lý đăng nhập
│   ├── dangky_logic.php        # Xử lý đăng ký
│   ├── logout.php
│   ├── read_story.php          # Chi tiết truyện, bình luận, luot_xem
│   ├── read_chapter.php        # Đọc chương, paywall
│   ├── buy_chapter.php         # Mua chương bằng coin
│   ├── topup_create_order.php  # Tạo đơn nạp coin
│   ├── topup_confirm_paid.php  # Xác nhận thanh toán demo
│   ├── topup_coin.php          # Redirect legacy → napcoin.php
│   ├── search_ajax.php         # API tìm kiếm JSON
│   ├── add/edit/delete_*.php   # CRUD admin (story, chapter, user)
│   └── uploads/                # Ảnh bìa upload từ admin
│
├── frontend/
│   ├── home.php                # Trang chủ (modal đăng nhập/đăng ký)
│   ├── timkiem.php             # Tìm kiếm đầy đủ
│   ├── napcoin.php             # Chọn gói nạp coin
│   ├── thanhtoan.php           # Trang QR thanh toán
│   ├── taikhoan.php            # Thông tin tài khoản
│   ├── tusach.php              # Tủ sách cá nhân
│   ├── luutruyen.php           # API lưu truyện (POST)
│   ├── _category_template.php  # Template danh mục dùng chung
│   ├── tho_tanvan.php, trinhtham.php, …  # Trang theo thể loại
│   ├── includes/paths.php      # app_url(), app_login_url()
│   ├── admin/                  # Panel quản trị
│   │   ├── index.php           # Dashboard
│   │   ├── thongke.php         # Thống kê chi tiết
│   │   ├── stories.php         # Quản lý truyện
│   │   ├── chapter.php         # Quản lý chương
│   │   └── users.php           # Quản lý người dùng
│   ├── css/
│   └── js/search-ajax.js
│
├── database/
│   ├── connect.php             # Kết nối DB + auto-migration (dùng chính)
│   ├── db_connect.php          # Tạo DB/bảng lần đầu (bootstrap)
│   └── update_schema.sql       # Migration bổ sung (coin, orders, …)
│
├── code/images/                # Ảnh bìa mặc định hiển thị trên site
└── README.md
```

---

## Cơ sở dữ liệu

**Database:** `db_BTL5` (cấu hình tại `database/connect.php`)

### Bảng chính

| Bảng | Mô tả |
|------|--------|
| `users` | Tài khoản: username, email, sdt, password, coins, role, status |
| `stories` | Truyện: title, description (mã danh mục), cover, status, luot_xem |
| `chapters` | Chương: story_id, title, content, chapter_number |
| `user_stories` | Truyện đã lưu vào tủ sách |
| `purchased_chapters` | Chương đã mua bằng coin |
| `coin_transactions` | Lịch sử nạp/tiêu coin |
| `topup_orders` | Đơn nạp coin (pending / paid) |
| `comments` | Bình luận (hỗ trợ parent_id cho reply) |

### Auto-migration

File `database/connect.php` tự thêm cột/bảng thiếu khi chạy (coins, role, status, luot_xem, purchased_chapters, …) — phù hợp môi trường demo trên XAMPP.

---

## Luồng nghiệp vụ

### Đăng nhập

```
User → home.php (modal) → POST dangnhap_logic.php
  → Kiểm tra password + status ≠ banned
  → Set session (user_id, username, role)
  → Redirect về trang trước (napcoin, read_chapter, …) hoặc home
```

Trang yêu cầu đăng nhập dùng `require_active_user()` — user bị ban giữa phiên sẽ bị logout ngay.

### Đọc & mua chương

```
read_story.php → tăng luot_xem (GET)
read_chapter.php → chương ≤ 3: free
                 → chương > 3: kiểm tra purchased_chapters hoặc khóa paywall
                 → admin: bypass paywall
buy_chapter.php  → trừ coin (transaction) + ghi purchased_chapters
```

Hằng số nghiệp vụ tập trung tại `backend/story_config.php`:

```php
FREE_CHAPTERS = 3
COINS_PER_CHAPTER = 3
```

### Nạp coin (demo VietQR)

```
napcoin.php → POST topup_create_order.php → thanhtoan.php?order_id=…
           → Hiển thị QR VietQR (img.vietqr.io)
           → POST topup_confirm_paid.php → cộng coin + ghi coin_transactions
           → Quay lại napcoin.php?success=1
```

> Đây là **mô phỏng**: người dùng bấm xác nhận đã chuyển khoản, hệ thống cộng coin — **không** có webhook ngân hàng thật.

### Admin CRUD

```
frontend/admin/*.php → require_admin()
backend/*_story|chapter|user.php → require_admin_api()
Xóa truyện → cascade: chapters, comments, user_stories (transaction)
```

---

## Cài đặt & chạy

### Yêu cầu

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL) hoặc WAMP / Laragon
- PHP **7.4+** (khuyến nghị 8.x)
- Trình duyệt Chrome / Edge / Firefox

### Bước 1 — Copy project

Đặt thư mục vào `htdocs`, ví dụ:

```
C:\xampp\htdocs\btl chuyen de
```

Hoặc tạo **junction/symlink** trỏ tới thư mục gốc (URL ngắn hơn):

```
http://localhost/btl-nhom1-chuyendedinhhuong/frontend/home.php
```

### Bước 2 — Bật dịch vụ

XAMPP Control Panel → **Start Apache** và **MySQL**.

### Bước 3 — Database

**Cách 1 (khuyến nghị):** Truy cập bất kỳ trang nào có include `database/connect.php` — hệ thống tự migrate.

**Cách 2:** Chạy `database/db_connect.php` một lần để tạo DB + bảng gốc, sau đó import `database/update_schema.sql` nếu cần.

**Cấu hình mặc định** (`database/connect.php`):

| Tham số | Giá trị |
|---------|---------|
| Host | `localhost` |
| User | `root` |
| Password | *(trống)* |
| Database | `db_BTL5` |

### Bước 4 — Ảnh bìa

- Ảnh có sẵn: `code/images/`
- Ảnh upload admin: `backend/uploads/`

Tạo thư mục `code/images/` nếu chưa có và thêm ảnh mẫu.

### Bước 5 — Truy cập

| Trang | URL mẫu |
|-------|---------|
| Trang chủ | `http://localhost/btl chuyen de/frontend/home.php` |
| Tìm kiếm | `…/frontend/timkiem.php` |
| Nạp coin | `…/frontend/napcoin.php` |
| Admin | `…/frontend/admin/index.php` |

*(Thay path theo tên thư mục hoặc junction của bạn.)*

---

## Tài khoản & phân quyền

### Tạo user thường

Form **Đăng ký** trên trang chủ (modal) hoặc admin thêm user tại `admin/users.php`.

### Tạo admin

1. Đăng ký tài khoản bình thường
2. Trong phpMyAdmin:

```sql
UPDATE users SET role = 'admin' WHERE username = 'ten_tai_khoan';
```

3. Đăng nhập lại

### Phân quyền

| Role | Quyền |
|------|--------|
| `user` | Đọc, mua chương, nạp coin, tủ sách, bình luận |
| `admin` | Toàn bộ panel admin; **không** nạp coin / tủ sách |

### Khóa tài khoản

Admin đặt `status = 'banned'` → user không đăng nhập được; session cũ bị chặn khi truy cập trang bảo vệ.

---

## Cấu hình

### Coin & paywall — `backend/story_config.php`

```php
define('FREE_CHAPTERS', 3);      // Số chương miễn phí đầu
define('COINS_PER_CHAPTER', 3);  // Giá mỗi chương trả phí
```

### Thanh toán demo — `backend/payment_config.php`

```php
PAYMENT_BANK          // Mã ngân hàng (vd: MB)
PAYMENT_ACCOUNT       // Số tài khoản nhận
PAYMENT_ACCOUNT_NAME  // Tên chủ TK
payment_valid_packs() // [10, 30, 50, 100, 200, 500] coin
```

Tỷ giá demo: **1 coin = 10 VND**.

### Mã danh mục — `stories.description`

Cột `description` lưu **mã thể loại** (không phải mô tả dài). Ví dụ:

| Mã | Trang frontend |
|----|----------------|
| `home` | Trang chủ / nổi bật |
| `tho` | `tho_tanvan.php` |
| `trinhtham` | `trinhtham.php` |
| `taichinh` | `taichinhcanhan.php` |
| `tinhcam` | `tinhcam.php` |
| `nam` / `nu` | `nam.php` / `nu.php` |
| … | Xem đầy đủ trong `story_config.php` |

---

## API & endpoint chính

### Tìm kiếm AJAX

```
GET /backend/search_ajax.php?q={từ_khóa}&limit={1-50}
```

Phản hồi JSON: `{ success, count, keyword, items[] }`.

### Nạp coin

| Method | File | Mô tả |
|--------|------|--------|
| POST | `topup_create_order.php` | Tạo đơn, redirect `thanhtoan.php` |
| POST | `topup_confirm_paid.php` | Xác nhận demo, cộng coin |

### Mua chương

| Method | File | Mô tả |
|--------|------|--------|
| POST | `buy_chapter.php` | Trừ coin, ghi `purchased_chapters` |

### Admin API (JSON, cần session admin)

| File | Chức năng |
|------|-----------|
| `add/edit/delete_story.php` | CRUD truyện |
| `edit_chapter.php`, `delete_chapter.php` | CRUD chương |
| `add/edit/delete_user.php` | CRUD user |

---

## Hạn chế & ghi chú

| Hạng mục | Ghi chú |
|----------|---------|
| Thanh toán | Demo VietQR — không xác minh chuyển khoản thật |
| Bảo mật | Chưa có CSRF token; phù hợp môi trường đồ án / localhost |
| Ảnh bìa | Upload admin (`backend/uploads/`) và hiển thị (`code/images/`) có thể cần đồng bộ path thủ công |
| `topup_coin.php` | File legacy, chỉ redirect sang `napcoin.php` |

---

## Kiểm thử nhanh (checklist)

- [ ] Đăng ký / đăng nhập / đăng xuất
- [ ] Đọc 3 chương đầu miễn phí; chương 4 yêu cầu coin
- [ ] Nạp coin qua QR → coin tăng
- [ ] Mua chương → đọc được nội dung
- [ ] Lưu truyện vào tủ sách
- [ ] Bình luận & trả lời
- [ ] Admin: CRUD truyện/chương/user, xem thống kê
- [ ] User `banned` không đăng nhập / bị chặn khi đang online
- [ ] `luot_xem` tăng khi mở trang truyện

---

## Tác giả

| | |
|---|---|
| **Đề tài** | Xây dựng website đọc sách/truyện online KEWE |
| **Môn học** | Chuyên đề định hướnghướng |
| **Nhóm** | Nhóm 1|
| **Giảng viên hướng dẫndẫn** | *Ths. Ngô Ngọc AnhAnh* |
| **Năm học** | 2025 – 2026 |

---

## Giấy phép

Dự án phục vụ mục đích **học tập / đồ án chuyên đề**. Không khuyến khích triển khai production khi chưa hoàn thiện bảo mật và tích hợp thanh toán thật.
