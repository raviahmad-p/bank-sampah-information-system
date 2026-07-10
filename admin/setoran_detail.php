<?php
include '../includes/config.php';

$id = $_GET['id'] ?? 0;

$q = mysqli_query($conn,"
    SELECT sd.berat, sd.subtotal, js.nama_sampah
    FROM setoran_detail sd
    JOIN jenis_sampah js ON sd.id_sampah = js.id
    WHERE sd.id_setoran = '$id'
");

$data = [];
while($r = mysqli_fetch_assoc($q)){
    $data[] = $r;
}

echo json_encode($data);
