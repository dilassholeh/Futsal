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

  <style>
    .card {
      position: relative;
    }

    .card.disabled {
      opacity: 0.7;
      pointer-events: none;
    }

    .card.disabled::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.3);
      z-index: 1;
      border-radius: 12px;
    }

    .status-overlay {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 2;
      background: rgba(255, 255, 255, 0.95);
      padding: 15px 25px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .status-overlay i {
      font-size: 36px;
      margin-bottom: 8px;
      display: block;
    }

    .status-overlay.rusak {
      border: 2px solid #dc3545;
    }

    .status-overlay.rusak i {
      color: #dc3545;
    }

    .status-overlay.perbaikan {
      border: 2px solid #ffc107;
    }

    .status-overlay.perbaikan i {
      color: #ffc107;
    }

    .status-overlay h4 {
      margin: 0 0 5px 0;
      font-size: 16px;
      font-weight: 600;
    }

    .status-overlay p {
      margin: 0;
      font-size: 13px;
      color: #666;
    }

    .status-badge-card {
      position: absolute;
      top: 12px;
      right: 12px;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      z-index: 3;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-badge-card.tersedia {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .status-badge-card.rusak {
      background: linear-gradient(135deg, #dc3545, #c82333);
      color: white;
      box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .status-badge-card.perbaikan {
      background: linear-gradient(135deg, #ffc107, #ff9800);
      color: white;
      box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    }

    .btn-book.disabled {
      background: #6c757d;
      cursor: not-allowed;
      opacity: 0.6;
    }

    .btn-book.disabled:hover {
      background: #6c757d;
      transform: none;
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

          $status = $row['status'] ?? 'tersedia';
          $isDisabled = ($status == 'rusak' || $status == 'perbaikan');
          $cardClass = $isDisabled ? 'card disabled' : 'card';
          
          // Status text
          $statusText = '';
          $statusIcon = '';
          $statusClass = '';
          if ($status == 'rusak') {
            $statusText = 'Lapangan Rusak';
            $statusIcon = 'bx-error-circle';
            $statusClass = 'rusak';
          } elseif ($status == 'perbaikan') {
            $statusText = 'Sedang Perbaikan';
            $statusIcon = 'bx-wrench';
            $statusClass = 'perbaikan';
          }
      ?>
          <div class="<?= $cardClass; ?>">
            
            <!-- Status Badge -->
            <span class="status-badge-card <?= $status; ?>">
              <?= $status == 'tersedia' ? 'Tersedia' : ($status == 'rusak' ? 'Rusak' : 'Perbaikan'); ?>
            </span>

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

              <?php if ($isDisabled): ?>
                <button class="btn-book disabled" disabled>Tidak Tersedia</button>
              <?php else: ?>
                <a href="booking.php?id=<?= urlencode($row['id']); ?>" class="btn-book">Booking Sekarang</a>
              <?php endif; ?>

            </div>

            <!-- Status Overlay untuk lapangan tidak tersedia -->
            <?php if ($isDisabled): ?>
              <div class="status-overlay <?= $statusClass; ?>">
                <i class="bx <?= $statusIcon; ?>"></i>
                <h4><?= $statusText; ?></h4>
                <p>Mohon maaf, lapangan sedang tidak dapat digunakan</p>
              </div>
            <?php endif; ?>

          </div>
      <?php
        }
      } else {
        echo "<p class='no-data'>Belum ada data lapangan tersedia.</p>";
      }
      ?>
    </div>
  </section>

  <div class="garis"></div>

  <?php
  include 'footer.php';
  ?>

</body>

</html>