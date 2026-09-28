<?php
/**
 * Admin → Media
 *
 * Uploads live in /uploads (script execution is disabled there by .htaccess)
 * and can be selected as the image for any service.
 */
require __DIR__ . '/includes/admin-bootstrap.php';
require_admin();

$uploadsDir = path(config('uploads.dir', 'uploads'));
$errors     = [];

if (is_post()) {
    $action = input('action');

    if (!csrf_valid()) {
        $errors[] = 'Your session expired. Please reload and try again.';
    } elseif ($action === 'upload') {
        $files = $_FILES['images'] ?? null;
        if (!$files || !isset($files['name'])) {
            $errors[] = 'Choose at least one file to upload.';
        } else {
            $names = (array) $files['name'];
            $count = count($names);
            $done  = 0;

            for ($i = 0; $i < $count; $i++) {
                // Re-shape the $_FILES entry so handle_image_upload() can read it.
                $_FILES['single_upload'] = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                list($saved, $error) = handle_image_upload('single_upload', 'media');
                if ($error) {
                    $errors[] = basename((string) $files['name'][$i]) . ': ' . $error;
                } elseif ($saved) {
                    $done++;
                }
            }

            unset($_FILES['single_upload']);
            if ($done > 0) {
                flash('success', $done . ' file' . ($done === 1 ? '' : 's') . ' uploaded.');
            }
        }
        if (!$errors) {
            redirect('admin/media.php');
        }
    } elseif ($action === 'delete') {
        $file = clean_line(input('file'), 255);
        if (delete_upload($file)) {
            flash('success', basename($file) . ' deleted.');
        } else {
            flash('error', 'That file could not be deleted.');
        }
        redirect('admin/media.php');
    }
}

$files = [];
if (is_dir($uploadsDir)) {
    foreach ((array) glob($uploadsDir . '/*') as $path) {
        if (!is_file($path)) {
            continue;
        }
        $name = basename($path);
        if ($name === '.htaccess') {
            continue;
        }
        $files[] = [
            'name'    => $name,
            'path'    => config('uploads.url', 'uploads') . '/' . $name,
            'size'    => filesize($path),
            'modified' => filemtime($path),
        ];
    }
    usort($files, function ($a, $b) {
        return $b['modified'] <=> $a['modified'];
    });
}

admin_header('Media', 'media');
admin_page_head('Media library', 'Images uploaded here can be selected as the featured image for any service.');
?>

<?php if ($errors) : ?>
    <div class="alert alert--error">
        <strong>Upload problem</strong>
        <ul><?php foreach ($errors as $error) : ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<section class="panel">
    <div class="panel__head"><h2>Upload images</h2></div>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="upload">
        <div class="field">
            <label for="media-files">Choose files</label>
            <input type="file" id="media-files" name="images[]" accept="image/jpeg,image/png,image/webp,image/svg+xml" multiple required>
            <p class="field__hint">
                JPG, PNG, WebP or SVG. Maximum <?= e(round((int) config('security.upload_max_bytes') / 1048576, 1)) ?> MB per file,
                minimum 200×200 pixels. Files are stored in <code>/uploads</code> with script execution disabled.
            </p>
        </div>
        <div class="form-actions">
            <button class="btn btn--primary" type="submit">Upload</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel__head">
        <h2>Uploaded files <span class="badge"><?= count($files) ?></span></h2>
    </div>
    <?php if (!$files) : ?>
        <p class="empty-state">Nothing uploaded yet. The site ships with its own photography in <code>assets/images</code>.</p>
    <?php else : ?>
        <ul class="media-grid">
            <?php foreach ($files as $file) : ?>
                <li class="media-card">
                    <div class="media-card__preview">
                        <?php if (preg_match('/\.svg$/i', $file['name'])) : ?>
                            <img src="<?= e(upload_url($file['name'])) ?>" alt="" width="200" height="140" loading="lazy" decoding="async">
                        <?php else : ?>
                            <img src="<?= e(upload_url($file['name'])) ?>" alt="" width="200" height="140" loading="lazy" decoding="async">
                        <?php endif; ?>
                    </div>
                    <div class="media-card__body">
                        <strong><?= e($file['name']) ?></strong>
                        <small><?= e(round($file['size'] / 1024)) ?> KB · <?= e(date('M j, Y', (int) $file['modified'])) ?></small>
                        <code><?= e($file['path']) ?></code>
                    </div>
                    <form method="post" data-confirm="Delete this file? Any service using it will lose its image.">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="file" value="<?= e($file['path']) ?>">
                        <button class="btn btn--danger btn--xs" type="submit">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel__head"><h2>Bundled photography</h2></div>
    <p class="panel__intro">
        These images ship with the site in <code>assets/images</code>. Replace any of them with your own team and
        client photos — keep the filename identical and the site updates everywhere. Attribution details are on the
        <a href="<?= e(url('image-credits')) ?>" target="_blank" rel="noopener">photography credits page</a>.
    </p>
    <ul class="media-grid media-grid--compact">
        <?php foreach (m4c_available_images() as $image) : ?>
            <li class="media-card">
                <div class="media-card__preview">
                    <img src="<?= e(url('assets/images/' . $image)) ?>" alt="" width="200" height="140" loading="lazy" decoding="async">
                </div>
                <div class="media-card__body">
                    <strong><?= e($image) ?></strong>
                    <code>assets/images/<?= e($image) ?></code>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php admin_footer(); ?>
