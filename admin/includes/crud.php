<?php
/**
 * Admin CRUD engine.
 *
 * Powers the simple content types (FAQs, testimonials, service areas, add-ons,
 * schedules, navigation) from one place so every screen behaves identically:
 * create, edit, delete, publish and unpublish — all CSRF protected and using
 * prepared statements only.
 */

/** Definition of every type managed by the generic engine. */
function m4c_crud_types()
{
    return [
        'faqs' => [
            'title'    => 'Frequently asked questions',
            'singular' => 'question',
            'icon'     => 'help',
            'intro'    => 'Questions shown on the FAQ page, the homepage preview and inside FAQPage structured data.',
            'fields'   => [
                'category'   => ['label' => 'Category', 'type' => 'text', 'required' => true, 'hint' => 'Questions are grouped by category on the FAQ page.'],
                'question'   => ['label' => 'Question', 'type' => 'text', 'required' => true, 'maxlength' => 320],
                'answer'     => ['label' => 'Answer', 'type' => 'textarea', 'required' => true],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number'],
            ],
            'list' => ['category', 'question'],
        ],

        'testimonials' => [
            'title'    => 'Testimonials',
            'singular' => 'testimonial',
            'icon'     => 'quote',
            'intro'    => 'Only publish genuine reviews. Leave the rating blank unless a real star rating was given.',
            'fields'   => [
                'name'       => ['label' => 'Client name', 'type' => 'text', 'required' => true],
                'location'   => ['label' => 'Location', 'type' => 'text', 'hint' => 'e.g. Liberty Village, Toronto'],
                'service'    => ['label' => 'Service (optional)', 'type' => 'text', 'hint' => 'e.g. Bi-weekly recurring cleaning'],
                'quote'      => ['label' => 'Review', 'type' => 'textarea', 'required' => true],
                'rating'     => ['label' => 'Rating out of 5 (optional)', 'type' => 'number', 'nullable' => true, 'hint' => 'Leave blank unless the client gave a star rating.'],
                'source'     => ['label' => 'Source (optional)', 'type' => 'text', 'hint' => 'e.g. Google, Yelp, maid4condos.com'],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number'],
            ],
            'list' => ['name', 'location', 'service'],
        ],

        'areas' => [
            'title'    => 'Service areas',
            'singular' => 'area',
            'icon'     => 'map',
            'intro'    => 'Neighbourhoods shown on the homepage, contact page, footer and in LocalBusiness areaServed data.',
            'fields'   => [
                'name'       => ['label' => 'Neighbourhood or region', 'type' => 'text', 'required' => true],
                'note'       => ['label' => 'Short note', 'type' => 'text'],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number'],
            ],
            'list' => ['name', 'note'],
        ],

        'extras' => [
            'title'    => 'Add-on services',
            'singular' => 'add-on',
            'icon'     => 'spray',
            'intro'    => 'Extras customers can add to a cleaning. Published extras appear on the services page and in the quote form.',
            'fields'   => [
                'slug'       => ['label' => 'Slug', 'type' => 'text', 'required' => true, 'hint' => 'Lowercase, dashes only — used in the quote form.'],
                'name'       => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'summary'    => ['label' => 'Summary', 'type' => 'textarea'],
                'details'    => ['label' => 'Details', 'type' => 'textarea'],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number'],
            ],
            'list' => ['name', 'slug'],
        ],

        'frequencies' => [
            'title'    => 'Cleaning schedules',
            'singular' => 'schedule',
            'icon'     => 'calendar',
            'intro'    => 'AutoPilot frequencies and their discounts, shown on the services and quote pages.',
            'fields'   => [
                'slug'        => ['label' => 'Slug', 'type' => 'text', 'required' => true],
                'label'       => ['label' => 'Label', 'type' => 'text', 'required' => true],
                'discount'    => ['label' => 'Discount text', 'type' => 'text', 'hint' => 'e.g. 15% off'],
                'note'        => ['label' => 'Note', 'type' => 'text'],
                'recommended' => ['label' => 'Mark as recommended', 'type' => 'checkbox'],
                'sort_order'  => ['label' => 'Sort order', 'type' => 'number'],
            ],
            'list' => ['label', 'discount'],
        ],

        'navigation' => [
            'title'    => 'Primary navigation',
            'singular' => 'link',
            'icon'     => 'list',
            'intro'    => 'Header and footer navigation. Routes are relative — for example <code>services</code> or <code>faq</code>.',
            'fields'   => [
                'label'      => ['label' => 'Label', 'type' => 'text', 'required' => true],
                'route'      => ['label' => 'Route', 'type' => 'text', 'required' => true],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number'],
            ],
            'list' => ['label', 'route'],
        ],
    ];
}

/**
 * Database table backing a CRUD type.
 *
 * Most types share their name with the table, but the service areas screen has
 * to reach the service_areas table (there is no "areas" table).
 */
