<?php
/**
 * Maid4Condos — public form handling (quote requests and contact messages).
 *
 * Handles validation, spam checks, optional MySQL persistence, admin
 * notification email and the post/redirect/get pattern that prevents
 * duplicate submissions.
 */

/** Property types offered on the quote form. */
function quote_property_types()
{
    return [
        'condo'     => 'Condo',
        'apartment' => 'Apartment',
        'loft'      => 'Loft',
        'house'     => 'House / townhouse',
        'other'     => 'Something else',
    ];
}

function quote_bedroom_options()
{
    return ['Studio', '1', '2', '3', '4', '5', '6+'];
}

function quote_bathroom_options()
{
    return ['1', '2', '3', '4', '5', '6+'];
}

function quote_time_options()
{
    return [
        'morning'   => 'Morning (8am – 12pm)',
        'afternoon' => 'Afternoon (12pm – 6pm)',
        'flexible'  => 'Flexible — anytime between 8am and 6pm',
    ];
}

function quote_access_options()
{
    return [
        'home'      => 'I will be home',
        'concierge' => 'Key at concierge',
        'lockbox'   => 'Lockbox',
        'keypad'    => 'Smart key pad',
        'unsure'    => 'Not sure yet',
    ];
}

function contact_subject_options()
{
    return [
        'quote'        => 'A question about a quote',
        'booking'      => 'Booking or scheduling',
        'existing'     => 'I am an existing client',
        'move'         => 'Move in / move out cleaning',
        'commercial'   => 'Commercial or property management',
        'careers'      => 'Careers — join the team',
        'feedback'     => 'Feedback about a cleaning',
        'other'        => 'Something else',
    ];
}

/** Resolve a slug to its human label from a list of slug/label pairs. */
function label_for_slug(array $list, $slug, $default = '')
{
    foreach ($list as $key => $item) {
        if (is_array($item)) {
            $itemSlug = $item['slug'] ?? null;
            if ($itemSlug === $slug) {
                return $item['label'] ?? $item['name'] ?? $slug;
            }
        } elseif ($key === $slug) {
            return $item;
        }
    }

    return $default;
}

// ---------------------------------------------------------------------------
// Quote requests
// ---------------------------------------------------------------------------

/**
 * Validate and persist a quote request.
 *
 * @return array{attempted:bool,ok:bool,errors:array,values:array}
 */
