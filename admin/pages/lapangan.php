<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

function generateLapanganID($conn)
{
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
    $nama_lapangan = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $harga_pagi = mysqli_real_escape_string($conn, $_POST['harga_pagi']);
    $harga_malam = mysqli_real_escape_string($conn, $_POST['harga_malam']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];

    $upload_dir = '../../uploads/lapangan/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_ext = pathinfo($foto, PATHINFO_EXTENSION);
    $new_filename = uniqid('lapangan_') . '.' . $file_ext;
    $folder = $upload_dir . $new_filename;

    if (move_uploaded_file($tmp, $folder)) {
        $id = generateLapanganID($conn);

        $stmt = $conn->prepare("INSERT INTO lapangan (id, nama_lapangan, harga_pagi, harga_malam, foto, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddss", $id, $nama_lapangan, $harga_pagi, $harga_malam, $new_filename, $status);

        if ($stmt->execute()) {
            echo "<script>alert('Lapangan berhasil ditambahkan! Status: $status'); window.location='lapangan.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan lapangan: " . $stmt->error . "'); window.location='lapangan.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Gagal upload foto!'); window.location='lapangan.php';</script>";
    }
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);

    $getFoto = mysqli_query($conn, "SELECT foto FROM lapangan WHERE id='$id'");
    if ($row = mysqli_fetch_assoc($getFoto)) {
        $foto_path = '../../uploads/lapangan/' . $row['foto'];
        if (file_exists($foto_path)) {
            unlink($foto_path);
        }
    }

    mysqli_query($conn, "DELETE FROM lapangan WHERE id='$id'");
    echo "<script>alert('Data lapangan berhasil dihapus!'); window.location='lapangan.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_lapangan = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $harga_pagi = mysqli_real_escape_string($conn, $_POST['harga_pagi']);
    $harga_malam = mysqli_real_escape_string($conn, $_POST['harga_malam']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($_FILES['foto']['name'] == "") {
        $stmt = $conn->prepare("UPDATE lapangan SET nama_lapangan=?, harga_pagi=?, harga_malam=?, status=? WHERE id=?");
        $stmt->bind_param("sddss", $nama_lapangan, $harga_pagi, $harga_malam, $status, $id);
    } else {
        $getFoto = mysqli_query($conn, "SELECT foto FROM lapangan WHERE id='$id'");
        if ($row = mysqli_fetch_assoc($getFoto)) {
            $old_foto = '../../uploads/lapangan/' . $row['foto'];
            if (file_exists($old_foto)) {
                unlink($old_foto);
            }
        }

        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $file_ext = pathinfo($foto, PATHINFO_EXTENSION);
        $new_filename = uniqid('lapangan_') . '.' . $file_ext;
        $folder = '../../uploads/lapangan/' . $new_filename;

        move_uploaded_file($tmp, $folder);

        $stmt = $conn->prepare("UPDATE lapangan SET nama_lapangan=?, harga_pagi=?, harga_malam=?, foto=?, status=? WHERE id=?");
        $stmt->bind_param("sddsss", $nama_lapangan, $harga_pagi, $harga_malam, $new_filename, $status, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Data lapangan berhasil diperbarui! Status: $status'); window.location='lapangan.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . $stmt->error . "'); window.location='lapangan.php';</script>";
    }
    $stmt->close();
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
    <style>
      /* === Badge Status === */
.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
    letter-spacing: .3px;
}

/* Status warna */
.status-active {
    background: #e6f5e6;
    color: #1a8f1a;
    border: 1px solid #b6e3b6;
}

.status-inactive {
    background: #ffecec;
    color: #d43b3b;
    border: 1px solid #ffb7b7;
}

.status-maintenance {
    background: #fff6e5;
    color: #d88a0d;
    border: 1px solid #ffd79b;
}

/* === Tombol Edit === */
.edit-btn {
    background: #eef5ff;
    padding: 6px 10px;
    border-radius: 8px;
    margin-right: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: 0.2s ease;
}

.edit-btn i {
    font-size: 20px;
    color: #1b6fe3 !important;
}

/* Hover efek */
.edit-btn:hover {
    background: #d4e5ff;
    transform: scale(1.07);
    box-shadow: 0 3px 7px rgba(27, 111, 227, 0.25);
}

/* Tombol hapus */
.delete-btn i {
    color: #e03131 !important;
}

.delete-btn:hover {
    transform: scale(1.07);
}

        
    </style>
</head>

<body>
    <main class="main">
        <div class="header">
            <h1>Data Lapangan</h1>
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
                                    <th>Nama Lapangan</th>
                                    <th>Harga Pagi</th>
                                    <th>Harga Malam</th>
                                    <th>Status</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id DESC");
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // PERBAIKAN: Pastikan status ditampilkan dengan benar
                                        $status = $row['status'] ?? 'tersedia';
                                        $statusClass = 'status-' . $status;
                                        $statusText = ucfirst($status);
                                        if ($status == 'perbaikan') $statusText = 'Sedang Perbaikan';

                                        echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nama_lapangan']}</td>
                            <td>Rp " . number_format($row['harga_pagi'], 0, ',', '.') . "</td>
                            <td>Rp " . number_format($row['harga_malam'], 0, ',', '.') . "</td>
                            <td><span class='status-badge $statusClass'>$statusText</span></td>
                            <td><img src='../../uploads/lapangan/{$row['foto']}' width='70' height='50' style='object-fit:cover; border-radius:6px;' onerror=\"this.src='../../uploads/no-image.png'\"></td>
                            <td>
                                <a href='#' class='edit-btn' 
                                   data-id='{$row['id']}' 
                                   data-nama='{$row['nama_lapangan']}'
                                   data-pagi='{$row['harga_pagi']}'
                                   data-malam='{$row['harga_malam']}'
                                   data-status='$status'
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
                                    echo "<tr><td colspan='7'>Belum ada data lapangan.</td></tr>";
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
            <h3>Tambah Lapangan</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tambah" value="1">
                <label>Nama Lapangan</label>
                <input type="text" name="nama_lapangan" required>

                <label>Harga Pagi</label>
                <input type="number" name="harga_pagi" required>

                <label>Harga Malam</label>
                <input type="number" name="harga_malam" required>

                <label>Status Lapangan</label>
                <select name="status" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="rusak">Rusak</option>
                    <option value="perbaikan">Sedang Perbaikan</option>
                </select>

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

                <label>Status Lapangan</label>
                <select name="status" id="edit-status" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="rusak">Rusak</option>
                    <option value="perbaikan">Sedang Perbaikan</option>
                </select>

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
                document.getElementById('edit-status').value = btn.dataset.status;

                console.log('Status yang akan di-edit:', btn.dataset.status);
            });
        });
    </script>

</body>

</html>