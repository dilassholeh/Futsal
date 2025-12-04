<?php
session_start();
if (isset($_SESSION['admin_id'])) {
  $_SESSION['user_id'] = $_SESSION['admin_id'];
}

include '../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (isset($_GET['admin_view']) && isset($_SESSION['from_admin']) && $_SESSION['from_admin'] === true && isset($_SESSION['admin_id'])) {
  $_SESSION['user_id'] = $_SESSION['admin_id'];
  $_SESSION['user_name'] = $_SESSION['admin_nama'] ?? null;
  $_SESSION['user_nohp'] = $_SESSION['admin_nohp'] ?? null;
  $_SESSION['user_role'] = 'admin';
}

$current_date = date('Y-m-d');
$esc_date = $conn->real_escape_string($current_date);
$query = "SELECT e.*, k.nama as kategori_nama 
          FROM event e 
          LEFT JOIN kategori k ON e.kategori_id = k.id 
          WHERE e.tanggal_berakhir >= '$esc_date'
          ORDER BY e.tanggal_mulai ASC";
$result = $conn->query($query);

$notifBaru = 0;

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
  $user_id = $conn->real_escape_string($_SESSION['user_id']);

  $q2 = "SELECT COUNT(*) AS jumlah_baru FROM pesan WHERE user_id = '$user_id' AND status = 'baru'";
  $res2 = $conn->query($q2);
  if ($res2) {
    $notifBaruData = $res2->fetch_assoc();
    $notifBaru = $notifBaruData['jumlah_baru'] ?? 0;
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

    @media(max-width: 900px) {
      .about {
        grid-template-columns: 1fr;
      }
    }

    .lokasi-section {
      padding: 70px 6% 20px;
      background: #fff;
      text-align: center;
    }

    .lokasi-info {
      max-width: 900px;
      margin: 0 auto 40px;
    }

    .lokasi-info h2 {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #2e7d32;
    }


    .lokasi-detail {
      display: inline-flex;
      gap: 12px;
      justify-content: center;
      align-items: flex-start;
    }

    .lokasi-detail i {
      font-size: 30px;
      color: #2e7d32;
    }

    .lokasi-map-full {
      width: 100%;
      margin-top: 30px;
    }

    .lokasi-map-full iframe {
      width: 100%;
      height: 420px;
      border: 0;
    }

    @media (max-width: 768px) {
      .lokasi-section {
        padding: 60px 20px 10px;
      }

      .lokasi-map-full iframe {
        height: 350px;
      }

      .lokasi-detail {
        flex-direction: column;
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
      </div>

      <div class="fitur-card">
        <img src="./assets/image/futsal.png" alt="">
        <h3>Booking Online Cepat</h3>
        <p>Pilih waktu dan pesan lapangan dalam hitungan detik.</p>
      </div>

      <div class="fitur-card">
        <img src="./assets/image/futsal.png" alt="">
        <h3>Harga Terjangkau</h3>
        <p>Kualitas premium dengan harga ramah di kantong.</p>
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
      </div>
    </div>
  </section>

  <section class="lokasi-section" id="lokasi">
    <div class="lokasi-info">
      <h2>Lokasi ZonaFutsal</h2>
      <div class="lokasi-detail">
        <i class='bx bxs-map'></i>
        <div>
          <p>Jl. Contoh</p>
        </div>
      </div>
    </div>

    <div class="lokasi-map-full">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.3266638733494!2d113.72363987432873!3d-8.169807481874903!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd695cfe9894601%3A0xc9639ab1c93a874a!2sZona%20Futsal!5e0!3m2!1sid!2sid!4v1764759792532!5m2!1sid!2sid"
        allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </section>




  <?php include './pages/footer.php'; ?>

</body>

</html>