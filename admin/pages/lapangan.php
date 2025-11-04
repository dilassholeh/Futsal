<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fungsi untuk generate ID lapangan
function generateLapanganID($conn) {
    $result = $conn->query("SELECT id FROM lapangan ORDER BY id DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['id'];
        $num = (int)substr($lastID, 2) + 1;
        return 'LP' . str_pad($num, 5, '0', STR_PAD_LEFT);
    } else {
        return 'LP00001';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama_lapangan = $_POST['nama_lapangan'];
    $harga_pagi = $_POST['harga_pagi'];
    $harga_malam = $_POST['harga_malam'];

    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    // Pastikan folder uploads/lapangan ada
    $upload_dir = '../../uploads/lapangan/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate nama file unik untuk menghindari konflik
    $file_ext = pathinfo($foto, PATHINFO_EXTENSION);
    $new_filename = uniqid('lapangan_') . '.' . $file_ext;
    $folder = $upload_dir . $new_filename;

    if (move_uploaded_file($tmp, $folder)) {
        $id = generateLapanganID($conn);
        $query = "INSERT INTO lapangan (id, nama_lapangan, harga_pagi, harga_malam, foto)
                  VALUES ('$id', '$nama_lapangan', '$harga_pagi', '$harga_malam', '$new_filename')";
        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Lapangan berhasil ditambahkan!'); window.location='lapangan.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan lapangan!'); window.location='lapangan.php';</script>";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Ambil nama foto sebelum dihapus
    $getFoto = mysqli_query($conn, "SELECT foto FROM lapangan WHERE id='$id'");
    if($row = mysqli_fetch_assoc($getFoto)) {
        $foto_path = '../../uploads/lapangan/' . $row['foto'];
        if(file_exists($foto_path)) {
            unlink($foto_path); // Hapus file foto
        }
    }
    
    mysqli_query($conn, "DELETE FROM lapangan WHERE id='$id'");
    echo "<script>alert('Data lapangan berhasil dihapus!'); window.location='lapangan.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama_lapangan = $_POST['nama_lapangan'];
    $harga_pagi = $_POST['harga_pagi'];
    $harga_malam = $_POST['harga_malam'];

    if ($_FILES['foto']['name'] == "") {
        $query = "UPDATE lapangan SET 
                  nama_lapangan='$nama_lapangan', 
                  harga_pagi='$harga_pagi', 
                  harga_malam='$harga_malam'
                  WHERE id='$id'";
    } else {
        // Hapus foto lama
        $getFoto = mysqli_query($conn, "SELECT foto FROM lapangan WHERE id='$id'");
        if($row = mysqli_fetch_assoc($getFoto)) {
            $old_foto = '../../uploads/lapangan/' . $row['foto'];
            if(file_exists($old_foto)) {
                unlink($old_foto);
            }
        }
        
        // Upload foto baru
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $file_ext = pathinfo($foto, PATHINFO_EXTENSION);
        $new_filename = uniqid('lapangan_') . '.' . $file_ext;
        $folder = '../../uploads/lapangan/' . $new_filename;
        
        move_uploaded_file($tmp, $folder);
        
        $query = "UPDATE lapangan SET 
                  nama_lapangan='$nama_lapangan', 
                  harga_pagi='$harga_pagi', 
                  harga_malam='$harga_malam', 
                  foto='$new_filename'
                  WHERE id='$id'";
    }

    mysqli_query($conn, $query);
    echo "<script>alert('Data lapangan berhasil diperbarui!'); window.location='lapangan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapangan | Zona Futsal</title>
    <link rel="stylesheet" href="../assets/css/lapangan.css?v=<?php echo filemtime('../assets/css/lapangan.css'); ?>">
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
                            <th>Nama Lapangan</th>
                            <th>Harga Pagi</th>
                            <th>Harga Malam</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id DESC");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nama_lapangan']}</td>
                            <td>Rp " . number_format($row['harga_pagi'], 0, ',', '.') . "</td>
                            <td>Rp " . number_format($row['harga_malam'], 0, ',', '.') . "</td>
                            <td><img src='../../uploads/lapangan/{$row['foto']}' width='70' height='50' style='object-fit:cover; border-radius:6px;' onerror=\"this.src='../../uploads/no-image.png'\"></td>
                            <td>
                                <a href='#' class='edit-btn' 
                                   data-id='{$row['id']}' 
                                   data-nama='{$row['nama_lapangan']}'
                                   data-pagi='{$row['harga_pagi']}'
                                   data-malam='{$row['harga_malam']}'
                                   data-foto='{$row['foto']}'>
                                   <i class='bx bx-edit' style='color:blue; font-size:20px;'></i>
                                </a>
                                <a href='lapangan.php?hapus={$row['id']}' onclick=\"return confirm('Yakin ingin hapus data ini?');\">
                                   <i class='bx bx-trash' style='color:red; font-size:20px;'></i>
                                </a>
                            </td>
                        </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Belum ada data lapangan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal" id="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h3>Tambah Lapangan</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tambah" value="1">
                <label>Nama Lapangan</label>
                <input type="text" name="nama_lapangan" required>

                <label>Harga Pagi</label>
                <input type="number" name="harga_pagi" required>

                <label>Harga Malam</label>
                <input type="number" name="harga_malam" required>

                <label>Foto</label>
                <input type="file" name="foto" accept="image/*" required>

                <button type="submit"><i class='bx bx-save'></i> Tambah</button>
            </form>
        </div>
    </div>

    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h3>Edit Lapangan</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit" value="1">
                <input type="hidden" name="id" id="edit-id">

                <label>Nama Lapangan</label>
                <input type="text" name="nama_lapangan" id="edit-nama" required>

                <label>Harga Pagi</label>
                <input type="number" name="harga_pagi" id="edit-pagi" required>

                <label>Harga Malam</label>
                <input type="number" name="harga_malam" id="edit-malam" required>

                <label>Foto (Kosongkan jika tidak diubah)</label>
                <input type="file" name="foto" accept="image/*">

                <button type="submit"><i class='bx bx-save'></i> Simpan</button>
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
                document.getElementById('edit-pagi').value = btn.dataset.pagi;
                document.getElementById('edit-malam').value = btn.dataset.malam;
            });
        });
    </script>

</body>

</html> 