<?php
session_start();
include '../includes/sidebar_admin.php';

$conn = mysqli_connect("localhost", "root", "", "sisteminformasi");
if (!$conn) die("Koneksi gagal");

/* =====================
   TAMBAH / EDIT
===================== */
if (isset($_POST['simpan'])) {
    $id    = $_POST['id'];
    $nama  = $_POST['nama_sampah'];
    $harga = $_POST['harga_per_kg'];

    if ($id == "") {
        mysqli_query($conn, "
            INSERT INTO jenis_sampah (nama_sampah, harga_per_kg)
            VALUES ('$nama','$harga')
        ");
    } else {
        mysqli_query($conn, "
            UPDATE jenis_sampah
            SET nama_sampah='$nama', harga_per_kg='$harga'
            WHERE id='$id'
        ");
    }
    header("Location: jenis_sampah.php");
    exit;
}

/* =====================
   SEARCH
===================== */
$keyword = $_GET['q'] ?? '';
$data = mysqli_query($conn,"
    SELECT * FROM jenis_sampah
    WHERE nama_sampah LIKE '%$keyword%'
    ORDER BY id ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Jenis Sampah</title>

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
h1{margin-bottom:20px}
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.search-box{
    width:260px;
    padding:10px 14px;
    border-radius:8px;
    border:1px solid #ddd;
}
.btn-add{
    background:#6366f1;
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:8px;
    cursor:pointer;
}
.card{
    background:#fff;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
}
.card-header{
    padding:16px 20px;
    font-weight:600;
    border-bottom:1px solid #eee;
}
.card-body{padding:20px}
table{width:100%;border-collapse:collapse}
th,td{
    padding:14px 12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}
th{background:#fafafa;color:#6b7280;text-align:left}
.actions{display:flex}
.btn-edit{
    padding:6px 12px;
    border:1px solid #c7d2fe;
    color:#4f46e5;
    border-radius:6px;
    background:none;
    cursor:pointer;
}
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
    width:380px;
    border-radius:14px;
    padding:22px;
}
.form-group{
    margin-bottom:14px;
}
.form-group label{
    display:block;
    font-size:14px;
    margin-bottom:6px;
    font-weight:500;
}
.form-group input{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #ddd;
}
.modal-actions{
    display:flex;
    gap:10px;
    margin-top:10px;
}
.btn-save{
    flex:1;
    background:#6366f1;
    color:#fff;
    border:none;
    padding:10px;
    border-radius:8px;
}
.btn-cancel{
    flex:1;
    background:#e5e7eb;
    border:none;
    padding:10px;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="content">

<h1>Manajemen Jenis Sampah</h1>

<div class="top-bar">
    <form>
        <input class="search-box" name="q"
               placeholder="Cari jenis sampah..." value="<?= $keyword ?>">
    </form>
    <button class="btn-add" onclick="openModal()">＋ Tambah Jenis Sampah</button>
</div>

<div class="card">
<div class="card-header">Daftar Jenis Sampah</div>
<div class="card-body">
<table>
<tr>
<th>ID</th>
<th>Nama Sampah</th>
<th>Harga per Kg (Rp)</th>
<th width="120">Aksi</th>
</tr>

<?php while($r=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= $r['nama_sampah'] ?></td>
<td><?= number_format($r['harga_per_kg'],0,',','.') ?></td>
<td class="actions">
<button class="btn-edit"
onclick="editData('<?= $r['id'] ?>','<?= $r['nama_sampah'] ?>','<?= $r['harga_per_kg'] ?>')">
✏ Edit
</button>
</td>
</tr>
<?php } ?>

</table>
</div>
</div>
</div>

<!-- MODAL -->
<div class="modal" id="modal">
<div class="modal-box">
<h3 id="modalTitle">Tambah Jenis Sampah</h3>
<form method="post">
<input type="hidden" name="id" id="id">

<div class="form-group">
<label>Nama Sampah</label>
<input name="nama_sampah" id="nama" required>
</div>

<div class="form-group">
<label>Harga per Kg</label>
<input type="number" name="harga_per_kg" id="harga" required>
</div>

<div class="modal-actions">
<button class="btn-save" name="simpan">Simpan</button>
<button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
</div>
</form>
</div>
</div>

<script>
function openModal(){
    modal.style.display='flex';
    modalTitle.innerText='Tambah Jenis Sampah';
    id.value=''; nama.value=''; harga.value='';
}
function closeModal(){
    modal.style.display='none';
}
function editData(idv, namav, hargav){
    openModal();
    modalTitle.innerText='Edit Jenis Sampah';
    id.value=idv;
    nama.value=namav;
    harga.value=hargav;
}
window.onclick=e=>{
    if(e.target===modal) closeModal();
}
</script>

</body>
</html>
