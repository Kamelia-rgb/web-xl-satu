<?php
require __DIR__ . '/includes/bootstrap.php';

$paket = get_paket();

$page = [
    'title'       => 'Daftar Paket Internet',
    'description' => 'Daftar harga paket internet rumah ' . config('site_name') . ' terbaru. Pilih kecepatan 30 Mbps hingga 200 Mbps, unlimited, pesan langsung via WhatsApp.',
    'path'        => 'paket',
    'active'      => 'paket',
];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Paket Internet</p>
    <h1>Daftar Paket Internet</h1>
    <p>Semua paket unlimited tanpa batas kuota. Pilih kecepatan sesuai jumlah perangkat dan aktivitas di rumah.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($paket): ?>
      <div class="packages">
        <?php foreach ($paket as $p) { render_package_card($p); } ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Daftar paket sedang diperbarui. Silakan hubungi sales via WhatsApp untuk info terbaru.</div>
    <?php endif; ?>
    <p class="note text-center mt-2">Harga belum termasuk PPN bila berlaku. Harga dan benefit dapat berubah sewaktu-waktu sesuai ketentuan.</p>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Panduan memilih</span>
      <h2>Berapa kecepatan yang saya butuhkan?</h2>
    </div>
    <div class="grid grid-3">
      <div class="feature"><h3>30 Mbps</h3><p>Rumah kecil, 3–5 perangkat. Browsing, media sosial, video call, dan streaming ringan.</p></div>
      <div class="feature"><h3>50–100 Mbps</h3><p>Keluarga, 5–12 perangkat. Streaming di beberapa TV, sekolah &amp; kerja online bersamaan.</p></div>
      <div class="feature"><h3>200 Mbps</h3><p>Rumah besar, usaha, atau gamer. Banyak perangkat aktif, upload file besar, CCTV.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container"><?php render_cta(); ?></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
