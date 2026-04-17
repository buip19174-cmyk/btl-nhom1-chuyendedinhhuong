<<<<<<< HEAD
=======
# I. Giới thiệu hệ thống
Tên hệ thống: Website đọc sách, truyện online
Mục tiêu:
1. Cho phép người dùng đọc sách/truyện trực tuyến
2. Tìm kiếm, lưu, bình luận về nội dung
3. Quản lý nội dung (admin)
Phạm vi: 
1. Web-based (trình duyệt)
2. Có tài khoản người dùng
3. Có hệ thống quản trị
# II. Phân tích yêu cầu hệ thống
1. Actor( Tác nhân)
- Khách(Guest)
- Người dùng(User)
- Quản trị viên(Admin)
2. Use Case chính
- Guest:
+ Xem danh sách truyện
+ Tìm kiếm truyện
+ Xem chi tiết truyện
- User:
+ Đăng ký / đăng nhập
+ Đọc truyện
+ Lưu truyện (bookmark)
+ Đánh giá / bình luận
+ Theo dõi truyện
- Admin:
+ Quản lý truyện (CRUD)
+ Quản lý chương truyện
+ Quản lý người dùng
3. Use Case Diagram (mô tả chữ)
- Guest → (Xem truyện, tìm kiếm)
- User → (Đọc truyện, lưu, comment)
- Admin → (Quản lý hệ thống)
# III Phân tích chức năng
1. Chức năng chính
a. Quản lý người dùng
- Đăng ký, đăng nhập
- Quản lý hồ sơ
b. Quản lý truyện
- Thêm/sửa/xóa truyện
- Phân loại theo thể loại
c. Đọc truyện
- Hiển thị nội dung chương
- Chuyển chương
d. Tìm kiếm
- Theo tên truyện
- Theo thể loại
e. Tương tác
- Bình luận
2. Yêu cầu phi chức năng
- Tốc độ tải nhanh
- Bảo mật (JWT, hash password)
- Khả năng mở rộng
# IV. Thiết kế hệ thống 
1. Kiến trúc hệ thống
Mô hình:
3-tier architecture
a. Frontend
HTML, CSS, 
b. Backend
javascprit 
c. Database
MySQL
2. Database Design(ERD - mô tả)
Bảng User
- id
- username
- password
- email
- sdt
Bảng chapter
- id
- story_id
- title
- content
- chapter_number
Bảng stories
- id
- title
- description
- cover
Bảng Comment
- id
- user_id
- story_id
- content
# v. UML Diagram
1. Class Diagram
- User
- Story
- Chapter
- Comment
Quan hệ:
- story 1 - N Chapter
- User 1 - N Comment
- story 1 - N Comment
2. Sequence Diagram (Đọc truyện)
Flow:
-  User chọn truyện
- Frontend gửi request
- Backend lấy dữ liệu
- Database trả về
- Hiển thị nội dung
3. Activity Diagram (Đăng nhập)
- Nhập tài khoản
→ Kiểm tra
→ Đúng → vào hệ thống
→ Sai → báo lỗi
# VII. Giao diện (UI gợi ý)
- Trang chủ: danh sách truyện
- Trang chi tiết: nội dung truyện
- Trang đọc: hiển thị chương
- Trang cá nhân
# VIII. Công nghệ đề xuất
Frontend: html, css,javaScript
Backend: php, javascpirt
DB: MySQL
# X. Kết luận
Hệ thống đảm bảo:
Đầy đủ chức năng đọc truyện
Dễ mở rộng
Phù hợp triển khai thực tế
>>>>>>> e066d26c1290a9bdffed82c99550b495c0cc30c7
