<?php
include __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $no_hp = mysqli_real_escape_string($conn, $_POST['tel'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (empty($nama) || empty($username) || empty($no_hp) || empty($password)) {
        echo "<script>alert('Semua field harus diisi!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah username sudah digunakan
    $cek_user = mysqli_query($conn, "SELECT id FROM user WHERE username = '$username' LIMIT 1");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>
                alert('Username sudah digunakan! Silakan pilih username lain.');
                window.history.back();
              </script>";
        exit;
    }

    // Buat ID unik (USR001, USR002, dst)
    $result_last = mysqli_query($conn, "SELECT id FROM user WHERE id LIKE 'USR%' ORDER BY id DESC LIMIT 1");
    if ($result_last && mysqli_num_rows($result_last) > 0) {
        $last_id = mysqli_fetch_assoc($result_last)['id'];
        $num = intval(substr($last_id, 3)) + 1;
        $id = 'USR' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $id = 'USR001';
    }

    // Grup default: user biasa
    $id_grup = '00002';

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke database
    $query = "INSERT INTO user (id, id_grup, name, username, password, no_hp)
              VALUES ('$id', '$id_grup', '$nama', '$username', '$hashed_password', '$no_hp')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Pendaftaran berhasil! Silakan login.');
                window.location.href = './login.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Pendaftaran gagal: " . addslashes(mysqli_error($conn)) . "');
                window.history.back();
              </script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - ZonaFutsal</title>
    <link rel="stylesheet" href="./assets/css/auth.css?v=<?php echo time(); ?>">
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
</head>
<body>
    <section class="form-section-register">
        <div class="logo-register">
            <img src="./assets/image/logo_orange.png" alt="Logo Sport Club">
        </div>

        <h1>Daftar Akun</h1>

        <form action="" method="POST">
            <div class="form-group-register">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
            </div>

            <div class="form-group-register">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group-register">
                <input type="tel" name="tel" placeholder="No Telp" required>
            </div>

            <div class="form-group-register">
                <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
                <i class="fa-regular fa-eye toggle-password" id="togglePassword" style="cursor:pointer;"></i>
            </div>

            <button type="submit">Daftar</button>
        </form>

        <div class="extra-links-register">
            <p>Sudah punya akun? <a href="./login.php">Masuk di sini</a></p>
        </div>
    </section>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");
        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>
