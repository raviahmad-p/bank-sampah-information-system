<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../includes/config.php';
include '../includes/sidebar_admin.php';

/* ===============================
   STATISTIK UTAMA
================================ */
$nasabah = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM nasabah"));

$setoran = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM setoran"));

$saldo = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT IFNULL(SUM(saldo),0) total FROM nasabah"));

/* === SETORAN HARI INI (FIX) === */
$hari_ini = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT IFNULL(SUM(total_berat),0) total
    FROM setoran
    WHERE tanggal >= CURDATE()
      AND tanggal < CURDATE() + INTERVAL 1 DAY
"));

$trx_hari_ini = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM setoran
    WHERE tanggal >= CURDATE()
      AND tanggal < CURDATE() + INTERVAL 1 DAY
"));

/* ===============================
   GRAFIK SETORAN BULANAN
================================ */
$qBulanan = mysqli_query($conn,"
    SELECT MONTH(tanggal) bulan, SUM(total_berat) total
    FROM setoran
    WHERE YEAR(tanggal)=YEAR(CURDATE())
    GROUP BY MONTH(tanggal)
    ORDER BY bulan
");

$bulan=[];
$kgBulanan=[];
while($r=mysqli_fetch_assoc($qBulanan)){
    $bulan[] = date('M', mktime(0,0,0,$r['bulan'],1));
    $kgBulanan[] = $r['total'];
}

/* ===============================
   DISTRIBUSI JENIS SAMPAH
================================ */
$qJenis = mysqli_query($conn,"
    SELECT js.nama_sampah, SUM(sd.berat) total
    FROM setoran_detail sd
    JOIN jenis_sampah js ON sd.id_sampah = js.id
    GROUP BY js.nama_sampah
");

$jenis=[];
$kgJenis=[];
while($r=mysqli_fetch_assoc($qJenis)){
    $jenis[] = $r['nama_sampah'];
    $kgJenis[] = $r['total'];
}

/* ===============================
   RIWAYAT SETORAN
================================ */
$qRiwayat = mysqli_query($conn,"
    SELECT s.tanggal, n.nama, s.total_berat, s.total_harga
    FROM setoran s
    JOIN nasabah n ON s.id_nasabah = n.id
    ORDER BY s.tanggal DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f6f9;
    color:#111827;
}
.content{
    margin-left:240px;
    padding:24px;
}
h1{margin-bottom:20px}

/* ===== STAT ===== */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:16px;
    margin-bottom:24px;
}
.stat{
    background:#fff;
    padding:18px;
    border-radius:14px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
}
.stat small{color:#6b7280;}
.stat h2{
    margin:6px 0;
    font-weight:700;
}

/* ===== CHART ===== */
.charts{
    display:grid;
    grid-template-columns:1.5fr 1fr;
    gap:16px;
    margin-bottom:24px;
}
.chart-box{
    background:#fff;
    padding:16px;
    border-radius:14px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
    height:250px;
}
.chart-box h3{
    font-size:14px;
    margin-bottom:8px;
}

/* ===== TABLE ===== */
.table-box{
    background:#fff;
    padding:18px;
    border-radius:14px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
}
.table-box h3{
    margin-bottom:12px;
    font-size:14px;
}
table{
    width:100%;
    border-collapse:collapse;
    font-size:13px;
}
thead{background:#f3f4f6;}
th, td{
    padding:10px;
    border-bottom:1px solid #e5e7eb;
}
tbody tr:hover{background:#f9fafb;}
</style>
</head>

<body>

<div class="content">

<h1>Dashboard Admin</h1>

<!-- STATISTIK -->
<div class="stats">
    <div class="stat">
        <small>Total Nasabah</small>
        <h2><?= $nasabah['total'] ?></h2>
    </div>
    <div class="stat">
        <small>Total Transaksi</small>
        <h2><?= $setoran['total'] ?></h2>
    </div>
    <div class="stat">
        <small>Total Saldo</small>
        <h2>Rp <?= number_format($saldo['total'],0,',','.') ?></h2>
    </div>
    <div class="stat">
        <small>Setoran Hari Ini</small>
        <h2><?= $hari_ini['total'] ?> Kg</h2>
        <small><?= $trx_hari_ini['total'] ?> transaksi</small>
    </div>
</div>

<!-- GRAFIK -->
<div class="charts">

    <div class="chart-box">
        <h3>Setoran Bulanan (Kg)</h3>
        <canvas id="chartBulanan"></canvas>
    </div>

    <div class="chart-box">
        <h3>Distribusi Sampah</h3>
        <canvas id="chartJenis"></canvas>
    </div>

</div>

<!-- RIWAYAT -->
<div class="table-box">
    <h3>Riwayat Setoran Terbaru</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nasabah</th>
                <th>Berat (Kg)</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while($r=mysqli_fetch_assoc($qRiwayat)){ ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d-m-Y H:i', strtotime($r['tanggal'])) ?></td>
                <td><?= $r['nama'] ?></td>
                <td><?= $r['total_berat'] ?></td>
                <td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</div>

<script>
new Chart(document.getElementById('chartBulanan'),{
    type:'bar',
    data:{
        labels:<?= json_encode($bulan) ?>,
        datasets:[{
            data:<?= json_encode($kgBulanan) ?>,
            backgroundColor:'#4ade80',
            borderRadius:6,
            barThickness:18,
            maxBarThickness:22
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{
            x:{ticks:{font:{size:10}}},
            y:{beginAtZero:true,ticks:{font:{size:10}}}
        }
    }
});

new Chart(document.getElementById('chartJenis'),{
    type:'doughnut',
    data:{
        labels:<?= json_encode($jenis) ?>,
        datasets:[{
            data:<?= json_encode($kgJenis) ?>,
            backgroundColor:[
                '#4ade80','#60a5fa','#facc15',
                '#f87171','#c084fc'
            ]
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{position:'bottom'}}
    }
});
</script>

</body>
</html>
