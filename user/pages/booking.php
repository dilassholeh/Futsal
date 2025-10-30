<?php

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/booking.css?v=<?php echo filemtime('../assets/css/booking.css'); ?>">
</head>
<body>
    
<div class="container">
    <h2>Detail Booking</h2>

    <table>
        <thead>
            <tr>
                <th>Lapangan</th>
                <th>Harga</th>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Durasi (Jam)</th>
                <th>Jam Selesai</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-label="Lapangan">Lapangan A</td>
                <td data-label="Harga">100,000</td>
                <td data-label="Tanggal"><input type="date"></td>
                <td data-label="Jam Mulai">
                    <select>
                        <option selected>- Pilih Jam -</option>
                        <option>08:00</option>
                        <option>09:00</option>
                        <option>10:00</option>
                    </select>
                </td>
                <td data-label="Durasi"><input type="number" min="1"></td>
                <td data-label="Jam Selesai">—</td>
                <td data-label="Total">—</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row"><span>SubTotal</span><span>Rp 0</span></div>
        <div class="summary-row"><span>Grand Total</span><span>Rp 0</span></div>
    </div>

    <label for="catatan" style="font-weight:600; margin-top:20px; display:block;">Catatan</label>
    <textarea id="catatan" rows="3" placeholder="Tambahkan catatan untuk booking Anda..."></textarea>

    <div class="btn-group">
        <button class="btn btn-danger">Batal Booking</button>
        <button class="btn btn-success">Checkout</button>
    </div>
</div>

</body>
</html>