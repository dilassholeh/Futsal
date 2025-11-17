<?php
session_start();
include '../../../includes/koneksi.php';

if(!isset($_SESSION['admin_id'])){
    die("Akses ditolak!");
}

if(!isset($_GET['id'])){
    die("ID pesan tidak ditemukan!");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

mysqli_query($conn, "UPDATE pesan SET status='dikonfirmasi' WHERE id='$id'");

$pesan = mysqli_query($conn, "SELECT * FROM pesan WHERE id='$id'");
$p = mysqli_fetch_assoc($pesan);

header("Location: ../transaksi/transaksi_detail.php?id=" . $p['id_transaksi']);
exit;
