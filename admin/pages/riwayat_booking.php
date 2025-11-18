<?php
session_start();
include '../../includes/koneksi.php';

// Pastikan admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil semua riwayat booking
$query = "SELECT 
    t.id,
    t.created_at,
    t.subtotal,
    t.status_pembayaran,
    u.name as nama_user,
    u.no_hp,
    GROUP_CONCAT(DISTINCT l.nama_lapangan SEPARATOR ', ') as lapangan,
    GROUP_CONCAT(DISTINCT DATE_FORMAT(td.tanggal, '%d/%m/%Y') SEPARATOR ', ') as tanggal_booking,
    GROUP_CONCAT(DISTINCT CONCAT(td.jam_mulai, '-', td.jam_selesai) SEPARATOR ', ') as jam
FROM transaksi t
LEFT JOIN user u ON t.user_id = u.id
LEFT JOIN transaksi_detail td ON t.id = td.id_transaksi
LEFT JOIN lapangan l ON td.id_lapangan = l.id
GROUP BY t.id ORDER BY t.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Booking</title>

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
    padding: 20px;
}

.message-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.message-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 5px;
}

.message-subtitle {
    font-size: 14px;
    color: #666;
}

.message-date {
    font-size: 12px;
    color: #999;
    margin-bottom: 10px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 14px;
}

.info-label { color: #666; }
.info-value { font-weight: 600; }

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-pending { background:#fff3cd; color:#856404; }
.status-menunggu_konfirmasi { background:#cfe2ff; color:#084298; }
.status-lunas { background:#d1e7dd; color:#0f5132; }
.status-dibatalkan { background:#f8d7da; color:#842029; }
.status-dp { background:#d3d3f5; color:#3d348b; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i { font-size: 64px; opacity: .5; }
</style>
</head>

<body>

<h2>Riwayat Booking</h2>

<div class="message-container">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $statusClass = 'status-' . $row['status_pembayaran'];
        $statusText = ucwords(str_replace('_', ' ', $row['status_pembayaran']));
?>
        <div class="message-card">
            <div class="message-title">Booking #<?= $row['id']; ?> - <?= $row['nama_user'] ?? '-'; ?></div>
            <div class="message-subtitle"><?= $row['lapangan'] ?? '-'; ?></div>
            <div class="message-date"><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></div>

            <div class="info-row">
                <span class="info-label">Tanggal Main:</span>
                <span class="info-value"><?= $row['tanggal_booking'] ?? '-'; ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Jam:</span>
                <span class="info-value"><?= $row['jam'] ?? '-'; ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">No. HP:</span>
                <span class="info-value"><?= $row['no_hp'] ?? '-'; ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Total Pembayaran:</span>
                <span class="info-value">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></span>
            </div>

            <br>
            <span class="status-badge <?= $statusClass; ?>"><?= $statusText; ?></span>
        </div>
<?php
    }
} else {
?>
    <div class="empty-state">
        <i class='bx bx-inbox'></i>
        <h3>Tidak ada riwayat booking</h3>
    </div>
<?php
}
?>
</div>
</body>
</html>
