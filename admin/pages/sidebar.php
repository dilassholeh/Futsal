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
            <img src="../assets/image/logo_orange.png" alt="Logo" class="logo-img">
        </div>
        <button id="toggle-btn"><i class='bx bx-chevron-left'></i></button>

        <nav class="menu">
            <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i> <span>Dashboard</span>
            </a>
            <a href="../../user/index.php" target="_blank">
                <i class='bx bx-globe'></i> <span>Lihat Website</span>
            </a>
            <a href="transaksi.php" class="<?php echo ($current_page == 'transaksi.php') ? 'active' : ''; ?>">
                <i class='bx bx-transfer-alt'></i> <span>Transaksi</span>
            </a>
            <a href="lapangan.php" class="<?php echo ($current_page == 'lapangan.php') ? 'active' : ''; ?>">
                <i class='bx bx-football'></i> <span>Lapangan</span>
            </a>
            <a href="slider.php" class="<?php echo ($current_page == 'slider.php') ? 'active' : ''; ?>">
                <i class='bx bx-slideshow'></i> <span>Slider</span>
            </a>
            <a href="event.php" class="<?php echo ($current_page == 'event.php') ? 'active' : ''; ?>">
                <i class='bx bx-category'></i> <span>Event</span>
            </a>
            <a href="user.php" class="<?php echo ($current_page == 'user.php') ? 'active' : ''; ?>">
                <i class='bx bx-group'></i> <span>User</span>
            </a>

            <a href="bank.php" class="<?php echo ($current_page == 'bank.php') ? 'active' : ''; ?>">
                <i class='bx bxs-bank'></i> <span>Bank</span>
            </a>

            <div class="log">
                <a href="../logout.php">
                    <i class='bx bx-log-out'></i> <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');
        const toggleIcon = toggleBtn.querySelector('i');

        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
            toggleIcon.classList.replace('bx-chevron-left', 'bx-chevron-right');
        }

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            const collapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', collapsed);
            toggleIcon.classList.toggle('bx-chevron-left', !collapsed);
            toggleIcon.classList.toggle('bx-chevron-right', collapsed);
        });
    </script>
</body>

</html>