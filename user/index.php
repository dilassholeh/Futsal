<?php
session_start();
include '../includes/koneksi.php';


$current_date = date('Y-m-d');
$query = "SELECT e.*, k.nama as kategori_nama 
          FROM event e 
          LEFT JOIN kategori k ON e.kategori_id = k.id 
          WHERE e.tanggal_berakhir >= '$current_date'
          ORDER BY e.tanggal_mulai ASC";
$result = $conn->query($query);

$jumlahKeranjang = 0;
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $queryCart = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM keranjang WHERE user_id = '$user_id'");
  $cartData = mysqli_fetch_assoc($queryCart);
  $jumlahKeranjang = $cartData['jumlah'];



  $notifQuery = mysqli_query($conn, "
        SELECT * FROM pesan 
        WHERE user_id = '$user_id' 
        ORDER BY created_at DESC
    ");
  $notifBaruResult = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah_baru 
        FROM pesan 
        WHERE user_id = '$user_id' AND status='baru'
    ");
  $notifBaruData = mysqli_fetch_assoc($notifBaruResult);
  $notifBaru = $notifBaruData['jumlah_baru'];
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

</head>

<body>
  <?php
  include './pages/header.php'
  ?>

  <section class="hero" style="background-image:url('./assets/image/futsal.png');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Bermain Futsal<br><span class="highlight">Lebih Seru di ZonaFutsal</span></h1>
      <p>Lapangan modern, bersih, dan nyaman. Booking mudah, harga bersahabat.</p>
      <a href="./pages/sewa.php" class="btn-primary big">Booking Sekarang</a>
    </div>
  </section>

  <script>
    const burger = document.querySelector('.burger');
    const navMenu = document.querySelector('.nav-menu');
    burger.addEventListener('click', () => {
      navMenu.classList.toggle('active');
    });
  </script>

</body>

</html>