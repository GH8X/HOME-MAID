<?php
/**
 * Maid4Condos — content repository.
 *
 * Every reader goes through this layer:
 *   • If MySQL is configured and the table contains rows, the database wins.
 *   • Otherwise the seeded content in includes/seed.php is returned.
 *
 * That means the public site is always complete (no empty sections, no
 * placeholders) and the admin panel can seed editable rows on first run.
 */

/** Raw seed data, loaded once. */
function seed_data($section = null)
{
    static $seed = null;
    if ($seed === null) {
        $seed = require M4C_ROOT . '/includes/seed.php';
    }
    if ($section === null) {
        return $seed;
    }

    return isset($seed[$section]) ? $seed[$section] : [];
}

/** Safe query wrapper — never lets a broken schema take the public site down. */
function content_query($sql, array $params = [])
{
    if (!db_available()) {
        return [];
    }
    try {
        return db_all($sql, $params);
    } catch (Throwable $e) {
        if (config('app.debug')) {
            error_log('[Maid4Condos] content query failed: ' . $e->getMessage());
        }

        return [];
    }
}

/** Decode a JSON column that may already be an array. */
function json_field($value, $fallback = [])
{
    if (is_array($value)) {
        return $value;
    }
    if (!is_string($value) || trim($value) === '') {
        return $fallback;
    }
    $decoded = json_decode($value, true);

    return is_array($decoded) ? $decoded : $fallback;
}

/** Split a textarea into a trimmed, non-empty list. */
function lines_to_list($text)
{
    if (is_array($text)) {
        return array_values(array_filter(array_map('trim', $text), 'strlen'));
    }
    $lines  = preg_split('/\r\n|\r|\n/', (string) $text);
    $result = [];
    foreach ($lines as $line) {
        $line = trim(preg_replace('/^\s*(?:[-*•]|\d+[\.\)])\s*/', '', (string) $line));
        if ($line !== '') {
            $result[] = $line;
        }
    }

    return $result;
}

/** Turn "Key: a | b" textareas into an associative list for checklists. */
function lines_to_grouped($text)
{
    if (is_array($text)) {
        return $text;
    }
    $groups = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) $text) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = explode('|', $line);
        $key   = slugify(array_shift($parts));
        if ($key === '') {
            continue;
        }
        $groups[$key] = array_values(array_filter(array_map('trim', $parts), 'strlen'));
    }

    return $groups;
}

// ---------------------------------------------------------------------------
// Settings
// ---------------------------------------------------------------------------

