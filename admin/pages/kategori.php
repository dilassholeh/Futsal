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
  <title>Data Kategori</title>
  <link rel="stylesheet" href="../assets/css/kategori.css?v=<?php echo filemtime('../assets/css/kategori.css'); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <main class="main">
    <div class="header">
      <h1>Data Kategori</h1>
      <div class="header-right">
        <div class="profile-card">
          <img src="../assets/image/<?php echo htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" class="profile-img">
          <div class="profile-info">
            <span class="profile-name"><?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
          </div>
          <a href="../logout.php" class="btn-logout"><i class='bx bx-log-out'></i></a>
        </div>
      </div>
    </div>
    <div class="bottom">
      <div class="latar">
        <div class="table-actions">
          <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari...">
            <i class='bx bx-search'></i>
          </div>
          <button class="btn-tambah" id="openAddModal"><i class='bx bx-plus'></i>Tambah</button>
        </div>

        <div class="table-wrapper">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama Kategori</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="kategoriTable">
                <?php
                $query = "SELECT * FROM kategori ORDER BY id DESC";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nama']}</td>
                        <td>
                          <button class='btn btn-edit' 
                            data-id='{$row['id']}'
                            data-nama='{$row['nama']}'>
                            <i class='bx bx-edit' style='color:#007bff; font-size:18px;'></i>
                          </button>
                          <button class='btn btn-delete' data-id='{$row['id']}'>
                            <i class='bx bx-trash' style='color:#dc3545; font-size:18px;'></i>
                          </button>
                        </td>
                      </tr>";
                  }
                } else {
                  echo "<tr><td colspan='3' style='text-align:center;'>Belum ada data kategori</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <div class="modal" id="addModal">
    <div class="modal-content">
      <span class="close-btn" id="closeAdd">&times;</span>
      <h3>Tambah Kategori</h3>
      <form id="addForm" action="../includes/kategori/tambah_kategori.php" method="POST">
        <label>Nama Kategori</label>
        <input type="text" name="nama" required>
        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="editModal">
    <div class="modal-content">
      <span class="close-btn" id="closeEdit">&times;</span>
      <h3>Edit Kategori</h3>
      <form id="editForm" action="../includes/kategori/edit_kategori_ajax.php" method="POST">
        <input type="hidden" name="id" id="edit_id">
        <label>Nama Kategori</label>
        <input type="text" name="nama" id="edit_nama" required>
        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <h3>Yakin ingin menghapus data ini?</h3>
      <form id="deleteForm" action="../includes/kategori/hapus_kategori_ajax.php" method="POST">
        <input type="hidden" name="id" id="delete_id">
        <div class="submit">
          <button type="submit" style="background:#dc3545"><i class='bx bx-trash'></i> Hapus</button>
        </div>
      </form>
      <div class="submit">
        <button id="cancelDelete" style="background:#6c757d; margin-top:5px;">Batal</button>
      </div>
    </div>
  </div>

  <script>
    const addModal = document.getElementById('addModal');
    document.getElementById('openAddModal').onclick = () => addModal.classList.add('active');
    document.getElementById('closeAdd').onclick = () => addModal.classList.remove('active');

    const editModal = document.getElementById('editModal');
    const editBtns = document.querySelectorAll('.btn-edit');
    editBtns.forEach(btn => {
      btn.onclick = () => {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_nama').value = btn.dataset.nama;
        editModal.classList.add('active');
      };
    });
    document.getElementById('closeEdit').onclick = () => editModal.classList.remove('active');

    const deleteModal = document.getElementById('deleteModal');
    const deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(btn => {
      btn.onclick = () => {
        document.getElementById('delete_id').value = btn.dataset.id;
        deleteModal.classList.add('active');
      };
    });
    document.getElementById('cancelDelete').onclick = () => deleteModal.classList.remove('active');
  </script>
</body>

</html>