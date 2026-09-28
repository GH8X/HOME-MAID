<?php
/**
 * Site footer: navigation, services, contact details, service areas, legal bar,
 * the sticky mobile call-to-action and the site scripts.
 */
$biz      = business();
$services = services_all();
$navItems = navigation_items();
$areas    = areas_all();
$year     = date('Y');
?>
</main>

<footer class="site-footer">
    <div class="site-footer__glow" aria-hidden="true"></div>
    <div class="container">
        <div class="footer-cta">
            <div>
                <p class="footer-cta__eyebrow">Time is precious</p>
                <h2 class="footer-cta__title">Ready to hand the cleaning over?</h2>
                <p class="footer-cta__text">Tell us about your condo and we will send a clear, no-obligation quote — usually within one business day.</p>
            </div>
            <div class="footer-cta__actions">
                <a class="btn btn--primary btn--lg" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Footer">
                    Get a quote <?php partial('icon', ['name' => 'arrow-right', 'size' => 18]); ?>
                </a>
                <a class="btn btn--ghost btn--lg" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Footer button">
                    <?php partial('icon', ['name' => 'phone', 'size' => 17]); ?> <?= e($biz['phone_display']) ?>
                </a>
            </div>
        </div>

        <div class="footer-grid">
            <div class="footer-col footer-col--brand">
                <a class="brand brand--footer" href="<?= e(url()) ?>">
                    <img class="brand__mark" src="<?= e(asset('images/logo.svg')) ?>" width="40" height="40" alt="" aria-hidden="true" loading="lazy" decoding="async">
                    <span class="brand__text"><span class="brand__name">Maid<span>4</span>Condos</span></span>
                </a>
                <p class="footer-col__text"><?= e($biz['description']) ?></p>
                <ul class="footer-badges">
                    <li><?php partial('icon', ['name' => 'shield', 'size' => 15]); ?> Bonded &amp; insured</li>
                    <li><?php partial('icon', ['name' => 'badge', 'size' => 15]); ?> 24 hour guarantee</li>
                    <li><?php partial('icon', ['name' => 'users', 'size' => 15]); ?> Employee cleaners</li>
                </ul>
                <?php if (!empty($biz['social'])) : ?>
                    <div class="footer-social">
                        <?php foreach ($biz['social'] as $network => $link) : ?>
                            <a href="<?= e($link) ?>" target="_blank" rel="noopener"
                               aria-label="<?= e(ucfirst($network)) ?>">
                                <?php partial('icon', ['name' => $network === 'twitter' ? 'twitter' : $network, 'size' => 18]); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <nav class="footer-col" aria-label="Cleaning services">
                <h3 class="footer-col__title">Services</h3>
                <ul class="footer-links">
                    <?php foreach ($services as $service) : ?>
                        <li><a href="<?= e(url('services/' . $service['slug'])) ?>"><?= e($service['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <nav class="footer-col" aria-label="Company">
                <h3 class="footer-col__title">Company</h3>
                <ul class="footer-links">
                    <?php foreach ($navItems as $item) : ?>
                        <li><a href="<?= e(url($item['route'])) ?>"><?= e($item['label']) ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="<?= e(url('get-a-quote')) ?>">Get a quote</a></li>
                    <li><a href="<?= e(url('privacy-policy')) ?>">Privacy policy</a></li>
                    <li><a href="<?= e(url('terms')) ?>">Terms &amp; conditions</a></li>
                    <li><a href="<?= e(url('image-credits')) ?>">Photography credits</a></li>
                </ul>
            </nav>

            <div class="footer-col">
                <h3 class="footer-col__title">Get in touch</h3>
                <ul class="footer-contact">
                    <li>
                        <?php partial('icon', ['name' => 'phone', 'size' => 16]); ?>
                        <a href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Footer contact"><?= e($biz['phone_display']) ?></a>
                    </li>
                    <li>
                        <?php partial('icon', ['name' => 'mail', 'size' => 16]); ?>
                        <a href="mailto:<?= e($biz['email']) ?>" data-ga-event="email_click" data-ga-label="Footer contact"><?= e($biz['email']) ?></a>
                    </li>
                    <li>
                        <?php partial('icon', ['name' => 'pin', 'size' => 16]); ?>
                        <address>
                            <?= e($biz['street']) ?><br>
                            <?= e($biz['city']) ?>, <?= e($biz['region']) ?> <?= e($biz['postal']) ?>
                        </address>
                    </li>
                    <li>
                        <?php partial('icon', ['name' => 'clock', 'size' => 16]); ?>
                        <span>
                            <?php foreach (preg_split('/\r\n|\r|\n/', (string) $biz['office_hours']) as $line) : ?>
                                <?php if (trim($line) !== '') : ?><span class="footer-hours__line"><?= e(trim($line)) ?></span><?php endif; ?>
                            <?php endforeach; ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-areas">
            <h3 class="footer-areas__title"><?php partial('icon', ['name' => 'map', 'size' => 16]); ?> Cleaning services across Toronto &amp; the GTA</h3>
            <ul class="footer-areas__list">
                <?php foreach ($areas as $area) : ?>
                    <li><?= e($area['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="footer-legal">
            <p>&copy; <?= e($year) ?> <?= e($biz['legal_name']) ?>. All rights reserved.</p>
            <ul>
                <li><a href="<?= e(url('privacy-policy')) ?>">Privacy policy</a></li>
                <li><a href="<?= e(url('terms')) ?>">Terms</a></li>
                <li><a href="<?= e(url('contact')) ?>">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>

<div class="mobile-cta" aria-label="Quick actions">
    <a class="mobile-cta__call" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Mobile sticky bar">
        <?php partial('icon', ['name' => 'phone', 'size' => 18]); ?>
        <span>Call</span>
    </a>
    <a class="mobile-cta__quote" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Mobile sticky bar">
        <?php partial('icon', ['name' => 'sparkles', 'size' => 18]); ?>
        <span>Get a quote</span>
    </a>
</div>

<button class="to-top" type="button" data-to-top aria-label="Back to top" hidden>
    <?php partial('icon', ['name' => 'chevron', 'size' => 20]); ?>
</button>

<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
