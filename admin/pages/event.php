<?php
include '../../includes/koneksi.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Event</title>
  <link rel="stylesheet" href="../assets/css/event.css?v=<?php echo filemtime('../assets/css/event.css'); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <main class="main">
    <div class="header">
      <div class="header-left">
        <h1>Data Event</h1>
      </div>
      <div class="header-right">
        <div class="notif"><i class='bx bxs-bell'></i></div>
        <div class="profile">
          <img src="https://i.pravatar.cc/100" alt="Profile">
          <span>Admin</span>
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
              <th>Nama Event</th>
              <th>Kategori</th>
              <th>Deskripsi</th>
              <th>Foto</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = "
              SELECT e.*, k.nama AS nama_kategori 
              FROM event e 
              LEFT JOIN kategori k ON e.kategori_id = k.id
              ORDER BY e.id DESC
            ";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nama_event']}</td>
                        <td>{$row['nama_kategori']}</td>
                        <td>{$row['deskripsi']}</td>
                        <td>";
                if (!empty($row['foto'])) {
                  echo "<img src='../../uploads/event/{$row['foto']}' width='80' style='border-radius:8px;'>";
                } else {
                  echo "<em>-</em>";
                }
                echo "</td>
                        <td>
                          <a href='edit_event.php?id={$row['id']}'><i class='bx bx-edit' style='color:#007bff; font-size:18px;'></i></a>
                          <a href='hapus_event.php?id={$row['id']}' onclick=\"return confirm('Hapus event ini?')\"><i class='bx bx-trash' style='color:#dc3545; font-size:18px;'></i></a>
                        </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='6' style='text-align:center;'>Belum ada event</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- POPUP TAMBAH EVENT -->
  <div class="modal" id="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModal">&times;</span>
      <h3>Tambah Event</h3>
      <form class="modal-form-custom" id="eventForm" action="tambah_event.php" method="POST" enctype="multipart/form-data">
        <div class="nama">
          <label>Nama Event</label>
          <input type="text" name="nama_event" placeholder="Masukkan nama event" required>
        </div>

        <div class="kategori">
          <label>Kategori</label>
          <select name="kategori_id" required>
            <option value="">Pilih kategori</option>
            <?php
            $kat = $conn->query("SELECT * FROM kategori");
            while ($k = $kat->fetch_assoc()) {
              echo "<option value='{$k['id']}'>{$k['nama']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="deskripsi">
          <label>Deskripsi</label>
          <textarea name="deskripsi" rows="3" placeholder="Masukkan deskripsi" required></textarea>
        </div>

        <div class="gambar">
          <label>Foto Event</label>
          <input type="file" name="foto" accept="image/*">
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