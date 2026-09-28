<?php
/**
 * /admin — sign-in screen, then the dashboard.
 *
 * The administrator system is entirely separate from the public website: only
 * rows in the `admins` table can authenticate and every password is stored as a
 * password_hash() digest.
 */
require __DIR__ . '/includes/admin-bootstrap.php';

$loginError = null;
$needsSetup = false;

if (db_available()) {
    try {
        $needsSetup = (int) db_value('SELECT COUNT(*) FROM ' . table('admins')) === 0;
    } catch (Throwable $e) {
        $needsSetup = true;
    }
} else {
    $needsSetup = true;
}

// --- Sign in -----------------------------------------------------------------
if (is_post() && input('form') === 'login' && !admin_logged_in()) {
    if (!csrf_valid()) {
        $loginError = 'Your session expired. Please try again.';
    } else {
        $loginError = admin_attempt_login((string) input('email'), (string) input('password'));
        if ($loginError === null) {
            flash('success', 'Welcome back.');
            redirect('admin/index.php');
        }
    }
}

// --- Import default content --------------------------------------------------
if (is_post() && admin_logged_in() && input('form') === 'seed') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired.');
    } else {
        $result = m4c_seed_all(input('force') === '1');
        if (isset($result['error'])) {
            flash('error', $result['error']);
        } else {
            flash('success', empty($result['log'])
                ? 'Nothing to import — all content already exists in the database.'
                : 'Imported: ' . implode(', ', $result['log']));
        }
    }
    redirect('admin/index.php');
}

/* -----------------------------------------------------------------------------
   Signed out: sign-in screen
   -------------------------------------------------------------------------- */
if (!admin_logged_in()) {
    $adminCount = db_available() ? (int) db_value('SELECT COUNT(*) FROM ' . table('admins')) : 0;
    ?>
    <!doctype html>
    <html lang="en-CA">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title>Admin sign in · <?= e(setting('site_name', 'Maid4Condos')) ?></title>
        <link rel="icon" href="<?= e(asset('images/favicon.svg')) ?>" type="image/svg+xml">
        <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
    </head>
    <body class="admin-auth">
    <main class="admin-auth__wrap">
        <div class="admin-auth__card">
            <div class="admin-auth__brand">
                <img src="<?= e(asset('images/logo.svg')) ?>" width="44" height="44" alt="" aria-hidden="true">
                <div>
                    <strong><?= e(setting('site_name', 'Maid4Condos')) ?></strong>
                    <span>Administrator access</span>
                </div>
            </div>

            <h1>Sign in</h1>
            <p class="admin-auth__lead">This area is for Maid4Condos staff only and is not linked from the public website.</p>

            <?php foreach (take_flashes() as $flash) : ?>
                <div class="alert alert--<?= $flash['type'] === 'error' ? 'error' : 'success' ?>"><?= e($flash['message']) ?></div>
            <?php endforeach; ?>
            <?php if ($loginError) : ?>
                <div class="alert alert--error" role="alert"><?= e($loginError) ?></div>
            <?php endif; ?>
            <?php if (!$loginError && isset($_GET['returnTo'])) : ?>
                <div class="alert alert--info">Please sign in to continue.</div>
            <?php endif; ?>

            <?php if (!$adminCount) : ?>
                <div class="alert alert--info">
                    <strong>No administrator exists yet.</strong>
                    <?php if (db_available()) : ?>
                        <p>Run the one-time installer to create your account, then delete the installer.</p>
                        <a class="btn btn--primary btn--sm" href="<?= e(url('database/install.php')) ?>">Open the installer</a>
                    <?php else : ?>
                        <p>Connect your MySQL database first — see <code>config/config.php</code> or create <code>config/config.local.php</code>.</p>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <form method="post" class="admin-auth__form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form" value="login">
                    <div class="field">
                        <label for="admin-email">Email address</label>
                        <input type="email" id="admin-email" name="email" required autocomplete="username" autofocus>
                    </div>
                    <div class="field">
                        <label for="admin-password">Password</label>
                        <input type="password" id="admin-password" name="password" required autocomplete="current-password">
                    </div>
                    <button class="btn btn--primary btn--block" type="submit">Sign in</button>
                </form>
            <?php endif; ?>

            <p class="admin-auth__foot"><a href="<?= e(url()) ?>">← Back to the website</a></p>
        </div>
    </main>
    </body>
    </html>
    <?php
    exit;
}

/* -----------------------------------------------------------------------------
   Signed in: dashboard
   -------------------------------------------------------------------------- */
require_admin();

$newInquiries = 0;
$totalInquiries = 0;
$newMessages = 0;
$recentInquiries = [];
$recentMessages = [];

if (db_available()) {
    try {
        $newInquiries   = (int) db_value('SELECT COUNT(*) FROM ' . table('inquiries') . " WHERE status = 'new'");
        $totalInquiries = (int) db_value('SELECT COUNT(*) FROM ' . table('inquiries'));
        $newMessages    = (int) db_value('SELECT COUNT(*) FROM ' . table('messages') . " WHERE status = 'new'");
        $recentInquiries = db_all('SELECT id, name, neighbourhood, service_name, status, created_at FROM ' . table('inquiries') . ' ORDER BY id DESC LIMIT 6');
        $recentMessages  = db_all('SELECT id, name, subject, status, created_at FROM ' . table('messages') . ' ORDER BY id DESC LIMIT 5');
    } catch (Throwable $e) {
        // Fall through with the zero values.
    }
}

