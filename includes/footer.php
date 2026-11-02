</main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <p class="footer-brand"><img src="<?= e(asset('assets/img/logo-xl-satu-putih.png')) ?>" alt="<?= e(config('site_name')) ?>" width="136" height="40" loading="lazy"></p>
      <p><?= e(config('tagline')) ?>. Layanan pemasangan untuk area <?= e(config('area_layanan')) ?>.</p>
    </div>
    <div>
      <p class="footer-title">Menu</p>
      <ul class="footer-links">
        <li><a href="<?= e(url('layanan')) ?>">Layanan</a></li>
        <li><a href="<?= e(url('paket')) ?>">Paket Internet</a></li>
        <li><a href="<?= e(url('promo')) ?>">Promo</a></li>
        <li><a href="<?= e(url('kontak')) ?>">Kontak &amp; Pemasangan</a></li>
        <li><a href="<?= e(url('kebijakan-privasi')) ?>">Kebijakan Privasi</a></li>
      </ul>
    </div>
    <div>
      <p class="footer-title">Hubungi Kami</p>
      <ul class="footer-links">
        <li>WhatsApp: <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" data-track="wa"><?= e(config('wa_display')) ?></a></li>
        <?php if (config('email')): ?><li>Email: <a href="mailto:<?= e(config('email')) ?>"><?= e(config('email')) ?></a></li><?php endif; ?>
        <li><?= e(config('jam_layanan')) ?></li>
      </ul>
    </div>
  </div>
  <div class="container footer-bottom">
    <p class="disclaimer"><?= e(config('disclaimer')) ?></p>
    <p>&copy; <?= date('Y') ?> <?= e(config('site_name')) ?></p>
  </div>
</footer>

<a class="wa-float" href="<?= e(wa_link('Halo, saya ingin pasang internet ' . config('site_name') . '. Boleh minta info paket dan promonya?')) ?>" target="_blank" rel="noopener" data-track="wa" aria-label="Chat WhatsApp">
  <svg viewBox="0 0 32 32" width="30" height="30" aria-hidden="true"><path fill="currentColor" d="M16 3C8.8 3 3 8.7 3 15.8c0 2.5.7 4.9 2 7L3 29l6.4-2c2 1.1 4.3 1.7 6.6 1.7 7.2 0 13-5.7 13-12.8S23.2 3 16 3Zm0 23.4c-2.1 0-4.1-.6-5.8-1.7l-.4-.3-3.8 1.2 1.2-3.7-.3-.4a10.4 10.4 0 0 1-1.6-5.6C5.3 10 10.1 5.3 16 5.3S26.7 10 26.7 15.8 21.9 26.4 16 26.4Zm5.9-7.8c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2l-1 1.2c-.2.2-.4.2-.7.1-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-1.9-1.8-2.2-.2-.3 0-.5.1-.7l.5-.6.3-.5c.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.7s1.2 3.1 1.4 3.4c.2.2 2.4 3.6 5.7 5 .8.3 1.4.5 1.9.7.8.2 1.5.2 2.1.1.6-.1 1.9-.8 2.2-1.5.3-.8.3-1.4.2-1.5-.1-.2-.3-.3-.6-.4Z"/></svg>
  <span class="wa-float-label">Chat Sales</span>
</a>

<div class="sticky-cta" aria-hidden="true">
  <span class="sticky-icon"><?= icon('hourglass', 26) ?></span>
  <strong>Sekarang Waktunya Dapat Lebih</strong>
  <a class="btn btn-wa" href="<?= e(wa_link('Halo, saya mau daftar internet ' . config('site_name') . '. Boleh dibantu?')) ?>" target="_blank" rel="noopener" data-track="wa" tabindex="-1">Daftar Sebelum Terlambat!</a>
</div>

<script src="<?= e(asset('assets/js/main.js')) ?>" defer></script>
</body>
</html>
