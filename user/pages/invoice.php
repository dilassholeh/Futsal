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
$stmt = $conn->prepare("SELECT t.*, td.id_lapangan, td.tanggal, td.jam_mulai, td.jam_selesai, td.durasi, td.harga_jual FROM transaksi t LEFT JOIN transaksi_detail td ON td.id_transaksi = t.id WHERE t.id = ?");
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
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif
        }

        body {
            background: #e9f7ef;
            display: flex;
            justify-content: center;
            padding: 32px
        }

     .box {
    width: 100vw;
    background-color: #f4f4f4;
    padding: 20px;
    margin-top: -20px;
}

        h2 {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 20px
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 12px
        }

        .card {
            background: #f8fffa;
            padding: 16px;
            border-radius: 10px;
            border: 1px solid #d8e8d9
        }

        label {
            font-size: 14px;
            font-weight: 600;
            color: #2e7d32
        }

        .value {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 500;
            color: #333
        }

        #timer {
            text-align: center;
            margin-top: 14px;
            font-size: 17px;
            font-weight: 700;
            color: #c62828
        }

        .form-box {
            margin-top: 26px;
            padding: 18px;
            border-radius: 12px;
            background: #f6fdf7;
            border: 1px solid #d8e8d9
        }

        .payment-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px
        }

        .pay-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #d8e8d9;
            font-weight: 600;
            color: #2e7d32;
            font-size: 14px;
            cursor: pointer;
            transition: .2s
        }

        .pay-card:hover {
            border-color: #2e7d32;
            background: #f1fff1
        }

        .pay-card.active {
            background: #2e7d32;
            color: white;
            border-color: #256628
        }

        .pay-card input {
            width: 16px;
            height: 16px
        }

        button {
            margin-top: 16px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #2e7d32;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer
        }

        button:hover {
            background: #256628
        }

        .upload-box {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        #fileLabel {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(46, 125, 50, 0.12);
            padding: 12px;
            border-radius: 10px;
            color: #2e7d32;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            border: 2px dashed #2e7d32;
        }

        #fileLabel:hover {
            background: rgba(46, 125, 50, 0.22);
        }

        #fileLabel i {
            font-size: 18px;
        }

        #fileInput {
            display: none
        }

        #fileName {
            font-size: 14px;
            color: #2e7d32;
            font-weight: 600;
            padding-left: 5px
        }

        #fileLabel i {
            font-size: 18px
        }

        @media(max-width:600px) {
            .grid {
                grid-template-columns: 1fr
            }
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Invoice Pembayaran</h2>

        <div class="grid">
            <div class="card">
                <label>Invoice ID</label>
                <div class="value"><?= htmlspecialchars($data['id']) ?></div>
            </div>
            <div class="card">
                <label>Lapangan</label>
                <div class="value"><?= htmlspecialchars($nama_lapangan) ?></div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <label>Tanggal</label>
                <div class="value"><?= htmlspecialchars($data['tanggal']) ?></div>
            </div>
            <div class="card">
                <label>Jam</label>
                <div class="value"><?= htmlspecialchars($data['jam_mulai']) ?> - <?= htmlspecialchars($data['jam_selesai']) ?></div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <label>Durasi</label>
                <div class="value"><?= htmlspecialchars($data['durasi']) ?> Jam</div>
            </div>
            <div class="card">
                <label>Total Pembayaran</label>
                <div class="value">Rp <?= number_format($data['subtotal'], 0, ',', '.') ?></div>
            </div>
        </div>

        <div id="timer"></div>

        <form action="../includes/booking/upload_bukti.php" method="POST" enctype="multipart/form-data" class="form-box">
            <input type="hidden" name="id_transaksi" value="<?= htmlspecialchars($data['id']) ?>">

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
                <label for="fileInput" id="fileLabel"><i class="fa-solid fa-cloud-arrow-up"></i> Pilih File Bukti Pembayaran</label>
                <input type="file" name="bukti" id="fileInput" required accept="image/*,.pdf">
                <div id="fileName">Belum ada file dipilih</div>
            </div>

            <button type="submit">Kirim Bukti Pembayaran</button>
        </form>
    </div>

    <script>
        let sisa = <?= max(0, (int)$remaining) ?>;

        function countdown() {
            let t = document.getElementById('timer');
            if (sisa <= 0) {
                t.innerHTML = "Waktu upload habis. Booking dibatalkan.";
                setTimeout(() => {
                    window.location.href = "booking.php?id=<?= $lapangan_id ?>";
                }, 1500);
                return;
            }
            t.innerHTML = "Sisa waktu upload: " + Math.floor(sisa / 60) + " menit " + (sisa % 60) + " detik";
            sisa--;
        }
        countdown();
        setInterval(countdown, 1000);

        const cardDP = document.getElementById("card-dp");
        const cardLunas = document.getElementById("card-lunas");
        cardDP.onclick = () => {
            cardDP.classList.add("active");
            cardLunas.classList.remove("active");
            cardDP.querySelector("input").checked = true
        };
        cardLunas.onclick = () => {
            cardLunas.classList.add("active");
            cardDP.classList.remove("active");
            cardLunas.querySelector("input").checked = true
        };

        document.getElementById("fileInput").addEventListener("change", function() {
            const file = this.files[0];
            document.getElementById("fileName").textContent = file ? file.name : "Belum ada file dipilih";
        });
    </script>

</body>

</html>