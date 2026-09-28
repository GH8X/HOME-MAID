/**
 * Five-step quote wizard.
 *
 * Step 1 contact details → step 2 property → step 3 service → step 4 schedule →
 * step 5 extras, notes and consent. Conditional logic: the vacant-unit notice
 * appears for Move In / Move Out, the frequency discount is surfaced as soon as
 * a recurring frequency is chosen, commercial properties get a note about
 * separate agreements, and the add-on list explains what is already included.
 *
 * The site is hosted as static files, so a finished request is handed back to
 * the visitor as a written summary they can send to the office in one press.
 */

import { useMemo, useRef, useState, type ReactNode } from 'react';
import { Link, useSearchParams } from 'react-router-dom';

import { trackEvent } from '../lib/analytics';
import { classNames } from '../lib/format';
import type { PreparedEnquiry, QuoteRequestInput } from '../lib/types';
import {
  ACCESS_METHODS,
  ARRIVAL_WINDOWS,
  BATHROOM_OPTIONS,
  BEDROOM_OPTIONS,
  EMPTY_QUOTE,
  EXTRAS,
  PROPERTY_TYPES,
  QUOTE_STEPS,
  REFERRAL_SOURCES,
  SIZE_OPTIONS,
  firstIncompleteStep,
  hasErrors,
  validateQuote,
  validateQuoteStep,
  type ErrorMap,
  type QuoteStepId,
} from '../lib/validation';
import { useContent } from '../services/contentService';
import { quoteEnquiry, submitQuoteRequest } from '../services/quoteService';
import { EnquirySent } from './EnquirySent';
import { Icon } from './Icon';

interface FieldProps {
  label: string;
  htmlFor: string;
  error?: string;
  hint?: string;
  required?: boolean;
  children: ReactNode;
}

function Field({ label, htmlFor, error, hint, required = true, children }: FieldProps) {
  return (
    <div className="field">
      <label className="field__label" htmlFor={htmlFor}>
        {label}
        {required ? (
          <span className="field__required" aria-hidden="true">
            *
          </span>
        ) : null}
      </label>
      {children}
      {error ? (
        <p className="field__error" id={`${htmlFor}-error`}>
          <Icon name="alert" size={14} />
          {error}
        </p>
      ) : null}
      {hint && !error ? <p className="field__hint">{hint}</p> : null}
    </div>
  );
}

function todayIso(): string {
  const now = new Date();
  const offset = now.getTimezoneOffset();
  return new Date(now.getTime() - offset * 60_000).toISOString().slice(0, 10);
}

