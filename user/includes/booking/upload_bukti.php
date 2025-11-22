<?php
session_start();
include '../../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['bukti']) && !isset($_GET['id'])) {
    header("Location: ../../user.php");
    exit;
}

$id_transaksi = $_POST['id_transaksi'] 
    ?? $_GET['id'] 
    ?? $_SESSION['booking']['id_transaksi'] 
    ?? null;

if (!$id_transaksi) {
    header("Location: ../../user.php");
    exit;
}

if (!isset($_SESSION['booking'])) {
    $q = $conn->prepare("SELECT total FROM transaksi WHERE id = ?");
    $q->bind_param("s", $id_transaksi);
    $q->execute();
    $res = $q->get_result()->fetch_assoc();
    $_SESSION['booking']['total'] = $res['total'];
    $q->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }

    $uploadDir = '../../../uploads/booking/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time()."_".basename($_FILES['bukti']['name']);
    $target = $uploadDir.$filename;
    move_uploaded_file($_FILES['bukti']['tmp_name'], $target);

    $payment_type = $_POST['payment_type'] ?? 'dp';
    $total = floatval($_SESSION['booking']['total']);
    $nominal_bayar = ($payment_type === 'lunas') ? $total : $total / 2;

    $stmt = $conn->prepare("UPDATE transaksi SET bukti_pembayaran=?, status_pembayaran=?, subtotal=? WHERE id=?");
    $stmt->bind_param("ssds", $filename, $payment_type, $nominal_bayar, $id_transaksi);
    $stmt->execute();
    $stmt->close();

    $pesan_admin = "User $user_id mengirim bukti pembayaran transaksi $id_transaksi";
    $notif = $conn->prepare("INSERT INTO notifikasi_admin (pesan, tipe, id_transaksi) VALUES (?, 'pembayaran', ?)");
    $notif->bind_param("ss", $pesan_admin, $id_transaksi);
    $notif->execute();
    $notif->close();

    $judul = "Pesanan Menunggu Konfirmasi";
    $msg = "Bukti pembayaran Anda telah dikirim.";
    $u = $conn->prepare("INSERT INTO pesan (user_id, id_transaksi, judul, pesan, status, created_at) VALUES (?, ?, ?, ?, 'baru', NOW())");
    $u->bind_param("ssss", $user_id, $id_transaksi, $judul, $msg);
    $u->execute();
    $u->close();

    unset($_SESSION['booking']);

    header("Location: berhasil.php?id=$id_transaksi");
    exit;
}
?>
<!DOCTYPE html>
<html>
<body>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_transaksi" value="<?= $id_transaksi ?>">
    <input type="file" name="bukti" required>
    <select name="payment_type">
        <option value="dp">DP</option>
        <option value="lunas">Lunas</option>
    </select>
    <button type="submit">Upload</button>
</form>
</body>
</html>
