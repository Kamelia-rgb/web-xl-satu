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
      <h2 style="font-size:1.35rem;margin:0"><?= e(paket_judul($unggulan)) ?></h2>
      <p class="price-big"><?= e(rupiah($unggulan['harga'])) ?> <small><?= e(periode_label($unggulan)) ?></small></p>
      <?php if (!empty($unggulan['harga_coret']) && $unggulan['harga_coret'] > $unggulan['harga']): ?>
        <p class="price-from">Harga normal <s><?= e(rupiah($unggulan['harga_coret'])) ?></s></p>
      <?php endif; ?>
      <ul>
        <li>Hingga <?= paket_perangkat($unggulan) ?> perangkat</li>
        <li><?= (int) $unggulan['biaya_instalasi'] > 0 ? 'Biaya instalasi ' . e(rupiah($unggulan['biaya_instalasi'])) : 'Gratis biaya instalasi' ?></li>
        <li><?= $unggulan['ppn_termasuk'] ? 'Sudah termasuk PPN 11%' : 'Belum termasuk PPN 11%' ?></li>
        <li>Ideal untuk <?= e(implode(', ', array_slice(paket_ideal($unggulan), 2, 3))) ?></li>
      </ul>
      <a class="btn btn-primary btn-block" href="<?= e(wa_link(wa_message_paket($unggulan))) ?>" target="_blank" rel="noopener" data-track="wa" data-paket="<?= e(paket_judul($unggulan)) ?>">Ambil Paket Ini</a>
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

<?php render_sekali_bayar($paket); ?>

<section class="section" id="paket">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Paket internet</span>
      <h2>Pilihan paket sesuai kebutuhan internet rumah kamu</h2>
      <?php if ($termurah): ?><p>Mulai dari <strong><?= e(rupiah($termurah)) ?></strong> per bulan. Tersedia Internet Only, Internet + TV, dan paket bonus.</p><?php endif; ?>
    </div>
    <?php render_package_list(array_slice($paket, 0, 6), false); ?>
    <div class="text-center mt-3">
      <a class="btn btn-primary" href="<?= e(url('paket')) ?>">Lihat Semua <?= count($paket) ?> Paket</a>
    </div>
    <p class="note text-center mt-2">Harga dapat berubah sewaktu-waktu. Hubungi sales untuk info harga &amp; promo terbaru di area kamu.</p>
  </div>
</section>

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
