<?php
session_start();
include '../includes/koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZonaFutsal | Booking Lapangan</title>
       <link rel="stylesheet" href="../assets/css/user/pages.css?v=<?php echo filemtime('../assets/css/user/pages.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body data-loggedin="<?= isset($_SESSION['username']) ? 'true' : 'false' ?>">

    <header>
        <nav class="nav">
            <div class="logo-container">
                <a href="../index.php" class="logo-text">
                    <img src="../assets/image/logo.png" alt="ZonaFutsal Logo" class="logo-img">
                    ZonaFutsal
                </a>
            </div>
            <div class="sub-container">
                <ul active>
                    <li><a href="../index.php">Beranda</a></li>
                    <li><a href="sewa.php" class="active">Penyewaan</a></li>
                    <li><a href="event.php">Event</a></li>
                </ul>

                <?php if (isset($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <button class="btn-profil">
                            <img src="../assets/image/user.png" alt="Profil" class="profil-img">
                            <span><?= htmlspecialchars($_SESSION['username']); ?></span>
                            ⮟
                        </button>
                        <div class="dropdown-content">
                            <a href="./user/profil.php">Profil Saya</a>
                            <a href="../auth/logout.php" class="logout">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn-masuk">Masuk</a>
                    <a href="../auth/register.php" class="btn-daftar">Daftar</a>
                <?php endif; ?>

            </div>
        </nav>
    </header>

    <section class="hero">
        <img src="../assets/image/bakground.png" alt="ZonaFutsal" class="hero-img">
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
        <div class="card-grid">
            <?php
            $lapangan = ['1', '2', '3', '4', '5', '6'];
            foreach ($lapangan as $l): ?>
                <div class="card">
                    <img src="../assets/image/futsal.png" alt="Lapangan <?php echo $l; ?>">
                    <h3>Lapangan <?php echo $l; ?></h3>
                    <a href="booking.php?lapangan=<?php echo $l; ?>" class="btn-book">Booking</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="garis"></div>

    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p>Booking Futsal adalah platform modern untuk memesan lapangan, melihat jadwal, dan mengikuti event futsal secara online.</p>
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
            <p>Email: info@bookingfutsal.id</p>
            <p>Telp: +62 812 3456 7890</p>
            <p>Alamat: Jl. Raya Sport Center No. 88, Bandung</p>
        </div>
    </footer>

</body>
</html>
