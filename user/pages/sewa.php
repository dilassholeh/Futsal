<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZonaFutsal | Booking Lapangan</title>
    <link rel="stylesheet" href="../assets/css/pages.css?v=<?php echo filemtime('../assets/css/pages.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body data-loggedin="<?= isset($_SESSION['username']) ? 'true' : 'false' ?>">

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

    <section class="container" id="fasilitas">
        <h2 class="section-title">Fasilitas Tersedia</h2>
        <div class="fasilitas-tabs">
            <button class="tab-item active">Semua</button>
            <button class="tab-item">Bola Futsal</button>
            <button class="tab-item">Ruang Ganti</button>
            <button class="tab-item">Parkir</button>
            <button class="tab-item">Kantin</button>
            <button class="tab-item">Wi-Fi</button>
            <button class="tab-item">Pencahayaan</button>
        </div>
    </section>

    <section class="container" id="lapangan">
        <h2 class="section-title">Daftar Lapangan</h2>
        <div class="card-grid">
            <div class="card">
                <img src="../assets/image/lap1.jpg" alt="Lapangan A">
                <h3>Lapangan A</h3>
                <p class="harga">Harga Pagi: Rp 100.000</p>
                <p class="harga">Harga Malam: Rp 150.000</p>
                <a href="booking.php?id=1" class="btn-book">Booking</a>
            </div>

            <div class="card">
                <img src="../assets/image/lap2.jpg" alt="Lapangan B">
                <h3>Lapangan B</h3>
                <p class="harga">Harga Pagi: Rp 120.000</p>
                <p class="harga">Harga Malam: Rp 170.000</p>
                <a href="booking.php?id=2" class="btn-book">Booking</a>
            </div>

            <div class="card">
                <img src="../assets/image/lap3.jpg" alt="Lapangan C">
                <h3>Lapangan C</h3>
                <p class="harga">Harga Pagi: Rp 90.000</p>
                <p class="harga">Harga Malam: Rp 130.000</p>
                <a href="booking.php?id=3" class="btn-book">Booking</a>
            </div>
        </div>
    </section>

    <div class="garis"></div>

    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p>ZonaFutsal adalah platform modern untuk memesan lapangan, melihat jadwal, dan mengikuti event futsal secara online.</p>
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