<?php
$testimonials = testimonials_all();
$biz          = business();

partial('header', [
    'head' => [
        'title'       => 'Client Reviews & Testimonials | Maid4Condos Toronto',
        'description' => 'Read real client reviews of Maid4Condos condo cleaning across Toronto and the GTA, plus where to find our reviews on Google, Yelp and maid4condos.com.',
        'canonical'   => '/testimonials',
        'og_image'    => 'assets/images/cleaned-living-room.jpg',
        'schema'      => [schema_breadcrumbs(['Home' => '', 'Testimonials' => 'testimonials'])],
    ],
    'body_class' => 'page-testimonials',
]);

partial('page-hero', [
    'eyebrow'  => 'Client reviews',
    'title'    => 'What Toronto clients say about Maid4Condos',
    'intro'    => 'We are proud of the relationships we build. These are reviews shared by Maid4Condos clients — nothing here is written by us, and nothing has been invented.',
    'crumbs'   => ['Home' => '', 'Testimonials' => 'testimonials'],
    'image'    => 'cleaning-vacuum-rug',
    'image_alt' => 'A cleaner vacuuming a rug in a bright living room',
    'facts'    => array_filter([
        !empty($biz['reviews']['site']) ? $biz['reviews']['site'] . ' reviews on maid4condos.com' : '',
        !empty($biz['reviews']['google']) ? $biz['reviews']['google'] . ' reviews on Google' : '',
        !empty($biz['reviews']['yelp']) ? $biz['reviews']['yelp'] . ' reviews on Yelp' : '',
    ]),
]);
?>

<section class="section" aria-labelledby="reviews-title">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'quote', 'size' => 15]); ?> In their words</p>
            <h2 class="section-head__title" id="reviews-title"><?= count($testimonials) ?> client reviews</h2>
            <p class="section-head__text">Your opinion means everything to us. Incorporating client feedback is part of our policy, and it shapes how we train and improve.</p>
        </div>

        <div class="testimonial-grid testimonial-grid--masonry">
            <?php foreach ($testimonials as $testimonial) : ?>
                <?php partial('testimonial-card', ['testimonial' => $testimonial]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--shell" aria-labelledby="review-platforms">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'external', 'size' => 15]); ?> Independent platforms</p>
            <h2 class="section-head__title" id="review-platforms">Find us on the review sites you trust</h2>
        </div>
        <div class="platform-grid">
            <?php
            $platforms = [
                ['google', 'Google', $biz['reviews']['google'] ?? '', 'Google Business Profile', 'https://www.google.com/search?q=Maid4Condos+Toronto+reviews'],
                ['mail', 'maid4condos.com', $biz['reviews']['site'] ?? '', 'Reviews left directly on our site', url('contact')],
                ['star', 'Yelp', $biz['reviews']['yelp'] ?? '', 'Yelp business page', 'https://www.yelp.ca/search?find_desc=Maid4Condos&find_loc=Toronto%2C+ON'],
            ];
            foreach ($platforms as $platform) : ?>
                <a class="platform-card" href="<?= e($platform[4]) ?>" target="_blank" rel="noopener">
                    <span class="platform-card__icon"><?php partial('icon', ['name' => $platform[0], 'size' => 22]); ?></span>
                    <strong><?= e($platform[1]) ?></strong>
                    <?php if ($platform[2] !== '') : ?>
                        <span class="platform-card__count"><?= e($platform[2]) ?> reviews</span>
                    <?php endif; ?>
                    <small><?= e($platform[3]) ?></small>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="help-band help-band--stacked">
            <div>
                <h3>Recently cleaned with us?</h3>
                <p>We love getting customer feedback. After every cleaning you will receive a feedback tool — or send it to us directly and we will incorporate it into our ever-evolving approach.</p>
            </div>
            <div class="help-band__actions">
                <a class="btn btn--primary" href="mailto:<?= e($biz['email']) ?>?subject=My%20Maid4Condos%20review" data-ga-event="email_click" data-ga-label="Testimonials review">
                    <?php partial('icon', ['name' => 'mail', 'size' => 17]); ?> Share your feedback
                </a>
                <a class="btn btn--outline" href="<?= e(url('contact')) ?>">Contact the office</a>
            </div>
        </div>
    </div>
</section>

<?php partial('cta-band', [
    'heading' => 'Join them',
    'text'    => 'See the Maid4Condos difference for yourself. Request a quote and we will take it from there.',
]); ?>
<?php partial('footer'); ?>
