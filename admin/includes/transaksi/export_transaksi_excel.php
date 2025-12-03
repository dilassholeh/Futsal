<?php
session_start();
include '../../../includes/koneksi.php';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_transaksi_" . date('Ymd') . ".xls");

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

$where = [];
if ($search) $where[] = "(t.id LIKE '%$search%' OR u.name LIKE '%$search%')";
if ($status) {
    if ($status == 'dp') $where[] = "t.status_pembayaran='menunggu_konfirmasi'";
    else $where[] = "t.status_pembayaran='$status'";
}
if ($tanggal) $where[] = "DATE(td.tanggal)='$tanggal'";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$query = mysqli_query($conn, "
SELECT t.*, u.name AS user_nama, td.tanggal, td.jam_mulai, td.jam_selesai
FROM transaksi t
LEFT JOIN user u ON t.user_id = u.id
LEFT JOIN (
    SELECT id_transaksi, MIN(tanggal) AS tanggal, MIN(jam_mulai) AS jam_mulai, MAX(jam_selesai) AS jam_selesai
    FROM transaksi_detail
    GROUP BY id_transaksi
) td ON td.id_transaksi=t.id
$where_sql
ORDER BY t.created_at DESC
");

echo "<table border='1'>
<tr>
    <th>No</th>
    <th>ID Transaksi</th>
    <th>Nama User</th>
    <th>Total (Rp)</th>
    <th>Tanggal</th>
    <th>Jam</th>
    <th>Status</th>
</tr>";

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $tanggal_booking = !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-';
    $jam_booking = (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) ? $row['jam_mulai'] . " - " . $row['jam_selesai'] : '-';

    echo "<tr>
        <td>{$no}</td>
        <td>{$row['id']}</td>
        <td>{$row['user_nama']}</td>
        <td>" . number_format($row['subtotal'], 0, ',', '.') . "</td>
        <td>{$tanggal_booking}</td>
        <td>{$jam_booking}</td>
        <td>{$row['status_pembayaran']}</td>
    </tr>";
    $no++;
}

echo "</table>";
exit;
