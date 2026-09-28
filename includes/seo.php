<?php
/**
 * Maid4Condos — on-page SEO: meta tags, Open Graph, Twitter cards and schema.org.
 */

/** Escape and emit a <meta> tag. */
function meta_tag($attribute, $name, $content)
{
    if ($content === null || $content === '') {
        return '';
    }

    return '<meta ' . $attribute . '="' . e($name) . '" content="' . e($content) . '">' . "\n";
}

/** Encode structured data for a JSON-LD script block. */
function json_ld($data)
{
    return '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>' . "\n";
}

/**
 * Render the full <head> block.
 *
 * @param array $page title, description, canonical (route), robots, og_type,
 *                    og_image, schema (array of JSON-LD arrays), preload_image
 */
function m4c_head(array $page = [])
{
    $biz         = business();
    $title       = $page['title'] ?? setting('default_meta_title', $biz['name']);
    $description = $page['description'] ?? setting('default_meta_description', $biz['description']);
    $route       = $page['canonical'] ?? current_path();
    $canonical   = absolute_url(ltrim($route, '/'));
    $robots      = $page['robots'] ?? setting('robots_policy', 'index,follow');
    $ogType      = $page['og_type'] ?? 'website';
    $ogImageSet  = $page['og_image'] ?? setting('og_image', '');
    $ogImage     = $ogImageSet ? (preg_match('#^https?://#i', $ogImageSet) ? $ogImageSet : absolute_url($ogImageSet)) : '';

    $schemas = isset($page['schema']) && is_array($page['schema']) ? $page['schema'] : [];
    array_unshift($schemas, schema_organization());

    echo '<meta charset="utf-8">' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">' . "\n";
    echo '<title>' . e($title) . '</title>' . "\n";
    echo meta_tag('name', 'description', $description);
    echo '<link rel="canonical" href="' . e($canonical) . '">' . "\n";
    echo meta_tag('name', 'robots', $robots);
    echo '<meta name="theme-color" content="#07242E">' . "\n";
    echo '<meta name="author" content="' . e($biz['legal_name']) . '">' . "\n";

    echo meta_tag('property', 'og:site_name', $biz['name']);
    echo meta_tag('property', 'og:locale', 'en_CA');
    echo meta_tag('property', 'og:type', $ogType);
    echo meta_tag('property', 'og:title', $title);
    echo meta_tag('property', 'og:description', $description);
    echo meta_tag('property', 'og:url', $canonical);
    echo meta_tag('property', 'og:image', $ogImage);
    echo meta_tag('property', 'og:image:alt', $biz['name'] . ' — ' . $biz['tagline']);

    echo meta_tag('name', 'twitter:card', 'summary_large_image');
    echo meta_tag('name', 'twitter:title', $title);
    echo meta_tag('name', 'twitter:description', $description);
    echo meta_tag('name', 'twitter:image', $ogImage);

    echo '<link rel="icon" href="' . e(asset('images/favicon.svg')) . '" type="image/svg+xml">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . e(asset('images/favicon.svg')) . '">' . "\n";

    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="stylesheet" media="print" onload="this.media=\'all\'" href="https://fonts.googleapis.com/css2'
        . '?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap">' . "\n";
    echo '<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2'
        . '?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap"></noscript>' . "\n";

    if (!empty($page['preload_image'])) {
        echo '<link rel="preload" as="image" href="' . e(url($page['preload_image'])) . '">' . "\n";
    }

    echo '<link rel="stylesheet" href="' . e(asset('css/main.css')) . '">' . "\n";
    m4c_analytics_head();

    foreach ($schemas as $schema) {
        if (!empty($schema)) {
            echo json_ld($schema);
        }
    }
}

// ---------------------------------------------------------------------------
// Structured data
// ---------------------------------------------------------------------------

