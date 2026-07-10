<?php
include 'includes/config.php';

$username = 'admin';
$password = 'admin123'; // password login
$hash = password_hash($password, PASSWORD_DEFAULT);

$query = mysqli_query($conn,
    "INSERT INTO users (username, password, role)
     VALUES ('$username', '$hash', 'admin')"
);

if ($query) {
    echo "Admin berhasil dibuat<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
