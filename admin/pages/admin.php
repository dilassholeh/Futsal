<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Function untuk generate Admin ID
function generateAdminID($conn)
{
    $result = $conn->query("SELECT id FROM user WHERE id_grup='00001' ORDER BY id DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['id'];
        // Extract number from ID (assuming format: USR001, usr00001, etc)
        preg_match('/\d+/', $lastID, $matches);
        $num = (int)$matches[0] + 1;
        return 'usr' . str_pad($num, 5, '0', STR_PAD_LEFT);
    } else {
        return 'usr00001';
    }
}

// TAMBAH ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    
    // Cek username sudah ada atau belum
    $check = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $id = generateAdminID($conn);
        $id_grup = '00001'; // Grup admin
        
        $query = "INSERT INTO user (id, id_grup, name, username, password, no_hp) 
                  VALUES ('$id', '$id_grup', '$name', '$username', '$hashed_password', '$no_hp')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Admin berhasil ditambahkan!'); window.location='kelola_admin.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan admin!');</script>";
        }
    }
}

// HAPUS ADMIN
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    // Cek jika admin yang akan dihapus adalah admin yang sedang login
    if ($id == $_SESSION['admin_id']) {
        echo "<script>alert('Tidak dapat menghapus akun Anda sendiri!'); window.location='kelola_admin.php';</script>";
        exit;
    }
    
    $delete = mysqli_query($conn, "DELETE FROM user WHERE id='$id' AND id_grup='00001'");
    
    if ($delete) {
        echo "<script>alert('Admin berhasil dihapus!'); window.location='kelola_admin.php';</script>";
    }
}

// EDIT ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $password = $_POST['password'];
    
    // Cek username duplikat (kecuali username sendiri)
    $check = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND id != '$id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        if (!empty($password)) {
            // Update dengan password baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET name='$name', username='$username', password='$hashed_password', no_hp='$no_hp' WHERE id='$id'";
        } else {
            // Update tanpa password
            $query = "UPDATE user SET name='$name', username='$username', no_hp='$no_hp' WHERE id='$id'";
        }
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Data admin berhasil diperbarui!'); window.location='kelola_admin.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data admin!');</script>";
        }
    }
}

// Query admin
$result = mysqli_query($conn, "SELECT * FROM user WHERE id_grup='00001' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin | Zona Futsal</title>
    <link rel="stylesheet" href="../assets/css/lapangan.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .admin-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        
        .admin-info {
            flex: 1;
        }
        
        .admin-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .admin-meta {
            color: #666;
            font-size: 14px;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        
        .current-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle input {
            padding-right: 40px;
        }
        
        .password-toggle i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
    </style>
</head>

<body>
    <main class="main">
        <div class="header">
            <div class="header-left">
                <h1>Kelola Admin</h1>
            </div>
            <div class="header-right">
                <div class="notif"><i class='bx bxs-bell'></i></div>
                <div class="profile">
                    <img src="../assets/image/<?= $_SESSION['admin_foto'] ?? 'profil.png'; ?>" 
                         alt="Profile" 
                         style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <span><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
                </div>
            </div>
        </div>

        <div class="latar">
            <div class="table-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari admin...">
                    <i class='bx bx-search'></i>
                </div>
                <button class="btn-tambah" id="openModal">
                    <i class='bx bx-plus'></i> Tambah Admin
                </button>
            </div>

            <!-- Admin Cards -->
            <div id="adminList">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $initial = strtoupper(substr($row['name'], 0, 1));
                        $isCurrent = ($row['id'] == $_SESSION['admin_id']);
                        ?>
                        <div class="admin-card searchable">
                            <div class="admin-avatar"><?= $initial; ?></div>
                            <div class="admin-info">
                                <div class="admin-name">
                                    <?= htmlspecialchars($row['name']); ?>
                                    <?php if ($isCurrent): ?>
                                        <span class="current-badge">Anda</span>
                                    <?php endif; ?>
                                </div>
                                <div class="admin-meta">
                                    <i class='bx bx-user'></i> <?= htmlspecialchars($row['username']); ?> | 
                                    <i class='bx bx-phone'></i> <?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                                </div>
                            </div>
                            <div class="admin-actions">
                                <button class="edit-btn" 
                                        data-id="<?= $row['id']; ?>"
                                        data-name="<?= htmlspecialchars($row['name']); ?>"
                                        data-username="<?= htmlspecialchars($row['username']); ?>"
                                        data-nohp="<?= htmlspecialchars($row['no_hp'] ?? ''); ?>">
                                    <i class='bx bx-edit' style='color:blue; font-size:20px;'></i>
                                </button>
                                <?php if (!$isCurrent): ?>
                                <a href="kelola_admin.php?hapus=<?= $row['id']; ?>" 
                                   onclick="return confirm('Yakin ingin hapus admin ini?');">
                                    <i class='bx bx-trash' style='color:red; font-size:20px;'></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p style='text-align:center; padding:40px; color:#999;'>Belum ada data admin.</p>";
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Modal Tambah -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h3>Tambah Admin</h3>
            <form method="POST">
                <input type="hidden" name="tambah" value="1">
                
                <label>Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama lengkap admin">

                <label>Username</label>
                <input type="text" name="username" required placeholder="Username untuk login">

                <label>Password</label>
                <div class="password-toggle">
                    <input type="password" name="password" id="password-add" required placeholder="Minimal 6 karakter">
                    <i class='bx bx-hide' id="toggle-password-add"></i>
                </div>

                <label>No. HP</label>
                <input type="text" name="no_hp" placeholder="08xxxxxxxxxx">

                <button type="submit"><i class='bx bx-save'></i> Tambah Admin</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h3>Edit Admin</h3>
            <form method="POST">
                <input type="hidden" name="edit" value="1">
                <input type="hidden" name="id" id="edit-id">

                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required>

                <label>Username</label>
                <input type="text" name="username" id="edit-username" required>

                <label>Password (Kosongkan jika tidak diubah)</label>
                <div class="password-toggle">
                    <input type="password" name="password" id="password-edit" placeholder="Kosongkan jika tidak diubah">
                    <i class='bx bx-hide' id="toggle-password-edit"></i>
                </div>

                <label>No. HP</label>
                <input type="text" name="no_hp" id="edit-nohp">

                <button type="submit"><i class='bx bx-save'></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase();
            const cards = document.querySelectorAll('.searchable');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(keyword) ? '' : 'none';
            });
        });

        // Modal controls
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

        // Edit button
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                editModal.classList.add('active');
                document.getElementById('edit-id').value = btn.dataset.id;
                document.getElementById('edit-name').value = btn.dataset.name;
                document.getElementById('edit-username').value = btn.dataset.username;
                document.getElementById('edit-nohp').value = btn.dataset.nohp;
            });
        });

        // Password toggle for add modal
        const togglePasswordAdd = document.getElementById('toggle-password-add');
        const passwordAdd = document.getElementById('password-add');
        
        togglePasswordAdd.addEventListener('click', function() {
            const type = passwordAdd.type === 'password' ? 'text' : 'password';
            passwordAdd.type = type;
            this.classList.toggle('bx-hide');
            this.classList.toggle('bx-show');
        });

        // Password toggle for edit modal
        const togglePasswordEdit = document.getElementById('toggle-password-edit');
        const passwordEdit = document.getElementById('password-edit');
        
        togglePasswordEdit.addEventListener('click', function() {
            const type = passwordEdit.type === 'password' ? 'text' : 'password';
            passwordEdit.type = type;
            this.classList.toggle('bx-hide');
            this.classList.toggle('bx-show');
        });
    </script>
</body>

</html>