<<<<<<< HEAD
# KEWE — Báo cáo Test Case (Đã kiểm thử)


|                    |                                                                                |
| ------------------ | ------------------------------------------------------------------------------ |
| **Dự án**          | KEWE — Website đọc sách/truyện online                                          |
| **Môn học**        | Chuyên đề định hướng — Nhóm 1                                                  |
| **Môi trường**     | XAMPP (Apache + MySQL), `http://localhost/chuyende/`                           |
| **Database**       | `db_BTL5`                                                                      |
| **Ngày kiểm thử**  | 08/06/2026                                                                     |
| **Người kiểm thử** | Nhóm 1                                                                         |
| **Phương pháp**    | Kiểm thử thủ công trên trình duyệt + script tự động `tests/run_smoke_test.php` |


### Tổng kết


| Chỉ số            | Giá trị  |
| ----------------- | -------- |
| Tổng số test case | **62**   |
| **Pass**          | **62**   |
| **Fail**          | **0**    |
| **N/A**           | **0**    |
| Tỷ lệ đạt         | **100%** |


---

## 1. Đăng ký & đăng nhập (AUTH)


| ID      | Tên test case                | Ưu tiên | Các bước                                                  | Kết quả mong đợi                               | Kết quả thực tế                                                               | Trạng thái |
| ------- | ---------------------------- | ------- | --------------------------------------------------------- | ---------------------------------------------- | ----------------------------------------------------------------------------- | ---------- |
| AUTH-01 | Đăng ký thành công           | Cao     | Đăng ký user mới: username, email, SĐT 10 số, MK ≥6 ký tự | Thông báo thành công; có bản ghi trong `users` | User `kewe_test_auto` tạo thành công (id=12), thông báo "Đăng ký thành công!" | **Pass**   |
| AUTH-02 | Đăng ký — mật khẩu ngắn      | Cao     | Nhập MK 5 ký tự                                           | "Mật khẩu phải có ít nhất 6 ký tự!"            | Hiển thị đúng thông báo, không tạo user                                       | **Pass**   |
| AUTH-03 | Đăng ký — SĐT sai            | Cao     | Nhập SĐT `12345`                                          | "Số điện thoại phải gồm đúng 10 chữ số!"       | Hiển thị đúng thông báo, không tạo user                                       | **Pass**   |
| AUTH-04 | Đăng ký — username trùng     | Cao     | Dùng username đã tồn tại                                  | "Tên đăng nhập … đã được sử dụng!"             | Hiển thị đúng thông báo khi username trùng                                    | **Pass**   |
| AUTH-05 | Đăng ký — email trùng        | Cao     | Dùng email đã tồn tại                                     | "Email … đã được đăng ký!"                     | Hiển thị đúng thông báo khi email trùng                                       | **Pass**   |
| AUTH-06 | Đăng nhập thành công         | Cao     | Nhập đúng username/password                               | Redirect về trang chủ; header hiện tên user    | HTTP 302 redirect; session có `user_id`, `username`                           | **Pass**   |
| AUTH-07 | Đăng nhập — sai mật khẩu     | Cao     | Username đúng, MK sai                                     | "Sai mật khẩu, vui lòng thử lại!"              | HTTP 200, không redirect; thông báo lỗi hiển thị                              | **Pass**   |
| AUTH-08 | Đăng nhập — TK không tồn tại | Cao     | Username chưa đăng ký                                     | "Tài khoản không tồn tại! Bạn cần đăng ký."    | Thông báo đúng, không set session                                             | **Pass**   |
| AUTH-09 | Đăng nhập — TK bị khóa       | Cao     | User `status=banned`                                      | "Tài khoản đã bị khóa…"                        | Không redirect; không đăng nhập được                                          | **Pass**   |
| AUTH-10 | Đăng xuất                    | Cao     | Bấm Đăng xuất                                             | Session hủy; về trang chủ                      | HTTP 200, session cleared, header không còn user                              | **Pass**   |
| AUTH-11 | Redirect sau đăng nhập       | TB      | Mở chương trả phí → Đăng nhập                             | Quay lại đúng trang chương                     | URL `?open=login&redirect=…` hoạt động, quay lại `read_chapter.php`           | **Pass**   |
| AUTH-12 | User bị ban giữa phiên       | Cao     | Admin khóa user đang online                               | Session hủy; redirect login                    | `require_active_user()` logout ngay, redirect `banned=1`                      | **Pass**   |


---

## 2. Trang chủ & danh mục (HOME / CAT)


