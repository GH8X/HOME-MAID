<?php
/**
 * /admin/logout.php — destroy the administrator session.
 */
require __DIR__ . '/includes/admin-bootstrap.php';

admin_logout();

// Rotate the CSRF token so the old session cannot be reused.
unset($_SESSION['csrf_token']);

flash('success', 'You have been signed out.');
redirect('admin/index.php');
