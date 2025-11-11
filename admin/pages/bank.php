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
  <link rel="stylesheet" href="../assets/css/bank.css?v=<?php echo filemtime('../assets/css/bank.css'); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>

  </style>
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
          <img src="../assets/image/<?= $_SESSION['admin_foto'] ?? 'profil.png'; ?>" alt="Profile"
            style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
          <span><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
        </div>
      </div>
    </div>


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
                <th>Nama Bank</th>
                <th>Atas Nama</th>
                <th>No Rekening</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="bankTable">
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
                          <button class='btn btn-edit' 
                            data-id='{$row['id']}'
                            data-nama='{$row['nama_bank']}'
                            data-atas='{$row['atas_nama']}'
                            data-rek='{$row['no_rekening']}'>
                            <i class='bx bx-edit' style='color:#007bff; font-size:18px;'></i>
                          </button>
                          <button class='btn btn-delete' data-id='{$row['id']}'>
                            <i class='bx bx-trash' style='color:#dc3545; font-size:18px;'></i>
                          </button>
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
    </div>
  </main>

  <div class="modal" id="addModal">
    <div class="modal-content">
      <span class="close-btn" id="closeAdd">&times;</span>
      <h3>Tambah Bank</h3>
      <form id="addForm" action="../includes/bank/tambah_bank.php" method="POST">
        <label>Nama Bank</label>
        <input type="text" name="nama_bank" required>
        <label>Atas Nama</label>
        <input type="text" name="atas_nama" required>
        <label>No Rekening</label>
        <input type="text" name="no_rekening" required>
        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="editModal">
    <div class="modal-content">
      <span class="close-btn" id="closeEdit">&times;</span>
      <h3>Edit Bank</h3>
      <form id="editForm" action="../includes/bank/edit_bank_ajax.php" method="POST">
        <input type="hidden" name="id" id="edit_id">
        <label>Nama Bank</label>
        <input type="text" name="nama_bank" id="edit_nama" required>
        <label>Atas Nama</label>
        <input type="text" name="atas_nama" id="edit_atas" required>
        <label>No Rekening</label>
        <input type="text" name="no_rekening" id="edit_rek" required>
        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <h3>Yakin ingin menghapus data ini?</h3>
      <form id="deleteForm" action="../includes/bank/hapus_bank_ajax.php" method="POST">
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
        document.getElementById('edit_atas').value = btn.dataset.atas;
        document.getElementById('edit_rek').value = btn.dataset.rek;
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