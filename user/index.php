<?php
include '../includes/koneksi.php';
session_start();

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
        'nama' => 'Zona Futsal - Lapangan Modern',
        'foto' => 'assets/image/futsal.png'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Futsal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime('./assets/css/style.css'); ?>">
</head>

<body>
    <header>
        <nav class="nav">
            <div class="logo-container">
                <a href="company.php" class="logo-text">
                    <img src="assets/image/logo.png" alt="ZonaFutsal Logo" class="logo-img">
                    ZOFA
                </a>
            </div>
            <div class="sub-container">
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="../user/pages/sewa.php" class="active">Penyewaan</a></li>
                    <li><a href="../user/pages/event.php">Event</a></li>
                </ul>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <span class="user-name">👋 <?= htmlspecialchars($_SESSION['nama']); ?></span>
                        <a href="../logout.php" class="btn-logout">Keluar</a>
                    </div>
                <?php else: ?>
                    <a href="../login.php" class="btn-masuk">Masuk</a>
                    <a href="../register.php" class="btn-daftar">Daftar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero" id="hero" style="background-image:url('<?= $sliders[0]['foto']; ?>');">
        <div class="hero-overlay"></div>
        <div class="hero-content container">
            <h1>Bermain Futsal<br><span class="highlight">Lebih Seru di ZonaFutsal</span></h1>
            <p>Lapangan modern, bersih, dan nyaman. Booking mudah, harga bersahabat.</p>
            <a href="./pages/sewa.php" class="btn-primary big">Booking Sekarang</a>
        </div>
    </section>

   <!-- USER GUIDE -->
<section class="user-guide container">
  <h2>User Guide for First Timer</h2>
  <div class="steps">
    <ol>
      <li>Login atau daftar akun</li>
      <li>Pilih jadwal & lapangan</li>
      <li>Lakukan pembayaran</li>
      <li>Datang dan main di ZonaFutsal!</li>
    </ol>
  </div>
</section>

<!-- TESTIMONIAL -->
<section class="testimonial container">
  <h2>What Our Clients Say</h2>
  <div class="testi-grid">
    <div class="testi-item">
      <p>“Lapangan bersih dan booking mudah banget. Top!”</p>
      <h4>– Rian, Komunitas Futsal ITB</h4>
    </div>
    <div class="testi-item">
      <p>“ZonaFutsal bikin latihan jadi profesional.”</p>
      <h4>– Budi, Bandung United</h4>
    </div>
  </div>
</section>

<!-- STATISTICS -->
<section class="stats">
  <div class="stat-box">
    <div class="stat-number">10K+</div>
    <p>Pemain Terdaftar</p>
  </div>
  <div class="stat-box">
    <div class="stat-number">200+</div>
    <p>Komunitas Aktif</p>
  </div>
  <div class="stat-box">
    <div class="stat-number">50+</div>
    <p>Event per Tahun</p>
  </div>
</section>

<!-- BLOG -->
<section class="blog-section container">
  <h2>Blog & Artikel</h2>
  <div class="blog-grid">
    <div class="blog-card"><img src="assets/image/futsal.png"><h3>Tips Bermain Futsal Lebih Efektif</h3></div>
    <div class="blog-card"><img src="assets/image/futsal.png"><h3>Manfaat Futsal untuk Kesehatan</h3></div>
    <div class="blog-card"><img src="assets/image/futsal.png"><h3>Event ZonaFutsal 2025</h3></div>
  </div>
</section>

<!-- SERVICES -->
<section class="services container">
  <h2>Layanan Kami</h2>
  <div class="service-grid">
    <div class="service-card"><h3>Sewa Lapangan</h3><p>Lapangan indoor & outdoor berkualitas tinggi.</p></div>
    <div class="service-card"><h3>Event & Turnamen</h3><p>Bergabunglah dalam kompetisi seru setiap bulan.</p></div>
    <div class="service-card"><h3>Komunitas Futsal</h3><p>Temukan tim & lawan baru di ZonaFutsal.</p></div>
  </div>
</section>
