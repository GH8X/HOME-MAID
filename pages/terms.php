<?php
$biz = business();

partial('header', [
    'head' => [
        'title'       => 'Terms & Conditions | Maid4Condos',
        'description' => 'The terms that apply to Maid4Condos cleaning services in Toronto: quotes and pricing, booking and access, our 24 hour guarantee, cancellation fees and what is not included.',
        'canonical'   => '/terms',
        'robots'      => 'index,follow',
        'schema'      => [schema_breadcrumbs(['Home' => '', 'Terms & conditions' => 'terms'])],
    ],
    'body_class' => 'page-legal',
]);

partial('page-hero', [
    'eyebrow' => 'Legal',
    'title'   => 'Service terms & conditions',
    'intro'   => 'These terms apply to residential cleaning services provided by Maid4Condos and to the use of this website. By booking a cleaning you agree to them.',
    'crumbs'  => ['Home' => '', 'Terms & conditions' => 'terms'],
]);
?>

<section class="section">
    <div class="container container--narrow legal-copy">
        <p class="legal-copy__updated">Last updated: <?= e(date('F j, Y')) ?></p>

        <h2>1. Quotes and pricing</h2>
        <p>Quotes are prepared from the information you provide about your property: sizing, room count, number of bathrooms and the condition of the space. Published package prices are starting prices and the final price for your cleaning is confirmed before anything is booked.</p>
        <p>If, on arrival, the property is materially larger than described or is below average condition, we may contact you to request additional time. Additional time is charged at an hourly rate agreed with you at the time of the request. We will not charge you for extra time without your approval. If we cannot reach you during an active service to report the condition or request extra time, we will stop at the maximum time allocated to that service.</p>
        <p>Frequency discounts — 20% weekly, 15% bi-weekly and 5% monthly — apply to recurring AutoPilot schedules. Custom rates are available for daily service and high-volume requirements.</p>

        <h2>2. Booking and payment</h2>
        <p>When you book through this website or over the phone you are charged at the time of booking. Recurring schedules do not require a contract for residential clients, but by using our services you agree to these terms. Corporate and commercial clients are covered by separate service agreements.</p>

        <h2>3. Scheduling and arrival windows</h2>
        <p>We use either two-hour arrival windows or a flexible window during which we will clean between 9am and 5pm. Your chosen window is recorded at the time of booking. Arrival times are estimates because traffic, lockouts and same-day service changes are outside our control. Residential cleanings are scheduled Monday to Friday between 8am and 6pm; we do not currently offer evening or weekend residential bookings.</p>

        <h2>4. Access to your home</h2>
        <p>You choose how we access the property: you will be home, key at concierge, smart key pad, or lockbox and instructions. If access arrangements change, please tell our office as early as possible. If we cannot gain access within 30 minutes of arrival, the lockout provisions in section 7 apply.</p>

        <h2>5. Our satisfaction guarantee</h2>
        <p><?= e($biz['guarantee']) ?></p>
        <p>The guarantee depends on the property being as described and in average condition at the time of the service, and on you contacting our office within 24 hours of the cleaning.</p>

        <h2>6. What is not included</h2>
        <p>To protect the health and safety of our staff, and to set clear expectations, the following are outside the scope of our services:</p>
        <ul>
            <li>Inside or hard-to-reach light fixtures, exterior windows, blinds, drapes or shades.</li>
            <li>Inside dishwashers, washing machines or range hood fans.</li>
            <li>Cleaning during or after an infestation.</li>
            <li>Moving anything heavier than 30 lbs, or climbing higher than 2 ft on a ladder or step stool.</li>
            <li>Exterior or outdoor cleaning, including balconies.</li>
            <li>Pet or human waste and bodily fluids, including litter boxes, pet messes and overflowed toilets.</li>
            <li>Restoration of severely worn, stained, mildewed or mould infested caulking and grout. Mild surface presence can usually be addressed; where an infestation may pose an air-quality risk we reserve the right to remove our staff for health and safety, and our cancellation policy applies.</li>
            <li>Cleaning inside fireplaces, soot or ashes.</li>
            <li>Ironing or clothes folding. Folding is included where you have added a laundry load.</li>
            <li>Commercial carpet cleaning or shampooing — we can refer you to a partner.</li>
            <li>Post-renovation cleaning where construction debris or an active construction zone is involved. We are a finishing crew: we complete the final detailed clean before a property is returned or delivered.</li>
        </ul>

        <h2>7. Cancellations, rescheduling and lockouts</h2>
        <ul>
            <li>For scheduled cleanings, a <strong>$50 cancellation fee</strong> applies if a service is cancelled or rescheduled within 48 hours of your scheduled cleaning.</li>
            <li>For same-day cancellations, lockouts or rescheduling, or where we cannot gain access within 30 minutes of our arrival, we reserve the right to charge the <strong>greater of $75 or 50% of the total service fee</strong>.</li>
            <li>Recurring service cancellations: our cancellation policy applies to each visit. To cancel a recurring schedule before four or more completed visits, you will be charged the difference between your discounted frequency rate and a one-time cleaning fee for the prior visits.</li>
        </ul>

        <h2>8. Damage and liability</h2>
        <p>Our cleaners conduct themselves professionally in your home at all times. In the event of accidental damage, notify our office within 48 hours of your service. We cannot guarantee reimbursement for damage reported more than 48 hours after the end of the appointment, and we ask that you are reachable so we can assess and repair any damage.</p>
        <p>Maid4Condos carries liability insurance of $<?= e($biz['liability']) ?>, our staff are covered by WSIB and all staff are bonded.</p>

        <h2>9. Preparation and clutter</h2>
        <p>Please pick up loose items, garbage, debris and loose clothing from floors, table tops and countertops before we arrive. This allows our staff to spend the allocated time cleaning surfaces rather than tidying. We will work around highly cluttered areas and storage areas to the best of our ability.</p>

        <h2>10. Pets</h2>
        <p>We are happy to clean in homes with pets. Please tell us in advance so we can note it on your work order, and let us know about allergies to specific products.</p>

        <h2>11. Feedback</h2>
        <p>After every cleaning you will receive a feedback tool. We genuinely value your opinion — client comments and suggestions are how our training and quality control teams continue to improve.</p>

        <h2>12. Website use</h2>
        <p>The content on this website is provided for information about our services. Photographs are used under the licences listed on our <a href="<?= e(url('image-credits')) ?>">photography credits</a> page. If you would like to reuse any of our own written content, please ask first.</p>

        <h2>13. Governing law</h2>
        <p>These terms are governed by the laws of the Province of Ontario and the applicable laws of Canada.</p>

        <h2>14. Contact</h2>
        <address class="legal-copy__address">
            <strong><?= e($biz['legal_name']) ?></strong><br>
            <?= e($biz['street']) ?><br>
            <?= e($biz['city']) ?>, <?= e($biz['region']) ?> <?= e($biz['postal']) ?><br>
            <a href="mailto:<?= e($biz['email']) ?>"><?= e($biz['email']) ?></a><br>
            <a href="<?= e(tel_href()) ?>"><?= e($biz['phone_display']) ?></a>
        </address>
    </div>
</section>

<?php partial('footer'); ?>
