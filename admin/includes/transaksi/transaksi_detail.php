<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan!");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

$qTrans = mysqli_query($conn, "
    SELECT t.*, u.name AS nama_user 
    FROM transaksi t 
    JOIN user u ON t.user_id = u.id
    WHERE t.id = '$id'
");

if (mysqli_num_rows($qTrans) == 0) {
    die("Transaksi tidak ditemukan!");
}

$transaksi = mysqli_fetch_assoc($qTrans);

if (isset($_POST['konfirmasi_bayar'])) {
    mysqli_query($conn, "UPDATE transaksi SET is_verified_admin = 1 WHERE id='$id'");
    mysqli_query($conn, "
        INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at)
        VALUES ('{$transaksi['user_id']}', '$id', 'Pembayaran Diverifikasi Admin', 'Pembayaran Anda telah diverifikasi admin.', 'baru', NOW())
    ");
    header("Location: transaksi_detail.php?id=$id");
    exit;
}

if (isset($_POST['batalkan_bayar'])) {
    $alasan = trim($_POST['alasan_batal'] ?? '');
    if ($alasan !== '') {
        mysqli_query($conn, "UPDATE transaksi SET status_pembayaran='dibatalkan', alasan_batal='$alasan' WHERE id='$id'");
        mysqli_query($conn, "
            INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at)
            VALUES ('{$transaksi['user_id']}', '$id', 'Pembayaran Ditolak', 'Pembayaran dibatalkan admin. Alasan: $alasan', 'baru', NOW())
        ");
        header("Location: transaksi_detail.php?id=$id");
        exit;
    } else {
        $error = "Alasan batal wajib diisi!";
    }
}

$qDetail = mysqli_query($conn, "
    SELECT td.*, l.nama_lapangan 
    FROM transaksi_detail td
    JOIN lapangan l ON td.id_lapangan = l.id 
    WHERE td.id_transaksi = '$id'
");

$qPesan = mysqli_query($conn, "SELECT * FROM pesan WHERE id_transaksi = '$id'");

function tampilStatus($st)
{
    if ($st == 'menunggu_konfirmasi') return ['DP', 'dp'];
    if ($st == 'lunas') return ['Lunas', 'lunas'];
    if ($st == 'dibatalkan') return ['Dibatalkan', 'dibatalkan'];
    return [ucfirst($st), 'pending'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Inter', sans-serif;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #117139;
            font-weight: bold;
            margin-bottom: 18px;
        }

        h2 {
            font-size: 26px;
            margin-bottom: 25px;
        }

        .card-container {
            background: #fff;
            border-radius: 16px;
            padding: 28px 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            border: 1px solid #e3e3e3;
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
            margin-bottom: 35px;
        }

        .info-card {
            background: #f9fdf9;
            padding: 18px;
            border-radius: 12px;
            border-left: 5px solid #117139;
        }

        .info-card strong {
            font-size: 13px;
            color: #666;
        }

        .info-card p {
            font-size: 15px;
            font-weight: 600;
            margin-top: 4px;
        }

        .status-bayar {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
        }

        .status-bayar.dp {
            background: #8e44ad;
        }

        .status-bayar.lunas {
            background: #27ae60;
        }

        .status-bayar.dibatalkan {
            background: #c0392b;
        }

        .status-bayar.pending {
            background: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #117139;
            color: #fff;
            text-align: left;
            padding: 12px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover td {
            background: #f8fff9;
        }

        .message-list {
            display: grid;
            gap: 15px;
            margin-top: 15px;
        }

        .message-card {
            background: #f8f8f8;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #ddd;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
        }

        .message-text {
            font-size: 14px;
            margin-top: 5px;
        }

        .btn {
            background: #117139;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-danger {
            background: #c0392b;
        }

        input[type=text] {
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 250px;
            margin-right: 6px;
        }

        .error-msg {
            color: #c0392b;
            margin-top: 6px;
            font-size: 13px;
        }
    </style>

    <script>
        function tampilkanAlasan() {
            document.getElementById('formBatal').style.display = 'inline-block';
        }

        function hideButton() {
            document.getElementById('btnKonfirmasi').style.display = 'none';
        }
    </script>

</head>

<body>

    <div class="container">
        <a href="../../pages/transaksi.php" class="back-link"><i class='bx bx-arrow-back'></i> Kembali</a>
        <div class="card-container">

            <h2>Detail Transaksi #<?= $transaksi['id'] ?></h2>

            <div class="info-cards">
                <div class="info-card"><strong>Nama Pemesan</strong>
                    <p><?= htmlspecialchars($transaksi['nama_user']) ?></p>
                </div>
                <div class="info-card"><strong>Total Harga</strong>
                    <p>Rp <?= number_format($transaksi['subtotal'], 0, ',', '.') ?></p>
                </div>
                <div class="info-card"><strong>Jumlah Dibayar</strong>
                    <p>Rp <?= number_format($transaksi['jumlah_dibayar'], 0, ',', '.') ?></p>
                </div>
                <?php $st = tampilStatus($transaksi['status_pembayaran']); ?>
                <div class="info-card"><strong>Status Pembayaran</strong>
                    <p><span class="status-bayar <?= $st[1] ?>"><?= $st[0] ?></span></p>
                </div>
            </div>

            <?php if (!empty($transaksi['bukti_pembayaran'])): ?>
                <img src="../../../uploads/booking/<?= $transaksi['bukti_pembayaran'] ?>" style="max-width:300px;border-radius:12px;border:1px solid #ddd;margin-top:10px;">
                <div style="margin-top:15px;">

                    <?php if ($transaksi['status_pembayaran'] != 'dibatalkan' && $transaksi['is_verified_admin'] == 0): ?>
                        <form method="POST" style="display:inline;" onsubmit="hideButton()">
                            <button name="konfirmasi_bayar" class="btn" id="btnKonfirmasi">Konfirmasi</button>
                        </form>

                        <div id="batalContainer">
                            <button class="btn btn-danger" onclick="tampilkanAlasan()">Batalkan</button>
                            <form method="POST" id="formBatal" style="display:none;margin-top:8px;">
                                <input type="text" name="alasan_batal" placeholder="Masukkan alasan batal" required>
                                <button name="batalkan_bayar" class="btn btn-danger">Kirim</button>
                            </form>
                            <?php if (!empty($error)) echo "<div class='error-msg'>$error</div>"; ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php else: ?>
                <p style="color:#777;">User belum mengupload bukti pembayaran.</p>
            <?php endif; ?>

            <h3 style="margin-top:25px;">Lapangan</h3>
            <table>
                <tr>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Harga</th>
                </tr>
                <?php while ($d = mysqli_fetch_assoc($qDetail)): ?>
                    <tr>
                        <td><?= $d['nama_lapangan'] ?></td>
                        <td><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                        <td><?= $d['jam_mulai'] ?></td>
                        <td><?= $d['jam_selesai'] ?></td>
                        <td>Rp <?= number_format($d['harga_jual'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <h3 style="margin-top:25px;">Pesan</h3>
            <div class="message-list">
                <?php if (mysqli_num_rows($qPesan) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($qPesan)): ?>
                        <div class="message-card">
                            <div class="message-header"><?= htmlspecialchars($p['judul']) ?></div>
                            <p class="message-text"><?= nl2br(htmlspecialchars($p['pesan'])) ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color:#777;">Tidak ada pesan.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>

</html>