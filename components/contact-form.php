<?php
/**
 * Contact form.
 *
 * @var array $state Result of m4c_handle_contact().
 */
$state  = isset($state) ? $state : ['attempted' => false, 'ok' => false, 'errors' => [], 'values' => []];
$values = array_merge(['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''], $state['values']);
$biz    = business();
?>
<form class="contact-form" method="post" action="<?= e(url('contact')) ?>#contact-form" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="form" value="contact">
    <input type="hidden" name="form_started" value="<?= e(time()) ?>">
    <div class="hp-field" aria-hidden="true">
        <label for="contact-website">Website</label>
        <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <?php if ($state['attempted'] && $state['errors']) : ?>
        <div class="alert alert--error" role="alert" tabindex="-1" data-form-errors>
            <strong>Please check the following</strong>
            <ul>
                <?php foreach ($state['errors'] as $error) : ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-grid form-grid--2">
        <div class="field">
            <label for="c-name">Your name <span class="req" aria-hidden="true">*</span></label>
            <input type="text" id="c-name" name="name" required autocomplete="name" value="<?= e($values['name']) ?>">
        </div>
        <div class="field">
            <label for="c-email">Email <span class="req" aria-hidden="true">*</span></label>
            <input type="email" id="c-email" name="email" required autocomplete="email" value="<?= e($values['email']) ?>">
        </div>
        <div class="field">
            <label for="c-phone">Phone <span class="field__optional">(optional)</span></label>
            <input type="tel" id="c-phone" name="phone" autocomplete="tel" value="<?= e($values['phone']) ?>">
        </div>
        <div class="field">
            <label for="c-subject">What is this about? <span class="req" aria-hidden="true">*</span></label>
            <select id="c-subject" name="subject" required>
                <option value="">Choose a topic…</option>
                <?php foreach (contact_subject_options() as $value => $label) : ?>
                    <option value="<?= e($value) ?>" <?= $values['subject'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="field">
        <label for="c-message">Your message <span class="req" aria-hidden="true">*</span></label>
        <textarea id="c-message" name="message" rows="6" required
                  placeholder="Tell us about your condo, your preferred dates, or anything else we should know."><?= e($values['message']) ?></textarea>
    </div>

    <div class="contact-form__foot">
        <button class="btn btn--primary btn--lg" type="submit" data-ga-event="contact_form_submit" data-ga-label="Contact form submit">
            Send message
            <?php partial('icon', ['name' => 'arrow-right', 'size' => 18]); ?>
        </button>
        <p class="contact-form__note">
            Prefer to talk? Call <a href="<?= e(tel_href()) ?>" data-ga-event="phone_click" data-ga-label="Contact form"><?= e($biz['phone_display']) ?></a>
            or email <a href="mailto:<?= e($biz['email']) ?>" data-ga-event="email_click" data-ga-label="Contact form"><?= e($biz['email']) ?></a>.
        </p>
    </div>
</form>
