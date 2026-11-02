<?php
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
echo "Disallow: /admin/\n";
echo "Disallow: /terima-kasih\n\n";
echo 'Sitemap: ' . absolute_url('sitemap.xml') . "\n";
