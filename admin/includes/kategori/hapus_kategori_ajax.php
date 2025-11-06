<?php
include '../../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];

  if (!empty($id)) {
    $stmt = $conn->prepare("DELETE FROM kategori WHERE id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
      header("Location: ../../pages/kategori.php?status=deleted");
    } else {
      header("Location: ../../pages/kategori.php?status=error");
    }
  } else {
    header("Location: ../../pages/kategori.php?status=empty");
  }
}
?>
