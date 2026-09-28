<?php
/**
 * Quote request form.
 *
 * @var array $state  Result of m4c_handle_quote(): attempted, ok, errors, values.
 */
$state  = isset($state) ? $state : ['attempted' => false, 'ok' => false, 'errors' => [], 'values' => []];
$values = array_merge([
    'name' => '', 'email' => '', 'phone' => '', 'neighbourhood' => '', 'postal_code' => '',
    'property_type' => '', 'bedrooms' => '', 'bathrooms' => '', 'sqft' => '',
    'service_slug' => '', 'frequency_slug' => '', 'extras' => [], 'preferred_date' => '',
    'preferred_time' => '', 'access_method' => '', 'message' => '', 'consent' => '',
], $state['values']);

$services    = services_all();
$frequencies = frequencies_all();
$extras      = extras_all();
$areas       = areas_all();
$biz         = business();

$fieldValue = function ($key) use ($values) {
    return isset($values[$key]) ? $values[$key] : '';
};
$hasError = function ($needle) use ($state) {
    foreach ($state['errors'] as $error) {
        if (stripos($error, $needle) !== false) {
            return true;
        }
    }

    return false;
};
?>
<form class="quote-form" method="post" action="<?= e(url('get-a-quote')) ?>#quote-form" data-quote-form novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="form" value="quote">
    <input type="hidden" name="form_started" value="<?= e(time()) ?>">
    <div class="hp-field" aria-hidden="true">
        <label for="quote-website">Website</label>
        <input type="text" id="quote-website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <?php if ($state['attempted'] && $state['errors']) : ?>
        <div class="alert alert--error" role="alert" tabindex="-1" data-form-errors>
            <strong>We could not send that just yet</strong>
            <ul>
                <?php foreach ($state['errors'] as $error) : ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <fieldset class="form-section">
        <legend class="form-section__legend">
            <span class="form-section__num">01</span>
            <span>
                <strong>Your details</strong>
                <small>So we can send your quote and follow up.</small>
            </span>
        </legend>
        <div class="form-grid form-grid--3">
            <div class="field">
                <label for="q-name">Full name <span class="req" aria-hidden="true">*</span></label>
                <input type="text" id="q-name" name="name" required autocomplete="name"
                       value="<?= e($fieldValue('name')) ?>" placeholder="Jordan Lee">
            </div>
            <div class="field">
                <label for="q-email">Email <span class="req" aria-hidden="true">*</span></label>
                <input type="email" id="q-email" name="email" required autocomplete="email"
                       value="<?= e($fieldValue('email')) ?>" placeholder="you@example.com">
            </div>
            <div class="field">
                <label for="q-phone">Phone <span class="req" aria-hidden="true">*</span></label>
                <input type="tel" id="q-phone" name="phone" required autocomplete="tel"
                       value="<?= e($fieldValue('phone')) ?>" placeholder="(647) 555-0123">
            </div>
        </div>
    </fieldset>

    <fieldset class="form-section">
        <legend class="form-section__legend">
            <span class="form-section__num">02</span>
            <span>
                <strong>Your home</strong>
                <small>Size drives how much time we allocate to your clean.</small>
            </span>
        </legend>
        <div class="form-grid form-grid--2">
            <div class="field">
                <label for="q-neighbourhood">Neighbourhood or city <span class="req" aria-hidden="true">*</span></label>
                <input type="text" id="q-neighbourhood" name="neighbourhood" required list="m4c-areas"
                       value="<?= e($fieldValue('neighbourhood')) ?>" placeholder="Liberty Village">
                <datalist id="m4c-areas">
                    <?php foreach ($areas as $area) : ?>
                        <option value="<?= e($area['name']) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <p class="field__hint">We service Toronto and the GTA — not sure? Send it anyway and we will confirm.</p>
            </div>
            <div class="field">
                <label for="q-postal">Postal code</label>
                <input type="text" id="q-postal" name="postal_code" autocomplete="postal-code"
                       value="<?= e($fieldValue('postal_code')) ?>" placeholder="M6K 1X9">
            </div>
        </div>
        <div class="form-grid form-grid--4">
            <div class="field">
                <label for="q-property">Property type <span class="req" aria-hidden="true">*</span></label>
                <select id="q-property" name="property_type" required>
                    <option value="">Choose…</option>
                    <?php foreach (quote_property_types() as $value => $label) : ?>
                        <option value="<?= e($value) ?>" <?= $fieldValue('property_type') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="q-bedrooms">Bedrooms <span class="req" aria-hidden="true">*</span></label>
                <select id="q-bedrooms" name="bedrooms" required>
                    <option value="">Choose…</option>
                    <?php foreach (quote_bedroom_options() as $option) : ?>
                        <option value="<?= e($option) ?>" <?= $fieldValue('bedrooms') === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="q-bathrooms">Bathrooms <span class="req" aria-hidden="true">*</span></label>
                <select id="q-bathrooms" name="bathrooms" required>
                    <option value="">Choose…</option>
                    <?php foreach (quote_bathroom_options() as $option) : ?>
                        <option value="<?= e($option) ?>" <?= $fieldValue('bathrooms') === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="field__hint">Count every toilet — a powder room counts as one.</p>
            </div>
            <div class="field">
                <label for="q-sqft">Approx. square feet <span class="req" aria-hidden="true">*</span></label>
                <div class="input-suffix">
                    <input type="number" id="q-sqft" name="sqft" min="1" max="6000" step="10" required
                           inputmode="numeric" value="<?= e($fieldValue('sqft')) ?>" placeholder="850">
                    <span>ft²</span>
                </div>
                <p class="field__hint">Not sure? Round up — up to 6,000 ft².</p>
            </div>
        </div>
    </fieldset>

    <fieldset class="form-section">
        <legend class="form-section__legend">
            <span class="form-section__num">03</span>
            <span>
                <strong>Your cleaning</strong>
                <small>Pick the package, add extras and tell us when.</small>
            </span>
        </legend>

        <div class="field">
            <span class="field__label" id="q-service-label">Cleaning service <span class="req" aria-hidden="true">*</span></span>
            <div class="option-cards" role="radiogroup" aria-labelledby="q-service-label" data-service-options>
                <?php foreach ($services as $service) : ?>
                    <label class="option-card">
                        <input type="radio" name="service_slug" value="<?= e($service['slug']) ?>" required
                               data-service-radio <?= $fieldValue('service_slug') === $service['slug'] ? 'checked' : '' ?>>
                        <span class="option-card__inner">
                            <span class="option-card__name"><?= e($service['name']) ?></span>
                            <span class="option-card__desc"><?= e($service['tagline']) ?></span>
                            <span class="option-card__meta">
                                <?php if (!empty($service['price_from'])) : ?>
                                    From <?= e(money($service['price_from'])) ?>
                                <?php else : ?>
                                    Up to 20% off recurring
                                <?php endif; ?>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="field" data-reveal-when="recurring-cleaning" hidden>
            <p class="reveal-note">
                <?php partial('icon', ['name' => 'calendar', 'size' => 18]); ?>
                <span><strong>AutoPilot selected.</strong> Choose how often you would like us below — weekly saves 20%, bi-weekly 15% and monthly 5% per visit, with no contract required.</span>
            </p>
        </div>

        <div class="field" data-reveal-when="move-in-move-out" hidden>
            <p class="reveal-note reveal-note--warm">
                <?php partial('icon', ['name' => 'box', 'size' => 18]); ?>
                <span><strong>Moving clean selected.</strong> This package is built for vacant units — we clean inside and out, including cabinets, drawers, appliances and vents. Let us know how we will get in, below.</span>
            </p>
        </div>

        <div class="field">
            <label for="q-frequency">How often would you like us? <span class="req" aria-hidden="true">*</span></label>
            <select id="q-frequency" name="frequency_slug" required data-frequency-select>
                <option value="">Choose a schedule…</option>
                <?php foreach ($frequencies as $frequency) : ?>
                    <option value="<?= e($frequency['slug']) ?>" <?= $fieldValue('frequency_slug') === $frequency['slug'] ? 'selected' : '' ?>>
                        <?= e($frequency['label']) ?> — <?= e($frequency['discount']) ?><?= !empty($frequency['recommended']) ? ' (recommended)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="field__hint" data-frequency-hint>Recurring schedules save up to 20% on every visit.</p>
        </div>

        <div class="field">
            <span class="field__label">Add-on services <span class="field__optional">(optional)</span></span>
            <div class="check-grid">
                <?php foreach ($extras as $extra) : ?>
                    <label class="check">
                        <input type="checkbox" name="extras[]" value="<?= e($extra['slug']) ?>"
                               <?= in_array($extra['slug'], (array) $fieldValue('extras'), true) ? 'checked' : '' ?>>
                        <span class="check__box" aria-hidden="true"><?php partial('icon', ['name' => 'check', 'size' => 14]); ?></span>
                        <span class="check__text">
                            <strong><?= e($extra['name']) ?></strong>
                            <small><?= e(excerpt($extra['summary'], 90)) ?></small>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-grid form-grid--2">
            <div class="field">
                <label for="q-date">Preferred date <span class="field__optional">(optional)</span></label>
                <input type="date" id="q-date" name="preferred_date"
                       min="<?= e(date('Y-m-d')) ?>" value="<?= e($fieldValue('preferred_date')) ?>">
                <p class="field__hint">Residential cleanings run Monday to Friday, 8am – 6pm.</p>
            </div>
            <div class="field">
                <label for="q-time">Preferred arrival window</label>
                <select id="q-time" name="preferred_time">
                    <option value="">No preference</option>
                    <?php foreach (quote_time_options() as $value => $label) : ?>
                        <option value="<?= e($value) ?>" <?= $fieldValue('preferred_time') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field">
            <label for="q-access">How should we access your home?</label>
            <select id="q-access" name="access_method">
                <option value="">Choose an option…</option>
                <?php foreach (quote_access_options() as $value => $label) : ?>
                    <option value="<?= e($value) ?>" <?= $fieldValue('access_method') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="field__hint">Concierge or lockbox access is the easiest and most efficient for us.</p>
        </div>
    </fieldset>

    <fieldset class="form-section">
        <legend class="form-section__legend">
            <span class="form-section__num">04</span>
            <span>
                <strong>Anything else?</strong>
                <small>Pets, allergies, priority areas — tell us here.</small>
            </span>
        </legend>
        <div class="field">
            <label for="q-message">Additional information <span class="field__optional">(optional)</span></label>
            <textarea id="q-message" name="message" rows="5"
                      placeholder="We have a cat, please use fragrance-free products. The balcony is off limits."><?= e($fieldValue('message')) ?></textarea>
        </div>
        <div class="field field--check">
            <label class="check check--consent">
                <input type="checkbox" name="consent" value="1" <?= $fieldValue('consent') ? 'checked' : '' ?> required>
                <span class="check__box" aria-hidden="true"><?php partial('icon', ['name' => 'check', 'size' => 14]); ?></span>
                <span class="check__text">Yes, Maid4Condos may contact me about this request. See our <a href="<?= e(url('privacy-policy')) ?>">privacy policy</a>.</span>
            </label>
        </div>
    </fieldset>

    <div class="quote-form__foot">
        <button class="btn btn--primary btn--lg" type="submit" data-ga-event="quote_cta_click" data-ga-label="Quote form submit">
            Send my quote request
            <?php partial('icon', ['name' => 'arrow-right', 'size' => 18]); ?>
        </button>
        <div class="quote-form__assurance">
            <p><?php partial('icon', ['name' => 'lock', 'size' => 16]); ?> Your details stay private and are never sold.</p>
            <p><?php partial('icon', ['name' => 'clock', 'size' => 16]); ?> We reply within one business day.</p>
            <p>Prefer to talk? <a href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Quote form"><?= e($biz['phone_display']) ?></a></p>
        </div>
    </div>
</form>
