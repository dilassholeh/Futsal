<?php
include '../../includes/koneksi.php';
session_start();

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$queryUser = mysqli_query($conn, "SELECT * FROM user WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($queryUser);

// Ambil riwayat transaksi
$riwayatTransaksi = [];
$queryRiwayat = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id = '$user_id' ORDER BY created_at DESC");
if ($queryRiwayat) {
    while ($row = mysqli_fetch_assoc($queryRiwayat)) {
        $riwayatTransaksi[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZonaFutsal | Profil User</title>
<link href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/pages.css?v=<?php echo filemtime('../assets/css/pages.css'); ?>">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body { background: #f5f5f5; font-family: 'Poppins', sans-serif; margin: 0; }
.nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.nav ul { display: flex; gap: 1rem; list-style: none; padding: 0; margin: 0; }
.nav ul li a { text-decoration: none; color: #333; font-weight: 600; }
.nav ul li a.active { color: #007bff; }
.container { max-width: 1000px; margin: 2rem auto; display: flex; flex-direction: column; gap: 2rem; }
.profile-card { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); display: flex; align-items:center; gap:2rem; }
.profile-card img { width:120px; height:120px; border-radius:50%; object-fit:cover; }
.profile-info h3 { margin:0.25rem 0; }
.riwayat-transaksi { background:#fff; padding:1.5rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); max-height:400px; overflow-y:auto; }
.transaction-card { background:#f9f9f9; padding:1rem; margin-bottom:1rem; border-radius:8px; }
.transaction-card p { margin:0.25rem 0; }
.no-transaksi { text-align:center; padding:2rem; }
</style>
</head>
<body>

<header>
<nav class="nav">
    <div class="logo-container">
        <a href="../index.php" class="logo-text">
            <img src="../assets/image/logo.png" alt="ZonaFutsal Logo" class="logo-img"> ZOFA
        </a>
    </div>
    <ul>
        <li><a href="../index.php">Beranda</a></li>
        <li><a href="sewa.php">Penyewaan</a></li>
        <li><a href="user.php" class="active">Profil</a></li>
    </ul>
    <div class="user-menu">
        <a href="../logout.php" class="btn-logout"><i class='bx bx-log-out'></i> Keluar</a>
    </div>
</nav>
</header>

<section class="container">
    <!-- Profil User -->
    <div class="profile-card">
        <img src="../assets/image/<?= htmlspecialchars($user['foto'] ?? 'profil.png'); ?>" alt="Profile">
        <div class="profile-info">
            <h3><?= htmlspecialchars($user['name']); ?></h3>
            <p><b>Username:</b> <?= htmlspecialchars($user['username']); ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($user['email']); ?></p>
            <p><b>No HP:</b> <?= htmlspecialchars($user['no_hp'] ?? '-'); ?></p>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="riwayat-transaksi">
        <h2>Riwayat Transaksi</h2>
        <?php if (!empty($riwayatTransaksi)): ?>
            <?php foreach ($riwayatTransaksi as $t): ?>
                <div class="transaction-card">
                    <p><b>Tanggal:</b> <?= date('d/m/Y H:i', strtotime($t['created_at'] ?? 'now')); ?></p>
                    <p><b>Total:</b> Rp <?= number_format($t['subtotal'] ?? 0, 0, ',', '.'); ?></p>
                    <p><b>Status:</b> <?= htmlspecialchars($t['status_pembayaran'] ?? '-'); ?></p>
                    <p><b>Metode:</b> <?= htmlspecialchars($t['metode_pembayaran'] ?? '-'); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-transaksi">Belum ada transaksi.</div>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