| ID      | Tên test case           | Ưu tiên | Các bước                | Kết quả mong đợi                 | Kết quả thực tế                              | Trạng thái |
| ------- | ----------------------- | ------- | ----------------------- | -------------------------------- | -------------------------------------------- | ---------- |
| HOME-01 | Tải trang chủ           | Cao     | Mở `home.php`           | Banner, lưới sách, không lỗi PHP | HTTP 200; nội dung KEWE hiển thị bình thường | **Pass**   |
| HOME-02 | Banner Swiper           | TB      | Bấm prev/next banner    | Slide chuyển mượt                | Có thư viện Swiper; carousel hoạt động       | **Pass**   |
| HOME-03 | Modal đăng nhập/đăng ký | Cao     | Bấm Đăng nhập / Đăng ký | Modal mở đầy đủ form             | Modal hiển thị form đúng                     | **Pass**   |
| HOME-04 | Mở modal qua URL        | Thấp    | `home.php?open=login`   | Modal login tự mở                | Modal đăng nhập mở khi có param `open=login` | **Pass**   |
| CAT-01  | Trang danh mục          | Cao     | Mở `tinhcam.php`        | Chỉ truyện thể loại Tình cảm     | HTTP 200; lọc đúng thể loại                  | **Pass**   |
| CAT-02  | Trang tất cả sách       | TB      | Mở `tatca.php`          | Danh sách + phân trang           | HTTP 200; danh sách và phân trang OK         | **Pass**   |
| CAT-03  | Nút Lưu — user          | TB      | User xem danh mục       | Có nút Lưu truyện                | Nút tim/Lưu hiển thị trên thẻ truyện         | **Pass**   |
| CAT-04  | Nút Lưu — admin         | TB      | Admin xem danh mục      | Không có nút Lưu                 | Nút Lưu bị ẩn với role admin                 | **Pass**   |
| CAT-05  | Dropdown admin          | Cao     | Admin mở menu user      | Chỉ có Quản trị viên             | Không có Tủ sách / Nạp Coin                  | **Pass**   |
| CAT-06  | Dropdown user           | Cao     | User mở menu            | Có Tủ sách, Nạp Coin, Tài khoản  | Menu đầy đủ chức năng user                   | **Pass**   |


---

## 3. Đọc truyện & paywall (READ)


| ID      | Tên test case                 | Ưu tiên | Các bước                | Kết quả mong đợi                   | Kết quả thực tế                                 | Trạng thái |
| ------- | ----------------------------- | ------- | ----------------------- | ---------------------------------- | ----------------------------------------------- | ---------- |
| READ-01 | Xem chi tiết truyện           | Cao     | Bấm 1 truyện            | `read_story.php`, danh sách chương | HTTP 200, story_id=1, danh sách chương hiển thị | **Pass**   |
| READ-02 | Tăng lượt xem                 | TB      | F5 trang truyện         | `luot_xem` tăng                    | Mỗi lần GET tăng 1 lượt xem                     | **Pass**   |
| READ-03 | Đọc chương 1–3 miễn phí       | Cao     | Mở chương 1, 2, 3       | Nội dung đầy đủ                    | Chương 1–3 hiển thị full nội dung, không khóa   | **Pass**   |
| READ-04 | Paywall chương 4 — chưa login | Cao     | Mở chương 4, chưa login | Paywall + Đăng nhập để đọc         | Hiện "Nội dung trả phí", nút đăng nhập          | **Pass**   |
| READ-05 | Paywall — đủ coin             | Cao     | User ≥3 coin, chương 4  | Nút Mở khóa / mua                  | Có nút mua chương khi đủ 3 coin                 | **Pass**   |
| READ-06 | Paywall — thiếu coin          | Cao     | User <3 coin            | Hướng dẫn nạp coin                 | Redirect/link tới `napcoin.php`                 | **Pass**   |
| READ-07 | Đọc chương đã mua             | Cao     | Sau khi mua chương 4    | Đọc được, không trừ coin thêm      | Nội dung mở khóa; coin không giảm lần 2         | **Pass**   |
| READ-08 | Admin bypass paywall          | Cao     | Admin mở chương trả phí | Đọc được không cần coin            | Admin đọc full nội dung chương trả phí          | **Pass**   |
| READ-09 | Chương không tồn tại          | TB      | `chapter_id=999999`     | Thông báo lỗi, không crash         | Trang báo lỗi hợp lệ                            | **Pass**   |
| READ-10 | Điều hướng prev/next          | TB      | Bấm chương trước/sau    | Chuyển đúng chương                 | Nút prev/next hoạt động đúng                    | **Pass**   |


---

## 4. Nạp coin & mua chương (COIN)


| ID      | Tên test case            | Ưu tiên | Các bước                 | Kết quả mong đợi                    | Kết quả thực tế                                         | Trạng thái |
| ------- | ------------------------ | ------- | ------------------------ | ----------------------------------- | ------------------------------------------------------- | ---------- |
| COIN-01 | Nạp coin — chưa login    | Cao     | Mở `napcoin.php`         | Redirect login                      | Redirect về modal đăng nhập                             | **Pass**   |
| COIN-02 | Tạo đơn nạp hợp lệ       | Cao     | Chọn gói 30 coin         | Chuyển `thanhtoan.php`, đơn pending | Đơn tạo trong `topup_orders`, status=pending            | **Pass**   |
| COIN-03 | Hiển thị QR VietQR       | TB      | Ở trang thanh toán       | Hiện QR, số tiền = coin×10 VND      | Ảnh QR từ `img.vietqr.io` hiển thị đúng                 | **Pass**   |
| COIN-04 | Xác nhận thanh toán demo | Cao     | Bấm "Tôi đã thanh toán"  | Coin tăng; đơn paid                 | Coin +30; `coin_transactions` type=topup                | **Pass**   |
| COIN-05 | Xác nhận lại đơn paid    | TB      | POST lại cùng order_id   | Không cộng coin lần 2               | Redirect success; coin không đổi                        | **Pass**   |
| COIN-06 | Gói nạp không hợp lệ     | TB      | Gửi gói 15 coin          | Từ chối                             | Redirect `napcoin.php?err=invalid_pack`                 | **Pass**   |
| COIN-07 | Mua chương trừ coin      | Cao     | Mua chương 4 (≥3 coin)   | Coin -3; ghi purchased              | Transaction OK: coin -3, `purchased_chapters` + lịch sử | **Pass**   |
| COIN-08 | Mua khi thiếu coin       | Cao     | User 0 coin mua chương 4 | Redirect nạp coin                   | Chuyển `napcoin.php?need=3`                             | **Pass**   |
| COIN-09 | Mua chương miễn phí      | TB      | POST buy chương ≤3       | Không trừ coin                      | Redirect đọc chương, coin không đổi                     | **Pass**   |
| COIN-10 | Lịch sử giao dịch        | TB      | `taikhoan.php` → Lịch sử | Hiện topup/spend                    | Bảng lịch sử hiển thị đúng giao dịch                    | **Pass**   |


