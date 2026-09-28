<?php
/**
 * Maid4Condos — security utilities: headers, CSRF, validation, throttling, uploads.
 */

/** Emit hardened response headers. */
function m4c_send_security_headers()
{
    if (PHP_SAPI === 'cli' || headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header_remove('X-Powered-By');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    $csp = "default-src 'self'; "
        . "base-uri 'self'; "
        . "frame-ancestors 'self'; "
        . "object-src 'none'; "
        . "img-src 'self' data: https:; "
        . "font-src 'self' https://fonts.gstatic.com data:; "
        . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
        . "script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://www.google-analytics.com; "
        . "connect-src 'self' https://www.google-analytics.com https://region1.google-analytics.com; "
        . "frame-src https://www.google.com https://maps.google.com; "
        . "form-action 'self'";

    header('Content-Security-Policy: ' . $csp);
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

/** Current session CSRF token (created on demand). */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/** Hidden CSRF input for a form. */
function csrf_field()
{
    return '<input type="hidden" name="' . e(config('security.csrf_key', '_token')) . '" value="' . e(csrf_token()) . '">';
}

/** Validate a submitted CSRF token using a timing-safe comparison. */
function csrf_valid($token = null)
{
    if ($token === null) {
        $token = isset($_POST[config('security.csrf_key', '_token')])
            ? $_POST[config('security.csrf_key', '_token')]
            : (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '');
    }

    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Abort the request when the CSRF token is missing or wrong. */
function require_csrf()
{
    if (!csrf_valid()) {
        http_response_code(419);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Session expired</title>'
            . '<meta name="robots" content="noindex"></head><body style="font-family:system-ui;padding:3rem;max-width:40rem;margin:auto">'
            . '<h1>Your session expired</h1><p>For your security this form needs to be reloaded. '
            . '<a href="' . e(url('get-a-quote')) . '">Return to the quote form</a>.</p></body></html>';
        exit;
    }
}

// ---------------------------------------------------------------------------
// Request helpers
// ---------------------------------------------------------------------------

function is_post()
{
    return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Simple spam heuristics shared by public forms:
 *  - the honeypot field must stay empty,
 *  - the form must not be submitted faster than a human could type it.
 */
function spam_check(array $errors)
{
    if (!empty($errors)) {
        return $errors;
    }

    if (input('website') !== '') {
        $errors[] = 'We could not verify this submission. Please try again.';
    }

    $started = (int) input('form_started', 0);
    if ($started > 0) {
        $elapsed = time() - $started;
        $min     = (int) config('security.form_min_seconds', 2);
        if ($elapsed >= 0 && $elapsed < $min) {
            $errors[] = 'That was a little too quick — please take a moment and submit again.';
        }
        if ($elapsed > 86400) {
            $errors[] = 'This form expired. Please reload the page and try again.';
        }
    }

    return $errors;
}

// ---------------------------------------------------------------------------
// Validation / sanitising
// ---------------------------------------------------------------------------

/** Strip control characters and normalise whitespace. */
function clean_text($value, $maxLength = 5000)
{
    $value = (string) $value;
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
    $value = preg_replace('/[ \t]+/', ' ', $value);
    $value = preg_replace("/\n{3,}/", "\n\n", $value);
    $value = trim($value);
    if ($maxLength > 0 && function_exists('mb_substr')) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}

/** Collapse a value to a single line. */
function clean_line($value, $maxLength = 255)
{
    return clean_text(preg_replace('/\s+/', ' ', (string) $value), $maxLength);
}

function valid_email($value)
{
    return (bool) filter_var((string) $value, FILTER_VALIDATE_EMAIL);
}

/** Keep digits, spaces, dashes, dots, parentheses and an optional leading +. */
function clean_phone($value)
{
    return trim(preg_replace('/[^0-9\(\)\+\-\.\s]/', '', (string) $value));
}

function valid_phone($value)
{
    $digits = preg_replace('/\D/', '', (string) $value);

    return strlen($digits) >= 10 && strlen($digits) <= 15;
}

/** Validate an ISO date (Y-m-d). */
function valid_date($value)
{
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $value, $m)) {
        return false;
    }

    return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
}

/** Restrict a value to an allow-list. */
function in_allowlist($value, array $allowed, $fallback = null)
{
    return in_array($value, $allowed, true) ? $value : $fallback;
}

// ---------------------------------------------------------------------------
// Throttling (file backed — works without a database)
// ---------------------------------------------------------------------------

function m4c_storage_dir()
{
    $dir = path('storage');
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    return $dir;
}

/**
 * Fault-tolerant throttle: returns false once the limit is exceeded.
 * Falls back to a session counter when the storage folder is not writable.
 */
function throttle($bucket, $maxAttempts, $decaySeconds)
{
    $key  = preg_replace('/[^a-z0-9_\-]/i', '', (string) $bucket);
    $now  = time();
    $file = m4c_storage_dir() . '/throttle_' . $key . '.json';

    if (!is_writable(m4c_storage_dir())) {
        $sessionKey = 'throttle_' . $key;
        $data = isset($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : ['count' => 0, 'reset' => $now + $decaySeconds];
        if ($data['reset'] < $now) {
            $data = ['count' => 0, 'reset' => $now + $decaySeconds];
        }
        $data['count']++;
        $_SESSION[$sessionKey] = $data;

        return $data['count'] <= $maxAttempts;
    }

    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        return true;
    }
    @flock($handle, LOCK_EX);
    $raw  = stream_get_contents($handle);
    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['reset']) || $data['reset'] < $now) {
        $data = ['count' => 0, 'reset' => $now + $decaySeconds];
    }
    $data['count']++;
    $allowed = $data['count'] <= $maxAttempts;
    @ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($data));
    @flock($handle, LOCK_UN);
    fclose($handle);

    return $allowed;
}