$counts = content_counts();

admin_header('Overview', 'dashboard');
admin_page_head('Overview', 'Everything on the website, from one place.');

if (!db_available()) {
    ?>
    <div class="alert alert--warn">
        <strong>The database is not connected.</strong>
        <p>The website is still live and complete — it renders the built-in content. Connect MySQL to edit content here and to store quote requests.</p>
        <p>Create <code>config/config.local.php</code> with your credentials, then run the installer.</p>
    </div>
    <?php
}
?>

<div class="stat-grid">
    <?php
    admin_stat('New quote requests', $newInquiries, 'inquiries.php', 'inbox');
    admin_stat('All quote requests', $totalInquiries, 'inquiries.php', 'chart');
    admin_stat('Unread messages', $newMessages, 'messages.php', 'mail');
    admin_stat('Services', $counts['services'], 'services.php', 'spark');
    admin_stat('FAQs', $counts['faqs'], 'faqs.php', 'help');
    admin_stat('Testimonials', $counts['testimonials'], 'testimonials.php', 'quote');
    admin_stat('Service areas', $counts['areas'], 'areas.php', 'map');
    admin_stat('Add-ons', $counts['extras'], 'extras.php', 'spray');
    ?>
</div>

<div class="admin-grid admin-grid--split">
    <section class="panel">
        <div class="panel__head">
            <h2>Latest quote requests</h2>
            <a class="btn btn--outline btn--sm" href="<?= e(admin_url('inquiries.php')) ?>">View all</a>
        </div>
        <?php if (!$recentInquiries) : ?>
            <p class="empty-state">No quote requests recorded yet. Requests appear here as soon as the database is connected.</p>
        <?php else : ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>Name</th><th>Area</th><th>Service</th><th>Status</th><th>Received</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentInquiries as $inquiry) : ?>
                        <tr>
                            <td><a href="<?= e(admin_url('inquiries.php?view=' . (int) $inquiry['id'])) ?>"><?= e($inquiry['name']) ?></a></td>
                            <td><?= e($inquiry['neighbourhood']) ?></td>
                            <td><?= e($inquiry['service_name']) ?></td>
                            <td><span class="badge badge--<?= $inquiry['status'] === 'new' ? 'ok' : 'muted' ?>"><?= e(ucfirst($inquiry['status'])) ?></span></td>
                            <td><?= e(date('M j, g:ia', strtotime((string) $inquiry['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h2>Latest messages</h2>
            <a class="btn btn--outline btn--sm" href="<?= e(admin_url('messages.php')) ?>">View all</a>
        </div>
        <?php if (!$recentMessages) : ?>
            <p class="empty-state">No contact messages yet.</p>
        <?php else : ?>
            <ul class="mini-list">
                <?php foreach ($recentMessages as $message) : ?>
                    <li>
                        <a href="<?= e(admin_url('messages.php?view=' . (int) $message['id'])) ?>">
                            <strong><?= e($message['name']) ?></strong>
                            <small><?= e($message['subject']) ?> · <?= e(date('M j', strtotime((string) $message['created_at']))) ?></small>
                        </a>
                        <?php if ($message['status'] === 'new') : ?><span class="badge badge--ok">New</span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="panel__divider"></div>

        <h3>Content tools</h3>
        <form method="post" class="stack">
            <?= csrf_field() ?>
            <input type="hidden" name="form" value="seed">
            <p class="field__hint">Copy the verified default content (services, FAQs, testimonials, areas, settings) into the database so you can edit it. Existing records are kept unless you force a refresh.</p>
            <div class="form-actions">
                <button class="btn btn--outline btn--sm" type="submit">Import missing content</button>
                <button class="btn btn--ghost-dark btn--sm" type="submit" name="force" value="1"
                        data-confirm="Replace all content with the defaults? Your edits to these records will be lost.">
                    Replace with defaults
                </button>
            </div>
        </form>
    </section>
</div>

<div class="panel">
    <div class="panel__head"><h2>SEO &amp; analytics status</h2></div>
    <ul class="checklist-status">
        <li>
            <span class="badge <?= setting('ga4_measurement_id', '') !== '' ? 'badge--ok' : 'badge--muted' ?>">
                <?= setting('ga4_measurement_id', '') !== '' ? 'Active' : 'Not set' ?>
            </span>
            Google Analytics 4 — <?= setting('ga4_measurement_id', '') !== '' ? e(setting('ga4_measurement_id', '')) : 'add a measurement ID in Settings' ?>
        </li>
        <li><span class="badge badge--ok">Ready</span> XML sitemap at <a href="<?= e(url('sitemap.xml')) ?>" target="_blank" rel="noopener">/sitemap.xml</a></li>
        <li><span class="badge badge--ok">Ready</span> robots.txt at <a href="<?= e(absolute_url('robots.txt')) ?>" target="_blank" rel="noopener">/robots.txt</a></li>
        <li><span class="badge badge--ok">Ready</span> Structured data: LocalBusiness, Service, FAQPage and BreadcrumbList</li>
    </ul>
</div>

<?php admin_footer(); ?>
