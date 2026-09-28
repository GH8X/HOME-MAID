<?php
/**
 * Reusable conversion band used at the foot of every page.
 *
 * @var string $heading
 * @var string $text
 * @var string $primary_label
 * @var string $secondary_label
 * @var bool   $show_phone
 */
$heading       = isset($heading) ? $heading : content_block('home.final')['heading'];
$text          = isset($text) ? $text : content_block('home.final')['text'];
$primaryLabel  = isset($primary_label) ? $primary_label : 'Get a quote';
$secondaryLabel = isset($secondary_label) ? $secondary_label : 'Contact us';
$showPhone     = isset($show_phone) ? $show_phone : true;
$biz           = business();
?>
<section class="cta-band" aria-labelledby="cta-band-title">
    <div class="cta-band__pattern" aria-hidden="true"><span class="bubble-field"></span></div>
    <div class="container cta-band__inner">
        <div class="cta-band__copy">
            <p class="eyebrow eyebrow--light"><?php partial('icon', ['name' => 'sparkles', 'size' => 15]); ?> Free, no-obligation quote</p>
            <h2 id="cta-band-title"><?= e($heading) ?></h2>
            <p><?= e($text) ?></p>
        </div>
        <div class="cta-band__actions">
            <a class="btn btn--primary btn--lg" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="CTA band">
                <?= e($primaryLabel) ?> <?php partial('icon', ['name' => 'arrow-right', 'size' => 18]); ?>
            </a>
            <a class="btn btn--outline-light btn--lg" href="<?= e(url('contact')) ?>">
                <?= e($secondaryLabel) ?>
            </a>
            <?php if ($showPhone) : ?>
                <a class="cta-band__phone" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="CTA band">
                    <?php partial('icon', ['name' => 'phone', 'size' => 17]); ?> <?= e($biz['phone_display']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
