<?php
/**
 * FAQ accordion.
 *
 * Uses native <details>/<summary> so answers are reachable — and readable by
 * search engines — with JavaScript disabled. assets/js/main.js adds the
 * single-open behaviour and smooth height animation as progressive
 * enhancement.
 *
 * @var array  $faqs    List of ['question' => ..., 'answer' => ...].
 * @var string $context Optional id prefix so multiple accordions can coexist.
 * @var bool   $grouped Group questions by category when categories are present.
 */
$faqs    = isset($faqs) ? $faqs : [];
$context = isset($context) ? $context : 'faq';
$grouped = !empty($grouped) && isset($faqs[0]['category']);

$renderList = function (array $items, $prefix) {
    echo '<div class="faq-list" data-accordion data-accordion-group="' . e($prefix) . '">';
    foreach ($items as $index => $faq) {
        $id = $prefix . '-' . ($index + 1);
        ?>
        <details class="faq" id="<?= e($id) ?>">
            <summary class="faq__summary">
                <span class="faq__question"><?= e($faq['question']) ?></span>
                <span class="faq__toggle" aria-hidden="true">
                    <?php partial('icon', ['name' => 'plus', 'size' => 18]); ?>
                </span>
            </summary>
            <div class="faq__body">
                <p><?= e($faq['answer']) ?></p>
            </div>
        </details>
        <?php
    }
    echo '</div>';
};

if (!$faqs) {
    return;
}

if ($grouped) {
    $byCategory = [];
    foreach ($faqs as $faq) {
        $byCategory[$faq['category']][] = $faq;
    }
    ?>
    <div class="faq-groups" data-faq-filter>
        <div class="faq-filter" role="tablist" aria-label="FAQ categories">
            <button class="faq-filter__btn is-active" type="button" role="tab" aria-selected="true" data-faq-filter-btn="all">
                All questions
            </button>
            <?php foreach (array_keys($byCategory) as $category) : ?>
                <button class="faq-filter__btn" type="button" role="tab" aria-selected="false"
                        data-faq-filter-btn="<?= e(slugify($category)) ?>">
                    <?= e($category) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($byCategory as $category => $items) : ?>
            <section class="faq-group" data-faq-group="<?= e(slugify($category)) ?>">
                <h2 class="faq-group__title">
                    <?php partial('icon', ['name' => 'help', 'size' => 18]); ?>
                    <?= e($category) ?>
                </h2>
                <?php $renderList($items, $context . '-' . slugify($category)); ?>
            </section>
        <?php endforeach; ?>
    </div>
    <?php
} else {
    $renderList($faqs, $context);
}
