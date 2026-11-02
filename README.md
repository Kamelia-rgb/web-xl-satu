# Website XL SATU

Website promosi internet rumah XL Satu untuk sales. Dibuat dengan **PHP + MySQL** (tanpa framework), cocok untuk shared hosting biasa (cPanel).

## Fitur

**Halaman website**
- Beranda: banner promo, paket unggulan, keunggulan, langkah pemasangan, FAQ
- Profil layanan XL Satu
- Daftar paket internet
- Promo (galeri dan slider banner)
- Kontak dan informasi pemasangan, lengkap dengan **formulir calon pelanggan**
- Kebijakan privasi (wajib untuk Google Ads dan Meta Ads)

**Fitur lain**
- Tombol WhatsApp melayang, dan setiap paket punya tombol WA dengan pesan yang sudah terisi otomatis
- Tampilan responsif untuk HP dan laptop
- SEO dasar: judul dan deskripsi per halaman, `sitemap.xml`, `robots.txt`, pratinjau link (Open Graph), alamat rapi (`/paket`, `/promo`)
- Pelacakan iklan: Google Ads, Google Analytics, dan Meta Pixel. Klik WA dan formulir terkirim tercatat sebagai konversi. Sumber iklan (utm/gclid/fbclid) ikut tersimpan di data calon pelanggan
- Perlindungan formulir dari spam (honeypot dan jeda kirim)

**Halaman admin (`/admin`)**
- Daftar calon pelanggan, dengan filter status, pencarian, catatan sales, tombol chat WA, dan export ke Excel (CSV)
- Kelola paket: kategori (Internet Only / Internet + TV / Internet + Bonus), kecepatan dan Speed Booster, harga dan harga coret, periode (per bulan atau per 3 bulan), PPN, biaya instalasi, bonus, label "Terlaris", paket unggulan
- Kelola promo dan upload gambar banner (otomatis hilang setelah tanggal berakhir)
- Kelola akun (satu akun per sales)

---

## Cara upload ke hosting (cPanel)

### 1. Beli hosting + domain
Pilih paket hosting yang sudah termasuk **domain .com gratis 1 tahun** dan **SSL gratis** (Hostinger, Niagahoster, Rumahweb, Domainesia, dll). Pastikan mendukung **PHP 8.1 atau lebih baru** dan **MySQL**.

### 2. Buat database
1. Masuk cPanel, lalu buka **MySQL Databases**
2. Buat database baru, misalnya `xlsatu_web`
3. Buat user database dan password-nya
4. Tambahkan user tersebut ke database dengan **ALL PRIVILEGES**
5. Buka **phpMyAdmin**, pilih database tadi, buka tab **Import**, lalu pilih file `database.sql` dan klik **Go**

> **Sudah pernah import `database.sql` versi lama?** Di phpMyAdmin hapus (Drop) tabel `paket`, lalu import ulang `database.sql`. Tabel lain tidak berubah, jadi data lain aman.

### 3. Upload file
1. Download repo ini sebagai ZIP (tombol **Code → Download ZIP** di GitHub)
2. Di cPanel, buka **File Manager**, lalu masuk ke folder `public_html`
3. Upload ZIP lalu **Extract**. Pindahkan semua isi folder hasil extract langsung ke `public_html`, termasuk file `.htaccess` (aktifkan *Show Hidden Files* supaya terlihat)

### 4. Atur `config.php`
1. Di File Manager, salin `config.example.php` lalu beri nama `config.php`
2. Edit `config.php` dan isi:
   - `base_url`: alamat web kamu, misalnya `https://xlsatu-promo.com`
   - `db_dsn`: ganti `NAMA_DATABASE` dengan nama database lengkap (biasanya ada awalan, contoh `u123_xlsatu_web`)
   - `db_user` dan `db_pass`: user dan password database
   - Nomor WA, area layanan, jam layanan bila perlu

### 5. Aktifkan SSL
Di cPanel, buka **SSL/TLS Status** (atau menu SSL di panel hosting) lalu aktifkan SSL gratis untuk domain. File `.htaccess` sudah otomatis mengarahkan pengunjung ke `https://`.

> Kalau web jadi tidak bisa dibuka karena SSL belum aktif, beri tanda `#` di depan 3 baris "Paksa HTTPS" di `.htaccess`, lalu hapus lagi tanda `#` itu setelah SSL aktif.

### 6. Buat akun admin
Buka `https://domainkamu.com/admin/` dan kamu akan diminta membuat akun admin pertama. Setelah itu:
- Ganti data paket contoh di menu **Paket**
- Upload banner promo asli di menu **Promo** (ukuran ideal 1200 × 450 px)
- Tambahkan akun untuk tiap sales di menu **Akun**

### 7. Daftarkan ke Google
- Daftarkan web di [Google Search Console](https://search.google.com/search-console) dan kirim `https://domainkamu.com/sitemap.xml`
- Isi `google_tag_id`, `google_ads_conversion`, dan `meta_pixel_id` di `config.php` setelah akun iklan siap

---

## Tips iklan

- Jangan membuat web terlihat seperti situs resmi XL. Teks pemberitahuan di bagian bawah web (`disclaimer` di `config.php`) sudah menjelaskan bahwa ini web mitra penjualan. Gunakan logo resmi XL hanya bila sudah mendapat izin.
- Pastikan harga dan promo di web sama dengan yang ditawarkan sales. Iklan bisa ditolak kalau isinya tidak sesuai.
- Gunakan link iklan dengan parameter, misalnya `https://domainkamu.com/?utm_source=facebook&utm_campaign=promo-november`, supaya sumber calon pelanggan terlihat di halaman admin.

## Mengubah tampilan

- Warna utama: `assets/css/style.css`, bagian `:root` (`--primary`, `--accent`)
- Teks FAQ dan langkah pemasangan: `includes/partials.php`
- Teks halaman: file `index.php`, `layanan.php`, `paket.php`, `promo.php`, `kontak.php`

## Struktur folder

```
admin/            halaman admin (login, calon pelanggan, paket, promo, akun)
assets/           CSS, JavaScript, gambar
includes/         fungsi bersama, header & footer
uploads/promo/    gambar banner yang di-upload dari admin
database.sql      struktur database + data contoh
config.example.php contoh pengaturan (salin jadi config.php)
```
