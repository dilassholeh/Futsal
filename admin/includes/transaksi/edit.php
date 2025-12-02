<?php
session_start();
include '../../../includes/koneksi.php';
if (!isset($_SESSION['admin_id'])) header("Location: ../../pages/login.php");

$id = $_GET['id'] ?? '';
if (!$id) die("ID transaksi tidak ditemukan.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subtotal = $_POST['subtotal'] ?? 0;
    $status = $_POST['status'] ?? 'pending';

    mysqli_query($conn, "UPDATE transaksi SET subtotal='$subtotal', status_pembayaran='$status' WHERE id='$id'");
    header("Location: ../../pages/transaksi.php");
    exit;
}

$transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Transaksi</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f7fb;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 500px;
        margin: 50px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        animation: fadeIn 0.4s ease;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 700;
        color: #333;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        color: #444;
    }

    input, select {
        width: 100%;
        padding: 12px;
        font-size: 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        outline: none;
        transition: 0.3s;
        background: #f9f9f9;
        margin-bottom: 20px;
    }

    input:focus, select:focus {
        border-color: #26a639ff;
        background: #fff;
        box-shadow: 0 0 3px rgba(74,121,255,0.4);
    }

    button {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        border: none;
        border-radius: 8px;
        background: #26a639ff;
        color: #fff;
        transition: 0.25s ease-in-out;
    }

    button:hover {
        background: #26a639ff;
    }

    .back {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-weight: 600;
        color: #26a639ff;
        text-decoration: none;
        transition: 0.3s;
    }

    .back:hover {
        color: #26a639ff;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

</head>
<body>

<div class="container">
    <h2>Edit Transaksi #<?= $transaksi['id']; ?></h2>

    <form method="POST">
        <label>Total (Subtotal)</label>
        <input type="number" name="subtotal" value="<?= $transaksi['subtotal']; ?>" required>

        <label>Status Pembayaran</label>
        <select name="status">
            <option value="pending" <?= $transaksi['status_pembayaran']=='pending'?'selected':''; ?>>Pending</option>
            <option value="lunas" <?= $transaksi['status_pembayaran']=='lunas'?'selected':''; ?>>Lunas</option>
            <option value="dibatalkan" <?= $transaksi['status_pembayaran']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
        </select>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a class="back" href="../../pages/transaksi.php">← Kembali ke Daftar Transaksi</a>
</div>

</body>
</html>
