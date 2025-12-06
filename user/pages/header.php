<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../../includes/koneksi.php';

$q = mysqli_query($conn, "SELECT nama_website, logo FROM pengaturan WHERE id = 1");
$pengaturan = mysqli_fetch_assoc($q);

$namaWeb = !empty($pengaturan['nama_website']) ? $pengaturan['nama_website'] : "Nama Website";

$logoPath = !empty($pengaturan['logo'])
    ? "/Futsal/uploads/" . $pengaturan['logo']
    : "/Futsal/assets/image/logo.png";

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $qNotif = mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM pesan 
        WHERE user_id = '$uid' 
        AND status = 'baru'
    ");

    $rNotif = mysqli_fetch_assoc($qNotif);
    $_SESSION['notif_count'] = $rNotif['total'] ?? 0;
}


$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($namaWeb) ?></title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Poppins", sans-serif;
        background: #f5f5f5;
        color: #222;
    }

    .nav {
        width: 100%;
        position: fixed;
        top: 0;
        z-index: 1000;
        background: #f5f5f5;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .nav-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 50px;
    }

    .logo-container {
        display: flex;
        align-items: center;
    }

    .logo-text {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 22px;
        font-weight: 700;
        color: #333;
        text-decoration: none;
    }

    .logo-text img {
        width: 45px;
        height: 45px;
        object-fit: cover;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 30px;
        justify-content: center;
        flex: 1;
    }

    .nav-menu li a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: .3s;
    }

    .nav-menu li a:hover,
    .nav-menu li a.active {
        color: #16a34a;
    }

    .user-icons {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .notif {
        position: relative;
        font-size: 20px;
        color: #333;
        text-decoration: none;
    }

    .notif-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: #fff;
        padding: 2px 6px;
        font-size: 11px;
        border-radius: 50%;
        font-weight: bold;
    }

    .profile-card,
    .profile-link {
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #f5f5f5;
        border-radius: 50%;
        border: 1px solid #333;
    }

    .profile-img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }

    .burger {
        display: none;
        font-size: 28px;
        cursor: pointer;
    }

    @media screen and (max-width:992px) {
        .nav-inner {
            flex-direction: column;
            align-items: stretch;
            padding: 0;
        }

        .logo-container {
            justify-content: center;
            padding: 10px 0;
            background: #117139;
        }

        .nav-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            flex-direction: column;
            background: #fff;
            display: none;
            z-index: 1000;
        }

        .nav-menu.active {
            display: flex;
        }

        .nav-menu li {
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .nav-menu li a {
            display: block;
            padding: 10px 0;
        }

        .nav-bottom {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #fff;
        }

        .burger {
            display: block;
        }

        .user-icons {
            gap: 10px;
            margin-left: auto;
        }
    }

    .btn-login,
    .btn-register {
        padding: 5px 10px;
        border-radius: 7px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .btn-login {
        background: #f5f5f5;
        color: #117139;
        border: 1px solid #117139;
    }

    .btn-register {
        background: #117139;
        color: #fff;
    }
</style>

<body>
    <nav class="nav">
        <div class="nav-inner">
            <div class="logo-container">
                <a href="index.php" class="logo-text">
                    <img src="<?= $logoPath ?>" alt="<?= htmlspecialchars($namaWeb) ?> Logo">
                    <span><?= htmlspecialchars($namaWeb) ?></span>
                </a>
            </div>

            <ul class="nav-menu">
                <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="sewa.php" class="<?= ($current_page == 'sewa.php') ? 'active' : '' ?>">Penyewaan</a></li>
                <li><a href="event.php" class="<?= ($current_page == 'event.php') ? 'active' : '' ?>">Event</a></li>
            </ul>

            <div class="nav-bottom">
                <div class="burger"><i class="bx bx-menu"></i></div>
                <div class="user-icons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="./pages/pesan.php" class="notif">
                            <i class='bx bxs-bell'></i>
                            <?php if ($_SESSION['notif_count'] > 0): ?>
                                <span class="notif-badge"><?= $_SESSION['notif_count'] ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="profile-card">
                            <a href="./pages/user.php" class="profile-link">
                                <img src="./assets/image/<?= $_SESSION['foto'] ?? 'profil.png'; ?>" class="profile-img">
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">Login</a>
                        <a href="register.php" class="btn-register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</body>

</html>