<?php
include '../../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    die("Akses ditolak!");
}

$id = $_GET['id'] ?? '';
if (empty($id)) {
    die("ID transaksi tidak ditemukan!");
}

$conn->begin_transaction();

try {
    $detail = $conn->prepare("SELECT id_lapangan, tanggal, jam_mulai, jam_selesai FROM transaksi_detail WHERE id_transaksi = ?");
    $detail->bind_param("s", $id);
    $detail->execute();
    $result = $detail->get_result();

    while ($row = $result->fetch_assoc()) {

        // 
        // $conn->query("INSERT INTO log_pembatalan (id_lapangan, tanggal, jam_mulai, jam_selesai, id_transaksi) 
        //  ('{$row['id_lapangan']}', '{$row['tanggal']}', '{$row['jam_mulai']}', '{$row['jam_selesai']}', '$id')");
    }
    $delete_detail = $conn->prepare("DELETE FROM transaksi_detail WHERE id_transaksi = ?");
    $delete_detail->bind_param("s", $id);
    $delete_detail->execute();


    $update = $conn->prepare("UPDATE transaksi SET status_pembayaran = 'dibatalkan' WHERE id = ?");
    $update->bind_param("s", $id);
    $update->execute();

    $conn->commit();

    echo "<script>
        alert('Transaksi berhasil dibatalkan dan jam dikosongkan kembali!');
        window.location='../../pages/transaksi.php';
    </script>";
} catch (Exception $e) {
    $conn->rollback();
    echo "<script>
        alert('Gagal membatalkan transaksi: " . addslashes($e->getMessage()) . "');
        window.location='../../pages/transaksi.php';
    </script>";
}
?>
