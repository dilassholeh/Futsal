<?php
include '../../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $query = mysqli_query($conn, "DELETE FROM keranjang WHERE id = '$id' AND user_id = '$user_id'");

  if ($query) {
    echo "<script>alert('Item berhasil dihapus dari keranjang!'); window.location.href='../../pages/keranjang.php';</script>";
  } else {
    echo "<script>alert('Gagal menghapus item!'); window.location.href='../../pages/keranjang.php';</script>";
  }
} else {
  header('Location: ../../pages/keranjang.php');
  exit;
}
?>
