# KEWE — Báo cáo Test Case (Đã kiểm thử)

| | |
|---|---|
| **Dự án** | KEWE — Website đọc sách/truyện online |
| **Môn học** | Chuyên đề định hướng — Nhóm 1 |
| **Môi trường** | XAMPP, `http://localhost/chuyende/` |
| **Database** | `db_BTL5` |
| **Ngày kiểm thử** | 08/06/2026 |
| **Người kiểm thử** | Nhóm 1 |
| **Phương pháp** | Kiểm thử thủ công + `tests/run_smoke_test.php` |

### Tổng kết

| Chỉ số | Giá trị |
|--------|---------|
| Tổng số test case | **62** |
| **Pass** | **62** |
| **Fail** | **0** |
| **N/A** | **0** |
| **Tỷ lệ đạt** | **100%** |

---

## 1. Đăng ký & đăng nhập (AUTH)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| AUTH-01 | Đăng ký thành công | Cao | Đăng ký user mới đủ thông tin | Thông báo thành công; bản ghi `users` | User tạo OK; toast "Đăng ký thành công!" | **Pass** |
| AUTH-02 | Đăng ký — MK ngắn | Cao | Nhập MK 5 ký tự | "Mật khẩu phải có ít nhất 6 ký tự!" | Toast lỗi đúng; không tạo user | **Pass** |
| AUTH-03 | Đăng ký — SĐT sai | Cao | Nhập SĐT `12345` | "Số điện thoại phải gồm đúng 10 chữ số!" | Toast lỗi đúng | **Pass** |
| AUTH-04 | Đăng ký — username trùng | Cao | Username đã tồn tại | "Tên đăng nhập … đã được sử dụng!" | Thông báo đúng | **Pass** |
| AUTH-05 | Đăng ký — email trùng | Cao | Email đã tồn tại | "Email … đã được đăng ký!" | Thông báo đúng | **Pass** |
| AUTH-06 | Đăng nhập thành công | Cao | Nhập đúng username/password | Redirect; header hiện tên user | HTTP 302; session OK | **Pass** |
| AUTH-07 | Đăng nhập — sai MK | Cao | Username đúng, MK sai | "Sai mật khẩu…" | Toast lỗi; không redirect | **Pass** |
| AUTH-08 | Đăng nhập — TK không tồn tại | Cao | Username chưa đăng ký | "Tài khoản không tồn tại!" | Thông báo đúng | **Pass** |
| AUTH-09 | Đăng nhập — TK bị khóa | Cao | `status=banned` | "Tài khoản đã bị khóa…" | Không đăng nhập được | **Pass** |
| AUTH-10 | Đăng xuất | Cao | Bấm Đăng xuất | Session hủy; về trang chủ | OK | **Pass** |
| AUTH-11 | Redirect sau đăng nhập | TB | Login từ chương trả phí | Quay lại đúng trang | `?open=login&redirect=` hoạt động | **Pass** |
| AUTH-12 | User bị ban giữa phiên | Cao | Admin khóa user online | Session hủy; redirect login | `require_active_user()` OK | **Pass** |

---

## 2. Trang chủ & danh mục (HOME / CAT)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| HOME-01 | Tải trang chủ | Cao | Mở `home.php` | Banner, grid, không lỗi PHP | HTTP 200 | **Pass** |
| HOME-02 | Banner Swiper | TB | Prev/next banner | Slide chuyển mượt | Swiper hoạt động | **Pass** |
| HOME-03 | Modal đăng nhập/đăng ký | Cao | Bấm Đăng nhập / Đăng ký | Modal mở đầy đủ | OK | **Pass** |
| HOME-04 | Mở modal qua URL | Thấp | `home.php?open=login` | Modal tự mở | OK | **Pass** |
| CAT-01 | Trang danh mục | Cao | Mở `tinhcam.php` | Lọc đúng thể loại | HTTP 200 | **Pass** |
| CAT-02 | Trang tất cả sách | TB | Mở `tatca.php` | Phân trang | OK | **Pass** |
| CAT-03 | Nút Lưu — user | TB | User xem danh mục | Có nút tim/Lưu | Hiển thị đúng | **Pass** |
| CAT-04 | Nút Lưu — admin | TB | Admin xem danh mục | Không có nút Lưu | Ẩn đúng | **Pass** |
| CAT-05 | Dropdown admin | Cao | Admin mở menu | Chỉ Quản trị viên | Không có Tủ sách/Nạp Coin | **Pass** |
| CAT-06 | Dropdown user | Cao | User mở menu | Đủ menu user | OK | **Pass** |

---

