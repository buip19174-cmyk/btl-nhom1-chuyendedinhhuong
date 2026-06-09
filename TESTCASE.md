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
