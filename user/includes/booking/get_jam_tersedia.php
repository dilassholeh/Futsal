<?php
include '../../../includes/koneksi.php';
header('Content-Type: application/json');

if (!isset($_GET['lapangan_id']) || !isset($_GET['tanggal'])) {
    echo json_encode(['error' => 'Parameter tidak lengkap']);
    exit;
}

$lapangan_id = mysqli_real_escape_string($conn, $_GET['lapangan_id']);
$tanggal = mysqli_real_escape_string($conn, $_GET['tanggal']);

$allJam = [];
$resultJam = mysqli_query($conn, "SELECT jam FROM jam ORDER BY jam ASC");
while ($row = mysqli_fetch_assoc($resultJam)) {
    $allJam[] = substr($row['jam'], 0, 5); 
}

$query = "
    SELECT jam_mulai, jam_selesai 
    FROM transaksi_detail
    WHERE id_lapangan = '$lapangan_id' AND tanggal = '$tanggal'
";
$result = mysqli_query($conn, $query);

$bookedJam = [];
while ($row = mysqli_fetch_assoc($result)) {
    $mulai = (int)substr($row['jam_mulai'], 0, 2);
    $selesai = (int)substr($row['jam_selesai'], 0, 2);
    for ($i = $mulai; $i < $selesai; $i++) {
        $bookedJam[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
    }
}

$available = array_values(array_diff($allJam, $bookedJam));

echo json_encode($available);
?> 
