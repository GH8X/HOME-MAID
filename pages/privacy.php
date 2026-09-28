<?php
$biz = business();

partial('header', [
    'head' => [
        'title'       => 'Privacy Policy | Maid4Condos',
        'description' => 'How Maid4Condos collects, uses, stores and protects the personal information you share when requesting a quote or contacting our Toronto cleaning team.',
        'canonical'   => '/privacy-policy',
        'robots'      => 'index,follow',
        'schema'      => [schema_breadcrumbs(['Home' => '', 'Privacy policy' => 'privacy-policy'])],
    ],
    'body_class' => 'page-legal',
]);

partial('page-hero', [
    'eyebrow' => 'Legal',
    'title'   => 'Privacy policy',
    'intro'   => 'This policy explains what information Maid4Condos collects through this website, why we collect it, how long we keep it and the choices you have.',
    'crumbs'  => ['Home' => '', 'Privacy policy' => 'privacy-policy'],
]);
?>

<section class="section">
    <div class="container container--narrow legal-copy">
        <p class="legal-copy__updated">Last updated: <?= e(date('F j, Y')) ?></p>

        <p>Maid4Condos ("we", "us", "our") operates this website. We are a Toronto cleaning company and this website exists to explain our cleaning services and to let you request a quote or contact our office. This page describes our practices for information collected through the website.</p>

        <h2>What we collect</h2>
        <ul>
            <li><strong>Quote requests.</strong> Your name, email address, phone number, neighbourhood and postal code, property type, number of bedrooms and bathrooms, approximate square footage, the cleaning service and frequency you selected, any add-ons you chose, your preferred date and arrival window, how we should access your home, and any notes you write in the additional information field.</li>
            <li><strong>Messages.</strong> If you use the contact form we collect your name, email address, an optional phone number, the topic you selected and your message.</li>
            <li><strong>Technical data.</strong> Like most websites, our server receives your IP address, browser and device information, and the page you requested. This is used for security, abuse prevention and aggregate traffic reporting.</li>
        </ul>
        <p>We do not ask for payment card details through this website, and we never ask for your social insurance number, banking passwords or other sensitive identifiers.</p>

        <h2>Why we use it</h2>
        <ul>
            <li>To prepare and send you a cleaning quote, and to answer your questions.</li>
            <li>To schedule and deliver the cleaning service you book.</li>
            <li>To send booking confirmations, reminders and service-related notices.</li>
            <li>To keep records of requests and cleanings so we can service you consistently.</li>
            <li>To detect and prevent spam, abuse and fraud on our forms.</li>
        </ul>
        <p>We rely on your consent when you submit a form, and on our legitimate business interests when we keep records of the work we have done and protect the website from abuse.</p>

        <h2>Who we share it with</h2>
        <p>We do not sell your personal information. We share it only with the service providers that make the website and our operations work:</p>
        <ul>
            <li><strong>Email delivery.</strong> Quote requests and messages are emailed to our office through our email service provider.</li>
            <li><strong>Hosting.</strong> This website and its database are hosted on a third-party web hosting service.</li>
            <li><strong>Analytics.</strong> If analytics is enabled, aggregated and, where configured, anonymised usage data is processed by Google Analytics.</li>
            <li><strong>Maps.</strong> Our contact page and service area section embed Google Maps, which may set its own cookies.</li>
        </ul>
        <p>We may also disclose information where we are legally required to do so, or to protect the safety and rights of our staff and clients.</p>

        <h2>How long we keep it</h2>
        <p>Quote requests and messages are kept for as long as needed to respond to you and to maintain our business records. Records relating to completed cleanings are kept for our accounting and service history. You can ask us to delete your enquiry at any time.</p>

        <h2>Cookies</h2>
        <p>This website uses a small session cookie so that forms are protected against cross-site request forgery and so you can keep a form open for a short time. If analytics is enabled, analytics cookies are also used to measure how visitors use the site. You can block or delete cookies in your browser settings; form submissions may stop working if session cookies are blocked.</p>

        <h2>Security</h2>
        <p>We protect the information you send us with industry-standard measures: encrypted connections (HTTPS), server-side validation, protection against cross-site request forgery and cross-site scripting, and restricted access to administrative systems. Passwords for any administrative accounts are stored as one-way hashes, never as readable text.</p>

        <h2>Your choices</h2>
        <ul>
            <li>Ask us what personal information we hold about you.</li>
            <li>Ask us to correct anything that is inaccurate.</li>
            <li>Ask us to delete your enquiry or message.</li>
            <li>Ask us to stop contacting you (except where we must retain a record for legal or accounting reasons).</li>
        </ul>
        <p>To make any of these requests, email <a href="mailto:<?= e($biz['email']) ?>" data-ga-event="email_click" data-ga-label="Privacy page"><?= e($biz['email']) ?></a> or call <a href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Privacy page"><?= e($biz['phone_display']) ?></a>.</p>

        <h2>Changes to this policy</h2>
        <p>If we change how we handle personal information we will update this page and revise the date at the top. Material changes affecting how we use information you have already given us will be communicated by email where we have an address for you.</p>

        <h2>Contact</h2>
        <address class="legal-copy__address">
            <strong><?= e($biz['legal_name']) ?></strong><br>
            <?= e($biz['street']) ?><br>
            <?= e($biz['city']) ?>, <?= e($biz['region']) ?> <?= e($biz['postal']) ?><br>
            <?= e($biz['country']) ?><br>
            <a href="mailto:<?= e($biz['email']) ?>"><?= e($biz['email']) ?></a><br>
            <a href="<?= e(tel_href()) ?>"><?= e($biz['phone_display']) ?></a>
        </address>
    </div>
</section>

<?php partial('footer'); ?>
