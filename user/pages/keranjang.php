<?php
session_start();
include '../../includes/koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data keranjang user
$result = mysqli_query($conn, "
  SELECT k.*, l.nama_lapangan 
  FROM keranjang k
  JOIN lapangan l ON k.id_lapangan = l.id
  WHERE k.user_id = '$user_id'
  ORDER BY k.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keranjang Saya | ZonaFutsal</title>
  <link rel="stylesheet" href="../assets/css/pages.css?v=<?php echo filemtime('../assets/css/pages.css'); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    header {
      background: #333;
      color: #fff;
      padding: 15px 0;
    }

    nav {
      width: 90%;
      margin: auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo-text {
      font-weight: 700;
      color: white;
      text-decoration: none;
      font-size: 20px;
    }

    .btn-masuk,
    .btn-daftar,
    .btn-logout {
      color: white;
      background: #27ae60;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.2s;
    }

    .btn-logout {
      background: #e74c3c;
    }

    .btn-masuk:hover,
    .btn-daftar:hover,
    .btn-logout:hover {
      opacity: 0.9;
    }

    .container {
      max-width: 1000px;
      margin: 60px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th,
    td {
      text-align: center;
      padding: 12px 10px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #333;
      color: #fff;
      font-weight: 600;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .btn {
      padding: 6px 14px;
      border-radius: 6px;
      text-decoration: none;
      color: white;
      font-weight: 600;
      transition: 0.2s;
    }

    .btn-hapus {
      background-color: #e74c3c;
    }

    .btn-hapus:hover {
      background-color: #c0392b;
    }

    .btn-checkout {
      background-color: #27ae60;
    }

    .btn-checkout:hover {
      background-color: #219150;
    }

    .btn-back {
      display: inline-block;
      margin-top: 15px;
      background-color: #3498db;
    }

    .btn-back:hover {
      background-color: #2980b9;
    }

    .total-box {
      text-align: right;
      font-size: 18px;
      font-weight: 600;
      margin-top: 10px;
      color: #333;
    }

    .no-data {
      text-align: center;
      padding: 30px;
      font-size: 16px;
      color: #555;
    }

    input[type="checkbox"] {
      transform: scale(1.3);
      cursor: pointer;
    }

    .select-all {
      cursor: pointer;
      color: #007bff;
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <header>
    <nav>
      <a href="../index.php" class="logo-text">🏆 ZonaFutsal</a>
      <div>
        <?php if (isset($_SESSION['user_id'])): ?>
          <span style="margin-right:15px;">👋 <?= htmlspecialchars($_SESSION['nama']); ?></span>
          <a href="../logout.php" class="btn-logout">Keluar</a>
        <?php else: ?>
          <a href="../login.php" class="btn-masuk">Masuk</a>
          <a href="../register.php" class="btn-daftar">Daftar</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <div class="container">
    <h2>🛒 Keranjang Saya</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <form action="checkout.php" method="POST">
        <table>
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAll"></th>
              <th>Lapangan</th>
              <th>Tanggal</th>
              <th>Jam</th>
              <th>Durasi</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $grandTotal = 0;
            while ($row = mysqli_fetch_assoc($result)):
              $grandTotal += $row['total'];
            ?>
              <tr>
                <td><input type="checkbox" name="selected_ids[]" value="<?= $row['id']; ?>"></td>
                <td><?= htmlspecialchars($row['nama_lapangan']); ?></td>
                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                <td><?= htmlspecialchars($row['jam_mulai'] . ' - ' . $row['jam_selesai']); ?></td>
                <td><?= htmlspecialchars($row['durasi']); ?> jam</td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                <td>
                  <a href="../includes/booking/hapus_keranjang.php?id=<?= urlencode($row['id']); ?>"
                    class="btn btn-hapus"
                    onclick="return confirm('Yakin ingin menghapus item ini dari keranjang?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div class="total-box">
          Total Keseluruhan:
          <span style="color:#27ae60;">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></span>
        </div>

        <div style="text-align: right; margin-top: 20px;">
          <button type="submit" class="btn btn-checkout">Checkout yang Dipilih</button>
        </div>
      </form>

      <script>
        // Pilih semua checkbox
        const selectAll = document.getElementById('selectAll');
        selectAll.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
          checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
      </script>

    <?php else: ?>
      <div class="no-data">
        😢 Keranjang kamu masih kosong.<br><br>
        <a href="sewa.php" class="btn btn-back">← Kembali ke Daftar Lapangan</a>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
