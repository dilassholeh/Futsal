<?php
session_start();
// sementara belum koneksi ke database
// include '../../includes/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Futsal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/event.css?v=<?php echo filemtime('../assets/css/event.css'); ?>">
</head>

<body>
    <header>
        <nav class="nav">
            <div class="logo-container">
                <a href="../index.php" class="logo-text">
                    <img src="../assets/image/logo_orange.png" alt="ZonaFutsal Logo" class="logo-img">
                    ZonaFutsal
                </a>
            </div>
            <div class="sub-container">
                <ul>
                    <li><a href="../index.php">Beranda</a></li>
                    <li><a href="sewa.php">Penyewaan</a></li>
                    <li><a href="event.php" class="active">Event</a></li>
                </ul>

                <?php if (isset($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <button class="btn-profil">
                            <img src="../assets/image/user.png" alt="Profil" class="profil-img">
                            <span><?= htmlspecialchars($_SESSION['username']); ?></span>
                            ⮟
                        </button>
                        <div class="dropdown-content">
                            <a href="profil.php">Profil Saya</a>
                            <a href="../auth/logout.php" class="logout">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../login.php" class="btn-masuk">Masuk</a>
                    <a href="../register.php" class="btn-daftar">Daftar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <section class="hero">
        <img src="../assets/image/latar.png" alt="ZonaFutsal" class="hero-img">
        <div class="hero-overlay">
            <h1>Event Futsal Terbaru</h1>
        </div>
    </section>

    <div class="event-container">
        <h2 class="title">Daftar Event Futsal</h2>

        <!-- Data event statis untuk tampilan awal -->
        <div class="event-card">
            <div class="event-img" style="background-image:url('../assets/image/event1.jpg');"></div>
            <div class="event-info">
                <h3>Turnamen Antar Sekolah 2025</h3>
                <div class="event-meta">🏷️ Kategori: Pelajar</div>
                <div class="event-desc">Kompetisi seru antar sekolah menengah di Bandung. Tunjukkan kemampuan timmu!</div>
                <a href="#" class="btn">Lihat Detail</a>
            </div>
        </div>

        <div class="event-card">
            <div class="event-img" style="background-image:url('../assets/image/event2.jpg');"></div>
            <div class="event-info">
                <h3>ZonaFutsal Cup</h3>
                <div class="event-meta">🏷️ Kategori: Umum</div>
                <div class="event-desc">Event tahunan terbesar ZonaFutsal dengan hadiah total puluhan juta rupiah.</div>
                <a href="#" class="btn">Lihat Detail</a>
            </div>
        </div>

        <div class="event-card">
            <div class="event-img" style="background-image:url('../assets/image/event3.jpg');"></div>
            <div class="event-info">
                <h3>Futsal Ramadhan 2025</h3>
                <div class="event-meta">🏷️ Kategori: Komunitas</div>
                <div class="event-desc">Event malam hari selama bulan Ramadhan. Buka bersama lalu bertanding santai!</div>
                <a href="#" class="btn">Lihat Detail</a>
            </div>
        </div>
    </div>

    <div class="garis"></div>

    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p>ZonaFutsal adalah platform modern untuk memesan lapangan, melihat jadwal, dan mengikuti event futsal secara online.</p>
        </div>

        <div class="footer-section">
            <h4>Link Cepat</h4>
            <a href="../index.php">Beranda</a>
            <a href="sewa.php">Penyewaan</a>
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
