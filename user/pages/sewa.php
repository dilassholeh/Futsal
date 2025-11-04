<?php
include '../../includes/koneksi.php'; 

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
                    <img src="../assets/image/logo_orange.png" alt="ZonaFutsal Logo" class="logo-img">
                    ZonaFutsal
                </a>
            </div>
            <div class="sub-container">
                <ul>
                    <li><a href="../index.php">Beranda</a></li>
                    <li><a href="sewa.php">Penyewaan</a></li>
                    <li><a href="event.php">Event</a></li>
                </ul>

                <a href="login.php" class="btn-masuk">Masuk</a>
                <a href="register.php" class="btn-daftar">Daftar</a>
            </div>
        </nav>

    </header>

  <section class="hero">
    <img src="../assets/image/latar.png" alt="ZonaFutsal" class="hero-img">
    <div class="hero-overlay">
      <h1>Booking Lapangan Futsal Kini Lebih Mudah!</h1>
      <a href="#lapangan">Mulai Booking</a>
    </div>
  </section>


  <section class="container" id="lapangan">
    <h2 class="section-title">Daftar Lapangan</h2>
    <div class="card-grid">
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          
          $fotoPath = "../../uploads/lapangan/" . $row['foto'];
          if (!file_exists($fotoPath) || empty($row['foto'])) {
            $fotoPath = "../assets/image/noimage.png"; 
          }
          ?>
          <div class="card">
            <img src="<?php echo $fotoPath; ?>" alt="<?php echo htmlspecialchars($row['nama_lapangan']); ?>">
            <div style="padding:15px;">
              <h3><?php echo htmlspecialchars($row['nama_lapangan']); ?></h3>
              <p>Harga Pagi: <b>Rp <?php echo number_format($row['harga_pagi'], 0, ',', '.'); ?></b></p>
              <p>Harga Malam: <b>Rp <?php echo number_format($row['harga_malam'], 0, ',', '.'); ?></b></p>
              <a href="booking.php?id=<?php echo urlencode($row['id']); ?>" class="btn-book">Booking</a>
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

 
  <footer>
    <div class="footer-section">
      <h4>Tentang Kami</h4>
      <p>ZonaFutsal adalah platform modern untuk memesan lapangan dan mengikuti event futsal secara online.</p>
    </div>
    <div class="footer-section">
      <h4>Link Cepat</h4>
      <a href="../index.html">Beranda</a>
      <a href="jadwal.html">Jadwal</a>
      <a href="event.html">Event</a>
      <a href="kontak.html">Kontak</a>
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
