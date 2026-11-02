-- =========================================================
-- Database website XL SATU
-- Import file ini lewat cPanel > phpMyAdmin > Import
-- Data paket mengikuti daftar harga XL Satu; data promo masih CONTOH. Semua bisa diubah lewat halaman admin.
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
  jenis VARCHAR(20) NOT NULL DEFAULT 'Fiber' COMMENT 'Fiber = jaringan kabel, FWA = tanpa kabel (modem)',
  kategori VARCHAR(40) NOT NULL DEFAULT 'Internet Only' COMMENT 'Internet Only / Internet + TV + Kuota Keluarga / Internet + Bonus',
  nama VARCHAR(100) NOT NULL,
  kecepatan INT UNSIGNED NOT NULL COMMENT 'Mbps',
  kecepatan_coret INT UNSIGNED NULL COMMENT 'Kecepatan sebelum Speed Booster (boleh kosong)',
  harga INT UNSIGNED NOT NULL COMMENT 'Rupiah per periode',
  harga_coret INT UNSIGNED NULL COMMENT 'Harga normal sebelum promo (boleh kosong)',
  periode_bulan TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = per bulan, 3 = per 3 bulan',
  ppn_termasuk TINYINT(1) NOT NULL DEFAULT 1,
  biaya_instalasi INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = gratis',
  maks_perangkat SMALLINT UNSIGNED NULL COMMENT 'Kosong = dihitung otomatis dari kecepatan',
  ideal_untuk VARCHAR(255) NULL COMMENT 'Dipisah koma. Kosong = otomatis dari kecepatan',
  benefit TEXT NULL COMMENT 'Keterangan tambahan, satu per baris',
  bonus VARCHAR(255) NULL,
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
  lokasi VARCHAR(255) NULL COMMENT 'Link Google Maps titik rumah',
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

-- ---------- Paket (sesuai daftar harga, ubah lewat halaman admin) ----------
INSERT INTO paket (jenis, kategori, nama, kecepatan, harga, harga_coret, periode_bulan, ppn_termasuk, biaya_instalasi, benefit, bonus, label, unggulan, aktif, urutan) VALUES
('Fiber', 'Internet Only', 'XL Satu Starter', 20, 205000, NULL, 1, 1, 111000, NULL, NULL, NULL, 0, 1, 1),
('Fiber', 'Internet Only', 'XL Satu Spark', 250, 254000, NULL, 1, 1, 0, NULL, NULL, 'Terlaris', 1, 1, 2),
('Fiber', 'Internet Only', 'XL Satu Spark', 300, 265290, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 2),
('Fiber', 'Internet Only', 'XL Satu Spark', 350, 309000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 3),
('Fiber', 'Internet Only', 'XL Satu Spark', 400, 333000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 4),
('Fiber', 'Internet + TV + Kuota Keluarga', 'Joy Value', 100, 388000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 5),
('Fiber', 'Internet + TV + Kuota Keluarga', 'Joy Pro', 150, 471000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 6),
('Fiber', 'Internet Only', 'XL Satu Spark', 600, 555000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 7),
('Fiber', 'Internet + TV + Kuota Keluarga', 'Joy Premium', 200, 749000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 8),
('Fiber', 'Internet Only', 'XL Satu Spark', 1000, 999000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 9),
('Fiber', 'Internet + TV + Kuota Keluarga', 'Star Value', 300, 1093000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 10),
('Fiber', 'Internet + TV + Kuota Keluarga', 'Star Pro', 500, 2053000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 11),
('Fiber', 'Internet + TV + Kuota Keluarga', 'Star Premium', 1000, 4993000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 12),
('FWA', 'Internet + Bonus', 'FWA Bulanan', 100, 219000, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 13),
('FWA', 'Internet + Bonus', 'FWA 3 Bulanan', 100, 650000, 942000, 3, 0, 0, 'Bulan ke-4 dst Rp219.000/bulan', 'Bonus nonton 3 bulan: Vidio Lite & Catchplay+', 'Hemat', 0, 1, 14),
('FWA', 'Internet + Bonus', 'FWA 4 Bulanan', 50, 650000, 1085000, 4, 0, 0, 'Bulan ke-5 dst Rp200.000/bulan', 'Bonus nonton 3 bulan: Vidio Lite & Catchplay+', 'Hemat', 0, 1, 15);

-- ---------- Contoh promo ----------
INSERT INTO promo (judul, deskripsi, gambar, berlaku_sampai, tampil_banner, aktif, urutan) VALUES
('Gratis Biaya Pemasangan', 'Daftar bulan ini dan nikmati pemasangan tanpa biaya. Teknisi datang ke rumah setelah survei lokasi.', 'assets/img/promo/promo-1.svg', NULL, 1, 1, 1),
('Bonus Kuota untuk HP', 'Setiap paket internet rumah dapat bonus kuota XL di HP untuk seluruh anggota keluarga.', 'assets/img/promo/promo-3.svg', NULL, 1, 1, 2);
