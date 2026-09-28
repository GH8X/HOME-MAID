<?php
/**
 * Admin → Page sections
 *
 * Homepage and About content is stored as JSON so nested structures (hero
 * bullets, trust cards, step lists) stay editable without a schema change.
 * This screen edits that JSON with validation and a pretty-printer.
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$table = table('content_blocks');

$labels = [
    'home.hero'  => ['Homepage hero', 'Headline, supporting text, buttons and the hero image.'],
    'home.trust' => ['Homepage — trust section', 'Heading, intro and the credibility cards.'],
    'home.why'   => ['Homepage — why choose us', 'The “Because we care” section and its promise list.'],
    'home.how'   => ['Homepage — how it works', 'The numbered four-step process.'],
    'home.final' => ['Homepage — final call to action', 'The closing conversion band copy.'],
    'about'      => ['About page', 'Story paragraphs, promise list, memberships and exclusions.'],
];

// --- Save ---------------------------------------------------------------------
if (is_post() && input('action') === 'save') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $key  = clean_line(input('block_key'), 80);
        $raw  = (string) input('block_json');
        $data = json_decode($raw, true);

        if (!isset($labels[$key])) {
            flash('error', 'Unknown section.');
        } elseif (!is_array($data)) {
            flash('error', 'That is not valid JSON — check for a missing comma, quote or brace. Nothing was saved.');
        } else {
            $existing = db_one('SELECT id FROM ' . $table . ' WHERE block_key = ? LIMIT 1', [$key]);
            $payload  = [
                'block_json'   => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'is_published' => !empty($_POST['is_published']) ? 1 : 0,
            ];
            if ($existing) {
                db_update($table, $payload, 'id = ?', [$existing['id']]);
            } else {
                $payload['block_key'] = $key;
                db_insert($table, $payload);
            }
            flash('success', $labels[$key][0] . ' saved.');
        }
    }
    redirect('admin/blocks.php');
}

// --- Publish toggle -----------------------------------------------------------
if (is_post() && input('action') === 'toggle') {
    if (csrf_valid()) {
        $key = clean_line(input('block_key'), 80);
        $row = db_one('SELECT id, is_published FROM ' . $table . ' WHERE block_key = ? LIMIT 1', [$key]);
        if ($row) {
            db_update($table, ['is_published' => (int) $row['is_published'] === 1 ? 0 : 1], 'id = ?', [$row['id']]);
            flash('success', (int) $row['is_published'] === 1 ? 'Reverted to the built-in default.' : 'Custom content is live.');
        }
    }
    redirect('admin/blocks.php');
}

$editing = clean_line(input('edit', 'home.hero', $_GET), 80);
if (!isset($labels[$editing])) {
    $editing = 'home.hero';
}

$row        = db_one('SELECT * FROM ' . $table . ' WHERE block_key = ? LIMIT 1', [$editing]);
$isCustom   = $row && json_field($row['block_json'], []);
$current    = content_block($editing);
$jsonText   = json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

admin_header('Page sections', 'blocks');
admin_page_head('Page sections', 'Edit the copy for the homepage and About page. Each section is stored as structured JSON.');
?>

<div class="alert alert--info">
    <strong>Editing JSON</strong>
    <p>Content is grouped so nested lists (bullets, cards, steps) stay editable. Change only the text inside the quotation marks — keep the keys and the commas exactly as they are. Moving a value to <code>null</code> restores the built-in default for that section.</p>
</div>

<div class="admin-grid admin-grid--split">
    <section class="panel">
        <div class="panel__head"><h2>Sections</h2></div>
        <ul class="section-list">
            <?php foreach ($labels as $key => $meta) : ?>
                <?php
                $rowFor = db_one('SELECT is_published FROM ' . $table . ' WHERE block_key = ? LIMIT 1', [$key]);
                $status = $rowFor ? ((int) $rowFor['is_published'] === 1 ? 'Custom' : 'Default') : 'Default';
                ?>
                <li class="<?= $key === $editing ? 'is-active' : '' ?>">
                    <a href="<?= e(admin_url('blocks.php?edit=' . urlencode($key))) ?>">
                        <strong><?= e($meta[0]) ?></strong>
                        <small><?= e($meta[1]) ?></small>
                    </a>
                    <span class="badge <?= $status === 'Custom' ? 'badge--ok' : 'badge--muted' ?>"><?= e($status) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="panel panel--editor">
        <div class="panel__head">
            <h2><?= e($labels[$editing][0]) ?></h2>
            <div class="panel__actions">
                <button class="btn btn--outline btn--xs" type="button" data-json-format>Pretty print</button>
                <?php if ($row) : ?>
                    <form method="post" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="block_key" value="<?= e($editing) ?>">
                        <button class="btn btn--ghost-dark btn--xs" type="submit">
                            <?= (int) $row['is_published'] === 1 ? 'Revert to default' : 'Use custom content' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <p class="panel__intro"><?= e($labels[$editing][1]) ?></p>

        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="block_key" value="<?= e($editing) ?>">
            <div class="field">
                <label for="block-json">Section content (JSON)</label>
                <textarea id="block-json" name="block_json" rows="26" class="mono" spellcheck="false" data-json-editor><?= e($jsonText) ?></textarea>
                <p class="field__hint" data-json-status>Waiting for edits…</p>
            </div>
            <?php if ($row) : ?>
                <div class="field">
                    <label class="check">
                        <input type="checkbox" name="is_published" value="1" <?= (int) $row['is_published'] === 1 ? 'checked' : '' ?>>
                        <span class="check__box" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </span>
                        <span class="check__text">Publish this custom content (uncheck to fall back to the built-in default)</span>
                    </label>
                </div>
            <?php else : ?>
                <div class="alert alert--info">Saving will create a custom version of this section. The built-in default stays available if you ever want to revert.</div>
            <?php endif; ?>
            <div class="form-actions">
                <button class="btn btn--primary" type="submit">Save section</button>
                <a class="btn btn--outline" href="<?= e(url()) ?>" target="_blank" rel="noopener">Preview the homepage</a>
            </div>
        </form>
    </section>
</div>

<?php admin_footer(); ?>
