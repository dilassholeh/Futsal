<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}

// Handle Insert
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
        // Generate a shorter ID that fits varchar(10)
        $id = 'EV' . substr(uniqid(), -7);
        $query = "INSERT INTO event (id, nama_event, deskripsi, kategori_id, foto, tanggal_mulai, tanggal_berakhir)
                  VALUES ('$id', '$nama_event', '$deskripsi', '$kategori_id', '$foto', '$tanggal_mulai', '$tanggal_berakhir')";
        if(mysqli_query($conn, $query)) {
            header("Location: event.php?status=added");
            exit;
        } else {
            header("Location: event.php?status=error");
            exit;
        }
    }
}

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Get image filename before deleting record
    $query = "SELECT foto FROM event WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    if($row = mysqli_fetch_assoc($result)) {
        $foto = $row['foto'];
        // Delete image file if exists
        if(!empty($foto)) {
            $file_path = '../../uploads/event/' . $foto;
            if(file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    // Delete database record
    if(mysqli_query($conn, "DELETE FROM event WHERE id='$id'")) {
        header("Location: event.php?status=deleted");
        exit;
    } else {
        header("Location: event.php?status=error");
        exit;
    }
}

// Handle Update
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

    if(mysqli_query($conn, $query)) {
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
      <div class="header-left">
        <h1>Dashboard</h1>
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
  </main>

  <!-- POPUP TAMBAH EVENT -->
  <!-- Modal Tambah -->
  <div class="modal" id="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModal">&times;</span>
      <h3>Tambah Event</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="tambah" value="1">
        
        <label>Nama Event</label>
        <input type="text" name="nama_event" required>

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

        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="3" required></textarea>

        <label>Foto Event</label>
        <input type="file" name="foto" accept="image/*" required>

        <label>Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" required>

        <label>Tanggal Berakhir</label>
        <input type="date" name="tanggal_berakhir" required>

        <script>
            // Set min date for date inputs to today
            const todayAdd = new Date().toISOString().split('T')[0];
            const startDateAdd = document.querySelector('#modal [name="tanggal_mulai"]');
            const endDateAdd = document.querySelector('#modal [name="tanggal_berakhir"]');
            
            startDateAdd.min = todayAdd;
            endDateAdd.min = todayAdd;

            // Ensure end date is not before start date
            startDateAdd.addEventListener('change', function() {
                endDateAdd.min = this.value;
                if (endDateAdd.value && endDateAdd.value < this.value) {
                    endDateAdd.value = this.value;
                }
            });

            // Ensure start date is not after end date
            endDateAdd.addEventListener('change', function() {
                if (startDateAdd.value && this.value < startDateAdd.value) {
                    alert('Tanggal berakhir tidak boleh lebih awal dari tanggal mulai!');
                    this.value = startDateAdd.value;
                }
            });
        </script>

        <button type="submit" onclick="return validateDates(event)"><i class='bx bx-save'></i> Tambah</button>
      </form>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <span class="close-btn" id="closeEdit">&times;</span>
      <h3>Edit Event</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit" value="1">
        <input type="hidden" name="id" id="edit-id">

        <label>Nama Event</label>
        <input type="text" name="nama_event" id="edit-nama" required>

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

        <label>Deskripsi</label>
        <textarea name="deskripsi" id="edit-deskripsi" rows="3" required></textarea>

        <label>Foto (Kosongkan jika tidak diubah)</label>
        <input type="file" name="foto" accept="image/*">

        <label>Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" id="edit-tanggal-mulai" required>

        <label>Tanggal Berakhir</label>
        <input type="date" name="tanggal_berakhir" id="edit-tanggal-berakhir" required>

        <script>
            const startDateEdit = document.querySelector('#editModal [name="tanggal_mulai"]');
            const endDateEdit = document.querySelector('#editModal [name="tanggal_berakhir"]');

            // Ensure end date is not before start date
            startDateEdit.addEventListener('change', function() {
                endDateEdit.min = this.value;
                if (endDateEdit.value && endDateEdit.value < this.value) {
                    endDateEdit.value = this.value;
                }
            });

            // Ensure start date is not after end date
            endDateEdit.addEventListener('change', function() {
                if (startDateEdit.value && this.value < startDateEdit.value) {
                    alert('Tanggal berakhir tidak boleh lebih awal dari tanggal mulai!');
                    this.value = startDateEdit.value;
                }
            });
        </script>

        <button type="submit" onclick="return validateDates(event)"><i class='bx bx-save'></i> Simpan</button>
      </form>
    </div>
  </div>

  <script>
    // Search functionality
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

    // Edit Modal Functionality
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

    // Date validation function
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

    // Handle status messages
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    if (status) {
        let message = '';
        switch(status) {
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
            // Remove status from URL without refreshing
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
  </script>
</body>

</html>