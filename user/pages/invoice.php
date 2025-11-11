<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['booking'])) {
    die("Tidak ada data booking!");
}

$data = $_SESSION['booking'];

$bankQuery = mysqli_query($conn, "SELECT * FROM bank ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            padding: 20px;
        }

        .invoice-box {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 750px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .total-box {
            background: #f4f4f4;
            padding: 12px 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            text-align: right;
            margin-bottom: 25px;
        }

        .bank-box {
            background: #eef6ff;
            padding: 15px;
            border-left: 5px solid #007bff;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .upload-box {
            border: 2px dashed #aaa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        button {
            background: #28a745;
            padding: 12px 25px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
        }

        button:hover {
            opacity: 0.9;
        }

        .back-btn {
            background: #dc3545;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="invoice-box">

        <h2>Invoice Booking Lapangan</h2>

        <table>
            <tr>
                <td><strong>Nama Lapangan</strong></td>
                <td><?= htmlspecialchars($data['nama_lapangan']); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Booking</strong></td>
                <td><?= htmlspecialchars($data['tanggal']); ?></td>
            </tr>
            <tr>
                <td><strong>Jam Mulai</strong></td>
                <td><?= htmlspecialchars($data['jam_mulai']); ?></td>
            </tr>
            <tr>
                <td><strong>Durasi</strong></td>
                <td><?= htmlspecialchars($data['durasi']); ?> Jam</td>
            </tr>
            <tr>
                <td><strong>Jam Selesai</strong></td>
                <td><?= htmlspecialchars($data['jam_selesai']); ?></td>
            </tr>
            <tr>
                <td><strong>Harga per Jam</strong></td>
                <td>Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="total-box">
            Total Tagihan: Rp <?= number_format($data['total'], 0, ',', '.'); ?>
        </div>

        <div class="bank-box">
            <h3>Pembayaran Transfer</h3>
            <p>Silakan transfer ke salah satu rekening berikut:</p>

            <ul>
                <?php while ($bank = mysqli_fetch_assoc($bankQuery)): ?>
                    <li>
                        <strong><?= htmlspecialchars($bank['nama_bank']); ?></strong><br>
                        a.n <strong><?= htmlspecialchars($bank['atas_nama']); ?></strong><br>
                        No. Rek: <strong><?= htmlspecialchars($bank['no_rekening']); ?></strong>
                    </li>
                    <br>
                <?php endwhile; ?>
            </ul>


            <p><em>Setelah transfer, unggah bukti pembayaran di bawah ini.</em></p>
        </div>

        <form action="../includes/booking/upload_bukti.php" method="POST" enctype="multipart/form-data">
            <div class="upload-box">
                <label><strong>Upload Bukti Pembayaran</strong></label><br><br>
                <input type="file" name="bukti" accept="image/*,.pdf" required>
            </div>

            <button type="submit">Kirim Bukti Pembayaran</button>
        </form>

        <form action="booking.php?id=<?= $data['lapangan_id']; ?>">
            <button class="back-btn">Kembali</button>
        </form>

    </div>

</body>

</html>