<?php
session_start();
include 'includes/config.php';

/* AMBIL DATA EDUKASI */
$edukasi = mysqli_query($conn,"
    SELECT * FROM edukasi
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edukasi Bank Sampah</title>

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:Inter,system-ui,Arial,sans-serif;
  background:#f9fafb;
  color:#111827;
}
a{text-decoration:none}

:root{
  --primary:#16a34a;
  --muted:#6b7280;
  --border:#e5e7eb;
  --card:#ffffff;
}

/* ================= ANIMATION ================= */
@keyframes fadeUp{
  from{
    opacity:0;
    transform:translateY(24px);
  }
  to{
    opacity:1;
    transform:translateY(0);
  }
}

/* HEADER */
.header{
  background:#fff;
  border-bottom:1px solid var(--border);
  padding:18px 56px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  animation:fadeUp .6s ease;
}
.logo{
  font-weight:800;
  font-size:18px;
  color:var(--primary);
}
.nav a{
  margin-left:12px;
  padding:8px 16px;
  font-size:13px;
  font-weight:600;
  border-radius:8px;
  border:1px solid var(--border);
  color:#374151;
}
.nav a.login{
  background:var(--primary);
  color:#fff;
  border:none;
}

/* HERO */
.hero{
  background:#ffffff;
  padding:72px 56px;
  text-align:center;
  animation:fadeUp .8s ease;
}
.hero h1{
  font-size:32px;
  font-weight:800;
  margin-bottom:14px;
}
.hero p{
  max-width:620px;
  margin:0 auto;
  color:var(--muted);
  line-height:1.7;
}

/* SECTION */
.section{
  padding:64px 56px;
}
.section h2{
  text-align:center;
  font-size:24px;
  font-weight:800;
  margin-bottom:8px;
  animation:fadeUp .8s ease;
}
.section .sub{
  text-align:center;
  font-size:14px;
  color:var(--muted);
  margin-bottom:48px;
  animation:fadeUp .9s ease;
}

/* EDUKASI CARDS */
.cards{
  max-width:1100px;
  margin:auto;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
  gap:24px;
}
.card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:14px;
  padding:28px;
  opacity:0;
  animation:fadeUp .7s ease forwards;
}
.card:nth-child(1){animation-delay:.1s}
.card:nth-child(2){animation-delay:.2s}
.card:nth-child(3){animation-delay:.3s}
.card:nth-child(4){animation-delay:.4s}
.card:nth-child(5){animation-delay:.5s}

.card:hover{
  transform:translateY(-6px);
  box-shadow:0 12px 30px rgba(0,0,0,.08);
}
.card h3{
  font-size:17px;
  font-weight:700;
  margin-bottom:12px;
}
.card p{
  font-size:14px;
  color:#4b5563;
  line-height:1.7;
  text-align:justify;
}

/* FOOTER */
.footer{
  text-align:center;
  padding:28px;
  font-size:13px;
  color:var(--muted);
  border-top:1px solid var(--border);
  background:#fff;
  animation:fadeUp .9s ease;
}

/* RESPONSIVE */
@media(max-width:768px){
  .header,.hero,.section{padding:32px}
  .hero h1{font-size:26px}
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
  <div class="logo">Bank Sampah Digital</div>
  <div class="nav">
    <a href="index.php">Kembali</a>
  </div>
</div>

<!-- HERO -->
<section class="hero">
  <h1>Edukasi Pengelolaan Sampah</h1>
  <p>
    Informasi dan materi edukasi seputar pengelolaan sampah yang dikelola
    langsung oleh admin Bank Sampah untuk meningkatkan kesadaran lingkungan.
  </p>
</section>

<!-- EDUKASI -->
<section class="section">
  <h2>Materi Edukasi</h2>
  <p class="sub">Konten ini diinput langsung oleh admin</p>

  <div class="cards">
    <?php if(mysqli_num_rows($edukasi) > 0): ?>
      <?php while($e = mysqli_fetch_assoc($edukasi)): ?>
        <div class="card">
          <h3><?= htmlspecialchars($e['judul']) ?></h3>
          <p><?= nl2br(htmlspecialchars($e['isi'])) ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="card">
        <p>Belum ada materi edukasi yang tersedia.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

</body>
</html>
