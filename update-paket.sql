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

-- 7. Kategori Internet + TV menjadi Internet + TV + Kuota Keluarga
UPDATE paket SET kategori = 'Internet + TV + Kuota Keluarga' WHERE kategori = 'Internet + TV';

-- 8. Detail paket Joy: harga, channel TV, bonus kuota XL, pemasangan
UPDATE paket SET harga = 388500, ideal_untuk = 'Cocok untuk WFH dan Online Shop, Video Conference 4K, Game Online HD, Streaming 4K, Multi Device, TV Kabel',
  benefit = '98 Channel TV\nBonus Kuota XL 25 GB\nGratis Biaya Pemasangan + Alat + Kabel 40 Meter' WHERE nama = 'Joy Value' AND kecepatan = 100;
UPDATE paket SET harga = 471750, ideal_untuk = 'Cocok untuk WFH dan Online Shop, Video Conference 4K, Game Online HD, Streaming 4K, Multi Device, TV Kabel',
  benefit = '98 Channel TV\nBonus Kuota XL 50 GB\nGratis Biaya Pemasangan + Alat + Kabel 40 Meter' WHERE nama = 'Joy Pro' AND kecepatan = 150;
UPDATE paket SET harga = 749250, ideal_untuk = 'Cocok untuk WFH dan Online Shop, Video Conference 4K, Game Online HD, Streaming 4K, Multi Device, TV Kabel',
  benefit = '160 Channel TV\nBonus Kuota XL 50 GB\nGratis Biaya Pemasangan + Alat + Kabel 40 Meter' WHERE nama = 'Joy Premium' AND kecepatan = 200;

-- 9. Hapus paket Internet Only 350 Mbps (Rp309.000)
DELETE FROM paket WHERE nama = 'XL Satu Spark' AND kecepatan = 350;

-- 10. Paket FWA baru sesuai brosur (semua sudah termasuk PPN)
DELETE FROM paket WHERE jenis = 'FWA';
INSERT INTO paket (jenis, kategori, nama, kecepatan, harga, harga_coret, periode_bulan, ppn_termasuk, biaya_instalasi, benefit, bonus, label, unggulan, aktif, urutan) VALUES
('FWA', 'Per Bulan', 'FWA Per Bulan', 100, 243090, NULL, 1, 1, 0, NULL, 'Free Vidio Lite & CATCHPLAY+ 3 bulan', 'Best Value', 0, 1, 13),
('FWA', 'Hemat 4 Bulan (Bayar di Muka)', 'FWA Hemat 4 Bulan', 50, 721500, NULL, 4, 1, 0, 'Setara Rp180.000/bulan', 'Free Vidio Lite & CATCHPLAY+ 3 bulan', 'Hemat', 0, 1, 14),
('FWA', 'Hemat 4 Bulan (Bayar di Muka)', 'FWA Hemat 4 Bulan', 100, 876900, NULL, 4, 1, 0, 'Setara Rp219.009/bulan', 'Free Vidio Lite & CATCHPLAY+ 3 bulan', 'Hemat', 0, 1, 15);
