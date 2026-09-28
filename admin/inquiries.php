<?php
/**
 * Admin → Quote requests
 *
 * Lists every quote submitted through /get-a-quote, with status tracking,
 * internal notes, deletion and a CSV export.
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$table   = table('inquiries');
$statuses = ['new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'booked' => 'Booked', 'closed' => 'Closed', 'spam' => 'Spam'];

// --- CSV export ---------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    if (!db_available()) {
        flash('error', 'The database is not connected.');
        redirect('admin/inquiries.php');
    }
    $rows = db_all('SELECT * FROM ' . $table . ' ORDER BY id DESC');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="maid4condos-quote-requests-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($rows) {
        fputcsv($out, array_keys($rows[0]));
        foreach ($rows as $row) {
            fputcsv($out, array_values($row));
        }
    } else {
        fputcsv($out, ['No quote requests recorded.']);
    }
    fclose($out);
    exit;
}

// --- Update ------------------------------------------------------------------
if (is_post() && input('action') === 'update') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $id     = int_input('id');
        $status = in_allowlist(input('status'), array_keys($statuses), 'new');
        if ($id > 0) {
            db_update($table, [
                'status'     => $status,
                'admin_note' => clean_text(input('admin_note'), 4000),
            ], 'id = ?', [$id]);
            flash('success', 'Quote request updated.');
        }
    }
    redirect('admin/inquiries.php?view=' . int_input('id'));
}

// --- Delete ------------------------------------------------------------------
if (is_post() && input('action') === 'delete') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $id = int_input('id');
        if ($id > 0) {
            db_delete($table, 'id = ?', [$id]);
            flash('success', 'Quote request deleted.');
        }
    }
    redirect('admin/inquiries.php');
}

$viewId = int_input('view', 0, $_GET);
$record = $viewId > 0 ? db_one('SELECT * FROM ' . $table . ' WHERE id = ? LIMIT 1', [$viewId]) : null;

$filterStatus = clean_line(input('status', '', $_GET), 20);
$search       = clean_line(input('q', '', $_GET), 120);

if (db_available()) {
    $sql    = 'SELECT * FROM ' . $table;
    $params = [];
    $where  = [];
    if (isset($statuses[$filterStatus])) {
        $where[]  = 'status = ?';
        $params[] = $filterStatus;
    }
    if ($search !== '') {
        $where[]  = '(name LIKE ? OR email LIKE ? OR neighbourhood LIKE ?)';
        $like     = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY id DESC LIMIT 200';
    $rows = db_all($sql, $params);
} else {
    $rows = [];
}

admin_header('Quote requests', 'inquiries');
admin_page_head('Quote requests', 'Every request submitted through the quote form, newest first.', [
    ['label' => 'Export CSV', 'href' => admin_url('inquiries.php?export=csv')],
]);
?>

<?php if (!db_available()) : ?>
    <div class="alert alert--warn">
        <strong>No database connected.</strong>
        <p>Quote requests are emailed to <?= e(setting('booking_email', 'bookings@maid4condos.com')) ?> but are not being stored. Connect MySQL to record them here.</p>
    </div>
<?php elseif ($record) : ?>
    <section class="panel">
        <div class="panel__head">
            <h2>
                <?= e($record['name']) ?>
                <span class="badge badge--<?= $record['status'] === 'new' ? 'ok' : 'muted' ?>"><?= e($statuses[$record['status']] ?? $record['status']) ?></span>
            </h2>
            <a class="btn btn--outline btn--sm" href="<?= e(admin_url('inquiries.php')) ?>">Back to list</a>
        </div>

        <div class="detail-grid">
            <dl class="detail-list">
                <div><dt>Received</dt><dd><?= e(date('D, M j, Y g:ia', strtotime((string) $record['created_at']))) ?></dd></div>
                <div><dt>Email</dt><dd><a href="mailto:<?= e($record['email']) ?>"><?= e($record['email']) ?></a></dd></div>
                <div><dt>Phone</dt><dd><a href="tel:<?= e($record['phone']) ?>"><?= e($record['phone']) ?></a></dd></div>
                <div><dt>Neighbourhood</dt><dd><?= e($record['neighbourhood']) ?><?= $record['postal_code'] ? ' · ' . e($record['postal_code']) : '' ?></dd></div>
                <div><dt>Property</dt><dd><?= e(ucfirst((string) $record['property_type'])) ?> · <?= e((string) $record['bedrooms']) ?> bed · <?= e((string) $record['bathrooms']) ?> bath · <?= (int) $record['sqft'] ?> ft²</dd></div>
                <div><dt>Service</dt><dd><?= e($record['service_name']) ?></dd></div>
                <div><dt>Frequency</dt><dd><?= e($record['frequency_label']) ?></dd></div>
                <div>
                    <dt>Add-ons</dt>
                    <dd>
                        <?php
                        $extras = json_field($record['extras_json'], []);
                        echo $extras
                            ? e(implode(', ', array_map(function ($slug) {
                                return label_for_slug(extras_all(), $slug, (string) $slug);
                            }, $extras)))
                            : '—';
                        ?>
                    </dd>
                </div>
                <div><dt>Preferred date</dt><dd><?= e($record['preferred_date'] ?: '—') ?></dd></div>
                <div><dt>Arrival window</dt><dd><?= e($record['preferred_time'] ? label_for_slug(quote_time_options(), $record['preferred_time'], $record['preferred_time']) : '—') ?></dd></div>
                <div><dt>Access</dt><dd><?= e($record['access_method'] ? label_for_slug(quote_access_options(), $record['access_method'], $record['access_method']) : '—') ?></dd></div>
                <div><dt>Source</dt><dd><?= e($record['source_page']) ?></dd></div>
            </dl>

            <div class="detail-note">
                <h3>Customer notes</h3>
                <p><?= $record['message'] !== '' ? nl2br(e($record['message'])) : '—' ?></p>
            </div>
        </div>

        <form method="post" class="stack">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
            <div class="form-grid form-grid--2">
                <div class="field">
                    <label for="i-status">Status</label>
                    <select id="i-status" name="status">
                        <?php foreach ($statuses as $value => $label) : ?>
                            <option value="<?= e($value) ?>" <?= $record['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="i-note">Internal note</label>
                    <input type="text" id="i-note" name="admin_note" value="<?= e((string) $record['admin_note']) ?>" placeholder="Quoted $185, booked for Tuesday">
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn--primary" type="submit">Save changes</button>
                <a class="btn btn--outline" href="mailto:<?= e($record['email']) ?>?subject=<?= e(rawurlencode('Your Maid4Condos quote')) ?>">Reply by email</a>
            </div>
        </form>
    </section>

    <section class="panel panel--danger">
        <div class="panel__head"><h2>Danger zone</h2></div>
        <form method="post" data-confirm="Delete this quote request permanently?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
            <button class="btn btn--danger btn--sm" type="submit">Delete this request</button>
        </form>
    </section>
<?php else : ?>
    <section class="panel">
        <form class="filters" method="get" action="<?= e(admin_url('inquiries.php')) ?>">
            <div class="field">
                <label for="f-status">Status</label>
                <select id="f-status" name="status">
                    <option value="">All statuses</option>
                    <?php foreach ($statuses as $value => $label) : ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="f-q">Search</label>
                <input type="search" id="f-q" name="q" value="<?= e($search) ?>" placeholder="Name, email or neighbourhood">
            </div>
            <button class="btn btn--outline btn--sm" type="submit">Filter</button>
        </form>

        <?php if (!$rows) : ?>
            <p class="empty-state">No quote requests match. New submissions appear here automatically.</p>
        <?php else : ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Received</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Area</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th class="admin-table__actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <td><?= e(date('M j, g:ia', strtotime((string) $row['created_at']))) ?></td>
                            <td><strong><?= e($row['name']) ?></strong></td>
                            <td>
                                <a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a><br>
                                <small class="table-sub"><?= e($row['phone']) ?></small>
                            </td>
                            <td><?= e($row['neighbourhood']) ?></td>
                            <td>
                                <?= e($row['service_name']) ?><br>
                                <small class="table-sub"><?= e($row['frequency_label']) ?></small>
                            </td>
                            <td><span class="badge badge--<?= $row['status'] === 'new' ? 'ok' : 'muted' ?>"><?= e($statuses[$row['status']] ?? $row['status']) ?></span></td>
                            <td class="admin-table__actions">
                                <a class="btn btn--outline btn--xs" href="<?= e(admin_url('inquiries.php?view=' . (int) $row['id'])) ?>">Open</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php admin_footer(); ?>
