<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?php echo filemtime('../assets/css/sidebar.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <h2>ZOFA</h2>
        </div>

        <nav class="menu">
            <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i><span>Dashboard</span>
            </a>

            <a href="../../user/index.php" target="_blank">
                <i class='bx bx-globe'></i><span>Lihat Website</span>
            </a>

            <div class="menu-group <?php echo in_array($current_page, ['lapangan.php','kategori.php','event.php','slider.php']) ? 'open' : ''; ?>">
                <div class="group-header">
                    <div class="left">
                        <i class='bx bx-football'></i>
                        <span>Lapangan</span>
                    </div>
                    <i class='bx bx-chevron-down arrow'></i>
                </div>
                <div class="submenu">
                    <a href="lapangan.php" class="<?php echo ($current_page == 'lapangan.php') ? 'active' : ''; ?>">
                        <i class='bx bx-square'></i><span>Lapangan</span>
                    </a>
                    <a href="kategori.php" class="<?php echo ($current_page == 'kategori.php') ? 'active' : ''; ?>">
                        <i class='bx bx-category'></i><span>Kategori</span>
                    </a>
                    <a href="event.php" class="<?php echo ($current_page == 'event.php') ? 'active' : ''; ?>">
                        <i class='bx bx-calendar-event'></i><span>Event</span>
                    </a>
                </div>
            </div>

            <div class="menu-group <?php echo in_array($current_page, ['transaksi.php','bank.php','keuangan.php']) ? 'open' : ''; ?>">
                <div class="group-header">
                    <div class="left">
                        <i class='bx bx-wallet'></i>
                        <span>Keuangan</span>
                    </div>
                    <i class='bx bx-chevron-down arrow'></i>
                </div>
                <div class="submenu">
                    <a href="transaksi.php" class="<?php echo ($current_page == 'transaksi.php') ? 'active' : ''; ?>">
                        <i class='bx bx-transfer-alt'></i><span>Transaksi</span>
                    </a>
                    <a href="bank.php" class="<?php echo ($current_page == 'bank.php') ? 'active' : ''; ?>">
                        <i class='bx bxs-bank'></i><span>Bank</span>
                    </a>
                    <a href="keuangan.php" class="<?php echo ($current_page == 'keuangan.php') ? 'active' : ''; ?>">
                        <i class='bx bx-bar-chart-alt-2'></i><span>Laporan</span>
                    </a>
                </div>
            </div>

            <a href="user.php" class="<?php echo ($current_page == 'user.php') ? 'active' : ''; ?>">
                <i class='bx bx-group'></i><span>User</span>
            </a>


            <a href="riwayat_booking.php" class="<?php echo ($current_page == 'riwayat_booking.php') ? 'active' : ''; ?>">
                <i class='bx bx-history'></i><span>Riwayat Booking</span>
            </a>

            <a href="admin.php" class="<?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>">
                <i class='bx bx-shield-quarter'></i><span>Kelola Admin</span>
            </a>

            <a href="pengaturan.php" class="<?php echo ($current_page == 'pengaturan.php') ? 'active' : ''; ?>">
                <i class='bx bx-cog'></i><span>Pengaturan Website</span>
            </a>


            <div class="log">
                <a href="../logout.php">
                    <i class='bx bx-log-out'></i><span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <script>
        document.querySelectorAll('.group-header').forEach(header => {
            header.addEventListener('click', () => {
                const group = header.parentElement;
                group.classList.toggle('open');
            });
        });
    </script>
</body>
</html>
