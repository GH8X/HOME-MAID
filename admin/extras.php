<?php
/**
 * Admin → Add-ons
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$result = m4c_crud_handle('extras');

admin_header('Add-ons', 'extras');
admin_page_head('Add-on services', 'Extras customers can add to a cleaning. Published add-ons appear on the services page and as choices in the quote form.');

if ($result['errors']) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($result['errors'] as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

m4c_crud_render('extras');
admin_footer();
