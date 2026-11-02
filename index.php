<?php
require __DIR__ . '/includes/bootstrap.php';

$paket = get_paket();
$promo = get_promo();
$unggulan = null;
foreach ($paket as $p) {
    if ($p['unggulan']) { $unggulan = $p; break; }
}
$unggulan = $unggulan ?? ($paket[0] ?? null);
$termurah = $paket ? min(array_column($paket, 'harga')) : null;

$page = [
    'title'       => '',
    'description' => 'Pasang internet rumah ' . config('site_name') . ' cepat, stabil, dan unlimited. Promo gratis pemasangan, cek area & daftar langsung via WhatsApp.',
    'path'        => '',
    'active'      => '',
];
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-inner">
    <div>
      <span class="hero-badge">🔥 Promo pelanggan baru bulan ini</span>
      <h1>Internet Rumah <span class="hl"><?= e(config('site_name')) ?></span>, Cepat &amp; Unlimited</h1>
      <p class="hero-lead">Streaming, kerja, sekolah online, dan main game tanpa putus. Pemasangan dibantu sales kami dari cek area sampai internet aktif.</p>
      <div class="hero-actions">
        <a class="btn btn-wa" href="<?= e(wa_link('Halo, saya mau pasang internet ' . config('site_name') . '. Boleh info paket dan promonya?')) ?>" target="_blank" rel="noopener" data-track="wa">Daftar via WhatsApp</a>
        <a class="btn btn-outline-light" href="<?= e(url('paket')) ?>">Lihat Paket</a>
      </div>
      <ul class="hero-points">
        <li>Tanpa batas kuota</li>
        <li>Gratis cek area</li>
        <li>Teknisi profesional</li>
      </ul>
    </div>

    <?php if ($unggulan): ?>
    <div class="hero-card">
      <p class="eyebrow"><?= e($unggulan['label'] ?: 'Paket Pilihan') ?></p>
      <h2 style="font-size:1.35rem;margin:0"><?= e($unggulan['nama']) ?> · <?= (int) $unggulan['kecepatan'] ?> Mbps</h2>
      <p class="price-big"><?= e(rupiah($unggulan['harga'])) ?> <small>/bulan</small></p>
      <?php if (!empty($unggulan['harga_coret']) && $unggulan['harga_coret'] > $unggulan['harga']): ?>
        <p class="price-from">Harga normal <s><?= e(rupiah($unggulan['harga_coret'])) ?></s></p>
      <?php endif; ?>
      <ul>
        <?php foreach (array_slice(lines((string) $unggulan['benefit']), 0, 4) as $b): ?><li><?= e($b) ?></li><?php endforeach; ?>
      </ul>
      <a class="btn btn-primary btn-block" href="<?= e(wa_link(wa_message_paket($unggulan))) ?>" target="_blank" rel="noopener" data-track="wa" data-paket="<?= e($unggulan['nama']) ?>">Ambil Paket Ini</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($promo): ?>
<section class="section" aria-label="Banner promo">
  <div class="container">
    <?php render_promo_slider($promo); ?>
  </div>
</section>
<?php endif; ?>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Kenapa pilih kami</span>
      <h2>Internet rumah yang bisa diandalkan</h2>
      <p>Didukung jaringan fiber optik untuk koneksi yang stabil di jam sibuk.</p>
    </div>
    <div class="grid grid-4">
      <div class="feature"><div class="feature-icon"><?= icon('speed') ?></div><h3>Kecepatan Stabil</h3><p>Jaringan fiber optik, download &amp; upload lancar sepanjang hari.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('infinity') ?></div><h3>Unlimited</h3><p>Internetan sepuasnya tanpa khawatir kuota habis.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('tool') ?></div><h3>Pemasangan Cepat</h3><p>Teknisi datang ke rumah setelah area dinyatakan tercover.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('support') ?></div><h3>Dibantu Sales</h3><p>Tanya apa saja lewat WhatsApp, dari daftar sampai aktif.</p></div>
    </div>
  </div>
</section>

<section class="section" id="paket">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Paket internet</span>
      <h2>Pilih kecepatan sesuai kebutuhan</h2>
      <?php if ($termurah): ?><p>Mulai dari <strong><?= e(rupiah($termurah)) ?></strong> per bulan. Semua paket unlimited.</p><?php endif; ?>
    </div>
    <div class="packages">
      <?php foreach ($paket as $p) { render_package_card($p); } ?>
    </div>
    <p class="note text-center mt-2">Harga dapat berubah sewaktu-waktu. Hubungi sales untuk info harga &amp; promo terbaru di area kamu.</p>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Cara pemasangan</span>
      <h2>4 langkah mudah, internet langsung aktif</h2>
    </div>
    <?php render_steps(); ?>
    <div class="text-center mt-3">
      <a class="btn btn-primary" href="<?= e(url('kontak#daftar')) ?>">Isi Formulir Pendaftaran</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Pertanyaan umum</span>
      <h2>Yang sering ditanyakan</h2>
    </div>
    <?php render_faq(); ?>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container"><?php render_cta(); ?></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
