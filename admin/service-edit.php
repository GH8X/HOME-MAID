<?php
/**
 * Admin → Services → add / edit.
 *
 * Long-form fields are edited as readable text and converted to JSON on save:
 *   • Paragraphs      — separated by a blank line
 *   • Lists           — one item per line
 *   • Checklists      — "Room | item | item"
 *   • Service FAQs    — "Question | Answer"
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$table = table('services');
$id    = int_input('id', 0, array_merge($_GET, $_POST));
$isNew = isset($_GET['new']);
$errors = [];

/** Split a textarea into paragraphs (blank-line separated). */
function m4c_paragraphs($text)
{
    $parts = preg_split('/\n\s*\n/', (string) $text);

    return array_values(array_filter(array_map(function ($part) {
        return trim(preg_replace('/\s+/', ' ', $part));
    }, $parts), 'strlen'));
}

function m4c_paragraphs_to_text(array $paragraphs)
{
    return implode("\n\n", $paragraphs);
}

/** Convert "Room | a | b" lines into an ordered map preserving the room label. */
function m4c_checklist_from_text($text)
{
    $groups = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) $text) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        $label = array_shift($parts);
        if ($label === '') {
            continue;
        }
        $items = array_values(array_filter($parts, 'strlen'));
        if ($items) {
            $groups[$label] = $items;
        }
    }

    return $groups;
}

function m4c_checklist_to_text(array $checklist)
{
    $lines = [];
    foreach ($checklist as $label => $items) {
        $items = is_array($items) ? $items : [$items];
        $lines[] = $label . ' | ' . implode(' | ', array_map('strval', $items));
    }

    return implode("\n", $lines);
}

/** Convert "Question | Answer" lines into pairs. */
function m4c_faqs_from_text($text)
{
    $faqs = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) $text) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '|') === false) {
            continue;
        }
        list($question, $answer) = array_map('trim', explode('|', $line, 2));
        if ($question !== '' && $answer !== '') {
            // Stored in the same shape as the main FAQ list (see faq_pairs()).
            $faqs[] = ['question' => $question, 'answer' => $answer];
        }
    }

    return $faqs;
}

function m4c_faqs_to_text(array $faqs)
{
    $lines = [];
    foreach (faq_pairs($faqs) as $faq) {
        $lines[] = $faq['question'] . ' | ' . $faq['answer'];
    }

    return implode("\n", $lines);
}

// --- Save ---------------------------------------------------------------------
if (is_post() && input('action') === 'save') {
    if (!csrf_valid()) {
        $errors[] = 'Your session expired. Please reload and try again.';
    } else {
        $slug = slugify(input('slug'));
        if ($slug === '') {
            $slug = slugify(input('name'));
        }

        $serviceId = int_input('id');
        $image     = clean_line(input('image'), 190);

        // Optional replacement upload.
        list($uploaded, $uploadError) = handle_image_upload('image_upload', $slug ?: 'service');
        if ($uploadError) {
            $errors[] = $uploadError;
        } elseif ($uploaded) {
            $image = $uploaded;
        }

        $data = [
            'slug'             => $slug,
            'name'             => clean_line(input('name'), 120),
            'eyebrow'          => clean_line(input('eyebrow'), 120),
            'tagline'          => clean_line(input('tagline'), 190),
            'summary'          => clean_text(input('summary'), 600),
            'intro_json'       => json_encode(m4c_paragraphs(input('intro'))),
            'price_from'       => input('price_from') !== '' ? number_format((float) input('price_from'), 2, '.', '') : null,
            'image'            => $image,
            'hero_image'       => $image,
            'duration_note'    => clean_line(input('duration_note'), 190),
            'best_paired'      => clean_line(input('best_paired'), 190),
            'meta_title'       => clean_line(input('meta_title'), 190),
            'meta_description' => clean_line(input('meta_description'), 320),
            'who_for_json'     => json_encode(lines_to_list(input('who_for'))),
            'checklist_json'   => json_encode(m4c_checklist_from_text(input('checklist'))),
            'benefits_json'    => json_encode(lines_to_list(input('benefits'))),
            'faqs_json'        => json_encode(m4c_faqs_from_text(input('faqs'))),
            'sort_order'       => int_input('sort_order'),
            'is_published'     => !empty($_POST['is_published']) ? 1 : 0,
        ];

        if ($data['name'] === '') {
            $errors[] = 'The service name is required.';
        }
        if ($data['slug'] === '') {
            $errors[] = 'A URL slug is required.';
        }
        if ($data['summary'] === '') {
            $errors[] = 'A short summary is required — it appears on the service cards.';
        }

        if (!$errors) {
            try {
                if ($serviceId > 0) {
                    db_update($table, $data, 'id = ?', [$serviceId]);
                    flash('success', 'Service updated.');
                } else {
                    $existing = db_one('SELECT id FROM ' . $table . ' WHERE slug = ? LIMIT 1', [$data['slug']]);
                    if ($existing) {
                        db_update($table, $data, 'id = ?', [$existing['id']]);
                        flash('success', 'Service updated.');
                    } else {
                        db_insert($table, $data);
                        flash('success', 'Service created.');
                    }
                }
                redirect('admin/services.php');
            } catch (Throwable $e) {
                $errors[] = config('app.debug') ? 'Database error: ' . $e->getMessage() : 'That could not be saved.';
            }
        }
    }
}

