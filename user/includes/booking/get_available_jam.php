<?php
include '../../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$conn->query("UPDATE transaksi SET status_pembayaran='expired' WHERE status_pembayaran='pending' AND expire_at <= NOW()");
$id_lapangan = $_GET['lapangan_id'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';
if (empty($id_lapangan) || empty($tanggal)) { echo json_encode(['error'=>'Data tidak lengkap']); exit; }
$all_slots = [];
$q_all = $conn->query("SELECT jam FROM jam ORDER BY jam ASC");
while ($row = $q_all->fetch_assoc()) { $all_slots[] = substr($row['jam'],0,5); }
$sql = "SELECT td.jam_mulai, td.jam_selesai FROM transaksi_detail td JOIN transaksi t ON t.id = td.id_transaksi WHERE td.id_lapangan = ? AND td.tanggal = ? AND (t.status_pembayaran IN ('dp','lunas') OR (t.status_pembayaran='pending' AND t.expire_at > NOW()))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $id_lapangan, $tanggal);
$stmt->execute();
$res = $stmt->get_result();
$booked = [];
while ($r = $res->fetch_assoc()) { $booked[] = ['mulai'=>substr($r['jam_mulai'],0,5),'selesai'=>substr($r['jam_selesai'],0,5)]; }
$available = array_filter($all_slots, function($slot) use ($booked) {
    foreach ($booked as $b) {
        if ($slot >= $b['mulai'] && $slot < $b['selesai']) { return false; }
    }
    return true;
});
$today = date('Y-m-d');
$now = date('H:i');
if ($tanggal === $today) {
    $available = array_filter($available, function($slot) use ($now) { return $slot > $now; });
}
if (empty($available)) { echo json_encode(['empty'=>true]); exit; }
echo json_encode(array_values($available));
?>
