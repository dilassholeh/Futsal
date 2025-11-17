<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
  $nama_event = $_POST['nama_event'];
  $deskripsi = $_POST['deskripsi'];
  $kategori_id = $_POST['kategori_id'];
  $tanggal_mulai = $_POST['tanggal_mulai'];
  $tanggal_berakhir = $_POST['tanggal_berakhir'];

  $foto = $_FILES['foto']['name'];
  $tmp = $_FILES['foto']['tmp_name'];
  $folder = '../../uploads/event/' . $foto;

  if (move_uploaded_file($tmp, $folder)) {
    $id = 'EV' . substr(uniqid(), -7);
    $query = "INSERT INTO event (id, nama_event, deskripsi, kategori_id, foto, tanggal_mulai, tanggal_berakhir)
                  VALUES ('$id', '$nama_event', '$deskripsi', '$kategori_id', '$foto', '$tanggal_mulai', '$tanggal_berakhir')";
    if (mysqli_query($conn, $query)) {
      header("Location: event.php?status=added");
      exit;
    } else {
      header("Location: event.php?status=error");
      exit;
    }
  }
}

if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];

  $query = "SELECT foto FROM event WHERE id = '$id'";
  $result = mysqli_query($conn, $query);
  if ($row = mysqli_fetch_assoc($result)) {
    $foto = $row['foto'];
    // Delete image file if exists
    if (!empty($foto)) {
      $file_path = '../../uploads/event/' . $foto;
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }
  }

  if (mysqli_query($conn, "DELETE FROM event WHERE id='$id'")) {
    header("Location: event.php?status=deleted");
    exit;
  } else {
    header("Location: event.php?status=error");
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
  $id = $_POST['id'];
  $nama_event = $_POST['nama_event'];
  $deskripsi = $_POST['deskripsi'];
  $kategori_id = $_POST['kategori_id'];
  $tanggal_mulai = $_POST['tanggal_mulai'];
  $tanggal_berakhir = $_POST['tanggal_berakhir'];

  if ($_FILES['foto']['name'] == "") {
    $query = "UPDATE event SET 
                  nama_event='$nama_event', 
                  deskripsi='$deskripsi', 
                  kategori_id='$kategori_id',
                  tanggal_mulai='$tanggal_mulai',
                  tanggal_berakhir='$tanggal_berakhir'
                  WHERE id='$id'";
  } else {
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $folder = '../../uploads/event/' . $foto;
    move_uploaded_file($tmp, $folder);
    $query = "UPDATE event SET 
                  nama_event='$nama_event', 
                  deskripsi='$deskripsi', 
                  kategori_id='$kategori_id', 
                  foto='$foto',
                  tanggal_mulai='$tanggal_mulai',
                  tanggal_berakhir='$tanggal_berakhir'
                  WHERE id='$id'";
  }

  if (mysqli_query($conn, $query)) {
    header("Location: event.php?status=updated");
    exit;
  } else {
    header("Location: event.php?status=error");
    exit;
  }
}
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
      <h1>Data Event</h1>
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
                  <th>Tanggal Mulai</th>
                  <th>Tanggal Berakhir</th>
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
                      echo "<img src='../../uploads/event/{$row['foto']}' width='70' height='50' style='object-fit:cover; border-radius:6px;'>";
                    } else {
                      echo "<em>-</em>";
                    }
                    echo "</td>
                        <td>" . date('d/m/Y', strtotime($row['tanggal_mulai'])) . "</td>
                        <td>" . date('d/m/Y', strtotime($row['tanggal_berakhir'])) . "</td>
                        <td>
                            <a href='#' class='edit-btn' 
                               data-id='{$row['id']}'
                               data-nama='{$row['nama_event']}'
                               data-kategori='{$row['kategori_id']}'
                               data-deskripsi='" . htmlspecialchars($row['deskripsi'], ENT_QUOTES) . "'
                               data-tanggalmulai='{$row['tanggal_mulai']}'
                               data-tanggalberakhir='{$row['tanggal_berakhir']}'>
                               <i class='bx bx-edit' style='color:blue; font-size:20px;'></i>
                            </a>
                            <a href='event.php?hapus={$row['id']}' onclick=\"return confirm('Yakin ingin hapus event ini?');\">
                               <i class='bx bx-trash' style='color:red; font-size:20px;'></i>
                            </a>
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
      </div>
    </div>
  </main>

  <div class="modal" id="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModal">&times;</span>
      <h3>Tambah Event</h3>
      <form class="modal-form-custom" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="tambah" value="1">

        <div class="nama">
          <label>Nama Event</label>
          <input type="text" name="nama_event" required>
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
          <textarea name="deskripsi" rows="3" required></textarea>
        </div>

        <div class="gambar">
          <label>Foto Event</label>
          <input type="file" name="foto" accept="image/*" required>
        </div>

        <div class="tanggal">
          <div>
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" required>
          </div>
          <div>
            <label>Tanggal Berakhir</label>
            <input type="date" name="tanggal_berakhir" required>
          </div>
        </div>

        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Tambah</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="editModal">
    <div class="modal-content">
      <span class="close-btn" id="closeEdit">&times;</span>
      <h3>Edit Event</h3>
      <form class="modal-form-custom" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit" value="1">
        <input type="hidden" name="id" id="edit-id">

        <div class="nama">
          <label>Nama Event</label>
          <input type="text" name="nama_event" id="edit-nama" required>
        </div>

        <div class="kategori">
          <label>Kategori</label>
          <select name="kategori_id" id="edit-kategori" required>
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
          <textarea name="deskripsi" id="edit-deskripsi" rows="3" required></textarea>
        </div>

        <div class="gambar">
          <label>Foto (Kosongkan jika tidak diubah)</label>
          <input type="file" name="foto" accept="image/*">
        </div>

        <div class="tanggal">
          <div>
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" id="edit-tanggal-mulai" required>
          </div>
          <div>
            <label>Tanggal Berakhir</label>
            <input type="date" name="tanggal_berakhir" id="edit-tanggal-berakhir" required>
          </div>
        </div>

        <div class="submit">
          <button type="submit"><i class='bx bx-save'></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>


  <script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
      const keyword = this.value.toLowerCase();
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? '' : 'none';
      });
    });

    const openModal = document.getElementById('openModal');
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');
    const editModal = document.getElementById('editModal');
    const closeEdit = document.getElementById('closeEdit');

    openModal.onclick = () => modal.classList.add('active');
    closeModal.onclick = () => modal.classList.remove('active');
    closeEdit.onclick = () => editModal.classList.remove('active');

    window.onclick = (e) => {
      if (e.target === modal) modal.classList.remove('active');
      if (e.target === editModal) editModal.classList.remove('active');
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        editModal.classList.add('active');
        document.getElementById('edit-id').value = btn.dataset.id;
        document.getElementById('edit-nama').value = btn.dataset.nama;
        document.getElementById('edit-kategori').value = btn.dataset.kategori;
        document.getElementById('edit-deskripsi').value = btn.dataset.deskripsi;
        document.getElementById('edit-tanggal-mulai').value = btn.dataset.tanggalmulai;
        document.getElementById('edit-tanggal-berakhir').value = btn.dataset.tanggalberakhir;
      });
    });

    function validateDates(e) {
      const form = e.target.closest('form');
      const startDate = form.querySelector('[name="tanggal_mulai"]').value;
      const endDate = form.querySelector('[name="tanggal_berakhir"]').value;

      if (startDate > endDate) {
        alert('Tanggal mulai tidak boleh lebih akhir dari tanggal berakhir!');
        return false;
      }
      return true;
    }

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    if (status) {
      let message = '';
      switch (status) {
        case 'added':
          message = 'Event berhasil ditambahkan!';
          break;
        case 'updated':
          message = 'Event berhasil diperbarui!';
          break;
        case 'deleted':
          message = 'Event berhasil dihapus!';
          break;
        case 'error':
          message = 'Terjadi kesalahan! Silakan coba lagi.';
          break;
      }
      if (message) {
        alert(message);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    }
  </script>
</body>

</html>