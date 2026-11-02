<?php
/**
 * Variabel yang bisa diisi sebelum include:
 * $page = ['title' => '', 'description' => '', 'path' => '', 'active' => '', 'noindex' => false]
 */
$page = $page ?? [];
$siteName = config('site_name');
$title = !empty($page['title']) ? $page['title'] . ' | ' . $siteName : $siteName . ' – ' . config('tagline');
$description = $page['description'] ?? 'Pasang internet rumah ' . $siteName . ' cepat dan stabil. Lihat paket, promo terbaru, dan daftar pemasangan langsung via WhatsApp.';
$canonical = absolute_url($page['path'] ?? '');
$active = $page['active'] ?? '';
$ogImage = absolute_url('assets/img/og-image.png');

$nav = [
    ''        => 'Beranda',
    'layanan' => 'Layanan',
    'paket'   => 'Paket Internet',
    'promo'   => 'Promo',
    'kontak'  => 'Kontak & Pemasangan',
];

$gtag = (string) config('google_tag_id');
$pixel = (string) config('meta_pixel_id');
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php if (!empty($page['noindex'])): ?><meta name="robots" content="noindex"><?php endif; ?>
<meta name="theme-color" content="#0b3d91">

<meta property="og:type" content="website">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" href="<?= e(url('assets/img/favicon.png')) ?>" type="image/png">
<link rel="apple-touch-icon" href="<?= e(url('assets/img/apple-touch-icon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
<script>document.documentElement.classList.add('js');</script>

<script type="application/ld+json"><?= json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'LocalBusiness',
    'name'       => $siteName,
    'url'        => absolute_url(),
    'telephone'  => '+' . config('wa_number'),
    'areaServed' => config('area_layanan'),
    'image'      => $ogImage,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

<script>
window.SITE = <?= json_encode(['adsConversion' => (string) config('google_ads_conversion')], JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php if ($gtag !== ''): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($gtag) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', <?= json_encode($gtag) ?>);
</script>
<?php endif; ?>
<?php if ($pixel !== ''): ?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', <?= json_encode($pixel) ?>);
fbq('track', 'PageView');
</script>
<?php endif; ?>
</head>
<body>
<a class="skip-link" href="#konten">Lewati ke konten</a>

<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e($siteName) ?> – Beranda">
      <img class="brand-logo" src="<?= e(asset('assets/img/logo-xl-satu.png')) ?>" alt="<?= e($siteName) ?>" width="123" height="36">
    </a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu-utama">
      <span class="sr-only">Buka menu</span>
      <span class="bar"></span><span class="bar"></span><span class="bar"></span>
    </button>

    <nav id="menu-utama" class="main-nav" aria-label="Menu utama">
      <ul>
        <?php foreach ($nav as $slug => $label): ?>
          <li><a href="<?= e(url($slug)) ?>"<?= $active === $slug ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</header>

<main id="konten">
