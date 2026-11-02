<?php
require __DIR__ . '/includes/bootstrap.php';

$paket = get_paket();
$speeds = array_column($paket, 'kecepatan');
$range = $speeds ? min($speeds) . ' Mbps hingga ' . max($speeds) . ' Mbps' : '';

$page = [
    'title'       => 'Daftar Paket Internet',
    'description' => 'Daftar harga paket internet rumah ' . config('site_name') . ' terbaru: Internet Only, Internet + TV, dan paket bonus. Kecepatan ' . $range . ', pesan langsung via WhatsApp.',
    'path'        => 'paket',
    'active'      => 'paket',
];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Paket Internet</p>
    <h1>Daftar Paket Internet</h1>
    <p>Pilih kategori dan kecepatan sesuai jumlah perangkat dan aktivitas di rumah. Klik <strong>Chat WA</strong> atau <strong>Isi Form</strong> untuk daftar.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($paket): ?>
      <?php render_package_list($paket); ?>
    <?php else: ?>
      <div class="alert alert-info">Daftar paket sedang diperbarui. Silakan hubungi sales via WhatsApp untuk info terbaru.</div>
    <?php endif; ?>
    <p class="note text-center mt-2">Keterangan PPN tercantum di tiap paket. Harga dan benefit dapat berubah sewaktu-waktu sesuai ketentuan.</p>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Panduan memilih</span>
      <h2>Berapa kecepatan yang saya butuhkan?</h2>
    </div>
    <div class="grid grid-3">
      <div class="feature"><h3>20–100 Mbps</h3><p>Rumah kecil, 4–8 perangkat. Browsing, media sosial, video call, dan streaming.</p></div>
      <div class="feature"><h3>150–400 Mbps</h3><p>Keluarga, 9–14 perangkat. Streaming 4K di beberapa TV, game online, sekolah &amp; kerja online bersamaan.</p></div>
      <div class="feature"><h3>500–1000 Mbps</h3><p>Rumah besar, usaha, atau gamer. Banyak perangkat aktif, smart home, CCTV, upload file besar.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container"><?php render_cta(); ?></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
