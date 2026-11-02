-- =========================================================
-- Database website XL SATU
-- Import file ini lewat cPanel > phpMyAdmin > Import
-- Data paket & promo di bawah hanya CONTOH, ubah lewat halaman admin.
-- =========================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS paket (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  kecepatan INT UNSIGNED NOT NULL COMMENT 'Mbps',
  harga INT UNSIGNED NOT NULL COMMENT 'Rupiah per bulan',
  harga_coret INT UNSIGNED NULL COMMENT 'Harga normal sebelum promo (boleh kosong)',
  benefit TEXT NULL COMMENT 'Satu benefit per baris',
  label VARCHAR(40) NULL COMMENT 'Contoh: Terlaris, Hemat',
  unggulan TINYINT(1) NOT NULL DEFAULT 0,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  urutan INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS promo (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  deskripsi TEXT NULL,
  gambar VARCHAR(255) NULL,
  berlaku_sampai DATE NULL,
  tampil_banner TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Tampil di slider halaman depan',
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  urutan INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  no_wa VARCHAR(20) NOT NULL,
  alamat TEXT NOT NULL,
  kota VARCHAR(100) NULL,
  paket_id INT UNSIGNED NULL,
  paket_nama VARCHAR(100) NULL,
  catatan TEXT NULL,
  sumber VARCHAR(100) NULL COMMENT 'utm_source / asal iklan',
  status VARCHAR(20) NOT NULL DEFAULT 'baru',
  catatan_sales TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Contoh paket ----------
INSERT INTO paket (nama, kecepatan, harga, harga_coret, benefit, label, unggulan, aktif, urutan) VALUES
('Paket Hemat', 30, 229000, 269000, 'Internet unlimited tanpa FUP\nCocok untuk 3–5 perangkat\nGratis pemasangan\nModem WiFi dipinjamkan', NULL, 0, 1, 1),
('Paket Keluarga', 50, 289000, 339000, 'Internet unlimited tanpa FUP\nCocok untuk 5–8 perangkat\nGratis pemasangan\nBonus kuota XL di HP', 'Terlaris', 1, 1, 2),
('Paket Streaming', 100, 389000, 449000, 'Internet unlimited tanpa FUP\nCocok untuk 8–12 perangkat\nStreaming & gaming lancar\nBonus kuota XL di HP', NULL, 0, 1, 3),
('Paket Ultra', 200, 589000, 659000, 'Internet unlimited tanpa FUP\nCocok untuk rumah besar & usaha\nPrioritas layanan teknisi\nBonus kuota XL di HP', 'Paling Ngebut', 0, 1, 4);

-- ---------- Contoh promo ----------
INSERT INTO promo (judul, deskripsi, gambar, berlaku_sampai, tampil_banner, aktif, urutan) VALUES
('Gratis Biaya Pemasangan', 'Daftar bulan ini dan nikmati pemasangan tanpa biaya. Teknisi datang ke rumah setelah survei lokasi.', 'assets/img/promo/promo-1.svg', NULL, 1, 1, 1),
('Diskon Bulan Pertama', 'Potongan harga untuk tagihan bulan pertama khusus pelanggan baru. Syarat & ketentuan berlaku.', 'assets/img/promo/promo-2.svg', NULL, 1, 1, 2),
('Bonus Kuota untuk HP', 'Setiap paket internet rumah dapat bonus kuota XL di HP untuk seluruh anggota keluarga.', 'assets/img/promo/promo-3.svg', NULL, 1, 1, 3);
