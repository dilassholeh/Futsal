<?php
include '../../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM bank WHERE id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

    echo "<script>alert('Data berhasil dihapus!');window.location.href='../../pages/bank.php';</script>";
}
?>
