<?php
/**
 * /services/{slug} — a single cleaning package.
 *
 * @var array $service
 */
$biz      = business();
$extras   = extras_all();
$others   = array_values(array_filter(services_all(), function ($item) use ($service) {
    return $item['slug'] !== $service['slug'];
}));
$crumbs   = ['Home' => '', 'Cleaning services' => 'services', $service['name'] => ''];
$includedPoints = [];
foreach ($service['checklist'] as $room => $items) {
    $includedPoints[] = $room . ' (' . count($items) . ')';
}
?>
<?php partial('header', [
    'head' => [
        'title'       => $service['meta_title'],
        'description' => $service['meta_description'],
        'canonical'   => '/services/' . $service['slug'],
        'og_type'     => 'article',
        'og_image'    => m4c_image_path($service['hero_image']),
        'preload_image' => strpos($service['hero_image'], 'uploads/') === 0 ? null : m4c_image_path($service['hero_image']),
        'schema'      => [
            schema_service($service),
            schema_breadcrumbs($crumbs),
            schema_faq_page($service['faqs']),
        ],
    ],
    'body_class' => 'page-service',
    'active'     => 'services',
]); ?>

<?php partial('page-hero', [
    'eyebrow'   => $service['eyebrow'],
    'title'     => $service['name'] . ' in Toronto',
    'intro'     => $service['tagline'] . '. ' . $service['summary'],
    'crumbs'    => $crumbs,
    'image'     => $service['hero_image'],
    'image_alt' => $service['name'] . ' cleaning in a Toronto condo',
    'actions'   => [
        ['label' => 'Get a quote', 'route' => 'get-a-quote', 'variant' => 'primary', 'ga' => 'quote_cta_click'],
        ['label' => 'Compare packages', 'route' => 'services', 'variant' => 'outline-light'],
    ],
    'facts'     => array_filter([
        !empty($service['price_from']) ? 'Starting from ' . money($service['price_from']) : 'Save up to 20% with AutoPilot',
        $service['duration_note'],
        $service['best_paired'] ? 'Best paired with ' . $service['best_paired'] : '',
        'Backed by our 24 hour guarantee',
    ]),
]); ?>

