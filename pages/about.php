<?php
$about = content_block('about');
$biz   = business();

partial('header', [
    'head' => [
        'title'       => 'About Maid4Condos — Toronto Cleaning Company Since 2014',
        'description' => 'Maid4Condos is a family run Toronto cleaning company founded in 2014. Meet the team behind our meticulous condo cleanings, insurance coverage and 24 hour guarantee.',
        'canonical'   => '/about',
        'og_image'    => 'assets/images/cleaning-vacuum-kitchen.jpg',
        'schema'      => [schema_breadcrumbs(['Home' => '', 'About us' => 'about'])],
    ],
    'body_class' => 'page-about',
]);

partial('page-hero', [
    'eyebrow'   => 'About us',
    'title'     => $about['heading'],
    'intro'     => $about['intro'],
    'crumbs'    => ['Home' => '', 'About us' => 'about'],
    'image'     => 'cleaning-vacuum-kitchen',
    'image_alt' => 'A Maid4Condos cleaner vacuuming a bright Toronto condo kitchen',
    'actions'   => [
        ['label' => 'Get a quote', 'route' => 'get-a-quote', 'variant' => 'primary', 'ga' => 'quote_cta_click'],
        ['label' => 'See our services', 'route' => 'services', 'variant' => 'outline-light'],
    ],
    'facts'     => [
        'Family run since ' . $biz['founded'],
        'Members of ISSA and ARCSI',
        '$' . $biz['liability'] . ' liability coverage',
        'All staff are employees, not contractors',
    ],
]);
?>

<section class="section" aria-labelledby="story-title">
    <div class="container">
        <div class="split">
            <div class="split__copy">
                <p class="eyebrow"><?php partial('icon', ['name' => 'sparkles', 'size' => 15]); ?> Our story</p>
                <h2 id="story-title">Started because we were not satisfied either</h2>
                <?php foreach ($about['story'] as $paragraph) : ?>
                    <p class="lead"><?= e($paragraph) ?></p>
                <?php endforeach; ?>
            </div>
            <div class="split__media">
                <div class="stacked-media">
                    <div class="arch-frame arch-frame--sm">
                        <?= m4c_image('cleaned-living-room.jpg', 'A bright, freshly cleaned Toronto condo living room', [
                            'class'  => 'arch-frame__image',
                            'width'  => 1200,
                            'height' => 800,
                            'sizes'  => '(min-width: 1024px) 40vw, 92vw',
                        ]) ?>
                    </div>
                    <div class="arch-frame arch-frame--sm arch-frame--offset">
                        <?= m4c_image('clean-kitchen.jpg', 'A spotless white kitchen with stainless steel appliances', [
                            'class'  => 'arch-frame__image',
                            'width'  => 900,
                            'height' => 600,
                            'sizes'  => '(min-width: 1024px) 30vw, 60vw',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--ink" aria-labelledby="promise-title">
    <div class="why-band" aria-hidden="true"><span class="bubble-field"></span></div>
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow eyebrow--light"><?php partial('icon', ['name' => 'badge', 'size' => 15]); ?> Our promise to you</p>
            <h2 class="section-head__title" id="promise-title">What you can count on, every visit</h2>
            <p class="section-head__text">At the end of the day we are not just cleaning — we are taking care of your biggest asset. Your home.</p>
        </div>
        <ul class="promise-grid">
            <?php foreach ($about['promise'] as $item) : ?>
                <li><?php partial('icon', ['name' => 'check-circle', 'size' => 19]); ?> <span><?= e($item) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="section" aria-labelledby="credentials-title">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'shield', 'size' => 15]); ?> Credentials</p>
            <h2 class="section-head__title" id="credentials-title">Trained, insured and accountable</h2>
        </div>
        <div class="credential-grid">
            <article class="credential-card">
                <span class="credential-card__icon"><?php partial('icon', ['name' => 'badge', 'size' => 22]); ?></span>
                <h3>Industry memberships</h3>
                <p><?= e($about['memberships'][0]) ?></p>
            </article>
            <article class="credential-card">
                <span class="credential-card__icon"><?php partial('icon', ['name' => 'users', 'size' => 22]); ?></span>
                <h3>Real employees</h3>
                <p><?= e($about['memberships'][1]) ?></p>
            </article>
            <article class="credential-card">
                <span class="credential-card__icon"><?php partial('icon', ['name' => 'shield', 'size' => 22]); ?></span>
                <h3>The 24 hour guarantee</h3>
                <p><?= e($biz['guarantee']) ?></p>
            </article>
            <article class="credential-card">
                <span class="credential-card__icon"><?php partial('icon', ['name' => 'leaf', 'size' => 22]); ?></span>
                <h3>Products we trust</h3>
                <p>We use Procter &amp; Gamble, Vileda and other leading professional grade brands, chosen with the environment and efficiency in mind. Our products are biodegradable and safe on the environment.</p>
            </article>
        </div>
    </div>
</section>

<section class="section section--shell" aria-labelledby="about-voices">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow"><?php partial('icon', ['name' => 'star', 'size' => 15]); ?> Client voices</p>
                <h2 class="section-head__title" id="about-voices">What our clients say</h2>
            </div>
            <a class="link-arrow" href="<?= e(url('testimonials')) ?>">All reviews <?php partial('icon', ['name' => 'arrow-right', 'size' => 15]); ?></a>
        </div>
        <div class="testimonial-grid">
            <?php foreach (testimonials_all(3) as $testimonial) : ?>
                <?php partial('testimonial-card', ['testimonial' => $testimonial]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php partial('cta-band', [
    'heading' => 'Let’s look after your home',
    'text'    => 'Whether you need a regular hand or a one-time reset, we would love to hear about your condo.',
    'secondary_label' => 'Ask a question',
]); ?>
<?php partial('footer'); ?>