/** All settings as key => string value (database overrides seed defaults). */
function settings_all()
{
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $settings = [];
    foreach (seed_data('settings') as $key => $meta) {
        $settings[$key] = $meta[2];
    }

    $rows = content_query(
        'SELECT setting_key, setting_value FROM ' . table('settings') . ' WHERE 1=1'
    );
    foreach ($rows as $row) {
        if ($row['setting_value'] !== null) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $settings;
}

/** Single setting value with a fallback chain. */
function setting($key, $default = '')
{
    $all = settings_all();
    if (isset($all[$key]) && $all[$key] !== '') {
        return $all[$key];
    }

    $configMap = [
        'phone' => 'business.phone',
        'email' => 'business.email',
        'booking_email' => 'business.booking_email',
        'ga4_measurement_id' => 'analytics.ga4_id',
    ];
    if (isset($configMap[$key])) {
        $value = config($configMap[$key]);
        if ($value) {
            return $value;
        }
    }

    return $default;
}

/** Setting row metadata for the admin editor. */
function setting_definitions()
{
    $definitions = seed_data('settings');

    $rows = content_query(
        'SELECT setting_key, setting_value, setting_group, setting_type, label FROM ' . table('settings') . ' ORDER BY setting_group, setting_key'
    );
    foreach ($rows as $row) {
        $definitions[$row['setting_key']] = [
            $row['setting_group'] ?: 'general',
            $row['setting_type'] ?: 'text',
            $row['setting_value'],
            $row['label'] ?: ucwords(str_replace('_', ' ', $row['setting_key'])),
        ];
    }

    return $definitions;
}

// ---------------------------------------------------------------------------
// Services
// ---------------------------------------------------------------------------

/**
 * Normalise a question/answer pair into ['question' => …, 'answer' => …].
 *
 * Content is authored both as [question, answer] tuples (service checklists in
 * includes/seed.php and the admin service editor) and as question/answer maps
 * (the main FAQ list), so everything is funnelled through here.
 */
function faq_pairs($faqs)
{
    if (!is_array($faqs)) {
        return [];
    }

    $out = [];
    foreach ($faqs as $faq) {
        if (!is_array($faq)) {
            continue;
        }
        $question = $faq['question'] ?? ($faq[0] ?? '');
        $answer   = $faq['answer'] ?? ($faq[1] ?? '');
        if (!is_scalar($question) || !is_scalar($answer)) {
            continue;
        }
        if (trim((string) $question) === '' || trim((string) $answer) === '') {
            continue;
        }
        $out[] = ['question' => (string) $question, 'answer' => (string) $answer];
    }

    return $out;
}

/** Map a database service row to the canonical array shape. */
function map_service_row(array $row)
{
    return [
        'id'               => isset($row['id']) ? (int) $row['id'] : null,
        'slug'             => $row['slug'],
        'name'             => $row['name'],
        'eyebrow'          => $row['eyebrow'] ?? '',
        'tagline'          => $row['tagline'] ?? '',
        'summary'          => $row['summary'] ?? '',
        'intro'            => json_field($row['intro_json'] ?? null, lines_to_list($row['intro'] ?? '')),
        'price_from'       => $row['price_from'] ?? null,
        'image'            => $row['image'] ?? '',
        'hero_image'       => $row['hero_image'] ?? ($row['image'] ?? ''),
        'duration_note'    => $row['duration_note'] ?? '',
        'best_paired'      => $row['best_paired'] ?? '',
        'meta_title'       => $row['meta_title'] ?? ($row['name'] . ' | Maid4Condos'),
        'meta_description' => $row['meta_description'] ?? '',
        'who_for'          => json_field($row['who_for_json'] ?? null, lines_to_list($row['who_for'] ?? '')),
        'checklist'        => json_field($row['checklist_json'] ?? null, []),
        'benefits'         => json_field($row['benefits_json'] ?? null, lines_to_list($row['benefits'] ?? '')),
        'faqs'             => faq_pairs(json_field($row['faqs_json'] ?? null, [])),
        'sort_order'       => isset($row['sort_order']) ? (int) $row['sort_order'] : 0,
        'is_published'     => isset($row['is_published']) ? (int) $row['is_published'] : 1,
    ];
}

/** Normalise a seed service into the same shape. */
function map_service_seed(array $service, $index = 0)
{
    $service['id']           = null;
    $service['sort_order']   = $index + 1;
    $service['is_published'] = 1;
    $service['intro']        = isset($service['intro']) ? $service['intro'] : [];
    $service['who_for']      = isset($service['who_for']) ? $service['who_for'] : [];
    $service['checklist']    = isset($service['checklist']) ? $service['checklist'] : [];
    $service['benefits']     = isset($service['benefits']) ? $service['benefits'] : [];
    $service['faqs']         = faq_pairs(isset($service['faqs']) ? $service['faqs'] : []);

    return $service;
}

/** Every service, published only by default. */
function services_all($includeUnpublished = false)
{
    static $cache = null;
    if ($cache === null) {
        $rows = content_query(
            'SELECT * FROM ' . table('services') . ' ORDER BY sort_order ASC, id ASC'
        );
        if ($rows) {
            $cache = array_map('map_service_row', $rows);
        } else {
            $cache = [];
            foreach (seed_data('services') as $index => $service) {
                $cache[] = map_service_seed($service, $index);
            }
        }
    }

    if ($includeUnpublished) {
        return $cache;
    }

    return array_values(array_filter($cache, function ($service) {
        return (int) $service['is_published'] === 1;
    }));
}

/** A single service by slug (or null). */
function service($slug)
{
    foreach (services_all(true) as $item) {
        if ($item['slug'] === $slug) {
            return $item;
        }
    }

    return null;
}

/** Slugs of every published service, used for routing and sitemaps. */
function service_slugs()
{
    return array_map(function ($service) {
        return $service['slug'];
    }, services_all());
}

// ---------------------------------------------------------------------------
// Extras, frequencies, areas
// ---------------------------------------------------------------------------

function extras_all()
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $rows = content_query('SELECT * FROM ' . table('extras') . ' ORDER BY sort_order ASC, id ASC');
    if ($rows) {
        $cache = array_map(function ($row) {
            $row['id']           = (int) $row['id'];
            $row['is_published'] = (int) $row['is_published'];

            return $row;
        }, $rows);
    } else {
        $cache = [];
        foreach (seed_data('extras') as $index => $extra) {
            $extra['id']           = null;
            $extra['sort_order']   = $index + 1;
            $extra['is_published'] = 1;
            $cache[]               = $extra;
        }
    }

    return array_values(array_filter($cache, function ($row) {
        return (int) $row['is_published'] === 1;
    }));
}

