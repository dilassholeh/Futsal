<?php
session_start();
include '../../../includes/koneksi.php';

if(!isset($_SESSION['admin_id'])) exit;

$type = $_GET['type'] ?? 'excel';
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

$where = [];
if($search) $where[] = "(t.id LIKE '%$search%' OR u.name LIKE '%$search%')";
if($status) $where[] = "LOWER(t.status_pembayaran) = '".strtolower($status)."'";
if($tanggal) $where[] = "DATE((SELECT td.tanggal FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1)) = '$tanggal'";
$where_sql = $where ? "WHERE ".implode(" AND ", $where) : "";

$data = mysqli_query($conn, "
    SELECT t.id, u.name AS user_nama, t.subtotal, t.status_pembayaran,
           (SELECT td.tanggal FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS tanggal
    FROM transaksi t
    LEFT JOIN user u ON t.user_id = u.id
    $where_sql
    ORDER BY t.created_at DESC
");

if($type == 'excel'){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=transaksi.xls");
    echo "ID\tUser\tTotal\tStatus\tTanggal\n";
    while($row = mysqli_fetch_assoc($data)){
        echo "{$row['id']}\t{$row['user_nama']}\t{$row['subtotal']}\t{$row['status_pembayaran']}\t{$row['tanggal']}\n";
    }
}elseif($type=='pdf'){
    // minimal contoh PDF dengan HTML -> bisa pakai library FPDF atau TCPDF
    require_once 'vendor/autoload.php'; // jika pakai TCPDF
}
exit;
