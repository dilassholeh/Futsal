<?php
session_start();
include '../../includes/koneksi.php';
session_start();


$current_date = date('Y-m-d');
$query = "SELECT e.*, k.nama as kategori_nama 
          FROM event e 
          LEFT JOIN kategori k ON e.kategori_id = k.id 
          WHERE e.tanggal_berakhir >= '$current_date'
          ORDER BY e.tanggal_mulai ASC";
$result = $conn->query($query);

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
  <title>Event Futsal</title>
  <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/event.css?v=<?php echo filemtime('../assets/css/event.css'); ?>">
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

            <span class="user-name">👋 <?= htmlspecialchars($_SESSION['nama']); ?></span>
            <a href="../logout.php" class="btn-logout">Keluar</a>
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
      <h1>Event Futsal Terbaru</h1>
    </div>
  </section>

  <div class="event-container">
    <h2 class="title">Daftar Event Futsal</h2>

    <?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $tanggal_mulai = date('d M Y', strtotime($row['tanggal_mulai']));
        $tanggal_berakhir = date('d M Y', strtotime($row['tanggal_berakhir']));

        $foto_path = !empty($row['foto']) ? "../../uploads/event/{$row['foto']}" : "../assets/image/default-event.jpg";

        $status = '';
        $status_class = '';
        if (strtotime($current_date) < strtotime($row['tanggal_mulai'])) {
          $status = 'Akan Datang';
          $status_class = 'upcoming';
        } else {
          $status = 'Sedang Berlangsung';
          $status_class = 'ongoing';
        }

        echo "<div class='event-card'>
                    <div class='event-img'>
                        <img src='{$foto_path}' alt='{$row['nama_event']}'>
                        <span class='event-status {$status_class}'>{$status}</span>
                    </div>
                    <div class='event-info'>
                        <h3>{$row['nama_event']}</h3>
                        <div class='event-meta'>
                            <span>🏷️ Kategori: {$row['kategori_nama']}</span>
                            <span>📅 {$tanggal_mulai} - {$tanggal_berakhir}</span>
                        </div>
                        <div class='event-desc'>" . nl2br(substr($row['deskripsi'], 0, 150)) .
          (strlen($row['deskripsi']) > 150 ? '...' : '') . "</div>
                        <a href='detail_event.php?id={$row['id']}' class='btn'>Lihat Detail</a>
                    </div>
                </div>";
      }
    } else {
      echo "<div class='no-events'>
                    <p>Tidak ada event yang sedang berlangsung saat ini.</p>
                  </div>";
    }
    ?>
  </div>

  <div class="garis"></div>

  <?php
  include 'footer.php';
  ?>
</body>

</html>