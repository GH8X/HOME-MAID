<?php
/**
 * Admin → Testimonials
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$result = m4c_crud_handle('testimonials');

admin_header('Testimonials', 'testimonials');
admin_page_head('Testimonials', 'Only publish genuine client reviews — never invent a review, a rating or a source.');

if ($result['errors']) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($result['errors'] as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

m4c_crud_render('testimonials');
admin_footer();
