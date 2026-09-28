<?php
/**
 * Testimonial card.
 *
 * Rating stars are only rendered when a rating actually exists in the data —
 * Maid4Condos publishes review counts per platform, not per-review ratings, so
 * nothing is fabricated here.
 *
 * @var array $testimonial ['name','location','service','quote','rating']
 */
$rating = isset($testimonial['rating']) ? $testimonial['rating'] : null;
$initials = '';
foreach (preg_split('/\s+/', trim((string) $testimonial['name'])) as $part) {
    if ($part !== '') {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    if (strlen($initials) >= 2) {
        break;
    }
}
?>
<figure class="testimonial">
    <span class="testimonial__quote-mark" aria-hidden="true"><?php partial('icon', ['name' => 'quote', 'size' => 26]); ?></span>
    <?php if ($rating) : ?>
        <p class="testimonial__rating" aria-label="<?= (int) $rating ?> out of 5">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <span class="<?= $i <= $rating ? 'is-on' : '' ?>"><?php partial('icon', ['name' => 'star', 'size' => 15]); ?></span>
            <?php endfor; ?>
        </p>
    <?php endif; ?>
    <blockquote class="testimonial__text">
        <p><?= e($testimonial['quote']) ?></p>
    </blockquote>
    <figcaption class="testimonial__author">
        <span class="testimonial__avatar" aria-hidden="true"><?= e($initials) ?></span>
        <span>
            <strong><?= e($testimonial['name']) ?></strong>
            <small>
                <?= e($testimonial['location']) ?>
                <?php if (!empty($testimonial['service'])) : ?>
                    · <?= e($testimonial['service']) ?>
                <?php endif; ?>
            </small>
        </span>
    </figcaption>
</figure>
