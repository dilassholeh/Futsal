<?php
include '../../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$id_lapangan = $_GET['lapangan_id'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

if (empty($id_lapangan) || empty($tanggal)) {
    die(json_encode(['error' => 'Data tidak lengkap']));
}

$all_slots = [];
$q_all = $conn->query("SELECT jam FROM jam ORDER BY jam ASC");
while ($row = $q_all->fetch_assoc()) {
    $all_slots[] = str_pad(substr($row['jam'], 0, 5), 5, '0', STR_PAD_LEFT);
}

$sql = "
    SELECT td.jam_mulai, td.jam_selesai 
    FROM transaksi_detail td
    JOIN transaksi t ON t.id = td.id_transaksi
    WHERE td.id_lapangan = ?
    AND td.tanggal = ?
    AND t.status_pembayaran IN ('dp', 'lunas')
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $id_lapangan, $tanggal);
$stmt->execute();
$res = $stmt->get_result();

$booked_slots = [];
while ($r = $res->fetch_assoc()) {
    $booked_slots[] = [
        'jam_mulai' => substr($r['jam_mulai'], 0, 5),
        'jam_selesai' => substr($r['jam_selesai'], 0, 5)
    ];
}

$available_slots = array_filter($all_slots, function($slot) use ($booked_slots) {
    foreach ($booked_slots as $b) {
        if ($slot >= $b['jam_mulai'] && $slot < $b['jam_selesai']) {
            return false;
        }
    }
    return true;
});

$today = date('Y-m-d');
$currentHour = date('H:i');
if ($tanggal === $today) {
    $available_slots = array_filter($available_slots, function($slot) use ($currentHour) {
        return $slot > $currentHour;
    });
}

if (empty($available_slots)) {
    echo json_encode(['empty' => true]);
    exit;
}

echo json_encode(array_values($available_slots));
?>
