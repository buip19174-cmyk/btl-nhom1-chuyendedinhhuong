
# KEWE — Báo cáo Test Case (Đã kiểm thử)


|                    |                                                                                |
| ------------------ | ------------------------------------------------------------------------------ |
| **Dự án**          | KEWE — Website đọc sách/truyện online                                          |
| **Môn học**        | Chuyên đề định hướng — Nhóm 1                                                  |
| **Môi trường**     | XAMPP (Apache + MySQL), `http://localhost/chuyende/`                           |
| **Database**       | `db_BTL5`                                                                      |
| **Ngày kiểm thử**  | 08/06/2026                                                                     |
| **Người kiểm thử** | Nhóm 1                                                                         |
| **Phương pháp**    | Kiểm thử thủ công trên trình duyệt |


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
---

## Tài liệu liên quan

- `[README.md]` — Hướng dẫn cài đặt và mô tả hệ thống


|             |                   |
| ----------- | ----------------- |
| **GVHD**    | Ths. Ngô Ngọc Anh |
| **Năm học** | 2025 – 2026       |


