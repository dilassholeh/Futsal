<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    $_SESSION['user_id'] = $_SESSION['admin_id'];
}

include '../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

// Jika halaman dibuka dari admin (Lihat Website) dan admin memang login
if (isset($_GET['admin_view']) && isset($_SESSION['from_admin']) && $_SESSION['from_admin'] === true && isset($_SESSION['admin_id'])) {
  $_SESSION['user_id'] = $_SESSION['admin_id'];
  $_SESSION['user_name'] = $_SESSION['admin_nama'] ?? null;
  $_SESSION['user_nohp'] = $_SESSION['admin_nohp'] ?? null;
  $_SESSION['user_role'] = 'admin';
}

// Ambil event yang masih berjalan
$current_date = date('Y-m-d');
$esc_date = $conn->real_escape_string($current_date);
$query = "SELECT e.*, k.nama as kategori_nama 
          FROM event e 
          LEFT JOIN kategori k ON e.kategori_id = k.id 
          WHERE e.tanggal_berakhir >= '$esc_date'
          ORDER BY e.tanggal_mulai ASC";
$result = $conn->query($query);

// Inisialisasi agar header/footer tidak error jika tidak ada user
$jumlahKeranjang = 0;
$notifBaru = 0;

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
  $user_id = $conn->real_escape_string($_SESSION['user_id']);

  // COUNT keranjang
  $q1 = "SELECT COUNT(*) AS jumlah FROM keranjang WHERE user_id = '$user_id'";
  $res1 = $conn->query($q1);
  if ($res1) {
    $cartData = $res1->fetch_assoc();
    $jumlahKeranjang = isset($cartData['jumlah']) ? (int)$cartData['jumlah'] : 0;
  } else {
    $jumlahKeranjang = 0;
  }

  // COUNT pesan baru
  $q2 = "SELECT COUNT(*) AS jumlah_baru FROM pesan WHERE user_id = '$user_id' AND status = 'baru'";
  $res2 = $conn->query($q2);
  if ($res2) {
    $notifBaruData = $res2->fetch_assoc();
    $notifBaru = isset($notifBaruData['jumlah_baru']) ? (int)$notifBaruData['jumlah_baru'] : 0;
  } else {
    $notifBaru = 0;
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zona Futsal</title>
  <link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo filemtime('./assets/css/style.css'); ?>">
  <style>
    :root {
      --green: #2e7d32;
      --green-dark: #256628;
      --soft-bg: #f9fff9;
      --card: #ffffff;
      --muted: #666;
      --border: #e9eee7;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: "Poppins", sans-serif;
      background: #fff;
      color: #222;
      line-height: 1.6;
    }

    .hero2 {
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      gap: 36px;
      padding: 80px 6%;
      align-items: center;
      background: linear-gradient(180deg, #fbfff9, #ffffff);
    }

    .hero-left h1 {
      font-size: 42px;
      font-weight: 700;
      margin-bottom: 14px;
    }

    .hero-left h1 span {
      color: var(--green);
    }

    .hero-left p {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .btn-primary {
      background: var(--green);
      padding: 12px 22px;
      border-radius: 10px;
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      transition: .2s ease;
    }

    .btn-primary:hover {
      background: var(--green-dark);
      transform: translateY(-2px);
    }

    .hero-right img {
      width: 100%;
      border-radius: 14px;
      box-shadow: 0 12px 30px rgba(46, 125, 50, 0.08);
    }

    .section {
      padding: 70px 6%;
    }

    .fitur-wrapper {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 28px;
    }

    .fitur-card {
      background: var(--card);
      padding: 24px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
      text-align: center;
      transition: .2s ease;
    }

    .fitur-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 26px rgba(0, 0, 0, 0.06);
    }

    .fitur-card img {
      width: 100%;
      height: 150px;
      border-radius: 12px;
      object-fit: cover;
      margin-bottom: 14px;
    }

    .fitur-card h3 {
      color: var(--green);
      font-size: 20px;
      margin-bottom: 10px;
    }

    .fitur-card p {
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 18px;
    }

    .fitur-card button {
      border: 1px solid var(--green);
      background: transparent;
      padding: 8px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      color: var(--green);
      transition: .2s;
    }

    .fitur-card button:hover {
      background: var(--green);
      color: #fff;
    }

    .about {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      align-items: center;
    }

    .about-img {
      width: 100%;
      height: 330px;
      border-radius: 14px;
      object-fit: cover;
      box-shadow: 0 12px 26px rgba(0, 0, 0, 0.06);
    }

    .about h2 {
      margin: 0 0 10px;
      font-size: 32px;
      color: var(--green);
    }

    .about p {
      color: #444;
      margin-bottom: 16px;
    }

    .testi-section {
      background: #f4fff4;
      padding: 70px 6%;
      text-align: center;
    }

    .testi-section h2 {
      margin: 0 0 20px;
      color: var(--green);
      font-size: 30px;
    }

    .testi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 22px;
      max-width: 1100px;
      margin: auto;
    }

    .testi-card {
      background: #fff;
      padding: 22px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.03);
    }

    .testi-card p {
      margin-bottom: 10px;
      color: var(--muted);
    }

    .testi-card span {
      color: var(--green);
      font-weight: 600;
    }

    .blog-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 26px;
    }

    .blog-item {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 12px;
      border: 1px solid #eee;
    }

    .blog-item h3 {
      margin: 0 0 8px;
      font-size: 18px;
      color: #222;
    }

    .blog-item p {
      font-size: 14px;
      color: #666;
    }

    .bottom-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }

    .bottom-box {
      background: #fafafa;
      padding: 24px;
      border-radius: 12px;
      border: 1px solid #eaeaea;
    }

    .bottom-box h3 {
      margin: 0 0 10px;
      color: var(--green);
    }

    @media(max-width: 900px) {
      .about {
        grid-template-columns: 1fr;
      }
    }
  </style>

