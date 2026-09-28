<?php
/**
 * Inline SVG icon component.
 *
 * Usage: partial('icon', ['name' => 'spark', 'size' => 20]);
 * Icons are inline so they inherit currentColor and add no extra requests.
 */
$name  = isset($name) ? $name : 'spark';
$size  = isset($size) ? (int) $size : 24;
$class = isset($class) ? ' ' . $class : '';
$label = isset($label) ? $label : '';

$paths = [
    'spark'    => '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3z"/><path d="M18.5 15.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2z"/>',
    'shield'   => '<path d="M12 3l7 3v6c0 4.2-2.9 7.6-7 9-4.1-1.4-7-4.8-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
    'team'     => '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><path d="M16 5.5a3 3 0 010 5.8"/><path d="M17.5 20a6 6 0 00-2.2-4.6"/>',
    'calendar' => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="M8 14h3"/>',
    'leaf'     => '<path d="M4 20c0-8 6-14 16-14 0 10-6 15-13 15H4z"/><path d="M4 20c3-5 7-8 12-9"/>',
    'badge'    => '<path d="M12 3l2.3 1.6 2.8-.2 1 2.6 2.4 1.5-.8 2.7.8 2.7-2.4 1.5-1 2.6-2.8-.2L12 19.4 9.7 17.8l-2.8.2-1-2.6L3.5 13.9l.8-2.7-.8-2.7 2.4-1.5 1-2.6 2.8.2L12 3z"/><path d="M9 12l2 2 4-4"/>',
    'check'    => '<path d="M20 6L9 17l-5-5"/>',
    'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
    'phone'    => '<path d="M6.6 3h2.6l1.4 3.5-2 1.3a12 12 0 005.6 5.6l1.3-2 3.5 1.4v2.6A2.6 2.6 0 0116.4 18 14.4 14.4 0 013 4.6A2.6 2.6 0 015.6 2h1z"/>',
    'mail'     => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M4 7.5l7.1 5a1.6 1.6 0 001.8 0l7.1-5"/>',
    'pin'      => '<path d="M12 21s7-5.4 7-11a7 7 0 10-14 0c0 5.6 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>',
    'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3.2 2"/>',
    'star'     => '<path d="M12 3.6l2.6 5.3 5.8.8-4.2 4.1 1 5.8-5.2-2.8-5.2 2.8 1-5.8L3.6 9.7l5.8-.8L12 3.6z"/>',
    'arrow-right' => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
    'arrow-up-right' => '<path d="M7 17L17 7"/><path d="M9 7h8v8"/>',
    'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'close'    => '<path d="M6 6l12 12M18 6L6 18"/>',
    'plus'     => '<path d="M12 5v14M5 12h14"/>',
    'chevron'  => '<path d="M6 9l6 6 6-6"/>',
    'quote'    => '<path d="M9 6H5.6A2.6 2.6 0 003 8.6V12a2.6 2.6 0 002.6 2.6H7M20 6h-3.4A2.6 2.6 0 0014 8.6V12a2.6 2.6 0 002.6 2.6H18M7 14.6V18M18 14.6V18"/>',
    'spray'    => '<path d="M10 8h5v13H10z"/><path d="M10 8V5h4v3"/><path d="M17 5h3M17 8h2M17 11h3"/>',
    'broom'    => '<path d="M14 4l6 6-9 2.5L8.5 10 14 4z"/><path d="M8.5 10L4 20l10-4.5"/>',
    'window'   => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M12 3v18M4 12h16"/>',
    'box'      => '<path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>',
    'oven'     => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M4 9h16M9 6h.01M13 6h.01M8 13h8v4H8z"/>',
    'fridge'   => '<rect x="6" y="3" width="12" height="18" rx="3"/><path d="M6 10h12M9 6.5v2M9 13v3"/>',
    'home'     => '<path d="M4 11l8-7 8 7v9a2 2 0 01-2 2H6a2 2 0 01-2-2v-9z"/><path d="M9.5 21v-6h5v6"/>',
    'building' => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h1M14 7h1M9 11h1M14 11h1M9 15h1M14 15h1M10 21v-3h4v3"/>',
    'bubble'   => '<circle cx="9" cy="9" r="5.2"/><circle cx="16.5" cy="15" r="3.2"/>',
    'facebook' => '<path d="M14 8.5V7c0-.8.4-1.2 1.3-1.2H17V3h-2.3C11.9 3 11 4.4 11 6.6v1.9H9V12h2v9h3v-9h2.3l.5-3.5H14z"/>',
    'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="3.8"/><path d="M16.8 7.3h.01"/>',
    'twitter'  => '<path d="M4 4h3.6l4.2 5.6L16.6 4H20l-6.3 7.2L20.4 20h-3.7l-4.4-5.9L7 20H3.6l6.6-7.5L4 4z"/>',
    'google'   => '<path d="M20 12.2c0-.6-.05-1.2-.15-1.7H12v3.3h4.5a3.9 3.9 0 01-1.7 2.5v2.1h2.7c1.6-1.5 2.5-3.7 2.5-6.2z"/><path d="M12 20.5c2.3 0 4.2-.75 5.5-2.05l-2.7-2.1c-.75.5-1.7.8-2.8.8-2.2 0-4-1.45-4.65-3.4H4.5v2.15A8.5 8.5 0 0012 20.5z"/><path d="M7.35 13.75a5.1 5.1 0 010-3.25V8.35H4.5a8.5 8.5 0 000 7.6l2.85-2.2z"/><path d="M12 7.1c1.25 0 2.35.43 3.2 1.27l2.4-2.4A8.4 8.4 0 0012 3.75 8.5 8.5 0 004.5 8.35l2.85 2.15C7.99 8.55 9.8 7.1 12 7.1z"/>',
    'sparkles' => '<path d="M12 3l1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6L12 3z"/><path d="M18 15l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7.7-1.8z"/>',
    'clock-fast' => '<path d="M12 4a8 8 0 108 8"/><path d="M12 8v4l3 2"/><path d="M17 3.5l3 2-3 2"/>',
    'hands'    => '<path d="M8 12V5.5a1.5 1.5 0 013 0V11"/><path d="M11 11V4.5a1.5 1.5 0 013 0V11"/><path d="M14 11V6.5a1.5 1.5 0 013 0V14a7 7 0 01-7 7H9a6 6 0 01-6-6v-2.5a1.5 1.5 0 013 0"/>',
    'lock'     => '<rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/><path d="M8 10.5V8a4 4 0 018 0v2.5"/>',
    'chart'    => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    'list'     => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="3.6" cy="6" r="1.1"/><circle cx="3.6" cy="12" r="1.1"/><circle cx="3.6" cy="18" r="1.1"/>',
    'image'    => '<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="8.5" cy="9.5" r="1.6"/><path d="M4 17l5-4.5 4 3.5 3-2.5 4 3.5"/>',
    'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.6 1.6 0 00-1.8-.3 1.6 1.6 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.6 1.6 0 00-1-1.5 1.6 1.6 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.6 1.6 0 00.3-1.8 1.6 1.6 0 00-1.5-1H3a2 2 0 110-4h.1a1.6 1.6 0 001.5-1 1.6 1.6 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.6 1.6 0 001.8.3H9a1.6 1.6 0 001-1.5V3a2 2 0 114 0v.1a1.6 1.6 0 001 1.5 1.6 1.6 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.6 1.6 0 00-.3 1.8V9a1.6 1.6 0 001.5 1H21a2 2 0 110 4h-.1a1.6 1.6 0 00-1.5 1z"/>',
    'users'    => '<circle cx="8.5" cy="8" r="3.5"/><path d="M2.5 20a6 6 0 0112 0"/><path d="M16 4.8a3.5 3.5 0 010 6.4M17 20a6 6 0 00-1.5-4"/>',
    'logout'   => '<path d="M15 4h3a2 2 0 012 2v12a2 2 0 01-2 2h-3"/><path d="M10 8l-4 4 4 4M6 12h9"/>',
    'trash'    => '<path d="M4 7h16M9 7V4.5h6V7M6 7l1 13h10l1-13"/>',
    'edit'     => '<path d="M4 20h4l11-11a2.5 2.5 0 10-3.5-3.5L4 16v4z"/><path d="M14 6l4 4"/>',
    'eye'      => '<path d="M2.5 12S6 6 12 6s9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/><circle cx="12" cy="12" r="3"/>',
    'eye-off'  => '<path d="M4 4l16 16"/><path d="M9.5 9.7A3 3 0 0012 15c.8 0 1.5-.3 2-.8"/><path d="M6.5 6.9C3.9 8.5 2.5 12 2.5 12s3.5 6 9.5 6c1.6 0 3-.4 4.2-1M19.2 15c1.1-1 1.8-2.2 2.3-3 0 0-3.5-6-9.5-6-.7 0-1.3.1-1.9.2"/>',
    'plus-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/>',
    'inbox'    => '<path d="M3 12h5l1.5 3h5L16 12h5"/><path d="M5 5h14l2 7v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5l2-7z"/>',
    'map'      => '<path d="M9 3L3 5.5v16L9 19l6 2.5 6-2.5v-16L15 5.5 9 3z"/><path d="M9 3v16M15 5.5v16"/>',
    'help'     => '<circle cx="12" cy="12" r="9"/><path d="M9.6 9.3a2.5 2.5 0 114.4 1.4c-.8.8-1.5 1.2-1.5 2.3"/><path d="M12.5 16.5h.01"/>',
    'external' => '<path d="M14 4h6v6"/><path d="M20 4l-8 8"/><path d="M18 14v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h4"/>',
];

$path = isset($paths[$name]) ? $paths[$name] : $paths['spark'];
$aria = $label !== '' ? ' role="img" aria-label="' . e($label) . '"' : ' aria-hidden="true"';
?>
<svg xmlns="http://www.w3.org/2000/svg" width="<?= (int) $size ?>" height="<?= (int) $size ?>"
     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
     stroke-linecap="round" stroke-linejoin="round"
     class="icon icon--<?= e($name) ?><?= e($class) ?>"<?= $aria ?>><?= $path ?></svg>
