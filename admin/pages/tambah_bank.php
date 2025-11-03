<?php
include '../../includes/koneksi.php';

// Cek apakah form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bank   = trim($_POST['nama_bank']);
    $atas_nama   = trim($_POST['atas_nama']);
    $no_rekening = trim($_POST['no_rekening']);

    // Ambil ID terakhir dari tabel bank
    $query = mysqli_query($conn, "SELECT id FROM bank ORDER BY id DESC LIMIT 1");
    $lastId = mysqli_fetch_assoc($query)['id'] ?? null;

    // Generate ID baru
    if ($lastId) {
        // Ambil angka dari ID terakhir, misal BANK00012 → 12
        $num = (int)substr($lastId, 4);
        $newId = 'BANK' . str_pad($num + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newId = 'BANK00001';
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO bank (id, nama_bank, atas_nama, no_rekening) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $newId, $nama_bank, $atas_nama, $no_rekening);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data bank berhasil ditambahkan!');
                window.location.href='bank.php';
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
