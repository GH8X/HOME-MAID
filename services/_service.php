<?php
/**
 * Shared controller for a single cleaning package.
 *
 * Each /services/{slug}/index.php sets $service_slug and requires this file.
 * Requested directly, this file resolves to nothing and returns a 404.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';

$slug = isset($service_slug) ? (string) $service_slug : '';
$service = $slug !== '' ? service($slug) : null;

if (!$service || (int) $service['is_published'] !== 1) {
    http_response_code(404);
    view('not-found');
    exit;
}

view('service-detail', ['service' => $service]);
