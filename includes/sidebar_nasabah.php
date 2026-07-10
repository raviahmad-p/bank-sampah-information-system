<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = $_SERVER['REQUEST_URI'];
?>
<!-- FONT AWESOME ICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{font-family:Segoe UI, Arial, sans-serif;}

.sidebar{
    width:230px;
    height:100vh;
    background:#ffffff;
    position:fixed;
    left:0; top:0;
    border-right:1px solid #e5e7eb;
    padding:22px 16px;
    box-sizing:border-box;
    display:flex;
    flex-direction:column;
}

/* LOGO */
.logo{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:700;
    font-size:17px;
    margin-bottom:26px;
    color:#111827;
}

/* MENU */
.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 12px;
    border-radius:8px;
    text-decoration:none;
    color:#374151;
    font-size:14px;
    margin-bottom:6px;
    transition:.15s ease;
}

.menu a i{
    width:18px;
    text-align:center;
    font-size:15px;
}

.menu a:hover{
    background:#f3f4f6;
}

.menu a.active{
    background:#e5e7eb;
    color:#111827;
    font-weight:600;
}

/* LOGOUT */
.logout{
    margin-top:auto;
}

.logout a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 12px;
    border-radius:8px;
    text-decoration:none;
    background:#fef2f2;
    color:#991b1b;
    font-weight:600;
}

.logout a:hover{
    background:#fee2e2;
}
</style>

<div class="sidebar">

    <div class="logo">
        <i class="fa-solid fa-recycle"></i>
        <span>Bank Sampah</span>
    </div>

    <div class="menu">

        <a href="dashboard.php"
           class="<?= strpos($current,'dashboard.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <!-- EDUKASI -->
        <a href="/SistemInformasi/edukasi.php"
           class="<?= strpos($current,'/SistemInformasi/edukasi.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-book-open"></i>
            Edukasi
        </a>

    </div>

    <div class="logout">
        <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>
    </div>

</div>
