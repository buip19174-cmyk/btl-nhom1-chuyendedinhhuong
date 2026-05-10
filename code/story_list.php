<?php
header('Content-Type: application/json');
include('connect.php');

$sql = "SELECT * FROM stories ORDER BY id DESC";
$result = mysqli_query($con, $sql);

$stories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stories[] = $row;
    }
}

echo json_encode($stories);
?>
