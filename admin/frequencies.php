<?php
/**
 * Admin → Cleaning schedules
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$result = m4c_crud_handle('frequencies');

admin_header('Schedules', 'frequencies');
admin_page_head('Cleaning schedules', 'AutoPilot frequencies and their discounts, shown on the services page and as options in the quote form.');

if ($result['errors']) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($result['errors'] as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

m4c_crud_render('frequencies');
admin_footer();
