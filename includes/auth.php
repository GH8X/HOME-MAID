<?php
/**
 * Maid4Condos — administrator authentication.
 *
 * Deliberately separate from any public/front-end account: only rows in the
 * `admins` table can authenticate, passwords are stored with password_hash(),
 * and every protected page calls require_admin().
 */

/** Currently authenticated administrator row, or null. */
function current_admin()
{
    static $admin = null;
    static $loaded = false;

    if ($loaded) {
        return $admin;
    }
    $loaded = true;

    $id = isset($_SESSION['m4c_admin_id']) ? (int) $_SESSION['m4c_admin_id'] : 0;
    if ($id < 1 || !db_available()) {
        return null;
    }

    $admin = db_one(
        'SELECT id, name, email, role, last_login_at, is_active
           FROM ' . table('admins') . '
          WHERE id = ? AND is_active = 1
          LIMIT 1',
        [$id]
    );

    // Bind the session to a user agent + IP fingerprint to blunt session theft.
    $fingerprint = admin_fingerprint();
    if ($admin && isset($_SESSION['m4c_admin_fp']) && $_SESSION['m4c_admin_fp'] !== $fingerprint) {
        admin_logout();
        $admin = null;
    }

    return $admin;
}

function admin_fingerprint()
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $ip    = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    return hash('sha256', $agent . '|' . $ip);
}

function admin_logged_in()
{
    return current_admin() !== null;
}

/** True for the owner role (can manage other administrators). */
function admin_is_owner()
{
    $admin = current_admin();

    return $admin && $admin['role'] === 'owner';
}

/** Attempt a login. Returns null on success or a human-readable error. */
function admin_attempt_login($email, $password)
{
    $email = strtolower(clean_line($email, 190));
    if (!valid_email($email) || $password === '') {
        return 'Enter your administrator email and password.';
    }

    // Throttle by email + IP to slow credential stuffing.
    $bucket = 'admin_login_' . md5($email . '|' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''));
    if (!throttle($bucket, (int) config('security.login_max_attempts', 5), (int) config('security.login_lockout', 900))) {
        return 'Too many failed attempts. Please wait a few minutes before trying again.';
    }

    if (!db_available()) {
        return 'The database is unavailable. Check config/config.local.php.';
    }

    $admin = db_one(
        'SELECT id, name, email, password_hash, role, is_active
           FROM ' . table('admins') . '
          WHERE email = ?
          LIMIT 1',
        [$email]
    );

    // Always run a hash comparison so timing does not reveal account existence.
    $hash = $admin['password_hash'] ?? '$2y$10$invalidinvalidinvalidinvalidinvalidinvalidinvalidinvalidinv';
    if (!password_verify($password, $hash) || !$admin || (int) $admin['is_active'] !== 1) {
        return 'Those credentials do not match an administrator account.';
    }

    // Re-hash transparently when the cost factor has changed.
    if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
        db_update(table('admins'), ['password_hash' => password_hash($password, PASSWORD_DEFAULT)], 'id = ?', [$admin['id']]);
    }

    session_regenerate_id(true);
    $_SESSION['m4c_admin_id'] = (int) $admin['id'];
    $_SESSION['m4c_admin_fp'] = admin_fingerprint();
    $_SESSION['m4c_admin_at'] = time();

    db_update(table('admins'), ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$admin['id']]);
    throttle_clear($bucket);

    return null;
}

function admin_logout()
{
    unset($_SESSION['m4c_admin_id'], $_SESSION['m4c_admin_fp'], $_SESSION['m4c_admin_at']);
    session_regenerate_id(true);
}

/** Block the request unless an administrator is signed in. */
function require_admin()
{
    if (admin_logged_in()) {
        // Idle timeout (default 2 hours).
        $last = isset($_SESSION['m4c_admin_at']) ? (int) $_SESSION['m4c_admin_at'] : 0;
        if ($last > 0 && (time() - $last) > 7200) {
            admin_logout();
            flash('info', 'You were signed out after a period of inactivity.');
            redirect('admin/');
        }
        $_SESSION['m4c_admin_at'] = time();

        return;
    }

    $target = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : url('admin/');
    redirect('admin/index.php?returnTo=' . rawurlencode($target));
}

/** Block the request unless the signed-in administrator is the owner. */
function require_owner()
{
    require_admin();
    if (!admin_is_owner()) {
        http_response_code(403);
        flash('error', 'Only the account owner can manage administrators.');
        redirect('admin/index.php');
    }
}
