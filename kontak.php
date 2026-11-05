<?php
require __DIR__ . '/includes/bootstrap.php';

$paket = get_paket();
$paketById = array_column($paket, null, 'id');

ensure_leads_lokasi();
$old = ['nama' => '', 'no_wa' => '', 'alamat' => '', 'kota' => '', 'lokasi' => '', 'paket_id' => (string) (int) ($_GET['paket'] ?? 0), 'catatan' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    foreach ($old as $key => $_) {
        $old[$key] = trim((string) ($_POST[$key] ?? ''));
    }
    $hasil = proses_lead($_POST, $paketById);
    $errors = $hasil['errors'];
    if (!$errors) {
        $_SESSION['lead_done'] = $hasil['lead'];
        redirect('terima-kasih');
    }
}

$page = [
    'title'       => 'Kontak & Informasi Pemasangan',
    'description' => 'Daftar pemasangan internet rumah ' . config('site_name') . '. Isi formulir atau chat WhatsApp ' . config('wa_display') . ' untuk cek area dan jadwal pemasangan.',
    'path'        => 'kontak',
    'active'      => 'kontak',
];
require __DIR__ . '/includes/header.php';

$err = function (string $f) use ($errors): string {
    return isset($errors[$f]) ? '<p class="field-error">' . e($errors[$f]) . '</p>' : '';
};
$cls = function (string $f) use ($errors): string {
    return isset($errors[$f]) ? ' has-error' : '';
};
?>

<section class="page-hero">
  <div class="container">
    <p class="breadcrumb"><a href="<?= e(url()) ?>">Beranda</a> / Kontak &amp; Pemasangan</p>
    <h1>Kontak &amp; Informasi Pemasangan</h1>
    <p>Isi formulir di bawah atau langsung chat WhatsApp. Sales kami akan menghubungi kamu untuk cek area dan jadwal pemasangan.</p>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div>
      <h2>Hubungi sales kami</h2>
      <ul class="info-list">
        <li>
          <span class="feature-icon"><?= icon('users') ?></span>
          <div><strong>Sales</strong><?= e(nama_sales()) ?>, mitra penjualan <?= e(config('site_name')) ?><br><small><?= e(alamat_kantor()) ?></small></div>
        </li>
        <li>
          <span class="feature-icon"><?= icon('chat') ?></span>
          <div><strong>WhatsApp</strong><a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" data-track="wa"><?= e(config('wa_display')) ?></a></div>
        </li>
        <li>
          <span class="feature-icon"><?= icon('clock') ?></span>
          <div><strong>Jam layanan</strong><?= e(config('jam_layanan')) ?></div>
        </li>
        <li>
          <span class="feature-icon"><?= icon('map') ?></span>
          <div><strong>Area pemasangan</strong><?= e(config('area_layanan')) ?></div>
        </li>
      </ul>
      <a class="btn btn-wa btn-block" href="<?= e(wa_link('Halo, saya mau cek apakah alamat saya bisa dipasang internet ' . config('site_name') . '.')) ?>" target="_blank" rel="noopener" data-track="wa">Chat WhatsApp Sekarang</a>

      <div class="mt-3"><?php render_bantuan(); ?></div>

      <h3 class="mt-3">Syarat pendaftaran</h3>
      <ul class="checklist">
        <li>Foto KTP pemohon</li>
        <li>Nomor HP / WhatsApp aktif</li>
        <li>Alamat lengkap &amp; titik lokasi (share location)</li>
        <li>Alamat email aktif</li>
      </ul>
    </div>

    <div class="form-card" id="daftar">
      <h2>Formulir Calon Pelanggan</h2>
      <p class="note">Data kamu hanya digunakan untuk proses pemasangan. Lihat <a href="<?= e(url('kebijakan-privasi')) ?>">Kebijakan Privasi</a>.</p>

      <?php if (!empty($errors['form'])): ?>
        <div class="alert alert-info"><?= e($errors['form']) ?></div>
      <?php elseif ($errors): ?>
        <div class="alert alert-error">Ada data yang belum benar, silakan cek kembali.</div>
      <?php endif; ?>

      <form method="post" action="<?= e(url('kontak')) ?>#daftar" novalidate>
        <?= csrf_field() ?>
        <div class="hp-field" aria-hidden="true">
          <label for="website">Website</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-row<?= $cls('nama') ?>">
          <label for="nama">Nama lengkap <span class="req">*</span></label>
          <input type="text" id="nama" name="nama" value="<?= e($old['nama']) ?>" autocomplete="name" required maxlength="100">
          <?= $err('nama') ?>
        </div>

        <div class="form-row<?= $cls('no_wa') ?>">
          <label for="no_wa">Nomor WhatsApp <span class="req">*</span></label>
          <input type="tel" id="no_wa" name="no_wa" value="<?= e($old['no_wa']) ?>" autocomplete="tel" inputmode="tel" placeholder="0812 3456 7890" required maxlength="20">
          <?= $err('no_wa') ?>
        </div>

        <div class="form-row<?= $cls('alamat') ?>">
          <label for="alamat">Alamat pemasangan <span class="req">*</span></label>
          <textarea id="alamat" name="alamat" required maxlength="1000" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"><?= e($old['alamat']) ?></textarea>
          <?= $err('alamat') ?>
        </div>

        <div class="form-row<?= $cls('lokasi') ?>">
          <label for="lokasi">Titik lokasi rumah <span class="optional">(opsional, mempercepat cek area)</span></label>
          <input type="text" id="lokasi" name="lokasi" value="<?= e($old['lokasi']) ?>" maxlength="255" placeholder="Link Google Maps / share location" inputmode="url">
          <button type="button" class="loc-btn" data-geo>📍 Ambil lokasi saya otomatis</button>
          <p class="hint" data-geo-status>Atau tempel link "Bagikan" dari Google Maps / lokasi WhatsApp.</p>
          <?= $err('lokasi') ?>
        </div>

        <div class="form-row-2">
          <div class="form-row">
            <label for="kota">Kota / Kabupaten</label>
            <input type="text" id="kota" name="kota" value="<?= e($old['kota']) ?>" autocomplete="address-level2" maxlength="100">
          </div>
          <div class="form-row">
            <label for="paket_id">Paket yang diminati</label>
            <select id="paket_id" name="paket_id">
              <option value="0">Belum tahu / minta saran</option>
              <?php foreach ($paket as $p): ?>
                <option value="<?= (int) $p['id'] ?>"<?= (int) $old['paket_id'] === (int) $p['id'] ? ' selected' : '' ?>>
                  <?= e(paket_judul($p) . ' – ' . rupiah($p['harga']) . periode_label($p) . ' (' . (paket_jenis($p) === 'FWA' ? 'FWA, ' : '') . $p['kategori'] . ')') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row<?= $cls('catatan') ?>">
          <label for="catatan">Catatan (opsional)</label>
          <textarea id="catatan" name="catatan" maxlength="1000" placeholder="Contoh: patokan rumah, waktu yang enak dihubungi"><?= e($old['catatan']) ?></textarea>
          <?= $err('catatan') ?>
        </div>

        <button class="btn btn-primary btn-block" type="submit">Kirim Pendaftaran</button>
      </form>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Informasi pemasangan</span>
      <h2>Alur pemasangan</h2>
    </div>
    <?php render_steps(); ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head"><h2>Pertanyaan umum</h2></div>
    <?php render_faq(); ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