<div class="container service-layout">
    <div class="service-layout__main">
        <section class="section section--tight" aria-labelledby="about-package">
            <h2 id="about-package">About the <?= e($service['name']) ?> package</h2>
            <?php foreach ($service['intro'] as $paragraph) : ?>
                <p class="lead"><?= e($paragraph) ?></p>
            <?php endforeach; ?>
            <p class="data-ga-anchor" data-ga-event="service_view" data-ga-label="<?= e($service['name']) ?>" hidden></p>
        </section>

        <?php if (!empty($service['who_for'])) : ?>
            <section class="section section--tight" aria-labelledby="who-for">
                <h2 id="who-for">Who this package is for</h2>
                <ul class="promise-list promise-list--light">
                    <?php foreach ($service['who_for'] as $item) : ?>
                        <li><?php partial('icon', ['name' => 'check-circle', 'size' => 19]); ?> <span><?= e($item) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($service['checklist'])) : ?>
            <section class="section section--tight" aria-labelledby="checklist">
                <h2 id="checklist">What is included</h2>
                <p class="section__lead">Our <?= e($service['name']) ?> follows a written checklist so nothing gets missed — and we check it twice.</p>
                <div class="checklist-grid">
                    <?php foreach ($service['checklist'] as $room => $items) : ?>
                        <article class="checklist-card">
                            <h3 class="checklist-card__title">
                                <span class="checklist-card__icon"><?php partial('icon', ['name' => 'check', 'size' => 17]); ?></span>
                                <?= e($room) ?>
                            </h3>
                            <ul>
                                <?php foreach ($items as $item) : ?>
                                    <li><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                </div>
                <p class="checklist-note">
                    <?php partial('icon', ['name' => 'help', 'size' => 16]); ?>
                    All standard supplies and equipment are included. We only ask that you provide a vacuum and a toilet brush.
                </p>
            </section>
        <?php endif; ?>

        <?php if (!empty($service['benefits'])) : ?>
            <section class="section section--tight" aria-labelledby="benefits">
                <h2 id="benefits">Why clients book it</h2>
                <div class="benefit-grid">
                    <?php foreach ($service['benefits'] as $benefit) : ?>
                        <div class="benefit">
                            <span class="benefit__icon"><?php partial('icon', ['name' => 'spark', 'size' => 18]); ?></span>
                            <p><?= e($benefit) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="section section--tight" aria-labelledby="optional-extras">
            <h2 id="optional-extras">Optional extras</h2>
            <p class="section__lead">Add any of these when you book, or ask us to add them to an existing visit.</p>
            <div class="extra-grid extra-grid--compact">
                <?php foreach ($extras as $extra) : ?>
                    <article class="extra-card extra-card--compact">
                        <h3><?= e($extra['name']) ?></h3>
                        <p><?= e($extra['summary']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($service['faqs'])) : ?>
            <section class="section section--tight" aria-labelledby="service-faq">
                <h2 id="service-faq"><?= e($service['name']) ?> questions</h2>
                <?php partial('faq-list', [
                    'faqs'    => $service['faqs'],
                    'context' => 'service-' . $service['slug'],
                ]); ?>
            </section>
        <?php endif; ?>
    </div>

    <aside class="service-layout__aside" aria-label="Get a quote">
        <div class="quote-card">
            <h2 class="quote-card__title">Request your quote</h2>
            <?php if (!empty($service['price_from'])) : ?>
                <p class="quote-card__price"><small>Starting from</small> <strong><?= e(money($service['price_from'])) ?></strong></p>
                <p class="quote-card__note">Final pricing depends on your condo’s size, layout and condition. We confirm everything before anything is booked.</p>
            <?php else : ?>
                <p class="quote-card__price"><strong>Up to 20% off</strong> <small>every recurring visit</small></p>
                <p class="quote-card__note">Choose how often you would like us and we will apply the frequency discount to your quote.</p>
            <?php endif; ?>
            <ul class="quote-card__list">
                <?php foreach (array_slice($includedPoints, 0, 5) as $point) : ?>
                    <li><?php partial('icon', ['name' => 'check', 'size' => 15]); ?> <?= e($point) ?></li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn--primary btn--block" href="<?= e(url('get-a-quote') . '?service=' . $service['slug']) ?>" data-ga-event="quote_cta_click" data-ga-label="<?= e($service['name']) ?> sidebar">
                Get a quote <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?>
            </a>
            <a class="quote-card__phone" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="<?= e($service['name']) ?> sidebar">
                <?php partial('icon', ['name' => 'phone', 'size' => 16]); ?> <?= e($biz['phone_display']) ?>
            </a>
            <p class="quote-card__meta"><?php partial('icon', ['name' => 'clock', 'size' => 15]); ?> Reply within one business day</p>
        </div>

        <div class="aside-note">
            <h3><?php partial('icon', ['name' => 'badge', 'size' => 17]); ?> Our guarantee</h3>
            <p><?= e(excerpt($biz['guarantee'], 220)) ?></p>
        </div>
    </aside>
</div>

<section class="section section--shell" aria-labelledby="other-services">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?php partial('icon', ['name' => 'list', 'size' => 15]); ?> Also available</p>
                <h2 class="section-head__title" id="other-services">Other cleaning packages</h2>
            </div>
            <a class="link-arrow" href="<?= e(url('services')) ?>">View all <?php partial('icon', ['name' => 'arrow-right', 'size' => 15]); ?></a>
        </div>
        <div class="service-grid">
            <?php foreach (array_slice($others, 0, 3) as $other) : ?>
                <?php partial('service-card', ['service' => $other, 'heading_level' => 'h3', 'variant' => 'compact']); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php partial('cta-band', [
    'heading' => 'Ready for a ' . strtolower($service['name']) . '?',
    'text'    => 'Tell us about your condo and we will confirm pricing, availability and the best arrival window for you.',
]); ?>
<?php partial('footer'); ?>
