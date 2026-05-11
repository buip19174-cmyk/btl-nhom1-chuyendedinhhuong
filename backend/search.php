<?php
// search.php

// 1. Kiểm tra xem các biến cấu hình đã được trang con định nghĩa chưa
if (!isset($searchTable) || !isset($searchColumn)) {
    return; // Nếu chưa định nghĩa thì không chạy tiếp
}

$keyword = $_GET['keyword'] ?? '';
$filterValue = $_GET['filter'] ?? '';

// 2. Xây dựng câu lệnh SQL động
$sql = "SELECT * FROM $searchTable WHERE $searchColumn LIKE ?";
$params = ["%$keyword%"];
$types = "s";

// Nếu trang con có yêu cầu lọc (ví dụ: lọc theo giới tính hoặc thể loại)
if (!empty($filterValue) && isset($filterColumn)) {
    $sql .= " AND $filterColumn = ?";
    $params[] = $filterValue;
    $types .= "s";
}

// 3. Thực thi truy vấn
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>