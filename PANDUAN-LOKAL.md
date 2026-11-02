# Panduan Menjalankan Web di Komputer Sendiri (VS Code + XAMPP)

Panduan ini untuk membuka kode di **Visual Studio Code** dan menjalankan web di komputer sendiri, termasuk **import database**. Contoh di sini memakai Windows. Untuk Mac, lihat catatan di bagian akhir.

Gambaran singkatnya:

| Program | Fungsinya |
|---|---|
| **VS Code** | Untuk membuka dan mengedit kode |
| **XAMPP** | Untuk menjalankan web: berisi Apache (server web), PHP, dan MySQL (database) |
| **phpMyAdmin** | Halaman untuk mengatur database. Sudah termasuk di XAMPP |

---

## Langkah 1: Install program

1. **VS Code**: download di https://code.visualstudio.com lalu install.
2. **XAMPP**: download di https://www.apachefriends.org. Pilih versi **PHP 8.2 atau lebih baru**, lalu install di lokasi bawaan `C:\xampp`.
3. *(Opsional)* **Git**: download di https://git-scm.com. Hanya perlu kalau mau download kode lewat Git (Langkah 2, cara B).

---

## Langkah 2: Download kode ke folder XAMPP

Kode harus diletakkan di folder **`C:\xampp\htdocs`** supaya bisa dijalankan XAMPP.

### Cara A: Download ZIP (paling mudah)
1. Buka https://github.com/Kamelia-rgb/web-xl-satu
2. Klik tombol hijau **Code**, lalu **Download ZIP**.
3. Extract ZIP-nya. Hasilnya folder bernama `web-xl-satu-main`.
4. **Ganti nama** folder menjadi `web-xl-satu`, lalu pindahkan ke `C:\xampp\htdocs\`.

Hasil akhirnya harus begini: `C:\xampp\htdocs\web-xl-satu\index.php`

> Pastikan foldernya tidak dobel, misalnya `htdocs\web-xl-satu\web-xl-satu\index.php`. Kalau dobel, pindahkan isi folder yang di dalam ke atas.

### Cara B: Pakai Git (kalau nanti mau push perubahan ke GitHub)
Buka **Command Prompt**, lalu jalankan:
```bash
cd C:\xampp\htdocs
git clone https://github.com/Kamelia-rgb/web-xl-satu.git
```

---

## Langkah 3: Buka kode di VS Code

1. Buka VS Code.
2. Klik menu **File → Open Folder…**
3. Pilih folder `C:\xampp\htdocs\web-xl-satu`, lalu klik **Select Folder**.
4. Kalau muncul pertanyaan *"Do you trust the authors?"*, klik **Yes, I trust the authors**.

Ekstensi yang disarankan (klik ikon kotak-kotak **Extensions** di sisi kiri lalu cari namanya):
- **PHP Intelephense**: untuk warna dan saran kode PHP.

Isi folder yang akan terlihat di VS Code:
```
admin/              halaman admin
assets/             CSS, JavaScript, gambar
includes/           fungsi bersama, header & footer
uploads/            gambar banner hasil upload
config.example.php  contoh pengaturan
database.sql        file database yang akan di-import
index.php, paket.php, promo.php, kontak.php, ...  halaman web
```

---

## Langkah 4: Nyalakan Apache dan MySQL

1. Buka **XAMPP Control Panel** (cari "XAMPP" di menu Start).
2. Klik **Start** di baris **Apache**.
3. Klik **Start** di baris **MySQL**.
4. Keduanya harus berwarna **hijau**.

> Kalau Apache atau MySQL tidak mau hijau, lihat bagian **Masalah yang sering terjadi** di bawah.

---

## Langkah 5: Buat database dan import `database.sql`

### 5a. Buat database kosong
1. Buka browser, lalu ketik alamat: **http://localhost/phpmyadmin**
2. Di menu kiri, klik **New** (Baru).
3. Di kolom *Database name*, ketik: `xlsatu_web`
4. Di pilihan di sebelahnya (*Collation*), pilih **utf8mb4_unicode_ci**.
5. Klik **Create**.

### 5b. Import file database
1. Pastikan database **`xlsatu_web` sedang dipilih**: namanya tercetak tebal di menu kiri.
2. Klik tab **Import** di bagian atas.
3. Di bagian *File to import*, klik **Choose File**, lalu pilih file
   `C:\xampp\htdocs\web-xl-satu\database.sql`
4. Biarkan pengaturan lain apa adanya, lalu gulir ke bawah dan klik **Import** (atau **Go**).
5. Kalau berhasil, muncul tulisan hijau *"Import has been successfully finished"*.

### 5c. Cek hasilnya
Di menu kiri, klik `xlsatu_web`. Harus ada **4 tabel**:

| Tabel | Isinya |
|---|---|
| `admin_users` | akun admin/sales (masih kosong, dibuat di Langkah 7) |
| `leads` | data calon pelanggan dari formulir (masih kosong) |
| `paket` | **15 paket** internet |
| `promo` | **3 promo** contoh |

---

## Langkah 6: Buat file `config.php`

File ini memberi tahu web cara tersambung ke database.

1. Di VS Code, klik kanan file **`config.example.php`**, lalu pilih **Copy**.
2. Klik kanan di area kosong daftar file, lalu pilih **Paste**. Akan muncul `config.example copy.php`.
3. Klik kanan file itu, pilih **Rename**, lalu ganti namanya menjadi **`config.php`**.
4. Buka `config.php` dan ubah 4 baris ini:

```php
'base_url'     => 'http://localhost/web-xl-satu',

