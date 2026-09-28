<?php
/**
 * Photography credits — required attribution for the CC BY imagery used on the
 * site. The same data is mirrored in assets/images/CREDITS.md.
 */
$biz = business();

$images = [
    'hero-cleaning-modern-home.jpg' => ['Person cleaning living room floor with vacuum cleaner in modern home', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Person_cleaning_living_room_floor_with_vacuum_cleaner_in_modern_home.jpg'],
    'cleaning-kitchen-stove.jpg'    => ['Woman cleaning kitchen stove with spray cleaner and cloth in bright kitchen setting', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Woman_cleaning_kitchen_stove_with_spray_cleaner_and_cloth_in_bright_kitchen_setting.jpg'],
    'cleaning-kitchen-cabinets.jpg' => ['Woman cleaning kitchen cabinets in a bright home', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Woman_cleaning_kitchen_cabinets_in_a_bright_home.jpg'],
    'cleaning-vacuum-kitchen.jpg'   => ['Woman cleaning her home with a vacuum cleaner in a kitchen', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Woman_cleaning_her_home_with_a_vacuum_cleaner_in_a_kitchen.jpg'],
    'cleaning-windows.jpg'          => ['Handheld window cleaner in use for streak-free shine on glass surface in bright indoor setting', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Handheld_window_cleaner_in_use_for_streak-free_shine_on_glass_surface_in_bright_indoor_setting.jpg'],
    'cleaning-bathroom-sink.jpg'    => ['Hands washing with soap at a sink in a bright bathroom', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Hands_washing_with_soap_at_a_sink_in_a_bright_bathroom.jpg'],
    'cleaned-living-room.jpg'       => ['Modern living room with stylish furniture and a view of the outdoors in a cozy apartment setting', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Modern_living_room_with_stylish_furniture_and_a_view_of_the_outdoors_in_a_cozy_apartment_setting.jpg'],
    'cleaning-vacuum-rug.jpg'       => ['Vacuum cleaner with bright lights cleaning a colorful rug in a cozy living room during the day', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Vacuum_cleaner_with_bright_lights_cleaning_a_colorful_rug_in_a_cozy_living_room_during_the_day.jpg'],
    'cleaning-vacuum-detail.jpg'    => ['Efficient cleaning with modern vacuum technology in a well-lit indoor space', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Efficient_cleaning_with_modern_vacuum_technology_in_a_well-lit_indoor_space.jpg'],
    'cleaning-floors.jpg'           => ['Cleaning the floor with a vacuum cleaner in a home setting', 'Shixart1985', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:Cleaning_the_floor_with_a_vacuum_cleaner_in_a_home_setting.jpg'],
    'moving-boxes.jpg'              => ['An Overview of Moving Companies and Their Use of Moving Boxes', 'brownpau', 'CC BY 2.0', 'https://commons.wikimedia.org/wiki/File:An_Overview_of_Moving_Companies_and_Their_Use_of_Moving_Boxes.jpg'],
    'clean-kitchen.jpg'             => ['Bright white kitchen with stainless steel appliances and pendant lights above clean countertops', 'Federal Bureau of Investigation', 'Public domain', 'https://commons.wikimedia.org/wiki/File:EFTA00001525_-_Bright_white_kitchen_with_stainless_steel_appliances_and_pendant_lights_above_clean_countertops.jpg'],
    'clean-bathroom.jpg'            => ['Clean white bathroom with a pedestal sink, toilet, large framed mirror and a towel on the wall', 'Federal Bureau of Investigation', 'Public domain', 'https://commons.wikimedia.org/wiki/File:EFTA00000556_-_Clean_white_bathroom_featuring_a_pedestal_sink_toilet_and_large_mirror_with_black_frame_and_a_towel_hanging_on_the_wall.jpg'],
    'clean-bathroom-alt.jpg'        => ['Small bathroom with white tiles, a toilet, sink, mirror and cleaning supplies', 'Federal Bureau of Investigation', 'Public domain', 'https://commons.wikimedia.org/wiki/File:EFTA00000295_-_Small_bathroom_with_white_tiles_a_toilet_sink_mirror_and_cleaning_supplies_on_the_tank_A_towel_hangs_on_the_wall.jpg'],
    'clean-bathroom-vanity.jpg'     => ['Bathroom sink with a marble countertop and an open cabinet revealing cleaning supplies and glass jars', 'Federal Bureau of Investigation', 'Public domain', 'https://commons.wikimedia.org/wiki/File:EFTA00002046_-_Bathroom_sink_with_a_marble_countertop_and_open_cabinet_revealing_cleaning_supplies_and_glass_jars_below.jpg'],
];

partial('header', [
    'head' => [
        'title'       => 'Photography Credits | Maid4Condos',
        'description' => 'Attribution for the photography used on the Maid4Condos website, including each image licence and its source.',
        'canonical'   => '/image-credits',
        'robots'      => 'noindex,follow',
    ],
    'body_class' => 'page-legal',
]);

partial('page-hero', [
    'eyebrow' => 'Credits',
    'title'   => 'Photography credits',
    'intro'   => 'We believe in crediting the people whose work we rely on. These images are used under the licences shown below.',
    'crumbs'  => ['Home' => '', 'Photography credits' => 'image-credits'],
]);
?>

<section class="section">
    <div class="container">
        <div class="section-head section-head--compact">
            <p class="eyebrow"><?php partial('icon', ['name' => 'image', 'size' => 15]); ?> Attribution</p>
            <p class="section-head__text">
                Photographs are sourced from Wikimedia Commons and used under the licence listed with each image.
                Images marked <strong>CC BY 2.0</strong> are used with attribution to their author; images marked
                <strong>Public domain</strong> carry no attribution requirement but are credited here for completeness.
            </p>
        </div>

        <ul class="credit-grid">
            <?php foreach ($images as $file => $meta) : ?>
                <li class="credit-card">
                    <div class="credit-card__media">
                        <?= m4c_image($file, $meta[0], [
                            'class'  => 'credit-card__image',
                            'width'  => 600,
                            'height' => 400,
                            'sizes'  => '(min-width: 1024px) 24vw, (min-width: 640px) 45vw, 92vw',
                        ]) ?>
                    </div>
                    <div class="credit-card__body">
                        <strong><?= e($file) ?></strong>
                        <p><?= e($meta[0]) ?></p>
                        <dl>
                            <div><dt>Author</dt><dd><?= e($meta[1]) ?></dd></div>
                            <div><dt>Licence</dt><dd><?= e($meta[2]) ?></dd></div>
                        </dl>
                        <a class="link-arrow" href="<?= e($meta[3]) ?>" target="_blank" rel="noopener nofollow">
                            Source page <?php partial('icon', ['name' => 'external', 'size' => 14]); ?>
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="callout">
            <span class="callout__icon"><?php partial('icon', ['name' => 'spark', 'size' => 22]); ?></span>
            <div>
                <h2>Swapping in your own photography</h2>
                <p>
                    These images are placeholders chosen for their relevance to condo cleaning. To use your own team
                    and client photos, upload them in the admin panel (Admin → Media) or drop files into
                    <code>assets/images/</code> using the same filenames and they will replace the stock photography
                    everywhere on the site.
                </p>
            </div>
        </div>
    </div>
</section>

<?php partial('cta-band'); ?>
<?php partial('footer'); ?>
