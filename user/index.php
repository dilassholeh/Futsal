<?php
include '../includes/koneksi.php';
session_start();

// Ambil data slider dari database
$querySlider = mysqli_query($conn, "SELECT * FROM slider ORDER BY id DESC");
$sliders = [];

while ($row = mysqli_fetch_assoc($querySlider)) {
    $sliders[] = [
        'nama' => $row['nama_slider'],
        'foto' => "../uploads/slider/" . $row['foto']
    ];
}

if (empty($sliders)) {
    $sliders[] = [
        'nama' => 'Zona Futsal',
        'foto' => 'assets/image/futsal.png'
    ];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime('./assets/css/style.css'); ?>">

</head>

<body>
    <header>
        <nav class="nav">
            <div class="logo-container">
                <a href="../index.php" class="logo-text">
                    <img src="./assets/image/logo_orange.png" alt="ZonaFutsal Logo" class="logo-img">
                    ZonaFutsal
                </a>
            </div>
            <div class="sub-container">
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="./pages/sewa.php">Penyewaan</a></li>
                    <li><a href="./pages/event.php">Event</a></li>
                </ul>

                <a href="login.php" class="btn-masuk">Masuk</a>
                <a href="register.php" class="btn-daftar">Daftar</a>
            </div>
        </nav>

    </header>

    <section class="hero" id="hero" style="background: url('<?= $sliders[0]['foto']; ?>') center/cover no-repeat;">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1><?= htmlspecialchars($sliders[0]['nama']); ?></h1>
        </div>
    </section>
    <div class="search-section">
        <div class="search-box">
            <div class="info-item">
                <h4>📍 Alamat</h4>
                <p>Jl. Raya Sport No. 21, Bandung</p>
            </div>

            <div class="divider"></div>

            <div class="info-item">
                <h4>⏰ Jam Buka</h4>
                <p>Setiap Hari: 07.00 - 23.00 WIB</p>
            </div>

            <div class="divider"></div>

            <div class="info-item">
                <h4>☎️ Kontak</h4>
                <p>0812-3456-7890</p>
            </div>

            <div class="divider"></div>


            <a href="./pages/sewa.php" class="btn-search">Booking Sekarang</a>
        </div>
    </div>


    <section class="about">
        <div class="about-container">
            <div class="about-images">
                <div class="left-images">
                    <img src="./assets/image/futsal.png" alt="Lapangan Futsal 1">
                    <img src="./assets/image/futsal.png" alt="Lapangan Futsal 2">
                </div>
                <div class="right-image">
                    <img src="./assets/image/futsal.png" alt="Lapangan Futsal 3">
                </div>
            </div>

            <div class="about-text">
                <h2>Tentang ZonaFutsal</h2>
                <p>
                    ZonaFutsal hadir untuk memberikan pengalaman bermain futsal terbaik dengan lapangan berstandar internasional,
                    pencahayaan LED modern, serta fasilitas yang nyaman dan bersih. Kami berkomitmen menghadirkan suasana
                    olahraga yang menyenangkan bagi semua kalangan — baik pertandingan santai maupun turnamen profesional.
                </p>

                <div class="price-cards">
                    <div class="price-card">
                        <h3>Harga Sewa Siang</h3>
                        <p>Rp 40.000 / jam</p>
                    </div>
                    <div class="price-card">
                        <h3>Harga Sewa Malam</h3>
                        <p>Rp 80.000 / jam</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="facilities-section">
        <h2 class="section-title">Fasilitas</h2>

        <div class="facilities">
            <div class="facility-card">
                <img src="https://cdn-icons-png.flaticon.com/512/2329/2329315.png" alt="Parkir">
                <h3>Tempat Parkir</h3>
            </div>
            <div class="facility-card">
                <img src="https://cdn-icons-png.flaticon.com/512/69/69524.png" alt="Toilet">
                <h3>Kamar Mandi</h3>
            </div>
            <div class="facility-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Musholla">
                <h3>Musholla</h3>
            </div>
            <div class="facility-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3480/3480721.png" alt="Kantin">
                <h3>Kantin</h3>
            </div>
            <div class="facility-card">
                <img src="https://cdn-icons-png.flaticon.com/512/2921/2921822.png" alt="Kafe">
                <h3>Kafe</h3>
            </div>
        </div>

    </section>



    <section class="testimonial">
        <div class="text">
            <div class="quote-icon">❝</div>
            <h2>Apa Kata Mereka tentang Zona Futsal</h2>
        </div>

        <div class="card">
            <p>
                My buying experience was amazing. The staff treated me kindly and answered all my questions.
                The quality and performance exceeded my expectations. I’m very satisfied with the service!
            </p>
            <div class="stars">★★★★★</div>
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
            <a href="index.php">Beranda</a>
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

    <script>
        const sliders = <?= json_encode($sliders); ?>;
        let index = 0;

        setInterval(() => {
            const hero = document.getElementById('hero');
            hero.style.background = `url('${sliders[index].foto}') center/cover no-repeat`;
            index = (index + 1) % sliders.length;
        }, 4000);
    </script>
    <!-- <script src="./assets/js/script.js"></script> -->
</body>

</html>