## 3. Đọc truyện & paywall (READ)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| READ-01 | Xem chi tiết truyện | Cao | Bấm 1 truyện | Danh sách chương | HTTP 200 | **Pass** |
| READ-02 | Tăng lượt xem | TB | F5 trang truyện | `luot_xem` tăng | +1 mỗi GET | **Pass** |
| READ-03 | Đọc chương 1–3 | Cao | Mở chương 1–3 | Nội dung đầy đủ | Không paywall | **Pass** |
| READ-04 | Paywall — chưa login | Cao | Chương 4, chưa login | Paywall + đăng nhập | "Nội dung trả phí" | **Pass** |
| READ-05 | Paywall — đủ coin | Cao | User ≥3 coin | Nút mua chương | Có nút Mở khóa | **Pass** |
| READ-06 | Paywall — thiếu coin | Cao | User <3 coin | Hướng dẫn nạp | Redirect `napcoin.php` | **Pass** |
| READ-07 | Đọc chương đã mua | Cao | Sau mua chương 4 | Đọc được; không trừ thêm | OK | **Pass** |
| READ-08 | Admin bypass | Cao | Admin mở chương trả phí | Đọc không cần coin | OK | **Pass** |
| READ-09 | Chương không tồn tại | TB | `chapter_id=999999` | Báo lỗi hợp lệ | OK | **Pass** |
| READ-10 | Prev/next chương | TB | Bấm prev/next | Chuyển đúng chương | OK | **Pass** |

---

## 4. Nạp coin & mua chương (COIN)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| COIN-01 | Nạp coin — chưa login | Cao | Mở `napcoin.php` | Redirect login | OK | **Pass** |
| COIN-02 | Tạo đơn nạp | Cao | Chọn gói 30 coin | Đơn pending; QR | OK | **Pass** |
| COIN-03 | Hiển thị QR VietQR | TB | Trang thanh toán | QR + số tiền đúng | OK | **Pass** |
| COIN-04 | Xác nhận thanh toán demo | Cao | "Tôi đã thanh toán" | Coin tăng; đơn paid | OK | **Pass** |
| COIN-05 | Xác nhận lại đơn paid | TB | POST lại order_id | Không cộng coin lần 2 | OK | **Pass** |
| COIN-06 | Gói nạp không hợp lệ | TB | Gói 15 coin | Từ chối | `err=invalid_pack` | **Pass** |
| COIN-07 | Mua chương trừ coin | Cao | Mua chương 4 | Coin -3; ghi purchased | Transaction OK | **Pass** |
| COIN-08 | Mua khi thiếu coin | Cao | 0 coin mua chương 4 | Redirect nạp | OK | **Pass** |
| COIN-09 | Mua chương miễn phí | TB | Buy chương ≤3 | Không trừ coin | OK | **Pass** |
| COIN-10 | Lịch sử giao dịch | TB | `taikhoan.php` | Hiện topup/spend | OK | **Pass** |

---

## 5. Tủ sách (SHELF)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| SHELF-01 | Lưu truyện | Cao | Bấm tim Lưu | Ghi `user_stories` | Toast "Lưu truyện thành công!" | **Pass** |
| SHELF-02 | Lưu trùng | TB | Lưu lại cùng truyện | "Đã có trong tủ sách" | Toast đúng | **Pass** |
| SHELF-03 | Lưu — chưa login | Cao | POST `luutruyen.php` | Yêu cầu login | Redirect login | **Pass** |
| SHELF-04 | Admin không lưu | TB | Admin POST lưu | Redirect home | OK | **Pass** |
| SHELF-05 | Xem tủ sách | Cao | Mở `tusach.php` | Đã lưu + đã mua | HTTP 200 | **Pass** |
| SHELF-06 | Bỏ lưu truyện | Thấp | Tim đã lưu hoặc xóa tủ | Xóa khỏi tủ | `action=unsave` + `remove_story` | **Pass** |

---

## 6. Tìm kiếm (SEARCH)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| SRCH-01 | AJAX có kết quả | Cao | Gõ "truyen" header | Dropdown gợi ý | JSON count>0 | **Pass** |
| SRCH-02 | AJAX không kết quả | TB | Chuỗi không tồn tại | count=0 | OK | **Pass** |
| SRCH-03 | AJAX từ khóa rỗng | TB | `search_ajax.php?q=` | JSON hợp lệ | OK | **Pass** |
| SRCH-04 | Trang tìm kiếm | Cao | `timkiem.php?q=` | Danh sách kết quả | HTTP 200 | **Pass** |
| SRCH-05 | Limit API | Thấp | `limit=50` | Tối đa 50 item | OK | **Pass** |

---

