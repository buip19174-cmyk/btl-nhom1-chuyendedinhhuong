<?php
// API tìm kiếm AJAX - trả về JSON hợp lệ cho dropdown và trang tìm kiếm.
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__ . '/../database/connect.php';

$keyword  = isset($_GET['q']) ? trim($_GET['q']) : '';
$hasLimit = isset($_GET['limit']);
$limit    = $hasLimit ? (int) $_GET['limit'] : 8;
$limit    = max(1, min($limit, 50));



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

function send_json($data): void
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($keyword === '') {
    send_json($hasLimit
        ? ['success' => true, 'count' => 0, 'items' => [], 'keyword' => '']
        : []
    );
}

if ($hasLimit && mb_strlen($keyword) < 2) {
    send_json([
        'success' => true,
        'count'   => 0,
        'items'   => [],
        'keyword' => $keyword,
        'message' => 'Nhập ít nhất 2 ký tự để tìm kiếm',
    ]);
}

$like = '%' . $keyword . '%';
$sql = "SELECT MIN(id) AS id,
               TRIM(title) AS title,
               MIN(cover) AS cover,
               MIN(description) AS description
        FROM stories
        WHERE title LIKE ?
        GROUP BY LOWER(TRIM(title))
        ORDER BY title ASC
        LIMIT {$limit}";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    http_response_code(500);
    send_json($hasLimit
        ? ['success' => false, 'message' => 'Lỗi truy vấn']
        : []
    );
}

mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$seen = [];
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $titleKey = mb_strtolower(trim($row['title']));

    if (isset($seen[$titleKey])) {
        continue;
    }

    $seen[$titleKey] = true;

    $id = (int) $row['id'];

    if ($hasLimit) {
        $desc = $row['description'] ?? '';
        $items[] = [
            'id'       => $id,
            'title'    => trim($row['title']),
            'cover'    => search_cover_url($row['cover'] ?? ''),
            'category' => $desc !== '' ? $desc : 'Sách',
            'url'      => '../backend/read_story.php?story_id=' . $id,
        ];
    } else {
        $items[] = [
            'id'    => $id,
            'title' => trim($row['title']),
            'cover' => $row['cover'],
        ];
    }
}

mysqli_stmt_close($stmt);

send_json($hasLimit
    ? ['success' => true, 'count' => count($items), 'items' => $items, 'keyword' => $keyword]
    : $items
);
