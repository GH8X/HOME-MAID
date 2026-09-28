<?php
/**
 * Inner page hero (services, about, FAQ, testimonials, contact, legal…).
 *
 * @var string $eyebrow
 * @var string $title
 * @var string $intro
 * @var array  $crumbs     Passed to the breadcrumbs partial.
 * @var string $image      Optional image file name inside assets/images.
 * @var string $image_alt
 * @var array  $actions    Optional [['label','route','variant']] list.
 * @var array  $facts      Optional list of short facts shown under the intro.
 */
$eyebrow  = isset($eyebrow) ? $eyebrow : '';
$title    = isset($title) ? $title : '';
$intro    = isset($intro) ? $intro : '';
$crumbs   = isset($crumbs) ? $crumbs : [];
$image    = isset($image) ? $image : '';
$imageAlt = isset($image_alt) ? $image_alt : '';
$actions  = isset($actions) ? $actions : [];
$facts    = isset($facts) ? $facts : [];
$style    = $image ? 'page-hero--with-image' : '';
?>
<section class="page-hero <?= e($style) ?>">
    <div class="page-hero__bg" aria-hidden="true"><span class="bubble-field"></span></div>
    <div class="container">
        <?php if ($crumbs) { partial('breadcrumbs', ['crumbs' => $crumbs]); } ?>
        <div class="page-hero__grid">
            <div class="page-hero__copy">
                <?php if ($eyebrow) : ?>
                    <p class="eyebrow eyebrow--light"><?php partial('icon', ['name' => 'sparkles', 'size' => 15]); ?> <?= e($eyebrow) ?></p>
                <?php endif; ?>
                <h1><?= e($title) ?></h1>
                <?php if ($intro) : ?>
                    <p class="page-hero__intro"><?= e($intro) ?></p>
                <?php endif; ?>

                <?php if ($actions) : ?>
                    <div class="page-hero__actions">
                        <?php foreach ($actions as $action) : ?>
                            <a class="btn btn--<?= e($action['variant'] ?? 'primary') ?> btn--lg" href="<?= e(url($action['route'])) ?>"
                               <?= !empty($action['ga']) ? 'data-ga-event="' . e($action['ga']) . '" data-ga-label="' . e($action['label']) . '"' : '' ?>>
                                <?= e($action['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($facts) : ?>
                    <ul class="page-hero__facts">
                        <?php foreach ($facts as $fact) : ?>
                            <li><?php partial('icon', ['name' => 'check-circle', 'size' => 17]); ?> <span><?= e($fact) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php if ($image) : ?>
                <div class="page-hero__media">
                    <?= m4c_media_image($image, $imageAlt ?: $title, [
                        'class'         => 'page-hero__image',
                        'width'         => 1200,
                        'height'        => 800,
                        'sizes'         => '(min-width: 1024px) 42vw, 92vw',
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
