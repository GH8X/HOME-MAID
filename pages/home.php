<?php
/**
 * Homepage.
 *
 * Sections: hero → trust → services → why us → how it works → service areas
 * → testimonials → FAQ preview → final call to action.
 */
$biz          = business();
$services     = services_all();
$extras       = extras_all();
$why          = content_block('home.why');
$how          = content_block('home.how');
$trust        = content_block('home.trust');
$areas        = areas_all();
$testimonials = testimonials_all(3);
$faqPreview   = [];
foreach (faqs_all() as $faq) {
    if (in_array($faq['category'], ['About Maid4Condos', 'Booking'], true)) {
        $faqPreview[] = $faq;
    }
    if (count($faqPreview) >= 5) {
        break;
    }
}

partial('header', [
    'head' => [
        'title'       => 'Condo Cleaning Services Toronto | Maid4Condos',
        'description' => 'Professional condo and home cleaning across Toronto and the GTA. Basic, deep and move in/move out packages, flexible scheduling and a 24 hour guarantee. Request your free quote.',
        'canonical'   => '/',
        'og_image'    => 'assets/images/cleaned-living-room.jpg',
        'preload_image' => 'assets/images/hero-cleaning-modern-home.jpg',
        'schema'      => [
            schema_faq_page($faqPreview),
        ],
    ],
    'body_class' => 'page-home',
]);

partial('hero');
partial('trust-strip');
?>

<section class="section" id="services" aria-labelledby="services-title">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?php partial('icon', ['name' => 'spark', 'size' => 15]); ?> Cleaning packages</p>
                <h2 class="section-head__title" id="services-title">A package for every kind of clean</h2>
            </div>
            <p class="section-head__text">
                New to outsourcing? Start with the package that matches your condo right now, then let AutoPilot keep it that way.
                Every clean follows a written checklist — and we check it twice.
            </p>
        </div>

        <div class="service-grid">
            <?php foreach ($services as $service) : ?>
                <?php partial('service-card', ['service' => $service, 'heading_level' => 'h3']); ?>
            <?php endforeach; ?>
        </div>

        <div class="extras-strip">
            <div class="extras-strip__intro">
                <h3>Add-ons for the finishing touch</h3>
                <p>Layer any of these onto your cleaning when you need a little more.</p>
            </div>
            <ul class="extras-strip__list">
                <?php foreach ($extras as $extra) : ?>
                    <li>
                        <span class="extras-strip__icon"><?php partial('icon', ['name' => 'spray', 'size' => 18]); ?></span>
                        <strong><?= e($extra['name']) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="link-arrow" href="<?= e(url('services')) ?>#extras">See what is included <?php partial('icon', ['name' => 'arrow-right', 'size' => 15]); ?></a>
        </div>
    </div>
</section>

