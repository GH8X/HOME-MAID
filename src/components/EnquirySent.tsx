/**
 * The panel a visitor sees after a successful submission.
 *
 * The site is hosted as static files, so nothing is stored or emailed on the
 * visitor's behalf. Rather than claim a request was filed, the panel hands the
 * visitor their own request: one press opens their mail client with the whole
 * summary filled in, and a second copies it for pasting anywhere else. Both
 * quote and contact forms use this, so the wording and behaviour never drift
 * apart.
 */

import { useState } from 'react';
import { Link } from 'react-router-dom';

import { trackEvent } from '../lib/analytics';
import { telHref } from '../lib/format';
import type { PreparedEnquiry } from '../lib/types';
import { useContent } from '../services/contentService';
import { Icon } from './Icon';

interface EnquirySentProps {
  enquiry: PreparedEnquiry;
  /** Headline from the settings file. */
  title: string;
  /** Supporting sentence from the settings file. */
  text: string;
  /** An extra route offered alongside the send actions. */
  secondary?: { label: string; to: string };
}

export function EnquirySent({ enquiry, title, text, secondary }: EnquirySentProps) {
  const { settings } = useContent();
  const [copied, setCopied] = useState(false);

  const copy = async () => {
    try {
      await navigator.clipboard.writeText(enquiry.body);
      setCopied(true);
      trackEvent('enquiry_copy');
      window.setTimeout(() => setCopied(false), 2400);
    } catch {
      // Clipboard blocked (insecure context or denied). The summary is still
      // on screen under "Read your request", so nothing is lost.
      setCopied(false);
    }
  };

  return (
    <div className="success-panel">
      <span className="success-panel__icon">
        <Icon name="check" size={30} />
      </span>
      <h2>{title}</h2>
      <p className="success-panel__text">{text}</p>

      <p className="form-note">
        Send your request to <strong>{settings.booking_email}</strong> — the button below opens
        your email with every detail already written out.
      </p>

      <div className="success-panel__actions">
        <a
          className="btn btn--primary btn--lg"
          href={enquiry.mailto}
          data-ga-event="quote_submit"
          data-ga-label="Success panel"
          onClick={() => trackEvent('enquiry_send')}
        >
          <Icon name="mail" size={18} />
          Send your request
        </a>
        <button type="button" className="btn btn--secondary btn--lg" onClick={() => void copy()}>
          <Icon name={copied ? 'check' : 'clipboard'} size={18} />
          {copied ? 'Summary copied' : 'Copy the summary'}
        </button>
      </div>

      <div className="success-panel__actions">
        <a
          className="btn btn--ghost"
          href={telHref(settings.phone)}
          data-ga-event="phone_click"
          data-ga-label="Success panel"
        >
          <Icon name="phone" size={17} />
          {settings.phone_display}
        </a>
        <a className="btn btn--ghost" href={`mailto:${settings.email}`} data-ga-event="email_click">
          <Icon name="mail" size={17} />
          {settings.email}
        </a>
        {secondary ? (
          <Link className="btn btn--ghost" to={secondary.to}>
            {secondary.label}
          </Link>
        ) : null}
      </div>

      <details className="enquiry-preview">
        <summary>Read your request</summary>
        <pre className="enquiry-preview__body">{enquiry.body}</pre>
      </details>
    </div>
  );
}
