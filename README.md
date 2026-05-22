# KEWE — Nền tảng đọc sách & truyện online

Website đọc sách/truyện trực tuyến viết bằng **PHP thuần** và **MySQL**, phù hợp đồ án / BTL môn Lập trình web. Người dùng có thể đọc miễn phí một số chương đầu, mua chương bằng coin, lưu sách vào tủ sách, bình luận; quản trị viên quản lý truyện, chương và người dùng.

---

## Tính năng chính

### Người đọc
- Đăng ký / đăng nhập / đăng xuất (mật khẩu mã hóa `password_hash`)
- Trang chủ, danh mục sách (Thơ, Trinh thám, Tài chính, …)
- Đọc truyện theo chương, chuyển chương trước/sau
- **3 chương đầu miễn phí**, từ chương 4 trở đi mua bằng coin (3 coin/chương)
- Nạp coin (mô phỏng, không tích hợp cổng thanh toán thật)
- Tủ sách cá nhân (lưu / bỏ lưu truyện)
- Tìm kiếm theo tên sách — **AJAX** (gợi ý trên header + trang tìm kiếm đầy đủ)
- Bình luận truyện (có trả lời)

### Quản trị viên
- Dashboard, thống kê cơ bản
- Quản lý truyện (thêm / sửa / xóa, upload ảnh bìa)
- Quản lý chương, người dùng

---

## Công nghệ sử dụng

| Thành phần | Công nghệ |
|------------|-----------|
| Backend | PHP 7.4+ (mysqli) |
| Database | MySQL / MariaDB, utf8mb4 |
| Frontend | HTML5, CSS3, JavaScript (Fetch API) |
| Thư viện UI | Font Awesome 6, Swiper 11 |

---

## Cấu trúc thư mục

```
doanchuyende/
├── backend/              # Xử lý logic, API, đọc chương
│   ├── search_ajax.php   # API tìm kiếm JSON
│   ├── buy_chapter.php   # Mua chương bằng coin
│   ├── topup_coin.php    # Nạp coin
│   ├── read_story.php    # Trang chi tiết truyện
│   ├── read_chapter.php  # Trang đọc chương
│   └── ...
├── frontend/             # Giao diện người dùng
│   ├── home.php          # Trang chủ
│   ├── timkiem.php       # Tìm kiếm (AJAX)
│   ├── admin/            # Panel quản trị
│   ├── css/
│   ├── js/
│   │   └── search-ajax.js
│   └── _category_template.php
└── database/
    ├── connect.php       # Kết nối DB (dùng chính)
    ├── db_connect.php    # Tạo DB/bảng lần đầu (tùy chọn)
    └── update_schema.sql # Migration bổ sung
```

---

## Yêu cầu hệ thống

