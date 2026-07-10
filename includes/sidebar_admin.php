<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* keamanan: hanya admin */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$current = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{box-sizing:border-box}
body{margin:0;font-family:Segoe UI,Arial,sans-serif}

.sidebar{
  width:250px;
  height:100vh;
  background:#ffffff;
  border-right:1px solid #e5e7eb;
  position:fixed;
  left:0;top:0;
  display:flex;
  flex-direction:column;
}

/* LOGO */
.logo{
  display:flex;
  align-items:center;
  gap:12px;
  padding:24px 20px;
  font-weight:700;
  color:#111827;
}
.logo-icon{
  width:36px;height:36px;
  border-radius:10px;
  background:linear-gradient(135deg,#6366f1,#4f46e5);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:18px
}
.logo small{color:#6b7280;font-weight:500}

/* MENU */
.menu{padding:10px}
.menu a{
  display:flex;
  align-items:center;
  gap:12px;
  padding:11px 14px;
  margin-bottom:6px;
  border-radius:10px;
  color:#374151;
  font-size:14px;
  text-decoration:none;
  transition:.2s ease
}
.menu a i{width:18px;text-align:center}
.menu a:hover{background:#f1f5f9}

.menu a.active{
  background:linear-gradient(135deg,#6366f1,#4f46e5);
  color:#fff;
}
.menu a.active i{color:#fff}

/* FOOTER */
.sidebar-footer{
  margin-top:auto;
  padding:18px 20px;
  border-top:1px solid #e5e7eb
}
.logout{
  display:flex;align-items:center;gap:10px;
  color:#6b7280;font-size:14px;text-decoration:none
}
.logout:hover{color:#ef4444}
</style>

<div class="sidebar">

  <!-- LOGO -->
  <div class="logo">
    <div class="logo-icon"><i class="fa-solid fa-recycle"></i></div>
    <div>
      Bank<br><small>Sampah</small>
    </div>
  </div>

  <!-- MENU -->
  <div class="menu">
    <a href="dashboard.php" class="<?= $current=='dashboard.php'?'active':'' ?>">
      <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="data_nasabah.php" class="<?= $current=='data_nasabah.php'?'active':'' ?>">
      <i class="fa-solid fa-users"></i> Data Nasabah
    </a>

    <a href="jenis_sampah.php" class="<?= $current=='jenis_sampah.php'?'active':'' ?>">
      <i class="fa-solid fa-recycle"></i> Jenis Sampah
    </a>

    <a href="setoran.php" class="<?= $current=='setoran.php'?'active':'' ?>">
      <i class="fa-solid fa-box"></i> Setoran
    </a>

    <a href="tarik_saldo.php" class="<?= $current=='tarik_saldo.php'?'active':'' ?>">
      <i class="fa-solid fa-wallet"></i> Tarik Saldo
    </a>

    <a href="laporan.php" class="<?= $current=='laporan.php'?'active':'' ?>">
      <i class="fa-solid fa-chart-line"></i> Laporan
    <a href="edukasi.php" class="<?= $current=='edukasi.php'?'active':'' ?>">
    <i class="fa-solid fa-book-open"></i> Edukasi
  </a>
  </div>
  <li>
</li>


  <!-- FOOTER -->
  <div class="sidebar-footer">
    <a href="../logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </div>

</div>
