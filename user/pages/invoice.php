<?php
session_start();
include '../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan";
    exit;
}
$id = $_GET['id'];

$conn->query("UPDATE transaksi SET status_pembayaran='expired' WHERE status_pembayaran='pending' AND expire_at <= NOW()");

$stmt = $conn->prepare("SELECT t.*, td.id_lapangan, td.tanggal, td.jam_mulai, td.jam_selesai, td.durasi, td.harga_jual 
    FROM transaksi t 
    LEFT JOIN transaksi_detail td ON td.id_transaksi = t.id 
    WHERE t.id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();
$stmt->close();

if (!$data) {
    echo "Transaksi tidak ditemukan";
    exit;
}

$remaining = strtotime($data['expire_at']) - time();
$lapangan_id = $data['id_lapangan'];

$nama_lapangan = '';
if ($lapangan_id) {
    $q = $conn->prepare("SELECT nama_lapangan FROM lapangan WHERE id = ?");
    $q->bind_param("s", $lapangan_id);
    $q->execute();
    $r = $q->get_result()->fetch_assoc();
    $nama_lapangan = $r['nama_lapangan'] ?? '';
    $q->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Invoice <?= htmlspecialchars($id) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    background: #e9f7ef;
    display: flex;
    justify-content: center;
    padding: 40px 12px;
    font-family: "Poppins", sans-serif;
}
.card-wrapper {
    width: 100%;
    max-width: 530px;
    background: #ffffff;
    padding: 28px;
    border-radius: 18px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.08);
    border: 1px solid #d2e8d5;
}
h2 {
    text-align: center;
    font-size: 26px;
    font-weight: 700;
    color: #2e7d32;
    margin-bottom: 25px;
}
.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 10px;
}
.card {
    background: #f8fffa;
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #d8e8d9;
}
label {
    font-size: 13px;
    font-weight: 600;
    color: #2e7d32;
}
.value {
    font-size: 14px;
    font-weight: 600;
    margin-top: 4px;
}
#timer {
    margin: 14px 0;
    text-align: center;
    font-size: 16px;
    font-weight: 700;
    color: #c62828;
}
.form-box {
    margin-top: 15px;
    padding: 18px;
    background: #f6fdf7;
    border-radius: 12px;
    border: 1px solid #d8e8d9;
}
.payment-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 10px;
}
.pay-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 10px;
    font-weight: 600;
    color: #2e7d32;
    border: 2px solid #d8e8d9;
    cursor: pointer;
    transition: .2s;
}
.pay-card.active {
    background: #2e7d32;
    color: #fff;
    border-color: #256628;
}
.upload-box {
    margin-top: 16px;
}
#fileLabel {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(46,125,50,.12);
    padding: 12px;
    border-radius: 10px;
    border: 2px dashed #2e7d32;
    font-size: 15px;
    font-weight: 600;
    color: #2e7d32;
    cursor: pointer;
}
#fileInput { display: none; }
#fileName {
    font-size: 13.5px;
    font-weight: 600;
    margin-top: 5px;
}
button {
    width: 100%;
    margin-top: 18px;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-size: 15.5px;
    font-weight: 700;
    background: #2e7d32;
    color: #fff;
    cursor: pointer;
}
@media(max-width:480px) {
    .grid { grid-template-columns: 1fr; }
}
</style>
</head>

<body>
<div class="card-wrapper">

    <h2>Invoice Pembayaran</h2>

    <div class="grid">
        <div class="card">
            <label>Invoice ID</label>
            <div class="value"><?= $data['id'] ?></div>
        </div>
        <div class="card">
            <label>Lapangan</label>
            <div class="value"><?= $nama_lapangan ?></div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <label>Tanggal</label>
            <div class="value"><?= $data['tanggal'] ?></div>
        </div>
        <div class="card">
            <label>Jam</label>
            <div class="value"><?= $data['jam_mulai'] ?> - <?= $data['jam_selesai'] ?></div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <label>Durasi</label>
            <div class="value"><?= $data['durasi'] ?> Jam</div>
        </div>
        <div class="card">
            <label>Total Pembayaran</label>
            <div class="value">Rp <?= number_format($data['subtotal'],0,',','.') ?></div>
        </div>
    </div>

    <div id="timer"></div>

    <form action="../includes/booking/upload_bukti.php" method="POST" enctype="multipart/form-data" class="form-box">
        <input type="hidden" name="id_transaksi" value="<?= $data['id'] ?>">

        <label>Pilih Pembayaran</label>
        <div class="payment-options">
            <label class="pay-card active" id="card-dp">
                <input type="radio" name="payment_type" value="dp" checked> DP (50%)
            </label>
            <label class="pay-card" id="card-lunas">
                <input type="radio" name="payment_type" value="lunas"> Lunas
            </label>
        </div>

        <div class="upload-box">
            <label id="fileLabel" for="fileInput"><i class="fa-solid fa-cloud-arrow-up"></i> Pilih Bukti Pembayaran</label>
            <input type="file" id="fileInput" name="bukti" required accept="image/*,.pdf">
            <div id="fileName">Belum ada file dipilih</div>
        </div>

        <button type="submit">Kirim Bukti Pembayaran</button>
    </form>
</div>

<script>
let sisa = <?= max(0,(int)$remaining) ?>;
function countdown() {
    const t = document.getElementById('timer');
    if (sisa <= 0) {
        t.innerHTML = "Waktu upload habis. Booking dibatalkan.";
        setTimeout(() => {
            window.location.href = "booking.php?id=<?= $lapangan_id ?>";
        }, 1500);
        return;
    }
    t.innerHTML = "Sisa waktu upload: " + Math.floor(sisa/60) + " menit " + (sisa%60) + " detik";
    sisa--;
}
countdown();
setInterval(countdown, 1000);

function activate(i) {
    document.querySelectorAll(".pay-card").forEach(e => e.classList.remove("active"));
    i.classList.add("active");
    i.querySelector("input").checked = true;
}

document.getElementById("card-dp").onclick = () => activate(card-dp);
document.getElementById("card-lunas").onclick = () => activate(card-lunas);

document.getElementById("fileInput").addEventListener("change", function(){
    document.getElementById("fileName").textContent = this.files[0]?.name || "Belum ada file dipilih";
});
</script>

</body>
</html>
