<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_BTL5";

$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con) {
    echo " Không thể kết nối database: " . mysqli_connect_error();
}
?>