'db_dsn'       => 'mysql:host=localhost;dbname=xlsatu_web;charset=utf8mb4',
'db_user'      => 'root',
'db_pass'      => '',
```

5. Simpan dengan **Ctrl + S**.

> Di XAMPP, user database bawaan adalah `root` **tanpa password**. Karena itu `db_pass` dibiarkan kosong (`''`).
> File `config.php` sengaja **tidak ikut ke GitHub** karena berisi password database.

---

## Langkah 7: Buka web-nya

- **Website:** http://localhost/web-xl-satu
- **Admin:** http://localhost/web-xl-satu/admin

Saat pertama kali membuka halaman admin, kamu akan diminta **membuat akun admin** (username dan password minimal 8 karakter). Setelah itu login, dan kamu bisa:
- melihat calon pelanggan yang mengisi formulir
- mengubah paket dan harga
- upload banner promo
- menambah akun untuk sales

**Coba alur lengkapnya:** buka halaman Paket, klik **Isi Form** di salah satu paket, isi formulirnya, lalu buka admin. Data yang barusan kamu isi akan muncul di sana.

---

## Langkah 8: Mengedit kode

1. Ubah file di VS Code, lalu simpan (**Ctrl + S**).
2. Kembali ke browser dan tekan **F5** untuk refresh. Perubahan langsung terlihat.

Contoh yang sering diubah:

| Ingin mengubah | Buka file |
|---|---|
| Warna utama web | `assets/css/style.css`, bagian paling atas (`--primary`, `--accent`) |
| Nomor WA, jam layanan, area | `config.php` |
| Teks beranda | `index.php` |
| Pertanyaan FAQ & langkah pemasangan | `includes/partials.php` |
| Paket, harga, promo | **Tidak perlu edit kode**, cukup lewat halaman admin |

> Kalau CSS tidak berubah setelah di-refresh, tekan **Ctrl + F5** untuk refresh penuh.

### Simpan perubahan ke GitHub (kalau pakai Git / Cara B)
1. Di VS Code, klik ikon **Source Control** di sisi kiri (ikon cabang).
2. Tulis keterangan perubahan di kotak *Message*, lalu klik **Commit**.
3. Klik **Sync Changes** / **Push**.

---

## Mengulang import database (reset data)

`database.sql` **jangan di-import dua kali** ke database yang sama, karena data paket dan promo akan jadi dobel. Kalau ingin mengulang dari awal:

1. Buka phpMyAdmin, lalu klik database `xlsatu_web`.
2. Centang **Check all** di bawah daftar tabel.
3. Pilih **Drop** di menu *With selected*, lalu konfirmasi **Yes**.
4. Ulangi **Langkah 5b** (import).

> Cara ini menghapus semua data, termasuk akun admin dan calon pelanggan. Lakukan hanya di komputer sendiri, jangan di hosting yang sudah dipakai.

---

## Masalah yang sering terjadi

| Masalah | Penyebab & solusi |
|---|---|
| **Apache tidak mau Start** (port 80 dipakai) | Biasanya bentrok dengan Skype, IIS, atau VMware. Tutup program tersebut. Atau di XAMPP klik **Config → httpd.conf**, ganti `Listen 80` jadi `Listen 8080`, lalu buka web di `http://localhost:8080/web-xl-satu` dan ubah `base_url` di `config.php` menjadi `http://localhost:8080/web-xl-satu`. |
| **MySQL tidak mau Start** / **"MySQL shutdown unexpectedly"** | Lihat bagian **Memperbaiki "MySQL shutdown unexpectedly"** di bawah. |
| Tulisan **"File config.php belum ada"** | Langkah 6 belum dilakukan, atau nama filenya salah (misalnya `config.php.txt`). |
| **"Access denied for user"** | `db_user`/`db_pass` di `config.php` salah. Di XAMPP: `root` dan password kosong. |
| **"Unknown database 'xlsatu_web'"** | Database belum dibuat (Langkah 5a), atau nama di `config.php` berbeda. |
| **"Table ... doesn't exist"** | `database.sql` belum di-import (Langkah 5b). |
| **"#1046 No database selected"** saat import | Klik dulu database `xlsatu_web` di menu kiri, baru klik tab Import. |
| Web tampil **berantakan tanpa warna**, atau link menu **Not Found** | `base_url` di `config.php` tidak sama dengan nama folder. Folder `htdocs\web-xl-satu` berarti `base_url` harus `http://localhost/web-xl-satu`. |
| **"could not find driver"** | Ekstensi MySQL PHP mati. Di XAMPP klik **Config → PHP (php.ini)**, cari `;extension=pdo_mysql`, hapus tanda `;` di depannya, simpan, lalu Stop dan Start Apache. |
| **Gagal upload banner** | File harus JPG/PNG/WEBP dan maksimal 3 MB. |

