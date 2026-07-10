<?php
session_start();
include '../includes/config.php';
include '../includes/sidebar_admin.php';

/* ===============================
   KEAMANAN
================================ */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ===============================
   TAMBAH EDUKASI
================================ */
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);

    mysqli_query($conn, "INSERT INTO edukasi (judul, isi, created_at) VALUES ('$judul','$isi',NOW())");
    header("Location: edukasi.php");
    exit;
}

/* ===============================
   UPDATE EDUKASI
================================ */
if (isset($_POST['update'])) {
    $id    = $_POST['id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);

    mysqli_query($conn, "UPDATE edukasi SET judul='$judul', isi='$isi' WHERE id='$id'");
    header("Location: edukasi.php");
    exit;
}

/* ===============================
   HAPUS EDUKASI
================================ */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM edukasi WHERE id='$id'");
    header("Location: edukasi.php");
    exit;
}

/* ===============================
   EDIT MODE
================================ */
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM edukasi WHERE id='$id'"));
}

/* ===============================
   DATA EDUKASI
================================ */
$data = mysqli_query($conn, "SELECT * FROM edukasi ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Edukasi</title>

<style>
:root{
  --primary:#4f46e5;
  --success:#16a34a;
  --danger:#dc2626;
  --gray:#6b7280;
  --bg:#f4f6f9;
}

*{box-sizing:border-box}
body{
  margin:0;
  font-family:Segoe UI,Arial,sans-serif;
  background:var(--bg);
}

.content{
  margin-left:250px;
  padding:32px;
}

h1{
  font-size:22px;
  font-weight:700;
  margin-bottom:4px;
}
.subtitle{
  color:#6b7280;
  font-size:14px;
  margin-bottom:22px;
}

/* CARD */
.card{
  background:#fff;
  padding:24px;
  border-radius:14px;
  box-shadow:0 10px 20px rgba(0,0,0,.06);
  margin-bottom:28px;
}

/* FORM */
label{
  font-size:13px;
  font-weight:600;
  color:#374151;
}
input, textarea{
  width:100%;
  padding:11px 14px;
  margin:6px 0 16px;
  border:1px solid #e5e7eb;
  border-radius:10px;
  font-size:14px;
}
input:focus, textarea:focus{
  outline:none;
  border-color:var(--primary);
  box-shadow:0 0 0 2px rgba(79,70,229,.15);
}
textarea{
  height:130px;
  resize:vertical;
}

/* BUTTON */
button{
  background:var(--success);
  color:#fff;
  border:none;
  padding:10px 22px;
  border-radius:10px;
  font-weight:600;
  cursor:pointer;
}
button:hover{opacity:.9}

.btn-edit{background:var(--primary)}
.btn-hapus{background:var(--danger)}
.btn-cancel{
  background:#e5e7eb;
  color:#374151;
  padding:10px 18px;
  border-radius:10px;
  text-decoration:none;
  font-size:14px;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
}
th{
  background:#f9fafb;
  text-align:left;
  padding:14px;
  font-size:13px;
  color:#6b7280;
}
td{
  padding:14px;
  border-bottom:1px solid #e5e7eb;
  font-size:14px;
}
tr:hover{background:#f9fafb}

/* ACTION */
.action a{
  display:inline-block;
  padding:6px 12px;
  border-radius:8px;
  color:#fff;
  font-size:12px;
  text-decoration:none;
  margin-right:4px;
}
</style>
</head>

<body>

<div class="content">

<h1>Manajemen Edukasi</h1>
<div class="subtitle">Kelola konten edukasi untuk ditampilkan di landing page</div>

<!-- FORM -->
<div class="card">
<form method="post">

<?php if ($edit): ?>
  <input type="hidden" name="id" value="<?= $edit['id'] ?>">
<?php endif ?>

<label>Judul Edukasi</label>
<input type="text" name="judul" required value="<?= $edit['judul'] ?? '' ?>">

<label>Isi Edukasi</label>
<textarea name="isi" required><?= $edit['isi'] ?? '' ?></textarea>

<?php if ($edit): ?>
  <button name="update">Update Edukasi</button>
  <a href="edukasi.php" class="btn-cancel">Batal</a>
<?php else: ?>
  <button name="tambah">Tambah Edukasi</button>
<?php endif ?>

</form>
</div>

<!-- TABLE -->
<div class="card">
<table>
<tr>
  <th>No</th>
  <th>Judul</th>
  <th>Tanggal</th>
  <th>Aksi</th>
</tr>

<?php $no=1; while($r=mysqli_fetch_assoc($data)): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= htmlspecialchars($r['judul']) ?></td>
  <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
  <td class="action">
    <a href="?edit=<?= $r['id'] ?>" class="btn-edit">Edit</a>
    <a href="?hapus=<?= $r['id'] ?>" class="btn-hapus"
       onclick="return confirm('Yakin ingin menghapus edukasi ini?')">
       Hapus
    </a>
  </td>
</tr>
<?php endwhile ?>
</table>
</div>

</div>

</body>
</html>
