<?php
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bank   = trim($_POST['nama_bank']);
    $atas_nama   = trim($_POST['atas_nama']);
    $no_rekening = trim($_POST['no_rekening']);

    $query = mysqli_query($conn, "SELECT id FROM bank ORDER BY id DESC LIMIT 1");
    $lastId = mysqli_fetch_assoc($query)['id'] ?? null;

    if ($lastId) {
        $num = (int)substr($lastId, 4);
        $newId = 'BANK' . str_pad($num + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newId = 'BANK00001';
    }

    $stmt = $conn->prepare("INSERT INTO bank (id, nama_bank, atas_nama, no_rekening) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $newId, $nama_bank, $atas_nama, $no_rekening);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data bank berhasil ditambahkan!');
                window.location.href='../../pages/bank.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan data bank!');
                window.history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: bank.php");
    exit;
}
?>
