<?php
/**
 * Admin → Services (list)
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$table = table('services');

// Delete ---------------------------------------------------------------------
if (is_post() && input('action') === 'delete') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $id = int_input('id');
        if ($id > 0) {
            $slug = db_value('SELECT slug FROM ' . $table . ' WHERE id = ? LIMIT 1', [$id]);
            db_delete($table, 'id = ?', [$id]);
            flash('success', 'Service deleted' . ($slug ? ': ' . $slug : '') . '.');
        }
    }
    redirect('admin/services.php');
}

// Publish / unpublish --------------------------------------------------------
if (is_post() && input('action') === 'toggle') {
    if (!csrf_valid()) {
        flash('error', 'Your session expired. Please try again.');
    } else {
        $id = int_input('id');
        $current = db_one('SELECT is_published FROM ' . $table . ' WHERE id = ? LIMIT 1', [$id]);
        if ($current) {
            db_update($table, ['is_published' => (int) $current['is_published'] === 1 ? 0 : 1], 'id = ?', [$id]);
            flash('success', (int) $current['is_published'] === 1 ? 'Service unpublished.' : 'Service published.');
        }
    }
    redirect('admin/services.php');
}

$services = services_all(true);

admin_header('Services', 'services');
admin_page_head('Cleaning services', 'Full control over every package: descriptions, checklists, pricing, imagery and SEO.', [
    ['label' => 'Add a service', 'href' => admin_url('service-edit.php?new=1'), 'variant' => 'primary'],
]);
?>

<section class="panel">
    <?php if (!$services) : ?>
        <p class="empty-state">No services found. Import the default content from the Overview screen to begin.</p>
    <?php else : ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Service</th>
                        <th>From</th>
                        <th>Checklist</th>
                        <th>Status</th>
                        <th class="admin-table__actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $service) : ?>
                    <tr>
                        <td class="admin-thumb">
                            <?= m4c_media_image($service['hero_image'], '', ['width' => 88, 'height' => 60]) ?>
                        </td>
                        <td>
                            <strong><?= e($service['name']) ?></strong>
                            <small class="table-sub">/services/<?= e($service['slug']) ?></small>
                        </td>
                        <td><?= $service['price_from'] ? e(money($service['price_from'])) : '—' ?></td>
                        <td><?= count((array) $service['checklist']) ?> rooms · <?= count((array) $service['benefits']) ?> benefits</td>
                        <td>
                            <span class="badge <?= (int) $service['is_published'] === 1 ? 'badge--ok' : 'badge--muted' ?>">
                                <?= (int) $service['is_published'] === 1 ? 'Published' : 'Draft' ?>
                            </span>
                        </td>
                        <td class="admin-table__actions">
                            <a class="btn btn--outline btn--xs" href="<?= e(admin_url('service-edit.php?id=' . (int) $service['id'])) ?>">
                                <?= $service['id'] ? 'Edit' : 'Import to edit' ?>
                            </a>
                            <?php if ($service['id']) : ?>
                                <a class="btn btn--ghost-dark btn--xs" href="<?= e(url('services/' . $service['slug'])) ?>" target="_blank" rel="noopener">View</a>
                                <form method="post" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                                    <button class="btn btn--ghost-dark btn--xs" type="submit">
                                        <?= (int) $service['is_published'] === 1 ? 'Unpublish' : 'Publish' ?>
                                    </button>
                                </form>
                                <form method="post" class="inline-form" data-confirm="Delete this service? This cannot be undone.">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
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

    <?php if (!db_available()) : ?>
        <div class="alert alert--info">The database is not connected, so these services are read-only and come from the built-in defaults. Import them to make them editable.</div>
    <?php endif; ?>
</section>

<?php admin_footer(); ?>
