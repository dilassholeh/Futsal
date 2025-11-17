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
    background: #f5f5f7;
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
    padding: 2rem 1.5rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    position: relative;
}

.profile-card img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 3px solid #00b894;
}

.profile-card h2 {
    margin: 8px 0;
    font-size: 22px;
    color: #111;
}

.profile-card p {
    margin: 4px 0;
    font-size: 15px;
    color: #555;
}

.btn-back, .btn-logout {
    display: inline-block;
    text-align: center;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    margin: 10px 5px 0 5px;
    transition: 0.3s;
    font-size: 14px;
}

.btn-back {
    background: #00b894;
    color: #fff;
}

.btn-back:hover {
    background: #019875;
}

.btn-logout {
    background: #e74c3c;
    color: #fff;
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 13px;
    padding: 6px 12px;
}

.btn-logout:hover {
    background: #c0392b;
}

.riwayat-transaksi {
    background: #fff;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    max-height: 350px;
    overflow-y: auto;
}

.riwayat-transaksi h3 {
    margin-bottom: 1rem;
    color: #111;
    font-size: 18px;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}

.transaction-card {
    background: #f9f9f9;
    padding: 0.85rem 1rem;
    margin-bottom: 0.8rem;
    border-radius: 12px;
    font-size: 14px;
    display: flex;
    flex-direction: column;
    gap: 3px;
    transition: 0.2s;
}

.transaction-card:hover {
    background: #f0f9f5;
}

.transaction-card p {
    margin: 2px 0;
}

@media (max-width: 500px) {
    .profile-card {
        padding: 1.5rem 1rem;
    }

    .btn-logout {
        top: 10px;
        right: 10px;
    }

    .riwayat-transaksi {
        max-height: 300px;
        padding: 1rem;
    }
}
</style>
</head>
<body>

<div class="profile-container">
    <div class="profile-card">
        <a href="../logout.php" class="btn-logout">Logout</a>
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
