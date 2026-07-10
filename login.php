<?php
session_start();
include 'includes/config.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php"); exit;
    } elseif ($_SESSION['role'] == 'nasabah') {
        header("Location: nasabah/dashboard.php"); exit;
    }
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user  = mysqli_fetch_assoc($query);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user']  = $user['id'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['username'] = $user['username'];

            header("Location: " . ($user['role'] == 'admin' ? "admin/dashboard.php" : "nasabah/dashboard.php"));
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | Bank Sampah Digital</title>

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:"Segoe UI",Arial,sans-serif;
  background:linear-gradient(135deg,#e8f5ee,#f4f6fb);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#1f2937;
}

/* LOGIN CARD */
.login-box{
  width:380px;
  background:#fff;
  padding:36px 32px;
  border-radius:14px;
  box-shadow:0 20px 40px rgba(0,0,0,.08);
  animation:fadeUp .8s ease;
}

@keyframes fadeUp{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:translateY(0)}
}

.login-box h2{
  text-align:center;
  font-size:24px;
  font-weight:800;
  color:#27ae60;
  margin-bottom:8px;
}
.login-box p{
  text-align:center;
  font-size:13px;
  color:#6b7280;
  margin-bottom:26px;
}

/* ERROR */
.error{
  background:#fee2e2;
  color:#b91c1c;
  padding:10px;
  border-radius:8px;
  font-size:13px;
  text-align:center;
  margin-bottom:16px;
}

/* INPUT */
.form-group{
  margin-bottom:16px;
}
.form-group label{
  font-size:13px;
  font-weight:600;
  display:block;
  margin-bottom:6px;
}
.form-group input{
  width:100%;
  padding:11px 14px;
  border-radius:8px;
  border:1.5px solid #d1d5db;
  font-size:14px;
  transition:.25s;
}
.form-group input:focus{
  outline:none;
  border-color:#27ae60;
  box-shadow:0 0 0 3px rgba(39,174,96,.15);
}

/* BUTTON */
button{
  width:100%;
  padding:12px;
  border:none;
  border-radius:8px;
  background:#27ae60;
  color:#fff;
  font-size:14px;
  font-weight:700;
  cursor:pointer;
  transition:.3s;
}
button:hover{
  background:#219150;
  transform:translateY(-1px);
}

/* LINKS */
.links{
  text-align:center;
  margin-top:22px;
  font-size:13px;
}
.links a{
  color:#2563eb;
  text-decoration:none;
  font-weight:600;
}
.links a:hover{
  text-decoration:underline;
}
.back{
  display:block;
  margin-top:8px;
  color:#6b7280;
  font-size:12px;
}
</style>
</head>
<body>

<div class="login-box">
  <h2>Login</h2>
  <p>Masuk ke sistem Bank Sampah Digital</p>

  <?php if($error!=""){ ?>
    <div class="error"><?= $error ?></div>
  <?php } ?>

  <form method="POST">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" placeholder="Masukkan username" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Masukkan password" required>
    </div>

    <button type="submit" name="login">Masuk</button>
  </form>

  <div class="links">
    Belum punya akun? <a href="register.php">Daftar</a>
    <span class="back"><a href="index.php">Kembali ke Beranda</a></span>
  </div>
</div>

</body>
</html>