### Memperbaiki "MySQL shutdown unexpectedly"

Coba urut dari atas; berhenti begitu MySQL sudah hijau.

**1. Cek apakah port 3306 dipakai program lain**
- Di XAMPP Control Panel klik tombol **Netstat** (kanan atas) dan cari angka **3306**. Kalau ada program lain selain `mysqld.exe` dari XAMPP, berarti bentrok.
- Biasanya penyebabnya MySQL lain yang terpasang (misalnya dari MySQL Installer atau Laragon). Tekan **Windows + R**, ketik `services.msc`, Enter. Cari **MySQL** / **MySQL80**, klik kanan **Stop**, lalu klik kanan **Properties → Startup type: Manual**.
- Start lagi MySQL di XAMPP.

**2. Perbaiki data MySQL yang rusak** (penyebab paling sering, misalnya setelah komputer mati mendadak)
1. Klik **Stop** MySQL di XAMPP (kalau masih ada), lalu tutup XAMPP.
2. Buka folder `C:\xampp\mysql\`
3. Ganti nama folder **`data`** menjadi **`data_old`**.
4. **Copy** folder **`backup`** lalu **Paste** di tempat yang sama, dan ganti nama hasil copy-nya menjadi **`data`**.
5. Kalau sebelumnya sudah sempat membuat database (misalnya `xlsatu_web`): copy folder **`xlsatu_web`** dan file **`ibdata1`** dari `data_old` ke `data` (pilih *Replace*). Kalau belum pernah membuat database, lewati langkah ini.
6. Buka XAMPP lagi, lalu **Start** MySQL.
7. Kalau database `xlsatu_web` belum ada di phpMyAdmin, ulangi **Langkah 5** (buat database dan import).

**3. Jalankan XAMPP sebagai Administrator**
Klik kanan **XAMPP Control Panel**, lalu pilih **Run as administrator**. Pastikan juga XAMPP terpasang di `C:\xampp`, bukan di `C:\Program Files`.

**Masih gagal?** Klik tombol **Logs** di baris MySQL, pilih **mysql_error.log**, lalu salin 20 baris terakhir. Dari situ penyebab pastinya bisa terlihat.

---

## Catatan untuk Mac

- Install **XAMPP for macOS**. Folder web-nya ada di `/Applications/XAMPP/xamppfiles/htdocs/`
- Buka aplikasi **XAMPP** (manager-osx), lalu di tab *Manage Servers* Start **Apache Web Server** dan **MySQL Database**.
- Langkah lainnya sama persis: phpMyAdmin di `http://localhost/phpmyadmin`, user `root`, password kosong.
- Kalau upload banner gagal, buka Terminal dan jalankan:
  ```bash
  chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/web-xl-satu/uploads
  ```

---

Setelah semuanya lancar di komputer sendiri, ikuti **README.md** bagian *Cara upload ke hosting (cPanel)* untuk menayangkan web ke internet. Langkahnya mirip: buat database, import `database.sql`, lalu isi `config.php` dengan data dari hosting.
