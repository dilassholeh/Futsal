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

// ambil detail transaksi
$d = $conn->prepare("SELECT td.*, l.nama_lapangan AS lapangan 
                     FROM transaksi_detail td
                     LEFT JOIN lapangan l ON l.id = td.id_lapangan
                     WHERE td.id_transaksi = ?");
$d->bind_param("s", $id);
$d->execute();
$detail = $d->get_result()->fetch_assoc();

// ambil jumlah yang sudah dibayar (dp atau lunas)
$jumlah_dibayar = floatval($transaksi['jumlah_dibayar']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Berhasil</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: #EEF2F7;
    display: flex;
    justify-content: center;
    padding: 40px 10px;
    min-height: 100vh;
}
.receipt-card {
    background: #fff;
    width: 430px;
    padding: 35px;
    border-radius: 18px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.10);
    animation: fadeIn .5s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px);}
    to { opacity: 1; transform: translateY(0);}
}
.success-icon {
    width: 90px;
    height: 90px;
    background: #e6f8ec;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: auto;
}
.success-icon i {
    font-size: 45px;
    color: #28a745;
}
h2 {
    font-size: 24px;
    font-weight: 700;
    color: #232323;
    margin: 18px 0 10px;
}
.amount {
    color: #28a745;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 22px;
}
.details {
    margin-top: 20px;
    text-align: left;
}
.details .row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 15.5px;
    border-bottom: 1px dashed #ddd;
}
.details .label {
    font-weight: 600;
    color: #444;
}
.details .value {
    font-weight: 500;
    color: #666;
    text-align: right;
}
.btn {
    width: 100%;
    padding: 13px;
    border-radius: 10px;
    text-decoration: none;
    display: inline-block;
    margin-top: 17px;
    font-size: 16px;
    font-weight: 600;
    transition: .25s;
}
.btn-back {
    background: #117139;
    color: #fff;
}
.btn-back:hover {
    background: #0d5a2d;
}
.btn-print {
    border: 2px solid #117139;
    color: #117139;
    background: transparent;
}
.btn-print:hover {
    background: #117139;
    color: #fff;
}
</style>
</head>
<body>

<div class="receipt-card">
    
    <div class="success-icon">
        <i class="fa fa-check"></i>
    </div>

    <h2>Pembayaran Berhasil</h2>

    <!-- === MENAMPILKAN JUMLAH YANG DIBAYAR (DP ATAU LUNAS) === -->
    <div class="amount">
        Rp <?= number_format($jumlah_dibayar, 0, ',', '.'); ?>
    </div>

    <div class="details">
        <div class="row"><span class="label">Order ID</span><span class="value"><?= $transaksi['id'] ?></span></div>
        <div class="row"><span class="label">Lapangan</span><span class="value"><?= $detail['lapangan'] ?></span></div>
        <div class="row"><span class="label">Tanggal</span><span class="value"><?= date('d/m/Y', strtotime($detail['tanggal'])) ?></span></div>
        <div class="row"><span class="label">Waktu</span><span class="value"><?= $detail['jam_mulai'] ?> - <?= $detail['jam_selesai'] ?></span></div>
        <div class="row"><span class="label">Durasi</span><span class="value"><?= $detail['durasi'] ?> Jam</span></div>
        <div class="row"><span class="label">Metode</span><span class="value">Transfer Bank</span></div>
    </div>

    <a class="btn btn-back" href="../../sewa.php">Kembali</a>
    <a class="btn btn-print" href="#" onclick="window.print();return false;">Cetak Bukti</a>

</div>

</body>
</html>
