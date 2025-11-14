<?php
include __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $no_hp = mysqli_real_escape_string($conn, $_POST['tel'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($username) || empty($email) || empty($no_hp) || empty($password)) {
        echo "<script>alert('Semua field harus diisi!'); window.history.back();</script>";
        exit;
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        echo "<script>alert('Nama hanya boleh berisi huruf dan spasi!'); window.history.back();</script>";
        exit;
    }

    if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        echo "<script>alert('Username hanya boleh berisi huruf, angka, dan garis bawah!'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!'); window.history.back();</script>";
        exit;
    }

    if (!preg_match("/^[0-9]{10,15}$/", $no_hp)) {
        echo "<script>alert('Nomor HP harus berisi 10–15 angka!'); window.history.back();</script>";
        exit;
    }

    $cek_user = mysqli_query($conn, "SELECT id FROM user WHERE username = '$username' OR email = '$email' LIMIT 1");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username atau Email sudah digunakan!'); window.history.back();</script>";
        exit;
    }

    $result_last = mysqli_query($conn, "SELECT id FROM user WHERE id LIKE 'USR%' ORDER BY id DESC LIMIT 1");
    if ($result_last && mysqli_num_rows($result_last) > 0) {
        $last_id = mysqli_fetch_assoc($result_last)['id'];
        $num = intval(substr($last_id, 3)) + 1;
        $id = 'USR' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $id = 'USR001';
    }

    $id_grup = '00002';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO user (id, id_grup, name, username, email, password, no_hp)
              VALUES ('$id', '$id_grup', '$nama', '$username', '$email', '$hashed_password', '$no_hp')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location.href = './login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Pendaftaran gagal: " . addslashes(mysqli_error($conn)) . "'); window.history.back();</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background: url('../user/assets/image/latarlogin.png') no-repeat center center;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 0;
        }

        .register-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .form-section-register {
            background: #fff;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 360px;
            text-align: center;
            position: relative;
        }

        .logo-register img {
            width: 90px;
            margin-bottom: 15px;
        }

        h1 {
            font-size: 1.7em;
            color: #111;
            margin-bottom: 20px;
        }

        .form-group-register {
            position: relative;
            margin-bottom: 18px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
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
            box-shadow: 0 0 5px rgba(0, 184, 148, 0.4);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #111;
        }

        button {
            width: 100%;
            background: #1c6e03ff;
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
            background: #1c6e03ff;

        }

        .extra-links-register {
            margin-top: 15px;
            font-size: 0.9em;
        }

        .extra-links-register a {
            color: #1c6e03ff;
            font-weight: bold;
            text-decoration: none;
        }

        .extra-links-register a:hover {
            text-decoration: underline;
        }

        @media(max-width:400px) {
            .form-section-register {
                width: 90%;
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>

<body>
    <div class="register-wrapper">
        <section class="form-section-register">
            <div class="logo-register">
                <img src="./assets/image/logo.png" alt="Logo ZonaFutsal">
            </div>

            <h1>Daftar Akun</h1>

            <form action="" method="POST">
                <div class="form-group-register">
                    <input type="text" name="nama" id="nama" placeholder="Nama Lengkap" required pattern="[A-Za-z\s]+" title="Hanya huruf dan spasi diperbolehkan">
                </div>

                <div class="form-group-register">
                    <input type="text" name="username" id="username" placeholder="Username" required pattern="[A-Za-z0-9_]+" title="Hanya huruf, angka, dan garis bawah">
                </div>

                <div class="form-group-register">
                    <input type="email" name="email" id="email" placeholder="Alamat Email" required>
                </div>

                <div class="form-group-register">
                    <input type="tel" name="tel" id="tel" placeholder="No Telp" required pattern="[0-9]{10,15}" title="Hanya angka (10-15 digit)">
                </div>

                <div class="form-group-register">
                    <input type="password" id="password" name="password" placeholder="Kata Sandi" minlength="6" required>
                    <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                </div>

                <button type="submit">Daftar</button>
            </form>

            <div class="extra-links-register">
                <p>Sudah punya akun? <a href="./login.php">Masuk di sini</a></p>
            </div>
        </section>
    </div>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");
        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
        });

        document.getElementById("nama").addEventListener("input", function() {
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
        });
        document.getElementById("username").addEventListener("input", function() {
            this.value = this.value.replace(/[^A-Za-z0-9_]/g, '');
        });
        document.getElementById("tel").addEventListener("input", function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>