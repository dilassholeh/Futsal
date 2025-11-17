<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['booking'])) {
    die("Tidak ada data booking!");
}

$data = $_SESSION['booking'];

$bankQuery = mysqli_query($conn, "SELECT * FROM bank ORDER BY id ASC");

$totalTagihan = $data['total'];
$dp = $totalTagihan / 2;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: "Poppins", sans-serif;
            background: #e8f5e9;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            height: 90vh;
            background: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .invoice-box {
            display: flex;
            width: 100%;
            height: 100%;
            max-height: 90vh;
            background: #f6fff8;
            border-radius: 16px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .invoice-left {
            flex: 1;
            background: #ffffff;
            padding: 30px;
            border-right: 3px solid #e8f5e9;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .invoice-right {
            flex: 2;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 4px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        td:first-child {
            width: 40%;
            font-weight: bold;
            color: #555;
        }

        .total {
            margin-top: 20px;
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            color: #1b5e20;
            text-align: right;
            font-size: 18px;
        }

        .bank-section {
            flex: 1;
            margin-bottom: 20px;
        }

        .bank-item {
            font-size: 14px;
            margin-bottom: 8px;
            color: #2e7d32;
        }

        .bank-item span {
            display: block;
            font-weight: normal;
            color: #555;
        }

        .payment-option {
            margin-bottom: 10px;
        }

        .payment-option label {
            margin-right: 15px;
            font-weight: bold;
            color: #2e7d32;
        }

        #display-amount {
            font-weight: bold;
            color: #1b5e20;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .upload-box {
            border: 2px dashed #9ccc65;
            border-radius: 10px;
            padding: 20px;
            background: #f9fff9;
            text-align: center;
            margin-bottom: 15px;
        }

        input[type="file"] {
            margin-top: 8px;
        }

        button {
            width: 100%;
            background: #2e7d32;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #1b5e20;
        }

        .back-btn {
            background: #c62828;
        }

        .back-btn:hover {
            background: #8e0000;
        }

        @media (max-width: 900px) {
            .container {
                height: auto;
                padding: 20px;
                align-items: flex-start;
            }

            .invoice-box {
                flex-direction: column;
                max-height: none;
                height: auto;
            }

            .invoice-left {
                border-right: none;
                border-bottom: 3px solid #e8f5e9;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="invoice-box">
            <div class="invoice-left">
                <div>
                    <h2>Detail Booking</h2>
                    <table>
                        <tr>
                            <td>Nama Lapangan</td>
                            <td><?= htmlspecialchars($data['nama_lapangan']); ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Booking</td>
                            <td><?= htmlspecialchars($data['tanggal']); ?></td>
                        </tr>
                        <tr>
                            <td>Jam Mulai</td>
                            <td><?= htmlspecialchars($data['jam_mulai']); ?></td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td><?= htmlspecialchars($data['durasi']); ?> Jam</td>
                        </tr>
                        <tr>
                            <td>Jam Selesai</td>
                            <td><?= htmlspecialchars($data['jam_selesai']); ?></td>
                        </tr>
                        <tr>
                            <td>Harga per Jam</td>
                            <td>Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="total">
                    Total Tagihan: Rp <?= number_format($totalTagihan, 0, ',', '.'); ?>
                </div>
            </div>

            <div class="invoice-right">
                <div class="bank-section">
                    <h2>Pembayaran</h2>

                    <div class="payment-option">
                        <label><input type="radio" name="payment_type" value="dp" checked> DP (50%)</label>
                        <label><input type="radio" name="payment_type" value="lunas"> Lunas</label>
                    </div>

                    <div id="display-amount">Nominal: Rp <?= number_format($dp, 0, ',', '.'); ?></div>

                    <p>Silakan transfer ke salah satu rekening berikut:</p>
                    <?php while ($bank = mysqli_fetch_assoc($bankQuery)): ?>
                        <div class="bank-item">
                            <strong><?= htmlspecialchars($bank['nama_bank']); ?></strong>
                            <span>a.n <?= htmlspecialchars($bank['atas_nama']); ?></span>
                            <span>No. Rek: <?= htmlspecialchars($bank['no_rekening']); ?></span>
                        </div>
                    <?php endwhile; ?>
                    <p><em>Setelah transfer, unggah bukti pembayaran di bawah ini.</em></p>
                </div>

                <div>
                    <form action="../includes/booking/upload_bukti.php" method="POST" enctype="multipart/form-data">

                        <input type="hidden" name="lapangan_id" value="<?= $data['lapangan_id']; ?>">

                        <input type="hidden" name="payment_type" id="payment_type_input" value="dp">

                        <div class="upload-box">
                            <label><strong>Upload Bukti Pembayaran</strong></label><br>
                            <input type="file" name="bukti" accept="image/*,.pdf" required>
                        </div>

                        <button type="submit">Kirim Bukti Pembayaran</button>
                    </form>


                    <form action="booking.php?id=<?= $data['lapangan_id']; ?>">
                        <button class="back-btn" type="submit">Kembali</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dpAmount = <?= $dp ?>;
        const totalAmount = <?= $totalTagihan ?>;
        const displayAmount = document.getElementById('display-amount');
        const paymentTypeInputs = document.querySelectorAll('input[name="payment_type"]');
        const paymentTypeHidden = document.getElementById('payment_type_input');

        paymentTypeInputs.forEach(input => {
            input.addEventListener('change', () => {
                if (input.value === 'dp') {
                    displayAmount.textContent = 'Nominal: Rp ' + dpAmount.toLocaleString('id-ID');
                } else {
                    displayAmount.textContent = 'Nominal: Rp ' + totalAmount.toLocaleString('id-ID');
                }
                paymentTypeHidden.value = input.value;
            });
        });
    </script>

</body>

</html>