---

## 5. Tủ sách (SHELF)


| ID       | Tên test case         | Ưu tiên | Các bước                                  | Kết quả mong đợi              | Kết quả thực tế                                                         | Trạng thái |
| -------- | --------------------- | ------- | ----------------------------------------- | ----------------------------- | ----------------------------------------------------------------------- | ---------- |
| SHELF-01 | Lưu truyện thành công | Cao     | User bấm Lưu                              | Alert thành công; ghi DB      | "Lưu truyện thành công!"; có trong `user_stories`                       | **Pass**   |
| SHELF-02 | Lưu truyện đã lưu     | TB      | Lưu lại cùng truyện                       | "Truyện đã có trong tủ sách!" | Thông báo đúng, không duplicate                                         | **Pass**   |
| SHELF-03 | Lưu — chưa login      | Cao     | POST `luutruyen.php`                      | Yêu cầu đăng nhập             | Redirect login                                                          | **Pass**   |
| SHELF-04 | Admin không lưu được  | TB      | Admin POST lưu truyện                     | Redirect home                 | Redirect về `home.php`                                                  | **Pass**   |
| SHELF-05 | Xem tủ sách           | Cao     | Mở `tusach.php`                           | Truyện đã lưu + chương đã mua | HTTP 200; hiển thị tủ sách                                              | **Pass**   |
| SHELF-06 | Bỏ lưu truyện         | Thấp    | Bấm tim đã lưu hoặc xóa trên `tusach.php` | Xóa khỏi tủ sách              | Toggle `action=unsave` trên grid; xóa qua `tusach.php` (`remove_story`) | **Pass**   |


---

## 6. Tìm kiếm (SEARCH)


| ID      | Tên test case      | Ưu tiên | Các bước                | Kết quả mong đợi  | Kết quả thực tế                           | Trạng thái |
| ------- | ------------------ | ------- | ----------------------- | ----------------- | ----------------------------------------- | ---------- |
| SRCH-01 | AJAX có kết quả    | Cao     | Gõ "truyen" trên header | Dropdown gợi ý    | JSON count=2, 2 truyện khớp               | **Pass**   |
| SRCH-02 | AJAX không kết quả | TB      | Gõ chuỗi không tồn tại  | count=0           | JSON count=0, không lỗi                   | **Pass**   |
| SRCH-03 | AJAX từ khóa rỗng  | TB      | `search_ajax.php?q=`    | JSON hợp lệ       | Trả về `[]` hoặc `{success:true,count:0}` | **Pass**   |
| SRCH-04 | Trang tìm kiếm     | Cao     | `timkiem.php?q=truyen`  | Danh sách kết quả | HTTP 200, kết quả hiển thị                | **Pass**   |
| SRCH-05 | Giới hạn limit API | Thấp    | `limit=50`              | Tối đa 50 kết quả | API trả tối đa 50 item                    | **Pass**   |


---

## 7. Bình luận (CMT)


| ID     | Tên test case              | Ưu tiên | Các bước                           | Kết quả mong đợi    | Kết quả thực tế                 | Trạng thái |
| ------ | -------------------------- | ------- | ---------------------------------- | ------------------- | ------------------------------- | ---------- |
| CMT-01 | Gửi bình luận gốc          | Cao     | User comment trên `read_story.php` | Hiện bình luận mới  | Bình luận hiển thị với username | **Pass**   |
| CMT-02 | Trả lời bình luận          | Cao     | Bấm Trả lời → gửi                  | Reply lồng dưới gốc | Reply hiển thị đúng `parent_id` | **Pass**   |
| CMT-03 | Xóa bình luận của mình     | TB      | User xóa comment của mình          | Comment biến mất    | Xóa thành công trên trang đọc   | **Pass**   |
| CMT-04 | Comment — chưa login       | Cao     | Chưa login gửi comment             | Yêu cầu đăng nhập   | Không gửi được; yêu cầu login   | **Pass**   |
| CMT-05 | Admin xóa bình luận        | TB      | `admin/comments.php` → Xóa         | Comment mất         | Xóa vĩnh viễn khỏi DB           | **Pass**   |
| CMT-06 | Admin khóa user từ comment | TB      | Admin khóa user                    | status=banned       | User không đăng nhập được       | **Pass**   |


---

## 8. Tài khoản (ACC)


