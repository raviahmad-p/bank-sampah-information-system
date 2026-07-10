-- =========================================
-- BANK SAMPAH INFORMATION SYSTEM
-- InfinityFree Ready
-- =========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =========================================
-- USERS
-- =========================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','nasabah') NOT NULL
);

-- =========================================
-- NASABAH
-- =========================================

CREATE TABLE nasabah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(20),
    saldo DECIMAL(12,2) DEFAULT 0,
    tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

-- =========================================
-- JENIS SAMPAH
-- =========================================

CREATE TABLE jenis_sampah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sampah VARCHAR(100) NOT NULL,
    harga_per_kg DECIMAL(12,2) NOT NULL
);

-- =========================================
-- SETORAN
-- =========================================

CREATE TABLE setoran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nasabah INT NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_berat DECIMAL(10,2) DEFAULT 0,
    total_harga DECIMAL(12,2) DEFAULT 0,
    FOREIGN KEY(id_nasabah) REFERENCES nasabah(id)
    ON DELETE CASCADE
);

-- =========================================
-- DETAIL SETORAN
-- =========================================

CREATE TABLE setoran_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_setoran INT NOT NULL,
    id_sampah INT NOT NULL,
    berat DECIMAL(10,2),
    harga_per_kg DECIMAL(12,2),
    subtotal DECIMAL(12,2),

    FOREIGN KEY(id_setoran)
    REFERENCES setoran(id)
    ON DELETE CASCADE,

    FOREIGN KEY(id_sampah)
    REFERENCES jenis_sampah(id)
);

-- =========================================
-- TARIK SALDO
-- =========================================

CREATE TABLE tarik_saldo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nasabah INT NOT NULL,
    jumlah DECIMAL(12,2),
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    keterangan VARCHAR(255),

    FOREIGN KEY(id_nasabah)
    REFERENCES nasabah(id)
    ON DELETE CASCADE
);

-- =========================================
-- EDUKASI
-- =========================================

CREATE TABLE edukasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- ADMIN DEFAULT
-- username : admin
-- password : admin123
-- =========================================

INSERT INTO users(username,password,role)
VALUES
(
'admin',
'$2y$10$z0T9Jv3gV6nQ3QXc2Y3f8.Bz4Y7v6Wm0T5J2oHqM7f9sN4j2c5lJ2',
'admin'
);

-- =========================================
-- DATA JENIS SAMPAH
-- =========================================

INSERT INTO jenis_sampah(nama_sampah,harga_per_kg) VALUES
('Plastik',2000),
('Kertas',1500),
('Logam',5000),
('Kaca',1000);

-- =========================================
-- DATA EDUKASI
-- =========================================

INSERT INTO edukasi(judul,isi) VALUES
(
'Pentingnya Memilah Sampah',
'Membuang sampah sesuai jenisnya akan mempermudah proses daur ulang dan menjaga kebersihan lingkungan.'
),
(
'Manfaat Bank Sampah',
'Bank Sampah memberikan nilai ekonomi bagi masyarakat sekaligus membantu menjaga lingkungan tetap bersih.'
),
(
'Jenis Sampah yang Dapat Didaur Ulang',
'Plastik, kertas, logam, kaca dan beberapa jenis sampah lainnya dapat didaur ulang menjadi produk baru.'
);

COMMIT;