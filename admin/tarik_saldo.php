<?php
session_start();
include '../includes/sidebar_admin.php';

$conn = mysqli_connect("localhost","root","","sisteminformasi");
if(!$conn) die("Koneksi gagal");

/* =====================
   PROSES TARIK SALDO
===================== */
if(isset($_POST['tarik'])){
    $id_nasabah = $_POST['id_nasabah'];
    $jumlah     = $_POST['jumlah'];

    // Ambil saldo nasabah
    $q = mysqli_query($conn,"
        SELECT saldo FROM nasabah WHERE id='$id_nasabah'
    ");
    $n = mysqli_fetch_assoc($q);

    if($jumlah > $n['saldo']){
        echo "<script>
            alert('Saldo tidak mencukupi!');
            history.back();
        </script>";
        exit;
    }

    // Simpan penarikan
    mysqli_query($conn,"
        INSERT INTO tarik_saldo (id_nasabah,tanggal,jumlah)
        VALUES ('$id_nasabah',NOW(),'$jumlah')
    ");

    // Update saldo
    mysqli_query($conn,"
        UPDATE nasabah
        SET saldo = saldo - $jumlah
        WHERE id='$id_nasabah'
    ");

    header("Location: tarik_saldo.php");
    exit;
}

/* =====================
   DATA NASABAH
===================== */
$nasabah = mysqli_query($conn,"SELECT * FROM nasabah ORDER BY nama ASC");

/* =====================
   RIWAYAT TARIK
===================== */
$riwayat = mysqli_query($conn,"
    SELECT t.*, n.nama
    FROM tarik_saldo t
    JOIN nasabah n ON t.id_nasabah=n.id
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tarik Saldo</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f5f7fb;
}
.content{
    margin-left:240px;
    padding:30px;
}
.card{
    background:#fff;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
    margin-bottom:24px;
}
.card-header{
    padding:16px 20px;
    font-weight:600;
    border-bottom:1px solid #eee;
}
.card-body{padding:20px}
.form-group{margin-bottom:14px}
label{font-size:14px;font-weight:500}
select,input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
}
.btn{
    background:#6366f1;
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:8px;
    cursor:pointer;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}
th{background:#fafafa;text-align:left;color:#6b7280}
</style>
</head>

<body>

<div class="content">

<h1>Tarik Saldo Nasabah</h1>

<!-- FORM TARIK -->
<div class="card">
<div class="card-header">Form Penarikan</div>
<div class="card-body">
<form method="post">

<div class="form-group">
<label>Nasabah</label>
<select name="id_nasabah" required>
<option value="">-- Pilih Nasabah --</option>
<?php while($n=mysqli_fetch_assoc($nasabah)){ ?>
<option value="<?= $n['id'] ?>">
<?= $n['nama'] ?> (Saldo: Rp <?= number_format($n['saldo'],0,',','.') ?>)
</option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label>Jumlah Penarikan (Rp)</label>
<input type="number" name="jumlah" required>
</div>

<button class="btn" name="tarik">Tarik Saldo</button>

</form>
</div>
</div>

<!-- RIWAYAT -->
<div class="card">
<div class="card-header">Riwayat Penarikan</div>
<div class="card-body">

<table>
<tr>
<th>Tanggal</th>
<th>Nasabah</th>
<th>Jumlah</th>
</tr>

<?php while($r=mysqli_fetch_assoc($riwayat)){ ?>
<tr>
<td><?= $r['tanggal'] ?></td>
<td><?= $r['nama'] ?></td>
<td>Rp <?= number_format($r['jumlah'],0,',','.') ?></td>
</tr>
<?php } ?>

</table>

</div>
</div>

</div>

</body>
</html>
