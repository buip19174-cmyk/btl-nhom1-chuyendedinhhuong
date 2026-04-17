
usecaseDiagram
    actor "Độc giả" as Reader
    actor "Tác giả" as Author
    actor "Quản trị viên" as Admin

    package "Hệ thống Website Đọc Truyện" {
        %% Nhóm Độc giả
        usecase "Xem danh sách truyện" as UC_View
        usecase "Tìm kiếm truyện" as UC_Search
        usecase "Đọc truyện" as UC_Read
        usecase "Đăng ký / Đăng nhập" as UC_Auth
        usecase "Tương tác (Bình luận/Yêu thích)" as UC_Interact
        usecase "Lưu lịch sử đọc" as UC_History

        %% Nhóm Tác giả
        usecase "Đăng truyện mới / Chương mới" as UC_Upload
        usecase "Theo dõi thống kê lượt đọc" as UC_Stats

        %% Nhóm Admin
        usecase "Quản lý người dùng" as UC_User_Mgmt
        usecase "Quản lý nội dung (Duyệt/Xóa)" as UC_Content_Mgmt
        usecase "Quản lý thể loại truyện" as UC_Cat_Mgmt
    }

    %% Liên kết các Actor
    Reader -- UC_View
    Reader -- UC_Search
    Reader -- UC_Read
    Reader -- UC_Auth
    Reader -- UC_Interact
    Reader -- UC_History

    Author -- UC_Upload
    Author -- UC_Stats
    Author -- UC_Read

    Admin -- UC_User_Mgmt
    Admin -- UC_Content_Mgmt
    Admin -- UC_Cat_Mgmt