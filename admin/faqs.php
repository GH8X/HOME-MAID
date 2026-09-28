<?php
/**
 * Admin → FAQs
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$result = m4c_crud_handle('faqs');

admin_header('FAQs', 'faqs');
admin_page_head('Frequently asked questions', 'Manage the questions shown on the FAQ page, the homepage preview and in FAQPage structured data.');

if ($result['errors']) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($result['errors'] as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

m4c_crud_render('faqs');
admin_footer();
