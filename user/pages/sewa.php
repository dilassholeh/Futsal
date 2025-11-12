<?php
include '../../includes/koneksi.php';
session_start();

// Ambil semua data lapangan
$result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id ASC");

// Hitung jumlah item di keranjang user (jika sudah login)
$jumlahKeranjang = 0;
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $queryCart = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM keranjang WHERE user_id = '$user_id'");
  $cartData = mysqli_fetch_assoc($queryCart);
  $jumlahKeranjang = $cartData['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZonaFutsal | Booking Lapangan</title>
  <link rel="stylesheet" href="../assets/css/pages.css?v=<?php echo filemtime('../assets/css/pages.css'); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Tombol keranjang bergaya netral */
    .btn-cart {
      display: inline-flex;
      align-items: center;
      color: #333;
      padding: 8px 12px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      margin-left: 15px;
      transition: 0.3s;
    }

    .btn-cart:hover {
      color: #000;
      text-decoration: underline;
    }

    /* Angka jumlah item */
    .cart-count {
      background: #dc3545;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 12px;
      margin-left: 5px;
      color: white;
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 10px;
    }
  </style>
</head>

<body>

  <header>
    <nav class="nav">
      <div class="logo-container">
        <a href="../index.php" class="logo-text">
          <img src="../assets/image/logo.png" alt="ZonaFutsal Logo" class="logo-img">
          ZOFA
        </a>
      </div>

      <div class="sub-container">
        <ul>
          <li><a href="../index.php">Beranda</a></li>
          <li><a href="sewa.php" class="active">Penyewaan</a></li>
          <li><a href="event.php">Event</a></li>

          <!-- 🔹 Tombol Keranjang + Jumlah Item -->
          <?php if (isset($_SESSION['user_id'])): ?>
            <li>
              <a href="keranjang.php" class="btn-cart">
                🛒 Keranjang
                <?php if ($jumlahKeranjang > 0): ?>
                  <span class="cart-count"><?= $jumlahKeranjang; ?></span>
                <?php endif; ?>
              </a>
            </li>
          <?php endif; ?>
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
            <img src="<?= htmlspecialchars($fotoPath); ?>" alt="<?= htmlspecialchars($row['nama_lapangan']); ?>">
            <div class="card-content">
              <h3><?= htmlspecialchars($row['nama_lapangan']); ?></h3>

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
