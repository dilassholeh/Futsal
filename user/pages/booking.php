<?php
include '../../includes/koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    die("ID lapangan tidak ditemukan!");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM lapangan WHERE id = '$id'");
if (mysqli_num_rows($query) == 0) {
    die("Lapangan tidak ditemukan!");
}

$lapangan = mysqli_fetch_assoc($query);
$resultJam = mysqli_query($conn, "SELECT jam FROM jam ORDER BY jam ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Booking Lapangan - <?= htmlspecialchars($lapangan['nama_lapangan']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(135deg, #e6f9e8, #f9fff9);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .container {
            width: 95%;
            max-width: 950px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 100, 0, 0.15);
            padding: 30px 40px;
            max-height: 90vh;
            overflow-y: auto;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            font-weight: 600;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #c8e6c9;
            border-radius: 12px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #2e7d32;
            color: white;
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        th:first-child {
            border-top-left-radius: 12px;
        }

        th:last-child {
            border-top-right-radius: 12px;
        }

        tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }

        select,
        input[type="date"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            outline: none;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        select:focus,
        input:focus,
        textarea:focus {
            border-color: #66bb6a;
            box-shadow: 0 0 6px rgba(102, 187, 106, 0.4);
        }

        .summary-box {
            background: #f1fff3;
            border: 1px solid #c8e6c9;
            border-radius: 12px;
            padding: 15px 20px;
            margin-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            color: #2e7d32;
            font-weight: 500;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: 500;
            color: #333;
        }

        textarea {
            resize: vertical;
            min-height: 70px;
        }

        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 22px;
            border-radius: 25px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-success {
            background-color: #43a047;
            color: white;
        }

        .btn-success:hover {
            background-color: #2e7d32;
        }

        .btn-danger {
            background-color: #e53935;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c62828;
        }

        .btn-warning {
            background-color: #cddc39;
            color: #333;
        }

        .btn-warning:hover {
            background-color: #afb42b;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            table,
            th,
            td {
                font-size: 14px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 14px;
            }
        }

        .flatpickr-input {
            background: #f9fff9;
            border: 1px solid #a5d6a7;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 15px;
            color: #2e7d32;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            width: 100%;
        }

        .flatpickr-input:hover {
            border-color: #66bb6a;
            box-shadow: 0 0 8px rgba(102, 187, 106, 0.4);
        }

        .flatpickr-input:focus {
            border-color: #388e3c;
            box-shadow: 0 0 10px rgba(56, 142, 60, 0.5);
            background: #ffffff;
        }

        .date-input-wrapper {
            position: relative;
            width: 100%;
        }

        .date-input-wrapper input {
            width: 100%;
            padding: 10px 40px 10px 14px;
            border: 1px solid #a5d6a7;
            border-radius: 10px;
            font-size: 15px;
            color: #2e7d32;
            background: #f9fff9;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .date-input-wrapper input:hover {
            border-color: #66bb6a;
            box-shadow: 0 0 8px rgba(102, 187, 106, 0.4);
        }

        .date-input-wrapper input:focus {
            border-color: #388e3c;
            box-shadow: 0 0 10px rgba(56, 142, 60, 0.5);
            background: #ffffff;
        }

        .calendar-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #2e7d32;
            pointer-events: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hargaPagi = <?= (int)$lapangan['harga_pagi']; ?>;
            const hargaMalam = <?= (int)$lapangan['harga_malam']; ?>;
            const hargaDisplay = document.getElementById('harga');
            const totalDisplay = document.getElementById('total');
            const subtotalDisplay = document.getElementById('subtotal');
            const grandDisplay = document.getElementById('grandtotal');
            const hargaValue = document.getElementById('hargaValue');
            const totalValue = document.getElementById('totalValue');
            const jamMulai = document.getElementById('jamMulai');
            const durasi = document.getElementById('durasi');
            const jamSelesai = document.getElementById('jamSelesai');
            const jamMulaiValue = document.getElementById('jamMulaiValue');
            const durasiValue = document.getElementById('durasiValue');
            const jamSelesaiValue = document.getElementById('jamSelesaiValue');

            function hitung() {
                const jm = jamMulai.value;
                const dr = parseInt(durasi.value);
                if (!jm || isNaN(dr)) return;

                const jamMulaiInt = parseInt(jm.split(':')[0]);
                const jamSelesaiInt = jamMulaiInt + dr;
                const jamSelesaiStr = (jamSelesaiInt < 10 ? '0' : '') + jamSelesaiInt + ':00';
                jamSelesai.textContent = jamSelesaiStr;
                jamSelesaiValue.value = jamSelesaiStr;

                const hargaPerJam = jamMulaiInt < 18 ? hargaPagi : hargaMalam;
                hargaDisplay.textContent = 'Rp ' + hargaPerJam.toLocaleString('id-ID');
                hargaValue.value = hargaPerJam;

                const total = hargaPerJam * dr;
                totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
                subtotalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
                grandDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
                totalValue.value = total;

                jamMulaiValue.value = jm;
                durasiValue.value = dr;
            }

            jamMulai.addEventListener('change', hitung);
            durasi.addEventListener('input', hitung);
        });

        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#tanggal", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                minDate: "today",
                theme: "green",
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    },
                },
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h2>Booking Lapangan: <?= htmlspecialchars($lapangan['nama_lapangan']); ?></h2>

        <form action="../includes/booking/invoice_redirect.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Lapangan</th>
                        <th>Harga/Jam</th>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
                        <th>Durasi</th>
                        <th>Jam Selesai</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= htmlspecialchars($lapangan['nama_lapangan']); ?></td>
                        <td id="harga">Rp 0</td>
                        <td>
                            <div class="date-input-wrapper">
                                <input type="text" id="tanggal" name="tanggal" placeholder="Pilih tanggal..." required>
                                <i class='bx bx-calendar calendar-icon'></i>
                            </div>
                        </td>

                        <td>
                            <select id="jamMulai" name="jam_mulai" required>
                                <option selected disabled>- Pilih Jam -</option>
                                <?php while ($row = $resultJam->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars(substr($row['jam'], 0, 5)); ?>">
                                        <?= htmlspecialchars(substr($row['jam'], 0, 5)); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td><input type="number" id="durasi" name="durasi_display" min="1" max="5" placeholder="jam" required></td>
                        <td id="jamSelesai">—</td>
                        <td id="total">—</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-box">
                <div class="summary-row"><span>SubTotal</span><span id="subtotal">Rp 0</span></div>
                <div class="summary-row"><span>Grand Total</span><span id="grandtotal">Rp 0</span></div>
            </div>

            <input type="hidden" name="lapangan_id" value="<?= htmlspecialchars($lapangan['id']); ?>">
            <input type="hidden" name="nama_lapangan" value="<?= htmlspecialchars($lapangan['nama_lapangan']); ?>">
            <input type="hidden" name="harga" id="hargaValue">
            <input type="hidden" name="jam_mulai" id="jamMulaiValue">
            <input type="hidden" name="durasi" id="durasiValue">
            <input type="hidden" name="jam_selesai" id="jamSelesaiValue">
            <input type="hidden" name="total" id="totalValue">

            <label for="catatan">Catatan</label>
            <textarea name="catatan" id="catatan" rows="3" placeholder="Tulis catatan tambahan..."></textarea>

            <div class="btn-group">
                <button type="reset" class="btn btn-danger">Batal</button>
                <button type="submit" class="btn btn-success">Checkout</button>
                <button type="button" class="btn btn-warning" onclick="tambahKeKeranjang()">Keranjang</button>
            </div>
        </form>
    </div>

    <script>
        function tambahKeKeranjang() {
            const form = document.querySelector('form');
            const formData = new FormData(form);

            fetch('../includes/booking/add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('✅ Berhasil ditambahkan ke keranjang!');
                        form.reset();
                        document.getElementById('harga').textContent = 'Rp 0';
                        document.getElementById('total').textContent = '—';
                        document.getElementById('jamSelesai').textContent = '—';
                        document.getElementById('subtotal').textContent = 'Rp 0';
                        document.getElementById('grandtotal').textContent = 'Rp 0';
                    } else {
                        alert('❌ Gagal menambahkan ke keranjang: ' + data.message);
                    }
                })
                .catch(err => console.error('Error:', err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const lapanganId = "<?= $lapangan['id']; ?>";
            const tanggalInput = document.getElementById('tanggal');
            const jamSelect = document.getElementById('jamMulai');

            function loadJamTersedia() {
                const tanggal = tanggalInput.value;
                if (!lapanganId || !tanggal) return;
                fetch(`../includes/booking/get_available_jam.php?lapangan_id=${lapanganId}&tanggal=${tanggal}`)
                    .then(res => res.json())
                    .then(data => {
                        jamSelect.innerHTML = '<option selected disabled>- Pilih Jam -</option>';
                        data.forEach(j => {
                            const opt = document.createElement('option');
                            opt.value = j;
                            opt.textContent = j;
                            jamSelect.appendChild(opt);
                        });
                    })
                    .catch(err => console.error('Error memuat jam:', err));
            }

            tanggalInput.addEventListener('change', loadJamTersedia);
        });
    </script>
</body>

</html>