<section class="section section--ink" id="why-us" aria-labelledby="why-title">
    <div class="why-band" aria-hidden="true"><span class="bubble-field"></span></div>
    <div class="container">
        <div class="split">
            <div class="split__media">
                <div class="arch-frame">
                    <?= m4c_image('cleaning-kitchen-stove.jpg', 'A cleaner disinfecting a stovetop and kitchen counter with a spray and cloth', [
                        'class'  => 'arch-frame__image',
                        'width'  => 1200,
                        'height' => 1500,
                        'sizes'  => '(min-width: 1024px) 42vw, 92vw',
                    ]) ?>
                </div>
                <div class="arch-frame__badge">
                    <strong><?= e($biz['founded']) ?></strong>
                    <span>Family run in Toronto</span>
                </div>
            </div>

            <div class="split__copy">
                <p class="eyebrow eyebrow--light"><?php partial('icon', ['name' => 'team', 'size' => 15]); ?> Why Maid4Condos</p>
                <h2 id="why-title"><?= e($why['heading']) ?></h2>
                <p><?= e($why['intro']) ?></p>
                <ul class="promise-list">
                    <?php foreach ($why['points'] as $point) : ?>
                        <li><?php partial('icon', ['name' => 'check-circle', 'size' => 19]); ?> <span><?= e($point) ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <div class="split__actions">
                    <a class="btn btn--primary" href="<?= e(url('about')) ?>">Our story &amp; promise <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?></a>
                    <a class="btn btn--outline-light" href="<?= e(url('services')) ?>">Compare packages</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--cream" id="how-it-works" aria-labelledby="how-title">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'list', 'size' => 15]); ?> Simple from the start</p>
            <h2 class="section-head__title" id="how-title"><?= e($how['heading']) ?></h2>
            <p class="section-head__text"><?= e($how['intro']) ?></p>
        </div>

        <ol class="steps">
            <?php foreach ($how['steps'] as $index => $step) : ?>
                <li class="step">
                    <span class="step__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3 class="step__title"><?= e($step['title']) ?></h3>
                    <p><?= e($step['text']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>

        <div class="steps__cta">
            <a class="btn btn--primary btn--lg" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="How it works">
                Start my quote <?php partial('icon', ['name' => 'arrow-right', 'size' => 18]); ?>
            </a>
            <p>No obligation, and no account required.</p>
        </div>
    </div>
</section>

<section class="section" id="areas" aria-labelledby="areas-title">
    <div class="container">
        <div class="split split--reverse">
            <div class="split__copy">
                <p class="eyebrow"><?php partial('icon', ['name' => 'map', 'size' => 15]); ?> Service areas</p>
                <h2 id="areas-title">Cleaning across Toronto &amp; the GTA</h2>
                <p><?= e(content_block('home.areas_intro')) ?></p>
                <ul class="area-chips">
                    <?php foreach ($areas as $area) : ?>
                        <li>
                            <span class="area-chips__name"><?= e($area['name']) ?></span>
                            <small><?= e($area['note']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="split__actions">
                    <a class="btn btn--primary" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Service areas">
                        Check my area <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?>
                    </a>
                </div>
            </div>
            <div class="split__media">
                <div class="map-card">
                    <div class="map-card__head">
                        <?php partial('icon', ['name' => 'building', 'size' => 18]); ?>
                        <span>Based in <?= e($biz['city']) ?></span>
                    </div>
                    <p class="map-card__address"><?= e($biz['street']) ?><br><?= e($biz['city']) ?>, <?= e($biz['region']) ?> <?= e($biz['postal']) ?></p>
                    <?php if (map_embed_url()) : ?>
                        <iframe
                            class="map-card__frame"
                            src="<?= e(map_embed_url()) ?>"
                            title="Map of the Maid4Condos office in Toronto"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen></iframe>
                    <?php endif; ?>
                    <div class="map-card__foot">
                        <a class="link-arrow" href="<?= e(url('contact')) ?>">Directions &amp; contact details <?php partial('icon', ['name' => 'arrow-right', 'size' => 15]); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--shell" id="testimonials" aria-labelledby="testimonials-title">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?php partial('icon', ['name' => 'star', 'size' => 15]); ?> In their words</p>
                <h2 class="section-head__title" id="testimonials-title">Toronto clients on the Maid4Condos difference</h2>
            </div>
            <a class="link-arrow" href="<?= e(url('testimonials')) ?>">Read all reviews <?php partial('icon', ['name' => 'arrow-right', 'size' => 15]); ?></a>
        </div>
        <div class="testimonial-grid">
            <?php foreach ($testimonials as $testimonial) : ?>
                <?php partial('testimonial-card', ['testimonial' => $testimonial]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="faq-preview" aria-labelledby="faq-title">
    <div class="container">
        <div class="split split--narrow">
            <div class="split__copy">
                <p class="eyebrow"><?php partial('icon', ['name' => 'help', 'size' => 15]); ?> Good to know</p>
                <h2 id="faq-title">Questions we hear most</h2>
                <p>Can’t see your question? Our full FAQ covers booking, access, products, payment and everything we do not do.</p>
                <a class="btn btn--outline" href="<?= e(url('faq')) ?>">Browse the full FAQ <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?></a>
            </div>
            <div class="split__media split__media--wide">
                <?php partial('faq-list', ['faqs' => $faqPreview, 'context' => 'home-faq']); ?>
            </div>
        </div>
    </div>
</section>

<?php partial('cta-band'); ?>
<?php partial('footer'); ?>
