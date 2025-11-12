<?php
include '../../includes/koneksi.php';
session_start();

$result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZonaFutsal | Booking Lapangan</title>
  <link rel="stylesheet" href="../assets/css/pages.css?v=<?php echo filemtime('../assets/css/pages.css'); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

  <header>
    <nav class="nav">
      <div class="logo-container">
        <a href="company.php" class="logo-text">
          <img src="../assets/image/logo.png" alt="ZonaFutsal Logo" class="logo-img">
          ZOFA
        </a>
      </div>
      <div class="sub-container">
        <ul>
          <li><a href="../index.php">Beranda</a></li>
          <li><a href="sewa.php" class="active">Penyewaan</a></li>
          <li><a href="event.php">Event</a></li>
        </ul>

        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="user-menu">
            <span class="user-name">👋 <?= htmlspecialchars($_SESSION['nama']); ?></span>
            <a href="../logout.php" class="btn-logout">Keluar</a>
          </div>
        <?php else: ?>
          <a href="../login.php" class="btn-masuk">Masuk</a>
          <a href="../register.php" class="btn-daftar">Daftar</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <section class="hero">
    <img src="../assets/image/bakground.png" alt="ZonaFutsal" class="hero-img">
    <div class="hero-overlay">
      <h1>Booking Lapangan Futsal Kini Lebih Mudah!</h1>
      <a href="#lapangan" class="btn-scroll">Mulai Booking</a>
    </div>
  </section>

  <section class="container" id="lapangan">
    <h2 class="section-title">Daftar Lapangan</h2>

    <div class="card-grid">
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $fotoPath = "../../uploads/lapangan/" . $row['foto'];
          if (empty($row['foto']) || !file_exists($fotoPath)) {
            $fotoPath = "../assets/image/noimage.png";
          }
      ?>
          <div class="card">
            <img src="<?= htmlspecialchars($fotoPath); ?>"
              alt="<?= htmlspecialchars($row['nama_lapangan']); ?>">
            <div class="card-content">
              <h3><?= htmlspecialchars($row['nama_lapangan']); ?></h3>

              <p>
                <b>Pagi:</b> Rp <?= number_format($row['harga_pagi'], 0, ',', '.'); ?>/
                <b>Malam:</b> Rp <?= number_format($row['harga_malam'], 0, ',', '.'); ?>
              </p>

              <a href="booking.php?id=<?= urlencode($row['id']); ?>" class="btn-book">Booking</a>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p style='text-align:center;'>Belum ada data lapangan tersedia.</p>";
      }
      ?>
    </div>
  </section>

  <div class="garis"></div>

  <footer>
    <div class="footer-section">
      <h4>Tentang Kami</h4>
      <p>ZonaFutsal adalah platform modern untuk memesan lapangan dan mengikuti event futsal secara online.</p>
    </div>
    <div class="footer-section">
      <h4>Link Cepat</h4>
      <a href="../index.php">Beranda</a>
      <a href="jadwal.php">Jadwal</a>
      <a href="event.php">Event</a>
      <a href="kontak.php">Kontak</a>
    </div>
    <div class="footer-section">
      <h4>Hubungi Kami</h4>
      <p>Email: info@zonafutsal.id</p>
      <p>Telp: +62 812 3456 7890</p>
      <p>Alamat: Jl. Raya Sport Center No. 88, Bandung</p>
    </div>
  </footer>

</body>

</html>