- [XAMPP](https://www.apachefriends.org/) (hoặc WAMP/Laragon) với **Apache** + **MySQL**
- PHP **7.4** trở lên (khuyến nghị 8.x)
- Trình duyệt hiện đại (Chrome, Edge, Firefox)

---

## Cài đặt

### 1. Clone / copy project

Đặt thư mục project vào `htdocs` (ví dụ):

```
D:\xampp\htdocs\doanchuyende
```

### 2. Bật dịch vụ

Mở **XAMPP Control Panel** → Start **Apache** và **MySQL**.

### 3. Cấu hình database

Mặc định trong `database/connect.php`:

| Tham số | Giá trị |
|---------|---------|
| Host | `localhost` |
| User | `root` |
| Password | *(để trống)* |
| Database | `db_BTL5` |

**Cách 1 — Tự tạo khi chạy lần đầu**

Truy cập một trang có include `database/db_connect.php` hoặc mở project; file này có thể tạo database và các bảng cơ bản.

**Cách 2 — Import thủ công**

1. Mở [phpMyAdmin](http://localhost/phpmyadmin)
2. Tạo database `db_BTL5`, collation `utf8mb4_unicode_ci`
3. Chạy script trong `database/db_connect.php` (logic CREATE TABLE) hoặc `database/update_schema.sql` cho bảng coin/bình luận

**Cột `role` cho admin** (nếu chưa có):

```sql
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user';
UPDATE users SET role = 'admin' WHERE username = 'ten_admin';
```

### 4. Ảnh bìa sách

- Ảnh mặc định đặt tại: `code/images/` (đường dẫn tương đối từ `frontend/`)
- Ảnh upload từ admin lưu tại: `backend/uploads/`

Nếu thiếu thư mục `code/images`, tạo thủ công và thêm ảnh mẫu hoặc upload qua admin.

### 5. Truy cập website

| Trang | URL |
|-------|-----|
| Trang chủ | http://localhost/doanchuyende/frontend/home.php |
| Tìm kiếm | http://localhost/doanchuyende/frontend/timkiem.php |
| Admin | http://localhost/doanchuyende/frontend/admin/index.php |

*(Đổi `doanchuyende` nếu tên thư mục của bạn khác.)*

---

## Tài khoản demo

Tạo tài khoản qua form **Đăng ký** trên trang chủ.

Tài khoản **admin**: sau khi đăng ký, gán `role = 'admin'` trong bảng `users` (xem SQL ở trên), rồi đăng nhập lại.

---

## API tìm kiếm AJAX

```
GET /backend/search_ajax.php?q={từ_khóa}&limit={số_lượng}
```

| Tham số | Mô tả |
|---------|--------|
| `q` | Từ khóa (tối thiểu 2 ký tự) |
| `limit` | Số kết quả tối đa (1–50, mặc định 12) |

**Ví dụ:**

```
http://localhost/doanchuyende/backend/search_ajax.php?q=thơ&limit=8
```

**Phản hồi JSON:**

```json
{
  "success": true,
  "count": 2,
  "keyword": "thơ",
  "items": [
    {
      "id": 1,
      "title": "Tên sách",
      "cover": "../code/images/ten_anh.jpg",
      "category": "Thơ - Tản văn",
      "url": "../backend/read_story.php?story_id=1"
    }
  ]
}
```

---

## Mô hình coin (demo)

| Quy tắc | Giá trị |
|---------|---------|
| Chương miễn phí | 3 chương đầu |
| Giá mỗi chương | 3 coin |
| Tỷ giá nạp (demo) | 1 coin = 10 VND |
| Gói nạp | 10, 30, 50, 100, 200, 500 coin |

> **Lưu ý:** Nạp coin hiện chỉ **mô phỏng** (cộng coin khi submit form), chưa tích hợp VNPay/Momo.

---

## Danh mục sách (`stories.description`)

Cột `description` trong bảng `stories` được dùng làm **mã danh mục**, ví dụ:

| Mã | Trang |
|----|--------|
| `home` | Trang chủ (nổi bật) |
| `tho` | Thơ - Tản văn |
| `trinhtham` | Trinh thám |
| `taichinh` | Tài chính cá nhân |
| … | Các file `frontend/*.php` tương ứng |

---

## Hạn chế đã biết

- Một số API backend admin chưa kiểm tra session admin đầy đủ — chỉ nên dùng trong môi trường demo/đồ án.
- Chưa có CSRF token trên form POST.
- Nạp coin không phải thanh toán thật.
- Có nhiều file kết nối DB (`connect.php`, `db_connect.php`) — nên thống nhất cấu hình tại `database/connect.php`.

---

## Phát triển thêm (gợi ý)

- [ ] Middleware `require_admin.php` cho toàn bộ API admin
- [ ] Tách cấu hình DB ra `config.php` / biến môi trường
- [ ] Tích hợp VNPay / Momo cho nạp coin
- [ ] Bảng `categories` riêng thay vì dùng `description`
- [ ] File `.gitignore` (bỏ qua `uploads/`, config local)

---

## Tác giả

- Họ tên: *(điền tên nhóm / cá nhân)*
- Lớp / Trường: *(điền thông tin đồ án)*
- Năm: 2025–2026

---

## Giấy phép

Dự án phục vụ mục đích **học tập / đồ án**. Vui lòng không sử dụng cho mục đích thương mại khi chưa hoàn thiện bảo mật.
