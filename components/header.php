<?php
/**
 * Site header: utility bar, sticky navigation and the mobile menu panel.
 *
 * @var array $head        Head options passed to m4c_head().
 * @var string $body_class Optional body class.
 * @var string $active     Optional route override for active-state highlighting.
 */
$head       = isset($head) ? $head : [];
$bodyClass  = isset($body_class) ? $body_class : '';
$biz        = business();
$navItems   = navigation_items();
$services   = services_all();
$current    = isset($active) ? '/' . trim($active, '/') : current_path();
$logoMark   = asset('images/logo.svg');
?><!doctype html>
<html lang="en-CA">
<head>
<?php m4c_head($head); ?>
</head>
<body class="<?= e(trim($bodyClass)) ?>">
<a class="skip-link" href="#main">Skip to main content</a>

<div class="topbar" id="topbar">
    <div class="container topbar__inner">
        <p class="topbar__note">
            <?php partial('icon', ['name' => 'sparkles', 'size' => 16]); ?>
            <span>Toronto &amp; the GTA · Family run since <?= e($biz['founded']) ?></span>
        </p>
        <div class="topbar__links">
            <a href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Top bar phone">
                <?php partial('icon', ['name' => 'phone', 'size' => 15]); ?>
                <span><?= e($biz['phone_display']) ?></span>
            </a>
            <a href="mailto:<?= e($biz['email']) ?>" data-ga-event="email_click" data-ga-label="Top bar email">
                <?php partial('icon', ['name' => 'mail', 'size' => 15]); ?>
                <span><?= e($biz['email']) ?></span>
            </a>
            <?php if (!empty($biz['social']['instagram'])) : ?>
                <a class="topbar__social" href="<?= e($biz['social']['instagram']) ?>" target="_blank" rel="noopener" aria-label="Maid4Condos on Instagram">
                    <?php partial('icon', ['name' => 'instagram', 'size' => 16]); ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($biz['social']['facebook'])) : ?>
                <a class="topbar__social" href="<?= e($biz['social']['facebook']) ?>" target="_blank" rel="noopener" aria-label="Maid4Condos on Facebook">
                    <?php partial('icon', ['name' => 'facebook', 'size' => 16]); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="site-header" id="siteHeader">
    <div class="container site-header__inner">
        <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e($biz['name']) ?> — home">
            <img class="brand__mark" src="<?= e($logoMark) ?>" width="44" height="44" alt="" aria-hidden="true" decoding="async">
            <span class="brand__text">
                <span class="brand__name">Maid<span>4</span>Condos</span>
                <span class="brand__tag"><?= e($biz['tagline']) ?></span>
            </span>
        </a>

        <nav class="primary-nav" aria-label="Primary">
            <ul class="primary-nav__list">
                <li class="has-menu">
                    <a href="<?= e(url('services')) ?>" class="<?= strpos($current, '/services') === 0 ? 'is-current' : '' ?>"
                       aria-haspopup="true" aria-expanded="false" data-dropdown-toggle>
                        Services
                        <?php partial('icon', ['name' => 'chevron', 'size' => 15]); ?>
                    </a>
                    <div class="mega" role="menu" aria-label="Cleaning services">
                        <div class="mega__grid">
                            <?php foreach ($services as $service) : ?>
                                <a class="mega__item" role="menuitem" href="<?= e(url('services/' . $service['slug'])) ?>">
                                    <span class="mega__icon"><?php partial('icon', ['name' => 'spark', 'size' => 18]); ?></span>
                                    <span>
                                        <strong><?= e($service['name']) ?></strong>
                                        <small><?= e($service['eyebrow']) ?></small>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                            <a class="mega__item mega__item--all" role="menuitem" href="<?= e(url('services')) ?>">
                                <span class="mega__icon"><?php partial('icon', ['name' => 'list', 'size' => 18]); ?></span>
                                <span>
                                    <strong>All services &amp; extras</strong>
                                    <small>Compare every package</small>
                                </span>
                            </a>
                        </div>
                        <div class="mega__aside">
                            <p class="mega__aside-title">Not sure which clean you need?</p>
                            <p>Tell us about your condo and we will recommend the right package.</p>
                            <a class="btn btn--primary btn--sm" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Mega menu">Get a quote</a>
                            <a class="mega__phone" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Mega menu phone">
                                <?php partial('icon', ['name' => 'phone', 'size' => 16]); ?>
                                <?= e($biz['phone_display']) ?>
                            </a>
                        </div>
                    </div>
                </li>
                <?php foreach ($navItems as $item) : ?>
                    <li>
                        <a href="<?= e(url($item['route'])) ?>"
                           class="<?= strpos($current . '/', '/' . trim($item['route'], '/') . '/') === 0 ? 'is-current' : '' ?>">
                            <?= e($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="site-header__actions">
            <a class="header-phone" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Header phone">
                <span class="header-phone__icon"><?php partial('icon', ['name' => 'phone', 'size' => 17]); ?></span>
                <span class="header-phone__text">
                    <small>Call us</small>
                    <strong><?= e($biz['phone_display']) ?></strong>
                </span>
            </a>
            <a class="btn btn--primary header-quote" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Header">
                <span class="header-quote__full">Get a quote</span>
                <span class="header-quote__short">Quote</span>
                <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?>
            </a>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mobileNav"
                    data-nav-toggle aria-label="Open menu">
                <span class="nav-toggle__bars" aria-hidden="true"><span></span><span></span><span></span></span>
                <span class="nav-toggle__label">Menu</span>
            </button>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobileNav" hidden>
    <div class="mobile-nav__backdrop" data-nav-close></div>
    <div class="mobile-nav__panel" role="dialog" aria-modal="true" aria-label="Site menu">
        <div class="mobile-nav__head">
            <span class="mobile-nav__title">Menu</span>
            <button class="mobile-nav__close" type="button" data-nav-close aria-label="Close menu">
                <?php partial('icon', ['name' => 'close', 'size' => 22]); ?>
            </button>
        </div>
        <nav class="mobile-nav__body" aria-label="Mobile">
            <p class="mobile-nav__label">Cleaning services</p>
            <ul class="mobile-nav__services">
                <?php foreach ($services as $service) : ?>
                    <li>
                        <a href="<?= e(url('services/' . $service['slug'])) ?>">
                            <?php partial('icon', ['name' => 'arrow-right', 'size' => 16]); ?>
                            <?= e($service['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="mobile-nav__label">Explore</p>
            <ul class="mobile-nav__main">
                <?php foreach ($navItems as $item) : ?>
                    <li><a href="<?= e(url($item['route'])) ?>"><?= e($item['label']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?= e(url('privacy-policy')) ?>">Privacy policy</a></li>
                <li><a href="<?= e(url('terms')) ?>">Terms &amp; conditions</a></li>
            </ul>
        </nav>
        <div class="mobile-nav__footer">
            <a class="btn btn--primary btn--block" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Mobile menu">
                Get a quote
            </a>
            <div class="mobile-nav__contact">
                <a href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Mobile menu phone">
                    <?php partial('icon', ['name' => 'phone', 'size' => 16]); ?> <?= e($biz['phone_display']) ?>
                </a>
                <a href="mailto:<?= e($biz['email']) ?>" data-ga-event="email_click" data-ga-label="Mobile menu email">
                    <?php partial('icon', ['name' => 'mail', 'size' => 16]); ?> <?= e($biz['email']) ?>
                </a>
            </div>
        </div>
    </div>
</div>

<main id="main">
