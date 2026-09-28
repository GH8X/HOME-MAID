<?php
/**
 * Maid4Condos — shared helper functions.
 */

/** HTML-escape any value for safe output. */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Absolute path inside the project. */
function path($relative = '')
{
    return M4C_ROOT . '/' . ltrim($relative, '/');
}

/** Detect the public base URL (supports sub-directory installs). */
function base_url()
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $configured = config('app.base_url');
    if (is_string($configured) && $configured !== '') {
        $base = rtrim($configured, '/');

        return $base;
    }

    // When only the document root is exposed, the script sits at the root;
    // when the project folder itself is the document root, dirname() is empty.
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $dir    = str_replace('\\', '/', dirname($script));
    $dir    = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');

    // Strip known sub-directories so links always resolve from the project root.
    foreach (['/admin', '/services', '/api'] as $sub) {
        if (substr($dir, -strlen($sub)) === $sub) {
            $dir = substr($dir, 0, -strlen($sub));
        }
    }
    // Nested service pages (services/<slug>/index.php) add two more levels.
    if (preg_match('#/services/[a-z0-9\-]+$#', $dir)) {
        $dir = preg_replace('#/services/[a-z0-9\-]+$#', '', $dir);
    }

    $base = $dir;

    return $base;
}

/** Build an absolute path URL for a route such as 'services/basic-cleaning'. */
function url($route = '')
{
    $route = ltrim((string) $route, '/');
    if ($route === '') {
        return base_url() . '/';
    }

    // Preserve query strings and anchors.
    return base_url() . '/' . $route;
}

/** Absolute URL (with scheme + host) — used for canonical tags, schema and emails. */
function absolute_url($route = '')
{
    $path = url($route);
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return site_origin() . $path;
}

/** Scheme + host for the current installation. */
function site_origin()
{
    static $origin = null;
    if ($origin !== null) {
        return $origin;
    }

    $configured = config('app.base_url');
    if (is_string($configured) && preg_match('#^https?://#i', $configured)) {
        $parts  = parse_url($configured);
        $origin = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $origin .= ':' . $parts['port'];
        }

        return $origin;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $scheme = 'https';
    }
    $host = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^A-Za-z0-9\.\-:]/', '', $_SERVER['HTTP_HOST']) : 'localhost';
    $origin = $scheme . '://' . $host;

    return $origin;
}

/** Public URL for an asset with a cache-busting version based on file mtime. */
function asset($relative)
{
    $relative = ltrim($relative, '/');
    $file     = path('assets/' . $relative);
    $version  = is_file($file) ? filemtime($file) : null;

    return url('assets/' . $relative) . ($version ? '?v=' . $version : '');
}

/** URL for a file in /uploads. */
function upload_url($relative)
{
    if (!$relative) {
        return '';
    }
    if (preg_match('#^https?://#i', $relative)) {
        return $relative;
    }

    return url('uploads/' . ltrim($relative, '/'));
}

/**
 * Render a responsive <img>/<picture> element.
 *
 * Automatically upgrades to AVIF/WebP when a sibling file exists
 * (e.g. cleaning-windows.avif next to cleaning-windows.jpg) and builds a
 * srcset from the optional `-900` derivative.
 */
