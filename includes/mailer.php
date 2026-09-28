<?php
/**
 * Maid4Condos — transactional email.
 *
 * Two transports, chosen in Admin → Settings → Mail:
 *   • 'mail'    — PHP mail(). Works on nearly every shared host.
 *   • 'elastic' — Elastic Email v4 HTTP API over cURL. Recommended for
 *                 deliverability, and it needs no Composer packages.
 *
 * Set the API key with `M4C_MAIL_API_KEY` in the server environment or in
 * config/config.local.php. Never hard-code credentials here.
 */

function mailer_config()
{
    return config('mail');
}

/** Human-readable transport name for the admin UI. */
function mailer_transport_label()
{
    $transport = mailer_config()['transport'];

    return $transport === 'elastic' ? 'Elastic Email API' : 'PHP mail()';
}

/**
 * Send one HTML email. Returns true when the transport accepted it.
 *
 * @param string      $to        Verified recipient address.
 * @param string      $subject   Plain-text subject.
 * @param string      $htmlBody  HTML body.
 * @param string|null $replyTo   Optional reply-to address.
 * @param string      $textBody  Optional plain-text alternative.
 */
function m4c_send_mail($to, $subject, $htmlBody, $replyTo = null, $textBody = '')
{
    $cfg   = mailer_config();
    $to    = clean_line($to, 190);
    if (!valid_email($to)) {
        return false;
    }

    $subject = clean_line($subject, 190);
    $replyTo = $replyTo && valid_email($replyTo) ? $replyTo : $cfg['reply_to'];

    if ($cfg['transport'] === 'elastic' && trim((string) $cfg['api_key']) !== '') {
        return m4c_send_via_elastic($to, $subject, $htmlBody, $textBody, $replyTo);
    }

    return m4c_send_via_mail($to, $subject, $htmlBody, $textBody, $replyTo);
}

/** PHP mail() transport. */
function m4c_send_via_mail($to, $subject, $htmlBody, $textBody, $replyTo)
{
    $cfg = mailer_config();

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . m4c_header_address($cfg['from_name'], $cfg['from_email']),
        'Reply-To: ' . m4c_header_address($cfg['from_name'], $replyTo),
        'X-Mailer: Maid4Condos',
    ];

    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $sent    = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));

    if (!$sent && config('app.debug')) {
        error_log('[Maid4Condos] mail() rejected the message to ' . $to);
    }

    return (bool) $sent;
}

/** Elastic Email v4 API transport (cURL, no dependencies). */
function m4c_send_via_elastic($to, $subject, $htmlBody, $textBody, $replyTo)
{
    $cfg = mailer_config();

    if (!function_exists('curl_init')) {
        return m4c_send_via_mail($to, $subject, $htmlBody, $textBody, $replyTo);
    }

    $payload = [
        'Recipients' => [['Email' => $to]],
        'Content'    => [
            'From'     => m4c_header_address($cfg['from_name'], $cfg['from_email']),
            'ReplyTo'  => $replyTo,
            'Subject'  => $subject,
            'Body'     => [
                [
                    'ContentType' => 'HTML',
                    'Content'     => $htmlBody,
                    'Charset'     => 'utf-8',
                ],
                [
                    'ContentType' => 'PlainText',
                    'Content'     => $textBody !== '' ? $textBody : trim(strip_tags($htmlBody)),
                    'Charset'     => 'utf-8',
                ],
            ],
        ],
        'MsgFrom'         => $cfg['from_email'],
        'MsgTo'           => [$to],
        'AllowUnsubscribe' => false,
    ];

    $ch = curl_init($cfg['api_endpoint']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-ElasticEmail-ApiKey: ' . $cfg['api_key'],
        ],
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error  = curl_error($ch);
    curl_close($ch);

    if ($status < 200 || $status >= 300) {
        if (config('app.debug')) {
            error_log('[Maid4Condos] Elastic Email failed (' . $status . '): ' . $error . ' ' . substr((string) $body, 0, 400));
        }

        // Never lose the lead because of a provider hiccup.
        return m4c_send_via_mail($to, $subject, $htmlBody, $textBody, $replyTo);
    }

    return true;
}

