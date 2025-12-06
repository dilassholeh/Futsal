<?php
include '../includes/koneksi.php';
session_start();

$result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id ASC");

$notifBaru = 0;
$user_id = $_SESSION['user_id'] ?? null;
$pendingBooking = 0;

if ($user_id) {
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
    $notifBaru = $notifBaruData['jumlah_baru'] ?? 0;

    $pendingResult = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah_pending 
        FROM transaksi 
        WHERE user_id = '$user_id' AND status_pembayaran = 'pending'
    ");
    $pendingData = mysqli_fetch_assoc($pendingResult);
    $pendingBooking = $pendingData['jumlah_pending'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZonaFutsal | Booking Lapangan</title>
<link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="./assets/css/pages.css?v=<?php echo filemtime('./assets/css/pages.css'); ?>">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include './pages/header.php'; ?>

<section class="hero">
    <img src="./assets/image/bakground.png" alt="ZonaFutsal" class="hero-img">
    <div class="hero-overlay">
      <h1>Booking Lapangan Futsal Kini Lebih Mudah!</h1>
      <a href="#lapangan" class="btn-scroll">Mulai Booking</a>
    </div>
</section>

<section class="container" id="lapangan">
<h2 class="section-title">Daftar Lapangan</h2>
<div class="card-grid fade-in">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fotoPath = "../uploads/lapangan/" . $row['foto'];
        if (empty($row['foto']) || !file_exists($fotoPath)) $fotoPath = "./assets/image/noimage.png";

        $status = $row['status'] ?? 'tersedia';
        $isDisabled = ($status == 'rusak' || $status == 'perbaikan');
        $cardClass = $isDisabled ? 'card disabled' : 'card';

        $statusText = '';
        $statusIcon = '';
        $statusClass = '';
        if ($status == 'rusak') {
            $statusText = 'Lapangan Rusak';
            $statusIcon = 'bx-error-circle';
            $statusClass = 'rusak';
        } elseif ($status == 'perbaikan') {
            $statusText = 'Sedang Perbaikan';
            $statusIcon = 'bx-wrench';
            $statusClass = 'perbaikan';
        }
?>
<div class="<?= $cardClass; ?>">
    <span class="status-badge-card <?= $status; ?>">
        <?= $status == 'tersedia' ? 'Tersedia' : ($status == 'rusak' ? 'Rusak' : 'Perbaikan'); ?>
    </span>

    <div class="card-img">
        <img src="<?= htmlspecialchars($fotoPath); ?>" alt="<?= htmlspecialchars($row['nama_lapangan']); ?>">
    </div>

    <div class="card-content">
        <h3><?= htmlspecialchars($row['nama_lapangan']); ?></h3>
        <p class="card-desc"><?= htmlspecialchars($row['deskripsi'] ?? 'Lapangan futsal berkualitas tinggi untuk semua kalangan.'); ?></p>

        <div class="price-box">
            <span class="pagi">Pagi: <b>Rp <?= number_format($row['harga_pagi'], 0, ',', '.'); ?></b></span>
            <span class="malam">Malam: <b>Rp <?= number_format($row['harga_malam'], 0, ',', '.'); ?></b></span>
        </div>

        <?php if ($isDisabled): ?>
            <button class="btn-book disabled" disabled>Tidak Tersedia</button>
        <?php else: ?>
            <a href="./pages/booking.php?id=<?= urlencode($row['id']); ?>" 
               class="btn-book" 
               data-pending="<?= $pendingBooking; ?>">Booking Sekarang</a>
        <?php endif; ?>
    </div>

    <?php if ($isDisabled): ?>
    <div class="status-overlay <?= $statusClass; ?>">
        <i class="bx <?= $statusIcon; ?>"></i>
        <h4><?= $statusText; ?></h4>
        <p>Mohon maaf, lapangan sedang tidak dapat digunakan</p>
    </div>
    <?php endif; ?>
</div>
<?php
    }
} else {
    echo "<p class='no-data'>Belum ada data lapangan tersedia.</p>";
}
?>
</div>
</section>

<?php include './pages/footer.php'; ?>

<div id="loginAlertModal" class="modal">
<div class="modal-content">
  <span class="close">&times;</span>
  <h3>Perhatian!</h3>
  <p>Anda harus login terlebih dahulu untuk melakukan booking.</p>
  <a href="login.php" class="btn-login-modal">Login Sekarang</a>
</div>
</div>

<div id="pendingAlertModal" class="modal">
<div class="modal-content">
  <span class="close">&times;</span>
  <h3>Perhatian!</h3>
  <p>Anda masih memiliki pembayaran pending! Harap selesaikan terlebih dahulu.</p>
  <a href="./pages/user.php" class="btn-login-modal">Ke Profil</a>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const isLoggedIn = <?= $user_id ? 'true' : 'false'; ?>;
    const pendingModal = document.getElementById('pendingAlertModal');
    const loginModal = document.getElementById('loginAlertModal');
    const closeButtons = document.querySelectorAll('.modal .close');

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.modal').style.display = 'none';
        });
    });

    if (!isLoggedIn) {
        const bookingButtons = document.querySelectorAll('.btn-book');
        bookingButtons.forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                loginModal.style.display = "block";
            });
        });
    } else {
        const bookingButtons = document.querySelectorAll('.btn-book');
        bookingButtons.forEach(btn => {
            btn.addEventListener('click', e => {
                const pending = parseInt(btn.dataset.pending);
                if (pending > 0) {
                    e.preventDefault();
                    pendingModal.style.display = "block";
                }
            });
        });
    }

    window.addEventListener('click', e => {
        if (e.target.classList.contains('modal')) e.target.style.display = 'none';
    });
});
</script>

</body>
</html>
