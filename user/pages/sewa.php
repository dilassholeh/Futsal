<?php
include '../../includes/koneksi.php';
session_start();

$result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id ASC");

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
  <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/pages.css?v=<?php echo filemtime('../assets/css/pages.css'); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

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
        </ul>


        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="user-menu">
            <a href="keranjang.php" class="btn-cart">
              <i class="bx bx-cart"></i>
              <?php if ($jumlahKeranjang > 0): ?>
                <span class="cart-count"><?= $jumlahKeranjang; ?></span>
              <?php endif; ?>
            </a>
            <div class="profile-card">
              <a href="user.php" class="profile-link">
                <div class="profile-info">
                  <img src="../assets/image/<?= $_SESSION['foto'] ?? 'profil.png'; ?>" alt="Profile" class="profile-img">
                  <div class="profile-text">
                    <span class="profile-name"><?= $_SESSION['nama'] ?? 'User'; ?></span>
                    <small class="profile-role"><?= $_SESSION['username'] ?? ''; ?></small>
                  </div>
                </div>
              </a>
            </div>



          <?php else: ?>
            <div class="user-menu">
              <a href="../login.php" class="btn-masuk">Masuk</a>
              <a href="../register.php" class="btn-daftar">Daftar</a>
            </div>
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

    <div class="filter-bar">
      <form method="GET" action="sewa.php" class="filter-form">
        <select name="jenis" class="filter-input">
          <option value="">Semua Jenis</option>
          <option value="indoor">Indoor</option>
          <option value="outdoor">Outdoor</option>
        </select>
        <select name="waktu" class="filter-input">
          <option value="">Semua Waktu</option>
          <option value="pagi">Pagi</option>
          <option value="malam">Malam</option>
        </select>
        <input type="text" name="cari" placeholder="Cari lapangan..." class="filter-search">
        <button type="submit" class="filter-btn">Cari</button>
      </form>
    </div>

    <div class="card-grid fade-in">
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $fotoPath = "../../uploads/lapangan/" . $row['foto'];
          if (empty($row['foto']) || !file_exists($fotoPath)) {
            $fotoPath = "../assets/image/noimage.png";
          }
      ?>
          <div class="card">

            <div class="card-img">
              <img src="<?= htmlspecialchars($fotoPath); ?>" alt="<?= htmlspecialchars($row['nama_lapangan']); ?>">
            </div>

            <div class="card-content">
              <h3><?= htmlspecialchars($row['nama_lapangan']); ?></h3>
              <p class="card-desc"><?= htmlspecialchars($row['deskripsi'] ?? 'Lapangan futsal berkualitas tinggi untuk semua kalangan.'); ?></p>


              <div class="price-box">
                <span class="pagi">Pagi: <b>Rp <?= number_format($row['harga_pagi'], 0, ',', '.'); ?></b></span>
                <span class="malam">Malam: <b>Rp <?= number_format($row['harga_malam'], 0, ',', '.'); ?></b></span>
              </div>

              <a href="booking.php?id=<?= urlencode($row['id']); ?>" class="btn-book">Booking Sekarang</a>

            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='no-data'>Belum ada data lapangan tersedia.</p>";
      }
      ?>
    </div>
  </section>

  </section>

  <div class="garis"></div>

  <?php
  include 'footer.php';
  ?>

</body>

</html>