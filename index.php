<?php
require __DIR__ . '/includes/bootstrap.php';

$paket = get_paket();
$promo = get_promo();
$unggulan = null;
foreach ($paket as $p) {
    if ($p['unggulan']) { $unggulan = $p; break; }
}
$unggulan = $unggulan ?? ($paket[0] ?? null);
$perJenis = paket_per_jenis($paket);
$fiber = $perJenis['Fiber'];
$fwa = $perJenis['FWA'];
$termurah = $fiber ? min(array_column($fiber, 'harga')) : null;

$page = [
    'title'       => '',
    'description' => 'Pasang internet rumah ' . config('site_name') . ' cepat, stabil, dan unlimited. Promo gratis pemasangan, cek area & daftar langsung via WhatsApp.',
    'path'        => '',
    'active'      => '',
];
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="hero-dots" aria-hidden="true"></div>
  <div class="container hero-inner">
    <div class="hero-text">
      <span class="hero-badge"><span class="live-dot" aria-hidden="true"></span>Promo pelanggan baru bulan ini</span>
      <h1>Internet Rumah <span class="hl">XL SATU<svg class="hl-swoosh" viewBox="0 0 220 18" preserveAspectRatio="none" aria-hidden="true"><path d="M3 13C50 4 120 2 217 9" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/></svg></span><br>Cepat &amp; Unlimited</h1>
      <p class="hero-lead">Streaming, kerja, sekolah online, dan main game tanpa putus. Pemasangan dibantu sales kami dari cek area sampai internet aktif.</p>
      <div class="hero-actions">
        <a class="btn btn-wa btn-lg" href="<?= e(wa_link('Halo, saya mau pasang internet ' . config('site_name') . '. Boleh info paket dan promonya?')) ?>" target="_blank" rel="noopener" data-track="wa"><?= icon('chat', 20) ?> Daftar via WhatsApp</a>
        <a class="btn btn-glass btn-lg" href="<?= e(url('paket')) ?>">Lihat Paket <span aria-hidden="true">→</span></a>
      </div>
      <ul class="hero-stats">
        <li><strong><?= count($paket) ?>+</strong><span>Pilihan paket</span></li>
        <li><strong>24 Jam</strong><span>Layanan setiap hari</span></li>
        <li><strong>Fiber &amp; FWA</strong><span>Kabel atau tanpa kabel</span></li>
      </ul>
    </div>

    <?php if ($unggulan): ?>
    <div class="hero-visual">
      <div class="hero-waves" aria-hidden="true"><span></span><span></span><span></span></div>
      <span class="float-chip chip-1" aria-hidden="true"><?= icon('bolt', 18) ?> Hingga <?= max(array_column($paket, 'kecepatan')) ?> Mbps</span>
      <span class="float-chip chip-2" aria-hidden="true"><?= icon('wifi', 18) ?> Fiber &amp; FWA</span>
      <span class="float-chip chip-3" aria-hidden="true"><?= icon('tool', 18) ?> Gratis Instalasi</span>

      <div class="hero-card">
        <div class="hc-top">
          <span class="hc-label"><?= e($unggulan['label'] ?: 'Paket Pilihan') ?></span>
          <p class="hc-name"><?= e($unggulan['nama']) ?></p>
          <p class="hc-speed"><strong><?= (int) $unggulan['kecepatan'] ?></strong> Mbps</p>
        </div>
        <div class="hc-body">
          <?php if (!empty($unggulan['harga_coret']) && $unggulan['harga_coret'] > $unggulan['harga']): ?>
            <p class="price-from">Harga normal <s><?= e(rupiah($unggulan['harga_coret'])) ?></s></p>
          <?php endif; ?>
          <p class="price-big"><?= e(rupiah($unggulan['harga'])) ?><small><?= e(periode_label($unggulan)) ?></small></p>
          <ul>
            <li><?= icon('devices', 18) ?>Hingga <?= paket_perangkat($unggulan) ?> perangkat</li>
            <li><?= icon('router', 18) ?><?= (int) $unggulan['biaya_instalasi'] > 0 ? 'Biaya instalasi ' . e(rupiah($unggulan['biaya_instalasi'])) : 'Gratis biaya instalasi' ?></li>
            <li><?= icon('receipt', 18) ?><?= $unggulan['ppn_termasuk'] ? 'Sudah termasuk PPN 11%' : 'Belum termasuk PPN 11%' ?></li>
          </ul>
          <a class="btn btn-primary btn-block" href="<?= e(wa_link(wa_message_paket($unggulan))) ?>" target="_blank" rel="noopener" data-track="wa" data-paket="<?= e(paket_judul($unggulan)) ?>">Ambil Paket Ini</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <svg class="hero-wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 50C240 90 480 10 720 30s480 50 720 10V80H0Z" fill="#fff"/></svg>
</section>

<?php if ($promo): ?>
<section class="section" aria-label="Banner promo">
  <div class="container">
    <?php render_promo_slider($promo); ?>
  </div>
</section>
<?php endif; ?>

<section class="section" id="paket">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Paket internet</span>
      <h2>Pilihan paket sesuai kebutuhan internet rumah kamu</h2>
      <p>Tersedia dua jenis layanan, sesuaikan dengan jaringan di alamat kamu.</p>
    </div>
    <?php render_jenis_info(false); ?>
  </div>
</section>

<?php if ($fiber): ?>
<section class="section section-soft" id="fiber">
  <div class="container">
    <div class="section-head">
      <span class="jenis-badge jenis-badge-fiber">Fiber · Jaringan Kabel</span>
      <h2>Paket Fiber</h2>
      <?php if ($termurah): ?><p>Mulai dari <strong><?= e(rupiah($termurah)) ?></strong> per bulan. Untuk alamat yang sudah tercover jaringan kabel.</p><?php endif; ?>
    </div>
    <?php render_package_list(array_slice($fiber, 0, 6), false); ?>
    <div class="text-center mt-3">
      <a class="btn btn-primary" href="<?= e(url('paket#fiber')) ?>">Lihat Semua <?= count($fiber) ?> Paket Fiber</a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($fwa): ?>
<section class="section" id="fwa">
  <div class="container">
    <div class="section-head">
      <span class="jenis-badge jenis-badge-fwa">FWA · Tanpa Kabel</span>
      <h2>Belum Tercover Kabel? Pakai FWA</h2>
      <p>Internet rumah tanpa tarik kabel, cukup pakai modem. Pilihan untuk alamat yang belum terjangkau jaringan kabel.</p>
    </div>
    <?php render_package_list($fwa, false); ?>
    <p class="note text-center mt-2">Harga dapat berubah sewaktu-waktu. Hubungi sales untuk info harga &amp; promo terbaru di area kamu.</p>
  </div>
</section>
<?php endif; ?>

<?php render_keunggulan(); ?>

<?php render_myxl($unggulan); ?>

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
