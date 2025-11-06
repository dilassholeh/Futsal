<?php
include '../../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama']);
  if ($nama != '') {

  
    $result = $conn->query("SELECT id FROM kategori ORDER BY id DESC LIMIT 1");
    $lastID = $result->fetch_assoc()['id'] ?? 'K000';

    
    $num = (int)substr($lastID, 1) + 1;
    $newID = 'K' . str_pad($num, 3, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare("INSERT INTO kategori (id, nama) VALUES (?, ?)");
    $stmt->bind_param("ss", $newID, $nama);

    if ($stmt->execute()) {
      header("Location: ../../pages/kategori.php"); 
      exit;
    } else {
      echo "Gagal menambah kategori: " . $stmt->error;
    }
  } else {
    echo "Nama kategori wajib diisi.";
  }
}
?>
