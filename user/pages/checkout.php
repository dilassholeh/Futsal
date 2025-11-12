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
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.invoice-box {
    background: #fff;
    border-radius: 20px;
    max-width: 900px;
    width: 100%;
    padding: 30px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #27ae60;
    font-size: 28px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}
th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}
th {
    background-color: #27ae60;
    color: #fff;
    font-weight: 600;
}
tr:nth-child(even) { background: #f2f9f2; }
.total-box {
    background: #eafaf1;
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 10px;
    text-align: right;
    margin-bottom: 25px;
}
.bank-box {
    background: #eafaf1;
    padding: 20px;
    border-left: 5px solid #27ae60;
    border-radius: 12px;
    margin-bottom: 25px;
}
.upload-box {
    border: 2px dashed #aaa;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 20px;
    background: #f9fff9;
}
button {
    background: #27ae60;
    padding: 12px 25px;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    margin-right: 10px;
    transition: 0.2s;
}
button:hover { background: #1e8449; }
.back-btn { background: #c0392b; }
.back-btn:hover { background: #922b21; }
ul { list-style: none; padding-left: 0; }
li { margin-bottom: 10px; }
.no-bank { color: #555; font-style: italic; }
@media(max-width: 768px){
    .invoice-box { padding: 20px; }
    th, td { padding: 8px; font-size: 14px; }
    .total-box { font-size: 16px; }
}
</style>
</head>
<body>

<div class="invoice-box">
    <h2>Checkout Booking Lapangan</h2>

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
        <a href="keranjang.php"><button type="button" class="back-btn">Kembali ke Keranjang</button></a>
    </form>
</div>

</body>
</html>
