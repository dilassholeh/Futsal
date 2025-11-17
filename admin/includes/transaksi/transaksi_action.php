<?php
include '../../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    die("Akses ditolak!");
}

$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';

if (empty($id) || empty($action)) {
    die("Data tidak lengkap!");
}

$conn->begin_transaction();

try {
    if ($action === 'lunas') {
        $update = $conn->prepare("UPDATE transaksi SET status_pembayaran = 'lunas' WHERE id = ?");
        $update->bind_param("s", $id);
        $update->execute();

        $conn->commit();
        echo "<script>alert('Transaksi berhasil diubah menjadi LUNAS!');window.location='../../pages/transaksi.php';</script>";
    } elseif ($action === 'batal') {
        $update = $conn->prepare("UPDATE transaksi SET status_pembayaran = 'dibatalkan' WHERE id = ?");
        $update->bind_param("s", $id);
        $update->execute();

        $conn->commit();
        echo "<script>alert('Transaksi dibatalkan! Tanggal dan jam tetap tersimpan.');window.location='../../pages/transaksi.php';</script>";
    } elseif ($action === 'tolak') {
        $update = $conn->prepare("UPDATE transaksi SET status_pembayaran = 'revisi' WHERE id = ?");
        $update->bind_param("s", $id);
        $update->execute();

        $conn->commit();
        echo "<script>alert('Bukti pembayaran DITOLAK! User diminta upload ulang.');window.location='../../pages/transaksi.php';</script>";
    } else {
        throw new Exception("Aksi tidak valid!");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo "<script>alert('Gagal memperbarui transaksi: " . addslashes($e->getMessage()) . "');window.location='../../pages/transaksi.php';</script>";
}
