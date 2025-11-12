<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


if (isset($_POST['selected_ids']) && !empty($_POST['selected_ids'])) {
    $selected_ids = array_map('intval', $_POST['selected_ids']);
    $id_list = implode(',', $selected_ids);

    $query = mysqli_query($conn, "
        SELECT k.*, l.nama_lapangan
        FROM keranjang k
        JOIN lapangan l ON k.id_lapangan = l.id
        WHERE k.user_id = '$user_id' AND k.id IN ($id_list)
        ORDER BY k.created_at DESC
    ");
} else {
    $query = mysqli_query($conn, "
        SELECT k.*, l.nama_lapangan
        FROM keranjang k
        JOIN lapangan l ON k.id_lapangan = l.id
        WHERE k.user_id = '$user_id'
        ORDER BY k.created_at DESC
    ");
}

if (mysqli_num_rows($query) === 0) {
    die("<p style='text-align:center; font-family:Arial; margin-top:50px;'>
        Keranjang Anda masih kosong. <a href='keranjang.php'>Kembali</a>
    </p>");
}


$totalKeseluruhan = 0;
$keranjang = [];
while ($row = mysqli_fetch_assoc($query)) {
    $keranjang[] = $row;
    $totalKeseluruhan += $row['total'];
}

$bankQuery = mysqli_query($conn, "SELECT * FROM bank ORDER BY id ASC");
$banks = [];
while ($b = mysqli_fetch_assoc($bankQuery)) {
    $banks[] = $b;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout Booking | ZonaFutsal</title>
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
            max-width: 850px;
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

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
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

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .no-bank {
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <h2>Invoice Booking Lapangan</h2>

    <table>
        <tr>
            <th>Lapangan</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Durasi</th>
            <th>Total</th>
        </tr>
        <?php foreach ($keranjang as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nama_lapangan']); ?></td>
                <td><?= htmlspecialchars($item['tanggal']); ?></td>
                <td><?= htmlspecialchars($item['jam_mulai'] . ' - ' . $item['jam_selesai']); ?></td>
                <td><?= htmlspecialchars($item['durasi']); ?> jam</td>
                <td>Rp <?= number_format($item['total'], 0, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="total-box">
        Total Keseluruhan: Rp <?= number_format($totalKeseluruhan, 0, ',', '.'); ?>
    </div>

    <div class="bank-box">
        <h3>Pembayaran Transfer</h3>
        <?php if (count($banks) > 0): ?>
            <p>Silakan transfer ke salah satu rekening berikut:</p>
            <ul>
                <?php foreach ($banks as $bank): ?>
                    <li>
                        <strong><?= htmlspecialchars($bank['nama_bank']); ?></strong><br>
                        a.n <strong><?= htmlspecialchars($bank['atas_nama']); ?></strong><br>
                        No. Rek: <strong><?= htmlspecialchars($bank['no_rekening']); ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="no-bank">Belum ada data rekening bank yang tersedia.</p>
        <?php endif; ?>

        <p><em>Setelah transfer, unggah bukti pembayaran di bawah ini.</em></p>
    </div>

    <form action="../includes/booking/upload_bukti.php" method="POST" enctype="multipart/form-data">
        <div class="upload-box">
            <label><strong>Upload Bukti Pembayaran</strong></label><br><br>
            <input type="file" name="bukti" accept="image/*,.pdf" required>
        </div>

        <?php foreach ($keranjang as $item): ?>
            <input type="hidden" name="keranjang_ids[]" value="<?= $item['id']; ?>">
        <?php endforeach; ?>

        <button type="submit">Kirim Bukti Pembayaran</button>
    </form>

    <form action="keranjang.php" method="get">
        <button type="submit" class="back-btn">Kembali ke Keranjang</button>
    </form>
</div>

</body>
</html>