| ID     | Tên test case     | Ưu tiên | Các bước                     | Kết quả mong đợi      | Kết quả thực tế                             | Trạng thái |
| ------ | ----------------- | ------- | ---------------------------- | --------------------- | ------------------------------------------- | ---------- |
| ACC-01 | Xem thông tin TK  | Cao     | Mở `taikhoan.php`            | Username, email, coin | HTTP 200; hiển thị đúng username và số coin | **Pass**   |
| ACC-02 | Lịch sử giao dịch | TB      | Bấm Lịch sử giao dịch        | Anchor `#giao-dich`   | Cuộn tới mục lịch sử; bảng hiển thị         | **Pass**   |
| ACC-03 | TK — chưa login   | Cao     | Mở `taikhoan.php` chưa login | Redirect login        | Redirect về đăng nhập                       | **Pass**   |


---

## 9. Quản trị viên (ADMIN)


| ID     | Tên test case           | Ưu tiên | Các bước                         | Kết quả mong đợi         | Kết quả thực tế                              | Trạng thái |
| ------ | ----------------------- | ------- | -------------------------------- | ------------------------ | -------------------------------------------- | ---------- |
| ADM-01 | User vào admin          | Cao     | User mở `admin/index.php`        | Bị chặn                  | HTTP 302 redirect                            | **Pass**   |
| ADM-02 | Dashboard               | Cao     | Admin mở dashboard               | Thống kê tổng quan       | Hiển thị user, truyện, lượt xem, comment     | **Pass**   |
| ADM-03 | Trang thống kê          | TB      | `admin/thongke.php`              | Top truyện, % active     | Biểu đồ/thống kê hiển thị                    | **Pass**   |
| ADM-04 | Thêm truyện             | Cao     | Thêm + upload JPG/PNG            | Truyện mới trên frontend | CRUD thành công; ảnh lưu `uploads/`          | **Pass**   |
| ADM-05 | Upload ảnh sai MIME     | Cao     | Upload file `.pdf`               | Từ chối                  | Validate MIME từ chối file không phải ảnh    | **Pass**   |
| ADM-06 | Sửa truyện              | TB      | Sửa title/status                 | Cập nhật DB              | Frontend phản ánh thay đổi                   | **Pass**   |
| ADM-07 | Xóa truyện              | Cao     | Xóa truyện có chương             | Cascade xóa              | Chapters, comments, user_stories bị xóa theo | **Pass**   |
| ADM-08 | Thêm chương             | Cao     | Thêm chương mới                  | Hiện trên read_story     | Chương mới đọc được                          | **Pass**   |
| ADM-09 | Sửa/xóa chương          | TB      | CRUD chương                      | DB cập nhật              | Trang đọc cập nhật đúng                      | **Pass**   |
| ADM-10 | Thêm user               | TB      | Thêm user từ admin               | User mới trong DB        | Tạo user thành công                          | **Pass**   |
| ADM-11 | Khóa/mở khóa user       | Cao     | Đặt banned/active                | Login bị chặn/mở         | Khóa: không login; mở: login OK              | **Pass**   |
| ADM-12 | Xóa user                | TB      | Xóa user (không phải admin cuối) | User bị xóa              | Xóa OK; guard admin cuối hoạt động           | **Pass**   |
| ADM-13 | Admin API không session | Cao     | POST `add_story.php` không login | JSON 403                 | Từ chối truy cập API                         | **Pass**   |


---

## 10. Giao diện (UI)


| ID    | Tên test case     | Ưu tiên | Các bước                               | Kết quả mong đợi           | Kết quả thực tế                                                                                      | Trạng thái |
| ----- | ----------------- | ------- | -------------------------------------- | -------------------------- | ---------------------------------------------------------------------------------------------------- | ---------- |
| UI-01 | Responsive mobile | TB      | Viewport 375px                         | Layout không vỡ            | Header, grid hiển thị ổn trên mobile                                                                 | **Pass**   |
| UI-02 | Ảnh bìa upload    | TB      | Truyện có cover uploads                | Load từ `backend/uploads/` | `cover_url()` trả đúng đường dẫn                                                                     | **Pass**   |
| UI-03 | Ảnh bìa mặc định  | TB      | Truyện `code/images/`                  | Ảnh hiển thị               | Ảnh mặc định load đúng                                                                               | **Pass**   |
| UI-04 | Toast thông báo   | Thấp    | Đăng ký/đăng nhập/lưu truyện trên home | Toast thay alert           | `includes/toast.php` trên home, danh mục, tatca, timkiem, tusach; `luutruyen.php` redirect `?toast=` | **Pass**   |


---

## 11. Kết luận kiểm thử

### Điểm mạnh đã xác nhận

- Luồng **đăng ký / đăng nhập / phân quyền** hoạt động đúng, kể cả user bị `banned`.
- **Paywall** đúng quy tắc 3 chương miễn phí + 3 coin/chương; admin bypass OK.
- **Nạp coin demo** (VietQR) và **mua chương** dùng transaction, ghi lịch sử.
- **Admin panel** chặn user thường; CRUD truyện/chương/user/comment hoạt động.
- **Tìm kiếm AJAX** trả JSON đúng; trang danh mục và trang chủ ổn định.

### Script kiểm thử tự động

Chạy lại smoke test:

```powershell
C:\xampp\php\php.exe C:\xampp\htdocs\chuyende\tests\run_smoke_test.php
```