function frequencies_all()
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $rows = content_query('SELECT * FROM ' . table('frequencies') . ' ORDER BY sort_order ASC, id ASC');
    if ($rows) {
        $cache = array_map(function ($row) {
            return [
                'slug'        => $row['slug'],
                'label'       => $row['label'],
                'discount'    => $row['discount'],
                'note'        => $row['note'],
                'recommended' => (int) $row['recommended'] === 1,
            ];
        }, $rows);
    } else {
        $cache = seed_data('frequencies');
    }

    return array_values(array_filter($cache, function ($row) {
        return true;
    }));
}

function areas_all()
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $rows = content_query(
        'SELECT * FROM ' . table('service_areas') . ' WHERE is_published = 1 ORDER BY sort_order ASC, id ASC'
    );
    if ($rows) {
        $cache = array_map(function ($row) {
            return ['name' => $row['name'], 'note' => $row['note']];
        }, $rows);
    } else {
        $cache = seed_data('areas');
    }

    return $cache;
}

// ---------------------------------------------------------------------------
// FAQs
// ---------------------------------------------------------------------------

function faqs_all($category = null)
{
    static $cache = null;
    if ($cache === null) {
        $rows = content_query(
            'SELECT * FROM ' . table('faqs') . ' WHERE is_published = 1 ORDER BY sort_order ASC, id ASC'
        );
        if ($rows) {
            $cache = array_map(function ($row) {
                return [
                    'id'       => (int) $row['id'],
                    'category' => $row['category'],
                    'question' => $row['question'],
                    'answer'   => $row['answer'],
                ];
            }, $rows);
        } else {
            $cache = array_map(function ($faq, $index) {
                $faq['id'] = $index + 1;

                return $faq;
            }, seed_data('faqs'), array_keys(seed_data('faqs')));
        }
    }

    if ($category === null) {
        return $cache;
    }

    return array_values(array_filter($cache, function ($faq) use ($category) {
        return $faq['category'] === $category;
    }));
}

/** FAQ categories in the order they first appear. */
function faq_categories()
{
    $categories = [];
    foreach (faqs_all() as $faq) {
        if (!in_array($faq['category'], $categories, true)) {
            $categories[] = $faq['category'];
        }
    }

    return $categories;
}

// ---------------------------------------------------------------------------
// Testimonials
// ---------------------------------------------------------------------------

function testimonials_all($limit = null)
{
    static $cache = null;
    if ($cache === null) {
        $rows = content_query(
            'SELECT * FROM ' . table('testimonials') . ' WHERE is_published = 1 ORDER BY sort_order ASC, id ASC'
        );
        if ($rows) {
            $cache = array_map(function ($row) {
                return [
                    'id'       => (int) $row['id'],
                    'name'     => $row['name'],
                    'location' => $row['location'],
                    'service'  => $row['service'],
                    'quote'    => $row['quote'],
                    'rating'   => $row['rating'] !== null ? (int) $row['rating'] : null,
                    'source'   => $row['source'] ?? null,
                ];
            }, $rows);
        } else {
            $cache = seed_data('testimonials');
        }
    }

    return $limit ? array_slice($cache, 0, (int) $limit) : $cache;
}

