<?php
/**
 * /services — every cleaning package, the AutoPilot frequency guide and the
 * optional add-ons.
 */
$services    = services_all();
$extras      = extras_all();
$frequencies = frequencies_all();
$biz         = business();

partial('header', [
    'head' => [
        'title'       => 'Cleaning Services Toronto — Packages & Add-ons | Maid4Condos',
        'description' => 'Compare every Maid4Condos cleaning package: Basic, Basic Plus, Deep, Deep Plus, Move In/Move Out and recurring AutoPilot schedules across Toronto and the GTA.',
        'canonical'   => '/services',
        'og_image'    => 'assets/images/cleaned-living-room.jpg',
        'schema'      => [
            schema_breadcrumbs(['Home' => '', 'Cleaning services' => 'services']),
            [
                '@context' => 'https://schema.org',
                '@type'    => 'ItemList',
                'name'     => 'Maid4Condos cleaning packages',
                'itemListElement' => array_map(function ($service, $index) {
                    return [
                        '@type'    => 'ListItem',
                        'position' => $index + 1,
                        'name'     => $service['name'],
                        'url'      => absolute_url('services/' . $service['slug']),
                    ];
                }, $services, array_keys($services)),
            ],
        ],
    ],
    'body_class' => 'page-services',
]);

partial('page-hero', [
    'eyebrow'  => 'Cleaning packages',
    'title'    => 'Cleaning services built for condo living',
    'intro'    => 'Six ways to work with Maid4Condos — from a regular tidy-up to a full reset of an empty unit. Every package follows a written checklist, and every visit is backed by our 24 hour guarantee.',
    'crumbs'   => ['Home' => '', 'Cleaning services' => 'services'],
    'image'    => 'cleaning-kitchen-cabinets',
    'image_alt' => 'A cleaner wiping down kitchen cabinets in a bright Toronto condo',
    'actions'  => [
        ['label' => 'Get a quote', 'route' => 'get-a-quote', 'variant' => 'primary', 'ga' => 'quote_cta_click'],
        ['label' => 'Talk to us', 'route' => 'contact', 'variant' => 'outline-light'],
    ],
    'facts'    => [
        'Packages from $119.99',
        'Up to 20% off recurring visits',
        'Bonded, insured and background checked staff',
        'Serving Toronto and the GTA',
    ],
]);
?>

<section class="section" aria-labelledby="packages-title">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'list', 'size' => 15]); ?> Choose your clean</p>
            <h2 class="section-head__title" id="packages-title">Every package, side by side</h2>
            <p class="section-head__text">Not sure which one fits? Tell us about your condo and we will recommend the right package — no obligation.</p>
        </div>

        <div class="service-grid service-grid--wide">
            <?php foreach ($services as $service) : ?>
                <?php partial('service-card', ['service' => $service, 'heading_level' => 'h3', 'variant' => 'feature']); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--cream" id="frequencies" aria-labelledby="frequencies-title">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'calendar', 'size' => 15]); ?> AutoPilot schedules</p>
            <h2 class="section-head__title" id="frequencies-title">How often should your home be cleaned?</h2>
            <p class="section-head__text">
                How often you have Maid4Condos clean depends on your lifestyle, needs and schedule. Households that are cleaned more often pay less per visit.
            </p>
        </div>

        <div class="frequency-grid">
            <?php foreach ($frequencies as $frequency) : ?>
                <article class="frequency-card <?= !empty($frequency['recommended']) ? 'frequency-card--recommended' : '' ?>">
                    <?php if (!empty($frequency['recommended'])) : ?>
                        <span class="frequency-card__flag">Recommended</span>
                    <?php endif; ?>
                    <h3 class="frequency-card__title"><?= e($frequency['label']) ?></h3>
                    <p class="frequency-card__discount"><?= e($frequency['discount']) ?></p>
                    <p class="frequency-card__note"><?= e($frequency['note']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="callout">
            <span class="callout__icon"><?php partial('icon', ['name' => 'leaf', 'size' => 22]); ?></span>
            <div>
                <h3>A healthier home, for less</h3>
                <p>Weekly and bi-weekly visits are not only more cost effective, they are healthier: a routine clean reduces allergens and bacteria. With an AutoPilot schedule, Maid4Condos brings the supplies you need so you do not have to buy them.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="extras" aria-labelledby="extras-title">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?php partial('icon', ['name' => 'spray', 'size' => 15]); ?> Add-ons</p>
                <h2 class="section-head__title" id="extras-title">Service package extras</h2>
            </div>
            <p class="section-head__text">Add any of these to your cleaning when you need a little extra. Availability depends on your package — we will confirm when we quote.</p>
        </div>

        <div class="extra-grid">
            <?php foreach ($extras as $extra) : ?>
                <article class="extra-card">
                    <span class="extra-card__icon"><?php partial('icon', ['name' => 'spray', 'size' => 20]); ?></span>
                    <h3><?= e($extra['name']) ?></h3>
                    <p><?= e($extra['summary']) ?></p>
                    <details class="extra-card__more">
                        <summary>Details <?php partial('icon', ['name' => 'chevron', 'size' => 15]); ?></summary>
                        <p><?= e($extra['details']) ?></p>
                    </details>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--shell" aria-labelledby="not-included-title">
    <div class="container">
        <div class="split split--narrow">
            <div class="split__copy">
                <p class="eyebrow"><?php partial('icon', ['name' => 'shield', 'size' => 15]); ?> Clear expectations</p>
                <h2 id="not-included-title">What is not included</h2>
                <p>So there are no surprises on the day, here is what falls outside a Maid4Condos cleaning. If you are unsure whether something is covered, just ask.</p>
                <a class="btn btn--outline" href="<?= e(url('faq')) ?>">Read the full FAQ <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?></a>
            </div>
            <div class="split__media split__media--wide">
                <ul class="exclusion-list">
                    <?php foreach (content_block('about')['not_included'] as $item) : ?>
                        <li><span aria-hidden="true"><?php partial('icon', ['name' => 'close', 'size' => 15]); ?></span> <span><?= e($item) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php partial('cta-band', [
    'heading' => 'Not sure which package you need?',
    'text'    => 'Send us your condo details and we will recommend the right clean — with a clear price and no obligation.',
]); ?>
<?php partial('footer'); ?>
