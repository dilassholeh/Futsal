<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$action = $_GET['action'] ?? null;

$id_transaksi = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id_transaksi) {
    die("ID transaksi tidak ditemukan!");
}

$trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id FROM transaksi WHERE id='$id_transaksi'"));
$user_id = $trx['user_id'] ?? null;
if (!$user_id) die("User tidak ditemukan!");

if ($action === 'set_lunas') {
    $status = 'lunas';

    $stmt = $conn->prepare("UPDATE transaksi SET status_pembayaran=? WHERE id=?");
    $stmt->bind_param("ss", $status, $id_transaksi);
    $stmt->execute();
    $stmt->close();

    $judul = "Transaksi Lunas";
    $pesan = "Transaksi Anda telah lunas. Terima kasih telah melakukan pembayaran.";
    $stmt2 = $conn->prepare("
        INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at)
        VALUES (?, ?, ?, ?, 'baru', NOW())
    ");
    $stmt2->bind_param("ssss", $user_id, $id_transaksi, $judul, $pesan);
    $stmt2->execute();
    $stmt2->close();

    echo "<script>
        alert('Status transaksi diubah menjadi LUNAS dan user diberi notifikasi.');
               window.location.href='../../pages/transaksi.php';
    </script>";
    exit;
}

if ($action === 'batal' || isset($_POST['alasan_batal'])) {
    $alasan_batal = $_POST['alasan_batal'] ?? 'Tidak ada alasan';

    $status = 'dibatalkan';

    $stmt = $conn->prepare("UPDATE transaksi SET status_pembayaran=? WHERE id=?");
    $stmt->bind_param("ss", $status, $id_transaksi);
    $stmt->execute();
    $stmt->close();

    $judul = "Transaksi Dibatalkan";
    $pesan = "Transaksi Anda dibatalkan. Alasan: $alasan_batal";
    $stmt2 = $conn->prepare("
        INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at)
        VALUES (?, ?, ?, ?, 'baru', NOW())
    ");
    $stmt2->bind_param("ssss", $user_id, $id_transaksi, $judul, $pesan);
    $stmt2->execute();
    $stmt2->close();

    echo "<script>
        alert('Transaksi dibatalkan dan user diberi notifikasi.');
        window.location.href='../../pages/transaksi.php';
    </script>";
    exit;
}

die("Action tidak valid!");
