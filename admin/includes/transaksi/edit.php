<?php
session_start();
include '../../../includes/koneksi.php';
if(!isset($_SESSION['admin_id'])) header("Location: ../../pages/login.php");

$id = $_GET['id'] ?? '';
if(!$id) die("ID transaksi tidak ditemukan.");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $subtotal = $_POST['subtotal'] ?? 0;
    $status = $_POST['status'] ?? 'pending';
    mysqli_query($conn, "UPDATE transaksi SET subtotal='$subtotal', status_pembayaran='$status' WHERE id='$id'");
    header("Location: ../../pages/transaksi.php");
    exit;
}

$transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id'"));
?>
<form method="POST">
<h2>Edit Transaksi #<?= $transaksi['id']; ?></h2>
<p>Total: <input type="number" name="subtotal" value="<?= $transaksi['subtotal']; ?>"></p>
<p>Status:
<select name="status">
    <option value="pending" <?= $transaksi['status_pembayaran']=='pending'?'selected':''; ?>>Pending</option>
    <option value="lunas" <?= $transaksi['status_pembayaran']=='lunas'?'selected':''; ?>>Lunas</option>
    <option value="dibatalkan" <?= $transaksi['status_pembayaran']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
</select>
</p>
<button type="submit">Simpan</button>
</form>
<a href="../../pages/transaksi.php">Kembali</a>