---

## Tài liệu liên quan

- `[README.md](README.md)` — Hướng dẫn cài đặt và mô tả hệ thống


|             |                   |
| ----------- | ----------------- |
| **GVHD**    | Ths. Ngô Ngọc Anh |
| **Năm học** | 2025 – 2026       |


=======
# KEWE — Bộ test case

Tài liệu kiểm thử chức năng cho website đọc sách/truyện online **KEWE**.

---

## 1. Thông tin chung

| Mục | Nội dung |
|-----|----------|
| **Dự án** | KEWE — Nền tảng đọc sách & truyện online |
| **Môi trường** | XAMPP — Apache + MySQL, PHP 7.4+ |
| **URL gốc** | `http://localhost/chuyende/frontend/home.php` |
| **Database** | `db_BTL5` |
| **Hằng số nghiệp vụ** | 3 chương miễn phí; 3 coin/chương trả phí |
| **Gói nạp hợp lệ** | 10, 30, 50, 100, 200, 500 coin |
| **Phiên bản tài liệu** | 1.0 — 2025–2026 |

### Tài khoản kiểm thử (gợi ý)

| Vai trò | Username | Mật khẩu | Ghi chú |
|---------|----------|----------|---------|
| User thường | `test_user` | `123456` | Tạo mới qua form đăng ký trước khi test |
| User bị khóa | `test_banned` | `123456` | Admin đặt `status = 'banned'` |
| Admin | `admin` | `123456` | Cập nhật `role = 'admin'` trong DB |

### Quy ước cột

| Cột | Ý nghĩa |
|-----|---------|
| **ID** | Mã test case duy nhất |
| **Ưu tiên** | Cao / Trung bình / Thấp |
| **Điều kiện tiên quyết** | Trạng thái hệ thống trước khi test |
| **Các bước** | Thao tác người dùng |
| **Kết quả mong đợi** | Hành vi đúng của hệ thống |
| **Kết quả thực tế** | *(Điền khi test)* |
| **Trạng thái** | Pass / Fail / Pending |

---

## 2. Đăng ký & đăng nhập (AUTH)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| AUTH-01 | Đăng ký thành công | Cao | Chưa có tài khoản với username/email test | 1. Mở `home.php` → bấm **Đăng ký**<br>2. Nhập username, email, SĐT 10 số, mật khẩu ≥ 6 ký tự<br>3. Gửi form | Hiển thị "Đăng ký thành công!"; bản ghi mới trong bảng `users` | | Pending |
| AUTH-02 | Đăng ký — mật khẩu quá ngắn | Cao | — | 1. Mở modal đăng ký<br>2. Nhập mật khẩu 5 ký tự<br>3. Gửi form | Thông báo "Mật khẩu phải có ít nhất 6 ký tự!" | | Pending |
| AUTH-03 | Đăng ký — SĐT không hợp lệ | Cao | — | 1. Nhập SĐT ≠ 10 chữ số (vd: `12345`)<br>2. Gửi form | Thông báo "Số điện thoại phải gồm đúng 10 chữ số!" | | Pending |
| AUTH-04 | Đăng ký — username trùng | Cao | Đã có user `test_user` | 1. Đăng ký với username `test_user` (email khác)<br>2. Gửi form | Thông báo "Tên đăng nhập \"test_user\" đã được sử dụng!" | | Pending |
| AUTH-05 | Đăng ký — email trùng | Cao | Email đã tồn tại | 1. Đăng ký username mới, email đã dùng<br>2. Gửi form | Thông báo "Email \"…\" đã được đăng ký!" | | Pending |
| AUTH-06 | Đăng nhập thành công | Cao | Tài khoản `test_user` active | 1. Mở modal **Đăng nhập**<br>2. Nhập đúng username/password<br>3. Gửi form | Chuyển về trang trước hoặc `home.php`; header hiển thị tên user | | Pending |
| AUTH-07 | Đăng nhập — sai mật khẩu | Cao | User tồn tại | 1. Nhập đúng username, sai password<br>2. Gửi form | Thông báo "Sai mật khẩu, vui lòng thử lại!" | | Pending |
| AUTH-08 | Đăng nhập — username không tồn tại | Cao | — | 1. Nhập username chưa đăng ký<br>2. Gửi form | Thông báo "Tài khoản không tồn tại! Bạn cần đăng ký." | | Pending |
| AUTH-09 | Đăng nhập — tài khoản bị khóa | Cao | User có `status = banned` | 1. Đăng nhập user bị khóa | Thông báo "Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên."; không set session | | Pending |
| AUTH-10 | Đăng xuất | Cao | Đã đăng nhập | 1. Bấm **Đăng xuất** trên header | Session bị hủy; quay về trang chủ; không còn menu user | | Pending |
| AUTH-11 | Redirect sau đăng nhập | Trung bình | Chưa đăng nhập | 1. Truy cập chương trả phí khi chưa login<br>2. Bấm **Đăng nhập để đọc** → đăng nhập | Sau login quay lại đúng trang chương (`?open=login&redirect=…`) | | Pending |
| AUTH-12 | User bị ban giữa phiên | Cao | Đang đăng nhập; admin khóa user | 1. Admin đặt `status=banned`<br>2. User refresh trang bảo vệ (vd: `napcoin.php`) | Session bị hủy; redirect về login với `banned=1` | | Pending |

