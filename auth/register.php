<?php

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - ZonaFutsal</title>
    <link rel="stylesheet" href="../assets/css/user/auth.css">
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
</head>
<body>
    <section class="form-section-register">
        <div class="logo-register">
            <img src="../assets/image/logo.png" alt="Logo Sport Club">
        </div>

        <h1>Daftar Akun</h1>

        <form action="" method="POST">
            <div class="form-group-register">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
            </div>

            <div class="form-group-register">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group-register">
                <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
                <i class="fa-regular fa-eye toggle-password" id="togglePassword" style="cursor:pointer;"></i>
            </div>

            <button type="submit">Daftar</button>
        </form>

        <a href="#" class="google-btn-register">
            <i class="fa-brands fa-google"></i> Daftar dengan Google
        </a>

        <div class="extra-links-register">
            <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
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