function m4c_image($name, $alt, array $options = [])
{
    $name = ltrim((string) $name, '/');
    $dir  = config('app.image_dir', 'assets/images');
    $fsBase = path($dir . '/' . $name);

    $ext       = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $stem      = $ext ? substr($name, 0, -(strlen($ext) + 1)) : $name;
    $variants  = [];
    foreach (['avif', 'webp', $ext ?: 'jpg'] as $candidate) {
        $candidateFile = path($dir . '/' . $stem . '.' . $candidate);
        if (is_file($candidateFile)) {
            $variants[$candidate] = url($dir . '/' . $stem . '.' . $candidate);
        }
    }
    if (empty($variants)) {
        return '';
    }

    $classes = isset($options['class']) ? ' class="' . e($options['class']) . '"' : '';
    $width   = isset($options['width']) ? (int) $options['width'] : 0;
    $height  = isset($options['height']) ? (int) $options['height'] : 0;
    $sizes   = isset($options['sizes']) ? $options['sizes'] : '';
    $loading = isset($options['loading']) ? $options['loading'] : 'lazy';
    $fetch   = isset($options['fetchpriority']) ? ' fetchpriority="' . e($options['fetchpriority']) . '"' : '';
    $extra   = isset($options['attrs']) ? ' ' . $options['attrs'] : '';

    // The <img> element is the fallback: always use the original format so it
    // stays valid even when AVIF/WebP sources are listed above it.
    $src    = $variants[$ext] ?? reset($variants);
    $srcset = [];
    $deriv  = $stem . '-900.' . $ext;
    if ($ext && is_file(path($dir . '/' . $deriv))) {
        $srcset[] = url($dir . '/' . $deriv) . ' 900w';
        $srcset[] = $src . ' 1600w';
    }

    $img = '<img src="' . e($src) . '"'
        . ($srcset ? ' srcset="' . e(implode(', ', $srcset)) . '"' : '')
        . ($sizes ? ' sizes="' . e($sizes) . '"' : '')
        . ' alt="' . e($alt) . '"'
        . ($width ? ' width="' . $width . '"' : '')
        . ($height ? ' height="' . $height . '"' : '')
        . ' loading="' . e($loading) . '" decoding="async"' . $fetch . $classes . $extra . '>';

    if (count($variants) > 1 && isset($variants['avif'])) {
        $sources = '<source type="image/avif" srcset="' . e($variants['avif']) . '"'
            . ($sizes ? ' sizes="' . e($sizes) . '"' : '') . '>';
        if (isset($variants['webp'])) {
            $sources .= '<source type="image/webp" srcset="' . e($variants['webp']) . '"'
                . ($sizes ? ' sizes="' . e($sizes) . '"' : '') . '>';
        }

        return '<picture>' . $sources . $img . '</picture>';
    }

    return $img;
}

/**
 * Render an image whether it lives in /assets/images (shipped photography) or
 * in /uploads (uploaded through the admin panel).
 */
function m4c_media_image($value, $alt, array $options = [])
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $name = preg_match('/\.(jpe?g|png|webp|avif|svg)$/i', $value) ? $value : $value . '.jpg';

    if (preg_match('#^https?://#i', $name)) {
        return '<img src="' . e($name) . '" alt="' . e($alt) . '" loading="lazy" decoding="async">';
    }

    if (strpos($name, 'uploads/') === 0) {
        $classes = isset($options['class']) ? ' class="' . e($options['class']) . '"' : '';
        $width   = isset($options['width']) ? ' width="' . (int) $options['width'] . '"' : '';
        $height  = isset($options['height']) ? ' height="' . (int) $options['height'] . '"' : '';
        $loading = isset($options['loading']) ? $options['loading'] : 'lazy';
        $fetch   = !empty($options['fetchpriority']) ? ' fetchpriority="' . e($options['fetchpriority']) . '"' : '';

        return '<img src="' . e(upload_url(substr($name, strlen('uploads/')))) . '" alt="' . e($alt) . '"'
            . $width . $height . ' loading="' . e($loading) . '" decoding="async"' . $fetch . $classes . '>';
    }

    return m4c_image($name, $alt, $options);
}

/**
 * Resolve a stored image value to a web-relative path (for Open Graph tags and
 * structured data, which need a concrete URL rather than an <img> tag).
 */
function m4c_image_path($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (preg_match('#^(https?://|uploads/)#i', $value)) {
        return $value;
    }

    return 'assets/images/' . (preg_match('/\.(jpe?g|png|webp|avif|svg)$/i', $value) ? $value : $value . '.jpg');
}

/**
 * Photography available in assets/images, excluding derivatives and brand marks.
 * Used by the admin image picker.
 */
function m4c_available_images()
{
    $dir  = path('assets/images');
    $list = [];
    foreach ((array) glob($dir . '/*.jpg') as $file) {
        $base = basename($file);
        if (preg_match('/-\d+\.jpg$/', $base)) {
            continue;
        }
        $list[] = $base;
    }
    sort($list);

    return $list;
}

