<?php
// API tìm kiếm AJAX — trả về JSON
header('Content-Type: application/json; charset=utf-8');
include_once '../database/connect.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (mb_strlen($q) >= 1) {
    $safe = mysqli_real_escape_string($con, $q);
    $sql  = "SELECT id, title, cover FROM stories WHERE title LIKE '%$safe%' ORDER BY title ASC LIMIT 8";
    $r    = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($r)) {
        $results[] = [
            'id'    => $row['id'],
            'title' => $row['title'],
            'cover' => $row['cover'],
        ];
    }
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