// --- Load the record ----------------------------------------------------------
$record = null;
if ($id > 0) {
    $record = db_one('SELECT * FROM ' . $table . ' WHERE id = ? LIMIT 1', [$id]);
}
if (!$record && !$isNew) {
    flash('error', 'That service could not be found. Import the default content from the Overview screen first.');
    redirect('admin/services.php');
}

$defaults = [
    'id' => 0, 'slug' => '', 'name' => '', 'eyebrow' => '', 'tagline' => '', 'summary' => '',
    'price_from' => '', 'image' => '', 'hero_image' => '', 'duration_note' => '', 'best_paired' => '',
    'meta_title' => '', 'meta_description' => '', 'sort_order' => 0, 'is_published' => 1,
    'intro_json' => '[]', 'who_for_json' => '[]', 'checklist_json' => '{}', 'benefits_json' => '[]', 'faqs_json' => '[]',
];
$record = array_merge($defaults, $record ?: []);

$image = $record['hero_image'] ?: $record['image'];
$images = m4c_available_images();

admin_header('Edit service', 'services');
admin_page_head(
    $record['id'] ? 'Edit: ' . $record['name'] : 'Add a cleaning service',
    'Descriptions, checklists, pricing, imagery and SEO for one package.',
    [
        ['label' => 'Back to services', 'href' => admin_url('services.php')],
        ['label' => 'View on site', 'href' => url('services/' . ($record['slug'] ?: 'basic-cleaning')), 'variant' => 'outline'],
    ]
);

