<?php
/**
 * Admin → Service areas
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$result = m4c_crud_handle('areas');

admin_header('Service areas', 'areas');
admin_page_head('Service areas', 'Neighbourhoods and regions shown on the homepage, contact page, footer and in LocalBusiness structured data.');

if ($result['errors']) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($result['errors'] as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

m4c_crud_render('areas');
admin_footer();
