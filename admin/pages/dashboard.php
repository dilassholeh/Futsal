<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}

$totalLapangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lapangan"))['total'];
$totalKategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori"))['total'];
$totalSlider   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM slider"))['total'];
$totalEvent    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM event"))['total'];
$totalUser     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user"))['total'];

$qTotal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(subtotal) AS total FROM transaksi"));
$totalPendapatan = $qTotal['total'] ?? 0;

$qHari = mysqli_query($conn, "SELECT SUM(subtotal) AS total FROM transaksi WHERE DATE(created_at)=CURDATE()");
$omzetHari = mysqli_fetch_assoc($qHari)['total'] ?? 0;

$grafikQuery = mysqli_query($conn, "
    SELECT MONTH(created_at) AS bulan, SUM(subtotal) AS total
    FROM transaksi
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY bulan ORDER BY bulan ASC
");
$bulanArr = [];
$totalArr = [];
while ($row = mysqli_fetch_assoc($grafikQuery)) {
  $bulanArr[] = date("M", mktime(0, 0, 0, $row['bulan'], 1));
  $totalArr[] = $row['total'] ?? 0;
}
$bulanJSON = json_encode($bulanArr);
$totalJSON = json_encode($totalArr);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>

  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo filemtime('../assets/css/dashboard.css'); ?>">
</head>

<body>

  <main class="main">
    <div class="header">
      <div class="header-left">
        <h1>Dashboard</h1>
        <p>Selamat Datang di Perencanaan</p>
      </div>
      <div class="header-right">
        <div class="notif"><i class='bx bxs-bell'></i></div>
        <div class="profile">
          <img
            src="../assets/image/<?= $_SESSION['admin_foto'] ?? 'profil.png'; ?>"
            alt="Profile"
            style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
          <span><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
        </div>
      </div>
    </div>

    <div class="bot">

      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari...">
        <i class='bx bx-search'></i>
      </div>
      <div class="cards">
        <div class="card">
          <div class="card-top">
            <h3>Lapangan</h3>
            <i class='bx bx-football'></i>
          </div>

          <div class="card-info">
            <p><?= $totalLapangan ?></p>
          </div>
        </div>
        <div class="card">
          <div class="card-top">
            <h3>Kategori</h3>
            <i class='bx bx-category'></i>
          </div>

          <div class="card-info">
            <p><?= $totalKategori ?></p>
          </div>
        </div>
        <div class="card">
          <div class="card-top">
            <h3>Slider</h3>
            <i class='bx bx-slideshow'></i>

          </div>
          <div class="card-info">
            <p><?= $totalSlider ?></p>
          </div>
        </div>
        <div class="card">
          <div class="card-top">
            <h3>Event</h3>
            <i class='bx bx-calendar-event'></i>

          </div>
          <div class="card-info">
            <p><?= $totalEvent ?></p>
          </div>
        </div>
        <div class="card">
          <div class="card-top">
            <h3>User</h3>
            <i class='bx bx-user'></i>

          </div>
          <div class="card-info">
            <p><?= $totalUser ?></p>
          </div>
        </div>
      </div>

      <div class="bottom-section">
        <div class="chart-container" style="flex:2;">
          <div class="chart-header" style="display:flex; justify-content:space-between;">
            <h2>Grafik Transaksi</h2>
            <div class="filter-box">
              <i class='bx bx-filter-alt'></i>
              <select id="filterSelect">
                <option value="tahun">Tahun Ini</option>
                <option value="bulan">Bulan Ini</option>
                <option value="minggu">Minggu Ini</option>
                <option value="hari">Hari Ini</option>
              </select>
            </div>
          </div>
          <canvas id="barChart"></canvas>
        </div>

        <div style="flex:1; display:flex; flex-direction:column; gap:20px;">
          <div class="total-card">
            <i class='bx bx-wallet'></i>
            <div class="card-info">
              <h3>Total Pendapatan</h3>
              <p style="font-size:20px; margin-top:10px;">
                <strong>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></strong>
              </p>
            </div>
          </div>

          <div class="omzet-card">
            <i class='bx bx-money'></i>
            <div class="card-info">
              <h3>Omzet</h3>

              <div class="omzet-filter" style="margin:10px 0;">
                <span class="active" data-target="hari">Hari</span>
                <span data-target="minggu">Minggu</span>
                <span data-target="bulan">Bulan</span>
                <span data-target="tahun">Tahun</span>
              </div>

              <p id="omzet-value"><strong>Rp <?= number_format($omzetHari, 0, ',', '.'); ?></strong></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    const bulan = <?= $bulanJSON ?>;
    const total = <?= $totalJSON ?>;

    const ctx = document.getElementById('barChart').getContext('2d');
    let barChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: bulan,
        datasets: [{
          data: total,
          backgroundColor: '#117139',
          borderRadius: 8
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    document.getElementById('filterSelect').addEventListener('change', function() {
      fetch("get_grafik.php?type=" + this.value)
        .then(res => res.json())
        .then(data => {
          barChart.data.labels = data.bulan;
          barChart.data.datasets[0].data = data.total;
          barChart.update();
        });
    });

    document.querySelectorAll('.omzet-filter span').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.omzet-filter span').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        fetch("get_omzet.php?type=" + btn.dataset.target)
          .then(res => res.json())
          .then(data => {
            document.getElementById("omzet-value").innerHTML = "<strong>Rp " + data.total + "</strong>";
          });
      });
    });
  </script>
</body>

</html>