<?php
/**
 * Admin → Messages — contact form submissions.
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$table    = table('messages');
$statuses = ['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'closed' => 'Closed', 'spam' => 'Spam'];

if (is_post() && input('action') === 'update') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $id = int_input('id');
        if ($id > 0) {
            db_update($table, [
                'status'     => in_allowlist(input('status'), array_keys($statuses), 'new'),
                'admin_note' => clean_text(input('admin_note'), 4000),
            ], 'id = ?', [$id]);
            flash('success', 'Message updated.');
        }
    }
    redirect('admin/messages.php?view=' . int_input('id'));
}

if (is_post() && input('action') === 'delete') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $id = int_input('id');
        if ($id > 0) {
            db_delete($table, 'id = ?', [$id]);
            flash('success', 'Message deleted.');
        }
    }
    redirect('admin/messages.php');
}

$viewId = int_input('view', 0, $_GET);
$record = $viewId > 0 ? db_one('SELECT * FROM ' . $table . ' WHERE id = ? LIMIT 1', [$viewId]) : null;

if ($record && $record['status'] === 'new') {
    db_update($table, ['status' => 'read'], 'id = ?', [$record['id']]);
    $record['status'] = 'read';
}

$rows = db_available()
    ? db_all('SELECT * FROM ' . $table . ' ORDER BY id DESC LIMIT 200')
    : [];

admin_header('Messages', 'messages');
admin_page_head('Contact messages', 'Everything sent through the contact form.');
?>

<?php if (!db_available()) : ?>
    <div class="alert alert--warn">
        <strong>No database connected.</strong>
        <p>Messages are emailed to <?= e(setting('email', 'info@maid4condos.com')) ?> but are not being stored.</p>
    </div>
<?php elseif ($record) : ?>
    <section class="panel">
        <div class="panel__head">
            <h2><?= e($record['name']) ?> <span class="badge badge--<?= $record['status'] === 'new' ? 'ok' : 'muted' ?>"><?= e($statuses[$record['status']] ?? $record['status']) ?></span></h2>
            <a class="btn btn--outline btn--sm" href="<?= e(admin_url('messages.php')) ?>">Back to list</a>
        </div>

        <dl class="detail-list">
            <div><dt>Received</dt><dd><?= e(date('D, M j, Y g:ia', strtotime((string) $record['created_at']))) ?></dd></div>
            <div><dt>Email</dt><dd><a href="mailto:<?= e($record['email']) ?>"><?= e($record['email']) ?></a></dd></div>
            <div><dt>Phone</dt><dd><?= $record['phone'] ? '<a href="tel:' . e($record['phone']) . '">' . e($record['phone']) . '</a>' : '—' ?></dd></div>
            <div><dt>Topic</dt><dd><?= e($record['subject']) ?></dd></div>
        </dl>

        <div class="detail-note">
            <h3>Message</h3>
            <p><?= nl2br(e($record['message'])) ?></p>
        </div>

        <form method="post" class="stack">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
            <div class="form-grid form-grid--2">
                <div class="field">
                    <label for="m-status">Status</label>
                    <select id="m-status" name="status">
                        <?php foreach ($statuses as $value => $label) : ?>
                            <option value="<?= e($value) ?>" <?= $record['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="m-note">Internal note</label>
                    <input type="text" id="m-note" name="admin_note" value="<?= e((string) $record['admin_note']) ?>">
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn--primary" type="submit">Save changes</button>
                <a class="btn btn--outline" href="mailto:<?= e($record['email']) ?>">Reply by email</a>
            </div>
        </form>
    </section>

    <section class="panel panel--danger">
        <div class="panel__head"><h2>Danger zone</h2></div>
        <form method="post" data-confirm="Delete this message permanently?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
            <button class="btn btn--danger btn--sm" type="submit">Delete this message</button>
        </form>
    </section>
<?php else : ?>
    <section class="panel">
        <?php if (!$rows) : ?>
            <p class="empty-state">No messages yet.</p>
        <?php else : ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>Received</th><th>Name</th><th>Email</th><th>Topic</th><th>Status</th><th class="admin-table__actions">Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <td><?= e(date('M j, g:ia', strtotime((string) $row['created_at']))) ?></td>
                            <td><strong><?= e($row['name']) ?></strong></td>
                            <td><a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a></td>
                            <td><?= e($row['subject']) ?></td>
                            <td><span class="badge badge--<?= $row['status'] === 'new' ? 'ok' : 'muted' ?>"><?= e($statuses[$row['status']] ?? $row['status']) ?></span></td>
                            <td class="admin-table__actions">
                                <a class="btn btn--outline btn--xs" href="<?= e(admin_url('messages.php?view=' . (int) $row['id'])) ?>">Open</a>
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
