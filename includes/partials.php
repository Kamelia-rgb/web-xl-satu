<?php
/** Potongan tampilan yang dipakai di beberapa halaman. */

const PAKET_KATEGORI = ['Internet Only', 'Internet + TV', 'Internet + Bonus'];

function slugify(string $text): string
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-');
}

/** "/bulan" atau "/3 bulan" */
function periode_label(array $p): string
{
    $n = max(1, (int) ($p['periode_bulan'] ?? 1));
    return $n === 1 ? '/bulan' : '/' . $n . ' bulan';
}

function paket_judul(array $p): string
{
    return (int) $p['kecepatan'] . ' Mbps ' . $p['nama'];
}

function wa_message_paket(array $p): string
{
    return 'Halo, saya tertarik pasang ' . $p['nama'] . ' ' . (int) $p['kecepatan'] . ' Mbps (' . rupiah($p['harga']) . periode_label($p) . '). Apakah alamat saya bisa dipasang?';
}

/** Perkiraan jumlah perangkat bila tidak diisi di admin. */
function paket_perangkat(array $p): int
{
    if (!empty($p['maks_perangkat'])) {
        return (int) $p['maks_perangkat'];
    }
    $speed = (int) $p['kecepatan'];
    foreach ([20 => 4, 50 => 6, 100 => 8, 150 => 9, 250 => 10, 300 => 12, 350 => 13, 400 => 14, 500 => 16, 600 => 18] as $max => $devices) {
        if ($speed <= $max) {
            return $devices;
        }
    }
    return 25;
}

/** Label "Ideal untuk kamu": dari admin, atau otomatis sesuai kecepatan. */
function paket_ideal(array $p): array
{
    $custom = array_values(array_filter(array_map('trim', preg_split('/[,\n]/', (string) ($p['ideal_untuk'] ?? '')))));
    if ($custom) {
        return $custom;
    }
    $s = (int) $p['kecepatan'];
    $tags = ['Browsing', 'Sosial Media'];
    $tags[] = $s >= 100 ? 'Streaming 4K' : 'Streaming HD';
    $tags[] = $s >= 300 ? 'Video Conference 4K' : 'Video Conference HD';
    if ($s >= 100) $tags[] = 'Game Online';
    if ($s >= 400) $tags[] = 'Smart Home';
    elseif ($s >= 150) $tags[] = 'CCTV / Smart Device';
    return $tags;
}

/** Isi bar kecepatan (7 segmen), skala logaritmik 10–1000 Mbps. */
function speed_segments(int $speed, int $count = 7): array
{
    $fill = max(0.08, min(1, log(max($speed, 10) / 10) / log(100))) * $count;
    $out = [];
    for ($i = 0; $i < $count; $i++) {
        $out[] = (int) round(max(0, min(1, $fill - $i)) * 100);
    }
    return $out;
}

