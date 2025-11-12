<?php
include '../../../includes/koneksi.php';

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die("Data tidak lengkap!");
}

$id = $_GET['id'];
$status = $_GET['status'];

$allowed = ['pending', 'dp', 'lunas'];
if (!in_array($status, $allowed)) {
    die("Status tidak valid!");
}

$query = $conn->prepare("UPDATE transaksi SET status_pembayaran = ? WHERE id = ?");
$query->bind_param("ss", $status, $id);

if ($query->execute()) {
    header("Location: ../../admin/transaksi.php?success=status_updated");
    exit;
} else {
    die("Gagal memperbarui status: " . $conn->error);
}
