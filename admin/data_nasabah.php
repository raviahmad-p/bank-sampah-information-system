<?php
session_start();
include '../includes/config.php';
include '../includes/sidebar_admin.php';

/* ================== STATUS BADGE ================== */
function statusBadge($status){
    if($status=='Aktif') return ['Aktif','aktif'];
    if($status=='Kurang Aktif') return ['Kurang Aktif','warning'];
    return ['Tidak Aktif','nonaktif'];
}

/* ================== PAGINATION ================== */
$limit  = 5;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

/* ================== SEARCH & FILTER ================== */
$search = $_GET['search'] ?? '';
$filter = $_GET['filter_status'] ?? '';

$where = [];
if($search!=''){
    $s = mysqli_real_escape_string($conn,$search);
    $where[] = "(n.nama LIKE '%$s%' OR n.no_hp LIKE '%$s%')";
}
if($filter!=''){
    $f = mysqli_real_escape_string($conn,$filter);
    $where[] = "n.status='$f'";
}
$whereSql = count($where)?'WHERE '.implode(' AND ',$where):'';

/* ================== TOTAL ================== */
$total = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) total FROM nasabah n $whereSql
"))['total'];
$totalPage = ceil($total/$limit);

/* ================== DATA ================== */
$data = mysqli_query($conn,"
    SELECT n.*,u.username
    FROM nasabah n JOIN users u ON n.user_id=u.id
    $whereSql
    ORDER BY n.id DESC
    LIMIT $limit OFFSET $offset
");

/* ================== TAMBAH / EDIT ================== */
$aksi = $_GET['aksi'] ?? '';
$id_edit = (int)($_GET['id'] ?? 0);
$data_edit = null;

if($aksi=='edit'){
    $q = mysqli_query($conn,"
        SELECT n.*,u.username
        FROM nasabah n JOIN users u ON n.user_id=u.id
        WHERE n.id=$id_edit
    ");
    $data_edit = mysqli_fetch_assoc($q);
}

if(isset($_POST['simpan'])){
    $nama     = mysqli_real_escape_string($conn,$_POST['nama']);
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $alamat   = mysqli_real_escape_string($conn,$_POST['alamat']);
    $no_hp    = mysqli_real_escape_string($conn,$_POST['no_hp']);
    $password = $_POST['password'];
    $status   = 'Aktif';

    if($_POST['id']==''){
        $cek = mysqli_query($conn,"SELECT id FROM users WHERE username='$username'");
        if(mysqli_num_rows($cek)>0){
            echo "<script>alert('Username sudah digunakan');history.back();</script>";
            exit;
        }

        $hash = password_hash($password,PASSWORD_DEFAULT);

        mysqli_begin_transaction($conn);
        try{
            mysqli_query($conn,"
                INSERT INTO users(username,password,role)
                VALUES('$username','$hash','nasabah')
            ");
            $uid = mysqli_insert_id($conn);

            mysqli_query($conn,"
                INSERT INTO nasabah(user_id,nama,alamat,no_hp,status,saldo,tanggal_daftar)
                VALUES($uid,'$nama','$alamat','$no_hp','$status',0,CURDATE())
            ");

            mysqli_commit($conn);
        }catch(Exception $e){
            mysqli_rollback($conn);
            die('Gagal tambah nasabah');
        }

    }else{
        $id = (int)$_POST['id'];

        mysqli_query($conn,"
            UPDATE nasabah
            SET nama='$nama',alamat='$alamat',no_hp='$no_hp'
            WHERE id=$id
        ");

        if($password!=''){
            $hash = password_hash($password,PASSWORD_DEFAULT);
            mysqli_query($conn,"
                UPDATE users u
                JOIN nasabah n ON u.id=n.user_id
                SET u.username='$username',u.password='$hash'
                WHERE n.id=$id
            ");
        }else{
            mysqli_query($conn,"
                UPDATE users u
                JOIN nasabah n ON u.id=n.user_id
                SET u.username='$username'
                WHERE n.id=$id
            ");
        }
    }

    header("Location: data_nasabah.php");
    exit;
}

/* ================== HAPUS NASABAH ================== */
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    $q = mysqli_query($conn,"SELECT user_id FROM nasabah WHERE id=$id");
    if($r = mysqli_fetch_assoc($q)){
        $uid = $r['user_id'];
        mysqli_query($conn,"DELETE FROM nasabah WHERE id=$id");
        mysqli_query($conn,"DELETE FROM users WHERE id=$uid");
    }
    header("Location: data_nasabah.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Nasabah</title>
<style>
body{margin:0;background:#f5f7fb;font-family:Segoe UI}
.content{margin-left:240px;padding:30px}
.card{background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.05)}
.card-body{padding:20px}
.top{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.filter{display:flex;gap:10px}
input,select{padding:10px;border-radius:8px;border:1px solid #ddd}
table{width:100%;border-collapse:collapse}
th,td{padding:12px;border-bottom:1px solid #eee;font-size:14px}
th{background:#fafafa;color:#6b7280;text-align:left}
.badge{padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600}
.aktif{background:#dcfce7;color:#166534}
.warning{background:#fef3c7;color:#92400e}
.nonaktif{background:#fee2e2;color:#991b1b}
.btn-add{background:#6366f1;color:#fff;padding:8px 14px;border-radius:8px;text-decoration:none}
.btn-edit{border:1px solid #ddd;padding:6px 12px;border-radius:6px;text-decoration:none;color:#111}
.btn-delete{background:#ef4444;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none}
.pagination{text-align:center;margin-top:16px}
.pagination a{margin:0 4px;padding:6px 12px;border-radius:6px;border:1px solid #ddd;text-decoration:none}
.active{background:#6366f1;color:#fff}

/* ===== MODAL ===== */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center}
.modal-box{background:#fff;width:420px;border-radius:14px;padding:20px}
.modal-header{display:flex;justify-content:space-between;align-items:center}
.close{text-decoration:none;font-size:22px}
.modal-form input{width:100%;padding:10px;margin-top:10px;border-radius:8px;border:1px solid #ddd}
.modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px}
.btn-cancel{padding:8px 14px;border-radius:8px;border:1px solid #ddd;text-decoration:none}
.btn-primary{background:#6366f1;color:#fff;padding:8px 14px;border:none;border-radius:8px}
</style>
</head>
<body>

<div class="content">
<h2>Data Nasabah</h2>

<div class="card">
<div class="card-body">

<form class="top" method="get">
<div class="filter">
<input name="search" placeholder="Cari nasabah..." value="<?= htmlspecialchars($search) ?>">
<select name="filter_status">
<option value="">Semua Status</option>
<option value="Aktif" <?= $filter=='Aktif'?'selected':'' ?>>Aktif</option>
<option value="Kurang Aktif" <?= $filter=='Kurang Aktif'?'selected':'' ?>>Kurang Aktif</option>
<option value="Tidak Aktif" <?= $filter=='Tidak Aktif'?'selected':'' ?>>Tidak Aktif</option>
</select>
<button class="btn-edit">Cari</button>
</div>
<a class="btn-add" href="?aksi=tambah">+ Tambah Nasabah</a>
</form>

<table>
<tr>
<th>Nama</th><th>No HP</th><th>Alamat</th>
<th>Tanggal</th><th>Saldo</th><th>Status</th><th>Aksi</th>
</tr>

<?php while($r=mysqli_fetch_assoc($data)):
[$label,$cls]=statusBadge($r['status']); ?>
<tr>
<td><?= $r['nama'] ?></td>
<td><?= $r['no_hp'] ?></td>
<td><?= $r['alamat'] ?></td>
<td><?= date('d-m-Y',strtotime($r['tanggal_daftar'])) ?></td>
<td>Rp <?= number_format($r['saldo'],0,',','.') ?></td>
<td><span class="badge <?= $cls ?>"><?= $label ?></span></td>
<td style="display:flex;gap:6px">
<a class="btn-edit" href="?aksi=edit&id=<?= $r['id'] ?>">Edit</a>
<a class="btn-delete" href="?hapus=<?= $r['id'] ?>" onclick="return confirm('Yakin hapus nasabah ini?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<div class="pagination">
<?php for($i=1;$i<=$totalPage;$i++): ?>
<a class="<?= $i==$page?'active':'' ?>" href="?page=<?= $i ?>&search=<?= $search ?>&filter_status=<?= $filter ?>"><?= $i ?></a>
<?php endfor; ?>
</div>

</div>
</div>
</div>

<?php if($aksi=='tambah'||$aksi=='edit'): ?>
<div class="modal-overlay">
<div class="modal-box">
<div class="modal-header">
<h3><?= $aksi=='edit'?'Edit Nasabah':'Tambah Nasabah' ?></h3>
<a class="close" href="data_nasabah.php">×</a>
</div>

<form method="post" class="modal-form">
<input type="hidden" name="id" value="<?= $data_edit['id'] ?? '' ?>">
<input name="nama" placeholder="Nama Lengkap" required value="<?= $data_edit['nama'] ?? '' ?>">
<input name="username" placeholder="Username" required value="<?= $data_edit['username'] ?? '' ?>">
<input type="password" name="password" placeholder="<?= $aksi=='edit'?'Password (opsional)':'Password' ?>" <?= $aksi=='tambah'?'required':'' ?>>
<input name="alamat" placeholder="Alamat" required value="<?= $data_edit['alamat'] ?? '' ?>">
<input name="no_hp" placeholder="No HP" required value="<?= $data_edit['no_hp'] ?? '' ?>">

<div class="modal-actions">
<a href="data_nasabah.php" class="btn-cancel">Batal</a>
<button name="simpan" class="btn-primary"><?= $aksi=='edit'?'Simpan':'Tambah' ?></button>
</div>
</form>
</div>
</div>
<?php endif; ?>

</body>
</html>
