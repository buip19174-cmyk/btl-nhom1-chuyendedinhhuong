<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../database/connect.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit   = isset($_GET['limit']) ? (int) $_GET['limit'] : 12;
$limit   = max(1, min($limit, 50));

if ($keyword === '') {
    echo json_encode(['success' => true, 'count' => 0, 'items' => [], 'keyword' => '']);
    exit;
}

if (mb_strlen($keyword) < 2) {
    echo json_encode([
        'success' => true,
        'count'   => 0,
        'items'   => [],
        'keyword' => $keyword,
        'message' => 'Nhập ít nhất 2 ký tự để tìm kiếm',
    ]);
    exit;
}

$catLabels = [
    'home'      => 'Nổi bật',
    'tho'       => 'Thơ - Tản văn',
    'trinhtham' => 'Trinh thám',
    'taichinh'  => 'Tài chính',
    'ptcanhan'  => 'Phát triển cá nhân',
    'doanhnhan' => 'Doanh nhân',
    'suckhoe'   => 'Sức khỏe',
    'khoahoc'   => 'Khoa học',
    'tamlinh'   => 'Tâm linh',
    'giaoduc'   => 'Giáo dục',
    'mkt'       => 'Marketing',
    'tuduy'     => 'Tư duy sáng tạo',
    'nghethuat' => 'Nghệ thuật sống',
];

function search_cover_url(string $cover): string
{
    if ($cover === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $cover)) {
        return $cover;
    }
    if (strpos($cover, 'uploads/') === 0) {
        return '../backend/' . $cover;
    }
    return '../code/images/' . $cover;
}

$like = '%' . $keyword . '%';
$sql  = "SELECT id, title, cover, description
         FROM stories
         WHERE title LIKE ?
         ORDER BY title ASC
         LIMIT {$limit}";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn']);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $desc = $row['description'] ?? '';
    $items[] = [
        'id'       => (int) $row['id'],
        'title'    => $row['title'],
        'cover'    => search_cover_url($row['cover'] ?? ''),
        'category' => $catLabels[$desc] ?? ($desc !== '' ? $desc : 'Sách'),
        'url'      => '../backend/read_story.php?story_id=' . (int) $row['id'],
    ];
}
mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'count'   => count($items),
    'items'   => $items,
    'keyword' => $keyword,
], JSON_UNESCAPED_UNICODE);
