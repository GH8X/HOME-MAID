<?php
/**
 * Breadcrumbs.
 *
 * @var array $crumbs Ordered map of label => route (route may be empty for the
 *                    current page). The matching BreadcrumbList JSON-LD is
 *                    emitted by the page into the head schema array.
 */
$crumbs = isset($crumbs) ? $crumbs : [];
if (count($crumbs) < 2) {
    return;
}
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
        <?php $last = count($crumbs) - 1; $i = 0; ?>
        <?php foreach ($crumbs as $label => $route) : ?>
            <li>
                <?php if ($route && $i !== $last) : ?>
                    <a href="<?= e(url($route)) ?>"><?= e($label) ?></a>
                <?php else : ?>
                    <span aria-current="page"><?= e($label) ?></span>
                <?php endif; ?>
                <?php if ($i !== $last) : ?>
                    <?php partial('icon', ['name' => 'arrow-right', 'size' => 13]); ?>
                <?php endif; ?>
            </li>
            <?php $i++; ?>
        <?php endforeach; ?>
    </ol>
</nav>
