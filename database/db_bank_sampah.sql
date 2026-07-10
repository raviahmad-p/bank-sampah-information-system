-- ===============================
-- DATABASE BANK SAMPAH
-- ===============================

CREATE DATABASE IF NOT EXISTS sisteminformasi;
USE sisteminformasi;

-- ===============================
-- TABEL USERS (LOGIN)
-- ===============================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','nasabah') NOT NULL
);

-- ===============================
-- TABEL NASABAH
-- ===============================
CREATE TABLE nasabah (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  nama VARCHAR(100) NOT NULL,
  alamat TEXT,
  no_hp VARCHAR(15),
  saldo DECIMAL(12,2) DEFAULT 0,
  tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_nasabah_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

-- ===============================
-- TABEL JENIS SAMPAH
-- ===============================
CREATE TABLE jenis_sampah (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_sampah VARCHAR(100) NOT NULL,
  harga_per_kg DECIMAL(12,2) NOT NULL
);

-- ===============================
-- TABEL SETORAN (HEADER)
-- ===============================
CREATE TABLE setoran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_nasabah INT NOT NULL,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  total_berat DECIMAL(12,2) DEFAULT 0,
  total_harga DECIMAL(12,2) DEFAULT 0,
  CONSTRAINT fk_setoran_nasabah
    FOREIGN KEY (id_nasabah) REFERENCES nasabah(id)
    ON DELETE CASCADE
);

-- ===============================
-- TABEL SETORAN DETAIL
-- ===============================
CREATE TABLE setoran_detail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_setoran INT NOT NULL,
  id_sampah INT NOT NULL,
  berat DECIMAL(12,2) NOT NULL,
  harga_per_kg DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_detail_setoran
    FOREIGN KEY (id_setoran) REFERENCES setoran(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_detail_sampah
    FOREIGN KEY (id_sampah) REFERENCES jenis_sampah(id)
    ON DELETE RESTRICT
);

-- ===============================
-- TABEL TARIK SALDO (ADMIN ONLY)
-- ===============================
CREATE TABLE tarik_saldo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_nasabah INT NOT NULL,
  jumlah DECIMAL(12,2) NOT NULL,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  keterangan VARCHAR(255),
  CONSTRAINT fk_tarik_nasabah
    FOREIGN KEY (id_nasabah) REFERENCES nasabah(id)
    ON DELETE CASCADE
);

-- ===============================
-- DATA AWAL (OPSIONAL)
-- ===============================

-- ADMIN DEFAULT
INSERT INTO users (username, password, role)
VALUES ('admin', '$2y$10$examplehashpassword', 'admin');

-- CONTOH JENIS SAMPAH
INSERT INTO jenis_sampah (nama_sampah, harga_per_kg) VALUES
('Plastik', 2000),
('Kertas', 1500),
('Logam', 5000),
('Kaca', 1000);

-- ===============================
-- SELESAI
-- ===============================