function render_package_card(array $p): void
{
    $kategori = $p['kategori'] ?: 'Internet Only';
    $hasOldPrice = !empty($p['harga_coret']) && $p['harga_coret'] > $p['harga'];
    $hasOldSpeed = !empty($p['kecepatan_coret']) && $p['kecepatan_coret'] < $p['kecepatan'];
    $periode = periode_label($p);
    ?>
    <article class="pkg pkg-<?= e(slugify($kategori)) ?> pkg-jenis-<?= e(strtolower(paket_jenis($p))) ?><?= $p['unggulan'] ? ' is-featured' : '' ?>" data-kategori="<?= e(slugify($kategori)) ?>">
      <div class="pkg-head">
        <div class="pkg-head-top">
          <span class="pkg-cat"><?= e($kategori) ?></span>
          <?php if (!empty($p['label'])): ?><span class="pkg-ribbon"><?= e($p['label']) ?></span><?php endif; ?>
        </div>
        <h3 class="pkg-title"><?= (int) $p['kecepatan'] ?> Mbps <?= e($p['nama']) ?></h3>
      </div>

      <div class="pkg-body">
        <div class="speedbar" aria-hidden="true">
          <?php foreach (speed_segments((int) $p['kecepatan']) as $i => $pct): ?>
            <span><i style="width:<?= $pct ?>%"></i></span>
          <?php endforeach; ?>
        </div>

        <div class="pkg-speed">
          <span class="pkg-speed-label">Kecepatan internet<?= $hasOldSpeed ? ' dengan Speed Booster' : ' hingga' ?></span>
          <span class="pkg-speed-val">
            <?php if ($hasOldSpeed): ?><s><?= (int) $p['kecepatan_coret'] ?> <small>Mbps</small></s><?php endif; ?>
            <strong><?= (int) $p['kecepatan'] ?></strong> <small>Mbps</small>
          </span>
        </div>

        <p class="pkg-sub">Ideal untuk kamu</p>
        <ul class="tags">
          <?php foreach (paket_ideal($p) as $tag): ?><li><?= e($tag) ?></li><?php endforeach; ?>
        </ul>

        <ul class="pkg-feats">
          <li><?= icon('devices') ?><span>Hingga <?= paket_perangkat($p) ?> Perangkat</span></li>
          <li><?= icon('router') ?><span><?= (int) $p['biaya_instalasi'] > 0 ? 'Biaya Instalasi ' . e(rupiah($p['biaya_instalasi'])) : 'Gratis Biaya Instalasi' ?></span></li>
          <li><?= icon('receipt') ?><span><?= !empty($p['ppn_termasuk']) ? 'Termasuk PPN 11%' : 'Belum Termasuk PPN 11%' ?></span></li>
          <?php foreach (lines((string) $p['benefit']) as $b): ?>
            <li><?= icon('gauge') ?><span><?= e($b) ?></span></li>
          <?php endforeach; ?>
        </ul>

        <?php if (!empty($p['bonus'])): ?>
          <div class="pkg-bonus"><?= icon('gift') ?><span><?= e($p['bonus']) ?></span></div>
        <?php endif; ?>

        <div class="pkg-price">
          <p class="pkg-from">Mulai dari<?php if ($hasOldPrice): ?> <s><?= e(rupiah($p['harga_coret']) . $periode) ?></s><?php endif; ?></p>
          <p class="pkg-amount"><?= e(rupiah($p['harga'])) ?><small><?= e($periode) ?></small></p>
        </div>

        <div class="pkg-actions">
          <a class="btn btn-wa" href="<?= e(wa_link(wa_message_paket($p))) ?>" target="_blank" rel="noopener" data-track="wa" data-paket="<?= e(paket_judul($p)) ?>"><?= icon('chat', 18) ?> Chat WA</a>
          <a class="btn btn-primary" href="<?= e(url('kontak?paket=' . (int) $p['id'] . '#daftar')) ?>"><?= icon('edit', 18) ?> Isi Form</a>
        </div>
      </div>
    </article>
    <?php
}

/** Tab filter kategori + daftar kartu paket. */
function render_package_list(array $paket, bool $withTabs = true): void
{
    $cats = array_values(array_unique(array_map(function ($p) { return $p['kategori'] ?: 'Internet Only'; }, $paket)));
    ?>
    <div class="pkg-wrap" data-pkg-filter>
      <?php if ($withTabs && count($cats) > 1): ?>
        <div class="pkg-tabs" role="tablist" aria-label="Kategori paket">
          <button type="button" role="tab" aria-selected="true" data-filter="">Semua</button>
          <?php foreach ($cats as $c): ?>
            <button type="button" role="tab" aria-selected="false" data-filter="<?= e(slugify($c)) ?>"><?= e($c) ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="pkg-grid">
        <?php foreach ($paket as $p) { render_package_card($p); } ?>
      </div>
    </div>
    <?php
}

