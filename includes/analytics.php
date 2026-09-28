<?php
/**
 * Maid4Condos — Google Analytics 4 integration.
 *
 * The measurement ID is never hard-coded: it is read from the site settings
 * (Admin → Settings → Analytics, or the M4C_GA4_ID environment variable).
 * Conversion events are emitted from assets/js/main.js and use the
 * `data-ga-event` attribute convention so no inline tracking code is needed.
 */

/** True when a measurement ID is configured and analytics are switched on. */
function analytics_enabled()
{
    $id = trim((string) setting('ga4_measurement_id', config('analytics.ga4_id', '')));

    return $id !== '' && setting('analytics_enabled', '1') === '1';
}

function analytics_id()
{
    return trim((string) setting('ga4_measurement_id', config('analytics.ga4_id', '')));
}

/** Emit the GA4 loader in <head>. */
function m4c_analytics_head()
{
    if (!analytics_enabled()) {
        return;
    }
    $id = analytics_id();
    ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($id) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= e($id) ?>', {
    anonymize_ip: <?= config('analytics.anonymize_ip') ? 'true' : 'false' ?>,
    send_page_view: true
  });
</script>
    <?php
}

/**
 * Mapping of the conversion events the site reports.
 * Used by the admin dashboard as documentation, and by main.js as the allow-list.
 */
function analytics_events()
{
    return [
        'quote_form_submit'   => 'Quote form submitted successfully',
        'contact_form_submit' => 'Contact form submitted successfully',
        'phone_click'         => 'Phone number clicked',
        'email_click'         => 'Email address clicked',
        'quote_cta_click'     => 'Get a quote call-to-action clicked',
        'service_view'        => 'Service detail page viewed',
        'map_click'           => 'Directions / map link clicked',
    ];
}
