<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}

$totalEvent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM event WHERE CURDATE() BETWEEN tanggal_mulai AND tanggal_berakhir"))['total'] ?? 0;
$totalUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user"))['total'] ?? 0;

$qTotal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(subtotal) AS total FROM transaksi"));
$totalPendapatan = $qTotal['total'] ?? 0;

$qHari = mysqli_query($conn, "SELECT SUM(subtotal) AS total FROM transaksi WHERE DATE(created_at)=CURDATE()");
$omzetHari = mysqli_fetch_assoc($qHari)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo filemtime('../assets/css/dashboard.css'); ?>">
</head>

<body>
  <main class="main">
    <div class="header">
      <h1>Dashboard</h1>
      <div class="header-right">
        <div class="profile-card">
          <img src="../assets/image/<?php echo htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" class="profile-img">
          <div class="profile-info">
            <span class="profile-name"><?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
          </div>
          <a href="../logout.php" class="btn-logout"><i class='bx bx-log-out'></i></a>
        </div>
      </div>
    </div>

    <div class="bottom">

      <div class="cards-grid">
        <div class="card card-pendapatan"><i class='bx bx-wallet'></i>
          <div class="card-text">
            <p>Total Pendapatan<br><strong>Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></strong></p>
          </div>
        </div>
        <div class="card card-omzet"><i class='bx bx-money'></i>
          <div class="card-text">
            <p>Omzet Hari Ini<br><strong>Rp <?php echo number_format($omzetHari, 0, ',', '.'); ?></strong></p>
          </div>
        </div>
        <div class="card card-user"><i class='bx bx-user'></i>
          <div class="card-text">
            <p>User<br><strong><?php echo (int)$totalUser; ?></strong></p>
          </div>
        </div>
        <div class="card card-event"><i class='bx bx-calendar-event'></i>
          <div class="card-text">
            <p>Event<br><strong><?php echo (int)$totalEvent; ?></strong></p>
          </div>
        </div>
      </div>

      <div style="position:relative; padding-bottom:65%; height:0; margin-top:30px;">
        <iframe title="ChartDashboard"
                src="https://app.powerbi.com/view?r=eyJrIjoiZmVjOTE5NmMtMWI3YS00NzJhLWExOGItZjY4MmZmYjA4OTgyIiwidCI6ImE2OWUxOWU4LWYwYTQtNGU3Ny1iZmY2LTk1NjRjODgxOWIxNCJ9"
                style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
                allowFullScreen="true">
        </iframe>
      </div>

    </div>
  </main>
</body>
</html>