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
    <article class="pkg pkg-<?= e(slugify($kategori)) ?><?= $p['unggulan'] ? ' is-featured' : '' ?>" data-kategori="<?= e(slugify($kategori)) ?>">
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
