<?php
include '../../includes/koneksi.php';
include 'sidebar.php';

// ====== Tambah Data ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama_slider = $_POST['nama_slider'];
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $folder = '../../uploads/slider/' . $foto;

    // Buat folder kalau belum ada
    if (!file_exists('../../uploads/slider')) {
        mkdir('../../uploads/slider', 0777, true);
    }

    if (move_uploaded_file($tmp, $folder)) {
        $id = uniqid('SL');
        $query = "INSERT INTO slider (id, nama_slider, foto) VALUES ('$id', '$nama_slider', '$foto')";
        mysqli_query($conn, $query);
        echo "<script>alert('Slider berhasil ditambahkan!'); window.location='slider.php';</script>";
    }
}

// ====== Hapus Data ======
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM slider WHERE id='$id'");
    echo "<script>alert('Data slider berhasil dihapus!'); window.location='slider.php';</script>";
}

// ====== Edit Data ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama_slider = $_POST['nama_slider'];

    if ($_FILES['foto']['name'] == "") {
        $query = "UPDATE slider SET nama_slider='$nama_slider' WHERE id='$id'";
    } else {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $folder = '../../uploads/slider/' . $foto;
        move_uploaded_file($tmp, $folder);
        $query = "UPDATE slider SET nama_slider='$nama_slider', foto='$foto' WHERE id='$id'";
    }

    mysqli_query($conn, $query);
    echo "<script>alert('Data slider berhasil diperbarui!'); window.location='slider.php';</script>";
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
                <h1>Data Slider</h1>
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
                            <td><img src='../uploads/{$row['foto']}' width='80'></td>
                            <td>
                                <a href='#' class='edit-btn' 
                                   data-id='{$row['id']}' 
                                   data-nama='{$row['nama_slider']}'
                                   data-foto='{$row['foto']}'>
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