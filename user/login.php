<?php

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - ZonaFutsal</title>
    <link rel="stylesheet" href="../assets/css/user/auth.css">
    <script src="https://kit.fontawesome.com/a81368914c.js" crossorigin="anonymous"></script>
</head>

<body>
    <section class="form-section-login">
        <div class="logo-login">
            <img src="../assets/image/logo.png" alt="Logo Sport Club">
        </div>

        <h1>Masuk Akun</h1>

        <form action="" method="POST">
            <div class="form-group-login">
                <input type="tel" name="No tel" placeholder="No Telp" required>
            </div>

            <div class="form-group-login">
                <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
                <i class="fa-regular fa-eye toggle-password" id="togglePassword" style="cursor:pointer;"></i>
            </div>

            <button type="submit">Masuk</button>
        </form>

        <div class="extra-links-login">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
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
