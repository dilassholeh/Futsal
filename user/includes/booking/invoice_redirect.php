<?php
session_start();
include '../../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$lapangan_id = $_POST['lapangan_id'];
$tanggal = $_POST['tanggal'];
$jam_mulai = $_POST['jam_mulai'];
$durasi = (int)$_POST['durasi'];
$jam_selesai = $_POST['jam_selesai'];
$harga = (int)$_POST['harga'];
$total = (int)$_POST['total'];
$catatan = $_POST['catatan'];

if (empty($lapangan_id) || empty($tanggal) || empty($jam_mulai) || empty($durasi)) {
    die("Data booking tidak lengkap!");
}

$today = date('Y-m-d');
$currentHour = date('H:i');
if ($tanggal === $today && $jam_mulai <= $currentHour) {
    die("Jam yang dipilih sudah lewat, silakan pilih jam lain!");
}

$stmt = $conn->prepare("
    SELECT COUNT(*) AS sudah_booking
    FROM transaksi_detail td
    JOIN transaksi t ON t.id = td.id_transaksi
    WHERE td.id_lapangan = ?
      AND td.tanggal = ?
      AND (
          (? >= td.jam_mulai AND ? < td.jam_selesai)
          OR
          (? > td.jam_mulai AND ? <= td.jam_selesai)
      )
      AND t.status_pembayaran IN ('dp', 'lunas')
");
$stmt->bind_param("ssssss", $lapangan_id, $tanggal, $jam_mulai, $jam_mulai, $jam_selesai, $jam_selesai);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['sudah_booking'] > 0) {
    die("Jam tersebut sudah dibooking oleh orang lain.");
}

$_SESSION['booking'] = [
    'lapangan_id' => $lapangan_id,
    'nama_lapangan' => $_POST['nama_lapangan'],
    'tanggal' => $tanggal,
    'jam_mulai' => $jam_mulai,
    'durasi' => $durasi,
    'jam_selesai' => $jam_selesai,
    'harga' => $harga,
    'total' => $total,
    'catatan' => $catatan
];

header("Location: ../../pages/invoice.php");
exit;
