<?php
/**
 * Maid4Condos — application bootstrap.
 *
 * Every public entry point starts with:
 *     require __DIR__ . '/includes/bootstrap.php';
 */

if (defined('M4C_BOOTSTRAPPED')) {
    return;
}
define('M4C_BOOTSTRAPPED', true);

define('M4C_ROOT', dirname(__DIR__));
define('M4C_START', microtime(true));

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------
$GLOBALS['m4c_config'] = require M4C_ROOT . '/config/config.php';

/** Read a configuration value using dot notation, e.g. config('mail.from_email'). */
function config($key = null, $default = null)
{
    static $cache = [];

    if ($key === null) {
        return $GLOBALS['m4c_config'];
    }

    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $value = $GLOBALS['m4c_config'];
    foreach (explode('.', $key) as $segment) {
        if (is_array($value) && array_key_exists($segment, $value)) {
            $value = $value[$segment];
        } else {
            $cache[$key] = $default;
            return $default;
        }
    }

    $cache[$key] = $value;

    return $value;
}

date_default_timezone_set(config('app.timezone', 'America/Toronto'));
setlocale(LC_TIME, 'en_CA.UTF-8', 'en_CA', 'en_US.UTF-8', 'en_US');

// ---------------------------------------------------------------------------
// Error reporting
// ---------------------------------------------------------------------------
$debug = (bool) config('app.debug');
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

// ---------------------------------------------------------------------------
// Sessions (hardened)
// ---------------------------------------------------------------------------
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    session_name((string) config('security.session_name', 'm4c_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

// ---------------------------------------------------------------------------
// Core libraries (order matters: functions are available after each require)
// ---------------------------------------------------------------------------
require M4C_ROOT . '/includes/helpers.php';
require M4C_ROOT . '/includes/security.php';
require M4C_ROOT . '/config/database.php';
require M4C_ROOT . '/includes/auth.php';
require M4C_ROOT . '/includes/content.php';
require M4C_ROOT . '/includes/analytics.php';
require M4C_ROOT . '/includes/seo.php';
require M4C_ROOT . '/includes/mailer.php';
require M4C_ROOT . '/includes/forms.php';

m4c_send_security_headers();
