<?php
session_start();
include '../../../includes/koneksi.php';


if(!isset($_SESSION['admin_id'])){
    header("Location: ../../pages/login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if(!$id || !$action){
    header("Location: ../../pages/transaksi.php");
    exit;
}

if($action == 'set_lunas'){
    mysqli_query($conn, "UPDATE transaksi SET status_pembayaran='lunas' WHERE id='$id'");
} elseif($action == 'batal'){
    mysqli_query($conn, "UPDATE transaksi SET status_pembayaran='dibatalkan' WHERE id='$id'");
    $detail = mysqli_query($conn, "SELECT lapangan_id, tanggal, jam_mulai, jam_selesai FROM transaksi_detail WHERE id_transaksi='$id'");
    while($d = mysqli_fetch_assoc($detail)){
        mysqli_query($conn, "DELETE FROM booking_lapangan WHERE lapangan_id='{$d['lapangan_id']}' AND tanggal='{$d['tanggal']}' AND jam_mulai='{$d['jam_mulai']}' AND jam_selesai='{$d['jam_selesai']}'");
    }
}

header("Location: ../../pages/transaksi.php");
exit;
