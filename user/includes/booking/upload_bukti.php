<?php
session_start();
include '../../../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Anda harus login!'); window.location='../login.php';</script>";
    exit;
}

if (!isset($_FILES['bukti'])) {
    echo "<script>alert('Tidak ada file bukti!'); history.back();</script>";
    exit;
}

if (!isset($_SESSION['booking'])) {
    echo "<script>alert('Data booking tidak ditemukan!'); history.back();</script>";
    exit;
}

$booking = $_SESSION['booking'];
$user_id = $_SESSION['user_id'];

$uploadDir = '../../../uploads/booking/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = time() . "_" . basename($_FILES['bukti']['name']);
$target = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $target)) {
    echo "<script>alert('Gagal upload file!'); history.back();</script>";
    exit;
}

$id_transaksi = uniqid('trx_');

$payment_type = $_POST['payment_type'] ?? 'dp'; 
$status_pembayaran = ($payment_type == "lunas") ? "lunas" : "dp";

$q = $conn->prepare("
    INSERT INTO transaksi (id, user_id, subtotal, bukti_pembayaran, status_pembayaran, metode_pembayaran, created_at)
    VALUES (?, ?, ?, ?, ?, 'transfer', NOW())
");
$q->bind_param(
    "ssdss",
    $id_transaksi,
    $user_id,
    $booking['total'],
    $filename,
    $status_pembayaran
);
if (!$q->execute()) {
    echo "<script>alert('Gagal menyimpan transaksi!'); history.back();</script>";
    exit;
}
$q->close();

$id_detail = uniqid('det_');
$q2 = $conn->prepare("
    INSERT INTO transaksi_detail (id, id_transaksi, id_lapangan, tanggal, jam_mulai, jam_selesai, durasi, harga_jual)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$q2->bind_param(
    "ssssssid",
    $id_detail,
    $id_transaksi,
    $booking['lapangan_id'],
    $booking['tanggal'],
    $booking['jam_mulai'],
    $booking['jam_selesai'],
    $booking['durasi'],
    $booking['harga']
);
$q2->execute();
$q2->close();

    
$pesan_admin = "User mengirim bukti pembayaran untuk transaksi ID: $id_transaksi";
$notif = $conn->prepare("
    INSERT INTO notifikasi_admin (pesan, tipe, id_transaksi)
    VALUES (?, 'pembayaran', ?)
");
$notif->bind_param("ss", $pesan_admin, $id_transaksi);
$notif->execute();
$notif->close();


$judul_user = "Pesanan Menunggu Konfirmasi";
$pesan_user = "Bukti pembayaran Anda telah dikirim. Silakan tunggu konfirmasi dari admin.";

$userNotif = $conn->prepare("
    INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at)
    VALUES (?, ?, ?, ?, 'baru', NOW())
");
$userNotif->bind_param("isss", $user_id, $id_transaksi, $judul_user, $pesan_user);
$userNotif->execute();
$userNotif->close();


unset($_SESSION['booking']);

echo "<script>
    alert('Bukti pembayaran berhasil dikirim! Menunggu konfirmasi admin.');
    window.location.href = 'berhasil.php?id=$id_transaksi';
</script>";
exit;
?>
