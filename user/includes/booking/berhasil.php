<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_GET['id'])) {
    die("Transaksi tidak ditemukan!");
}

$id = $_GET['id'];

$q = $conn->prepare("SELECT * FROM transaksi WHERE id = ?");
$q->bind_param("s", $id);
$q->execute();
$transaksi = $q->get_result()->fetch_assoc();

if (!$transaksi) {
    die("Transaksi tidak ditemukan di database!");
}

$d = $conn->prepare("SELECT td.*, l.nama_lapangan AS lapangan 
                     FROM transaksi_detail td
                     LEFT JOIN lapangan l ON l.id = td.id_lapangan
                     WHERE td.id_transaksi = ?");
$d->bind_param("s", $id);
$d->execute();
$detail = $d->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Berhasil</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #e9ecef;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 10px;
    min-height: 100vh;
}
.card {
    background: #fff;
    width: 400px;
    max-width: 95%;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.check-circle {
    width: 90px;
    height: 90px;
    background: #e8f9ee;
    border-radius: 50%;
    margin: auto;
    display: flex;
    justify-content: center;
    align-items: center;
}
.check-circle i {
    font-size: 45px;
    color: #28a745;
}
h2 {
    margin-top: 15px;
    font-size: 22px;
    font-weight: bold;
}
.amount {
    color: #28a745;
    font-size: 26px;
    font-weight: bold;
    margin-top: -5px;
    margin-bottom: 25px;
}
.info-table {
    width: 100%;
    margin-top: 10px;
    font-size: 15px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}
.info-row:last-child {
    border-bottom: none;
}
.label {
    font-weight: bold;
    color: #333;
}
.value {
    color: #555;
    text-align: right;
}
.btn-main, .btn-print, .back-btn {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    font-weight: bold;
    cursor: pointer;
    margin-top: 15px;
    border: none;
    font-size: 16px;
}
.btn-main {
    background: #28a745;
    color: #fff;
}
.btn-print {
    background: #fff;
    color: #28a745;
    border: 1px solid #28a745;
}
.btn-print i {
    margin-right: 5px;
}
.back-btn {
    background: #6c757d;
    color: #fff;
}
.back-btn:hover { background: #5a6268; }
.btn-print:hover { background: #28a745; color: #fff; }
</style>
</head>
<body>
<div class="card">
    <div class="check-circle">
        <i class="fa fa-check"></i>
    </div>
    <h2>Pembayaran Berhasil!</h2>
    <div class="amount">Rp <?= number_format($transaksi['subtotal'], 0, ',', '.'); ?></div>

    <div class="info-table">
        <div class="info-row">
            <div class="label">Order ID</div>
            <div class="value"><?= $transaksi['id'] ?></div>
        </div>
        <div class="info-row">
            <div class="label">Nama Lapangan</div>
            <div class="value"><?= $detail['lapangan'] ?></div>
        </div>
        <div class="info-row">
            <div class="label">Tanggal</div>
            <div class="value"><?= $detail['tanggal'] ?></div>
        </div>
        <div class="info-row">
            <div class="label">Jam</div>
            <div class="value"><?= $detail['jam_mulai'] ?> - <?= $detail['jam_selesai'] ?></div>
        </div>
        <div class="info-row">
            <div class="label">Durasi</div>
            <div class="value"><?= $detail['durasi'] ?> Jam</div>
        </div>
        <div class="info-row">
            <div class="label">Metode</div>
            <div class="value">Transfer Bank</div>
        </div>
    </div>

    <a class="back-btn" href="../../pages/sewa.php">← Kembali</a>
    <a class="btn-print" href="#" onclick="window.print();return false;"><i class="fa fa-print"></i> Cetak Bukti</a>
</div>
</body>
</html>
