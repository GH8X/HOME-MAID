<?php
/**
 * Admin → Navigation
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$result = m4c_crud_handle('navigation');

admin_header('Navigation', 'navigation');
admin_page_head('Primary navigation', 'The links shown in the header (desktop and mobile) and in the footer navigation column.');

if ($result['errors']) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($result['errors'] as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

m4c_crud_render('navigation');
admin_footer();
