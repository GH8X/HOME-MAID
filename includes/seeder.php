<?php
/**
 * Maid4Condos — content seeder.
 *
 * Copies the verified content from includes/seed.php into MySQL so it becomes
 * editable in the admin panel. Run once from the installer (or from
 * Admin → Overview → "Import default content").
 *
 * Nothing is ever overwritten unless $force is true.
 */

function m4c_seed_all($force = false)
{
    if (!db_available()) {
        return ['error' => 'No database connection. Check config/config.local.php.'];
    }

    $log = [];

    foreach (seed_data('settings') as $key => $meta) {
        list($group, $type, $value, $label) = array_pad($meta, 4, '');
        $existing = db_one(
            'SELECT id, setting_value FROM ' . table('settings') . ' WHERE setting_key = ? LIMIT 1',
            [$key]
        );
        if (!$existing) {
            db_insert(table('settings'), [
                'setting_key'   => $key,
                'setting_value' => $value,
                'setting_group' => $group,
                'setting_type'  => $type,
                'label'         => $label,
            ]);
            $log[] = 'settings.' . $key;
        } elseif ($force) {
            db_update(table('settings'), ['setting_value' => $value, 'label' => $label, 'setting_group' => $group, 'setting_type' => $type], 'id = ?', [$existing['id']]);
            $log[] = 'settings.' . $key . ' (updated)';
        }
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('services'))) {
        if ($force) {
            db_delete(table('services'), '1 = 1');
        }
        foreach (seed_data('services') as $index => $service) {
            db_insert(table('services'), [
                'slug'             => $service['slug'],
                'name'             => $service['name'],
                'eyebrow'          => $service['eyebrow'],
                'tagline'          => $service['tagline'],
                'summary'          => $service['summary'],
                'intro_json'       => json_encode($service['intro']),
                'price_from'       => $service['price_from'],
                'image'            => $service['image'],
                'hero_image'       => $service['hero_image'],
                'duration_note'    => $service['duration_note'],
                'best_paired'      => $service['best_paired'],
                'meta_title'       => $service['meta_title'],
                'meta_description' => $service['meta_description'],
                'who_for_json'     => json_encode($service['who_for']),
                'checklist_json'   => json_encode($service['checklist']),
                'benefits_json'    => json_encode($service['benefits']),
                'faqs_json'        => json_encode($service['faqs']),
                'sort_order'       => $index + 1,
                'is_published'     => 1,
            ]);
        }
        $log[] = count(seed_data('services')) . ' services';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('faqs'))) {
        if ($force) {
            db_delete(table('faqs'), '1 = 1');
        }
        foreach (seed_data('faqs') as $index => $faq) {
            db_insert(table('faqs'), [
                'category'     => $faq['category'],
                'question'     => $faq['question'],
                'answer'       => $faq['answer'],
                'sort_order'   => $index + 1,
                'is_published' => 1,
            ]);
        }
        $log[] = count(seed_data('faqs')) . ' FAQs';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('testimonials'))) {
        if ($force) {
            db_delete(table('testimonials'), '1 = 1');
        }
        foreach (seed_data('testimonials') as $index => $testimonial) {
            db_insert(table('testimonials'), [
                'name'         => $testimonial['name'],
                'location'     => $testimonial['location'],
                'service'      => (string) $testimonial['service'],
                'quote'        => $testimonial['quote'],
                'rating'       => null,
                'source'       => 'maid4condos.com',
                'sort_order'   => $index + 1,
                'is_published' => 1,
            ]);
        }
        $log[] = count(seed_data('testimonials')) . ' testimonials';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('service_areas'))) {
        if ($force) {
            db_delete(table('service_areas'), '1 = 1');
        }
        foreach (seed_data('areas') as $index => $area) {
            db_insert(table('service_areas'), [
                'name'         => $area['name'],
                'note'         => $area['note'],
                'sort_order'   => $index + 1,
                'is_published' => 1,
            ]);
        }
        $log[] = count(seed_data('areas')) . ' service areas';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('extras'))) {
        if ($force) {
            db_delete(table('extras'), '1 = 1');
        }
        foreach (seed_data('extras') as $index => $extra) {
            db_insert(table('extras'), [
                'slug'         => $extra['slug'],
                'name'         => $extra['name'],
                'summary'      => $extra['summary'],
                'details'      => $extra['details'],
                'sort_order'   => $index + 1,
                'is_published' => 1,
            ]);
        }
        $log[] = count(seed_data('extras')) . ' add-ons';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('frequencies'))) {
        if ($force) {
            db_delete(table('frequencies'), '1 = 1');
        }
        foreach (seed_data('frequencies') as $index => $frequency) {
            db_insert(table('frequencies'), [
                'slug'         => $frequency['slug'],
                'label'        => $frequency['label'],
                'discount'     => $frequency['discount'],
                'note'         => $frequency['note'],
                'recommended'  => !empty($frequency['recommended']) ? 1 : 0,
                'sort_order'   => $index + 1,
                'is_published' => 1,
            ]);
        }
        $log[] = count(seed_data('frequencies')) . ' schedules';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('navigation'))) {
        if ($force) {
            db_delete(table('navigation'), '1 = 1');
        }
        foreach (seed_data('navigation') as $index => $item) {
            db_insert(table('navigation'), [
                'label'        => $item['label'],
                'route'        => $item['route'],
                'sort_order'   => $index + 1,
                'is_published' => 1,
            ]);
        }
        $log[] = count(seed_data('navigation')) . ' nav items';
    }

    if ($force || !db_value('SELECT COUNT(*) FROM ' . table('content_blocks'))) {
        if ($force) {
            db_delete(table('content_blocks'), '1 = 1');
        }
        $blocks = [
            'home.hero'  => seed_data('home')['hero'],
            'home.trust' => seed_data('home')['trust'],
            'home.why'   => seed_data('home')['why'],
            'home.how'   => seed_data('home')['how'],
            'home.final' => seed_data('home')['final'],
            'about'      => seed_data('about'),
        ];
        foreach ($blocks as $key => $body) {
            db_insert(table('content_blocks'), [
                'block_key'    => $key,
                'block_json'   => json_encode($body),
                'is_published' => 1,
            ]);
        }
        $log[] = count($blocks) . ' content blocks';
    }

    return ['log' => $log];
}

/** Create the first administrator account. Returns null on success. */
function m4c_create_admin($name, $email, $password, $role = 'owner')
{
    if (!db_available()) {
        return 'No database connection.';
    }
    $name  = clean_line($name, 120);
    $email = strtolower(clean_line($email, 190));

    if ($name === '') {
        return 'Please enter a name.';
    }
    if (!valid_email($email)) {
        return 'Please enter a valid email address.';
    }
    if (strlen((string) $password) < 10) {
        return 'Use a password of at least 10 characters.';
    }

    $exists = db_one('SELECT id FROM ' . table('admins') . ' WHERE email = ? LIMIT 1', [$email]);
    if ($exists) {
        return 'An administrator with that email already exists.';
    }

    db_insert(table('admins'), [
        'name'          => $name,
        'email'         => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role'          => $role === 'owner' ? 'owner' : 'editor',
        'is_active'     => 1,
        'created_at'    => date('Y-m-d H:i:s'),
    ]);

    return null;
}
