<?php
session_start();
include 'includes/config.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);

    if (strlen($password) < 6) {
        $error = "Password minimal 6 karakter";
    } else {

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah digunakan";
        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $insertUser = mysqli_query($conn, "
                INSERT INTO users (username, password, role)
                VALUES ('$username', '$password_hash', 'nasabah')
            ");

            if ($insertUser) {
                $user_id = mysqli_insert_id($conn);

                $insertNasabah = mysqli_query($conn, "
                    INSERT INTO nasabah 
                    (user_id, nama, alamat, no_hp, saldo, tanggal_daftar)
                    VALUES
                    ('$user_id', '$nama', '$alamat', '$no_hp', 0, NOW())
                ");

                if ($insertNasabah) {
                    $success = "Registrasi berhasil! Silakan login.";
                } else {
                    $error = "Gagal menyimpan data nasabah";
                }
            } else {
                $error = "Gagal membuat akun";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Nasabah | Bank Sampah</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ffffffff, #e0e6e3ff);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-card {
            width: 420px;
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .register-card h2 {
            text-align: center;
            color: #27ae60;
            margin-bottom: 8px;
        }

        .register-card p {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #27ae60;
            box-shadow: 0 0 0 2px rgba(39,174,96,0.15);
        }

        textarea {
            resize: none;
            height: 70px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #27ae60;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #219150;
            transform: translateY(-1px);
        }

        .alert-error {
            background: #fdecea;
            color: #b02a37;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 14px;
        }

        .alert-success {
            background: #e6f4ea;
            color: #0f5132;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 14px;
        }

        .links {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .links a {
            text-decoration: none;
            color: #27ae60;
            font-weight: 500;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-card">
    <h2>Daftar Nasabah</h2>
    <p>Bank Sampah Digital</p>

    <?php if ($error != "") { ?>
        <div class="alert-error"><?= $error; ?></div>
    <?php } ?>

    <?php if ($success != "") { ?>
        <div class="alert-success"><?= $success; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
        </div>

        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password (min 6 karakter)" required>
        </div>

        <div class="form-group">
            <textarea name="alamat" placeholder="Alamat Lengkap" required></textarea>
        </div>

        <div class="form-group">
            <input type="text" name="no_hp" placeholder="Nomor HP" required>
        </div>

        <button class="btn" type="submit" name="register">Daftar Sekarang</button>
    </form>

    <div class="links">
        Sudah punya akun? <a href="login.php">Login</a><br>
        <a href="index.php"> Kembali ke Beranda</a>
    </div>
</div>

</body>
</html>
