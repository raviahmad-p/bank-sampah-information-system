<?php
session_start();
include '../includes/config.php';

/* =====================
   FILTER
===================== */
$id_nasabah = $_GET['id_nasabah'] ?? '';
$tgl_awal   = $_GET['tgl_awal'] ?? '';
$tgl_akhir  = $_GET['tgl_akhir'] ?? '';

$where = "WHERE 1";
if($id_nasabah!='') $where .= " AND s.id_nasabah='$id_nasabah'";
if($tgl_awal!='')   $where .= " AND s.tanggal>='$tgl_awal'";
if($tgl_akhir!='')  $where .= " AND s.tanggal<='$tgl_akhir'";

/* =====================
   DOWNLOAD CSV (HARUS SEBELUM HTML & SIDEBAR)
===================== */
if(isset($_GET['download']) && $_GET['download']=='csv'){

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_setoran.csv');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID','Nasabah','Tanggal','Total Berat (Kg)','Total Nilai']);

    $q = mysqli_query($conn,"
        SELECT s.*, n.nama
        FROM setoran s
        JOIN nasabah n ON s.id_nasabah=n.id
        $where
        ORDER BY s.tanggal DESC
    ");

    while($r = mysqli_fetch_assoc($q)){
        fputcsv($output, [
            $r['id'],
            $r['nama'],
            date('d-m-Y', strtotime($r['tanggal'])),
            $r['total_berat'],
            $r['total_harga']
        ]);
    }

    fclose($output);
    exit; // WAJIB
}

/* =====================
   BARU INCLUDE SIDEBAR
===================== */
include '../includes/sidebar_admin.php';

/* =====================
   DATA NASABAH
===================== */
$nasabah = mysqli_query($conn,"SELECT * FROM nasabah ORDER BY nama");

/* =====================
   LAPORAN
===================== */
$laporan = mysqli_query($conn,"
    SELECT s.*, n.nama
    FROM setoran s
    JOIN nasabah n ON s.id_nasabah=n.id
    $where
    ORDER BY s.tanggal DESC
");

/* =====================
   TOTAL
===================== */
$total = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(total_berat) tb, SUM(total_harga) th
    FROM setoran s
    $where
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Setoran</title>

<style>
*{font-family:"Times New Roman", Times, serif;}
body{margin:0;background:#f5f7fb}
.content{margin-left:240px;padding:30px}
.card{background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.05)}
.card-header{padding:16px;font-weight:600;border-bottom:1px solid #eee}
.card-body{padding:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:12px;border-bottom:1px solid #eee;font-size:14px}
th{background:#fafafa;color:#374151;text-align:left}
input,select{padding:8px;border-radius:6px;border:1px solid #ddd}
.btn{padding:6px 12px;font-size:13px;border-radius:6px;border:1px solid #ddd;background:#fff;cursor:pointer;text-decoration:none}
.btn-primary{background:#4f46e5;color:#fff;border:none}
.btn-success{background:#16a34a;color:#fff;border:none}
.summary{display:flex;gap:20px;margin-top:20px}
.box{flex:1;background:#eef2ff;padding:16px;border-radius:10px}
.box h3{margin:0;font-size:16px;color:#1e3a8a}
.box p{margin:6px 0 0;font-size:18px;font-weight:600}
</style>
</head>

<body>
<div class="content">

<h2>Laporan Setoran</h2>

<div class="card">
<div class="card-body">
<form method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">

<select name="id_nasabah">
<option value="">Semua Nasabah</option>
<?php while($n=mysqli_fetch_assoc($nasabah)): ?>
<option value="<?= $n['id'] ?>" <?= $id_nasabah==$n['id']?'selected':'' ?>>
<?= $n['nama'] ?>
</option>
<?php endwhile; ?>
</select>

<input type="date" name="tgl_awal" value="<?= $tgl_awal ?>">
<input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>">

<button class="btn btn-primary">Filter</button>
<a href="laporan.php" class="btn">Reset</a>

<a class="btn btn-success"
   href="laporan.php?download=csv&id_nasabah=<?= $id_nasabah ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>">
Download CSV
</a>

</form>
</div>
</div>

<div class="card" style="margin-top:20px">
<div class="card-header">Data Setoran</div>
<div class="card-body">
<table>
<tr>
<th>ID</th>
<th>Nasabah</th>
<th>Tanggal</th>
<th>Total Berat</th>
<th>Total Nilai</th>
</tr>

<?php if(mysqli_num_rows($laporan)==0): ?>
<tr><td colspan="5" align="center">Tidak ada data</td></tr>
<?php endif; ?>

<?php while($r=mysqli_fetch_assoc($laporan)): ?>
<tr>
<td><?= str_pad($r['id'],3,'0',STR_PAD_LEFT) ?></td>
<td><?= $r['nama'] ?></td>
<td><?= date('d-m-Y',strtotime($r['tanggal'])) ?></td>
<td><?= $r['total_berat'] ?> kg</td>
<td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>

<div class="summary">
<div class="box">
<h3>Total Berat</h3>
<p><?= $total['tb'] ?? 0 ?> kg</p>
</div>
<div class="box">
<h3>Total Nilai</h3>
<p>Rp <?= number_format($total['th'] ?? 0,0,',','.') ?></p>
</div>
</div>

</div>
</body>
</html>
