<?php
include '../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$queryUser = mysqli_query($conn, "SELECT * FROM user WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($queryUser);

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
<title>Profil Saya - ZonaFutsal</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f5f5f5;
    margin: 0;
    display: flex;
    justify-content: center;
    padding: 2rem 1rem;
}

.profile-container {
    width: 100%;
    max-width: 500px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.profile-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.profile-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.profile-card h2 {
    margin: 5px 0;
    font-size: 20px;
    color: #333;
}

.profile-card p {
    margin: 3px 0;
    font-size: 14px;
    color: #555;
}

.riwayat-transaksi {
    background: #fff;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-height: 300px;
    overflow-y: auto;
}

.transaction-card {
    background: #f9f9f9;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    border-radius: 8px;
    font-size: 14px;
}

.transaction-card p {
    margin: 2px 0;
}

.btn-back {
    display: inline-block;
    text-align: center;
    background: #00b894;
    color: #fff;
    padding: 8px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    margin-top: 10px;
}

.btn-back:hover {
    background: #019875;
}
</style>
</head>
<body>

<div class="profile-container">
    <div class="profile-card">
        <img src="../assets/image/<?= htmlspecialchars($user['foto'] ?? 'profil.png'); ?>" alt="Profile">
        <h2><?= htmlspecialchars($user['name'] ?? 'User'); ?></h2>
        <p><b>Username:</b> <?= htmlspecialchars($user['username'] ?? '-'); ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($user['email'] ?? '-'); ?></p>
        <p><b>No HP:</b> <?= htmlspecialchars($user['no_hp'] ?? '-'); ?></p>
        <a href="javascript:history.back()" class="btn-back">Kembali</a>
    </div>

    <div class="riwayat-transaksi">
        <h3>Riwayat Transaksi</h3>
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
            <p style="text-align:center; color:#777;">Belum ada transaksi.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
