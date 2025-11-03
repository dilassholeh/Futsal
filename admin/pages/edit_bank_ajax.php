<?php
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama_bank'];
    $atas = $_POST['atas_nama'];
    $rek  = $_POST['no_rekening'];

    $stmt = $conn->prepare("UPDATE bank SET nama_bank=?, atas_nama=?, no_rekening=? WHERE id=?");
    $stmt->bind_param("ssss", $nama, $atas, $rek, $id);
    $stmt->execute();

    echo "<script>alert('Data berhasil diperbarui!');window.location.href='bank.php';</script>";
}
?>
