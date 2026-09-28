/**
 * Contact form.
 *
 * The site is static, so a message is turned into a written summary the visitor
 * sends to the office rather than a row in a database.
 */

import { useRef, useState } from 'react';
import { Link } from 'react-router-dom';

import { trackEvent } from '../lib/analytics';
import type { ContactMessageInput, PreparedEnquiry } from '../lib/types';
import {
  CONTACT_TOPICS,
  EMPTY_CONTACT,
  hasErrors,
  validateContact,
  type ContactErrorMap,
} from '../lib/validation';
import { useContent } from '../services/contentService';
import { contactEnquiry, submitContactMessage } from '../services/quoteService';
import { EnquirySent } from './EnquirySent';
import { Icon } from './Icon';

export function ContactForm() {
  const { settings } = useContent();
  const [input, setInput] = useState<ContactMessageInput>(EMPTY_CONTACT);
  const [errors, setErrors] = useState<ContactErrorMap>({});
  const [submitting, setSubmitting] = useState(false);
  const [failed, setFailed] = useState<string | null>(null);
  const [sent, setSent] = useState<PreparedEnquiry | null>(null);
  const honeypot = useRef<HTMLInputElement | null>(null);

  const update = <K extends keyof ContactMessageInput>(key: K, value: ContactMessageInput[K]) => {
    setInput((current) => ({ ...current, [key]: value }));
    setErrors((current) => ({ ...current, [key]: undefined }));
  };

  if (sent) {
    return (
      <EnquirySent
        enquiry={sent}
        title="Your message is ready to send"
        text={settings.contact_success_text}
        secondary={{ label: 'Book a cleaning', to: '/get-a-quote' }}
      />
    );
  }

  return (
    <form
      className="form"
      noValidate
      onSubmit={async (event) => {
        event.preventDefault();
        const found = validateContact(input);
        if (hasErrors(found)) {
          setErrors(found);
          setFailed('Please check the highlighted fields.');
          return;
        }
        if (honeypot.current?.value) {
          // Bots fill hidden fields. Show the same success state and drop it.
          setSent(contactEnquiry(input));
          return;
        }

        setSubmitting(true);
        setFailed(null);
        const result = await submitContactMessage(input);
        setSubmitting(false);

        if (!result.ok) {
          setFailed(result.message ?? 'Something went wrong. Please call the office instead.');
          return;
        }
        trackEvent('contact_form_submit', { topic: input.topic });
        setSent(contactEnquiry(input));
      }}
    >
      {failed ? (
        <div className="alert alert--error" role="alert">
          <Icon name="alert" size={17} />
          <span>{failed}</span>
        </div>
      ) : null}

      <div className="form-grid form-grid--2">
        <div className="field">
          <label className="field__label" htmlFor="contact-name">
            Your name<span className="field__required">*</span>
          </label>
          <input
            id="contact-name"
            className="input"
            autoComplete="name"
            value={input.full_name}
            onChange={(event) => update('full_name', event.target.value)}
            aria-invalid={Boolean(errors.full_name)}
          />
          {errors.full_name ? (
            <p className="field__error">
              <Icon name="alert" size={14} />
              {errors.full_name}
            </p>
          ) : null}
        </div>

        <div className="field">
          <label className="field__label" htmlFor="contact-email">
            Email<span className="field__required">*</span>
          </label>
          <input
            id="contact-email"
            className="input"
            type="email"
            autoComplete="email"
            value={input.email}
            onChange={(event) => update('email', event.target.value)}
            aria-invalid={Boolean(errors.email)}
          />
          {errors.email ? (
            <p className="field__error">
              <Icon name="alert" size={14} />
              {errors.email}
            </p>
          ) : null}
        </div>

        <div className="field">
          <label className="field__label" htmlFor="contact-phone">
            Phone <span className="muted">(optional)</span>
          </label>
          <input
            id="contact-phone"
            className="input"
            type="tel"
            autoComplete="tel"
            value={input.phone}
            onChange={(event) => update('phone', event.target.value)}
            aria-invalid={Boolean(errors.phone)}
          />
          {errors.phone ? (
            <p className="field__error">
              <Icon name="alert" size={14} />
              {errors.phone}
            </p>
          ) : null}
        </div>

        <div className="field">
          <label className="field__label" htmlFor="contact-topic">
            What is this about?<span className="field__required">*</span>
          </label>
          <select
            id="contact-topic"
            className="select"
            value={input.topic}
            onChange={(event) => update('topic', event.target.value)}
            aria-invalid={Boolean(errors.topic)}
          >
            <option value="">Choose a topic…</option>
            {CONTACT_TOPICS.map((topic) => (
              <option key={topic} value={topic}>
                {topic}
              </option>
            ))}
          </select>
          {errors.topic ? (
            <p className="field__error">
              <Icon name="alert" size={14} />
              {errors.topic}
            </p>
          ) : null}
        </div>

        <div className="field field--full">
          <label className="field__label" htmlFor="contact-message">
            Your message<span className="field__required">*</span>
          </label>
          <textarea
            id="contact-message"
            className="textarea"
            rows={6}
            value={input.message}
            onChange={(event) => update('message', event.target.value)}
            aria-invalid={Boolean(errors.message)}
            placeholder="Tell us about your condo, your building, or the question you have."
          />
          {errors.message ? (
            <p className="field__error">
              <Icon name="alert" size={14} />
              {errors.message}
            </p>
          ) : null}
        </div>

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

      <div className="form-actions">
        <p className="form-note">
          By sending this message you agree to our <Link to="/privacy-policy">privacy policy</Link>.
          We never share your details with third parties.
        </p>
        <button type="submit" className="btn btn--primary btn--lg" disabled={submitting}>
          {submitting ? <span className="spinner" /> : <Icon name="mail" size={18} />}
          {submitting ? 'Sending…' : 'Send message'}
        </button>
      </div>
    </form>
  );
}