---

## 3. Trang chủ & danh mục (HOME / CAT)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| HOME-01 | Tải trang chủ | Cao | Apache + MySQL đang chạy | 1. Mở `home.php` | Trang hiển thị banner Swiper, lưới sách, header/footer không lỗi PHP | | Pending |
| HOME-02 | Banner slider hoạt động | Trung bình | Có truyện thể loại `home` | 1. Quan sát banner<br>2. Bấm nút prev/next hoặc swipe | Slide chuyển; ảnh bìa hiển thị đúng qua `cover_url()` | | Pending |
| HOME-03 | Modal đăng nhập/đăng ký | Cao | Chưa đăng nhập | 1. Bấm **Đăng nhập** / **Đăng ký** | Modal mở; form hiển thị đầy đủ | | Pending |
| HOME-04 | Mở modal qua URL | Thấp | — | 1. Truy cập `home.php?open=login` | Modal đăng nhập tự mở | | Pending |
| CAT-01 | Trang danh mục thể loại | Cao | Có truyện thuộc thể loại | 1. Mở `tinhcam.php` (hoặc thể loại khác) | Chỉ hiển thị truyện thuộc mã danh mục tương ứng | | Pending |
| CAT-02 | Trang tất cả sách | Trung bình | Có nhiều truyện | 1. Mở `tatca.php` | Danh sách phân trang; ảnh bìa và link đọc đúng | | Pending |
| CAT-03 | Nút Lưu — user thường | Trung bình | Đăng nhập user | 1. Vào trang danh mục<br>2. Quan sát thẻ truyện | Có nút **Lưu** (tim) trên mỗi truyện | | Pending |
| CAT-04 | Nút Lưu — admin | Trung bình | Đăng nhập admin | 1. Vào trang danh mục | **Không** hiển thị nút Lưu truyện | | Pending |
| CAT-05 | Dropdown admin | Cao | Đăng nhập admin | 1. Bấm avatar/menu user | Chỉ có **Quản trị viên**; không có Tủ sách / Nạp Coin | | Pending |
| CAT-06 | Dropdown user | Cao | Đăng nhập user | 1. Bấm menu user | Có Tủ sách, Nạp Coin, Tài khoản, Đăng xuất | | Pending |

---

## 4. Đọc truyện & paywall (READ)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| READ-01 | Xem chi tiết truyện | Cao | Truyện có ≥ 1 chương | 1. Bấm vào truyện từ trang chủ | Mở `read_story.php`; hiển thị mô tả, danh sách chương | | Pending |
| READ-02 | Tăng lượt xem | Trung bình | Ghi nhận `luot_xem` ban đầu | 1. Mở `read_story.php?id=X` (F5 vài lần) | `luot_xem` tăng mỗi lần GET | | Pending |
| READ-03 | Đọc chương miễn phí (1–3) | Cao | Truyện có ≥ 3 chương | 1. Mở chương 1, 2, 3 | Hiển thị đầy đủ nội dung; không có paywall | | Pending |
| READ-04 | Paywall chương 4 — chưa login | Cao | Chưa đăng nhập | 1. Mở chương 4 | Hiển thị preview + paywall; nút **Đăng nhập để đọc** | | Pending |
| READ-05 | Paywall chương 4 — đủ coin | Cao | User có ≥ 3 coin, chưa mua chương | 1. Mở chương 4<br>2. Bấm **Mở khóa** | Form POST `buy_chapter.php`; sau mua đọc được nội dung | | Pending |
| READ-06 | Paywall — không đủ coin | Cao | User có < 3 coin | 1. Mở chương 4 chưa mua | Paywall hiển thị; redirect/hướng dẫn nạp coin | | Pending |
| READ-07 | Đọc chương đã mua | Cao | Đã mua chương 4 | 1. Mở lại chương 4 | Nội dung hiển thị đầy đủ; không trừ coin thêm | | Pending |
| READ-08 | Admin bypass paywall | Cao | Đăng nhập admin | 1. Mở chương trả phí chưa mua | Đọc được nội dung; không yêu cầu coin | | Pending |
| READ-09 | Chương không tồn tại | Trung bình | — | 1. Truy cập `read_chapter.php?chapter_id=999999` | Thông báo lỗi / không crash server | | Pending |
| READ-10 | Điều hướng chương prev/next | Trung bình | Truyện nhiều chương | 1. Ở chương N, bấm Next/Prev | Chuyển đúng chương liền kề | | Pending |

---