if ($errors) {
    echo '<div class="alert alert--error"><strong>Please fix the following</strong><ul>';
    foreach ($errors as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}
?>

<form method="post" enctype="multipart/form-data" action="<?= e(admin_url('service-edit.php')) ?>" class="stack">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">

    <section class="panel">
        <div class="panel__head"><h2>Identity</h2></div>
        <div class="form-grid form-grid--2">
            <div class="field">
                <label for="s-name">Service name</label>
                <input type="text" id="s-name" name="name" required value="<?= e($record['name']) ?>" placeholder="Deep Clean">
            </div>
            <div class="field">
                <label for="s-slug">URL slug</label>
                <input type="text" id="s-slug" name="slug" value="<?= e($record['slug']) ?>" placeholder="deep-cleaning">
                <p class="field__hint">Public URL: <?= e(url('services/')) ?><span id="slug-preview"><?= e($record['slug']) ?></span></p>
            </div>
            <div class="field">
                <label for="s-eyebrow">Eyebrow label</label>
                <input type="text" id="s-eyebrow" name="eyebrow" value="<?= e($record['eyebrow']) ?>" placeholder="Reset your space">
            </div>
            <div class="field">
                <label for="s-tagline">Tagline</label>
                <input type="text" id="s-tagline" name="tagline" value="<?= e($record['tagline']) ?>" placeholder="Every inch of your condo, spotless">
            </div>
        </div>
        <div class="field">
            <label for="s-summary">Card summary</label>
            <textarea id="s-summary" name="summary" rows="3"><?= e($record['summary']) ?></textarea>
            <p class="field__hint">One or two sentences. Appears on service cards and in listing meta descriptions.</p>
        </div>
        <div class="form-grid form-grid--3">
            <div class="field">
                <label for="s-price">Starting price (CAD)</label>
                <input type="text" id="s-price" name="price_from" value="<?= e((string) ($record['price_from'] === null ? '' : $record['price_from'])) ?>" placeholder="169.99">
                <p class="field__hint">Leave blank for packages with no starting price (e.g. recurring).</p>
            </div>
            <div class="field">
                <label for="s-duration">Duration note</label>
                <input type="text" id="s-duration" name="duration_note" value="<?= e($record['duration_note']) ?>">
            </div>
            <div class="field">
                <label for="s-paired">Best paired with</label>
                <input type="text" id="s-paired" name="best_paired" value="<?= e($record['best_paired']) ?>">
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel__head"><h2>Imagery</h2></div>
        <div class="image-picker">
            <div class="image-picker__preview">
                <?= m4c_media_image($image, 'Current image for ' . $record['name'], ['width' => 260, 'height' => 175]) ?>
            </div>
            <div class="image-picker__controls">
                <div class="field">
                    <label for="s-image">Choose from the media library</label>
                    <select id="s-image" name="image">
                        <option value="">— keep current / none —</option>
                        <?php foreach ($images as $file) : ?>
                            <option value="<?= e($file) ?>" <?= ($image === $file || $image === preg_replace('/\.jpg$/', '', $file)) ? 'selected' : '' ?>>
                                <?= e($file) ?>
                            </option>
                        <?php endforeach; ?>
                        <?php
                        $uploads = glob(path('uploads') . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
                        foreach ($uploads as $file) :
                            $value = 'uploads/' . basename($file);
                            ?>
                            <option value="<?= e($value) ?>" <?= $image === $value ? 'selected' : '' ?>>Upload · <?= e(basename($file)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="s-upload">Or upload a new image</label>
                    <input type="file" id="s-upload" name="image_upload" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                    <p class="field__hint">
                        JPG, PNG, WebP or SVG, up to <?= e(round((int) config('security.upload_max_bytes') / 1048576, 1)) ?> MB.
                        Wide landscape images (roughly 1600×1000) work best.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h2>Page content</h2>
            <span class="badge badge--muted">Formatting hints below</span>
        </div>

        <div class="field">
            <label for="s-intro">Introduction paragraphs</label>
            <textarea id="s-intro" name="intro" rows="8"><?= e(m4c_paragraphs_to_text(json_field($record['intro_json'], []))) ?></textarea>
            <p class="field__hint">Separate paragraphs with a blank line.</p>
        </div>

        <div class="field">
            <label for="s-who">Who it is for</label>
            <textarea id="s-who" name="who_for" rows="6"><?= e(implode("\n", json_field($record['who_for_json'], []))) ?></textarea>
            <p class="field__hint">One item per line.</p>
        </div>

        <div class="field">
            <label for="s-checklist">Cleaning checklist</label>
            <textarea id="s-checklist" name="checklist" rows="12" class="mono"><?= e(m4c_checklist_to_text(json_field($record['checklist_json'], []))) ?></textarea>
            <p class="field__hint">One room per line, in the format <code>Room | item | item | item</code>. The example below is a starting point.</p>
            <button class="btn btn--outline btn--xs" type="button" data-fill-checklist>Insert a checklist template</button>
        </div>

        <div class="field">
            <label for="s-benefits">Benefits</label>
            <textarea id="s-benefits" name="benefits" rows="6"><?= e(implode("\n", json_field($record['benefits_json'], []))) ?></textarea>
            <p class="field__hint">One item per line.</p>
        </div>

        <div class="field">
            <label for="s-faqs">Service questions</label>
            <textarea id="s-faqs" name="faqs" rows="8" class="mono"><?= e(m4c_faqs_to_text(json_field($record['faqs_json'], []))) ?></textarea>
            <p class="field__hint">One per line, in the format <code>Question | Answer</code>. These are added to FAQPage structured data.</p>
        </div>
    </section>

    <section class="panel">
        <div class="panel__head"><h2>SEO &amp; publishing</h2></div>
        <div class="field">
            <label for="s-meta-title">Meta title</label>
            <input type="text" id="s-meta-title" name="meta_title" maxlength="190" value="<?= e($record['meta_title']) ?>">
            <p class="field__hint">Aim for 50–60 characters, including the service name and “Toronto”.</p>
        </div>
        <div class="field">
            <label for="s-meta-desc">Meta description</label>
            <textarea id="s-meta-desc" name="meta_description" rows="3" maxlength="320"><?= e($record['meta_description']) ?></textarea>
            <p class="field__hint">Aim for 140–160 characters that describe the benefit, not keywords.</p>
        </div>
        <div class="form-grid form-grid--2">
            <div class="field">
                <label for="s-sort">Sort order</label>
                <input type="number" id="s-sort" name="sort_order" value="<?= (int) $record['sort_order'] ?>">
                <p class="field__hint">Lower numbers appear first.</p>
            </div>
            <div class="field">
                <label class="check">
                    <input type="checkbox" name="is_published" value="1" <?= (int) $record['is_published'] === 1 ? 'checked' : '' ?>>
                    <span class="check__box" aria-hidden="true">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                    </span>
                    <span class="check__text">Published — visible on the website</span>
                </label>
            </div>
        </div>
    </section>

    <div class="form-actions form-actions--sticky">
        <button class="btn btn--primary" type="submit">Save service</button>
        <a class="btn btn--outline" href="<?= e(admin_url('services.php')) ?>">Cancel</a>
    </div>
</form>

<?php admin_footer(); ?>
