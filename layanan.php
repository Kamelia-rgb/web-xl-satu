<?php
require __DIR__ . '/includes/bootstrap.php';

$page = [
    'title'       => 'Profil Layanan XL Satu',
    'description' => 'Kenali layanan internet rumah XL Satu: Fiber (jaringan kabel) dan FWA (tanpa kabel, pakai modem). Cocok untuk keluarga, kerja, sekolah online, streaming, dan gaming.',
    'path'        => 'layanan',
    'active'      => 'layanan',
];
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Layanan</p>
    <h1>Profil Layanan XL Satu</h1>
    <p>Internet rumah untuk seluruh keluarga, tersedia lewat jaringan kabel (Fiber) maupun tanpa kabel (FWA).</p>
  </div>
</section>

<section class="section">
  <div class="container split">
    <div>
      <span class="eyebrow">Tentang layanan</span>
      <h2>Satu langganan untuk seluruh keluarga</h2>
      <p>XL Satu adalah layanan internet rumah yang dirancang untuk kebutuhan keluarga masa kini. Tersedia dua pilihan: <strong>Fiber</strong> lewat jaringan kabel untuk alamat yang sudah tercover, dan <strong>FWA</strong> tanpa tarik kabel, cukup pakai modem, untuk alamat yang belum tercover. Keduanya stabil untuk banyak perangkat sekaligus, mulai dari HP, laptop, smart TV, hingga CCTV.</p>
      <p>Sebagai mitra penjualan, kami membantu kamu dari awal: cek jangkauan jaringan, memilih paket yang pas, mengurus pendaftaran, hingga menjadwalkan pemasangan oleh teknisi.</p>
      <ul class="checklist">
        <li>Pilihan Fiber (kabel) atau FWA (tanpa kabel)</li>
        <li>Unlimited tanpa batas kuota</li>
        <li>Perangkat WiFi / modem disediakan</li>
        <li>Pendampingan sales via WhatsApp</li>
      </ul>
    </div>
    <div class="illustration">
      <img src="<?= e(url('assets/img/layanan.svg')) ?>" alt="Ilustrasi internet rumah untuk seluruh keluarga" width="600" height="460" loading="lazy">
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Jenis layanan</span>
      <h2>Fiber atau FWA?</h2>
    </div>
    <?php render_jenis_info(false); ?>
    <div class="text-center mt-3"><a class="btn btn-primary" href="<?= e(url('paket')) ?>">Lihat Paket Fiber &amp; FWA</a></div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Cocok untuk</span>
      <h2>Semua aktivitas online jadi lancar</h2>
    </div>
    <div class="grid grid-3">
      <div class="feature"><div class="feature-icon"><?= icon('tv') ?></div><h3>Streaming &amp; Hiburan</h3><p>Nonton film dan video resolusi tinggi di smart TV tanpa buffering.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('wifi') ?></div><h3>Kerja &amp; Sekolah Online</h3><p>Video call dan meeting online lancar, upload tugas tanpa menunggu lama.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('game') ?></div><h3>Gaming</h3><p>Ping rendah dan koneksi stabil untuk main game online.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('phone') ?></div><h3>Banyak Perangkat</h3><p>Seluruh anggota keluarga bisa online bersamaan.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('shield') ?></div><h3>Usaha &amp; Toko</h3><p>Mendukung kasir online, CCTV, dan WiFi untuk pelanggan.</p></div>
      <div class="feature"><div class="feature-icon"><?= icon('support') ?></div><h3>Bantuan Mudah</h3><p>Ada kendala? Hubungi sales kami, kami bantu teruskan.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Pemasangan</span>
      <h2>Alur pemasangan</h2>
    </div>
    <?php render_steps(); ?>
    <div class="text-center mt-3">
      <a class="btn btn-primary" href="<?= e(url('paket')) ?>">Lihat Daftar Paket</a>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container"><?php render_cta(); ?></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
