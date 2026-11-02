-- =========================================================
-- Update paket (untuk database yang SUDAH pernah di-import)
-- Jalankan sekali di phpMyAdmin: pilih database xlsatu_web > tab SQL > paste > Go
-- Tidak perlu dijalankan kalau baru import database.sql versi terbaru.
-- =========================================================

-- 1. 100 Mbps FWA Bulanan pindah ke kategori Internet + Bonus
UPDATE paket SET kategori = 'Internet + Bonus'
WHERE nama = 'FWA Bulanan' AND kecepatan = 100;

-- 2. 1000 Mbps XL Satu Spark pindah ke kategori Internet Only
UPDATE paket SET kategori = 'Internet Only'
WHERE nama = 'XL Satu Spark' AND kecepatan = 1000;

-- 3. Paket baru: up to 300 Mbps, Rp265.290/bulan, Internet Only
INSERT INTO paket (kategori, nama, kecepatan, harga, harga_coret, periode_bulan, ppn_termasuk, biaya_instalasi, benefit, bonus, label, unggulan, aktif, urutan)
SELECT 'Internet Only', 'XL Satu Spark', 300, 265290, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 1, 2
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM paket WHERE nama = 'XL Satu Spark' AND kecepatan = 300);

-- 4. Jenis produk: Fiber (jaringan kabel) atau FWA (tanpa kabel, modem)
--    Catatan: web juga menambahkan kolom ini otomatis saat dibuka.
ALTER TABLE paket ADD COLUMN IF NOT EXISTS jenis VARCHAR(20) NOT NULL DEFAULT 'Fiber' AFTER id;
UPDATE paket SET jenis = 'FWA' WHERE nama LIKE 'FWA%';

-- 5. Hapus promo "Diskon Bulan Pertama"
DELETE FROM promo WHERE judul = 'Diskon Bulan Pertama';

-- 6. Kolom lokasi (link Google Maps) di data calon pelanggan
--    Catatan: web juga menambahkan kolom ini otomatis saat dibuka.
ALTER TABLE leads ADD COLUMN IF NOT EXISTS lokasi VARCHAR(255) NULL AFTER kota;
