<?php
$biz       = business();
$state     = m4c_handle_quote();

// Pre-select a package when arriving from a service page (?service=deep-cleaning).
$preselect = isset($_GET['service']) ? clean_line($_GET['service'], 80) : '';
if (!$state['attempted'] && $preselect !== '' && service($preselect)) {
    $state['values']['service_slug'] = $preselect;
    if ($preselect === 'recurring-cleaning' && empty($state['values']['frequency_slug'])) {
        $state['values']['frequency_slug'] = 'bi-weekly';
    }
}

$submitted = isset($_GET['submitted']) && $_GET['submitted'] === '1' && !empty($_SESSION['quote_submitted']);
$summary   = $submitted ? $_SESSION['quote_submitted'] : null;
unset($_SESSION['quote_submitted']);

partial('header', [
    'head' => [
        'title'       => 'Get a Free Cleaning Quote in Toronto | Maid4Condos',
        'description' => 'Request a free, no-obligation quote for condo cleaning in Toronto and the GTA. Tell us about your space, pick your package and we will confirm pricing within one business day.',
        'canonical'   => '/get-a-quote',
        'robots'      => $submitted ? 'noindex,follow' : setting('robots_policy', 'index,follow'),
        'og_image'    => 'assets/images/hero-cleaning-modern-home.jpg',
        'schema'      => $submitted ? [] : [schema_breadcrumbs(['Home' => '', 'Get a quote' => 'get-a-quote'])],
    ],
    'body_class' => 'page-quote',
]);
?>

<?php if ($submitted && $summary) : ?>
    <section class="page-hero page-hero--success" aria-labelledby="quote-thanks">
        <div class="page-hero__bg" aria-hidden="true"><span class="bubble-field"></span></div>
        <div class="container container--narrow">
            <div class="success-panel" role="status" data-ga-event="quote_form_submit" data-ga-label="Quote submitted">
                <span class="success-panel__icon"><?php partial('icon', ['name' => 'check-circle', 'size' => 34]); ?></span>
                <h1 id="quote-thanks"><?= e(setting('quote_success_title', 'Thank you! Your request has been received.')) ?></h1>
                <p class="success-panel__text"><?= e(setting('quote_success_text', 'A member of our team will contact you shortly.')) ?></p>

                <dl class="success-panel__facts">
                    <div>
                        <dt>Request name</dt>
                        <dd><?= e($summary['name']) ?></dd>
                    </div>
                    <?php if (!empty($summary['service'])) : ?>
                        <div>
                            <dt>Service requested</dt>
                            <dd><?= e($summary['service']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div>
                        <dt>Next step</dt>
                        <dd>We will confirm pricing and availability</dd>
                    </div>
                </dl>

                <div class="success-panel__actions">
                    <a class="btn btn--primary" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Quote success">
                        <?php partial('icon', ['name' => 'phone', 'size' => 17]); ?> Call <?= e($biz['phone_display']) ?>
                    </a>
                    <a class="btn btn--outline" href="<?= e(url('services')) ?>">Browse cleaning packages</a>
                </div>

                <p class="success-panel__meta">
                    Already thinking of your next visit? You can also email
                    <a href="mailto:<?= e($biz['booking_email']) ?>" data-ga-event="email_click" data-ga-label="Quote success"><?= e($biz['booking_email']) ?></a>.
                </p>
            </div>
        </div>
    </section>
<?php else : ?>
    <?php partial('page-hero', [
        'eyebrow' => 'Get a quote',
        'title'   => 'Your free cleaning quote in about a minute',
        'intro'   => 'Tell us about your condo and what kind of clean you are after. There is no obligation and no account required — we simply confirm your price and availability.',
        'crumbs'  => ['Home' => '', 'Get a quote' => 'get-a-quote'],
        'facts'   => [
            'No account needed',
            'Reply within one business day',
            'Up to 20% off recurring schedules',
            'Backed by our 24 hour guarantee',
        ],
    ]); ?>

    <section class="section" aria-labelledby="quote-form-title">
        <div class="container">
            <div class="quote-layout">
                <div class="quote-layout__form" id="quote-form">
                    <div class="section-head section-head--compact">
                        <h2 class="section-head__title" id="quote-form-title">Tell us about your space</h2>
                        <p class="section-head__text">Four short steps. Anything that affects pricing, we will confirm with you before booking.</p>
                    </div>
                    <?php partial('quote-form', ['state' => $state]); ?>
                </div>

                <aside class="quote-layout__aside" aria-label="What happens next">
                    <div class="aside-card">
                        <h2><?php partial('icon', ['name' => 'clock-fast', 'size' => 18]); ?> What happens next</h2>
                        <ol class="aside-steps">
                            <li><span>01</span> We review your details and confirm your package pricing.</li>
                            <li><span>02</span> We check availability for your preferred date and window.</li>
                            <li><span>03</span> You approve, and we lock in your clean.</li>
                        </ol>
                    </div>

                    <div class="aside-card">
                        <h2><?php partial('icon', ['name' => 'sparkles', 'size' => 18]); ?> Popular choices</h2>
                        <ul class="aside-links">
                            <?php foreach (services_all() as $service) : ?>
                                <li>
                                    <a href="<?= e(url('services/' . $service['slug'])) ?>">
                                        <span><?= e($service['name']) ?></span>
                                        <small><?= !empty($service['price_from']) ? 'From ' . e(money($service['price_from'])) : 'Up to 20% off' ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="aside-card aside-card--ink">
                        <h2><?php partial('icon', ['name' => 'shield', 'size' => 18]); ?> Our guarantee</h2>
                        <p><?= e(excerpt($biz['guarantee'], 240)) ?></p>
                        <a class="btn btn--outline-light btn--block" href="<?= e(url('faq')) ?>">Read the FAQ</a>
                    </div>

                    <div class="aside-card aside-card--contact">
                        <h2>Prefer to talk?</h2>
                        <p>Call us Monday to Friday and we will build the quote with you.</p>
                        <a class="btn btn--primary btn--block" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Quote aside">
                            <?php partial('icon', ['name' => 'phone', 'size' => 17]); ?> <?= e($biz['phone_display']) ?>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (!$submitted) { partial('cta-band', ['show_phone' => false]); } ?>
<?php partial('footer'); ?>
