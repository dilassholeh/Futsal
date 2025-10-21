<?php 
include '../includes/koneksi.php';
include 'menu.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>
<body>
    <main class="main">
    <header class="header">
      <div class="search-bar">
        <input type="text" placeholder="Cari data atau laporan..." />
      </div>

      <div class="profile">
        <img src="../assets/image/user.png" alt="Admin" />
        <div class="info">
          <strong>Admin Sewa</strong>
          <span>Super Admin</span>
        </div>
      </div>
    </header>

    <div class="content">
      <h1>Dashboard</h1>

      <div class="cards">
        <div class="card">
          <h3>Total Lapangan</h3>
          <p>8</p>
        </div>
        <div class="card">
          <h3>Total Penyewa</h3>
          <p>125</p>
        </div>
        <div class="card">
          <h3>Transaksi Hari Ini</h3>
          <p>12</p>
        </div>
        <div class="card">
          <h3>Pendapatan Bulan Ini</h3>
          <p>Rp 8.750.000</p>
        </div>
      </div>

      <div class="row">
        <div class="chart-box">
          <h3> Grafik Pemesanan Mingguan</h3>
          <div class="chart-placeholder">[ Grafik Dummy di sini ]</div>
        </div>

        <div class="notif-box">
          <h3>Notifikasi Pemesanan</h3>
          <div class="notif-item">
            <strong>Rafi</strong> memesan Lapangan A
            <span>10:00 - 11:00 WIB</span>
          </div>
          <div class="notif-item">
            <strong>Dila</strong> memesan Lapangan B
            <span>14:00 - 15:00 WIB</span>
          </div>
          <div class="notif-item">
            <strong>Andi</strong> memesan Lapangan C
            <span>16:00 - 17:00 WIB</span>
          </div>
          <div class="notif-item">
            <strong>Sinta</strong> memesan Lapangan D
            <span>18:00 - 19:00 WIB</span>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>