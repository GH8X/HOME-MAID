<?php
/**
 * Admin → Administrators (owner only).
 *
 * Passwords are only ever stored as password_hash() digests. Guards prevent
 * locking yourself out: you cannot delete or deactivate your own account, and
 * the last active owner cannot be removed.
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_owner();

$table   = table('admins');
$me      = current_admin();
$editing = int_input('edit', 0, $_GET);
$errors  = [];

if (is_post()) {
    $action = input('action');

    if (!csrf_valid()) {
        $errors[] = 'Your session expired. Please reload and try again.';
    } elseif ($action === 'save') {
        $id     = int_input('id');
        $name   = clean_line(input('name'), 120);
        $email  = strtolower(clean_line(input('email'), 190));
        $role   = input('role') === 'owner' ? 'owner' : 'editor';
        $active = !empty($_POST['is_active']) ? 1 : 0;
        $pass   = (string) input('password');

        if ($name === '') {
            $errors[] = 'A name is required.';
        }
        if (!valid_email($email)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($pass !== '' && strlen($pass) < 10) {
            $errors[] = 'Passwords must be at least 10 characters.';
        }
        if ($id === (int) $me['id'] && !$active) {
            $errors[] = 'You cannot deactivate your own account.';
        }

        $duplicate = db_one('SELECT id FROM ' . $table . ' WHERE email = ? AND id <> ? LIMIT 1', [$email, $id]);
        if ($duplicate) {
            $errors[] = 'Another administrator already uses that email address.';
        }

        if (!$errors) {
            $data = ['name' => $name, 'email' => $email, 'role' => $role, 'is_active' => $active];
            if ($pass !== '') {
                $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
            }

            if ($id > 0) {
                db_update($table, $data, 'id = ?', [$id]);
                flash('success', 'Administrator updated.');
            } else {
                if ($pass === '') {
                    $errors[] = 'Set a password for the new administrator.';
                } else {
                    $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
                    $data['created_at']    = date('Y-m-d H:i:s');
                    db_insert($table, $data);
                    flash('success', 'Administrator created.');
                }
            }
        }

        if (!$errors) {
            redirect('admin/admins.php');
        }
    } elseif ($action === 'delete') {
        $id = int_input('id');
        if ($id === (int) $me['id']) {
            flash('error', 'You cannot delete your own account.');
            redirect('admin/admins.php');
        }
        $owners = (int) db_value('SELECT COUNT(*) FROM ' . $table . " WHERE role = 'owner' AND is_active = 1 AND id <> ?", [$id]);
        $isOwner = db_value('SELECT role FROM ' . $table . ' WHERE id = ? LIMIT 1', [$id]) === 'owner';
        if ($isOwner && $owners < 1) {
            flash('error', 'At least one active owner must remain.');
            redirect('admin/admins.php');
        }
        db_delete($table, 'id = ?', [$id]);
        flash('success', 'Administrator removed.');
        redirect('admin/admins.php');
    }
}

$rows   = db_available() ? db_all('SELECT * FROM ' . $table . ' ORDER BY id ASC') : [];
$record = ['id' => 0, 'name' => '', 'email' => '', 'role' => 'editor', 'is_active' => 1];
if ($editing > 0) {
    $found = db_one('SELECT * FROM ' . $table . ' WHERE id = ? LIMIT 1', [$editing]);
    if ($found) {
        $record = $found;
    }
}
$showEditor = $editing > 0 || isset($_GET['new']);

admin_header('Administrators', 'admins');
admin_page_head('Administrators', 'Only owners can manage staff accounts. Passwords are stored as one-way hashes.', [
    ['label' => 'Add administrator', 'href' => admin_url('admins.php?new=1'), 'variant' => 'primary'],
]);
?>

<?php if ($errors) : ?>
    <div class="alert alert--error">
        <strong>Please fix the following</strong>
        <ul><?php foreach ($errors as $error) : ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="admin-grid <?= $showEditor ? 'admin-grid--split' : '' ?>">
    <section class="panel">
        <div class="panel__head"><h2>Accounts <span class="badge"><?= count($rows) ?></span></h2></div>
        <?php if (!$rows) : ?>
            <p class="empty-state">No administrators found. Run the installer to create the first owner account.</p>
        <?php else : ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last sign-in</th><th class="admin-table__actions">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <td><strong><?= e($row['name']) ?></strong><?= (int) $row['id'] === (int) $me['id'] ? ' <span class="badge badge--ok">You</span>' : '' ?></td>
                            <td><?= e($row['email']) ?></td>
                            <td><?= e(ucfirst($row['role'])) ?></td>
                            <td><span class="badge <?= (int) $row['is_active'] === 1 ? 'badge--ok' : 'badge--muted' ?>"><?= (int) $row['is_active'] === 1 ? 'Active' : 'Disabled' ?></span></td>
                            <td><?= $row['last_login_at'] ? e(date('M j, Y g:ia', strtotime((string) $row['last_login_at']))) : 'Never' ?></td>
                            <td class="admin-table__actions">
                                <a class="btn btn--outline btn--xs" href="<?= e(admin_url('admins.php?edit=' . (int) $row['id'])) ?>">Edit</a>
                                <?php if ((int) $row['id'] !== (int) $me['id']) : ?>
                                    <form method="post" class="inline-form" data-confirm="Remove this administrator?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn--danger btn--xs" type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
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
                <h2><?= $editing > 0 ? 'Edit administrator' : 'New administrator' ?></h2>
                <a class="btn btn--outline btn--sm" href="<?= e(admin_url('admins.php')) ?>">Close</a>
            </div>

            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">

                <div class="field">
                    <label for="a-name">Name</label>
                    <input type="text" id="a-name" name="name" required value="<?= e($record['name']) ?>">
                </div>
                <div class="field">
                    <label for="a-email">Email</label>
                    <input type="email" id="a-email" name="email" required value="<?= e($record['email']) ?>">
                </div>
                <div class="field">
                    <label for="a-role">Role</label>
                    <select id="a-role" name="role">
                        <option value="editor" <?= $record['role'] === 'editor' ? 'selected' : '' ?>>Editor — manage content and enquiries</option>
                        <option value="owner" <?= $record['role'] === 'owner' ? 'selected' : '' ?>>Owner — full access, including administrators</option>
                    </select>
                </div>
                <div class="field">
                    <label for="a-password"><?= $editing > 0 ? 'New password (leave blank to keep the current one)' : 'Password' ?></label>
                    <input type="password" id="a-password" name="password" minlength="10" autocomplete="new-password" <?= $editing > 0 ? '' : 'required' ?>>
                    <p class="field__hint">At least 10 characters. Stored with <code>password_hash()</code> — never in plain text.</p>
                </div>
                <div class="field">
                    <label class="check">
                        <input type="checkbox" name="is_active" value="1" <?= (int) $record['is_active'] === 1 ? 'checked' : '' ?>>
                        <span class="check__box" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </span>
                        <span class="check__text">Active — can sign in to this panel</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button class="btn btn--primary" type="submit">Save administrator</button>
                    <a class="btn btn--outline" href="<?= e(admin_url('admins.php')) ?>">Cancel</a>
                </div>
            </form>
        </section>
    <?php endif; ?>
</div>

<?php admin_footer(); ?>
