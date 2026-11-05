<?php
require __DIR__ . '/includes/bootstrap.php';

$page = [
    'title'       => 'Tentang Kami',
    'description' => nama_usaha() . ' adalah website mitra penjualan internet rumah ' . config('site_name') . ', dikelola oleh ' . nama_sales() . ' (kantor ' . kantor_sales() . '). Bukan situs resmi XL.',
    'path'        => 'tentang',
];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Tentang Kami</p>
    <h1>Tentang Kami</h1>
    <p>Siapa yang mengelola website ini dan bagaimana kami membantu kamu pasang internet rumah <?= e(config('site_name')) ?>.</p>
  </div>
</section>

<section class="section">
  <div class="container prose">
    <h2>Siapa kami</h2>
    <p><strong><?= e(nama_usaha()) ?></strong> adalah website milik <strong><?= e(nama_sales()) ?></strong>, sales <strong>mitra penjualan</strong> layanan internet rumah <?= e(config('site_name')) ?> yang berkantor di <?= e(kantor_sales()) ?>. Kami melayani pendaftaran untuk area <?= e(config('area_layanan')) ?>.</p>

    <div class="notice-box">
      <strong>Website ini bukan situs resmi XL.</strong> Kami adalah mitra penjualan yang membantu pendaftaran pelanggan baru. Layanan internet, pemasangan, dan tagihan disediakan langsung oleh XL. Informasi resmi produk dapat kamu cek di website dan aplikasi resmi XL.
    </div>

    <h2>Apa yang kami bantu</h2>
    <ul>
      <li>Menjelaskan pilihan paket Fiber (kabel) dan FWA (tanpa kabel) sesuai kebutuhan kamu.</li>
      <li>Mengecek apakah alamat kamu sudah tercover jaringan.</li>
      <li>Membantu proses pendaftaran sampai jadwal pemasangan oleh teknisi.</li>
      <li>Mendampingi lewat WhatsApp kalau ada pertanyaan setelah pemasangan.</li>
    </ul>

    <h2>Hubungi kami</h2>
    <ul>
      <li>Sales: <?= e(nama_sales()) ?></li>
      <li>Alamat kantor: <?= e(alamat_kantor()) ?></li>
      <li>WhatsApp: <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" data-track="wa"><?= e(config('wa_display')) ?></a></li>
      <?php if (config('email')): ?><li>Email: <a href="mailto:<?= e(config('email')) ?>"><?= e(config('email')) ?></a></li><?php endif; ?>
      <li>Jam layanan: <?= e(config('jam_layanan')) ?></li>
    </ul>

    <h2>Kendala setelah pemasangan</h2>
    <p>Untuk gangguan jaringan, kamu juga bisa langsung menghubungi Customer Service XL di <a href="<?= e(tel_link((string) config('cs_xl', '820'))) ?>"><?= e(config('cs_xl', '820')) ?></a> (dari nomor XL) atau <a href="<?= e(tel_link((string) config('cs_non_xl', '0817 0123 442'))) ?>"><?= e(config('cs_non_xl', '0817 0123 442')) ?></a> (dari nomor non-XL).</p>

    <p class="mt-3"><a class="btn btn-primary" href="<?= e(url('kontak')) ?>">Daftar Pemasangan</a></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
