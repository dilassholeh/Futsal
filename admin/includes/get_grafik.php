<?php
include '../includes/koneksi.php';

$type = $_GET['type'] ?? 'tahun';
$bulanArr = [];
$totalArr = [];

switch ($type) {

    case 'bulan':
        $query = mysqli_query($conn, "
            SELECT DATE(created_at) AS tanggal, SUM(subtotal) AS total
            FROM transaksi
            WHERE MONTH(created_at)=MONTH(CURDATE())
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $bulanArr[] = date("d M", strtotime($row['tanggal']));
            $totalArr[] = $row['total'] ?? 0;
        }
    break;

    case 'minggu':
        $query = mysqli_query($conn, "
            SELECT DATE(created_at) AS tanggal, SUM(subtotal) AS total
            FROM transaksi
            WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE())
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $bulanArr[] = date("D", strtotime($row['tanggal']));
            $totalArr[] = $row['total'] ?? 0;
        }
    break;

    case 'hari':
        $query = mysqli_query($conn, "
            SELECT HOUR(created_at) AS jam, SUM(subtotal) AS total
            FROM transaksi
            WHERE DATE(created_at)=CURDATE()
            GROUP BY HOUR(created_at)
            ORDER BY jam ASC
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $bulanArr[] = $row['jam'] . ':00';
            $totalArr[] = $row['total'] ?? 0;
        }
    break;

    default: // Tahun Ini
        $query = mysqli_query($conn, "
            SELECT MONTH(created_at) AS bulan, SUM(subtotal) AS total
            FROM transaksi
            WHERE YEAR(created_at)=YEAR(CURDATE())
            GROUP BY bulan ORDER BY bulan ASC
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $bulanArr[] = date("M", mktime(0, 0, 0, $row['bulan'], 1));
            $totalArr[] = $row['total'] ?? 0;
        }
    break;
}

echo json_encode([
    "bulan" => $bulanArr,
    "total" => $totalArr
]);
?>
