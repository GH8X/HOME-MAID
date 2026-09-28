<?php
/**
 * Trust / credibility section driven by the editable "home.trust" block.
 */
$trust = content_block('home.trust');
$biz   = business();
?>
<section class="section section--shell" id="trust" aria-labelledby="trust-title">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'shield', 'size' => 15]); ?> Peace of mind included</p>
            <h2 class="section-head__title" id="trust-title"><?= e($trust['heading']) ?></h2>
            <p class="section-head__text"><?= e($trust['intro']) ?></p>
        </div>

        <div class="trust-grid">
            <?php foreach ($trust['items'] as $item) : ?>
                <article class="trust-card">
                    <span class="trust-card__icon"><?php partial('icon', ['name' => $item['icon'], 'size' => 22]); ?></span>
                    <h3 class="trust-card__title"><?= e($item['title']) ?></h3>
                    <p><?= e($item['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="review-proof">
            <p class="review-proof__lead">Your opinion means everything to us — incorporating your feedback is part of our policy.</p>
            <ul class="review-proof__list">
                <?php if (!empty($biz['reviews']['site'])) : ?>
                    <li><strong><?= e($biz['reviews']['site']) ?></strong> <span>reviews on maid4condos.com</span></li>
                <?php endif; ?>
                <?php if (!empty($biz['reviews']['google'])) : ?>
                    <li><strong><?= e($biz['reviews']['google']) ?></strong> <span>reviews on Google</span></li>
                <?php endif; ?>
                <?php if (!empty($biz['reviews']['yelp'])) : ?>
                    <li><strong><?= e($biz['reviews']['yelp']) ?></strong> <span>reviews on Yelp</span></li>
                <?php endif; ?>
            </ul>
            <a class="link-arrow" href="<?= e(url('testimonials')) ?>">See what our customers say <?php partial('icon', ['name' => 'arrow-right', 'size' => 15]); ?></a>
        </div>
    </div>
</section>
