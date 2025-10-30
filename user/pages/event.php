<?php
session_start();
include '../../includes/koneksi.php'; 
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

        <?php
        $query = "
            SELECT e.*, k.nama AS nama_kategori 
            FROM event e
            LEFT JOIN kategori k ON e.kategori_id = k.id
            ORDER BY e.id DESC
        ";
        $result = $conn->query($query);

        if ($result->num_rows > 0):
            while ($event = $result->fetch_assoc()):
                $imgPath = !empty($event['foto']) ? "../admin/uploads/" . $event['foto'] : "../assets/image/default_event.jpg";
        ?>
                <div class="event-card">
                    <div class="event-img" style="background-image:url('<?php echo $imgPath; ?>');"></div>
                    <div class="event-info">
                        <h3><?php echo htmlspecialchars($event['nama_event']); ?></h3>
                        <div class="event-meta">🏷️ <?php echo htmlspecialchars($event['nama_kategori']); ?></div>
                        <div class="event-desc"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></div>
                        <a href="#" class="btn">Lihat Detail</a>
                    </div>
                </div>
        <?php
            endwhile;
        else:
            echo "<p style='text-align:center;'>Belum ada event futsal yang tersedia.</p>";
        endif;
        ?>
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
