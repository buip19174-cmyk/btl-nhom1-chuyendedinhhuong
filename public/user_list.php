<?php
header('Content-Type: application/json');
include('db_connect.php');

$sql = "SELECT id, username, sdt, email FROM users ORDER BY id DESC";
$result = mysqli_query($con, $sql);

$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

echo json_encode($users);
?>
