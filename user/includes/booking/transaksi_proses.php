<?php
include '../../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu!");
}

$user_id = $_SESSION['user_id'];

$id_lapangan = $_POST['lapangan_id'] ?? '';
$tanggal = $_POST['tanggal'] ?? '';
$jam_mulai = $_POST['jam_mulai'] ?? '';
$jam_selesai = $_POST['jam_selesai'] ?? '';
$durasi = (int)($_POST['durasi'] ?? 0);
$harga = (int)($_POST['harga'] ?? 0);

$total = $harga * $durasi;

if (!$id_lapangan || !$tanggal || !$jam_mulai || !$jam_selesai) {
    die("Data booking tidak lengkap!");
}

$id_transaksi = uniqid('TRX');
$id_detail = uniqid('DTL');

$query1 = "INSERT INTO transaksi (id, user_id, subtotal, created_at) 
           VALUES ('$id_transaksi', '$user_id', '$total', NOW())";

if (!mysqli_query($conn, $query1)) {
    die("Gagal simpan transaksi: " . mysqli_error($conn));
}

$query2 = "INSERT INTO transaksi_detail 
(id, id_transaksi, id_lapangan, tanggal, harga_jual, jam_mulai, jam_selesai, durasi)
VALUES 
('$id_detail', '$id_transaksi', '$id_lapangan', '$tanggal', '$harga', '$jam_mulai', '$jam_selesai', '$durasi')";

if (!mysqli_query($conn, $query2)) {
    die("Gagal simpan detail transaksi: " . mysqli_error($conn));
}


header("Location: ../../pages/invoice.php?id=$id_transaksi");



exit;

?>