/** Organisation / LocalBusiness node used on every page. */
function schema_organization()
{
    $biz = business();

    $node = [
        '@context' => 'https://schema.org',
        '@type'    => 'LocalBusiness',
        '@id'      => absolute_url('/') . '#business',
        'name'     => $biz['legal_name'],
        'alternateName' => $biz['name'],
        'url'      => absolute_url('/'),
        'description' => $biz['description'],
        'telephone' => '+' . preg_replace('/[^0-9]/', '', $biz['phone']),
        'email'    => $biz['email'],
        'foundingDate' => $biz['founded'],
        'address'  => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $biz['street'],
            'addressLocality' => $biz['city'],
            'addressRegion'   => $biz['region'],
            'postalCode'      => $biz['postal'],
            'addressCountry'  => 'CA',
        ],
        'areaServed' => array_map(function ($area) {
            return ['@type' => 'Place', 'name' => $area['name'] . ', Toronto'];
        }, array_slice(areas_all(), 0, 12)),
        'openingHoursSpecification' => schema_opening_hours(),
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name'  => 'Cleaning services',
            'itemListElement' => array_map(function ($service) {
                return [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name'  => $service['name'],
                        'url'   => absolute_url('services/' . $service['slug']),
                    ],
                ];
            }, services_all()),
        ],
    ];

    if (!empty($biz['social'])) {
        $node['sameAs'] = array_values($biz['social']);
    }
    $logo = setting('og_image', '');
    if ($logo) {
        $node['image'] = absolute_url($logo);
    }

    return $node;
}

/** Opening hours from the "Mo-Fr 08:00-18:00|Sa-Su 09:00-16:00" setting. */
function schema_opening_hours()
{
    $raw = setting('opening_hours_schema', 'Mo-Fr 08:00-18:00|Sa-Su 09:00-16:00');
    $out = [];
    foreach (explode('|', (string) $raw) as $spec) {
        $spec = trim($spec);
        if ($spec === '' || strpos($spec, ' ') === false) {
            continue;
        }
        list($days, $times) = explode(' ', $spec, 2);
        if (strpos($times, '-') === false) {
            continue;
        }
        list($open, $close) = explode('-', $times, 2);
        $out[] = [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => array_map(function ($day) {
                $map = [
                    'Mo' => 'Monday', 'Tu' => 'Tuesday', 'We' => 'Wednesday', 'Th' => 'Thursday',
                    'Fr' => 'Friday', 'Sa' => 'Saturday', 'Su' => 'Sunday',
                ];
                return isset($map[$day]) ? $map[$day] : $day;
            }, explode(',', $days)),
            'opens'  => trim($open),
            'closes' => trim($close),
        ];
    }

    return $out;
}

/** Service node for a single cleaning package. */
function schema_service(array $service)
{
    $node = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => $service['name'] . ' — Condo Cleaning',
        'serviceType' => $service['name'],
        'description' => $service['meta_description'] ?: $service['summary'],
        'url'         => absolute_url('services/' . $service['slug']),
        'provider'    => ['@id' => absolute_url('/') . '#business'],
        'areaServed'  => ['@type' => 'City', 'name' => business()['city']],
        'availableChannel' => [
            '@type'       => 'ServiceChannel',
            'serviceUrl'  => absolute_url('get-a-quote'),
            'servicePhone' => [
                '@type'         => 'ContactPoint',
                'telephone'     => '+' . preg_replace('/[^0-9]/', '', business()['phone']),
                'contactType'   => 'customer service',
            ],
        ],
    ];

    if (!empty($service['price_from'])) {
        $node['offers'] = [
            '@type'         => 'Offer',
            'priceCurrency' => config('app.currency', 'CAD'),
            'price'         => $service['price_from'],
            'priceSpecification' => [
                '@type'         => 'PriceSpecification',
                'priceCurrency' => config('app.currency', 'CAD'),
                'minPrice'      => $service['price_from'],
                'description'   => 'Starting from, based on property size and condition.',
            ],
            'url'           => absolute_url('get-a-quote'),
            'availability'  => 'https://schema.org/InStock',
        ];
    }

    if (!empty($service['image'])) {
        $node['image'] = absolute_url(m4c_image_path($service['image']));
    }

    return $node;
}

/** FAQPage node. */
function schema_faq_page(array $faqs)
{
    if (empty($faqs)) {
        return [];
    }

    return [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(function ($faq) {
            return [
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq['answer'],
                ],
            ];
        }, array_slice(faq_pairs($faqs), 0, 40)),
    ];
}

/** BreadcrumbList node. */
function schema_breadcrumbs(array $crumbs)
{
    $items = [];
    $position = 1;
    foreach ($crumbs as $label => $route) {
        $entry = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => $label,
        ];
        if ($route) {
            $entry['item'] = absolute_url($route);
        }
        $items[] = $entry;
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}
