<?php
require __DIR__ . '/includes/bootstrap.php';

$lead = $_SESSION['lead_done'] ?? null;
if (!$lead) {
    redirect('kontak');
}
// Tampil sekali saja, supaya konversi iklan tidak terhitung dua kali saat halaman dimuat ulang.
unset($_SESSION['lead_done']);

$message = wa_message_lead($lead);

$page = [
    'title'   => 'Terima Kasih',
    'path'    => 'terima-kasih',
    'noindex' => true,
];
require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container text-center" style="max-width:640px">
    <div class="feature-icon" style="margin:0 auto 16px;width:72px;height:72px;border-radius:50%;background:#e8f6ee;color:#198754">
      <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
    </div>
    <h1 style="font-size:2rem">Terima kasih, <?= e($lead['nama']) ?>!</h1>
    <p>Data pendaftaran kamu sudah kami terima. Sales kami akan segera menghubungi lewat WhatsApp untuk cek area dan jadwal pemasangan.</p>
    <div class="wa-auto" data-wa-auto="<?= e(wa_link($message)) ?>">
      <p><strong>Membuka WhatsApp…</strong> Tinggal tekan <b>Kirim</b> supaya data kamu langsung sampai ke sales.</p>
      <div class="wa-auto-bar"><span></span></div>
    </div>
    <p class="note">Kalau WhatsApp tidak terbuka otomatis, tekan tombol ini:</p>
    <a class="btn btn-wa" href="<?= e(wa_link($message)) ?>" data-track="wa" data-no-form>Kirim ke WhatsApp Sales</a>
    <p class="mt-2"><a href="<?= e(url()) ?>">Kembali ke beranda</a></p>
  </div>
</section>

<script>
(function () {
  if (typeof window.gtag === 'function') {
    window.gtag('event', 'generate_lead');
    if (window.SITE && window.SITE.adsConversion) {
      window.gtag('event', 'conversion', { send_to: window.SITE.adsConversion });
    }
  }
  if (typeof window.fbq === 'function') { window.fbq('track', 'Lead'); }
  // Buka WhatsApp otomatis setelah sebentar (memberi waktu pelacakan iklan terkirim)
  var box = document.querySelector('[data-wa-auto]');
  if (box) { setTimeout(function () { window.location.href = box.getAttribute('data-wa-auto'); }, 1800); }
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
