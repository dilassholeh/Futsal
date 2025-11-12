<?php
session_start();
include '../includes/koneksi.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['name'] ?? '';
            $_SESSION['id_grup'] = $user['id_grup'] ?? '';

            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.history.back();</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - ZonaFutsal</title>
    <link rel="stylesheet" href="./assets/css/auth.css?v=<?php echo filemtime('./assets/css/auth.css'); ?>">
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
</head>

<body>
    <section class="form-section-login">
        <div class="logo-login">
            <img src="./assets/image/logo.png" alt="Logo Sport Club">
        </div>

        <h1>Masuk Akun</h1>

        <form action="" method="POST">
            <div class="form-group-login">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group-login">
                <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
                <i class="fa-regular fa-eye toggle-password" id="togglePassword" style="cursor:pointer;"></i>
            </div>

            <button type="submit">Masuk</button>
        </form>

        <div class="extra-links-login">
            <p>Belum punya akun? <a href="../user/register.php">Daftar di sini</a></p>
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

</html>
