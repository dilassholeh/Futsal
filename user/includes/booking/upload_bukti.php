<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    die("User belum login!");
}

if (!isset($_FILES['bukti'])) {
    die("Tidak ada file yang diupload!");
}

$uploadDir = '../../../uploads/booking/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = time() . "_" . basename($_FILES['bukti']['name']);
$target_file = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
    die("Gagal upload file!");
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['booking'])) {
    die("Data booking tidak ditemukan di session!");
}

$booking = $_SESSION['booking'];
$subtotal = $booking['total'] ?? 0;

$id_transaksi = uniqid('trx_');

$status_pembayaran = 'dp'; 
$metode_pembayaran = 'transfer'; 

$stmt = $conn->prepare("
    INSERT INTO transaksi (
        id, user_id, subtotal, bukti_pembayaran, 
        status_pembayaran, metode_pembayaran, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, NOW())
");
if (!$stmt) die("Prepare statement gagal: " . $conn->error);

$stmt->bind_param("ssdsss", 
    $id_transaksi, 
    $user_id, 
    $subtotal, 
    $filename, 
    $status_pembayaran, 
    $metode_pembayaran
);

if (!$stmt->execute()) {
    die("Gagal menyimpan transaksi: " . $stmt->error);
}
$stmt->close();

$id_detail = uniqid('det_');
$lapangan_id = $booking['lapangan_id'];
$tanggal = $booking['tanggal'];
$jam_mulai = $booking['jam_mulai'];
$jam_selesai = $booking['jam_selesai'];
$durasi = $booking['durasi'];
$harga = $booking['harga'];

$detail_stmt = $conn->prepare("
    INSERT INTO transaksi_detail (
        id, id_transaksi, id_lapangan, tanggal, jam_mulai, jam_selesai, durasi, harga_jual
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$detail_stmt) die("Prepare statement gagal: " . $conn->error);

$detail_stmt->bind_param("ssssssid", 
    $id_detail, 
    $id_transaksi, 
    $lapangan_id, 
    $tanggal, 
    $jam_mulai, 
    $jam_selesai, 
    $durasi, 
    $harga
);

if (!$detail_stmt->execute()) {
    die("Gagal menyimpan detail transaksi: " . $detail_stmt->error);
}

$detail_stmt->close();

unset($_SESSION['booking']);

header("Location: berhasil.php?id=" . $id_transaksi);
exit;
?>