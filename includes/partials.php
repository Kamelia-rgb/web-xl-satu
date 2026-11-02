<?php
/** Potongan tampilan yang dipakai di beberapa halaman. */

function wa_message_paket(array $p): string
{
    return 'Halo, saya tertarik pasang ' . $p['nama'] . ' ' . $p['kecepatan'] . ' Mbps (' . rupiah($p['harga']) . '/bulan). Apakah alamat saya bisa dipasang?';
}

function render_package_card(array $p): void
{
    $benefits = lines((string) $p['benefit']);
    ?>
    <article class="package<?= $p['unggulan'] ? ' featured' : '' ?>">
      <?php if (!empty($p['label'])): ?><span class="package-badge"><?= e($p['label']) ?></span><?php endif; ?>
      <h3 class="package-name"><?= e($p['nama']) ?></h3>
      <p class="package-speed"><?= (int) $p['kecepatan'] ?> <small>Mbps</small></p>
      <?php if (!empty($p['harga_coret']) && $p['harga_coret'] > $p['harga']): ?>
        <span class="package-old"><?= e(rupiah($p['harga_coret'])) ?></span>
      <?php endif; ?>
      <p class="package-price"><?= e(rupiah($p['harga'])) ?> <small>/bulan</small></p>
      <?php if ($benefits): ?>
        <ul class="package-benefits">
          <?php foreach ($benefits as $b): ?><li><?= e($b) ?></li><?php endforeach; ?>
        </ul>
      <?php else: ?><div style="flex:1"></div><?php endif; ?>
      <div class="package-actions">
        <a class="btn btn-wa" href="<?= e(wa_link(wa_message_paket($p))) ?>" target="_blank" rel="noopener" data-track="wa" data-paket="<?= e($p['nama']) ?>">Pesan via WhatsApp</a>
        <a class="btn btn-outline btn-sm" href="<?= e(url('kontak?paket=' . (int) $p['id'] . '#daftar')) ?>">Isi Formulir Pendaftaran</a>
      </div>
    </article>
    <?php
}

function render_promo_slider(array $promos): void
{
    $promos = array_values(array_filter($promos, fn ($p) => !empty($p['gambar']) && !empty($p['tampil_banner'])));
    if (!$promos) {
        return;
    }
    ?>
    <div class="slider" data-slider>
      <div class="slider-track">
        <?php foreach ($promos as $i => $p): ?>
          <a class="slide" href="<?= e(url('promo')) ?>">
            <img src="<?= e(url($p['gambar'])) ?>" alt="<?= e($p['judul']) ?>" width="1200" height="450"<?= $i > 0 ? ' loading="lazy"' : '' ?>>
          </a>
        <?php endforeach; ?>
      </div>
      <button class="slider-nav prev" type="button" aria-label="Banner sebelumnya">‹</button>
      <button class="slider-nav next" type="button" aria-label="Banner berikutnya">›</button>
      <div class="slider-dots"></div>
    </div>
    <?php
}

function icon(string $name): string
{
    $paths = [
        'speed'   => '<path d="M12 14l4-4"/><path d="M3.3 17a9 9 0 1 1 17.4 0"/>',
        'infinity'=> '<path d="M12 12c-2-2.7-4-4-6-4a4 4 0 0 0 0 8c2 0 4-1.3 6-4Zm0 0c2 2.7 4 4 6 4a4 4 0 0 0 0-8c-2 0-4 1.3-6 4Z"/>',
        'tool'    => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.2L3 17.8V21h3.2l6.3-6.3a4 4 0 0 0 5.2-5.4l-2.6 2.6-2.4-.6-.6-2.4Z"/>',
        'support' => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="2.5" y="13" width="4" height="6" rx="1.5"/><rect x="17.5" y="13" width="4" height="6" rx="1.5"/><path d="M20 19a3 3 0 0 1-3 3h-3"/>',
        'phone'   => '<rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M11 18h2"/>',
        'wifi'    => '<path d="M2 9a15 15 0 0 1 20 0"/><path d="M5 12.5a10 10 0 0 1 14 0"/><path d="M8.5 16a5 5 0 0 1 7 0"/><circle cx="12" cy="19.5" r="1"/>',
        'map'     => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'clock'   => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'chat'    => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-4.9A8 8 0 1 1 21 12Z"/>',
        'shield'  => '<path d="M12 3l8 3v6c0 4.5-3.4 8-8 9-4.6-1-8-4.5-8-9V6Z"/><path d="M9 12l2 2 4-4"/>',
        'tv'      => '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M8 21h8"/>',
        'game'    => '<rect x="2.5" y="7" width="19" height="10" rx="5"/><path d="M7 12h3M8.5 10.5v3"/><circle cx="16" cy="11" r=".8"/><circle cx="17.5" cy="13" r=".8"/>',
    ];
    return '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

function render_faq(): void
{
    $faq = [
        ['Apakah alamat saya bisa dipasang?', 'Kirim alamat lengkap atau titik lokasi (share location) ke WhatsApp kami. Sales akan mengecek ketersediaan jaringan di area kamu, biasanya dalam hitungan menit.'],
        ['Berapa lama proses pemasangan?', 'Setelah alamat dinyatakan tercover dan data lengkap, teknisi biasanya datang dalam 1–3 hari kerja tergantung jadwal di area kamu.'],
        ['Apa saja syarat pendaftarannya?', 'Cukup foto KTP, nomor HP/WhatsApp aktif, alamat lengkap pemasangan, dan email. Semua bisa dikirim lewat WhatsApp.'],
        ['Apakah ada biaya pemasangan?', 'Ikuti promo yang sedang berlaku di halaman Promo. Sales akan menjelaskan rincian biaya dengan jelas sebelum pemasangan.'],
        ['Apakah internetnya unlimited?', 'Ya, paket internet rumah tidak dibatasi kuota. Detail kebijakan pemakaian akan dijelaskan saat pendaftaran.'],
        ['Bagaimana cara pembayaran tagihan bulanan?', 'Tagihan bisa dibayar melalui aplikasi, transfer bank, minimarket, atau dompet digital sesuai pilihan yang tersedia.'],
    ];
    ?>
    <div class="faq">
      <?php foreach ($faq as $i => [$q, $a]): ?>
        <details<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= e($q) ?></summary>
          <p><?= e($a) ?></p>
        </details>
      <?php endforeach; ?>
    </div>
    <?php
}

function render_steps(): void
{
    $steps = [
        ['Cek Area', 'Kirim alamat / share location ke WhatsApp untuk cek jaringan.'],
        ['Pilih Paket', 'Pilih kecepatan sesuai kebutuhan rumah atau usaha kamu.'],
        ['Kirim Data', 'Kirim foto KTP dan data diri lewat WhatsApp atau formulir.'],
        ['Pemasangan', 'Teknisi datang memasang perangkat. Internet langsung aktif.'],
    ];
    ?>
    <ol class="steps">
      <?php foreach ($steps as [$t, $d]): ?>
        <li><h3><?= e($t) ?></h3><p><?= e($d) ?></p></li>
      <?php endforeach; ?>
    </ol>
    <?php
}

function render_cta(): void
{
    ?>
    <div class="cta-band">
      <div>
        <h2>Siap pasang internet di rumah?</h2>
        <p>Chat sales kami sekarang, cek area gratis dan tanpa ribet.</p>
      </div>
      <a class="btn btn-accent" href="<?= e(wa_link('Halo, saya mau cek apakah alamat saya bisa dipasang internet ' . config('site_name') . '.')) ?>" target="_blank" rel="noopener" data-track="wa">Cek Area via WhatsApp</a>
    </div>
    <?php
}
