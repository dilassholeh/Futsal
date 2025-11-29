<?php
session_start();
include '../../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id_transaksi = $_POST['id_transaksi'] ?? $_GET['id'] ?? null;
if (!$id_transaksi) {
    header("Location: ../../user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $uploadDir = '../../../uploads/booking/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time() . "_" . basename($_FILES['bukti']['name']);
    $target = $uploadDir . $filename;
    move_uploaded_file($_FILES['bukti']['tmp_name'], $target);

    $cek = mysqli_query($conn, "SELECT subtotal,jumlah_dibayar FROM transaksi WHERE id='$id_transaksi'");
    $row = mysqli_fetch_assoc($cek);
    $subtotal = floatval($row['subtotal']);
    $dibayar = floatval($row['jumlah_dibayar']);
    $payment_type = $_POST['payment_type'] ?? 'dp';

    if ($payment_type === 'dp') {
        $bayar = $subtotal * 0.5;
        $status_pembayaran = 'dp';
    } else {
        $bayar = $subtotal - $dibayar;
        $total_bayar = $dibayar + $bayar;
        $status_pembayaran = ($total_bayar >= $subtotal) ? 'lunas' : 'menunggu_konfirmasi';
    }

    $stmt = $conn->prepare("UPDATE transaksi 
        SET bukti_pembayaran=?, 
            jumlah_dibayar=jumlah_dibayar + ?, 
            status_pembayaran=?
        WHERE id=?");
    $stmt->bind_param("sdss", $filename, $bayar, $status_pembayaran, $id_transaksi);
    $stmt->execute();
    $stmt->close();

    $pesan_admin = "User $user_id mengirim bukti pembayaran transaksi $id_transaksi";
    $na = $conn->prepare("INSERT INTO notifikasi_admin (pesan, tipe, id_transaksi) VALUES (?, 'pembayaran', ?)");
    $na->bind_param("ss", $pesan_admin, $id_transaksi);
    $na->execute();
    $na->close();

    $judul = "Pembayaran Dikirim";
    $isi = "Bukti pembayaran sudah dikirim, menunggu konfirmasi admin.";
    $pm = $conn->prepare("INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at) VALUES (?, ?, ?, ?, 'baru', NOW())");
    $pm->bind_param("ssss", $user_id, $id_transaksi, $judul, $isi);
    $pm->execute();
    $pm->close();

    header("Location: berhasil.php?id=$id_transaksi");
    exit;
}
