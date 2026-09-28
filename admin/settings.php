<?php
/**
 * Admin → Settings & SEO
 *
 * Groups: general, SEO, contact, social proof, trust, analytics and form copy.
 * Only keys that exist in the definitions table can be written, so this screen
 * can never create arbitrary settings.
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$definitions = m4c_setting_definitions_with_rows();
$table       = table('settings');
$saved       = false;

if (is_post() && input('action') === 'save') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $group  = clean_line(input('group'), 60);
        $posted = isset($_POST['settings']) && is_array($_POST['settings']) ? $_POST['settings'] : [];

        foreach ($posted as $key => $value) {
            if (!isset($definitions[$key])) {
                continue;
            }
            $meta  = $definitions[$key];
            $type  = $meta[1];
            $clean = $type === 'textarea' ? clean_text($value, 4000) : clean_line($value, 500);

            $existing = db_one('SELECT id FROM ' . $table . ' WHERE setting_key = ? LIMIT 1', [$key]);
            if ($existing) {
                db_update($table, ['setting_value' => $clean], 'id = ?', [$existing['id']]);
            } else {
                db_insert($table, [
                    'setting_key'   => $key,
                    'setting_value' => $clean,
                    'setting_group' => $meta[0],
                    'setting_type'  => $type,
                    'label'         => $meta[3],
                ]);
            }
        }

        flash('success', 'Settings saved.');
        redirect('admin/settings.php?group=' . urlencode($group));
    }
}

/** Settings definitions merged with the values currently stored in the database. */
function m4c_setting_definitions_with_rows()
{
    $definitions = setting_definitions();
    $stored      = [];
    foreach (content_query('SELECT setting_key, setting_value FROM ' . table('settings')) as $row) {
        $stored[$row['setting_key']] = $row['setting_value'];
    }
    $out = [];
    foreach ($definitions as $key => $meta) {
        $meta[2] = array_key_exists($key, $stored) ? $stored[$key] : $meta[2];
        $out[$key] = $meta;
    }

    return $out;
}

$groups = [
    'general'   => ['General', 'Site name, tagline and the default description used across the site.'],
    'seo'       => ['SEO defaults', 'Titles, meta descriptions and social sharing defaults.'],
    'contact'   => ['Contact details', 'Phone, email, address, hours and the Google Maps query.'],
    'social'    => ['Social & reviews', 'Social profiles and the review counts shown as social proof.'],
    'trust'     => ['Trust & guarantees', 'The guarantee wording and liability coverage figure.'],
    'analytics' => ['Analytics', 'Google Analytics 4 measurement ID.'],
    'forms'     => ['Form messages', 'The thank-you copy shown after a successful submission.'],
];

$group = clean_line(input('group', 'general', $_GET), 60);
if (!isset($groups[$group])) {
    $group = 'general';
}

$groupSettings = array_filter($definitions, function ($meta) use ($group) {
    return $meta[0] === $group;
});

admin_header('Settings', 'settings');
admin_page_head('Settings & SEO', 'Business details, search metadata, analytics and the words customers read.');
?>

<div class="tabs" role="tablist">
    <?php foreach ($groups as $key => $meta) : ?>
        <a class="tabs__tab <?= $key === $group ? 'is-active' : '' ?>" role="tab"
           aria-selected="<?= $key === $group ? 'true' : 'false' ?>"
           href="<?= e(admin_url('settings.php?group=' . urlencode($key))) ?>">
            <?= e($meta[0]) ?>
            <span class="tabs__count"><?= (int) count(array_filter($definitions, function ($m) use ($key) { return $m[0] === $key; })) ?></span>
        </a>
    <?php endforeach; ?>
</div>

