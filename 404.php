<?php
/**
 * Error document for Apache (see ErrorDocument in .htaccess).
 */
require __DIR__ . '/includes/bootstrap.php';

http_response_code(404);
view('not-found');
