<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Bank</title>
  <link rel="stylesheet" href="../assets/css/event.css?v=<?php echo filemtime('../assets/css/event.css'); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <main class="main">
    <div class="header">
      <div class="header-left">
        <h1>Data Bank</h1>
      </div>
      <div class="header-right">
        <div class="notif"><i class='bx bxs-bell'></i></div>
        <div class="profile">
          <img
            src="../assets/image/<?= $_SESSION['admin_foto'] ?? 'profil.png'; ?>"
            alt="Profile"
            style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
          <span><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
        </div>
      </div>
    </div>

    <div class="table-actions">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari...">
        <i class='bx bx-search'></i>
      </div>
      <button class="btn-tambah" id="openModal"><i class='bx bx-plus'></i>Tambah</button>
    </div>

    <div class="table-wrapper">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Bank</th>
              <th>Atas Nama</th>
              <th>No Rekening</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = "SELECT * FROM bank ORDER BY id DESC";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nama_bank']}</td>
                        <td>{$row['atas_nama']}</td>
                        <td>{$row['no_rekening']}</td>
                        <td>
                          <a href='edit_bank.php?id={$row['id']}'><i class='bx bx-edit' style='color:#007bff; font-size:18px;'></i></a>
                          <a href='hapus_bank.php?id={$row['id']}' onclick=\"return confirm('Hapus data bank ini?')\"><i class='bx bx-trash' style='color:#dc3545; font-size:18px;'></i></a>
                        </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='5' style='text-align:center;'>Belum ada data bank</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- POPUP TAMBAH BANK -->
  <div class="modal" id="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModal">&times;</span>
      <h3>Tambah Bank</h3>
      <form class="modal-form-custom" id="bankForm" action="tambah_bank.php" method="POST">
        <div class="nama">
          <label>Nama Bank</label>
          <input type="text" name="nama_bank" placeholder="Contoh: BCA / Mandiri" required>
        </div>

        <div class="atas-nama">
          <label>Atas Nama</label>
          <input type="text" name="atas_nama" placeholder="Masukkan nama pemilik rekening" required>
        </div>

        <div class="no-rekening">
          <label>No Rekening</label>
          <input type="text" name="no_rekening" placeholder="Masukkan nomor rekening" required>
        </div>

        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const openModal = document.getElementById('openModal');
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');

    openModal.addEventListener('click', () => modal.classList.add('active'));
    closeModal.addEventListener('click', () => modal.classList.remove('active'));
    window.addEventListener('click', e => {
      if (e.target == modal) modal.classList.remove('active');
    });
  </script>
</body>

</html>
