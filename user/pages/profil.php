<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - ZonaFutsal</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #00b09b, #96c93d);
    color: #333;
    min-height: 100vh;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 15px 40px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.nav .logo-text {
    font-weight: 700;
    color: #00b09b;
    font-size: 24px;
    text-decoration: none;
}

.nav ul {
    display: flex;
    list-style: none;
    gap: 25px;
    margin: 0;
    padding: 0;
}

.nav ul li a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: color 0.3s;
}

.nav ul li a:hover {
    color: #00b09b;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 15px;
}

.btn-logout {
    background: #ff4d4d;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-logout:hover {
    background: #d93636;
}

.profile-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
}

.profile-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    padding: 40px 60px;
    width: 100%;
    max-width: 600px;
    text-align: center;
    animation: fadeIn 0.6s ease;
}

.profile-card img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #00b09b;
    margin-bottom: 15px;
}

.profile-card h2 {
    font-size: 24px;
    color: #00b09b;
    margin: 10px 0;
}

.profile-card p {
    margin: 5px 0;
    font-size: 16px;
    color: #555;
}

.profile-card .btn-edit {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 24px;
    background: #00b09b;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.3s;
}

.profile-card .btn-edit:hover {
    background: #029680;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
    <nav class="nav">
        <a href="../index.php" class="logo-text">ZonaFutsal</a>
        <ul>
            <li><a href="../index.php">Beranda</a></li>
            <li><a href="sewa.php">Penyewaan</a></li>
            <li><a href="event.php">Event</a></li>
        </ul>
        <div class="user-menu">
            <span>👤 <?= htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="profile-container">
        <div class="profile-card">
            <img src="../assets/image/default-profile.jpg" alt="Foto Profil">
            <h2><?= htmlspecialchars($_SESSION['username']); ?></h2>
            <p>Email: <?= htmlspecialchars($_SESSION['email']); ?></p>
            <p>Bergabung sejak: <?= date("d F Y") ?></p>

            <a href="#" class="btn-edit">Edit Profil</a>
        </div>
    </div>
</body>
</html>
