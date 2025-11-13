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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<link rel="stylesheet" href="./assets/css/auth.css?v=<?php echo time(); ?>">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        height: 100vh;
        background: url('./assets/image/bg-futsal.jpg') no-repeat center center;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    /* Lapisan overlay agar teks tetap terbaca */
    body::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 0;
    }

    .form-section-login {
        background: #fff;
        border-radius: 16px;
        padding: 40px 35px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        width: 340px;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .logo-login img {
        width: 90px;
        margin-bottom: 10px;
    }

    h1 {
        font-size: 1.7em;
        color: #111;
        margin-bottom: 20px;
    }

    .form-group-login {
        position: relative;
        margin-bottom: 18px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px 40px 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s;
    }

    input:focus {
        border-color: #00b894;
        box-shadow: 0 0 5px rgba(0,184,148,0.4);
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        cursor: pointer;
    }

    button {
        width: 100%;
        background: #00b894;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: background 0.3s;
    }

    button:hover {
        background: #019875;
    }

    .extra-links-login {
        margin-top: 15px;
        font-size: 0.9em;
    }

    .extra-links-login a {
        color: #00b894;
        font-weight: bold;
        text-decoration: none;
    }

    .extra-links-login a:hover {
        text-decoration: underline;
    }

    .spinner {
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        display: inline-block;
        vertical-align: middle;
        animation: spin 1s linear infinite;
        margin-left: 5px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
</head>
<body>
<section class="form-section-login">
    <div class="logo-login">
        <img src="./assets/image/logo.png" alt="Logo ZonaFutsal">
    </div>

    <h1>Masuk Akun</h1>

    <form id="loginForm" action="" method="POST">
        <div class="form-group-login">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="form-group-login">
            <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
            <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
        </div>

        <button type="submit" id="loginButton">
            <span id="btnText">Login</span>
            <span id="btnLoader" class="spinner" style="display:none;"></span>
        </button>
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

    const loginForm = document.getElementById("loginForm");
    const loginButton = document.getElementById("loginButton");
    const btnText = document.getElementById("btnText");
    const btnLoader = document.getElementById("btnLoader");

    loginForm.addEventListener("submit", function(){
        btnText.style.display = "none";     
        btnLoader.style.display = "inline-block"; 
        loginButton.disabled = true;          
    });
</script>
</body>
</html>
