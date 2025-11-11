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

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e9ecef;
            display: flex;
            justify-content: center;
            padding-top: 40px;
        }

        .card {
            background: #ffffff;
            width: 380px;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
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
        }

        table {
            width: 100%;
            margin-top: 25px;
            font-size: 15px;
        }

        table td {
            padding: 5px 0;
            color: #444;
        }

        .btn-main {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background: #28a745;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print {
            margin-top: 15px;
            display: inline-block;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #28a745;
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .back-btn {
            margin-top: 12px;
            display: inline-block;
            padding: 10px 15px;
            border-radius: 8px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn:hover {
            background: #5a6268;
        }
    </style>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>

<body>

    <div class="card">

        <div class="check-circle">
            <i class="fa fa-check"></i>
        </div>

        <h2>Pembayaran Berhasil!</h2>

        <div class="amount">
            Rp <?= number_format($transaksi['subtotal'], 0, ',', '.'); ?>
        </div>

        <table>
            <tr>
                <td><strong>Order ID</strong></td>
                <td><?= $transaksi['id'] ?></td>
            </tr>

            <tr>
                <td><strong>Nama Lapangan</strong></td>
                <td><?= $detail['lapangan'] ?></td>
            </tr>

            <tr>
                <td><strong>Tanggal</strong></td>
                <td><?= $detail['tanggal'] ?></td>
            </tr>

            <tr>
                <td><strong>Jam</strong></td>
                <td><?= $detail['jam_mulai'] ?> - <?= $detail['jam_selesai'] ?></td>
            </tr>

            <tr>
                <td><strong>Durasi</strong></td>
                <td><?= $detail['durasi'] ?> Jam</td>
            </tr>

            <tr>
                <td><strong>Metode</strong></td>
                <td>Transfer Bank</td>
            </tr>
        </table>

        <a class="back-btn" href="../../pages/sewa.php">
            ← Kembali
        </a>

        <a class="btn-print" href="#" onclick="window.print();return false;">
            <i class="fa fa-print"></i> Cetak Bukti
        </a>

    </div>

</body>

</html>