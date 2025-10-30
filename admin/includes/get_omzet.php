<?php
include '../includes/koneksi.php';

// Ambil filter dari frontend
$type = $_GET['type'] ?? 'hari';

if ($type == "hari") {
    $query = "SELECT SUM(subtotal) AS total FROM transaksi WHERE DATE(created_at)=CURDATE()";
} elseif ($type == "minggu") {
    $query = "SELECT SUM(subtotal) AS total FROM transaksi 
              WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($type == "bulan") {
    $query = "SELECT SUM(subtotal) AS total FROM transaksi 
              WHERE MONTH(created_at)=MONTH(CURDATE()) 
              AND YEAR(created_at)=YEAR(CURDATE())";
} elseif ($type == "tahun") {
    $query = "SELECT SUM(subtotal) AS total FROM transaksi 
              WHERE YEAR(created_at)=YEAR(CURDATE())";
} else {
    $query = "SELECT SUM(subtotal) AS total FROM transaksi";
}

$result = mysqli_fetch_assoc(mysqli_query($conn, $query));
$total = $result['total'] ?? 0;

// Format ke Rupiah untuk frontend
$totalFormatted = number_format($total, 0, ',', '.');

// Output JSON
echo json_encode([
    "total" => $totalFormatted
]);
