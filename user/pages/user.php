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
while ($row = mysqli_fetch_assoc($queryRiwayat)) $riwayatTransaksi[] = $row;

$riwayatPending = [];
$queryPending = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id = '$user_id' AND status_pembayaran = 'pending' ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($queryPending)) $riwayatPending[] = $row;
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya - ZonaFutsal</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f5f6fa;
    margin: 0;
    display: flex;
    justify-content: center;
    padding: 2rem 1rem;
}

.profile-container {
    width: 100%;
    max-width: 1100px;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}

.profile-card {
    background: linear-gradient(145deg, #00b894, #00d084);
    padding: 2rem 1.5rem;
    border-radius: 24px;
    text-align: center;
    color: #fff;
    box-shadow: 0 16px 40px rgba(0,0,0,0.15);
    position: relative;
    transition: transform 0.3s;
}

.profile-card:hover {
    transform: translateY(-4px);
}

.profile-card img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 4px solid #fff;
    transition: all 0.3s;
}

.profile-card h2 {
    margin: 10px 0 5px;
    font-size: 26px;
}

.info-row {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
    font-size: 15px;
    margin: 6px 0;
}

.info-row svg {
    width: 16px;
    height: 16px;
}

.info-value {
    font-weight: 500;
}

.btn-back,
.btn-logout {
    display: inline-block;
    text-align: center;
    padding: 10px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    margin-top: 15px;
    transition: 0.3s;
    font-size: 14px;
}

.btn-back {
    background: #fff;
    color: #00b894;
}

.btn-back:hover {
    background: #f0f0f0;
}

.btn-logout {
    background: #e74c3c;
    color: #fff;
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 13px;
    padding: 7px 14px;
    border-radius: 10px;
}

.btn-logout:hover {
    background: #c0392b;
}

.right-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.riwayat-transaksi {
    background: #fff;
    padding: 1.5rem;
    border-radius: 20px;
    box-shadow: 0 16px 40px rgba(0,0,0,0.08);
    max-height: 360px;
    overflow-y: auto;
}

.riwayat-transaksi h3 {
    margin-bottom: 1rem;
    color: #111;
    font-size: 18px;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.transaction-card {
    background: #f9f9f9;
    padding: 1rem 1.2rem;
    margin-bottom: 1rem;
    border-radius: 16px;
    font-size: 14px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    transition: all 0.2s;
}

.transaction-card:hover {
    background: #f0f9f5;
    transform: translateY(-2px);
}

.status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    color: #fff;
}

.status-pending { background: #f39c12; }
.status-lunas { background: #27ae60; }
.status-gagal { background: #e74c3c; }

.btn-upload {
    padding: 8px 14px;
    background: #00b894;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    margin-top: 6px;
    display: inline-block;
    font-size: 13px;
    text-align: center;
    transition: background 0.3s;
}

.btn-upload:hover {
    background: #019875;
}

.empty-message {
    text-align: center;
    color: #777;
    padding: 20px 0;
    font-size: 14px;
    background: #f8f8f8;
    border-radius: 12px;
    border: 1px dashed #ccc;
}

@media(max-width: 768px) {
    .profile-container {
        grid-template-columns: 1fr;
    }

    body {
        padding: 1rem;
    }

    .riwayat-transaksi {
        max-height: none;
    }

    .profile-card {
        padding: 1.5rem;
    }

    .transaction-card {
        font-size: 13px;
    }
}
</style>
</head>
<body>

<div class="profile-container">
    <div class="profile-card">
        <a href="../logout.php" class="btn-logout">Logout</a>
        <img src="../assets/image/<?= htmlspecialchars($user['foto'] ?? 'profil.png'); ?>">
        <h2><?= htmlspecialchars($user['name']); ?></h2>
        <div class="info-row">
            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 100 12 6 6 0 000-12zM2 18a8 8 0 0116 0H2z"/></svg>
            <span class="info-value"><?= htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="info-row">
            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2.94 6.94l6 6 9-9"/></svg>
            <span class="info-value"><?= htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="info-row">
            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 3h16v14H2z"/></svg>
            <span class="info-value"><?= htmlspecialchars($user['no_hp']); ?></span>
        </div>
        <a href="javascript:history.back()" class="btn-back">Kembali</a>
    </div>

    <div class="right-section">
        <div class="riwayat-transaksi">
            <h3>Riwayat Checkout (Belum Upload Bukti)</h3>
            <?php if ($riwayatPending): ?>
                <?php foreach ($riwayatPending as $t): ?>
                    <div class="transaction-card">
                        <p><b>Tanggal:</b> <?= date('d/m/Y H:i', strtotime($t['created_at'])); ?></p>
                        <p><b>Total:</b> Rp <?= number_format(floatval($t['subtotal'] ?? 0), 0, ',', '.'); ?></p>
                        <p>
                            <b>Status:</b> 
                            <span class="status-badge status-<?= strtolower($t['status_pembayaran']); ?>">
                                <?= ucfirst($t['status_pembayaran']); ?>
                            </span>
                        </p>
                        <a href="../pages/invoice.php?id=<?= $t['id']; ?>" class="btn-upload">Upload Bukti</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-message">Tidak ada checkout pending.</p>
            <?php endif; ?>
        </div>

        <div class="riwayat-transaksi">
            <h3>Riwayat Transaksi</h3>
            <?php if ($riwayatTransaksi): ?>
                <?php foreach ($riwayatTransaksi as $t): ?>
                    <div class="transaction-card">
                        <p><b>Tanggal:</b> <?= date('d/m/Y H:i', strtotime($t['created_at'])); ?></p>
                        <p><b>Total:</b> Rp <?= number_format($t['subtotal'], 0, ',', '.'); ?></p>
                        <p>
                            <b>Status:</b> 
                            <span class="status-badge status-<?= strtolower($t['status_pembayaran']); ?>">
                                <?= ucfirst($t['status_pembayaran']); ?>
                            </span>
                        </p>
                        <p><b>Metode:</b> <?= $t['metode_pembayaran']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-message">Belum ada transaksi.</p>
            <?php endif; ?>
        </div>
    </div>
</div>



</body>
</html>