export function QuoteForm() {
  const { live, settings } = useContent();
  const [searchParams] = useSearchParams();

  const initialService = searchParams.get('service') ?? '';
  const matched = live.services.find((service) => service.slug === initialService);

  const [input, setInput] = useState<QuoteRequestInput>(() => ({
    ...EMPTY_QUOTE,    service_slug: matched?.slug ?? '',
    service_name: matched?.name ?? '',
  }));
  const [step, setStep] = useState(matched ? 1 : 0);
  const [errors, setErrors] = useState<ErrorMap>({});
  const [consent, setConsent] = useState(false);
  const [consentError, setConsentError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [failed, setFailed] = useState<string | null>(null);
  const [finished, setFinished] = useState<PreparedEnquiry | null>(null);
  const [started, setStarted] = useState(false);

  const honeypot = useRef<HTMLInputElement | null>(null);

  const activeStep = QUOTE_STEPS[step];
  const progress = ((step + 1) / QUOTE_STEPS.length) * 100;

  const selectedService = live.services.find((service) => service.slug === input.service_slug);
  const selectedFrequency = live.frequencies.find((entry) => entry.slug === input.frequency);
  const isVacantService = input.service_slug === 'move-in-move-out';
  const isCommercial = input.property_type === 'Office / commercial space';

  const update = <K extends keyof QuoteRequestInput>(key: K, value: QuoteRequestInput[K]) => {
    if (!started) {
      setStarted(true);
      trackEvent('quote_form_start');
    }
    setInput((current) => ({ ...current, [key]: value }));
    setErrors((current) => ({ ...current, [key]: undefined }));
  };

  const goTo = (next: number) => {
    const bounded = Math.max(0, Math.min(QUOTE_STEPS.length - 1, next));
    setStep(bounded);
    trackEvent('quote_form_step', { step: bounded + 1, name: QUOTE_STEPS[bounded].id });
  };

  const next = () => {
    const stepErrors = validateQuoteStep(activeStep.id as QuoteStepId, input);
    if (hasErrors(stepErrors)) {
      setErrors((current) => ({ ...current, ...stepErrors }));
      return;
    }
    setFailed(null);
    goTo(step + 1);
  };

  const back = () => {
    setFailed(null);
    goTo(step - 1);
  };

  const submit = async () => {
    const allErrors = validateQuote(input);
    if (hasErrors(allErrors)) {
      setErrors(allErrors);
      goTo(firstIncompleteStep(input));
      setFailed('Please complete the highlighted fields before requesting your quote.');
      return;
    }
    if (!consent) {
      setConsentError('Please confirm you agree to our terms and privacy policy.');
      return;
    }
    if (honeypot.current?.value) {
      // Bots fill hidden fields. Show the same success state and drop it.
      setFinished(quoteEnquiry(input));
      return;
    }

    setSubmitting(true);
    setFailed(null);
    const result = await submitQuoteRequest(input);
    setSubmitting(false);

    if (!result.ok) {
      setFailed(result.message ?? 'Something went wrong. Please call the office instead.');
      return;
    }

    trackEvent('quote_form_submit', {
      service: input.service_slug,
      frequency: input.frequency,
    });
    setFinished(quoteEnquiry(input));
  };

  const summaryRows = useMemo(
    () =>
      [
        { label: 'Service', value: selectedService?.name ?? '—' },
        { label: 'Frequency', value: selectedFrequency?.label ?? '—' },
        { label: 'Discount', value: selectedFrequency?.discount ?? '—' },
        { label: 'Property', value: [input.property_type, input.bedrooms && `${input.bedrooms} bd`].filter(Boolean).join(' · ') || '—' },
        { label: 'Postal code', value: input.postal_code || '—' },
        { label: 'Add-ons', value: input.extras.length ? `${input.extras.length} selected` : 'None' },
      ],
    [input, selectedFrequency, selectedService],
  );

  if (finished) {
    return (
      <EnquirySent
        enquiry={finished}
        title={settings.quote_success_title}
        text={settings.quote_success_text}
        secondary={{ label: 'Explore our services', to: '/services' }}
      />
    );
  }

  return (
    <div className="quote-layout">
      <div className="quote-layout__form">
        <form
          className="wizard"
          onSubmit={(event) => {
            event.preventDefault();
            void submit();
          }}
          noValidate
        >
          <div className="wizard__steps" role="tablist" aria-label="Quote steps">
            {QUOTE_STEPS.map((item, index) => (
              <button
                key={item.id}
                type="button"
                role="tab"
                aria-selected={index === step}
                className={classNames(
                  'wizard__step',
                  index === step && 'is-active',
                  index < step && 'is-done',
                )}
                disabled={index > step}
                onClick={() => (index < step ? goTo(index) : undefined)}
              >
                <span className="wizard__step-index" aria-hidden="true">
                  {index < step ? <Icon name="check" size={12} /> : index + 1}
                </span>
                {item.label}
              </button>
            ))}
          </div>

          <div className="wizard__progress">
            <div className="wizard__progress-bar" style={{ width: `${progress}%` }} />
          </div>

          <div className="wizard__panel">
            <div className="wizard__panel-head">
              <h2 className="wizard__panel-title">
                Step {step + 1} of {QUOTE_STEPS.length} — {activeStep.label}
              </h2>
              <p className="wizard__panel-text">
                {step === 0
                  ? 'So we know who to contact with your quote.'
                  : step === 1
                    ? 'Sizing tells us how much time to allocate to your clean.'
                    : step === 2
                      ? 'Choose the package that fits, and how often you would like us.'
                      : step === 3
                        ? 'Tell us when to come and how to get in.'
                        : 'Anything else we should know before we prepare your quote.'}
              </p>
            </div>

            {failed ? (
              <div className="alert alert--error" role="alert">
                <Icon name="alert" size={17} />
                <span>{failed}</span>
              </div>
            ) : null}

            {step === 0 ? (
              <div className="form-grid form-grid--2">
                <div className="field field--full">
                  <label className="field__label" htmlFor="full_name">
                    Full name<span className="field__required">*</span>
                  </label>
                  <input
                    id="full_name"
                    className="input"
                    name="full_name"
                    autoComplete="name"
                    value={input.full_name}
                    onChange={(event) => update('full_name', event.target.value)}
                    aria-invalid={Boolean(errors.full_name)}
                    aria-describedby={errors.full_name ? 'full_name-error' : undefined}
                  />
                  {errors.full_name ? (
                    <p className="field__error" id="full_name-error">
                      <Icon name="alert" size={14} />
                      {errors.full_name}
                    </p>
                  ) : null}
                </div>

                <Field label="Email" htmlFor="email" error={errors.email}>
                  <input
                    id="email"
                    className="input"
                    name="email"
                    type="email"
                    autoComplete="email"
                    inputMode="email"
                    value={input.email}
                    onChange={(event) => update('email', event.target.value)}
                    aria-invalid={Boolean(errors.email)}
                  />
                </Field>

                <Field
                  label="Phone"
                  htmlFor="phone"
                  error={errors.phone}
                  hint="We only use this to confirm your quote and booking."
                >
                  <input
                    id="phone"
                    className="input"
                    name="phone"
                    type="tel"
                    autoComplete="tel"
                    inputMode="tel"
                    value={input.phone}
                    onChange={(event) => update('phone', event.target.value)}
                    aria-invalid={Boolean(errors.phone)}
                  />
                </Field>

                <input
                  ref={honeypot}
                  className="honeypot"
                  type="text"
                  name="company_website"
                  tabIndex={-1}
                  autoComplete="off"
                  aria-hidden="true"
                />
              </div>
            ) : null}

            {step === 1 ? (
              <div className="stack stack--lg">
                <div className="field">
                  <span className="field__label">Property type</span>
                  <div className="choice-grid">
                    {PROPERTY_TYPES.map((option) => (
                      <label className="radio-card" key={option}>
                        <input
                          type="radio"
                          name="property_type"
                          value={option}
                          checked={input.property_type === option}
                          onChange={() => update('property_type', option)}
                        />
                        <span className="radio-card__title">{option}</span>
                      </label>
                    ))}
                  </div>
                  {errors.property_type ? (
                    <p className="field__error">
                      <Icon name="alert" size={14} />
                      {errors.property_type}
                    </p>
                  ) : null}
                </div>

                {isCommercial ? (
                  <div className="alert alert--info">
                    <Icon name="info" size={17} />
                    <span>
                      Commercial and property management clients are covered by separate service
                      agreements. Tell us about the space below and the office will follow up with
                      a tailored proposal.
                    </span>
                  </div>
                ) : null}

                <div className="form-grid form-grid--2">
                  <Field label="Bedrooms" htmlFor="bedrooms" error={errors.bedrooms}>
                    <select
                      id="bedrooms"
                      className="select"
                      value={input.bedrooms}
                      onChange={(event) => update('bedrooms', event.target.value)}
                    >
                      {BEDROOM_OPTIONS.map((option) => (
                        <option key={option} value={option}>
                          {option === 'Studio' ? 'Studio / open plan' : `${option} bedroom${option === '1' ? '' : 's'}`}
                        </option>
                      ))}
                    </select>
                  </Field>

                  <Field label="Bathrooms" htmlFor="bathrooms" error={errors.bathrooms}>
                    <select
                      id="bathrooms"
                      className="select"
                      value={input.bathrooms}
                      onChange={(event) => update('bathrooms', event.target.value)}
                    >
                      {BATHROOM_OPTIONS.map((option) => (
                        <option key={option} value={option}>
                          {option} bathroom{option === '1' ? '' : 's'}
                        </option>
                      ))}
                    </select>
                  </Field>

                  <Field
                    label="Approximate size"
                    htmlFor="square_footage"
                    error={errors.square_footage}
                    hint="Not sure? Round up — size drives how much time we allocate."
                  >
                    <select
                      id="square_footage"
                      className="select"
                      value={input.square_footage}
                      onChange={(event) => update('square_footage', event.target.value)}
                    >
                      <option value="">Select a size…</option>
                      {SIZE_OPTIONS.map((option) => (
                        <option key={option} value={option}>
                          {option}
                        </option>
                      ))}
                    </select>
                  </Field>

                  <Field label="Neighbourhood or area" htmlFor="neighbourhood" error={errors.neighbourhood}>
                    <input
                      id="neighbourhood"
                      className="input"
                      list="m4c-areas"
                      value={input.neighbourhood}
                      placeholder="e.g. Liberty Village"
                      onChange={(event) => update('neighbourhood', event.target.value)}
                      aria-invalid={Boolean(errors.neighbourhood)}
                    />
                    <datalist id="m4c-areas">
                      {live.areas.map((area) => (
                        <option key={area.name} value={area.name} />
                      ))}
                    </datalist>
                  </Field>
                </div>
              </div>
            ) : null}

            {step === 2 ? (
              <div className="stack stack--lg">
                <div className="field">
                  <span className="field__label">
                    Cleaning service<span className="field__required">*</span>
                  </span>
                  <div className="choice-grid">
                    {live.services.map((service) => (
                      <label className="radio-card" key={service.slug}>
                        <input
                          type="radio"
                          name="service_slug"
                          value={service.slug}
                          checked={input.service_slug === service.slug}
                          onChange={() => {
                            if (!started) {
                              setStarted(true);
                              trackEvent('quote_form_start');
                            }
                            setInput((current) => ({
                              ...current,
                              service_slug: service.slug,
                              service_name: service.name,
                            }));
                            setErrors((current) => ({ ...current, service_slug: undefined }));
                            setFailed(null);
                          }}
                        />
                        <span className="radio-card__top">
                          <span className="radio-card__title">{service.name}</span>
                        </span>
                        <span className="radio-card__desc">{service.tagline}</span>
                      </label>
                    ))}
                  </div>
                  {errors.service_slug ? (
                    <p className="field__error">
                      <Icon name="alert" size={14} />
                      {errors.service_slug}
                    </p>
                  ) : null}
                </div>

                {isVacantService ? (
                  <div className="alert alert--warn">
                    <Icon name="info" size={17} />
                    <span>
                      Move In / Move Out cleanings are designed for vacant units — inside cabinets,
                      drawers, appliances and vents. All supplies, equipment and the vacuum are
                      included. If the home will be occupied, choose a Deep Clean or Deep Plus
                      instead.
                    </span>
                  </div>
                ) : null}

                <div className="field">
                  <span className="field__label">
                    How often would you like us?<span className="field__required">*</span>
                  </span>
                  <div className="choice-grid">
                    {live.frequencies.map((frequency) => (
                      <label className="radio-card" key={frequency.slug}>
                        <input
                          type="radio"
                          name="frequency"
                          value={frequency.slug}
                          checked={input.frequency === frequency.slug}
                          onChange={() => update('frequency', frequency.slug)}
                        />
                        <span className="radio-card__top">
                          <span className="radio-card__title">{frequency.label}</span>
                          <span className="radio-card__meta">{frequency.discount}</span>
                        </span>
                        <span className="radio-card__desc">{frequency.note}</span>
                        {frequency.recommended ? (
                          <span className="badge badge--brand" style={{ justifySelf: 'start' }}>
                            Recommended
                          </span>
                        ) : null}
                      </label>
                    ))}
                  </div>
                  {input.frequency && input.frequency !== 'one-time' ? (
                    <p className="field__hint">
                      AutoPilot clients save {selectedFrequency?.discount} on every visit and are not
                      tied to a contract.
                    </p>
                  ) : null}
                </div>
              </div>
            ) : null}

            {step === 3 ? (
              <div className="form-grid form-grid--2">
                <Field label="Street address" htmlFor="address" error={errors.address}>
                  <input
                    id="address"
                    className="input"
                    autoComplete="street-address"
                    value={input.address}
                    placeholder="1200 — 60 Atlantic Ave."
                    onChange={(event) => update('address', event.target.value)}
                  />
                </Field>

                <Field
                  label="Postal code"
                  htmlFor="postal_code"
                  error={errors.postal_code}
                  required={false}
                  hint="We confirm service availability by postal code."
                >
                  <input
                    id="postal_code"
                    className="input"
                    autoComplete="postal-code"
                    value={input.postal_code}
                    placeholder="M6K 1X9"
                    onChange={(event) => update('postal_code', event.target.value)}
                  />
                </Field>

                <Field label="Preferred date" htmlFor="preferred_date" error={errors.preferred_date}>
                  <input
                    id="preferred_date"
                    className="input"
                    type="date"
                    min={todayIso()}
                    value={input.preferred_date}
                    onChange={(event) => update('preferred_date', event.target.value)}
                  />
                </Field>

                <Field label="Arrival window" htmlFor="arrival_window" error={errors.arrival_window}>
                  <select
                    id="arrival_window"
                    className="select"
                    value={input.arrival_window}
                    onChange={(event) => update('arrival_window', event.target.value)}
                  >
                    <option value="">Select a window…</option>
                    {ARRIVAL_WINDOWS.map((option) => (
                      <option key={option} value={option}>
                        {option}
                      </option>
                    ))}
                  </select>
                </Field>

                <Field
                  label="How can we get in?"
                  htmlFor="access_method"
                  error={errors.access_method}
                  hint="Key at concierge and lockbox access are the most efficient."
                >
                  <select
                    id="access_method"
                    className="select"
                    value={input.access_method}
                    onChange={(event) => update('access_method', event.target.value)}
                  >
                    <option value="">Select an option…</option>
                    {ACCESS_METHODS.map((option) => (
                      <option key={option} value={option}>
                        {option}
                      </option>
                    ))}
                  </select>
                </Field>

                <div className="field field--full">
                  <p className="form-note">{settings.service_hours}</p>
                </div>
              </div>
            ) : null}

            {step === 4 ? (
              <div className="stack stack--lg">
                <div className="field">
                  <span className="field__label">Add-ons</span>
                  <div className="choice-grid">
                    {EXTRAS.map((extra) => (
                      <label className="checkbox" key={extra.slug}>
                        <input
                          type="checkbox"
                          checked={input.extras.includes(extra.slug)}
                          onChange={(event) => {
                            const nextExtras = event.target.checked
                              ? [...input.extras, extra.slug]
                              : input.extras.filter((slug) => slug !== extra.slug);
                            update('extras', nextExtras);
                          }}
                        />
                        <span className="checkbox__label">
                          {extra.label}
                          {isVacantService &&
                          (extra.slug === 'inside-oven' || extra.slug === 'inside-fridge') ? (
                            <span>Already included in Move In / Move Out.</span>
                          ) : null}
                        </span>
                      </label>
                    ))}
                  </div>
                </div>

                <div className="form-grid form-grid--2">
                  <Field
                    label="How did you hear about us?"
                    htmlFor="referral_source"
                    required={false}
                  >
                    <select
                      id="referral_source"
                      className="select"
                      value={input.referral_source}
                      onChange={(event) => update('referral_source', event.target.value)}
                    >
                      <option value="">Prefer not to say</option>
                      {REFERRAL_SOURCES.map((option) => (
                        <option key={option} value={option}>
                          {option}
                        </option>
                      ))}
                    </select>
                  </Field>
                </div>

                <div className="field">
                  <label className="field__label" htmlFor="notes">
                    Additional information
                  </label>
                  <textarea
                    id="notes"
                    className="textarea"
                    rows={5}
                    value={input.notes}
                    placeholder="Pets, allergies, parking, building details, areas you want us to focus on — anything that helps us prepare."
                    onChange={(event) => update('notes', event.target.value)}
                  />
                </div>

                <label className="checkbox">
                  <input
                    type="checkbox"
                    checked={consent}
                    onChange={(event) => {
                      setConsent(event.target.checked);
                      if (event.target.checked) setConsentError(null);
                    }}
                  />
                  <span className="checkbox__label">
                    I agree to the terms of service and privacy policy.
                    <span>
                      We use your details only to prepare and confirm your quote. See our{' '}
                      <Link to="/terms">terms</Link> and <Link to="/privacy-policy">privacy policy</Link>.
                    </span>
                  </span>
                </label>
                {consentError ? (
                  <p className="field__error">
                    <Icon name="alert" size={14} />
                    {consentError}
                  </p>
                ) : null}
              </div>
            ) : null}

            <div className="form-note">
              <strong>Please note:</strong> published package prices are starting prices. Your final
              price is confirmed before anything is booked — you will never be charged without your
              approval.
            </div>
          </div>

          <div className="wizard__nav">
            <button
              type="button"
              className="btn btn--ghost"
              onClick={back}
              disabled={step === 0}
            >
              <Icon name="arrow" size={17} style={{ transform: 'rotate(180deg)' }} />
              Back
            </button>

            {step < QUOTE_STEPS.length - 1 ? (
              <button type="button" className="btn btn--primary" onClick={next}>
                Continue
                <Icon name="arrow" size={17} />
              </button>
            ) : (
              <button type="submit" className="btn btn--primary btn--lg" disabled={submitting}>
                {submitting ? <span className="spinner" /> : <Icon name="checkCircle" size={18} />}
                {submitting ? 'Sending…' : 'Request a quote'}
              </button>
            )}
          </div>
        </form>
      </div>

      <aside className="service-layout__aside" aria-label="Your selections">
        <div className="wizard__summary">
          <h2 className="info-card__title">Your selections</h2>
          {summaryRows.map((row) => (
            <div className="wizard__summary-row" key={row.label}>
              <span className="wizard__summary-label">{row.label}</span>
              <span className="wizard__summary-value">{row.value}</span>
            </div>
          ))}
          <p className="field__hint">
            Your request is confirmed with the office, who size up the details and the condition of
            your home before you book.
          </p>
        </div>

        <div className="info-card">
          <h2 className="info-card__title">Need a hand?</h2>
          <p className="card__text">
            Our office is open Monday to Friday, 8:00 am to 6:00 pm. Call and we will build the
            quote with you.
          </p>
          <a
            className="btn btn--secondary btn--block"
            href={`tel:${settings.phone.replace(/[^\d]/g, '')}`}
            data-ga-event="phone_click"
            data-ga-label="Quote sidebar"
          >
            <Icon name="phone" size={17} />
            {settings.phone_display}
          </a>
        </div>

        <div className="info-card">
          <h2 className="info-card__title">What happens next</h2>
          <ul className="check-list">
            {[
              'We review your details and confirm availability.',
              'You receive a clear price and a visit window.',
              'Our team arrives and works through the checklist.',
            ].map((line) => (
              <li key={line}>
                <Icon name="check" size={16} />
                <span>{line}</span>
              </li>
            ))}
          </ul>
        </div>
      </aside>
    </div>
  );
}
