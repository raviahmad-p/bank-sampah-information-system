<?php
include '../includes/config.php';

$id = $_GET['id'] ?? 0;

mysqli_begin_transaction($conn);

// ambil saldo lama
$old = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT id_nasabah, total_harga FROM setoran WHERE id='$id'
"));

if ($old) {
    mysqli_query($conn,"DELETE FROM setoran_detail WHERE id_setoran='$id'");
    mysqli_query($conn,"DELETE FROM setoran WHERE id='$id'");

    mysqli_query($conn,"
        UPDATE nasabah
        SET saldo = saldo - '{$old['total_harga']}'
        WHERE id='{$old['id_nasabah']}'
    ");
}

mysqli_commit($conn);

header("Location: setoran.php");
exit;