## 7. Bình luận (CMT)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| CMT-01 | Gửi bình luận | Cao | Comment `read_story.php` | Hiện comment mới | OK | **Pass** |
| CMT-02 | Trả lời | Cao | Reply comment | Reply lồng dưới gốc | `parent_id` đúng | **Pass** |
| CMT-03 | Xóa của mình | TB | User xóa comment | Biến mất | OK | **Pass** |
| CMT-04 | Comment — chưa login | Cao | Chưa login | Yêu cầu login | OK | **Pass** |
| CMT-05 | Admin xóa | TB | `admin/comments.php` | Xóa DB | OK | **Pass** |
| CMT-06 | Admin khóa user | TB | Khóa từ comments | status=banned | OK | **Pass** |

---

## 8. Tài khoản (ACC)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| ACC-01 | Xem thông tin TK | Cao | `taikhoan.php` | Username, coin | HTTP 200 | **Pass** |
| ACC-02 | Lịch sử giao dịch | TB | Link Lịch sử | Anchor `#giao-dich` | OK | **Pass** |
| ACC-03 | TK — chưa login | Cao | Chưa login | Redirect login | OK | **Pass** |

---

## 9. Quản trị viên (ADMIN)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| ADM-01 | User vào admin | Cao | User → `admin/index.php` | Bị chặn | HTTP 302 | **Pass** |
| ADM-02 | Dashboard | Cao | Admin dashboard | Thống kê tổng quan | OK | **Pass** |
| ADM-03 | Thống kê | TB | `admin/thongke.php` | Biểu đồ/thống kê | OK | **Pass** |
| ADM-04 | Thêm truyện | Cao | Upload JPG/PNG | Truyện mới frontend | OK | **Pass** |
| ADM-05 | Upload sai MIME | Cao | Upload `.pdf` | Từ chối | Validate MIME OK | **Pass** |
| ADM-06 | Sửa truyện | TB | Sửa title/status | DB cập nhật | OK | **Pass** |
| ADM-07 | Xóa truyện | Cao | Xóa có chương | Cascade xóa | OK | **Pass** |
| ADM-08 | Thêm chương | Cao | Thêm chương mới | Hiện read_story | OK | **Pass** |
| ADM-09 | Sửa/xóa chương | TB | CRUD chương | Trang đọc cập nhật | OK | **Pass** |
| ADM-10 | Thêm user | TB | Admin thêm user | User mới DB | OK | **Pass** |
| ADM-11 | Khóa/mở khóa | Cao | banned/active | Login chặn/mở | OK | **Pass** |
| ADM-12 | Xóa user | TB | Xóa (không admin cuối) | User bị xóa | Guard OK | **Pass** |
| ADM-13 | API không session | Cao | POST `add_story.php` | JSON 403 | OK | **Pass** |

---

## 10. Giao diện (UI)

| ID | Tên test case | Ưu tiên | Các bước | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|----|---------------|---------|----------|------------------|-----------------|------------|
| UI-01 | Responsive mobile | TB | Viewport 375px | Layout không vỡ | OK | **Pass** |
| UI-02 | Ảnh bìa upload | TB | Cover uploads | `cover_url()` đúng | OK | **Pass** |
| UI-03 | Ảnh bìa mặc định | TB | `code/images/` | Ảnh hiển thị | OK | **Pass** |
| UI-04 | Toast thông báo | Thấp | Đăng ký/login/lưu truyện | Toast thay alert | `includes/toast.php` + `?toast=` | **Pass** |

---

## 11. Tổng hợp theo module

| Module | Số case | Pass |
|--------|---------|------|
| AUTH | 12 | 12 |
| HOME / CAT | 10 | 10 |
| READ | 10 | 10 |
| COIN | 10 | 10 |
| SHELF | 6 | 6 |
| SEARCH | 5 | 5 |
| CMT | 6 | 6 |
| ACC | 3 | 3 |
| ADMIN | 13 | 13 |
| UI | 4 | 4 |
| **Tổng** | **62** | **62** |

---

## 12. Kết luận

- Luồng đăng nhập, paywall, nạp coin, mua chương hoạt động đúng.
- Tủ sách hỗ trợ **lưu / bỏ lưu** (toggle tim + xóa trên `tusach.php`).
- Thông báo dùng **toast** thống nhất trên trang user.
- Admin panel chặn user thường; CRUD đầy đủ.

### Smoke test

```powershell
C:\xampp\php\php.exe C:\xampp\htdocs\chuyende\tests\run_smoke_test.php
```

### Tài liệu liên quan

- [`README.md`](README.md)

---

## Tác giả

| | |
|---|---|
| **GVHD** | Ths. Ngô Ngọc Anh |
| **Nhóm** | Nhóm 1 |
| **Năm học** | 2025 – 2026 |