/** Human readable phone link. */
function tel_href()
{
    return 'tel:+1' . preg_replace('/[^0-9]/', '', (string) config('business.phone'));
}

/** Build a smart-looking excerpt. */
function excerpt($text, $length = 150)
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    if (function_exists('mb_strlen')) {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $length), " \t\n\r\0\x0B.,") . '…';
    }
    if (strlen($text) <= $length) {
        return $text;
    }

    return rtrim(substr($text, 0, $length), " \t\n\r\0\x0B.,") . '…';
}

/**
 * Multibyte-safe string length.
 *
 * mbstring is present on essentially every host, but the rest of the codebase
 * degrades gracefully without it, so validation does too.
 */
function str_length($value)
{
    $value = (string) $value;

    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

/** URL-safe slug, transliteration-safe for common Latin characters. */
function slugify($value)
{
    $value = strtolower(trim((string) $value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);

    return trim((string) $value, '-');
}

/** Formatted price from a numeric string, using the configured currency. */
function money($amount)
{
    if ($amount === null || $amount === '') {
        return '';
    }
    if (!is_numeric($amount)) {
        return (string) $amount;
    }

    $symbol = config('app.currency', 'CAD') === 'USD' ? '$' : '$';

    return $symbol . number_format((float) $amount, 2);
}

/** Current request path relative to the installation (no query string). */
function current_path()
{
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $uri = explode('?', $uri)[0];
    $base = base_url();
    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }

    return '/' . trim($uri, '/');
}

/** True when the given route (or its branch) is the active page. */
function is_current($route, $strict = false)
{
    $route = '/' . trim((string) $route, '/');
    $here  = current_path();
    if ($strict) {
        return $here === $route;
    }

    return $here === $route || strpos($here . '/', $route . '/') === 0;
}

/** Redirect and stop. */
function redirect($route, $status = 302)
{
    $target = preg_match('#^https?://#i', $route) ? $route : url($route);
    header('Location: ' . $target, true, $status);
    exit;
}

/** Read a trimmed value from $_GET / $_POST. */
function input($key, $default = '', $source = null)
{
    $source = $source ?: $_POST;
    if (!is_array($source)) {
        return $default;
    }
    if (!array_key_exists($key, $source)) {
        return $default;
    }
    $value = $source[$key];
    if (is_array($value)) {
        return $value;
    }

    return trim((string) $value);
}

/** Integer input with an upper/lower bound. */
function int_input($key, $default = 0, $source = null)
{
    $value = input($key, $default, $source);

    return is_numeric($value) ? (int) $value : $default;
}

/** Set a one-time flash message for the next request. */
function flash($type, $message = null)
{
    if ($message === null) {
        $message = $type;
        $type    = 'success';
    }
    if (empty($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Pull and clear flash messages. */
function take_flashes()
{
    $flashes = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
    unset($_SESSION['flash']);

    return $flashes;
}

/** Numbered pagination helper for admin tables. */
function paginate($total, $perPage, $currentPage)
{
    $total   = max(0, (int) $total);
    $perPage = max(1, (int) $perPage);
    $pages   = max(1, (int) ceil($total / $perPage));
    $page    = min(max(1, (int) $currentPage), $pages);

    return [
        'page'     => $page,
        'pages'    => $pages,
        'per_page' => $perPage,
        'offset'   => ($page - 1) * $perPage,
        'total'    => $total,
    ];
}

/** Render a partial from /components or /includes. */
function partial($name, array $data = [])
{
    $file = path('components/' . $name . '.php');
    if (!is_file($file)) {
        $file = path('includes/' . $name . '.php');
    }
    if (!is_file($file)) {
        if (config('app.debug')) {
            echo '<!-- missing partial: ' . e($name) . ' -->';
        }
        return;
    }

    extract($data, EXTR_SKIP);
    include $file;
}

/** Render a page view from /pages. */
function view($name, array $data = [])
{
    $file = path('pages/' . $name . '.php');
    if (!is_file($file)) {
        http_response_code(500);
        echo 'View not found: ' . e($name);
        return;
    }

    extract($data, EXTR_SKIP);
    include $file;
}