function m4c_handle_quote()
{
    $state = ['attempted' => false, 'ok' => false, 'errors' => [], 'values' => []];

    if (!is_post() || input('form') !== 'quote') {
        return $state;
    }
    $state['attempted'] = true;

    if (!csrf_valid()) {
        $state['errors'][] = 'Your session expired. Please reload the page and try again.';

        return $state;
    }

    $values = [
        'name'            => clean_line(input('name'), 120),
        'email'           => clean_line(input('email'), 190),
        'phone'           => clean_phone(input('phone')),
        'neighbourhood'   => clean_line(input('neighbourhood'), 120),
        'postal_code'     => strtoupper(clean_line(input('postal_code'), 12)),
        'property_type'   => in_allowlist(input('property_type'), array_keys(quote_property_types()), ''),
        'bedrooms'        => in_allowlist(input('bedrooms'), quote_bedroom_options(), ''),
        'bathrooms'       => in_allowlist(input('bathrooms'), quote_bathroom_options(), ''),
        'sqft'            => int_input('sqft'),
        'service_slug'    => clean_line(input('service_slug'), 80),
        'frequency_slug'  => clean_line(input('frequency_slug'), 40),
        'preferred_date'  => clean_line(input('preferred_date'), 10),
        'preferred_time'  => in_allowlist(input('preferred_time'), array_keys(quote_time_options()), ''),
        'access_method'   => in_allowlist(input('access_method'), array_keys(quote_access_options()), ''),
        'message'         => clean_text(input('message'), 2000),
        'extras'          => [],
        'consent'         => input('consent'),
    ];

    if (is_array($_POST['extras'] ?? null)) {
        $allowedExtras = array_map(function ($extra) {
            return $extra['slug'];
        }, extras_all());
        foreach ($_POST['extras'] as $extra) {
            $extra = clean_line($extra, 60);
            if (in_array($extra, $allowedExtras, true)) {
                $values['extras'][] = $extra;
            }
        }
    }

    $errors = [];
    if ($values['name'] === '' || str_length($values['name']) < 2) {
        $errors[] = 'Please tell us your name.';
    }
    if (!valid_email($values['email'])) {
        $errors[] = 'Please enter a valid email address so we can send your quote.';
    }
    if (!valid_phone($values['phone'])) {
        $errors[] = 'Please enter a valid phone number, including the area code.';
    }
    if ($values['neighbourhood'] === '') {
        $errors[] = 'Please let us know your neighbourhood or city.';
    }
    if ($values['property_type'] === '') {
        $errors[] = 'Please choose your property type.';
    }
    if ($values['bedrooms'] === '') {
        $errors[] = 'Please choose the number of bedrooms.';
    }
    if ($values['bathrooms'] === '') {
        $errors[] = 'Please choose the number of bathrooms.';
    }
    if ($values['sqft'] < 1 || $values['sqft'] > 6000) {
        $errors[] = 'Please enter an approximate size between 1 and 6,000 sq ft.';
    }
    if ($values['service_slug'] === '' || !service($values['service_slug'])) {
        $errors[] = 'Please choose the cleaning service you are interested in.';
    }
    if ($values['frequency_slug'] === '' || !label_for_slug(frequencies_all(), $values['frequency_slug'])) {
        $errors[] = 'Please choose how often you would like us to clean.';
    }
    if ($values['preferred_date'] !== '' && !valid_date($values['preferred_date'])) {
        $errors[] = 'Please pick a valid preferred date, or leave it blank.';
    }
    if ($values['preferred_date'] !== '' && $values['preferred_date'] < date('Y-m-d')) {
        $errors[] = 'Please choose a preferred date in the future.';
    }
    if (empty($values['consent'])) {
        $errors[] = 'Please confirm we may contact you about your quote request.';
    }

    $errors = spam_check($errors);

    if ($errors) {
        $state['errors'] = $errors;
        $state['values'] = $values;

        return $state;
    }

    // Enrich for storage + notification.
    $service   = service($values['service_slug']);
    $frequency = label_for_slug(frequencies_all(), $values['frequency_slug']);

    save_inquiry($values, $service, $frequency);

    $rows = mailer_inquiry_rows(array_merge($values, [
        'service_name'    => $service ? $service['name'] : $values['service_slug'],
        'frequency_label' => $frequency,
        'extras'          => array_map(function ($slug) {
            return label_for_slug(extras_all(), $slug, $slug);
        }, $values['extras']),
        'source_page'     => absolute_url('get-a-quote'),
    ]));

    $html = m4c_mail_template(
        'New quote request',
        $rows,
        $values['name'] . ' would like a quote for ' . ($service ? $service['name'] : 'a cleaning') . '. Reply directly to this email to answer.'
    );

    m4c_send_mail(
        setting('booking_email', config('mail.to_email')),
        'New quote request — ' . $values['name'] . ' (' . $values['neighbourhood'] . ')',
        $html,
        $values['email']
    );

    // Courtesy acknowledgement to the client.
    m4c_send_mail(
        $values['email'],
        'We received your quote request — ' . business()['name'],
        m4c_mail_template(
            'Thanks — your request is with our team',
            [
                'What happens next' => 'A member of our team will contact you shortly to confirm the details and pricing.',
                'Service'           => $service ? $service['name'] : '',
                'Frequency'         => $frequency,
                'Preferred date'    => $values['preferred_date'],
                'Your note'         => $values['message'],
            ],
            'If you need to reach us sooner, call ' . business()['phone_display'] . ' or email ' . business()['email'] . '.'
        ),
        setting('booking_email', config('mail.to_email'))
    );

    $state['ok'] = true;

    $_SESSION['quote_submitted'] = [
        'name'    => $values['name'],
        'service' => $service ? $service['name'] : '',
        'at'      => time(),
    ];

    redirect('get-a-quote?submitted=1');
}

