<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
  SELECT k.*, l.nama_lapangan 
  FROM keranjang k
  JOIN lapangan l ON k.id_lapangan = l.id
  WHERE k.user_id = '$user_id'
  ORDER BY k.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang Saya | ZonaFutsal</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: "Poppins", sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 15px;
}

h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
    color: #333;
}

.btn-back {
    display: inline-block;
    background-color: #3498db;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: 0.2s;
}

.btn-back:hover {
    background-color: #2980b9;
}

.cart-box {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: 0.3s;
}

.cart-box:hover {
    transform: translateY(-3px);
}

.cart-info h3 {
    margin: 0 0 8px 0;
    font-size: 22px;
    color: #222;
}

.cart-info p {
    margin: 4px 0;
    color: #555;
    font-size: 15px;
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: flex-end;
}

input[type="checkbox"] {
    transform: scale(1.3);
    cursor: pointer;
}

.btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    border: none;
    transition: 0.2s;
}

.btn-hapus {
    background-color: #e74c3c;
    color: white;
}

.btn-hapus:hover {
    background-color: #c0392b;
}

.btn-checkout {
    background-color: #27ae60;
    color: white;
    margin-top: 15px;
}

.btn-checkout:hover {
    background-color: #219150;
}

.total-box {
    text-align: right;
    font-size: 20px;
    font-weight: 600;
    margin-top: 25px;
    color: #333;
}

.no-data {
    text-align: center;
    padding: 50px 20px;
    font-size: 16px;
    color: #555;
}

.select-all {
    cursor: pointer;
    color: #007bff;
    text-decoration: underline;
    font-size: 15px;
    margin-bottom: 15px;
    display: inline-block;
}
</style>
</head>
<body>

<div class="container">
    <a href="sewa.php" class="btn-back">← Kembali ke Daftar Lapangan</a>
    <h2>🛒 Keranjang Saya</h2>

<?php if (mysqli_num_rows($result) > 0): ?>
<form action="invoice.php" method="POST">
    <label class="select-all">
        <input type="checkbox" id="selectAll"> Pilih Semua
    </label>

    <?php
    $grandTotal = 0;
    while ($row = mysqli_fetch_assoc($result)):
        $grandTotal += $row['total'];
    ?>
    <div class="cart-box">
        <div class="cart-info">
            <h3><?= htmlspecialchars($row['nama_lapangan']); ?></h3>
            <p>📅 <?= htmlspecialchars($row['tanggal']); ?></p>
            <p>⏰ <?= htmlspecialchars($row['jam_mulai'] . ' - ' . $row['jam_selesai']); ?></p>
            <p>🕒 <?= htmlspecialchars($row['durasi']); ?> jam</p>
            <p>💰 Rp <?= number_format($row['total'], 0, ',', '.'); ?></p>
        </div>
        <div class="cart-actions">
            <input type="checkbox" name="selected_ids[]" value="<?= $row['id']; ?>">
            <a href="../includes/booking/hapus_keranjang.php?id=<?= urlencode($row['id']); ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus item ini dari keranjang?')">Hapus</a>
        </div>
    </div>
    <?php endwhile; ?>

    <div class="total-box">
        Total Keseluruhan: <span style="color:#27ae60;">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></span>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" class="btn btn-checkout">Checkout yang Dipilih</button>
    </div>
</form>

<script>
const selectAll = document.getElementById('selectAll');
selectAll.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
});
</script>

<?php else: ?>
<div class="no-data">
    😢 Keranjang kamu masih kosong.<br><br>
    <a href="sewa.php" class="btn-back">← Kembali ke Daftar Lapangan</a>
</div>
<?php endif; ?>
</div>

</body>
</html>
