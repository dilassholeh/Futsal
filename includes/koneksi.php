<?php
$host = "localhost";
$user = "root";      // ganti sesuai user MySQL
$pass = "";          // ganti sesuai password MySQL
$db   = "zafutsal";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
