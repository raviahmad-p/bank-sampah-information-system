<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sisteminformasi"; // PASTIKAN database ini benar di phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// set charset biar aman (hindari error karakter)
mysqli_set_charset($conn, "utf8");
?>
