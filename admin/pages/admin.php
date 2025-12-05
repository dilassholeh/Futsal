<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$admin_login_id = $_SESSION['admin_id'];

$result = mysqli_query($conn, "SELECT * FROM user WHERE id_grup='00001' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin | Zona Futsal</title>
    <link rel="stylesheet" href="../assets/css/lapangan.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
.profile-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 25px;
}

.profile-card2 {
    width: 90%;
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    text-align: center;
}

.profile-photo2 {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ddd;
    margin-bottom: 25px;
}

.profile-row {
    display: flex;
    justify-content: space-between;
    padding: 0 20px;
    margin-top: 10px;
}

.profile-item {
    width: 25%;
    text-align: center;
}

.label {
    font-weight: 600;
    font-size: 15px;
    color: #666;
}

.value {
    margin-top: 6px;
    font-size: 17px;
    color: #333;
    font-weight: 500;
}

.btn-change-pass2 {
    background: #007bff;
    border: none;
    padding: 10px 18px;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
}

.btn-change-pass2:hover {
    background: #005ecb;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: none;
    justify-content: center;
    align-items: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    padding: 25px;
    border-radius: 10px;
    width: 350px;
    animation: fadeIn 0.2s ease;
}

.close-btn {
    float: right;
    font-size: 24px;
    cursor: pointer;
}

@keyframes fadeIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

</head>
<body>

<main class="main">

    <div class="header">
        <h1>Kelola Admin</h1>
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

    <div class="profile-wrapper">
        <div class="profile-card2">

            <img src="../assets/image/<?php echo htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" class="profile-photo2">

            <div class="profile-row">

                <div class="profile-item">
                    <div class="label">Username</div>
                    <div class="value"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                </div>

                <div class="profile-item">
                    <div class="label">No HP</div>
                    <div class="value"><?php echo htmlspecialchars($_SESSION['admin_nohp']); ?></div>
                </div>

                <div class="profile-item">
                    <div class="label">Role</div>
                    <div class="value">Admin</div>
                </div>

                <div class="profile-item">
                    <button class="btn-change-pass2" onclick="document.getElementById('modalGantiSandi').classList.add('active')">
                        Ganti Password
                    </button>
                </div>

            </div>

        </div>
    </div>

</main>

<div class="modal" id="modalGantiSandi">
    <div class="modal-content">
        <span class="close-btn" onclick="document.getElementById('modalGantiSandi').classList.remove('active')">&times;</span>
        <h3>Ganti Password</h3>
        <form method="POST" action="proses_ganti_sandi.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($admin_login_id); ?>">

            <label>Password Lama</label>
            <input type="password" name="password_lama" required>

            <label>Password Baru</label>
            <input type="password" name="password_baru" required>

            <label>Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi" required>

            <button type="submit" class="btn-change-pass2" style="width:100%; margin-top:10px;">
                <i class='bx bx-save'></i> Simpan
            </button>
        </form>
    </div>
</div>

</body>
</html>
