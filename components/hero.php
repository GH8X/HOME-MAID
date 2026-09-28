<?php
/**
 * Homepage hero. Pulls its copy from the editable "home.hero" content block.
 */
$hero = content_block('home.hero');
$biz  = business();
?>
<section class="hero" aria-labelledby="hero-title">
    <div class="hero__bg" aria-hidden="true">
        <span class="bubble-field"></span>
    </div>
    <div class="container hero__inner">
        <div class="hero__copy">
            <p class="eyebrow eyebrow--light">
                <?php partial('icon', ['name' => 'pin', 'size' => 15]); ?>
                <?= e($hero['eyebrow']) ?>
            </p>
            <h1 class="hero__title" id="hero-title"><?= e($hero['title']) ?></h1>
            <p class="hero__subtitle"><?= e($hero['subtitle']) ?></p>

            <div class="hero__actions">
                <a class="btn btn--primary btn--lg" href="<?= e(url($hero['primary_cta']['route'])) ?>"
                   data-ga-event="quote_cta_click" data-ga-label="Hero primary">
                    <?= e($hero['primary_cta']['label']) ?>
                    <?php partial('icon', ['name' => 'arrow-right', 'size' => 18]); ?>
                </a>
                <a class="btn btn--outline-light btn--lg" href="<?= e(url($hero['secondary_cta']['route'])) ?>">
                    <?= e($hero['secondary_cta']['label']) ?>
                </a>
            </div>

            <ul class="hero__bullets">
                <?php foreach ($hero['bullets'] as $bullet) : ?>
                    <li>
                        <?php partial('icon', ['name' => 'check-circle', 'size' => 18]); ?>
                        <span><?= e($bullet) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="hero__media">
            <div class="hero__arch">
                <?= m4c_media_image(
                    $hero['image'],
                    'A professional cleaner vacuuming the living room floor of a bright modern home',
                    [
                        'class'         => 'hero__image',
                        'width'         => 1600,
                        'height'        => 1067,
                        'sizes'         => '(min-width: 1100px) 46vw, 92vw',
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                    ]
                ) ?>
            </div>

            <div class="hero__card hero__card--guarantee">
                <span class="hero__card-icon"><?php partial('icon', ['name' => 'badge', 'size' => 20]); ?></span>
                <div>
                    <strong>24 hour guarantee</strong>
                    <small>We come back if it is not right</small>
                </div>
            </div>

            <div class="hero__card hero__card--price">
                <small>Packages from</small>
                <strong>$119.99</strong>
                <span>+ up to 20% off recurring visits</span>
            </div>
        </div>
    </div>

    <div class="container">
        <ul class="hero__stats">
            <li>
                <strong><?= e($biz['reviews']['google'] ?? '77') ?></strong>
                <span>Google reviews</span>
            </li>
            <li>
                <strong><?= e($biz['founded']) ?></strong>
                <span>Family run since</span>
            </li>
            <li>
                <strong>$<?= e($biz['liability']) ?></strong>
                <span>Liability coverage</span>
            </li>
            <li>
                <strong>Toronto</strong>
                <span>&amp; the GTA served</span>
            </li>
        </ul>
    </div>
</section>