</head>

<body>

  <?php include './pages/header.php'; ?>

  <section class="hero2">
    <div class="hero-left">
      <h1>Rasakan Pengalaman Bermain Futsal Terbaik di <span>ZonaFutsal</span></h1>
      <p>ZonaFutsal menyediakan lapangan berkualitas, fasilitas modern, dan sistem booking online yang mudah.</p>
      <a href="./pages/sewa.php" class="btn-primary">Booking Sekarang</a>
    </div>
    <div class="hero-right">
      <img src="./assets/image/futsal.png" alt="">
    </div>
  </section>

  <section class="section">
    <div class="fitur-wrapper">

      <div class="fitur-card">
        <img src="./assets/image/futsal.png" alt="">
        <h3>Lapangan Standar Nasional</h3>
        <p>Lapangan aman, nyaman, dan sesuai standar nasional.</p>
        <button onclick="location.href='./pages/lapangan.php'">Selengkapnya</button>
      </div>

      <div class="fitur-card">
        <img src="./assets/image/futsal.png" alt="">
        <h3>Booking Online Cepat</h3>
        <p>Pilih waktu dan pesan lapangan dalam hitungan detik.</p>
        <button onclick="location.href='./pages/sewa.php'">Selengkapnya</button>
      </div>

      <div class="fitur-card">
        <img src="./assets/image/futsal.png" alt="">
        <h3>Harga Terjangkau</h3>
        <p>Kualitas premium dengan harga ramah di kantong.</p>
        <button onclick="location.href='./pages/promo.php'">Selengkapnya</button>
      </div>

    </div>
  </section>

  <section class="section">
    <div class="about">
      <img src="./assets/image/futsal.png" class="about-img" alt="">
      <div>
        <h2>Tentang ZonaFutsal</h2>
        <p>
          ZonaFutsal menyediakan lapangan premium, ruang ganti nyaman, area istirahat modern,
          dan pelayanan ramah untuk semua pengunjung.
        </p>
        <p>
          Kami hadir untuk memberikan pengalaman futsal terbaik — cocok untuk hiburan, latihan,
          maupun turnamen.
        </p>
        <a class="btn-primary" href="./pages/about.php">Pelajari Lebih Lanjut</a>
      </div>
    </div>
  </section>

  <section class="testi-section">
    <h2>Kata Mereka Tentang ZonaFutsal</h2>
    <div class="testi-grid">

      <div class="testi-card">
        <p>“Lapangannya terawat, lantai empuk, dan booking super gampang!”</p>
        <span>— Andi, Tim Harimau</span>
      </div>

      <div class="testi-card">
        <p>“Fasilitas lengkap, cocok untuk latihan intens atau fun match.”</p>
        <span>— Rina, Pelatih Futsal</span>
      </div>

    </div>
  </section>

  <section class="section">
    <div class="blog-grid">

      <div class="blog-item">
        <h3>Cara Memilih Sepatu Futsal</h3>
        <p>Rekomendasi sepatu untuk performa maksimal.</p>
      </div>

      <div class="blog-item">
        <h3>Latihan Dasar Pemula</h3>
        <p>Panduan latihan rutin untuk meningkatkan skill.</p>
      </div>

      <div class="blog-item">
        <h3>Teknik Shooting Akurat</h3>
        <p>Cara menendang bola dengan tenaga & presisi.</p>
      </div>

      <div class="blog-item">
        <h3>Peraturan Futsal Resmi</h3>
        <p>Pahami aturan dasar permainan futsal.</p>
      </div>

    </div>
  </section>

  <section class="section">
    <div class="bottom-grid">

      <div class="bottom-box">
        <h3>Promo & Diskon</h3>
        <p>Dapatkan promo menarik setiap minggu.</p>
      </div>

      <div class="bottom-box">
        <h3>Event & Turnamen</h3>
        <p>Ikuti turnamen futsal seru setiap bulan.</p>
      </div>

    </div>
  </section>

  <?php include './pages/footer.php'; ?>

</body>

</html>