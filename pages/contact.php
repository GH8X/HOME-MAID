<?php
$biz   = business();
$state = m4c_handle_contact();
$sent  = isset($_GET['sent']) && $_GET['sent'] === '1';
$areas = areas_all();

partial('header', [
    'head' => [
        'title'       => 'Contact Maid4Condos | Toronto Cleaning Service',
        'description' => 'Call ' . $biz['phone_display'] . ', email ' . $biz['email'] . ' or send us a message. Maid4Condos is based in Liberty Village, Toronto and serves the GTA.',
        'canonical'   => '/contact',
        'robots'      => $sent ? 'noindex,follow' : setting('robots_policy', 'index,follow'),
        'og_image'    => 'assets/images/clean-bathroom.jpg',
        'schema'      => [schema_breadcrumbs(['Home' => '', 'Contact' => 'contact'])],
    ],
    'body_class' => 'page-contact',
]);

partial('page-hero', [
    'eyebrow'  => 'Contact us',
    'title'    => 'Talk to the Maid4Condos team',
    'intro'    => 'We are a local Toronto cleaning company based in Liberty Village, in the heart of the city. Call, email or send us a message — we would love to hear about your home.',
    'crumbs'   => ['Home' => '', 'Contact' => 'contact'],
    'facts'    => [
        'Phone answered Monday to Friday',
        'Reply within one business day',
        'Serving Toronto and the GTA',
    ],
]);
?>

<section class="section" aria-labelledby="contact-methods">
    <div class="container">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'phone', 'size' => 15]); ?> How to reach us</p>
            <h2 class="section-head__title" id="contact-methods">Every way to get in touch</h2>
        </div>

        <div class="contact-grid">
            <a class="contact-card" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Contact card">
                <span class="contact-card__icon"><?php partial('icon', ['name' => 'phone', 'size' => 22]); ?></span>
                <h3>Call us</h3>
                <p class="contact-card__value"><?= e($biz['phone_display']) ?></p>
                <small>Fastest way to book a cleaning</small>
            </a>
            <a class="contact-card" href="mailto:<?= e($biz['email']) ?>" data-ga-event="email_click" data-ga-label="Contact card">
                <span class="contact-card__icon"><?php partial('icon', ['name' => 'mail', 'size' => 22]); ?></span>
                <h3>General enquiries</h3>
                <p class="contact-card__value"><?= e($biz['email']) ?></p>
                <small>Questions, feedback and partnerships</small>
            </a>
            <a class="contact-card" href="mailto:<?= e($biz['booking_email']) ?>" data-ga-event="email_click" data-ga-label="Contact card bookings">
                <span class="contact-card__icon"><?php partial('icon', ['name' => 'calendar', 'size' => 22]); ?></span>
                <h3>Bookings &amp; support</h3>
                <p class="contact-card__value"><?= e($biz['booking_email']) ?></p>
                <small>Scheduling, changes and special instructions</small>
            </a>
            <div class="contact-card contact-card--static">
                <span class="contact-card__icon"><?php partial('icon', ['name' => 'pin', 'size' => 22]); ?></span>
                <h3>Corporate office</h3>
                <address class="contact-card__value">
                    <?= e($biz['street']) ?><br>
                    <?= e($biz['city']) ?>, <?= e($biz['region']) ?> <?= e($biz['postal']) ?><br>
                    <?= e($biz['country']) ?>
                </address>
                <small>Based in Liberty Village</small>
            </div>
        </div>

        <div class="hours-band">
            <div class="hours-band__block">
                <h3><?php partial('icon', ['name' => 'clock', 'size' => 18]); ?> Office hours</h3>
                <?php foreach (preg_split('/\r\n|\r|\n/', (string) $biz['office_hours']) as $line) : ?>
                    <?php if (trim($line) !== '') : ?>
                        <p><?= e(trim($line)) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="hours-band__block">
                <h3><?php partial('icon', ['name' => 'calendar', 'size' => 18]); ?> Service window</h3>
                <p><?= e($biz['service_hours']) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section section--shell" id="contact-form" aria-labelledby="contact-form-title">
    <div class="container">
        <div class="split split--form">
            <div class="split__copy">
                <p class="eyebrow"><?php partial('icon', ['name' => 'mail', 'size' => 15]); ?> Send a message</p>
                <h2 id="contact-form-title">Tell us how we can help</h2>
                <?php if ($sent) : ?>
                    <div class="alert alert--success" role="status" tabindex="-1">
                        <strong>Message received — thank you.</strong>
                        <p><?= e(setting('contact_success_text', 'Thanks for getting in touch. Our team will reply to your message shortly.')) ?></p>
                    </div>
                <?php else : ?>
                    <p>Use the form and we will get back to you within one business day. If you are ready for pricing, the quote form is the fastest route.</p>
                    <a class="btn btn--outline" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="Contact page">
                        Get a quote instead <?php partial('icon', ['name' => 'arrow-right', 'size' => 17]); ?>
                    </a>
                <?php endif; ?>

                <div class="contact-map">
                    <?php if (map_embed_url()) : ?>
                        <iframe
                            src="<?= e(map_embed_url()) ?>"
                            title="Map showing the Maid4Condos corporate office in Toronto"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen></iframe>
                    <?php endif; ?>
                    <div class="contact-map__foot">
                        <p><?= e($biz['street']) ?>, <?= e($biz['city']) ?>, <?= e($biz['region']) ?> <?= e($biz['postal']) ?></p>
                        <a class="link-arrow" href="https://www.google.com/maps/search/?api=1&amp;query=<?= e(rawurlencode($biz['map_query'])) ?>"
                           target="_blank" rel="noopener" data-ga-event="map_click" data-ga-label="Contact directions">
                            Get directions <?php partial('icon', ['name' => 'arrow-up-right', 'size' => 15]); ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="split__media split__media--form">
                <?php partial('contact-form', ['state' => $state]); ?>
            </div>
        </div>
    </div>
</section>

<section class="section" aria-labelledby="contact-areas">
    <div class="container container--narrow">
        <div class="section-head section-head--center">
            <p class="eyebrow"><?php partial('icon', ['name' => 'map', 'size' => 15]); ?> Where we work</p>
            <h2 class="section-head__title" id="contact-areas">Neighbourhoods we service</h2>
            <p class="section-head__text">We service the entire Toronto region and surrounding GTA. Not listed? Send us your postal code and we will confirm.</p>
        </div>
        <ul class="area-chips area-chips--wide">
            <?php foreach ($areas as $area) : ?>
                <li><span class="area-chips__name"><?= e($area['name']) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php partial('cta-band'); ?>
<?php partial('footer'); ?>
