<?php

session_start();
require_once __DIR__ . '/db_connection.php';

function load_all_books(mysqli $conn): array
{
    $books = [];
    $res = $conn->query('SELECT id, title, cover_image AS cover FROM stories ORDER BY id DESC');
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}