/** Build a safe RFC 2822 address header. */
function m4c_header_address($name, $email)
{
    $email = str_replace(["\r", "\n"], '', (string) $email);
    $name  = str_replace(["\r", "\n"], '', (string) $name);

    return sprintf('%s <%s>', '=?UTF-8?B?' . base64_encode($name) . '?=', $email);
}

/**
 * Branded HTML email shell.
 *
 * @param string $heading  Headline inside the email.
 * @param array  $rows     label => value pairs.
 * @param string $intro    Optional lead paragraph.
 */
function m4c_mail_template($heading, array $rows = [], $intro = '')
{
    $biz   = business();
    $rowsHtml = '';
    foreach ($rows as $label => $value) {
        if ($value === '' || $value === null || $value === []) {
            continue;
        }
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $rowsHtml .= '<tr>'
            . '<td style="padding:10px 16px;border-bottom:1px solid #E6EFEC;font:600 13px/1.4 Helvetica,Arial,sans-serif;color:#4A6A72;vertical-align:top;width:38%">' . e($label) . '</td>'
            . '<td style="padding:10px 16px;border-bottom:1px solid #E6EFEC;font:400 14px/1.5 Helvetica,Arial,sans-serif;color:#07242E">' . nl2br(e((string) $value)) . '</td>'
            . '</tr>';
    }

    $introHtml = $intro !== ''
        ? '<p style="margin:0 0 18px;font:400 15px/1.6 Helvetica,Arial,sans-serif;color:#3C5A63">' . nl2br(e($intro)) . '</p>'
        : '';

    $footer = 'Sent from ' . e($biz['name']) . ' • ' . e($biz['phone_display']) . ' • ' . e($biz['email']);

    return '<!doctype html><html><body style="margin:0;padding:24px;background:#F3F7F5">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#FFFFFF;border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(7,36,46,.08)">'
        . '<tr><td style="background:#07242E;padding:22px 24px">'
        . '<span style="font:700 18px/1 Georgia,serif;color:#FFFFFF;letter-spacing:.01em">' . e($biz['name']) . '</span>'
        . '<div style="font:400 12px/1.4 Helvetica,Arial,sans-serif;color:#8FC7BE;margin-top:6px">' . e($biz['tagline']) . '</div>'
        . '</td></tr>'
        . '<tr><td style="padding:26px 24px 6px">'
        . '<h1 style="margin:0 0 14px;font:600 21px/1.3 Georgia,serif;color:#07242E">' . e($heading) . '</h1>'
        . $introHtml
        . '</td></tr>'
        . '<tr><td style="padding:0 8px"><table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $rowsHtml . '</table></td></tr>'
        . '<tr><td style="padding:22px 24px 26px"><p style="margin:0;font:400 12px/1.6 Helvetica,Arial,sans-serif;color:#7A9299">' . $footer . '</p></td></tr>'
        . '</table></body></html>';
}

/** Format an inquiry row for email. */
function mailer_inquiry_rows(array $data)
{
    return [
        'Name'            => $data['name'] ?? '',
        'Email'           => $data['email'] ?? '',
        'Phone'           => $data['phone'] ?? '',
        'Neighbourhood'   => $data['neighbourhood'] ?? '',
        'Postal code'     => $data['postal_code'] ?? '',
        'Property type'   => $data['property_type'] ?? '',
        'Bedrooms'        => $data['bedrooms'] ?? '',
        'Bathrooms'       => $data['bathrooms'] ?? '',
        'Approx. size'    => !empty($data['sqft']) ? $data['sqft'] . ' sq ft' : '',
        'Cleaning service' => $data['service_name'] ?? '',
        'Frequency'       => $data['frequency_label'] ?? '',
        'Add-ons'         => $data['extras'] ?? [],
        'Preferred date'  => $data['preferred_date'] ?? '',
        'Preferred time'  => $data['preferred_time'] ?? '',
        'Access'          => $data['access_method'] ?? '',
        'Additional notes' => $data['message'] ?? '',
        'Submitted from'  => $data['source_page'] ?? '',
        'Received'        => date('D, M j, Y g:i a'),
    ];
}
