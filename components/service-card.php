<?php
/**
 * Service card.
 *
 * @var array  $service Service array from services_all().
 * @var string $variant 'default' | 'feature' | 'compact'
 * @var string $heading_level Heading tag to use for the card title.
 */
$variant      = isset($variant) ? $variant : 'default';
$headingLevel = isset($heading_level) ? $heading_level : 'h3';
$headingLevel = in_array($headingLevel, ['h2', 'h3', 'h4'], true) ? $headingLevel : 'h3';
$link         = url('services/' . $service['slug']);
?>
<article class="service-card service-card--<?= e($variant) ?>">
    <a class="service-card__media" href="<?= e($link) ?>" tabindex="-1" aria-hidden="true">
        <?= m4c_media_image(
            $service['hero_image'],
            $service['name'] . ' cleaning service in Toronto',
            [
                'class'   => 'service-card__image',
                'width'   => 900,
                'height'  => 600,
                'sizes'   => '(min-width: 1024px) 30vw, (min-width: 640px) 45vw, 92vw',
                'loading' => 'lazy',
            ]
        ) ?>
        <span class="service-card__eyebrow"><?= e($service['eyebrow']) ?></span>
    </a>
    <div class="service-card__body">
        <<?= $headingLevel ?> class="service-card__title">
            <a href="<?= e($link) ?>"><?= e($service['name']) ?></a>
        </<?= $headingLevel ?>>
        <p class="service-card__text"><?= e($service['summary']) ?></p>

        <?php if (!empty($service['benefits'])) : ?>
            <ul class="service-card__list">
                <?php foreach (array_slice($service['benefits'], 0, 3) as $benefit) : ?>
                    <li><?php partial('icon', ['name' => 'check', 'size' => 16]); ?> <span><?= e($benefit) ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="service-card__foot">
            <?php if (!empty($service['price_from'])) : ?>
                <p class="service-card__price">
                    <small>From</small>
                    <strong><?= e(money($service['price_from'])) ?></strong>
                </p>
            <?php else : ?>
                <p class="service-card__price">
                    <small>Save up to</small>
                    <strong>20%</strong>
                </p>
            <?php endif; ?>
            <a class="service-card__link" href="<?= e($link) ?>">
                Learn more <?php partial('icon', ['name' => 'arrow-right', 'size' => 16]); ?>
            </a>
        </div>
    </div>
</article>
