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

$grafikQuery = mysqli_query($conn, "SELECT MONTH(created_at) AS bulan, SUM(subtotal) AS total FROM transaksi WHERE YEAR(created_at) = YEAR(CURDATE()) GROUP BY bulan ORDER BY bulan ASC");
$bulanArr = [];
$totalArr = [];
while ($row = mysqli_fetch_assoc($grafikQuery)) {
  $bulanArr[] = date("M", mktime(0, 0, 0, $row['bulan'], 1));
  $totalArr[] = $row['total'] ?? 0;
}
$bulanJSON = json_encode($bulanArr);
$totalJSON = json_encode($totalArr);

$jamQuery = mysqli_query($conn, "
    SELECT HOUR(created_at) AS jam, COUNT(*) AS total 
    FROM transaksi 
    GROUP BY jam ORDER BY jam ASC
");
$jamArr = [];
$totalJamArr = [];
while ($row = mysqli_fetch_assoc($jamQuery)) {
  $jamArr[] = $row['jam'] . ':00';
  $totalJamArr[] = $row['total'];
}
$jamJSON = json_encode($jamArr);
$totalJamJSON = json_encode($totalJamArr);
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

      <div class="charts-grid">
        <div class="chart-container">
          <h2><i class='bx bx-bar-chart'></i> Grafik Pendapatan</h2>
          <select id="filterSelect">
            <option value="tahun">Tahun Ini</option>
            <option value="bulan">Bulan Ini</option>
            <option value="minggu">Minggu Ini</option>
            <option value="hari">Hari Ini</option>
          </select>
          <canvas id="barChart"></canvas>
        </div>

        <div class="chart-container">
          <h2><i class='bx bx-time-five'></i> Grafik Jam Terlaris</h2>
          <canvas id="chartJamTerlaris"></canvas>
        </div>
      </div>
    </div>
  </main>

  <script>
    const bulan = <?php echo $bulanJSON; ?>;
    const total = <?php echo $totalJSON; ?>;

    const ctx = document.getElementById('barChart').getContext('2d');
    let barChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: bulan,
        datasets: [{
          data: total,
          backgroundColor: '#4CAF50',
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

    const jamLabels = <?php echo $jamJSON; ?>;
    const totalJam = <?php echo $totalJamJSON; ?>;

    new Chart(document.getElementById('chartJamTerlaris'), {
      type: 'line',
      data: {
        labels: jamLabels,
        datasets: [{
          data: totalJam,
          borderColor: '#4CAF50',
          backgroundColor: 'rgba(17,113,57,0.2)',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          title: {
            display: true,
            text: 'Jumlah Transaksi per Jam (Sepanjang Waktu)',
            color: '#4CAF50'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          },
          x: {
            title: {
              display: true,
              text: 'Jam'
            }
          }
        }
      }
    });
  </script>
</body>

</html>