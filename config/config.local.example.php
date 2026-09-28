<?php
/**
 * Copy this file to config/config.local.php and fill in your own values.
 *
 * config/config.local.php is ignored by git and must never be committed.
 * Anything set here overrides config/config.php. Environment variables
 * (M4C_DB_HOST, M4C_MAIL_API_KEY, …) override both.
 */
return [
    'app' => [
        'env'      => 'production',
        'debug'    => false,
        // Full URL of the site. Leave null to auto-detect the host.
        'base_url' => 'https://www.maid4condos.com',
    ],

    'db' => [
        'enabled' => true,
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'maid4condos',
        'user'    => 'your_database_user',
        'pass'    => 'your_database_password',
    ],

    'mail' => [
        // 'mail' (PHP mail(), works everywhere) or 'elastic' (Elastic Email API).
        'transport'  => 'mail',
        'api_key'    => '',
        'from_email' => 'info@maid4condos.com',
        'from_name'  => 'Maid4Condos',
        'to_email'   => 'bookings@maid4condos.com',
    ],
];