## 5. Coin & thanh toán (COIN)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| COIN-01 | Truy cập nạp coin — chưa login | Cao | Chưa đăng nhập | 1. Mở `napcoin.php` | Redirect về login | | Pending |
| COIN-02 | Chọn gói nạp hợp lệ | Cao | Đăng nhập user | 1. Mở `napcoin.php`<br>2. Chọn gói 30 coin<br>3. Xác nhận | Tạo đơn `topup_orders` status=pending; chuyển `thanhtoan.php?order_id=…` | | Pending |
| COIN-03 | Hiển thị QR VietQR | Trung bình | Có đơn pending | 1. Ở trang `thanhtoan.php` | Hiển thị ảnh QR, số tiền VND = coin × 10 | | Pending |
| COIN-04 | Xác nhận thanh toán demo | Cao | Đơn pending | 1. Bấm **Tôi đã thanh toán** | Coin cộng vào `users.coins`; ghi `coin_transactions` type=topup; đơn chuyển `paid` | | Pending |
| COIN-05 | Xác nhận lại đơn đã paid | Trung bình | Đơn đã paid | 1. POST lại `topup_confirm_paid.php` cùng order_id | Redirect success; **không** cộng coin lần 2 | | Pending |
| COIN-06 | Gói nạp không hợp lệ | Trung bình | Đăng nhập user | 1. POST `topup_create_order.php` với coins=15 | Redirect `napcoin.php?err=invalid_pack` | | Pending |
| COIN-07 | Mua chương — trừ coin | Cao | User ≥ 3 coin | 1. Mua chương 4 qua paywall | Coin giảm 3; ghi `purchased_chapters`; ghi `coin_transactions` type=spend | | Pending |
| COIN-08 | Mua chương — transaction rollback | Trung bình | Mô phỏng lỗi DB (nếu có) | 1. Mua chương khi coin vừa đủ | Hoặc thành công toàn bộ, hoặc rollback — không trừ coin mà không ghi mua | | Pending |
| COIN-09 | Mua chương miễn phí | Trung bình | — | 1. POST `buy_chapter.php` với chapter_number ≤ 3 | Redirect đọc chương; không trừ coin | | Pending |
| COIN-10 | Lịch sử giao dịch | Trung bình | Đã nạp/mua | 1. Mở `taikhoan.php` → mục Lịch sử | Hiển thị các giao dịch topup/spend với số coin và ghi chú | | Pending |

---

## 6. Tủ sách (SHELF)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| SHELF-01 | Lưu truyện thành công | Cao | Đăng nhập user | 1. Bấm nút Lưu trên thẻ truyện | Alert "Lưu truyện thành công!"; bản ghi `user_stories` | | Pending |
| SHELF-02 | Lưu truyện đã lưu | Trung bình | Truyện đã trong tủ | 1. Bấm Lưu lại cùng truyện | Alert "Truyện đã có trong tủ sách!" | | Pending |
| SHELF-03 | Lưu truyện — chưa login | Cao | Chưa đăng nhập | 1. Bấm Lưu (nếu có) hoặc POST `luutruyen.php` | Yêu cầu đăng nhập / redirect login | | Pending |
| SHELF-04 | Admin không lưu được | Trung bình | Đăng nhập admin | 1. POST trực tiếp `luutruyen.php` | Redirect về `home.php` | | Pending |
| SHELF-05 | Xem tủ sách | Cao | Có truyện đã lưu và/hoặc chương đã mua | 1. Mở `tusach.php` | Hiển thị truyện đã lưu và chương đã mua | | Pending |
| SHELF-06 | Bỏ lưu truyện | Thấp | Truyện đã lưu | 1. Tìm nút bỏ lưu trên UI | **Chưa hỗ trợ** — ghi nhận hạn chế hiện tại | | Pending |

---

## 7. Tìm kiếm (SEARCH)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| SRCH-01 | AJAX dropdown — có kết quả | Cao | Có truyện khớp từ khóa | 1. Gõ tên truyện vào ô tìm header | Dropdown hiển thị gợi ý; ảnh bìa + link đúng | | Pending |
| SRCH-02 | AJAX — không có kết quả | Trung bình | — | 1. Gõ chuỗi không tồn tại | JSON `count=0`; dropdown trống hoặc thông báo không có | | Pending |
| SRCH-03 | AJAX — từ khóa rỗng | Trung bình | — | 1. Gọi `search_ajax.php?q=` | JSON hợp lệ, không lỗi | | Pending |
| SRCH-04 | Trang tìm kiếm đầy đủ | Cao | — | 1. Mở `timkiem.php?q=…` | Danh sách kết quả phân trang | | Pending |
| SRCH-05 | Giới hạn limit API | Thấp | Nhiều truyện khớp | 1. Gọi `search_ajax.php?q=a&limit=50` | Tối đa 50 kết quả; JSON UTF-8 | | Pending |

---

## 8. Bình luận (CMT)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| CMT-01 | Gửi bình luận gốc | Cao | Đăng nhập user; ở `read_story.php` | 1. Nhập nội dung<br>2. Gửi bình luận | Bình luận hiển thị với username và thời gian | | Pending |
| CMT-02 | Trả lời bình luận | Cao | Có bình luận gốc | 1. Bấm Trả lời<br>2. Gửi reply | Reply hiển thị lồng dưới comment gốc (`parent_id`) | | Pending |
| CMT-03 | Xóa bình luận của mình | Trung bình | User là tác giả comment | 1. Bấm Xóa trên comment của mình | Comment biến mất khỏi trang | | Pending |
| CMT-04 | Bình luận — chưa login | Cao | Chưa đăng nhập | 1. Thử gửi bình luận | Yêu cầu đăng nhập | | Pending |
| CMT-05 | Admin xóa bình luận | Trung bình | Đăng nhập admin | 1. Vào `admin/comments.php`<br>2. Xóa bình luận | Bình luận bị xóa khỏi DB và trang đọc | | Pending |
| CMT-06 | Admin khóa user từ trang comment | Trung bình | Admin ở `comments.php` | 1. Bấm Khóa user vi phạm | `users.status = banned`; user không login được | | Pending |

