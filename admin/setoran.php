<?php
session_start();
include '../includes/config.php';
include '../includes/sidebar_admin.php';

$aksi = $_GET['aksi'] ?? '';
$id   = intval($_GET['id'] ?? 0);

/* =======================
   DATA MASTER
======================= */
$nasabah = mysqli_query($conn,"SELECT * FROM nasabah ORDER BY nama");
$jenisQ  = mysqli_query($conn,"SELECT * FROM jenis_sampah ORDER BY nama_sampah");
$jenis   = [];
while($j=mysqli_fetch_assoc($jenisQ)) $jenis[]=$j;

/* =======================
   SIMPAN SETORAN
======================= */
if(isset($_POST['simpan'])){
    mysqli_begin_transaction($conn);
    try{
        $id_nasabah=$_POST['id_nasabah'];

        mysqli_query($conn,"INSERT INTO setoran(id_nasabah,tanggal,total_berat,total_harga)
                            VALUES('$id_nasabah', CURDATE(), 0, 0)");
        $id_setoran=mysqli_insert_id($conn);

        $tb=0;$th=0;
        foreach($_POST['id_sampah'] as $i=>$id_sampah){
            $berat=floatval($_POST['berat'][$i]);
            if($berat<=0)continue;

            foreach($jenis as $js) if($js['id']==$id_sampah) $harga=$js['harga_per_kg'];
            $sub=$berat*$harga;

            mysqli_query($conn,"INSERT INTO setoran_detail
                (id_setoran,id_sampah,berat,harga_per_kg,subtotal)
                VALUES('$id_setoran','$id_sampah','$berat','$harga','$sub')");

            $tb+=$berat; 
            $th+=$sub;
        }

        mysqli_query($conn,"UPDATE setoran SET total_berat='$tb',total_harga='$th' WHERE id='$id_setoran'");
        mysqli_query($conn,"UPDATE nasabah SET saldo=saldo+'$th' WHERE id='$id_nasabah'");

        mysqli_commit($conn);
        $_SESSION['notif']="Setoran berhasil ditambahkan.";
        header("Location:setoran.php"); exit;
    }catch(Exception $e){
        mysqli_rollback($conn);
        $_SESSION['notif']="Gagal menyimpan setoran.";
        header("Location:setoran.php"); exit;
    }
}

/* =======================
   UPDATE SETORAN
======================= */
if(isset($_POST['update'])){
    mysqli_begin_transaction($conn);
    try{
        $id_setoran=$_POST['id_setoran'];

        $old=mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT id_nasabah,total_harga FROM setoran WHERE id='$id_setoran'
        "));

        mysqli_query($conn,"UPDATE nasabah SET saldo=saldo-'{$old['total_harga']}'
                            WHERE id='{$old['id_nasabah']}'");
        mysqli_query($conn,"DELETE FROM setoran_detail WHERE id_setoran='$id_setoran'");

        $tb=0;$th=0;
        foreach($_POST['id_sampah'] as $i=>$id_sampah){
            $berat=floatval($_POST['berat'][$i]);
            if($berat<=0)continue;

            foreach($jenis as $js) if($js['id']==$id_sampah) $harga=$js['harga_per_kg'];
            $sub=$berat*$harga;

            mysqli_query($conn,"INSERT INTO setoran_detail
                (id_setoran,id_sampah,berat,harga_per_kg,subtotal)
                VALUES('$id_setoran','$id_sampah','$berat','$harga','$sub')");

            $tb+=$berat; 
            $th+=$sub;
        }

        mysqli_query($conn,"UPDATE setoran SET total_berat='$tb',total_harga='$th'
                            WHERE id='$id_setoran'");
        mysqli_query($conn,"UPDATE nasabah SET saldo=saldo+'$th'
                            WHERE id='{$old['id_nasabah']}'");

        mysqli_commit($conn);
        $_SESSION['notif']="Setoran berhasil diperbarui.";
        header("Location:setoran.php"); exit;
    }catch(Exception $e){
        mysqli_rollback($conn);
        $_SESSION['notif']="Gagal update setoran.";
        header("Location:setoran.php"); exit;
    }
}

/* =======================
   HAPUS SETORAN
======================= */
if($aksi=='hapus' && $id){
    mysqli_begin_transaction($conn);
    $d=mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT id_nasabah,total_harga FROM setoran WHERE id='$id'
    "));
    mysqli_query($conn,"DELETE FROM setoran_detail WHERE id_setoran='$id'");
    mysqli_query($conn,"DELETE FROM setoran WHERE id='$id'");
    mysqli_query($conn,"UPDATE nasabah SET saldo=saldo-'{$d['total_harga']}'
                        WHERE id='{$d['id_nasabah']}'");
    mysqli_commit($conn);
    $_SESSION['notif']="Setoran berhasil dihapus.";
    header("Location:setoran.php"); exit;
}

/* =======================
   RIWAYAT
======================= */
$riwayat=mysqli_query($conn,"
    SELECT s.*,n.nama FROM setoran s
    JOIN nasabah n ON s.id_nasabah=n.id
    ORDER BY s.id DESC
");

/* =======================
   DETAIL / EDIT
======================= */
$data=null; 
$detail=[];
if(in_array($aksi,['detail','edit'])){
    $data=mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT s.*,n.nama FROM setoran s
        JOIN nasabah n ON s.id_nasabah=n.id 
        WHERE s.id='$id'
    "));
    $q=mysqli_query($conn,"
        SELECT sd.*,j.nama_sampah 
        FROM setoran_detail sd
        JOIN jenis_sampah j ON sd.id_sampah=j.id
        WHERE sd.id_setoran='$id'
    ");
    while($r=mysqli_fetch_assoc($q)) $detail[]=$r;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Setoran</title>
<style>
body{margin:0;background:#f5f7fb;font-family:Segoe UI}
.content{margin-left:240px;padding:30px}
.btn{padding:6px 12px;font-size:13px;border-radius:6px;border:1px solid #ddd;background:#fff;cursor:pointer}
.btn-primary{background:#6366f1;color:#fff;border:none}
.btn-danger{background:#ef4444;color:#fff;border:none}
.btn-outline{border:1px solid #c7d2fe;color:#4f46e5}
.card{background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.05)}
.card-header{padding:16px;font-weight:600;border-bottom:1px solid #eee}
.card-body{padding:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:12px;border-bottom:1px solid #eee;font-size:14px}
th{background:#fafafa;color:#6b7280}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.4);display:flex;justify-content:center;align-items:center}
.modal-box{background:#fff;width:650px;border-radius:14px;padding:20px}
input,select{width:100%;padding:8px;border-radius:6px;border:1px solid #ddd}
.item-row{display:grid;grid-template-columns:2fr 1fr auto;gap:8px;margin-bottom:8px}

/* ==== DETAIL STYLE ==== */
.detail-info{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:8px 20px;
}
.detail-table{
  border:1px solid #eee;
  border-radius:10px;
  overflow:hidden;
  margin-top:15px;
}
.total-setoran{
  margin-top:15px;
  text-align:right;
  font-weight:600;
  font-size:16px;
}
.total-setoran span{color:#16a34a}
.modal-footer{
  display:flex;
  justify-content:flex-end;
  margin-top:20px;
}
</style>
</head>

<body>
<div class="content">

<?php if(isset($_SESSION['notif'])): ?>
<div id="notif" style="
 position:fixed; top:20px; right:20px;
 background:#22c55e; color:white;
 padding:12px 18px; border-radius:10px;
 box-shadow:0 6px 15px rgba(0,0,0,.15);
 z-index:9999;">
 <?= $_SESSION['notif']; unset($_SESSION['notif']); ?>
</div>
<script>
setTimeout(()=>document.getElementById('notif')?.remove(),3000);
</script>
<?php endif; ?>

<h2>Setoran</h2>
<button class="btn btn-primary" onclick="location.href='?aksi=tambah'">＋ Tambah Setoran</button>

<div class="card" style="margin-top:20px">
<div class="card-header">Riwayat Setoran</div>
<div class="card-body">
<table>
<tr>
<th>ID</th><th>Nasabah</th><th>Tanggal</th><th>Total Berat</th><th>Total Harga</th><th>Aksi</th>
</tr>
<?php while($r=mysqli_fetch_assoc($riwayat)): ?>
<tr>
<td><?= str_pad($r['id'],3,'0',STR_PAD_LEFT) ?></td>
<td><?= $r['nama'] ?></td>
<td><?= date('d-m-Y',strtotime($r['tanggal'])) ?></td>
<td><?= $r['total_berat'] ?> kg</td>
<td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
<td>
<a class="btn" href="?aksi=detail&id=<?= $r['id'] ?>">Detail</a>
<a class="btn btn-outline" href="?aksi=edit&id=<?= $r['id'] ?>">Edit</a>
<a class="btn btn-danger" href="?aksi=hapus&id=<?= $r['id'] ?>" onclick="return confirm('Hapus setoran?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</div>

<?php if(in_array($aksi,['tambah','edit'])): ?>
<div class="modal">
<div class="modal-box">
<h3><?= $aksi=='edit'?'Edit':'Tambah' ?> Setoran</h3><hr>
<form method="post">
<?php if($aksi=='edit'): ?>
<input type="hidden" name="id_setoran" value="<?= $id ?>">
<label>Nasabah</label>
<input value="<?= $data['nama'] ?>" readonly><br><br>
<?php else: ?>
<label>Nasabah</label>
<select name="id_nasabah" required>
<option value="">-- Pilih Nasabah --</option>
<?php while($n=mysqli_fetch_assoc($nasabah)): ?>
<option value="<?= $n['id'] ?>"><?= $n['nama'] ?></option>
<?php endwhile; ?>
</select><br><br>
<?php endif; ?>

<div id="items">
<?php foreach($detail as $d): ?>
<div class="item-row">
<select name="id_sampah[]">
<?php foreach($jenis as $j): ?>
<option value="<?= $j['id'] ?>" <?= $j['id']==$d['id_sampah']?'selected':'' ?>>
<?= $j['nama_sampah'] ?>
</option>
<?php endforeach; ?>
</select>
<input type="number" step="0.01" name="berat[]" value="<?= $d['berat'] ?>">
<button type="button" onclick="this.parentElement.remove()">✖</button>
</div>
<?php endforeach; ?>
</div>

<button type="button" class="btn" onclick="addItem()">+ Tambah Item</button><br><br>
<button class="btn btn-primary" name="<?= $aksi=='edit'?'update':'simpan' ?>">Simpan</button>
<a class="btn" href="setoran.php">Tutup</a>
</form>
</div>
</div>
<?php endif; ?>

<?php if($aksi=='detail'): ?>
<div class="modal">
<div class="modal-box">
<h3>Detail Setoran</h3><hr>

<div class="detail-info">
  <div><b>ID Transaksi:</b> <?= str_pad($data['id'],3,'0',STR_PAD_LEFT) ?></div>
  <div><b>Tanggal Setoran:</b> <?= date('d F Y',strtotime($data['tanggal'])) ?></div>
  <div><b>Nama Nasabah:</b> <?= $data['nama'] ?></div>
</div>

<div class="detail-table">
<table>
<tr>
<th>Jenis Sampah</th>
<th>Berat (kg)</th>
<th>Harga/kg</th>
<th>Subtotal</th>
</tr>
<?php foreach($detail as $d): ?>
<tr>
<td><?= $d['nama_sampah'] ?></td>
<td><?= number_format($d['berat'],2) ?></td>
<td>Rp <?= number_format($d['harga_per_kg'],0,',','.') ?></td>
<td>Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="total-setoran">
Total Nilai Setoran:
<span>Rp <?= number_format($data['total_harga'],0,',','.') ?></span>
</div>

<div class="modal-footer">
<a href="setoran.php" class="btn btn-primary">Tutup</a>
</div>
</div>
</div>
<?php endif; ?>

<script>
const jenis=<?= json_encode($jenis) ?>;
function addItem(){
 let d=document.createElement('div');
 d.className='item-row';
 let opt='';
 jenis.forEach(j=>opt+=`<option value="${j.id}">${j.nama_sampah}</option>`);
 d.innerHTML=`<select name="id_sampah[]">${opt}</select>
              <input type="number" step="0.01" name="berat[]" placeholder="Kg">
              <button type="button" onclick="this.parentElement.remove()">✖</button>`;
 items.appendChild(d);
}
</script>
</body>
</html>
