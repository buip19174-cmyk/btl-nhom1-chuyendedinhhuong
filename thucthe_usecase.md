## 3. Xác định các thực thể chính và Use Case

### 3.1. Các thực thể chính
Hệ thống quản lý website đọc truyện bao gồm các thực thể cốt lõi sau:

* **Người dùng:** Lưu trữ thông tin tài khoản, mật khẩu và phân quyền thành viên.
* **Truyện:** Thông tin chi tiết về bộ truyện (Tên, tác giả, mô tả, trạng thái).
* **Chương:** Nội dung chi tiết của từng phần trong truyện.
* **Thể loại:** Danh mục phân loại truyện (Tiên hiệp, Kiếm hiệp, Ngôn tình...).
* **Bình luận:** Các ý kiến, đánh giá của người đọc.
* **Lịch sử / Yêu thích:** Lưu trữ dấu chân người dùng và các bộ truyện đã đánh dấu.

---

### 3.2. Xác định các Use Case

Hệ thống được thiết kế dựa trên 3 nhóm tác nhân chính:

#### **A. Độc giả (Reader)**
* **Xem danh sách truyện:** Theo dõi truyện mới cập nhật, truyện hot.
* **Tìm kiếm truyện:** Tìm theo tên truyện hoặc tác giả.
* **Đọc truyện:** Xem nội dung các chương truyện.
* **Đăng ký / Đăng nhập:** Quản lý tài khoản cá nhân.
* **Tương tác:** Gửi bình luận và đánh giá truyện.
* **Lưu lịch sử:** Xem lại các truyện đã đọc hoặc đang theo dõi.

#### **B. Tác giả (Author)**
* **Quản lý nội dung:** Đăng truyện mới và cập nhật các chương mới.
* **Theo dõi thống kê:** Xem số lượng lượt đọc và tương tác của độc giả.

#### **C. Quản trị viên (Admin)**
* **Quản lý người dùng:** Điều phối và xử lý tài khoản người dùng.
* **Quản lý nội dung:** Kiểm duyệt truyện và các bình luận trên hệ thống.
* **Quản lý danh mục:** Thêm, sửa hoặc xóa các thể loại truyện.