function throttle_clear($bucket)
{
    $key  = preg_replace('/[^a-z0-9_\-]/i', '', (string) $bucket);
    $file = m4c_storage_dir() . '/throttle_' . $key . '.json';
    if (is_file($file)) {
        @unlink($file);
    }
    unset($_SESSION['throttle_' . $key]);
}

// ---------------------------------------------------------------------------
// Uploads
// ---------------------------------------------------------------------------

/**
 * Validate and store an uploaded image inside /uploads.
 *
 * Checks: PHP upload error, byte size, real MIME type (finfo), extension
 * allow-list, and image dimensions for raster formats. SVG is sanitised
 * conservatively and stored with a .svg extension.
 *
 * @return array{0:?string,1:?string} [relative path, error message]
 */
function handle_image_upload($field, $prefix = 'img')
{
    if (empty($_FILES[$field]) || !is_array($_FILES[$field])) {
        return [null, null];
    }

    $file = $_FILES[$field];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'The upload failed (error code ' . (int) $file['error'] . ').'];
    }

    $maxBytes = (int) config('security.upload_max_bytes', 3145728);
    if (!is_uploaded_file($file['tmp_name'])) {
        return [null, 'The uploaded file could not be verified.'];
    }
    if ((int) $file['size'] > $maxBytes) {
        return [null, 'Images must be smaller than ' . round($maxBytes / 1048576, 1) . ' MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string) $finfo->file($file['tmp_name']);
    $allowed = (array) config('security.upload_mime', ['image/jpeg', 'image/png', 'image/webp']);
    if (!in_array($mime, $allowed, true)) {
        return [null, 'Unsupported image type. Use JPG, PNG, WebP or SVG.'];
    }

    $extensions = [
        'image/jpeg'    => 'jpg',
        'image/png'     => 'png',
        'image/webp'    => 'webp',
        'image/svg+xml' => 'svg',
    ];
    if (!isset($extensions[$mime])) {
        return [null, 'Unsupported image type.'];
    }
    $ext = $extensions[$mime];

    if ($ext !== 'svg') {
        $info = @getimagesize($file['tmp_name']);
        if ($info === false || empty($info[0]) || empty($info[1])) {
            return [null, 'That file is not a readable image.'];
        }
        if ($info[0] < 200 || $info[1] < 200) {
            return [null, 'Images must be at least 200×200 pixels.'];
        }
        $dimensions = [(int) $info[0], (int) $info[1]];
    } else {
        $svg = (string) file_get_contents($file['tmp_name'], false, null, 0, 512000);
        if (stripos($svg, '<svg') === false) {
            return [null, 'That SVG could not be read.'];
        }
        // Reject anything that could execute in a browser context.
        if (preg_match('/<\s*(script|foreignObject|iframe|embed|object|use\s+[^>]*xlink:href)/i', $svg)
            || preg_match('/on[a-z]+\s*=/i', $svg)
            || stripos($svg, 'javascript:') !== false) {
            return [null, 'That SVG contains unsupported markup.'];
        }
        $dimensions = [0, 0];
    }

    $dir = path(config('uploads.dir', 'uploads'));
    if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
        return [null, 'The uploads folder is not writable.'];
    }

    $name = sprintf(
        '%s-%s-%s.%s',
        slugify($prefix) ?: 'image',
        date('Ymd-His'),
        bin2hex(random_bytes(4)),
        $ext
    );

    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return [null, 'The file could not be saved. Check folder permissions.'];
    }
    @chmod($dir . '/' . $name, 0644);

    return [config('uploads.url', 'uploads') . '/' . $name, null];
}

/** Remove a previously uploaded file (only inside /uploads). */
function delete_upload($relative)
{
    $relative = ltrim((string) $relative, '/');
    if ($relative === '' || strpos($relative, '..') !== false) {
        return false;
    }
    $base = config('uploads.url', 'uploads') . '/';
    if (strpos($relative, $base) !== 0) {
        return false;
    }
    $file = path($relative);

    return is_file($file) ? @unlink($file) : false;
}
