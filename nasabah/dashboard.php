<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../includes/config.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'nasabah') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* DATA NASABAH */
$qNasabah = mysqli_query($conn,"
    SELECT n.*
    FROM nasabah n
    WHERE n.user_id='$id_user'
");
$nasabah = mysqli_fetch_assoc($qNasabah);

/* RIWAYAT SETORAN */
$riwayat = mysqli_query($conn,"
    SELECT *
    FROM setoran
    WHERE id_nasabah='{$nasabah['id']}'
    ORDER BY tanggal DESC
    LIMIT 5
");

/* HARGA SAMPAH REALTIME */
$harga = mysqli_query($conn,"
    SELECT nama_sampah, harga_per_kg
    FROM jenis_sampah
    ORDER BY nama_sampah ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Nasabah</title>

<style>
body{
    margin:0;
    background:#f5f7fb;
    font-family:'Segoe UI',sans-serif;
}
.content{
    margin-left:260px;
    padding:32px;
}
h2{margin:0}
.subtext{
    color:#6b7280;
    margin-bottom:24px;
}

/* GRID */
.top-grid{
    display:grid;
    grid-template-columns:1.5fr 1fr;
    gap:24px;
}

/* CARD */
.card{
    background:#fff;
    padding:24px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.05);
}

/* SALDO */
.saldo-wrap{
    padding-top:8px;
}
.saldo-title{
    font-size:14px;
    color:#6b7280;
    margin-bottom:6px;
}
.saldo-amount{
    font-size:36px;
    font-weight:800;
    color:#000;
    margin:0;
}

/* PRICE */
.price-item{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #e5e7eb;
    font-size:14px;
}

/* TABLE */
.table-card{margin-top:32px}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:16px;
}
th{
    text-align:left;
    background:#f3f4f6;
    color:#374151;
    font-size:13px;
    padding:12px 10px;
}
td{
    padding:12px 10px;
    border-top:1px solid #e5e7eb;
    font-size:14px;
}
tr:hover{background:#f9fafb}

/* BUTTON */
.action-btn{
    padding:6px 14px;
    border-radius:10px;
    border:1px solid #e5e7eb;
    background:#fff;
    cursor:pointer;
    transition:.2s;
}
.action-btn:hover{
    background:#6366f1;
    color:#fff;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.45);
    justify-content:center;
    align-items:center;
    z-index:999;
}
.modal-box{
    background:#fff;
    padding:24px;
    width:360px;
    border-radius:16px;
}
.modal-box h3{margin-top:0}
.btn-close{
    width:100%;
    padding:10px;
    margin-top:14px;
    border:none;
    border-radius:10px;
    background:#6366f1;
    color:#fff;
    cursor:pointer;
}
</style>
</head>

<body>

<?php include '../includes/sidebar_nasabah.php'; ?>

<div class="content">

<h2>Dashboard Nasabah</h2>
<div class="subtext">
Selamat datang, <b><?= htmlspecialchars($nasabah['nama']) ?></b>
</div>

<!-- GRID -->
<div class="top-grid">

<!-- SALDO -->
<div class="card">
    <div class="saldo-wrap">
        <div class="saldo-title">Saldo Saat Ini</div>
        <div class="saldo-amount">
            Rp <?= number_format($nasabah['saldo'],0,',','.') ?>
        </div>
    </div>
</div>

<!-- HARGA SAMPAH -->
<div class="card">
    <div class="subtext" style="margin-bottom:10px;">Harga Sampah</div>

    <?php while($h=mysqli_fetch_assoc($harga)): ?>
    <div class="price-item">
        <span>♻ <?= htmlspecialchars($h['nama_sampah']) ?></span>
        <span>Rp <?= number_format($h['harga_per_kg'],0,',','.') ?>/kg</span>
    </div>
    <?php endwhile; ?>
</div>

</div>

<!-- RIWAYAT -->
<div class="card table-card">
<h3>Riwayat Setoran Terbaru</h3>
<p class="subtext">Daftar setoran sampah terbaru Anda</p>

<table>
<tr>
<th style="width:25%">Tanggal</th>
<th style="width:20%">Berat</th>
<th style="width:25%">Total Nilai</th>
<th style="width:30%">Aksi</th>
</tr>

<?php while($r=mysqli_fetch_assoc($riwayat)): ?>
<tr>
<td><?= date('d M Y',strtotime($r['tanggal'])) ?></td>
<td><?= number_format($r['total_berat'],1) ?> kg</td>
<td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
<td>
<button class="action-btn"
onclick="lihatDetail(
'<?= $r['id'] ?>',
'<?= date('d M Y',strtotime($r['tanggal'])) ?>',
'<?= number_format($r['total_berat'],1) ?>',
'<?= number_format($r['total_harga'],0,',','.') ?>'
)">
👁 Lihat Detail
</button>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>

<!-- MODAL DETAIL -->
<div class="modal" id="modal">
<div class="modal-box">
<h3>Detail Setoran</h3>
<div id="detail"></div>
<button class="btn-close" onclick="tutup()">Tutup</button>
</div>
</div>

<script>
function lihatDetail(id,tgl,berat,harga){
    document.getElementById('detail').innerHTML = `
        <table style="width:100%">
            <tr><td><b>ID Setoran</b></td><td>${id}</td></tr>
            <tr><td><b>Tanggal</b></td><td>${tgl}</td></tr>
            <tr><td><b>Total Berat</b></td><td>${berat} kg</td></tr>
            <tr><td><b>Total Nilai</b></td><td>Rp ${harga}</td></tr>
        </table>
    `;
    document.getElementById('modal').style.display='flex';
}
function tutup(){
    document.getElementById('modal').style.display='none';
}
</script>

</body>
</html>
