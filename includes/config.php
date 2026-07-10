<?php

$host = "sql202.infinityfree.com";
$user = "if0_42380158";
$pass = "9TThivW0yAv2F";
$db   = "if0_42380158_banksampah";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");