<?php
/**
 * PENGATURAN WEBSITE
 * Salin file ini menjadi "config.php", lalu isi sesuai data hosting kamu.
 * File config.php TIDAK ikut disimpan ke GitHub (berisi password database).
 */
return [
    // ---- Identitas ----
    'site_name'    => 'XL SATU',
    'tagline'      => 'Internet Rumah Cepat & Stabil',
    // Alamat lengkap website, TANPA garis miring di akhir
    'base_url'     => 'https://domainkamu.com',

    // ---- Kontak sales ----
    // Format internasional tanpa + dan tanpa spasi (0813... -> 62813...)
    'wa_number'    => '6281340607328',
    'wa_display'   => '0813 4060 7328',
    'email'        => '',
    'area_layanan' => 'Kota kamu dan sekitarnya',
    'jam_layanan'  => 'Setiap hari, 24 jam',
    // Customer Service XL untuk kendala/gangguan (tampil di halaman Kontak, FAQ & footer)
    'cs_xl'        => '820',            // untuk pengguna XL
    'cs_non_xl'    => '0817 0123 442',  // untuk pengguna non-XL
    // Teks pemberitahuan di bagian bawah web (penting untuk kebijakan iklan)
    'disclaimer'   => 'Website ini dikelola oleh mitra penjualan layanan XL Satu dan bukan situs resmi XL. Harga dan promo dapat berubah sewaktu-waktu sesuai ketentuan yang berlaku.',

    // ---- Link aplikasi myXL (boleh dikosongkan) ----
    'link_playstore' => 'https://play.google.com/store/search?q=myXL&c=apps',
    'link_appstore'  => '',   // isi link App Store myXL supaya tombol App Store muncul

    // ---- Database (lihat di cPanel > MySQL Databases) ----
    'db_dsn'       => 'mysql:host=localhost;dbname=NAMA_DATABASE;charset=utf8mb4',
    'db_user'      => 'USER_DATABASE',
    'db_pass'      => 'PASSWORD_DATABASE',

    // ---- Pelacakan iklan (boleh dikosongkan dulu) ----
    'google_tag_id'           => '',   // contoh: AW-123456789 atau G-XXXXXXX
    'google_ads_conversion'   => '',   // contoh: AW-123456789/AbCdEfGhIj (label konversi)
    'meta_pixel_id'           => '',   // contoh: 123456789012345

    'timezone'     => 'Asia/Jakarta',
];
