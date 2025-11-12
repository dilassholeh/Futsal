<?php
session_start();
include '../../../includes/koneksi.php';

<<<<<<< HEAD
// Pastikan admin login
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

<<<<<<< HEAD
// Pastikan ID transaksi dikirim
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan!");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

<<<<<<< HEAD
// Ambil data transaksi
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
$transaksi = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*, u.name AS nama_user
    FROM transaksi t
    JOIN user u ON t.user_id = u.id
    WHERE t.id = '$id'
"));
if (!$transaksi) {
    die("Transaksi tidak ditemukan!");
}

<<<<<<< HEAD
// Ambil detail transaksi
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
$detail = mysqli_query($conn, "
    SELECT td.*, l.nama_lapangan
    FROM transaksi_detail td
    JOIN lapangan l ON td.id_lapangan = l.id
    WHERE td.id_transaksi = '$id'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Transaksi</title>
  <link rel="stylesheet" href="../assets/css/transaksi.css">
  <style>
    body { font-family: Arial, sans-serif; background: #fafafa; margin: 0; }
    .container { padding: 25px; max-width: 900px; margin: auto; background: #fff; box-shadow: 0 0 8px rgba(0,0,0,0.1); border-radius: 8px; margin-top: 30px; }
    h2 { margin-bottom: 10px; }
    .bukti img {
      max-width: 300px;
      border-radius: 8px;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
      display: block;
      margin-top: 10px;
    }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    th { background-color: #f5f5f5; }
    a.btn-back {
      display: inline-block;
      margin-top: 20px;
      background: #007bff;
      color: #fff;
      padding: 8px 15px;
      border-radius: 5px;
      text-decoration: none;
      transition: 0.3s;
    }
    a.btn-back:hover { background: #0056b3; }
  </style>
</head>
<body>
  <main class="main">
    <div class="container">
      <h2>Detail Transaksi #<?= htmlspecialchars($transaksi['id']); ?></h2>

      <p><strong>Nama Pemesan:</strong> <?= htmlspecialchars($transaksi['nama_user']); ?></p>
      <p><strong>Tanggal Transaksi:</strong> <?= date('d-m-Y H:i', strtotime($transaksi['created_at'])); ?></p>
      <p><strong>Total Bayar:</strong> Rp <?= number_format($transaksi['subtotal'], 0, ',', '.'); ?></p>

      <div class="bukti">
        <h3>Bukti Pembayaran:</h3>
        <?php 
        $path = "../../../uploads/booking/" . $transaksi['bukti_pembayaran']; 
        if (!empty($transaksi['bukti_pembayaran']) && file_exists($path)): ?>
          <img src="<?= $path; ?>" alt="Bukti Pembayaran">
        <?php else: ?>
          <p>- Belum ada bukti pembayaran -</p>
        <?php endif; ?>
      </div>

      <h3>Detail Penyewaan Lapangan:</h3>
      <table>
        <thead>
          <tr>
            <th>Nama Lapangan</th>
            <th>Tanggal</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Durasi</th>
            <th>Harga</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if (mysqli_num_rows($detail) > 0):
            while ($row = mysqli_fetch_assoc($detail)): ?>
            <tr>
              <td><?= htmlspecialchars($row['nama_lapangan']); ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
              <td><?= htmlspecialchars($row['jam_mulai']); ?></td>
              <td><?= htmlspecialchars($row['jam_selesai']); ?></td>
              <td><?= htmlspecialchars($row['durasi']); ?> jam</td>
              <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile;
          else: ?>
            <tr><td colspan="6">Tidak ada detail lapangan untuk transaksi ini.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <a href="../../pages/transaksi.php" class="btn-back">← Kembali</a>
    </div>
  </main>
</body>
</html>
