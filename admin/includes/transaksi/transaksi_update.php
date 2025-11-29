<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_POST['id'] ?? $_GET['id'] ?? null;
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$alasan_batal = $_POST['alasan'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID transaksi tidak ditemukan']);
    exit;
}

$qTrans = mysqli_query($conn, "SELECT user_id, subtotal, jumlah_dibayar FROM transaksi WHERE id='$id'");
$trans = mysqli_fetch_assoc($qTrans);

if (!$trans) {
    echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
    exit;
}

$user_id = $trans['user_id'];
$subtotal = $trans['subtotal'];
$jumlah_dibayar = $trans['jumlah_dibayar'] ?? 0;

if ($action == 'set_lunas') {

    $sisa_bayar = $subtotal - $jumlah_dibayar;
    $new_jumlah_dibayar = $jumlah_dibayar + $sisa_bayar;

    mysqli_query($conn, "
        UPDATE transaksi 
        SET status_pembayaran='lunas', jumlah_dibayar='$new_jumlah_dibayar'
        WHERE id='$id'
    ");
}

elseif ($action == 'batal' && $alasan_batal) {

    $alasan_escape = mysqli_real_escape_string($conn, $alasan_batal);

    mysqli_query($conn, "
        UPDATE transaksi 
        SET status_pembayaran='dibatalkan', alasan_batal='$alasan_escape'
        WHERE id='$id'
    ");

    $judul = "Pembatalan Transaksi";
    $pesan = "Transaksi ID $id dibatalkan. Alasan: $alasan_escape";

    mysqli_query($conn, "
        INSERT INTO pesan (user_id, judul, pesan, status, created_at)
        VALUES ('$user_id', '$judul', '$pesan', 'baru', NOW())
    ");

    header("Location: ../../pages/transaksi.php?message=batal-success");
    exit;
}

$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(jumlah_dibayar),0) AS total 
    FROM transaksi 
    WHERE status_pembayaran IN ('dp','lunas')
"))['total'];

$total_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM transaksi"));
$transaksi_hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM transaksi WHERE DATE(created_at)=CURDATE()"));

if ($action !== 'batal') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'total_pendapatan' => number_format($total_pendapatan, 0, ',', '.'),
        'total_transaksi' => $total_transaksi,
        'transaksi_hari_ini' => $transaksi_hari_ini
    ]);
}
?>
