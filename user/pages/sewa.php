<?php
session_start();
include '../../includes/koneksi.php';
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

    <!-- ===== HEADER / NAV ===== -->
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
                    <a href="../login.php" class="btn-masuk">Masuk</a>
                    <a href="../register.php" class="btn-daftar">Daftar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- ===== HERO ===== -->
    <section class="hero">
        <img src="../assets/image/latar.png" alt="ZonaFutsal" class="hero-img">
        <div class="hero-overlay">
            <h1>Booking Lapangan Futsal Kini Lebih Mudah!</h1>
            <a href="#lapangan">Mulai Booking</a>
        </div>
    </section>

    <!-- ===== FASILITAS ===== -->
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

    <!-- ===== LAPANGAN ===== -->
    <section class="container" id="lapangan">
        <h2 class="section-title">Daftar Lapangan</h2>
        <div class="card-grid">
            <?php
            $query = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id DESC");
            if (mysqli_num_rows($query) > 0):
                while ($row = mysqli_fetch_assoc($query)):
            ?>
                <div class="card">
                    <img src="../../uploads/<?php echo htmlspecialchars($row['foto']); ?>" 
                         alt="<?php echo htmlspecialchars($row['nama_lapangan']); ?>">
                    <h3><?php echo htmlspecialchars($row['nama_lapangan']); ?></h3>
                    <p class="harga">Harga Pagi: Rp <?php echo number_format($row['harga_pagi'], 0, ',', '.'); ?></p>
                    <p class="harga">Harga Malam: Rp <?php echo number_format($row['harga_malam'], 0, ',', '.'); ?></p>
                    <a href="booking.php?id=<?php echo $row['id']; ?>" class="btn-book">Booking</a>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <p class="no-data">Belum ada data lapangan tersedia.</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="garis"></div>

    <!-- ===== FOOTER ===== -->
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
