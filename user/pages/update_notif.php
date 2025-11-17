<?php
include '../../includes/koneksi.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

mysqli_query($conn, "
    UPDATE pesan
    SET status = 'dibaca'
    WHERE user_id = '$user_id' AND status = 'baru'
");

echo json_encode(['success' => true]);