function m4c_crud_table($type)
{
    $map = ['areas' => 'service_areas'];

    return table($map[$type] ?? $type);
}

function m4c_crud_config($type)
{
    $types = m4c_crud_types();
    if (!isset($types[$type])) {
        return null;
    }

    return $types[$type];
}

/** Column name used for the publish toggle. */
function m4c_crud_has_published($type)
{
    return in_array($type, ['faqs', 'testimonials', 'areas', 'extras', 'frequencies', 'navigation'], true);
}

/**
 * Process a CRUD POST request.
 *
 * @return array{ok:bool,errors:array,values:array,id:int}
 */
function m4c_crud_handle($type)
{
    $config = m4c_crud_config($type);
    $result = ['ok' => false, 'errors' => [], 'values' => [], 'id' => 0];

    if (!$config || !is_post()) {
        return $result;
    }

    $action = input('action');
    if ($action === '') {
        return $result;
    }

    if (!csrf_valid()) {
        $result['errors'][] = 'Your session expired. Please reload and try again.';

        return $result;
    }

    $table = m4c_crud_table($type);
    $id    = int_input('id');

    if ($action === 'delete' && $id > 0) {
        db_delete($table, 'id = ?', [$id]);
        flash('success', ucfirst($config['singular']) . ' deleted.');
        redirect('admin/' . $type . '.php');
    }

    if ($action === 'toggle' && $id > 0 && m4c_crud_has_published($type)) {
        $current = db_one('SELECT is_published FROM ' . $table . ' WHERE id = ? LIMIT 1', [$id]);
        if ($current) {
            $next = (int) $current['is_published'] === 1 ? 0 : 1;
            db_update($table, ['is_published' => $next], 'id = ?', [$id]);
            flash('success', $next === 1 ? 'Published.' : 'Unpublished.');
        }
        redirect('admin/' . $type . '.php');
    }

    if ($action !== 'save') {
        return $result;
    }

    $data   = [];
    $values = [];
    foreach ($config['fields'] as $field => $meta) {
        $raw = $_POST[$field] ?? '';
        if ($meta['type'] === 'checkbox') {
            $value = !empty($raw) ? 1 : 0;
        } elseif ($meta['type'] === 'number') {
            // Blank optional numbers (a star rating) stay NULL, but ordering
            // columns are NOT NULL so they fall back to 0 rather than failing.
            $value = $raw === '' ? (empty($meta['nullable']) ? 0 : null) : (int) $raw;
        } elseif ($meta['type'] === 'textarea') {
            $value = clean_text($raw, 6000);
        } else {
            $value = clean_line($raw, $meta['maxlength'] ?? 320);
        }

        if ($field === 'slug' || $field === 'route') {
            $value = slugify($value);
        }

        if (!empty($meta['required']) && ($value === '' || $value === null)) {
            $result['errors'][] = $meta['label'] . ' is required.';
        }

        $values[$field] = $value;
        $data[$field]   = $value;
    }

    if (m4c_crud_has_published($type)) {
        $data['is_published'] = !empty($_POST['is_published']) ? 1 : 0;
        $values['is_published'] = $data['is_published'];
    }

    $result['values'] = $values;

    if ($result['errors']) {
        return $result;
    }

    try {
        if ($id > 0) {
            db_update($table, $data, 'id = ?', [$id]);
            $result['id'] = $id;
            flash('success', ucfirst($config['singular']) . ' updated.');
        } else {
            $result['id'] = db_insert($table, $data);
            flash('success', ucfirst($config['singular']) . ' created.');
        }
        $result['ok'] = true;
    } catch (Throwable $e) {
        $result['errors'][] = config('app.debug')
            ? 'Database error: ' . $e->getMessage()
            : 'That could not be saved. Please try again.';

        return $result;
    }

    redirect('admin/' . $type . '.php');
}

/** Render one form control. */
function m4c_crud_field($field, array $meta, $value)
{
    $id = 'f-' . preg_replace('/[^a-z0-9_]/i', '', $field);
    echo '<div class="field field--' . e($meta['type']) . '">';
    if ($meta['type'] === 'checkbox') {
        printf(
            '<label class="check"><input type="checkbox" id="%s" name="%s" value="1" %s><span class="check__box" aria-hidden="true">%s</span><span class="check__text">%s</span></label>',
            e($id),
            e($field),
            !empty($value) ? 'checked' : '',
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>',
            e($meta['label'])
        );
    } else {
        echo '<label for="' . e($id) . '">' . e($meta['label']) . '</label>';
        $attrs = 'id="' . e($id) . '" name="' . e($field) . '"';
        if (!empty($meta['required'])) {
            $attrs .= ' required';
        }
        if (!empty($meta['maxlength'])) {
            $attrs .= ' maxlength="' . (int) $meta['maxlength'] . '"';
        }
        if ($meta['type'] === 'textarea') {
            echo '<textarea ' . $attrs . ' rows="5">' . e((string) $value) . '</textarea>';
        } else {
            $type = $meta['type'] === 'number' ? 'number' : 'text';
            echo '<input type="' . e($type) . '" ' . $attrs . ' value="' . e((string) $value) . '">';
        }
    }
    if (!empty($meta['hint'])) {
        echo '<p class="field__hint">' . $meta['hint'] . '</p>';
    }
    echo '</div>';
}

