<?php
/**
 * Maid4Condos — one-time installer.
 *
 * 1. Confirms the database connection from config/config.local.php.
 * 2. Creates the tables from database/schema.sql.
 * 3. Imports the verified default content from includes/seed.php.
 * 4. Creates the first administrator account.
 *
 * Delete this file (or the /database folder) when setup is complete.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';
require dirname(__DIR__) . '/includes/seeder.php';

$steps   = [];
$errors  = [];
$adminExists = false;

if (db_available()) {
    try {
        $adminExists = (int) db_value('SELECT COUNT(*) FROM ' . table('admins')) > 0;
    } catch (Throwable $e) {
        $adminExists = false;
    }
}

$posted = is_post() && input('install_step') !== '';

if ($posted) {
    if (!csrf_valid()) {
        $errors[] = 'Your session expired. Reload the page and try again.';
    } elseif (!db_available()) {
        $errors[] = 'No database connection. Add your MySQL credentials to config/config.local.php first.';
    } else {
        $step = input('install_step');

        if ($step === 'schema') {
            $sql = @file_get_contents(path('database/schema.sql'));
            if ($sql === false || trim($sql) === '') {
                $errors[] = 'database/schema.sql could not be read.';
            } else {
                $statements = m4c_split_sql($sql);
                $failed = 0;
                foreach ($statements as $statement) {
                    try {
                        db()->exec($statement);
                    } catch (Throwable $e) {
                        $failed++;
                        $errors[] = 'SQL error: ' . $e->getMessage();
                    }
                }
                if ($failed === 0) {
                    $steps[] = count($statements) . ' SQL statements executed — tables are ready.';
                }
            }
        }

        if ($step === 'content') {
            $result = m4c_seed_all(false);
            if (isset($result['error'])) {
                $errors[] = $result['error'];
            } else {
                $steps[] = 'Imported: ' . (empty($result['log']) ? 'nothing new (content already present).' : implode(', ', $result['log']));
            }
        }

        if ($step === 'admin') {
            $error = m4c_create_admin(
                input('name'),
                input('email'),
                (string) input('password'),
                'owner'
            );
            if ($error) {
                $errors[] = $error;
            } else {
                $steps[]  = 'Administrator created. Sign in at /admin/index.php.';
                $adminExists = true;
            }
        }
    }
}

/** Split a SQL file into individual statements, ignoring comment lines. */
function m4c_split_sql($sql)
{
    $lines = preg_split('/\r\n|\r|\n/', $sql);
    $clean = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '--') === 0) {
            continue;
        }
        $clean[] = $line;
    }
    $statements = [];
    foreach (explode(';', implode("\n", $clean)) as $statement) {
        $statement = trim($statement);
        if ($statement !== '') {
            $statements[] = $statement;
        }
    }

    return $statements;
}

$cfg = config('db');
?>
<!doctype html>
<html lang="en-CA">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Maid4Condos — setup</title>
    <link rel="icon" href="<?= e(asset('images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin-auth">
<main class="admin-auth__wrap">
    <div class="admin-auth__card admin-auth__card--wide">
        <div class="admin-auth__brand">
            <img src="<?= e(asset('images/logo.svg')) ?>" width="44" height="44" alt="" aria-hidden="true">
            <div>
                <strong>Maid4Condos</strong>
                <span>First-time setup</span>
            </div>
        </div>

        <h1>Install your database</h1>
        <p class="admin-auth__lead">
            The website already works without a database — it renders the built-in content.
            Installing MySQL lets you edit everything from the admin panel and store quote requests.
        </p>

        <?php foreach ($steps as $step) : ?>
            <div class="alert alert--success" role="status"><?= e($step) ?></div>
        <?php endforeach; ?>
        <?php foreach ($errors as $error) : ?>
            <div class="alert alert--error" role="alert"><?= e($error) ?></div>
        <?php endforeach; ?>

        <section class="install-block">
            <h2>1. Database connection</h2>
            <?php if (db_available()) : ?>
                <p class="install-ok">Connected to <code><?= e($cfg['name']) ?></code> on <code><?= e($cfg['host']) ?></code>.</p>
            <?php else : ?>
                <p class="install-bad">
                    Not connected. Create <code>config/config.local.php</code> with your MySQL credentials:
                </p>
<pre class="install-code">&lt;?php
return [
    'db' =&gt; [
        'enabled' =&gt; true,
        'host'    =&gt; 'localhost',
        'name'    =&gt; 'your_database',
        'user'    =&gt; 'your_user',
        'pass'    =&gt; 'your_password',
    ],
];</pre>
                <p class="install-note">You can also set the <code>M4C_DB_HOST</code>, <code>M4C_DB_NAME</code>, <code>M4C_DB_USER</code> and <code>M4C_DB_PASS</code> environment variables instead.</p>
            <?php endif; ?>
        </section>

        <section class="install-block">
            <h2>2. Create the tables</h2>
            <p>Imports <code>database/schema.sql</code>. Existing tables are left untouched.</p>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="install_step" value="schema">
                <button class="btn btn--primary" type="submit" <?= db_available() ? '' : 'disabled' ?>>Run schema import</button>
            </form>
        </section>

        <section class="install-block">
            <h2>3. Import the default content</h2>
            <p>Copies the real services, FAQs, testimonials and service areas into the database so you can edit them.</p>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="install_step" value="content">
                <button class="btn btn--primary" type="submit" <?= db_available() ? '' : 'disabled' ?>>Import content</button>
            </form>
        </section>

        <section class="install-block">
            <h2>4. Create the administrator</h2>
            <?php if ($adminExists) : ?>
                <p class="install-ok">An administrator account already exists. <a href="<?= e(url('admin/index.php')) ?>">Go to the admin sign-in</a>.</p>
            <?php else : ?>
                <form method="post" class="install-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="install_step" value="admin">
                    <div class="field">
                        <label for="i-name">Your name</label>
                        <input type="text" id="i-name" name="name" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="i-email">Email</label>
                        <input type="email" id="i-email" name="email" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="i-pass">Password <span class="field__optional">(at least 10 characters)</span></label>
                        <input type="password" id="i-pass" name="password" required minlength="10" autocomplete="new-password">
                    </div>
                    <button class="btn btn--primary" type="submit" <?= db_available() ? '' : 'disabled' ?>>Create administrator</button>
                </form>
            <?php endif; ?>
        </section>

        <div class="admin-auth__warning">
            <strong>Important:</strong> delete the <code>/database</code> folder once setup is complete so the installer cannot be reached again.
        </div>
    </div>
</main>
</body>
</html>
