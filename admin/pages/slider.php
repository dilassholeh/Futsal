<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fungsi buat ID otomatis SR01, SR02, dst
function generateSliderID($conn) {
    $result = $conn->query("SELECT id FROM slider ORDER BY id DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['id'];
        $num = (int)substr($lastID, 2) + 1;
        return 'SR' . str_pad($num, 2, '0', STR_PAD_LEFT);
    } else {
        return 'SR01';
    }
}

// Tambah Slider
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama_slider = $_POST['nama_slider'];
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $folder = '../../uploads/slider/' . $foto;

    // Pastikan folder uploads/slider ada
    if (!file_exists('../../uploads/slider')) {
        mkdir('../../uploads/slider', 0777, true);
    }

    // Pindahkan file
    if (move_uploaded_file($tmp, $folder)) {
        $id = generateSliderID($conn);
        $query = "INSERT INTO slider (id, nama_slider, foto) VALUES ('$id', '$nama_slider', '$foto')";
        if ($conn->query($query)) {
            echo "<script>alert('Slider berhasil ditambahkan!'); window.location='slider.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan slider: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload foto!');</script>";
    }
}

// Hapus Slider
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $getFoto = $conn->query("SELECT foto FROM slider WHERE id='$id'");
    if ($getFoto->num_rows > 0) {
        $fotoData = $getFoto->fetch_assoc();
        $fotoPath = '../../uploads/slider/' . $fotoData['foto'];
        if (file_exists($fotoPath)) {
            unlink($fotoPath);
        }
    }

    $conn->query("DELETE FROM slider WHERE id='$id'");
    echo "<script>alert('Slider berhasil dihapus!'); window.location='slider.php';</script>";
}

// Edit Slider
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama_slider = $_POST['nama_slider'];

    if ($_FILES['foto']['name'] == "") {
        // Jika tidak ubah foto
        $query = "UPDATE slider SET nama_slider='$nama_slider' WHERE id='$id'";
    } else {
        // Jika ubah foto
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $folder = '../../uploads/slider/' . $foto;
        move_uploaded_file($tmp, $folder);
        $query = "UPDATE slider SET nama_slider='$nama_slider', foto='$foto' WHERE id='$id'";
    }

    if ($conn->query($query)) {
        echo "<script>alert('Data slider berhasil diperbarui!'); window.location='slider.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui slider: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider | Zona Futsal</title>
    <link rel="stylesheet" href="../assets/css/slider.css?v=<?php echo filemtime('../assets/css/slider.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <main class="main">
        <div class="header">
            <div class="header-left">
                <h1>Slider</h1>
            </div>
            <div class="header-right">
                <div class="notif"><i class='bx bxs-bell'></i></div>
                <div class="profile">
                    <img src="../../uploads/<?php echo $_SESSION['admin_foto'] ?? 'profil.png'; ?>"
                        alt="Profile"
                        style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <span><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
                    <a href="../logout.php" title="Keluar" style="margin-left:10px; color:#f00;">
                        <i class='bx bx-log-out'></i>
                    </a>
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
                            <th>Nama Slider</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM slider ORDER BY id DESC");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "
                                <tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['nama_slider']}</td>
                                    <td><img src='../../uploads/slider/{$row['foto']}' width='100' style='border-radius:5px; object-fit:cover;'></td>
                                    <td>
                                        <a href='#' class='edit-btn' 
                                           data-id='{$row['id']}' 
                                           data-nama='{$row['nama_slider']}'>
                                           <i class='bx bx-edit' style='color:blue; font-size:20px;'></i>
                                        </a>
                                        <a href='slider.php?hapus={$row['id']}' onclick=\"return confirm('Yakin ingin hapus data ini?');\">
                                           <i class='bx bx-trash' style='color:red; font-size:20px;'></i>
                                        </a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Belum ada data slider.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h3>Tambah Slider</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tambah" value="1">

                <label>Nama Slider</label>
                <input type="text" name="nama_slider" required>

                <label>Foto</label>
                <input type="file" name="foto" accept="image/*" required>

                <button type="submit"><i class='bx bx-save'></i> Tambah</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h3>Edit Slider</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit" value="1">
                <input type="hidden" name="id" id="edit-id">

                <label>Nama Slider</label>
                <input type="text" name="nama_slider" id="edit-nama" required>

                <label>Foto (Kosongkan jika tidak diubah)</label>
                <input type="file" name="foto" accept="image/*">

                <button type="submit"><i class='bx bx-save'></i> Simpan</button>
            </form>
        </div>
    </div>

    <script>
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
            });
        });
    </script>
</body>
</html>
