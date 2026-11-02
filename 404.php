<?php
require __DIR__ . '/includes/bootstrap.php';
http_response_code(404);

$page = ['title' => 'Halaman Tidak Ditemukan', 'path' => '', 'noindex' => true];
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container text-center" style="max-width:600px">
    <h1>Halaman tidak ditemukan</h1>
    <p>Maaf, halaman yang kamu cari tidak ada atau sudah dipindahkan.</p>
    <a class="btn btn-primary" href="<?= e(url()) ?>">Kembali ke Beranda</a>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