// ---------------------------------------------------------------------------
// Editable content blocks (homepage sections, about page)
// ---------------------------------------------------------------------------

function content_block($key)
{
    static $cache = [];

    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $parts = explode('.', $key);
    $seed  = seed_data($parts[0]);
    foreach (array_slice($parts, 1) as $part) {
        $seed = (is_array($seed) && array_key_exists($part, $seed)) ? $seed[$part] : null;
    }

    $row = content_query(
        'SELECT block_json, is_published FROM ' . table('content_blocks') . ' WHERE block_key = ? LIMIT 1',
        [$key]
    );
    if ($row && (int) $row[0]['is_published'] === 1) {
        $decoded = json_decode((string) $row[0]['block_json'], true);
        if ($decoded === null || $decoded === '' || $decoded === []) {
            $decoded = null;
        }
        if ($decoded !== null) {
            // Structured sections merge over the defaults; scalar sections replace.
            $seed = (is_array($seed) && is_array($decoded))
                ? array_replace_recursive($seed, $decoded)
                : $decoded;
        }
    }

    if (is_array($seed)) {
        $cache[$key] = $seed;
    } elseif (is_scalar($seed)) {
        $cache[$key] = (string) $seed;
    } else {
        $cache[$key] = [];
    }

    return $cache[$key];
}

/** Primary navigation items. */
function navigation_items()
{
    $rows = content_query(
        'SELECT label, route FROM ' . table('navigation') . ' WHERE is_published = 1 ORDER BY sort_order ASC, id ASC'
    );
    if ($rows) {
        return $rows;
    }

    return seed_data('navigation');
}

// ---------------------------------------------------------------------------
// Convenience accessors used across templates
// ---------------------------------------------------------------------------

function business()
{
    return [
        'name'         => setting('site_name', config('app.name')),
        'legal_name'   => setting('site_legal_name', config('business.legal_name')),
        'tagline'      => setting('site_tagline', config('app.tagline')),
        'description'  => setting('site_description', ''),
        'phone'        => setting('phone', config('business.phone')),
        'phone_display' => setting('phone_display', config('business.phone_display')),
        'email'        => setting('email', config('business.email')),
        'booking_email' => setting('booking_email', config('business.booking_email')),
        'street'       => setting('address_street', config('business.address_street')),
        'city'         => setting('address_city', config('business.address_city')),
        'region'       => setting('address_region', config('business.address_region')),
        'postal'       => setting('address_postal', config('business.address_postal')),
        'country'      => setting('address_country', 'Canada'),
        'office_hours' => setting('office_hours', ''),
        'service_hours' => setting('service_hours', ''),
        'map_query'    => setting('map_query', ''),
        'founded'      => config('business.founded', '2014'),
        'liability'    => setting('liability_coverage', '5,000,000'),
        'guarantee'    => setting('guarantee_text', ''),
        'social'       => array_filter([
            'facebook'  => setting('social_facebook', ''),
            'instagram' => setting('social_instagram', ''),
            'twitter'   => setting('social_twitter', ''),
        ]),
        'reviews'      => array_filter([
            'site'   => setting('reviews_site_count', ''),
            'yelp'   => setting('reviews_yelp_count', ''),
            'google' => setting('reviews_google_count', ''),
        ]),
    ];
}

/** Google Maps embed URL built from settings. */
function map_embed_url()
{
    $query = business()['map_query'];
    if (!$query) {
        return '';
    }

    return 'https://www.google.com/maps?q=' . rawurlencode($query) . '&output=embed';
}

/** Total published content counts (used by the admin dashboard). */
function content_counts()
{
    return [
        'services'     => count(services_all()),
        'faqs'         => count(faqs_all()),
        'testimonials' => count(testimonials_all()),
        'areas'        => count(areas_all()),
        'extras'       => count(extras_all()),
    ];
}
