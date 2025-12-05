<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_POST['id'] ?? '';
$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$konfirmasi = $_POST['konfirmasi'] ?? '';

if (empty($admin_id) || empty($password_lama) || empty($password_baru) || empty($konfirmasi)) {
    $_SESSION['error'] = "Semua field harus diisi.";
    header("Location: admin.php");
    exit;
}

$query = $conn->prepare("SELECT password FROM user WHERE id = ?");
$query->bind_param("s", $admin_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Admin tidak ditemukan.";
    header("Location: admin.php");
    exit;
}

$row = $result->fetch_assoc();

if (!password_verify($password_lama, $row['password'])) {
    $_SESSION['error'] = "Password lama salah.";
    header("Location: admin.php");
    exit;
}

if ($password_baru !== $konfirmasi) {
    $_SESSION['error'] = "Password baru dan konfirmasi tidak cocok.";
    header("Location: admin.php");
    exit;
}

$password_baru_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

$update = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
$update->bind_param("ss", $password_baru_hashed, $admin_id);

if ($update->execute()) {
    $_SESSION['success'] = "Password berhasil diubah.";
} else {
    $_SESSION['error'] = "Terjadi kesalahan saat mengubah password.";
}

header("Location: admin.php");
exit;
?>