/** Persist a quote request when the database is available. */
function save_inquiry(array $values, $service, $frequency)
{
    if (!config('mail.log_inquiries') || !db_available()) {
        return 0;
    }

    try {
        return db_insert(table('inquiries'), [
            'name'            => $values['name'],
            'email'           => $values['email'],
            'phone'           => $values['phone'],
            'neighbourhood'   => $values['neighbourhood'],
            'postal_code'     => $values['postal_code'],
            'property_type'   => $values['property_type'],
            'bedrooms'        => $values['bedrooms'],
            'bathrooms'       => $values['bathrooms'],
            'sqft'            => $values['sqft'],
            'service_slug'    => $values['service_slug'],
            'service_name'    => $service ? $service['name'] : $values['service_slug'],
            'frequency_slug'  => $values['frequency_slug'],
            'frequency_label' => $frequency,
            'extras_json'     => json_encode($values['extras']),
            'preferred_date'  => $values['preferred_date'] ?: null,
            'preferred_time'  => $values['preferred_time'],
            'access_method'   => $values['access_method'],
            'message'         => $values['message'],
            'status'          => 'new',
            'source_page'     => 'get-a-quote',
            'ip_hash'         => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '') . config('security.csrf_key')),
            'user_agent'      => clean_line($_SERVER['HTTP_USER_AGENT'] ?? '', 255),
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $e) {
        if (config('app.debug')) {
            error_log('[Maid4Condos] Could not save inquiry: ' . $e->getMessage());
        }

        return 0;
    }
}

// ---------------------------------------------------------------------------
// Contact messages
// ---------------------------------------------------------------------------

/** @return array{attempted:bool,ok:bool,errors:array,values:array} */
function m4c_handle_contact()
{
    $state = ['attempted' => false, 'ok' => false, 'errors' => [], 'values' => []];

    if (!is_post() || input('form') !== 'contact') {
        return $state;
    }
    $state['attempted'] = true;

    if (!csrf_valid()) {
        $state['errors'][] = 'Your session expired. Please reload the page and try again.';

        return $state;
    }

    $values = [
        'name'    => clean_line(input('name'), 120),
        'email'   => clean_line(input('email'), 190),
        'phone'   => clean_phone(input('phone')),
        'subject' => in_allowlist(input('subject'), array_keys(contact_subject_options()), ''),
        'message' => clean_text(input('message'), 3000),
    ];

    $errors = [];
    if ($values['name'] === '') {
        $errors[] = 'Please tell us your name.';
    }
    if (!valid_email($values['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($values['phone'] !== '' && !valid_phone($values['phone'])) {
        $errors[] = 'That phone number does not look right — please check it, or leave it blank.';
    }
    if ($values['subject'] === '') {
        $errors[] = 'Please choose what your message is about.';
    }
    if (str_length($values['message']) < 10) {
        $errors[] = 'Please add a little more detail so we can help effectively.';
    }

    $errors = spam_check($errors);

    if ($errors) {
        $state['errors'] = $errors;
        $state['values'] = $values;

        return $state;
    }

    if (db_available()) {
        try {
            db_insert(table('messages'), [
                'name'       => $values['name'],
                'email'      => $values['email'],
                'phone'      => $values['phone'],
                'subject'    => label_for_slug(contact_subject_options(), $values['subject'], $values['subject']),
                'message'    => $values['message'],
                'status'     => 'new',
                'ip_hash'    => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '') . config('security.csrf_key')),
                'user_agent' => clean_line($_SERVER['HTTP_USER_AGENT'] ?? '', 255),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            if (config('app.debug')) {
                error_log('[Maid4Condos] Could not save message: ' . $e->getMessage());
            }
        }
    }

    m4c_send_mail(
        setting('email', config('mail.to_email')),
        'Website enquiry — ' . label_for_slug(contact_subject_options(), $values['subject'], 'General') . ' from ' . $values['name'],
        m4c_mail_template('New website message', [
            'Name'    => $values['name'],
            'Email'   => $values['email'],
            'Phone'   => $values['phone'],
            'Subject' => label_for_slug(contact_subject_options(), $values['subject'], ''),
            'Message' => $values['message'],
            'Received' => date('D, M j, Y g:i a'),
        ]),
        $values['email']
    );

    $state['ok'] = true;
    $_SESSION['contact_submitted'] = ['name' => $values['name'], 'at' => time()];

    redirect('contact?sent=1');
}
