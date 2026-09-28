<?php
/**
 * Maid4Condos — central configuration.
 *
 * Values can be overridden in three ways (later wins):
 *   1. Defaults below.
 *   2. config/config.local.php  (recommended for production; keep out of version control)
 *   3. Environment variables     (M4C_DB_HOST, M4C_MAIL_API_KEY, ...)
 *
 * Never commit real credentials — use config/config.local.php or the server environment.
 */

$defaults = [
    'app' => [
        'name'            => 'Maid4Condos',
        'tagline'         => 'Condo Cleaning Services in Toronto',
        'env'             => 'production',
        'debug'           => false,
        // Leave null to auto-detect. Set for sub-directory installs, e.g. '/maid4condos'.
        'base_url'        => null,
        'timezone'        => 'America/Toronto',
        'locale'          => 'en_CA',
        'currency'        => 'CAD',
    ],

    'db' => [
        'enabled'  => true,
        'driver'   => 'mysql',
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'maid4condos',
        'user'     => 'root',
        'pass'     => '',
        'charset'  => 'utf8mb4',
        'prefix'   => '',
    ],

    'mail' => [
        // 'mail' uses PHP mail(). 'elastic' posts to the Elastic Email HTTP API over cURL.
        'transport'     => 'mail',
        'api_key'       => '',
        'api_endpoint'  => 'https://api.elasticemail.com/v4/emails/transactional',
        'from_email'    => 'info@maid4condos.com',
        'from_name'     => 'Maid4Condos',
        'to_email'      => 'bookings@maid4condos.com',
        'reply_to'      => 'info@maid4condos.com',
        'log_inquiries' => true,
    ],

    'security' => [
        'session_name'      => 'm4c_session',
        'csrf_key'          => '_token',
        'login_max_attempts' => 5,
        'login_lockout'     => 900,   // seconds
        'form_min_seconds'  => 2,     // simple bot heuristic
        'upload_max_bytes'  => 3145728, // 3 MB
        'upload_mime'       => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'],
    ],

    'uploads' => [
        'dir'  => 'uploads',
        'url'  => 'uploads',
    ],

    'analytics' => [
        'ga4_id'         => '',
        'enabled'        => true,
        'anonymize_ip'   => true,
    ],

    'business' => [
        'phone'        => '647-822-0601',
        'phone_display' => '(647) 822-0601',
        'email'        => 'info@maid4condos.com',
        'booking_email' => 'bookings@maid4condos.com',
        'address_street' => '60 Atlantic Ave., Suite 200',
        'address_city'  => 'Toronto',
        'address_region' => 'ON',
        'address_postal' => 'M6K 1X9',
        'address_country' => 'CA',
        'legal_name'   => 'Maid4Condos',
        'founded'      => '2014',
        'reviews_site'  => ['count' => 250, 'label' => 'maid4condos.com'],
        'reviews_yelp'  => ['count' => 31,  'label' => 'Yelp'],
        'reviews_google' => ['count' => 77, 'label' => 'Google'],
    ],
];

$config = $defaults;

$localFile = __DIR__ . '/config.local.php';
if (is_file($localFile)) {
    $local = require $localFile;
    if (is_array($local)) {
        $config = m4c_config_merge($config, $local);
    }
}

/** Recursively merge overrides into the base configuration. */
function m4c_config_merge(array $base, array $override)
{
    foreach ($override as $key => $value) {
        if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
            $base[$key] = m4c_config_merge($base[$key], $value);
        } else {
            $base[$key] = $value;
        }
    }
    return $base;
}

/** Apply environment-variable overrides where a value is present. */
$envMap = [
    'M4C_ENV'              => ['app', 'env'],
    'M4C_DEBUG'            => ['app', 'debug'],
    'M4C_BASE_URL'         => ['app', 'base_url'],
    'M4C_DB_HOST'          => ['db', 'host'],
    'M4C_DB_PORT'          => ['db', 'port'],
    'M4C_DB_NAME'          => ['db', 'name'],
    'M4C_DB_USER'          => ['db', 'user'],
    'M4C_DB_PASS'          => ['db', 'pass'],
    'M4C_MAIL_TRANSPORT'   => ['mail', 'transport'],
    'M4C_MAIL_API_KEY'     => ['mail', 'api_key'],
    'M4C_MAIL_FROM'        => ['mail', 'from_email'],
    'M4C_MAIL_TO'          => ['mail', 'to_email'],
    'M4C_GA4_ID'           => ['analytics', 'ga4_id'],
];

foreach ($envMap as $envKey => $path) {
    $value = getenv($envKey);
    if ($value === false || $value === '') {
        continue;
    }
    if (in_array($envKey, ['M4C_DEBUG'], true)) {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    if ($envKey === 'M4C_DB_PORT') {
        $value = (int) $value;
    }
    $config[$path[0]][$path[1]] = $value;
}

if ((string) $config['app']['env'] === 'development') {
    $config['app']['debug'] = $config['app']['debug'] ?: true;
}

return $config;
