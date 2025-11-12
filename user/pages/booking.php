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
  <link rel="stylesheet" href="../assets/css/booking.css?v=<?= filemtime('../assets/css/booking.css'); ?>">
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
            <th>Durasi (Jam)</th>
            <th>Jam Selesai</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?= htmlspecialchars($lapangan['nama_lapangan']); ?></td>
            <td id="harga">Rp 0</td>
            <td>
              <input type="date" id="tanggal" name="tanggal" min="<?= date('Y-m-d'); ?>" required>
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
      <textarea name="catatan" id="catatan" rows="3"></textarea>

      <div class="btn-group">
        <button type="reset" class="btn btn-danger">Batal</button>
        <button type="submit" class="btn btn-success">Checkout</button>
      </div>
    </form>
  </div>

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

      const lapanganId = "<?= $lapangan['id']; ?>";
      const tanggalInput = document.getElementById('tanggal');

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

      function loadJamTersedia() {
        const tanggal = tanggalInput.value;
        if (!lapanganId || !tanggal) return;

        jamMulai.innerHTML = '<option selected disabled>Memuat jam...</option>';

        fetch(`../includes/booking/get_available_jam.php?lapangan_id=${lapanganId}&tanggal=${tanggal}`)
          .then(res => res.json())
          .then(data => {
            jamMulai.innerHTML = '<option selected disabled>- Pilih Jam -</option>';
            if (data.length === 0) {
              const opt = document.createElement('option');
              opt.disabled = true;
              opt.textContent = 'Semua jam sudah penuh';
              jamMulai.appendChild(opt);
              return;
            }
            data.forEach(j => {
              const opt = document.createElement('option');
              opt.value = j;
              opt.textContent = j;
              jamMulai.appendChild(opt);
            });
          })
          .catch(err => {
            console.error('Error memuat jam:', err);
            jamMulai.innerHTML = '<option disabled>Gagal memuat jam</option>';
          });
      }

      tanggalInput.addEventListener('change', loadJamTersedia);
      jamMulai.addEventListener('change', hitung);
      durasi.addEventListener('input', hitung);
    });
  </script>
</body>
</html>