/** Render the whole screen: list + editor. */
function m4c_crud_render($type)
{
    $config = m4c_crud_config($type);
    $table  = m4c_crud_table($type);

    $editingId = int_input('edit', 0, $_GET);
    $isNew     = isset($_GET['new']);
    $rows      = db_all('SELECT * FROM ' . $table . ' ORDER BY sort_order ASC, id ASC');
    $record    = ['id' => 0];

    if ($editingId > 0) {
        $record = db_one('SELECT * FROM ' . $table . ' WHERE id = ? LIMIT 1', [$editingId]);
        if (!$record) {
            flash('error', 'That record no longer exists.');
            redirect('admin/' . $type . '.php');
        }
    } elseif ($isNew) {
        foreach ($config['fields'] as $field => $meta) {
            $record[$field] = $meta['type'] === 'checkbox' ? 0 : '';
        }
        $record['is_published'] = 1;
    }

    $showEditor = $editingId > 0 || $isNew;
    ?>
    <div class="admin-grid <?= $showEditor ? 'admin-grid--split' : '' ?>">
        <section class="panel">
            <div class="panel__head">
                <h2><?= e($config['title']) ?> <span class="badge"><?= count($rows) ?></span></h2>
                <a class="btn btn--primary btn--sm" href="<?= e(admin_url($type . '.php?new=1')) ?>">Add <?= e($config['singular']) ?></a>
            </div>
            <p class="panel__intro"><?= $config['intro'] ?></p>

            <?php if (!$rows) : ?>
                <p class="empty-state">Nothing here yet. Use “Add <?= e($config['singular']) ?>” to create the first entry.</p>
            <?php else : ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <?php foreach ($config['list'] as $column) : ?>
                                    <th><?= e($config['fields'][$column]['label'] ?? ucfirst($column)) ?></th>
                                <?php endforeach; ?>
                                <?php if (m4c_crud_has_published($type)) : ?><th>Status</th><?php endif; ?>
                                <th class="admin-table__actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row) : ?>
                            <tr>
                                <?php foreach ($config['list'] as $column) : ?>
                                    <td><?= e((string) $row[$column]) ?></td>
                                <?php endforeach; ?>
                                <?php if (m4c_crud_has_published($type)) : ?>
                                    <td>
                                        <span class="badge <?= (int) $row['is_published'] === 1 ? 'badge--ok' : 'badge--muted' ?>">
                                            <?= (int) $row['is_published'] === 1 ? 'Published' : 'Draft' ?>
                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td class="admin-table__actions">
                                    <a class="btn btn--outline btn--xs" href="<?= e(admin_url($type . '.php?edit=' . (int) $row['id'])) ?>">Edit</a>
                                    <?php if (m4c_crud_has_published($type)) : ?>
                                        <form method="post" class="inline-form">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button class="btn btn--ghost-dark btn--xs" type="submit">
                                                <?= (int) $row['is_published'] === 1 ? 'Unpublish' : 'Publish' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" class="inline-form" data-confirm="Delete this <?= e($config['singular']) ?>? This cannot be undone.">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn--danger btn--xs" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($showEditor) : ?>
            <section class="panel panel--editor">
                <div class="panel__head">
                    <h2><?= $editingId > 0 ? 'Edit' : 'Add' ?> <?= e($config['singular']) ?></h2>
                    <a class="btn btn--outline btn--sm" href="<?= e(admin_url($type . '.php')) ?>">Close</a>
                </div>

                <form method="post" action="<?= e(admin_url($type . '.php')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">

                    <?php foreach ($config['fields'] as $field => $meta) : ?>
                        <?php m4c_crud_field($field, $meta, $record[$field] ?? ''); ?>
                    <?php endforeach; ?>

                    <?php if (m4c_crud_has_published($type)) : ?>
                        <div class="field">
                            <label class="check">
                                <input type="checkbox" name="is_published" value="1" <?= (int) ($record['is_published'] ?? 1) === 1 ? 'checked' : '' ?>>
                                <span class="check__box" aria-hidden="true">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                                </span>
                                <span class="check__text">Published — visible on the website</span>
                            </label>
                        </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <button class="btn btn--primary" type="submit">Save <?= e($config['singular']) ?></button>
                        <a class="btn btn--outline" href="<?= e(admin_url($type . '.php')) ?>">Cancel</a>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </div>
    <?php
}
