<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php"); exit;
    } elseif ($_SESSION['role'] == 'nasabah') {
        header("Location: nasabah/dashboard.php"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bank Sampah Digital</title>

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:"Segoe UI",Arial,sans-serif;
  background:#f5f6fa;
  color:#1f2937;
}
a{text-decoration:none}

/* ===== ANIMATION ===== */
@keyframes fadeUp{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:translateY(0)}
}
@keyframes slideLeft{
  from{opacity:0;transform:translateX(-40px)}
  to{opacity:1;transform:translateX(0)}
}
@keyframes slideRight{
  from{opacity:0;transform:translateX(40px)}
  to{opacity:1;transform:translateX(0)}
}

/* HEADER */
.header{
  background:#fff;
  padding:20px 60px;
}
.logo{
  font-size:20px;
  font-weight:800;
  animation:fadeUp .8s ease forwards;
}

/* HERO */
.hero{
  background:#fff;
  padding:90px 60px;
  display:grid;
  grid-template-columns:1fr 1fr;
  align-items:center;
  gap:60px;
}
.hero h1{
  font-size:40px;
  font-weight:900;
  line-height:1.3;
  margin-bottom:20px;
  animation:slideLeft .9s ease forwards;
}
.hero p{
  font-size:15px;
  color:#6b7280;
  max-width:520px;
  line-height:1.8;
  margin-bottom:30px;
  animation:slideLeft 1.1s ease forwards;
}
.hero-buttons{
  animation:fadeUp 1.3s ease forwards;
}
.hero-buttons a{
  display:inline-block;
  padding:12px 28px;
  border-radius:6px;
  font-size:14px;
  font-weight:600;
  margin-right:12px;
  transition:.3s ease;
}

/* BUTTON */
.btn-primary{
  background:#eef2ff;
  color:#4f46e5;
  border:1.5px solid #c7d2fe;
}
.btn-primary:hover{
  background:#4f46e5;
  color:#fff;
}
.btn-outline{
  background:#fff;
  border:1.5px solid #d1d5db;
  color:#374151;
}
.btn-outline:hover{
  background:#f3f4f6;
}

/* HERO IMAGE */
.hero-image{
  background:#f9fafb;
  border-radius:12px;
  padding:30px;
  display:flex;
  justify-content:center;
  animation:slideRight 1.2s ease forwards;
}
.hero-image img{
  max-width:100%;
  height:auto;
}

/* MISI */
.misi{
  background:#f5f6fa;
  padding:80px 60px;
  text-align:center;
  animation:fadeUp 1.2s ease forwards;
}
.misi h2{
  font-size:26px;
  font-weight:900;
  margin-bottom:18px;
}
.misi p{
  max-width:900px;
  margin:0 auto;
  font-size:14px;
  color:#6b7280;
  line-height:1.9;
}

/* FOOTER */
.footer{
  background:#fff;
  padding:18px;
  text-align:center;
  color:#9ca3af;
  font-size:13px;
}

/* RESPONSIVE */
@media(max-width:900px){
  .hero{
    grid-template-columns:1fr;
    padding:60px 30px;
  }
  .misi{
    padding:60px 30px;
  }
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="logo">Bank Sampah Digital</div>
</div>

<!-- HERO -->
<section class="hero">
  <div>
    <h1>
      Ubah Sampah Jadi Berkah: Bergabunglah dengan Bank Sampah Digital Kami!
    </h1>
    <p>
      Kami membantu Anda mendaur ulang sampah dengan mudah, mendukung lingkungan bersih,
      dan memberikan imbalan yang menarik untuk setiap kontribusi Anda.
    </p>
    <div class="hero-buttons">
      <a href="login.php" class="btn-primary">Daftar/Login</a>
      <a href="edukasi.php" class="btn-outline">Edukasi</a>
    </div>
  </div>

  <div class="hero-image">
    <img src="assets/img/logo.png" alt="Ilustrasi Bank Sampah">
  </div>
</section>

<!-- MISI -->
<section class="misi">
  <h2>Misi Kami: Lingkungan Bersih untuk Masa Depan</h2>
  <p>
    Bank Sampah Digital berkomitmen untuk menciptakan sistem pengelolaan sampah yang
    efektif dan berkelanjutan, memberdayakan masyarakat untuk berpartisipasi aktif
    dalam daur ulang, serta memberikan dampak positif bagi lingkungan dan ekonomi lokal.
    Kami percaya setiap sampah memiliki potensi dan setiap individu memiliki peran penting.
  </p>
</section>

</div>

</body>
</html>
