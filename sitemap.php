<?php
/**
 * /sitemap.xml — XML sitemap generated from the live content.
 *
 * .htaccess maps /sitemap.xml to this file, so newly published services and
 * pages appear in the sitemap without any manual editing.
 */
require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$origin = site_origin();
$today  = date('Y-m-d');

$pages = [
    ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/get-a-quote', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/faq', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '/testimonials', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ['loc' => '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => '/privacy-policy', 'priority' => '0.2', 'changefreq' => 'yearly'],
    ['loc' => '/terms', 'priority' => '0.2', 'changefreq' => 'yearly'],
];

foreach (services_all() as $service) {
    $pages[] = [
        'loc'        => '/services/' . $service['slug'],
        'priority'   => '0.8',
        'changefreq' => 'monthly',
        'image'      => '/assets/images/' . $service['hero_image'] . '.jpg',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($pages as $page) : ?>
    <url>
        <loc><?= e($origin . url($page['loc'])) ?></loc>
        <lastmod><?= e($today) ?></lastmod>
        <changefreq><?= e($page['changefreq']) ?></changefreq>
        <priority><?= e($page['priority']) ?></priority>
<?php if (!empty($page['image'])) : ?>
        <image:image>
            <image:loc><?= e($origin . url($page['image'])) ?></image:loc>
        </image:image>
<?php endif; ?>
    </url>
<?php endforeach; ?>
</urlset>
