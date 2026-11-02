<?php
require __DIR__ . '/includes/bootstrap.php';

$promo = get_promo();

$page = [
    'title'       => 'Promo Terbaru',
    'description' => 'Promo internet rumah ' . config('site_name') . ' terbaru: gratis pemasangan, diskon bulan pertama, dan bonus lainnya. Klaim promo via WhatsApp.',
    'path'        => 'promo',
    'active'      => 'promo',
];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Promo</p>
    <h1>Promo Terbaru</h1>
    <p>Jangan sampai ketinggalan, promo berlaku selama periode tertentu dan kuota terbatas.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($promo): ?>
      <?php render_promo_slider($promo); ?>

      <div class="grid grid-3 mt-3">
        <?php foreach ($promo as $p): ?>
          <article class="promo-card">
            <?php if (!empty($p['gambar'])): ?>
              <img src="<?= e(url($p['gambar'])) ?>" alt="<?= e($p['judul']) ?>" width="640" height="360" loading="lazy">
            <?php endif; ?>
            <div class="promo-body">
              <h3><?= e($p['judul']) ?></h3>
              <?php if (!empty($p['berlaku_sampai'])): ?>
                <p class="promo-date">Berlaku sampai <?= e(tanggal_indo($p['berlaku_sampai'])) ?></p>
              <?php endif; ?>
              <p><?= nl2br(e($p['deskripsi'])) ?></p>
              <a class="btn btn-wa" href="<?= e(wa_link('Halo, saya mau klaim promo "' . $p['judul'] . '". Bagaimana caranya?')) ?>" target="_blank" rel="noopener" data-track="wa" data-paket="Promo: <?= e($p['judul']) ?>">Klaim Promo</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Belum ada promo yang berlaku saat ini. Hubungi sales kami untuk penawaran terbaik.</div>
    <?php endif; ?>
    <p class="note text-center mt-2">Syarat &amp; ketentuan berlaku. Promo dapat berbeda di setiap area.</p>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container"><?php render_cta(); ?></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
