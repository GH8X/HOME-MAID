<?php
/**
 * Admin layout helpers: sidebar navigation, header and footer.
 */

/** Absolute URL for a page inside /admin. */
function admin_url($path = '')
{
    return url('admin/' . ltrim((string) $path, '/'));
}

/** Navigation model for the admin sidebar. */
function admin_nav_items()
{
    $items = [
        ['key' => 'dashboard', 'label' => 'Overview',    'route' => 'index.php',        'icon' => 'chart'],
        ['key' => 'inquiries', 'label' => 'Quote requests', 'route' => 'inquiries.php', 'icon' => 'inbox'],
        ['key' => 'messages',  'label' => 'Messages',      'route' => 'messages.php',   'icon' => 'mail'],
    ];

    $content = [
        ['key' => 'services',     'label' => 'Services',     'route' => 'services.php',     'icon' => 'spark'],
        ['key' => 'blocks',       'label' => 'Page sections', 'route' => 'blocks.php',       'icon' => 'list'],
        ['key' => 'faqs',         'label' => 'FAQs',         'route' => 'faqs.php',         'icon' => 'help'],
        ['key' => 'testimonials', 'label' => 'Testimonials', 'route' => 'testimonials.php', 'icon' => 'quote'],
        ['key' => 'areas',        'label' => 'Service areas', 'route' => 'areas.php',       'icon' => 'map'],
        ['key' => 'extras',       'label' => 'Add-ons',      'route' => 'extras.php',       'icon' => 'spray'],
        ['key' => 'frequencies',  'label' => 'Schedules',    'route' => 'frequencies.php',  'icon' => 'calendar'],
        ['key' => 'navigation',   'label' => 'Navigation',   'route' => 'navigation.php',   'icon' => 'list'],
        ['key' => 'media',        'label' => 'Media',        'route' => 'media.php',        'icon' => 'image'],
    ];

    $system = [
        ['key' => 'settings', 'label' => 'Settings & SEO', 'route' => 'settings.php', 'icon' => 'settings'],
        ['key' => 'admins',   'label' => 'Administrators', 'route' => 'admins.php',   'icon' => 'users'],
    ];

    return ['main' => $items, 'content' => $content, 'system' => $system];
}

/** Render queued flash messages. */
function admin_flashes()
{
    foreach (take_flashes() as $flash) {
        $type = $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'info' ? 'info' : 'success');
        echo '<div class="alert alert--' . e($type) . '" role="status">' . e($flash['message']) . '</div>';
    }
}

/** Small stat card used on the dashboard. */
function admin_stat($label, $value, $route = null, $icon = 'chart')
{
    $tag = $route ? 'a' : 'div';
    echo '<' . $tag . ' class="stat-card"' . ($route ? ' href="' . e(admin_url($route)) . '"' : '') . '>';
    echo '<span class="stat-card__icon">';
    partial('icon', ['name' => $icon, 'size' => 20]);
    echo '</span>';
    echo '<span class="stat-card__value">' . e($value) . '</span>';
    echo '<span class="stat-card__label">' . e($label) . '</span>';
    echo '</' . $tag . '>';
}

/** Page header inside the admin content area. */
function admin_page_head($title, $subtitle = '', array $actions = [])
{
    ?>
    <header class="admin-head">
        <div>
            <h1><?= e($title) ?></h1>
            <?php if ($subtitle !== '') : ?>
                <p><?= e($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($actions) : ?>
            <div class="admin-head__actions">
                <?php foreach ($actions as $action) : ?>
                    <a class="btn btn--<?= e($action['variant'] ?? 'outline') ?> btn--sm" href="<?= e($action['href']) ?>">
                        <?= e($action['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </header>
    <?php
}

/** Open the admin document. */
function admin_header($title, $active = '')
{
    $admin  = current_admin();
    $biz    = business();
    $nav    = admin_nav_items();
    ?>
<!doctype html>
<html lang="en-CA">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title) ?> · <?= e($biz['name']) ?> admin</title>
    <link rel="icon" href="<?= e(asset('images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin-body">
<a class="skip-link" href="#admin-main">Skip to content</a>

<button class="admin-burger" type="button" data-admin-nav-toggle aria-expanded="false" aria-controls="adminSidebar">
    <?php partial('icon', ['name' => 'menu', 'size' => 20]); ?>
    <span>Menu</span>
</button>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
        <img src="<?= e(asset('images/logo.svg')) ?>" width="38" height="38" alt="" aria-hidden="true">
        <span>
            <strong><?= e($biz['name']) ?></strong>
            <small>Content manager</small>
        </span>
    </div>

    <nav class="admin-nav" aria-label="Admin">
        <?php foreach ($nav['main'] as $item) : ?>
            <a class="admin-nav__link <?= $active === $item['key'] ? 'is-active' : '' ?>" href="<?= e(admin_url($item['route'])) ?>">
                <?php partial('icon', ['name' => $item['icon'], 'size' => 18]); ?>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>

        <p class="admin-nav__label">Website content</p>
        <?php foreach ($nav['content'] as $item) : ?>
            <a class="admin-nav__link <?= $active === $item['key'] ? 'is-active' : '' ?>" href="<?= e(admin_url($item['route'])) ?>">
                <?php partial('icon', ['name' => $item['icon'], 'size' => 18]); ?>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>

        <p class="admin-nav__label">System</p>
        <?php foreach ($nav['system'] as $item) : ?>
            <?php if ($item['key'] === 'admins' && !admin_is_owner()) { continue; } ?>
            <a class="admin-nav__link <?= $active === $item['key'] ? 'is-active' : '' ?>" href="<?= e(admin_url($item['route'])) ?>">
                <?php partial('icon', ['name' => $item['icon'], 'size' => 18]); ?>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="admin-sidebar__foot">
        <a class="admin-nav__link" href="<?= e(url()) ?>" target="_blank" rel="noopener">
            <?php partial('icon', ['name' => 'external', 'size' => 18]); ?>
            <span>View website</span>
        </a>
        <a class="admin-nav__link admin-nav__link--danger" href="<?= e(admin_url('logout.php')) ?>">
            <?php partial('icon', ['name' => 'logout', 'size' => 18]); ?>
            <span>Sign out</span>
        </a>
    </div>
</aside>

<div class="admin-backdrop" data-admin-nav-toggle hidden></div>

<main class="admin-main" id="admin-main">
    <header class="admin-topbar">
        <div class="admin-topbar__meta">
            <span><?= e(date('l, F j')) ?></span>
            <?php if (!db_available()) : ?>
                <span class="badge badge--warn">No database connected</span>
            <?php endif; ?>
        </div>
        <div class="admin-topbar__user">
            <span class="admin-avatar" aria-hidden="true"><?= e(strtoupper(substr((string) ($admin['name'] ?? 'A'), 0, 1))) ?></span>
            <span>
                <strong><?= e($admin['name'] ?? 'Administrator') ?></strong>
                <small><?= e(ucfirst($admin['role'] ?? 'editor')) ?></small>
            </span>
        </div>
    </header>

    <div class="admin-content">
        <?php admin_flashes(); ?>
    <?php
}

/** Close the admin document. */
function admin_footer()
{
    ?>
    </div>
</main>
<script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</body>
</html>
    <?php
}
