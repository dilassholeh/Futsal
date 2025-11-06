<?php
include '../../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $nama = trim($_POST['nama']);

  if (!empty($id) && !empty($nama)) {
    $stmt = $conn->prepare("UPDATE kategori SET nama = ? WHERE id = ?");
    $stmt->bind_param("ss", $nama, $id);
    if ($stmt->execute()) {
      header("Location: ../../pages/kategori.php?status=updated");
    } else {
      header("Location: ../../pages/kategori.php?status=error");
    }
  } else {
    header("Location: ../../pages/kategori.php?status=empty");
  }
}
?>
