<?php
session_start();
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $query = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        
        if ($password === $data['password']) {
            
            $_SESSION['admin_id'] = $data['id'];
            $_SESSION['admin_nama'] = $data['name'];
            $_SESSION['admin_username'] = $data['username'];
            $_SESSION['admin_nohp'] = $data['no_hp'];

            header("Location: ./pages/dashboard.php");

            exit;
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - ZonaFutsal</title>
    <link rel="stylesheet" href="./assets/css/login.css?v=<?php echo filemtime('./assets/css/login.css'); ?>">
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