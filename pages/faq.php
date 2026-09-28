<?php
$faqs      = faqs_all();
$biz       = business();
$categories = faq_categories();

partial('header', [
    'head' => [
        'title'       => 'Cleaning Service FAQs Toronto | Maid4Condos',
        'description' => 'Answers to the questions we hear most: our guarantee, insurance, booking, access, products, pricing, cancellations and everything Maid4Condos does not do.',
        'canonical'   => '/faq',
        'og_image'    => 'assets/images/cleaning-windows.jpg',
        'schema'      => [
            schema_faq_page($faqs),
            schema_breadcrumbs(['Home' => '', 'FAQ' => 'faq']),
        ],
    ],
    'body_class' => 'page-faq',
]);

partial('page-hero', [
    'eyebrow'  => 'Common questions',
    'title'    => 'Frequently asked questions',
    'intro'    => 'Everything you might want to know before booking a Maid4Condos cleaning — from our guarantee and insurance to access, products, payment and the services we do not offer.',
    'crumbs'   => ['Home' => '', 'FAQ' => 'faq'],
    'facts'    => [
        'Still stuck? Call ' . $biz['phone_display'],
        'Or email ' . $biz['email'],
    ],
    'actions'  => [
        ['label' => 'Get a quote', 'route' => 'get-a-quote', 'variant' => 'primary', 'ga' => 'quote_cta_click'],
        ['label' => 'Contact us', 'route' => 'contact', 'variant' => 'outline-light'],
    ],
]);
?>

<section class="section" aria-label="Frequently asked questions">
    <div class="container container--narrow">
        <?php partial('faq-list', ['faqs' => $faqs, 'context' => 'faq-page', 'grouped' => true]); ?>
    </div>
</section>

<section class="section section--cream" aria-labelledby="faq-help">
    <div class="container">
        <div class="help-band">
            <div>
                <h2 id="faq-help">Still have a question?</h2>
                <p>Our office is open <?= e(trim(explode("\n", (string) $biz['office_hours'])[0])) ?>. Call, email or send us a message and we will get back to you.</p>
            </div>
            <div class="help-band__actions">
                <a class="btn btn--primary" href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="FAQ help">
                    <?php partial('icon', ['name' => 'phone', 'size' => 17]); ?> <?= e($biz['phone_display']) ?>
                </a>
                <a class="btn btn--outline" href="<?= e(url('contact')) ?>">Send a message</a>
            </div>
        </div>
    </div>
</section>

<?php partial('cta-band', [
    'heading' => 'Ready when you are',
    'text'    => 'Build your quote in about a minute — no account needed and no obligation.',
]); ?>
<?php partial('footer'); ?>