<section class="panel">
    <div class="panel__head">
        <h2><?= e($groups[$group][0]) ?></h2>
        <span class="badge badge--muted"><?= count($groupSettings) ?> settings</span>
    </div>
    <p class="panel__intro"><?= e($groups[$group][1]) ?></p>

    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="group" value="<?= e($group) ?>">

        <?php foreach ($groupSettings as $key => $meta) : ?>
            <?php
            $type  = $meta[1];
            $value = (string) $meta[2];
            $label = $meta[3];
            $fieldId = 'set-' . preg_replace('/[^a-z0-9_]/i', '-', $key);
            ?>
            <div class="field">
                <label for="<?= e($fieldId) ?>"><?= e($label) ?></label>
                <?php if ($type === 'textarea') : ?>
                    <textarea id="<?= e($fieldId) ?>" name="settings[<?= e($key) ?>]" rows="4"><?= e($value) ?></textarea>
                <?php elseif ($type === 'select' && $key === 'analytics_enabled') : ?>
                    <select id="<?= e($fieldId) ?>" name="settings[<?= e($key) ?>]">
                        <option value="1" <?= $value === '1' ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= $value !== '1' ? 'selected' : '' ?>>Disabled</option>
                    </select>
                <?php else : ?>
                    <input type="text" id="<?= e($fieldId) ?>" name="settings[<?= e($key) ?>]" value="<?= e($value) ?>">
                <?php endif; ?>
                <p class="field__hint">Key: <code><?= e($key) ?></code></p>

                <?php if ($key === 'ga4_measurement_id') : ?>
                    <div class="panel__hint">
                        <p>Create a Google Analytics 4 property, then copy the measurement ID (it looks like <code>G-XXXXXXXXXX</code>). Save it here and every page starts reporting automatically.</p>
                        <p>Tracked conversion events: quote requests, contact messages, phone clicks, email clicks and CTA clicks.</p>
                    </div>
                <?php elseif ($key === 'opening_hours_schema') : ?>
                    <div class="panel__hint">
                        <p>Format: <code>Mo-Fr 08:00-18:00|Sa-Su 09:00-16:00</code>. Day codes are <code>Mo Tu We Th Fr Sa Su</code>, groups separated by <code>|</code>. This feeds the LocalBusiness structured data.</p>
                    </div>
                <?php elseif ($key === 'default_meta_description') : ?>
                    <p class="field__hint">Aim for 140–160 characters. Currently <?= e(strlen($value)) ?>.</p>
                <?php elseif ($key === 'default_meta_title') : ?>
                    <p class="field__hint">Aim for 50–60 characters. Currently <?= e(strlen($value)) ?>.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="form-actions form-actions--sticky">
            <button class="btn btn--primary" type="submit">Save settings</button>
            <a class="btn btn--outline" href="<?= e(url()) ?>" target="_blank" rel="noopener">View the website</a>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel__head"><h2>Environment keys</h2></div>
    <p class="panel__intro">
        Secrets are never stored in the database. Set them in <code>config/config.local.php</code> or as environment
        variables on your server. Recognised keys:
    </p>
    <ul class="key-list">
        <li><code>M4C_DB_HOST</code>, <code>M4C_DB_NAME</code>, <code>M4C_DB_USER</code>, <code>M4C_DB_PASS</code> — MySQL connection</li>
        <li><code>M4C_MAIL_TRANSPORT</code> — <code>mail</code> (default) or <code>elastic</code></li>
        <li><code>M4C_MAIL_API_KEY</code> — Elastic Email API key, used when the transport is <code>elastic</code></li>
        <li><code>M4C_MAIL_FROM</code>, <code>M4C_MAIL_TO</code> — sender and notification recipient</li>
        <li><code>M4C_GA4_ID</code> — Google Analytics 4 ID (the settings value takes precedence)</li>
        <li><code>M4C_BASE_URL</code> — full URL of the site, e.g. <code>https://www.maid4condos.com</code></li>
    </ul>
    <p class="panel__hint">Current mail transport: <strong><?= e(mailer_transport_label()) ?></strong>.</p>
</section>

<?php admin_footer(); ?>
