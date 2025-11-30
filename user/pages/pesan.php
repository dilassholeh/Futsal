<?php
session_start();
include '../../includes/koneksi.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "
    SELECT * FROM pesan 
    WHERE user_id='$user_id' 
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pesan Saya</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #eef2f6;
    padding: 30px 16px;
}

.container {
    max-width: 700px;
    margin: auto;
}

h2 {
    text-align: center;
    margin-bottom: 28px;
    font-size: 26px;
    font-weight: 600;
    color: #1e2a38;
}

.card {
    background: #ffffff;
    padding: 18px 20px;
    border-radius: 14px;
    margin-bottom: 14px;
    box-shadow: 0 7px 18px rgba(0,0,0,0.06);
    transition: 0.25s ease;
    border-left: 5px solid #117139;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
}

.card-title {
    font-size: 17px;
    font-weight: 600;
    color: #1b1b1b;
    margin-bottom: 6px;
}

.card-text {
    font-size: 14px;
    color: #4a4a4a;
    line-height: 1.6;
    margin-bottom: 10px;
}

.card small {
    font-size: 12px;
    color: #8a8a8a;
}

.btn-bukti {
    display: inline-block;
    margin-top: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #2962ff;
    text-decoration: none;
}

.btn-bukti:hover {
    text-decoration: underline;
}

.no-data {
    text-align: center;
    font-size: 16px;
    margin-top: 50px;
    color: #777;
}
</style>
</head>
<body>

<div class="container">
    <h2>Pesan Saya</h2>

    <?php if(mysqli_num_rows($query) > 0): ?>
        <?php while($p = mysqli_fetch_assoc($query)): ?>
        <div class="card">
            <div class="card-title"><?= htmlspecialchars($p['judul']) ?></div>

            <div class="card-text">
                <?= nl2br(htmlspecialchars($p['pesan'])) ?>
            </div>

            <small><?= date('d M Y - H:i', strtotime($p['created_at'])) ?></small>

            <?php if(!empty($p['bukti'])): ?>
                <br><a class="btn-bukti" href="../../uploads/<?= htmlspecialchars($p['bukti']) ?>" target="_blank">Lihat Bukti Pembayaran</a>
            <?php endif; ?>

            <?php if(!empty($p['alasan'])): ?>
                <br><small><b>Alasan:</b> <?= htmlspecialchars($p['alasan']) ?></small>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-data">Belum ada pesan untuk ditampilkan.</p>
    <?php endif; ?>
</div>

</body>
</html>
