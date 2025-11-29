<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$keyword = $_GET['keyword'] ?? '';
$status  = $_GET['status'] ?? '';

$limit = 10;
$page  = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

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
WHERE (u.name LIKE '%$keyword%' OR l.nama_lapangan LIKE '%$keyword%')";

if ($status != "") {
    $query .= " AND t.status_pembayaran = '$status'";
}

$query .= " GROUP BY t.id ORDER BY t.created_at DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

$totalQuery = "SELECT t.id FROM transaksi t
LEFT JOIN user u ON t.user_id = u.id
LEFT JOIN transaksi_detail td ON t.id = td.id_transaksi
LEFT JOIN lapangan l ON td.id_lapangan = l.id
WHERE (u.name LIKE '%$keyword%' OR l.nama_lapangan LIKE '%$keyword%')";
if ($status != "") $totalQuery .= " AND t.status_pembayaran = '$status'";
$total = mysqli_num_rows(mysqli_query($conn, $totalQuery));
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Booking</title>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
:root { --primary:#1a1a1a; --background:#fff; --text:#1a1a1a; --white:#fff; --gray-border:#e0e0e0; }
* { margin:0; padding:0; box-sizing:border-box; font-family:Poppins,sans-serif; }
body { display:flex; min-height:100vh; background:#fff; color:#1a1a1a; }
.header { display:flex; justify-content:space-between; align-items:center; background:#fff; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,.1); }
.header h1 { font-size:20px; color:#333; }
.header-right { display:flex; align-items:center; gap:15px; }
.profile-card { display:flex; align-items:center; gap:12px; background:#fff; padding:6px 12px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.12); }
.profile-img { width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #28a745; }
.profile-name { font-weight:600; font-size:14px; color:#111; }
.btn-logout { background:#dc3545; color:#fff; padding:5px 10px; border-radius:6px; display:flex; align-items:center; text-decoration:none; }
.main { flex:1; display:flex; flex-direction:column; gap:20px; overflow:auto; }
.filter-box { display:flex; gap:10px; padding:15px; }
.filter-box input, .filter-box select { padding:8px; border:1px solid #ccc; border-radius:6px; }
.filter-box button { padding:8px 15px; background:#28a745; color:#fff; border:none; border-radius:6px; cursor:pointer; }
.message-container { display:grid; grid-template-columns:repeat(auto-fit,minmax(430px,1fr)); gap:18px; padding:15px; }
.message-card { background:rgba(255,255,255,.75); backdrop-filter:blur(12px); border-radius:16px; padding:22px 25px; border:1px solid rgba(0,0,0,.08); box-shadow:0 8px 20px rgba(0,0,0,.08); transition:.25s ease; cursor:pointer; }
.message-card:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(0,0,0,.12); border-color:rgba(40,167,69,.4); }
.message-title { font-size:19px; font-weight:700; margin-bottom:4px; }
.message-subtitle { font-size:14px; font-weight:500; margin-bottom:8px; color:#4a4a4a; }
.message-date { font-size:12px; color:#777; margin-bottom:15px; }
.info-row { display:flex; justify-content:space-between; margin-bottom:8px; padding:6px 0; font-size:14px; border-bottom:1px dashed #e5e5e5; }
.info-row:last-child { border-bottom:none; }
.info-label { color:#666; font-weight:500; }
.info-value { font-weight:600; color:#111; }
.status-badge { padding:8px 18px; border-radius:30px; font-size:13px; font-weight:700; display:inline-block; margin-top:10px; }
.status-pending { background:#fff7d1; color:#8a6d00; border:1px solid #ffec99; }
.status-menunggu_konfirmasi { background:#e2e9ff; color:#1d3faf; border:1px solid #c4d3ff; }
.status-lunas { background:#d8f3dc; color:#2d6a4f; border:1px solid #b7e4c7; }
.status-dibatalkan { background:#ffe2e2; color:#a4161a; border:1px solid #ffb3b3; }
.status-dp { background:#ebe4ff; color:#4a36b1; border:1px solid #d0c8ff; }
.status-expired { background:#f3e8ff; color:#7a318e; border:1px solid #deb8ff; }
.empty-state { text-align:center; padding:60px 20px; color:#999; }
.empty-state i { font-size:64px; opacity:.5; }
.pagination { text-align:center; margin:20px 0; }
.pagination a { padding:8px 12px; border:1px solid #ccc; margin:3px; border-radius:6px; text-decoration:none; color:#333; }
.pagination a.active { background:#28a745; border-color:#28a745; color:#fff; }
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main">
<div class="header">
<h1>Riwayat Booking</h1>
<div class="header-right">
<div class="profile-card">
<img src="../assets/image/<?php echo htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" class="profile-img">
<span class="profile-name"><?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
<a href="../logout.php" class="btn-logout"><i class='bx bx-log-out'></i></a>
</div>
</div>
</div>

<form method="GET" class="filter-box">
<input type="text" name="keyword" placeholder="Cari nama / lapangan..." value="<?= $keyword ?>">
<select name="status">
<option value="">Semua Status</option>
<option value="pending" <?= $status=="pending"?"selected":"" ?>>Pending</option>
<option value="menunggu_konfirmasi" <?= $status=="menunggu_konfirmasi"?"selected":"" ?>>Menunggu Konfirmasi</option>
<option value="lunas" <?= $status=="lunas"?"selected":"" ?>>Lunas</option>
<option value="dp" <?= $status=="dp"?"selected":"" ?>>DP</option>
<option value="dibatalkan" <?= $status=="dibatalkan"?"selected":"" ?>>Dibatalkan</option>
<option value="expired" <?= $status=="expired"?"selected":"" ?>>Expired</option>
</select>
<button type="submit">Filter</button>
</form>

<div class="message-container">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $statusClass = 'status-' . $row['status_pembayaran'];
        $statusText = ucwords(str_replace('_',' ',$row['status_pembayaran']));
?>
<div class="message-card">
<div class="message-title">Booking #<?= $row['id']; ?> - <?= $row['nama_user'] ?? '-'; ?></div>
<div class="message-subtitle"><?= $row['lapangan'] ?? '-'; ?></div>
<div class="message-date"><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></div>
<div class="info-row"><span class="info-label">Tanggal Main:</span><span class="info-value"><?= $row['tanggal_booking'] ?? '-'; ?></span></div>
<div class="info-row"><span class="info-label">Jam:</span><span class="info-value"><?= $row['jam'] ?? '-'; ?></span></div>
<div class="info-row"><span class="info-label">No. HP:</span><span class="info-value"><?= $row['no_hp'] ?? '-'; ?></span></div>
<div class="info-row"><span class="info-label">Total Pembayaran:</span><span class="info-value">Rp <?= number_format($row['subtotal'],0,',','.'); ?></span></div>
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

<?php if ($total_pages > 1): ?>
<div class="pagination">
<?php for ($i=1; $i <= $total_pages; $i++): ?>
<a href="?page=<?= $i ?>&keyword=<?= $keyword ?>&status=<?= $status ?>" class="<?= ($page==$i) ? 'active' : '' ?>"><?= $i ?></a>
<?php endfor; ?>
</div>
<?php endif; ?>

</main>
</body>
</html>
