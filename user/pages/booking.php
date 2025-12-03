<?php
session_start();
include '../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_GET['id'])) die("ID lapangan tidak ditemukan");

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$id'");
if (mysqli_num_rows($query) == 0) die("Lapangan tidak ditemukan");
$lapangan = mysqli_fetch_assoc($query);

$resultJam = mysqli_query($conn, "SELECT jam FROM jam ORDER BY jam ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Booking Lapangan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #e6f9e8, #f9fff9);
        }

        .wrapper {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .left,
        .right {
            flex: 1 1 45%;
        }

        .left img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        h2 {
            margin-top: 0;
            color: #2e7d32;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 70px;
        }

        .summary div {
            display: flex;
            justify-content: space-between;
            font-weight: 500;
            color: #2e7d32;
            margin: 5px 0;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-success {
            background: #43a047;
            color: #fff;
        }

        .btn-danger {
            background: #e53935;
            color: #fff;
        }

        .btn-warning {
            background: #cddc39;
            color: #333;
        }

        #popupAlert {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #popupAlert .box {
            background: #fff;
            width: 90%;
            max-width: 350px;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            animation: fadeIn .2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media(max-width:768px) {
            .wrapper {
                flex-direction: column;
            }

            .left,
            .right {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>

    <div id="popupAlert">
        <div class="box">
            <p id="popupMessage" style="font-weight:600;margin-bottom:15px"></p>
            <button onclick="closePopup()" class="btn btn-success" style="border-radius:10px">OK</button>
        </div>
    </div>

    <div class="wrapper">
        <div class="left">
            <img src="../../uploads/lapangan/<?= htmlspecialchars($lapangan['foto']); ?>">
        </div>

        <div class="right">
            <h2><?= htmlspecialchars($lapangan['nama_lapangan']); ?></h2>

            <form action="../includes/booking/invoice_redirect.php" method="POST" id="bookingForm">

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="text" id="tanggal" name="tanggal" required placeholder="Pilih Tanggal Booking">

                </div>

                <div class="form-group">
                    <label>Jam Mulai</label>
                    <select id="jamMulai" name="jam_mulai" required>
                        <option value="" disabled selected>- Pilih Jam -</option>
                        <?php while ($r = $resultJam->fetch_assoc()): ?>
                            <option value="<?= substr($r['jam'], 0, 5); ?>"><?= substr($r['jam'], 0, 5); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Durasi (Jam)</label>
                    <input type="number" id="durasi" name="durasi" min="1" max="12" required>
                </div>

                <div class="form-group">
                    <label>Jam Selesai</label>
                    <input type="text" id="jamSelesai" readonly>
                </div>

                <div class="summary">
                    <div><span>Harga / Jam</span><span id="harga">Rp 0</span></div>
                    <div><span>Total</span><span id="total">Rp 0</span></div>
                </div>

                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="catatan"></textarea>
                </div>

                <button type="submit" id="checkoutBtn" class="btn btn-success">Checkout</button>

                <input type="hidden" name="lapangan_id" value="<?= $lapangan['id']; ?>">
                <input type="hidden" id="hargaValue" name="harga">
                <input type="hidden" id="jamMulaiValue" name="jam_mulai">
                <input type="hidden" id="durasiValue" name="durasi">
                <input type="hidden" id="jamSelesaiValue" name="jam_selesai">
                <input type="hidden" id="totalValue" name="total">
            </form>
        </div>
    </div>

    <script>
        function showPopup(msg) {
            document.getElementById("popupMessage").innerText = msg;
            document.getElementById("popupAlert").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("popupAlert").style.display = "none";
        }

        document.addEventListener("DOMContentLoaded", () => {
            const hargaPagi = <?= (int)$lapangan['harga_pagi']; ?>;
            const hargaMalam = <?= (int)$lapangan['harga_malam']; ?>;

            const jm = document.getElementById("jamMulai");
            const dr = document.getElementById("durasi");
            const js = document.getElementById("jamSelesai");
            const harga = document.getElementById("harga");
            const total = document.getElementById("total");

            function hitung() {
                if (!jm.value || !dr.value) return;

                const sH = parseInt(jm.value.split(":")[0]);
                const dur = parseInt(dr.value);
                const end = sH + dur;

                const displayHour = (end % 24).toString().padStart(2, "0") + ":00";
                js.value = end >= 24 ? displayHour + " (Besok)" : displayHour;
                document.getElementById("jamSelesaiValue").value = js.value;

                let totalHarga = 0;
                for (let i = 0; i < dur; i++) {
                    let t = sH + i;
                    totalHarga += t < 18 ? hargaPagi : hargaMalam;
                }
                harga.textContent = "Rp " + (sH < 18 ? hargaPagi : hargaMalam).toLocaleString('id-ID');
                total.textContent = "Rp " + totalHarga.toLocaleString('id-ID');

                document.getElementById("hargaValue").value = (sH < 18 ? hargaPagi : hargaMalam);
                document.getElementById("jamMulaiValue").value = jm.value;
                document.getElementById("durasiValue").value = dur;
                document.getElementById("totalValue").value = totalHarga;
            }

            jm.addEventListener("change", hitung);
            dr.addEventListener("input", hitung);

            document.getElementById("checkoutBtn").addEventListener("click", function(e) {
                if (!jm.value || !dr.value || !js.value) {
                    e.preventDefault();
                    showPopup("Jam mulai dan durasi wajib diisi.");
                    return;
                }
                const sH = parseInt(jm.value.split(":")[0]);
                const dur = parseInt(dr.value);
                if (sH + dur > 24) {
                    e.preventDefault();
                    showPopup("Durasi melebihi batas jam operasional.\nMax sampai 24:00.");
                }
            });
        });

        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });
    </script>

</body>

</html>