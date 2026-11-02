<?php
require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');

$pages = ['' => '1.0', 'paket' => '0.9', 'promo' => '0.9', 'layanan' => '0.8', 'kontak' => '0.8', 'kebijakan-privasi' => '0.3'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as $path => $priority) {
    echo '  <url><loc>' . e(absolute_url($path)) . '</loc><changefreq>weekly</changefreq><priority>' . $priority . "</priority></url>\n";
}
echo '</urlset>';
