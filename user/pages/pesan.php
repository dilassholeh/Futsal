<?php
session_start();
include '../../includes/koneksi.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM pesan WHERE user_id='$user_id' ORDER BY created_at DESC");
if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$pesanList = [];
while($row = mysqli_fetch_assoc($query)){
    $pesanList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pesan Saya</title>
<link rel="stylesheet" href="../assets/css/pages.css">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f9f9f9;
    color: #333;
    padding: 20px;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #222;
}
.pesan-item {
    background: #fff;
    padding: 16px;
    margin-bottom: 12px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}
.pesan-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.pesan-item b {
    font-size: 18px;
    display: block;
    margin-bottom: 6px;
    color: #111;
}
.pesan-item p {
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 8px;
}
.pesan-item small {
    font-size: 12px;
    color: #888;
}
.pesan-status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    color: #fff;
    margin-top: 6px;
}
.pesan-status.baru {
    background:  #ff5b2dff;
}
.pesan-status.dikonfirmasi {
    background: #22b301ff;
}
.pesan-item a {
    display: inline-block;
    margin-top: 6px;
    font-size: 13px;
    color: #3498db;
}
</style>
</head>
<body>

<h2>Pesan Saya</h2>

<?php if(!empty($pesanList)): ?>
    <?php foreach($pesanList as $p): ?>
        <div class="pesan-item">
            <b><?= htmlspecialchars($p['judul']); ?></b>
            <p><?= nl2br(htmlspecialchars($p['pesan'])); ?></p>
            <small><?= date('d-m-Y H:i', strtotime($p['created_at'])); ?></small><br>
            <span class="pesan-status <?= $p['status']; ?>">
                <?= $p['status']=='dikonfirmasi'?'Dikonfirmasi':'Menunggu Konfirmasi'; ?>
            </span>
            <?php if(!empty($p['bukti'])): ?>
                <br><a href="../../uploads/<?= htmlspecialchars($p['bukti']) ?>" target="_blank">Lihat Bukti</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center; font-size:16px; color:#666;">Belum ada pesan.</p>
<?php endif; ?>

</body>
</html>
