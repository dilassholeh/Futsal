<?php
session_start();

$_SESSION['booking'] = [
    'lapangan_id' => $_POST['lapangan_id'],
    'nama_lapangan' => $_POST['nama_lapangan'],
    'tanggal' => $_POST['tanggal'],
    'jam_mulai' => $_POST['jam_mulai'],
    'durasi' => $_POST['durasi'],
    'jam_selesai' => $_POST['jam_selesai'],
    'harga' => $_POST['harga'],
    'total' => $_POST['total'],
    'catatan' => $_POST['catatan']
];

header("Location: ../../pages/invoice.php");
<<<<<<< HEAD
exit;
=======
exit;
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