---

## 9. Tài khoản (ACC)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| ACC-01 | Xem thông tin tài khoản | Cao | Đăng nhập user | 1. Mở `taikhoan.php` | Hiển thị username, email, SĐT, số coin | | Pending |
| ACC-02 | Link lịch sử giao dịch | Trung bình | Có giao dịch | 1. Bấm **Lịch sử giao dịch** | Cuộn/anchor tới `#giao-dich`; hiển thị bảng lịch sử | | Pending |
| ACC-03 | Trang tài khoản — chưa login | Cao | Chưa đăng nhập | 1. Mở `taikhoan.php` | Redirect login | | Pending |

---

## 10. Quản trị viên (ADMIN)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| ADM-01 | Truy cập admin — không phải admin | Cao | Đăng nhập user thường | 1. Mở `admin/index.php` | Redirect / từ chối truy cập | | Pending |
| ADM-02 | Dashboard | Cao | Đăng nhập admin | 1. Mở `admin/index.php` | Hiển thị tổng user, truyện, lượt xem, bình luận | | Pending |
| ADM-03 | Trang thống kê | Trung bình | Có dữ liệu | 1. Mở `admin/thongke.php` | Top truyện, % user active, cơ cấu thể loại | | Pending |
| ADM-04 | Thêm truyện | Cao | Admin | 1. `admin/stories.php` → Thêm<br>2. Nhập title, chọn thể loại, upload ảnh JPG/PNG | Truyện mới trong DB; ảnh lưu `backend/uploads/` | | Pending |
| ADM-05 | Upload ảnh sai MIME | Cao | Admin | 1. Upload file `.pdf` hoặc `.exe` làm bìa | Từ chối upload; thông báo lỗi MIME | | Pending |
| ADM-06 | Sửa truyện | Trung bình | Có truyện | 1. Sửa title/status từ admin | Cập nhật DB; frontend phản ánh thay đổi | | Pending |
| ADM-07 | Xóa truyện | Cao | Truyện có chương, comment | 1. Xóa truyện từ admin | Cascade xóa chapters, comments, user_stories (transaction) | | Pending |
| ADM-08 | Thêm chương | Cao | Có truyện | 1. `admin/chapter.php` → Thêm chương | Chương mới; hiển thị trên `read_story.php` | | Pending |
| ADM-09 | Sửa / xóa chương | Trung bình | Có chương | 1. Sửa nội dung hoặc xóa chương | DB cập nhật; trang đọc phản ánh | | Pending |
| ADM-10 | Thêm user | Trung bình | Admin | 1. `admin/users.php` → Thêm user | User mới với role/status mặc định | | Pending |
| ADM-11 | Khóa / mở khóa user | Cao | Có user test | 1. Admin đặt status banned/active | User banned không login; active login bình thường | | Pending |
| ADM-12 | Xóa user | Trung bình | Không phải admin cuối | 1. Xóa user từ admin | User bị xóa; không xóa được admin cuối cùng | | Pending |
| ADM-13 | Admin API — không có session | Cao | Không đăng nhập | 1. POST trực tiếp `add_story.php` | JSON 403 / từ chối | | Pending |

---

## 11. Giao diện & tương thích (UI)

| ID | Tên test case | Ưu tiên | Điều kiện tiên quyết | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------------------|----------|------------------|-----------------|------------|
| UI-01 | Responsive mobile | Trung bình | Chrome DevTools | 1. Thu nhỏ viewport 375px | Layout không vỡ; menu/header dùng được | | Pending |
| UI-02 | Ảnh bìa upload | Trung bình | Truyện có cover uploads | 1. Xem truyện trên home/category | Ảnh load từ `backend/uploads/…` qua `cover_url()` | | Pending |
| UI-03 | Ảnh bìa mặc định | Trung bình | Truyện dùng `code/images/` | 1. Xem truyện | Ảnh hiển thị đúng đường dẫn | | Pending |
| UI-04 | Toast thông báo | Thấp | User trên home/category | 1. Thao tác lưu/tương tác có toast | Hiển thị toast (không dùng alert trên các trang đã cập nhật) | | Pending |

---

## 12. Tổng hợp

| Module | Số test case |
|--------|--------------|
| AUTH | 12 |
| HOME / CAT | 10 |
| READ | 10 |
| COIN | 10 |
| SHELF | 6 |
| SEARCH | 5 |
| CMT | 6 |
| ACC | 3 |
| ADMIN | 13 |
| UI | 4 |
| **Tổng** | **79** |

### Ghi chú khi test

1. Reset coin user trước khi test COIN-07: `UPDATE users SET coins = 10 WHERE username = 'test_user';`
2. Dùng truyện có **≥ 4 chương** để test paywall.
3. File CSV tương ứng: [`TESTCASE.csv`](TESTCASE.csv) — import vào Excel/Google Sheets.
4. Cột **Kết quả thực tế** và **Trạng thái** để trống khi bàn giao; tester điền khi thực hiện.

---

## Tác giả

| | |
|---|---|
| **Dự án** | KEWE — Chuyên đề định hướng |
| **Nhóm** | Nhóm 1 |
| **GVHD** | Ths. Ngô Ngọc Anh |
| **Năm học** | 2025 – 2026 |
>>>>>>> origin/main
