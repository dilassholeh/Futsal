<?php
include '../../includes/koneksi.php';
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Perusahaan | ZonaFutsal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime('../assets/css/style.css'); ?>">
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

    <section class="hero" style="background: url('../assets/image/futsal.png') center/cover no-repeat;">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>Tentang Perusahaan Kami</h1>
            <p>ZonaFutsal — lebih dari sekadar lapangan, kami adalah komunitas olahraga!</p>
        </div>
    </section>

    <section class="company-section">
        <div class="company-container">
            <div class="company-text">
                <h2>Profil Perusahaan</h2>
                <p>
                    Didirikan pada tahun 2020, <strong>ZonaFutsal</strong> merupakan pusat olahraga futsal terkemuka di Bandung
                    yang menyediakan fasilitas lengkap, aman, dan nyaman bagi para pemain dari berbagai kalangan.
                    Dengan semangat sportivitas dan komunitas, kami berkomitmen untuk mendorong gaya hidup aktif dan sehat
                    melalui olahraga futsal.
                </p>
            </div>

            <div class="company-image">
                <img src="../assets/image/company_team.jpg" alt="Tim ZonaFutsal">
            </div>
        </div>
    </section>

    <section class="vision-mission">
        <h2>Visi & Misi Kami</h2>
        <div class="vision-cards">
            <div class="vision-card">
                <h3>Visi</h3>
                <p>
                    Menjadi pusat futsal terbaik di Indonesia yang mengedepankan kualitas, pelayanan, dan kebersamaan.
                </p>
            </div>
            <div class="vision-card">
                <h3>Misi</h3>
                <ul>
                    <li>Memberikan fasilitas futsal berkualitas tinggi.</li>
                    <li>Mendukung perkembangan atlet muda lokal.</li>
                    <li>Membangun komunitas olahraga yang sehat dan positif.</li>
                    <li>Menyediakan layanan pemesanan lapangan yang modern dan mudah diakses.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="team-section">
        <h2>Tim Kami</h2>
        <div class="team-container">
            <div class="team-card">
                <img src="./assets/image/team1.jpg" alt="CEO">
                <h3>Raden Alzen Arazin</h3>
                <p>CEO & Founder</p>
            </div>
            <div class="team-card">
                <img src="./assets/image/team2.jpg" alt="Manager">
                <h3>Putri Larasati</h3>
                <p>Manajer Operasional</p>
            </div>
            <div class="team-card">
                <img src="./assets/image/team3.jpg" alt="Marketing">
                <h3>Rafi Kurniawan</h3>
                <p>Kepala Marketing</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p>ZonaFutsal menyediakan platform pemesanan lapangan dan event futsal terbaik di Indonesia.</p>
        </div>

        <div class="footer-section">
            <h4>Link Cepat</h4>
            <a href="index.php">Beranda</a>
            <a href="company.php">Company</a>
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
