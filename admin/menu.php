<?php

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- <link rel="stylesheet" href="../assets/css/admin/menuside.css"> -->
    <link rel="stylesheet" href="../assets/css/admin/menuside.css?v=<?php echo filemtime('../assets/css/admin/menuside.css'); ?>">

</head>

<body>


<aside class="sidebar">
        <img src="../assets/image/logo.png" alt="">
        <nav class="menu">
            <div class="top">
                <a href="#" class="active"><i class='bx bxs-dashboard'></i><span>Dashboard</span></a>
                <a href="#"><i class='bx bxs-user'></i><span>User</span> </a>
                <a href="#"><i class='bx bx-football'></i> <span>Lapangan</span></a>
                <a href="#"><i class='bx bxs-calendar'></i><span>Jadwal Sewa</span> </a>
                <a href="#"><i class='bx bxs-credit-card'></i> <span>Transaksi</span></a>
                <a href="#"><i class='bx bxs-calendar-event'></i><span>Event</span></a>
            </div>
            <div class="bottom">
                <a href="#"><i class='bx bxs-cog'></i> <span>Pengaturan</span></a>
                <a href="#"> <i class='bx bx-log-out' ></i><span>Logout</span></a>
            </div>
        </nav>
    </aside>

</body>

</html>