<?php
include 'koneksi.php';
header('Content-Type: application/json');

$lapangan = $_GET['lapangan'] ?? '';
$tanggal  = $_GET['tanggal'] ?? '';

if (empty($lapangan) || empty($tanggal)) {
    echo json_encode(["error" => "Parameter tidak lengkap"]);
    exit;
}

$query = "SELECT jam_mulai FROM jadwal WHERE lapangan = '$lapangan' AND tanggal = '$tanggal'";
$result = $conn->query($query);

$booked = [];
while ($row = $result->fetch_assoc()) {
    $booked[] = substr($row['jam_mulai'], 0, 5); // ubah 08:00:00 -> 08:00
}

echo json_encode($booked);
?>
