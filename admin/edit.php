<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // load data edit
    $id = $_GET['id'];

    $q = mysqli_query($conn,"
        SELECT sd.id_sampah, sd.berat
        FROM setoran_detail sd
        WHERE sd.id_setoran='$id'
    ");

    $data = [];
    while($r=mysqli_fetch_assoc($q)){
        $data[]=$r;
    }

    echo json_encode($data);
    exit;
}

if (isset($_POST['update'])) {

    $id_setoran = $_POST['id_setoran'];

    mysqli_begin_transaction($conn);

    // ambil saldo lama
    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT id_nasabah, total_harga FROM setoran WHERE id='$id_setoran'
    "));

    mysqli_query($conn,"DELETE FROM setoran_detail WHERE id_setoran='$id_setoran'");

    $tb = 0; 
    $th = 0;

    foreach($_POST['id_sampah'] as $i=>$id_sampah){
        $berat = floatval($_POST['berat'][$i]);
        if($berat<=0) continue;

        $j = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT harga_per_kg FROM jenis_sampah WHERE id='$id_sampah'
        "));

        $harga = $j['harga_per_kg'];
        $sub   = $berat * $harga;

        mysqli_query($conn,"
            INSERT INTO setoran_detail
            (id_setoran,id_sampah,berat,harga_per_kg,subtotal)
            VALUES
            ('$id_setoran','$id_sampah','$berat','$harga','$sub')
        ");

        $tb += $berat;
        $th += $sub;
    }

    mysqli_query($conn,"
        UPDATE setoran 
        SET total_berat='$tb', total_harga='$th'
        WHERE id='$id_setoran'
    ");

    $selisih = $th - $old['total_harga'];

    mysqli_query($conn,"
        UPDATE nasabah
        SET saldo = saldo + '$selisih'
        WHERE id='{$old['id_nasabah']}'
    ");

    mysqli_commit($conn);

    header("Location: setoran.php");
    exit;
}