function render_promo_slider(array $promos): void
{
    $promos = array_values(array_filter($promos, function ($p) { return !empty($p['gambar']) && !empty($p['tampil_banner']); }));
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

function icon(string $name, int $size = 26): string
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
        'devices' => '<rect x="2.5" y="6" width="12" height="15" rx="1.5"/><path d="M7 3.5h12.5a1.5 1.5 0 0 1 1.5 1.5v13"/>',
        'router'  => '<rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 16.5h.01M11 16.5h.01"/><path d="M8 13V9M16 13V9"/><path d="M13.5 5.5a3 3 0 0 1 5 0M12 3a6 6 0 0 1 8 0"/>',
        'receipt' => '<path d="M6 2.5h12v19l-3-2-3 2-3-2-3 2Z"/><path d="M9 8h6M9 12h6M9 16h3"/>',
        'gauge'   => '<path d="M12 14l3.5-3.5"/><path d="M4.2 18.5a9 9 0 1 1 15.6 0"/><path d="M12 5.5V7M5.5 12H7M17 12h1.5"/>',
        'gift'    => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M5 12v9h14v-9M12 8v13"/><path d="M12 8S10.5 3.5 8 3.5a2.2 2.2 0 0 0 0 4.5h4Zm0 0s1.5-4.5 4-4.5a2.2 2.2 0 0 1 0 4.5h-4Z"/>',
        'edit'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'bolt'    => '<circle cx="12" cy="13.5" r="8"/><path d="M10 2.5h4M12 2.5v3"/><path d="M13 9l-3 5h4l-3 5"/>',
        'pin'     => '<path d="M12 21s-6-5.5-6-10.5a6 6 0 0 1 12 0C18 15.5 12 21 12 21Z"/><circle cx="12" cy="10.5" r="2.2"/><path d="M4.5 6.5a9 9 0 0 0 0 8M19.5 6.5a9 9 0 0 1 0 8"/>',
        'plusbox' => '<rect x="3.5" y="3.5" width="17" height="17" rx="4"/><path d="M12 8v8M8 12h8"/>',
        'users'   => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20a6 6 0 0 1 12 0"/><circle cx="17" cy="9" r="2.4"/><path d="M15.5 14.2A5 5 0 0 1 21 19"/>',
        'percent' => '<rect x="3" y="3" width="18" height="18" rx="4"/><path d="M8 16l8-8"/><circle cx="8.5" cy="8.5" r="1.4"/><circle cx="15.5" cy="15.5" r="1.4"/>',
        'list'    => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M8 8.5l1.2 1.2L11.5 7.5M13.5 9h3M8 14.5l1.2 1.2 2.3-2.2M13.5 15h3"/>',
        'coin'    => '<rect x="3" y="6" width="18" height="13" rx="3"/><path d="M3 10.5h18"/><circle cx="15.5" cy="15" r="1.6"/>',
        'cable'   => '<path d="M4 20c4 0 4-6 8-6s4 6 8 6"/><path d="M8 4v4M16 4v4"/><rect x="6" y="8" width="12" height="6" rx="2"/>',
        'modem'   => '<rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 16.5h.01M11 16.5h.01"/><path d="M8.5 9.5a5 5 0 0 1 7 0M6 7a8.5 8.5 0 0 1 12 0"/>',
        'phone-call' => '<path d="M5 4h3.5l1.8 4.4-2.3 1.4a11 11 0 0 0 6.2 6.2l1.4-2.3L20 15.5V19a1.5 1.5 0 0 1-1.6 1.5A16.5 16.5 0 0 1 3.5 5.6 1.5 1.5 0 0 1 5 4Z"/><path d="M14.5 3.5a6 6 0 0 1 6 6M14.5 7a2.5 2.5 0 0 1 2.5 2.5"/>',
        'hourglass' => '<path d="M6 3h12M6 21h12"/><path d="M7 3c0 5 5 6 5 9s-5 4-5 9M17 3c0 5-5 6-5 9s5 4 5 9"/>',
        'headset' => '<path d="M4 15v-3a8 8 0 0 1 16 0v3"/><rect x="2.5" y="14" width="4" height="6" rx="1.5"/><rect x="17.5" y="14" width="4" height="6" rx="1.5"/>',
    ];
    return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

