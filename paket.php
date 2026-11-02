<?php
require __DIR__ . '/includes/bootstrap.php';

$paket = get_paket();
$perJenis = paket_per_jenis($paket);
$speeds = array_column($paket, 'kecepatan');
$range = $speeds ? min($speeds) . ' Mbps hingga ' . max($speeds) . ' Mbps' : '';

$page = [
    'title'       => 'Daftar Paket Internet',
    'description' => 'Daftar harga paket internet rumah ' . config('site_name') . ' terbaru: paket Fiber (jaringan kabel) dan FWA (tanpa kabel, pakai modem). Kecepatan ' . $range . ', pesan langsung via WhatsApp.',
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
    <div class="section-head">
      <h2>Fiber atau FWA?</h2>
      <p>Pilih jenis layanan sesuai jaringan yang tersedia di alamat kamu.</p>
    </div>
    <?php render_jenis_info(); ?>
  </div>
</section>

<?php if (!$paket): ?>
<section class="section" style="padding-top:0">
  <div class="container"><div class="alert alert-info">Daftar paket sedang diperbarui. Silakan hubungi sales via WhatsApp untuk info terbaru.</div></div>
</section>
<?php endif; ?>

<?php if ($perJenis['Fiber']): ?>
<section class="section section-soft" id="fiber">
  <div class="container">
    <div class="section-head">
      <span class="jenis-badge jenis-badge-fiber">Fiber · Jaringan Kabel</span>
      <h2>Paket Fiber</h2>
      <p>Untuk alamat yang sudah tercover jaringan kabel fiber optik.</p>
    </div>
    <?php render_package_list($perJenis['Fiber']); ?>
  </div>
</section>
<?php endif; ?>

<?php if ($perJenis['FWA']): ?>
<section class="section" id="fwa">
  <div class="container">
    <div class="section-head">
      <span class="jenis-badge jenis-badge-fwa">FWA · Tanpa Kabel</span>
      <h2>Paket FWA</h2>
      <p>Tanpa tarik kabel, cukup pakai modem. Untuk alamat yang belum tercover jaringan kabel.</p>
    </div>
    <?php render_fwa_keunggulan(); ?>
    <?php render_package_list($perJenis['FWA']); ?>
    <p class="note text-center mt-2">Keterangan PPN tercantum di tiap paket. Harga dan benefit dapat berubah sewaktu-waktu sesuai ketentuan.</p>
  </div>
</section>
<?php endif; ?>

<?php render_addon_kuota(); ?>

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
