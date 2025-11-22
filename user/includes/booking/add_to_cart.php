<?php
include '../../../includes/koneksi.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Anda harus login']); exit; }
$conn->query("UPDATE transaksi SET status_pembayaran='expired' WHERE status_pembayaran='cart' AND expire_at <= NOW()");
if (!isset($_POST['lapangan_id']) || !isset($_POST['tanggal']) || !isset($_POST['jam_mulai']) || !isset($_POST['durasi']) || !isset($_POST['total'])) { echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']); exit; }
$user_id = $_SESSION['user_id'];
$lapangan_id = $_POST['lapangan_id'];
$tanggal = $_POST['tanggal'];
$jam_mulai = $_POST['jam_mulai'];
$durasi = (int)$_POST['durasi'];
$jm_h = (int)substr($jam_mulai,0,2);
$jam_selesai_h = $jm_h + $durasi;
$jam_selesai = (strlen($jam_selesai_h)===1?'0'.$jam_selesai_h:$jam_selesai_h).':00';
$total = (float)$_POST['total'];
$id_transaksi = "CART".date("YmdHis").rand(100,999);
$expire_at = date("Y-m-d H:i:s", strtotime("+30 minutes"));
$stmt = $conn->prepare("INSERT INTO transaksi (id, user_id, subtotal, status_pembayaran, expire_at, created_at) VALUES (?, ?, ?, 'cart', ?, NOW())");
$stmt->bind_param("ssds", $id_transaksi, $user_id, $total, $expire_at);
$stmt->execute();
$stmt->close();
$id_detail = uniqid('det_');
$stmt2 = $conn->prepare("INSERT INTO transaksi_detail (id, id_transaksi, id_lapangan, tanggal, jam_mulai, jam_selesai, durasi, harga_jual) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("ssssssid", $id_detail, $id_transaksi, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $durasi, $_POST['harga']);
$stmt2->execute();
$stmt2->close();
echo json_encode(['status'=>'success','message'=>'Ditambahkan ke keranjang','cart_id'=>$id_transaksi]);
?>
