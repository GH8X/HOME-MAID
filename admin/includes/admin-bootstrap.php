<?php
/**
 * Admin bootstrap — loaded by every page inside /admin.
 *
 * Loads the application, the seeder (for the "import default content" action)
 * and the admin layout helpers. Call require_admin() after including this file
 * on any page that a signed-out visitor must not reach.
 */
require dirname(__DIR__, 2) . '/includes/bootstrap.php';
require M4C_ROOT . '/includes/seeder.php';
require __DIR__ . '/layout.php';
require __DIR__ . '/crud.php';