function render_faq(): void
{
    $faq = [
        ['Apakah alamat saya bisa dipasang?', 'Kirim alamat lengkap atau titik lokasi (share location) ke WhatsApp kami. Sales akan mengecek ketersediaan jaringan di area kamu, biasanya dalam hitungan menit.'],
        ['Berapa lama proses pemasangan?', 'Setelah alamat dinyatakan tercover dan data lengkap, teknisi biasanya datang dalam 1–3 hari kerja tergantung jadwal di area kamu.'],
        ['Apa saja syarat pendaftarannya?', 'Cukup foto KTP, nomor HP/WhatsApp aktif, alamat lengkap pemasangan, dan email. Semua bisa dikirim lewat WhatsApp.'],
        ['Apakah ada biaya pemasangan?', 'Ikuti promo yang sedang berlaku di halaman Promo. Sales akan menjelaskan rincian biaya dengan jelas sebelum pemasangan.'],
        ['Apakah internetnya unlimited?', 'Ya, paket internet rumah tidak dibatasi kuota. Detail kebijakan pemakaian akan dijelaskan saat pendaftaran.'],
        ['Kalau ada kendala atau gangguan setelah terpasang?', 'Hubungi Customer Service XL 24 jam: ' . config('cs_xl', '820') . ' (dari nomor XL) atau ' . config('cs_non_xl', '0817 0123 442') . ' (dari nomor non-XL). Kamu juga bisa chat sales kami via WhatsApp, kami bantu teruskan.'],
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

/** "Mengapa Harus Pilih ..." + strip keuntungan. Ubah teksnya di sini. */
function render_keunggulan(): void
{
    $items = [
        ['speed', 'Lebih Ngebut', 'Koneksi fiber optik yang andal dan stabil, bahkan di jam sibuk.'],
        ['bolt', 'Lebih Instan', 'Instalasi cepat, teknisi datang setelah area dinyatakan tercover.'],
        ['pin', 'Lebih Luas', 'Jaringan tersedia di banyak kota dan terus bertambah.'],
        ['gift', 'Lebih Untung', 'Bayar sama, speed lebih kencang. Plus bonus nonton!'],
    ];
    ?>
    <section class="section why-section">
      <div class="container">
        <div class="section-head">
          <h2>Mengapa Harus Pilih <?= e(config('site_name')) ?>?</h2>
        </div>
        <div class="why-grid">
          <?php foreach ($items as [$ic, $title, $desc]): ?>
            <div class="why-item reveal">
              <div class="why-icon"><?= icon($ic, 64) ?></div>
              <h3><?= e($title) ?></h3>
              <p><?= e($desc) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="perks reveal">
          <div class="perk"><?= icon('tool', 28) ?><div><strong>GRATIS Instalasi</strong><span><s>Rp750.000</s> Rp0,-</span></div></div>
          <div class="perk"><?= icon('bolt', 28) ?><div><strong>Instalasi Cepat</strong><span>Langsung dijadwalkan</span></div></div>
          <div class="perk"><?= icon('headset', 28) ?><div><strong>24/7</strong><span>Customer Support</span></div></div>
        </div>
      </div>
    </section>
    <?php
}

/** Bagian aplikasi myXL dengan ikon yang berputar mengelilingi ponsel saat digulir. */
function render_myxl(?array $p): void
{
    $items = [
        ['plusbox', 'Tambah Layanan'], ['coin', 'Bayar Tagihan'], ['users', 'Tambah Anggota'],
        ['percent', 'Promo Spesial'], ['headset', 'Call Center'], ['list', 'Kelola Paket'],
    ];
    $play = (string) config('link_playstore', 'https://play.google.com/store/search?q=myXL&c=apps');
    $appstore = (string) config('link_appstore', '');
    ?>
    <section class="section myxl-section" data-orbit-section>
      <div class="container myxl-grid">
        <div class="orbit-wrap" aria-hidden="true">
          <div class="orbit-ring"></div>
          <div class="orbit">
            <?php foreach ($items as $i => [$ic, $label]): ?>
              <div class="orbit-item" style="--a: <?= $i * 60 ?>deg"><div class="orbit-bubble"><?= icon($ic, 28) ?><span><?= e($label) ?></span></div></div>
            <?php endforeach; ?>
          </div>
          <div class="phone">
            <div class="phone-notch"></div>
            <div class="phone-screen">
              <div class="ps-top"><span class="ps-avatar"></span><div><b>Halo, Pelanggan</b><small>0817 xxxx xxxx</small></div></div>
              <div class="ps-card"><small>Upgrade plan kamu dan dapatkan penawaran menarik!</small></div>
              <div class="ps-menu"><span></span><span></span><span></span><span></span></div>
              <div class="ps-bill"><small>Tagihan periode ini</small><b><?= $p ? e(rupiah($p['harga'])) : 'Rp0' ?></b><i>Atur Autodebit</i></div>
              <div class="ps-plan"><small>Plan Saya</small><b><?= $p ? e($p['nama']) : config('site_name') ?></b><small>Kecepatan hingga <?= $p ? (int) $p['kecepatan'] : 0 ?> Mbps</small></div>
            </div>
          </div>
        </div>
        <div class="myxl-text reveal">
          <h2>Kelola Internet Rumah dari Aplikasi myXL</h2>
          <p>Bayar tagihan, cek dan kelola paket, tambah anggota keluarga, sampai dapat promo spesial, semua langsung dari smartphone kamu.</p>
          <div class="store-btns">
            <?php if ($appstore !== ''): ?><a class="store-btn" href="<?= e($appstore) ?>" target="_blank" rel="noopener"><small>Download di</small>App Store</a><?php endif; ?>
            <a class="store-btn" href="<?= e($play) ?>" target="_blank" rel="noopener"><small>Tersedia di</small>Google Play</a>
          </div>
        </div>
      </div>
    </section>
    <?php
}

/** Pisahkan paket berdasarkan jenis: ['Fiber' => [...], 'FWA' => [...]] */
function paket_per_jenis(array $paket): array
{
    $out = array_fill_keys(array_keys(PAKET_JENIS), []);
    foreach ($paket as $p) {
        $out[paket_jenis($p)][] = $p;
    }
    return $out;
}

/** Dua kartu penjelasan: Fiber vs FWA. */
function render_jenis_info(bool $withLinks = true): void
{
    ?>
    <div class="jenis-grid">
      <div class="jenis-card jenis-fiber reveal">
        <div class="jenis-icon"><?= icon('cable', 34) ?></div>
        <div>
          <h3>Fiber (Jaringan Kabel)</h3>
          <p>Internet rumah lewat kabel fiber optik. Paling stabil dan kencang, untuk alamat yang <strong>sudah tercover jaringan kabel</strong>.</p>
          <?php if ($withLinks): ?><a href="#fiber">Lihat paket Fiber →</a><?php endif; ?>
        </div>
      </div>
      <div class="jenis-card jenis-fwa reveal">
        <div class="jenis-icon"><?= icon('modem', 34) ?></div>
        <div>
          <h3>FWA (Tanpa Kabel)</h3>
          <p>Internet rumah tanpa tarik kabel, cukup pakai <strong>modem</strong>. Solusi untuk alamat yang <strong>belum tercover jaringan kabel</strong>.</p>
          <?php if ($withLinks): ?><a href="#fwa">Lihat paket FWA →</a><?php endif; ?>
        </div>
      </div>
    </div>
    <p class="note text-center mt-2">Belum tahu alamatmu tercover kabel atau tidak? <a href="<?= e(wa_link('Halo, saya mau cek alamat saya tercover jaringan kabel (Fiber) atau pakai FWA.')) ?>" target="_blank" rel="noopener" data-track="wa">Cek gratis via WhatsApp</a>.</p>
    <?php
}

/** Kotak "Butuh Bantuan?" dengan nomor Customer Service XL. */
function render_bantuan(): void
{
    $xl = (string) config('cs_xl', '820');
    $nonXl = (string) config('cs_non_xl', '0817 0123 442');
    ?>
    <div class="help-card">
      <h3>Butuh Bantuan?</h3>
      <p>Ada kendala atau gangguan setelah terpasang? Hubungi <strong>Customer Service XL</strong>, siap membantu setiap hari 24 jam.</p>
      <div class="help-btns">
        <a class="help-btn help-xl" href="<?= e(tel_link($xl)) ?>"><?= icon('phone-call', 30) ?><span><small>Pengguna XL</small><b><?= e($xl) ?></b></span></a>
        <a class="help-btn help-nonxl" href="<?= e(tel_link($nonXl)) ?>"><?= icon('phone-call', 30) ?><span><small>Pengguna Non-XL</small><b><?= e($nonXl) ?></b></span></a>
      </div>
    </div>
    <?php
}
