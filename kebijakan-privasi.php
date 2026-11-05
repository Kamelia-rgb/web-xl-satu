<?php
require __DIR__ . '/includes/bootstrap.php';

$page = [
    'title'       => 'Kebijakan Privasi',
    'description' => 'Kebijakan privasi website ' . config('site_name') . ': data apa yang kami kumpulkan dan bagaimana kami menggunakannya.',
    'path'        => 'kebijakan-privasi',
];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Kebijakan Privasi</p>
    <h1>Kebijakan Privasi</h1>
  </div>
</section>

<section class="section">
  <div class="container prose">
    <p>Kebijakan ini menjelaskan bagaimana kami, <?= e(nama_usaha()) ?> (dikelola oleh <?= e(nama_sales()) ?>, mitra penjualan <?= e(config('site_name')) ?>), mengumpulkan dan menggunakan data pengunjung.</p>

    <h2>Data yang kami kumpulkan</h2>
    <p>Saat kamu mengisi formulir pendaftaran, kami menyimpan nama, nomor WhatsApp, alamat pemasangan, paket yang diminati, dan catatan yang kamu tulis. Kami juga dapat mencatat sumber kunjungan (misalnya iklan yang kamu klik).</p>

    <h2>Penggunaan data</h2>
    <p>Data hanya digunakan untuk menghubungi kamu, mengecek ketersediaan jaringan, dan memproses pendaftaran serta pemasangan layanan. Kami tidak menjual data kamu kepada pihak lain.</p>

    <h2>Cookie dan pelacakan iklan</h2>
    <p>Website ini dapat menggunakan cookie serta alat analitik dan iklan seperti Google Ads, Google Analytics, dan Meta Pixel untuk mengukur efektivitas iklan. Kamu dapat mengatur atau menolak cookie melalui pengaturan browser.</p>

    <h2>Penyimpanan dan penghapusan data</h2>
    <p>Data disimpan selama diperlukan untuk proses pendaftaran dan layanan. Kamu dapat meminta data kamu dihapus dengan menghubungi kami melalui WhatsApp <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"><?= e(config('wa_display')) ?></a>.</p>

    <h2>Perubahan kebijakan</h2>
    <p>Kebijakan ini dapat diperbarui sewaktu-waktu. Perubahan akan ditampilkan di halaman ini.</p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
