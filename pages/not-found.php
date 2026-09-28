<?php
$services = services_all();

partial('header', [
    'head' => [
        'title'       => 'Page not found | Maid4Condos',
        'description' => 'That page could not be found. Browse our cleaning services or request a quote from Maid4Condos in Toronto.',
        'canonical'   => '/404',
        'robots'      => 'noindex,follow',
        'schema'      => [],
    ],
    'body_class' => 'page-404',
]);
?>

<section class="page-hero page-hero--404">
    <div class="page-hero__bg" aria-hidden="true"><span class="bubble-field"></span></div>
    <div class="container container--narrow">
        <p class="eyebrow eyebrow--light"><?php partial('icon', ['name' => 'bubble', 'size' => 15]); ?> Error 404</p>
        <h1>We could not find that page</h1>
        <p class="page-hero__intro">
            The link may be out of date, or the page may have moved. Let’s get you back to something useful.
        </p>
        <div class="page-hero__actions">
            <a class="btn btn--primary btn--lg" href="<?= e(url()) ?>">Back to the homepage</a>
            <a class="btn btn--outline-light btn--lg" href="<?= e(url('get-a-quote')) ?>" data-ga-event="quote_cta_click" data-ga-label="404 page">
                Get a quote
            </a>
        </div>

        <div class="not-found-links">
            <h2>Popular pages</h2>
            <ul>
                <?php foreach (array_slice($services, 0, 5) as $service) : ?>
                    <li><a href="<?= e(url('services/' . $service['slug'])) ?>"><?= e($service['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?= e(url('faq')) ?>">Frequently asked questions</a></li>
                <li><a href="<?= e(url('contact')) ?>">Contact us</a></li>
            </ul>
        </div>
    </div>
</section>

<?php partial('footer'); ?>
