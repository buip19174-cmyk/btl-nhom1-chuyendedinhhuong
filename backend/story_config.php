<?php
/** Hằng số nghiệp vụ đọc truyện / mua chương */
if (!defined('FREE_CHAPTERS')) {
    define('FREE_CHAPTERS', 3);
}
if (!defined('COINS_PER_CHAPTER')) {
    define('COINS_PER_CHAPTER', 3);
}

/** Mã danh mục hợp lệ (stories.description) */
function story_category_labels(): array
{
    return [
        'home'       => 'Trang chủ / nổi bật',
        'tho'        => 'Thơ - Tản văn',
        'trinhtham'  => 'Trinh thám - Kinh dị',
        'mkt'        => 'Marketing - Bán hàng',
        'taichinh'   => 'Tài chính cá nhân',
        'ptcanhan'   => 'Phát triển cá nhân',
        'doanh_nhan' => 'Doanh nhân',
        'suckhoe'    => 'Sức khỏe - Làm đẹp',
        'khoahoc'    => 'Khoa học - Công nghệ',
        'tuduy'      => 'Tư duy sáng tạo',
        'giaoduc'    => 'Giáo dục - Văn hóa',
        'nghethuat'  => 'Nghệ thuật sống',
        'tamlinh'    => 'Tâm linh - Tôn giáo',
        'chungkhoan' => 'Chứng khoán - BĐS',
        'ngoai_van'  => 'Sách Ngoại văn',
        'nam'        => 'Truyện Nam',
        'nu'         => 'Truyện Nữ',
        'xuyenkhong' => 'Xuyên không',
        'truyenma'   => 'Truyện ma',
        'tinhcam'    => 'Tình cảm',
        'ngungon'    => 'Ngụ ngôn',
        'codai'      => 'Cổ đại',
        'thieunhi'   => 'Thiếu nhi',
        'haihuoc'    => 'Hài hước',
        'hanhdong'   => 'Hành động',
    ];
}

function story_category_label(string $code): string
{
    $labels = story_category_labels();
    return $labels[$code] ?? $code;
}
