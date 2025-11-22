<?php
session_start();


if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}


$username = $_SESSION['username'] ?? 'User';
$email = $_SESSION['email'] ?? 'Belum ada email';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya - ZonaFutsal</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f5f5f5;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .profile-card {
        background: white;
        border-radius: 16px;
        padding: 30px 25px;
        width: 100%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    .profile-card img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
    }

    .profile-card h2 {
        font-size: 22px;
        margin: 10px 0;
        color: #333;
    }

    .profile-card p {
        margin: 5px 0;
        font-size: 15px;
        color: #666;
    }

    .btn-back {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background: #00b894;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s;
    }

    .btn-back:hover {
        background: #019875;
    }
</style>
</head>
<body>

<div class="profile-card">
    <img src="../assets/image/default-profile.jpg" alt="Foto Profil">
    <h2><?= htmlspecialchars($username); ?></h2>
    <p>Email: <?= htmlspecialchars($email); ?></p>
    <p>Bergabung sejak: <?= date("d F Y"); ?></p>

    <a href="javascript:history.back()" class="btn-back">Kembali</a>
</div>

</body>
</html>
