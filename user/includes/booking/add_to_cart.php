<?php
include '../../../includes/koneksi.php';

session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Silakan login terlebih dahulu!'
    ]);
    exit;
}

// Ambil data dari form (POST)
$user_id = $_SESSION['user_id'];
$id_lapangan = mysqli_real_escape_string($conn, $_POST['lapangan_id']);
$tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
$jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
$jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
$durasi = (int)$_POST['durasi'];
$harga_jual = (float)$_POST['harga'];
$total = (float)$_POST['total'];
$catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');

// Cek kelengkapan data
if (empty($id_lapangan) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || $durasi <= 0 || $harga_jual <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap!'
    ]);
    exit;
}

// Cek apakah jam yang dipilih sudah dipesan di tanggal & lapangan sama
$cek = mysqli_query($conn, "
    SELECT * FROM keranjang 
    WHERE id_lapangan = '$id_lapangan' 
    AND tanggal = '$tanggal' 
    AND (
        (jam_mulai <= '$jam_mulai' AND jam_selesai > '$jam_mulai') OR
        (jam_mulai < '$jam_selesai' AND jam_selesai >= '$jam_selesai')
    )
");
if (mysqli_num_rows($cek) > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Jam yang dipilih sudah ada di keranjang!'
    ]);
    exit;
}

// Generate ID unik (misalnya: KER001, KER002, dst)
$q_last = mysqli_query($conn, "SELECT id FROM keranjang ORDER BY id DESC LIMIT 1");
if (mysqli_num_rows($q_last) > 0) {
    $last_id = mysqli_fetch_assoc($q_last)['id'];
    $num = (int)substr($last_id, 3) + 1;
    $new_id = 'KER' . str_pad($num, 3, '0', STR_PAD_LEFT);
} else {
    $new_id = 'KER001';
}

// Simpan ke tabel keranjang
$query = mysqli_query($conn, "
    INSERT INTO keranjang (id, user_id, id_lapangan, tanggal, jam_mulai, jam_selesai, durasi, harga_jual, total, catatan)
    VALUES ('$new_id', '$user_id', '$id_lapangan', '$tanggal', '$jam_mulai', '$jam_selesai', '$durasi', '$harga_jual', '$total', '$catatan')
");

if ($query) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Berhasil ditambahkan ke keranjang!'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambahkan ke keranjang! ' . mysqli_error($conn)
    ]);
}
?>
