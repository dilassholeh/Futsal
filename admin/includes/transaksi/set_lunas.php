<?php
include '../../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    die("Akses ditolak!");
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    die("ID transaksi tidak ditemukan!");
}

$update = $conn->prepare("UPDATE transaksi SET status_pembayaran = 'lunas' WHERE id = ?");
$update->bind_param("s", $id);

if ($update->execute()) {
    echo "<script>alert('Status transaksi berhasil diubah menjadi LUNAS!');window.location='../../pages/transaksi.php';</script>";
} else {
    echo "<script>alert('Gagal update status!');window.location='../../pages/transaksi.php';</script>";
}

$update->close();
?>
