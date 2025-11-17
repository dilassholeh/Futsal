<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

if(!isset($_GET['id'])){
    die("ID transaksi tidak ditemukan!");
}
$id = mysqli_real_escape_string($conn, $_GET['id']);

$qTrans = mysqli_query($conn, "
    SELECT t.*, u.name AS nama_user 
    FROM transaksi t
    JOIN user u ON t.user_id = u.id 
    WHERE t.id = '$id'
");
if(mysqli_num_rows($qTrans)==0){
    die("Transaksi tidak ditemukan!");
}
$transaksi = mysqli_fetch_assoc($qTrans);

$qDetail = mysqli_query($conn, "
    SELECT td.*, l.nama_lapangan 
    FROM transaksi_detail td
    JOIN lapangan l ON td.id_lapangan = l.id 
    WHERE td.id_transaksi = '$id'
");

$qPesan = mysqli_query($conn, "
    SELECT * FROM pesan 
    WHERE id_transaksi = '$id'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f5f7fa; color: #333; padding: 20px; }
        a { text-decoration: none; color: #117139; }
        a:hover { text-decoration: underline; }
        h2, h3 { margin-bottom: 15px; color: #117139; }
        .back-link { display: inline-block; margin-bottom: 20px; font-weight: 500; }
        .info-cards { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; }
        .card { display: flex; align-items: center; background: #fff; border-radius: 10px; padding: 15px 20px 15px 10px; flex: 1 1 200px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: relative; transition: 0.2s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        .card-icon { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5em; color: #fff; border-radius: 5px; margin-right: 15px; flex-shrink: 0; }
        .card-user .card-icon { background-color: #117139; }
        .card-subtotal .card-icon { background-color: #f39c12; }
        .card-status .card-icon { background-color: #3498db; }
        .card-bukti .card-icon { background-color: #9b59b6; }
        .card p { margin-top: 5px; font-size: 1em; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
        th, td { padding: 12px 15px; text-align: left; }
        th { background-color: #117139; color: #fff; font-weight: 500; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e2f0e6; }
        .status-baru { color: #fff; background-color: #ff6b6b; padding: 4px 8px; border-radius: 5px; font-size: 0.9em; }
        .status-konfirmasi { color: #fff; background-color: #117139; padding: 4px 8px; border-radius: 5px; font-size: 0.9em; }
        .btn { color: #fff; background-color: #117139; padding: 6px 12px; border-radius: 5px; font-size: 0.9em; transition: 0.2s; }
        .btn:hover { background-color: #0e5f2e; }
        @media (max-width: 768px) { .info-cards { flex-direction: column; } th, td { font-size: 0.9em; padding: 8px; } }
    </style>
</head>
<body>

<a class="back-link" href="../../pages/transaksi.php"><i class='bx bx-left-arrow-alt'></i> Kembali ke Daftar Transaksi</a>

<h2>Detail Transaksi ID: <?= $transaksi['id'] ?></h2>

<div class="info-cards">
    <div class="card card-user">
        <div class="card-icon"><i class='bx bx-user'></i></div>
        <div>
            <strong>User</strong>
            <p><?= $transaksi['nama_user'] ?></p>
        </div>
    </div>
    <div class="card card-subtotal">
        <div class="card-icon"><i class='bx bx-wallet'></i></div>
        <div>
            <strong>Subtotal</strong>
            <p>Rp <?= number_format($transaksi['subtotal'],0,',','.'); ?></p>
        </div>
    </div>
    <div class="card card-status">
        <div class="card-icon"><i class='bx bx-info-circle'></i></div>
        <div>
            <strong>Status</strong>
            <p><?= ucfirst($transaksi['status_pembayaran']); ?></p>
        </div>
    </div>
    <div class="card card-bukti">
        <div class="card-icon"><i class='bx bx-image'></i></div>
        <div>
            <strong>Bukti Bayar</strong>
            <p>
                <?php if(!empty($transaksi['bukti_pembayaran'])): ?>
                    <a href="../../../uploads/booking/<?= $transaksi['bukti_pembayaran'] ?>" target="_blank" class="btn">Lihat</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<h3>Daftar Lapangan</h3>
<table>
    <tr>
        <th>Lapangan</th>
        <th>Tanggal</th>
        <th>Jam Mulai</th>
        <th>Jam Selesai</th>
        <th>Harga</th>
    </tr>
    <?php while($d = mysqli_fetch_assoc($qDetail)){ ?>
    <tr>
        <td><?= $d['nama_lapangan'] ?></td>
        <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
        <td><?= $d['jam_mulai'] ?></td>
        <td><?= $d['jam_selesai'] ?></td>
        <td>Rp <?= number_format($d['harga_jual'],0,',','.'); ?></td>
    </tr>
    <?php } ?>
</table>

<h3>Pesan Terkait</h3>
<table>
    <tr>
        <th>Judul</th>
        <th>Pesan</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while($p = mysqli_fetch_assoc($qPesan)){ ?>
    <tr>
        <td><?= htmlspecialchars($p['judul']) ?></td>
        <td><?= htmlspecialchars($p['pesan']) ?></td>
        <td>
            <?php if($p['status'] === 'baru') echo '<span class="status-baru">Baru</span>'; 
                  else echo '<span class="status-konfirmasi">Dikonfirmasi</span>'; ?>
        </td>
        <td>
            <?php if($p['status'] === 'baru') { ?>
                <a class="btn" href="../pesan/konfirmasi_pesan.php?id=<?= $p['id'] ?>&transaksi=<?= $transaksi['id'] ?>">Konfirmasi</a>
            <?php } else { echo '-'; } ?>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
