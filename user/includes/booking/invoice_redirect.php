<?php
include '../../../includes/koneksi.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Anda harus login'); window.location='../../login.php';</script>";
    exit;
}
if (!isset($_POST['lapangan_id']) || !isset($_POST['tanggal']) || !isset($_POST['jam_mulai']) || !isset($_POST['durasi']) || !isset($_POST['total'])) {
    echo "<script>alert('Data tidak lengkap'); history.back();</script>";
    exit;
}
$conn->query("UPDATE transaksi SET status_pembayaran='expired' WHERE status_pembayaran='pending' AND expire_at <= NOW()");
$user_id = $_SESSION['user_id'];
$lapangan_id = $_POST['lapangan_id'];
$nama_lapangan = $_POST['nama_lapangan'] ?? '';
$tanggal = $_POST['tanggal'];
$jam_mulai = $_POST['jam_mulai'];
$durasi = (int)$_POST['durasi'];
$jm_h = (int)substr($jam_mulai, 0, 2);
$jam_selesai_h = $jm_h + $durasi;
$jam_selesai = (strlen($jam_selesai_h) === 1 ? '0'.$jam_selesai_h : $jam_selesai_h).':00';
$total = (float)$_POST['total'];
$id_transaksi = "TRX".date("YmdHis").rand(100, 999);
$expire_at = date("Y-m-d H:i:s", strtotime("+30 minutes"));
$stmt = $conn->prepare("INSERT INTO transaksi (id, user_id, subtotal, status_pembayaran, expire_at, created_at) VALUES (?, ?, ?, 'pending', ?, NOW())");
$stmt->bind_param("ssds", $id_transaksi, $user_id, $total, $expire_at);
$stmt->execute();
$stmt->close();
$id_detail = uniqid('det_');
$stmt2 = $conn->prepare("INSERT INTO transaksi_detail (id, id_transaksi, id_lapangan, tanggal, jam_mulai, jam_selesai, durasi, harga_jual) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("ssssssid", $id_detail, $id_transaksi, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $durasi, $_POST['harga']);
$stmt2->execute();
$stmt2->close();
$_SESSION['booking'] = [
    'lapangan_id' => $lapangan_id,
    'nama_lapangan' => $nama_lapangan,
    'tanggal' => $tanggal,
    'jam_mulai' => $jam_mulai,
    'durasi' => $durasi,
    'jam_selesai' => $jam_selesai,
    'harga' => $_POST['harga'],
    'total' => $total,
    'id_transaksi' => $id_transaksi
];
unset($_SESSION['selected_jam']);
header("Location: ../../pages/invoice.php?id=".$id_transaksi);
exit;
