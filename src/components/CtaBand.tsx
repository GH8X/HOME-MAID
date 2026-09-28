/** Closing call to action used at the bottom of most pages. */

import { Link } from 'react-router-dom';

import { telHref } from '../lib/format';
import { useContent } from '../services/contentService';
import { Icon } from './Icon';
import type { IconName } from '../lib/types';

export interface CtaBandProps {
  heading: string;
  text: string;
  primaryLabel?: string;
  secondaryLabel?: string;
  secondaryTo?: string;
  asideItems?: Array<{ icon: IconName; text: string }>;
}

export function CtaBand({
  heading,
  text,
  primaryLabel = 'Get a quote',
  secondaryLabel = 'View services',
  secondaryTo = '/services',
  asideItems,
}: CtaBandProps) {
  const { settings } = useContent();

  const items =
    asideItems ??
    [
      { icon: 'checkCircle' as IconName, text: 'No obligation, no pressure — a clear price first.' },
      { icon: 'clock' as IconName, text: 'Most quotes are confirmed the same business day.' },
      { icon: 'phone' as IconName, text: `Prefer to talk? Call ${settings.phone_display}.` },
    ];

  return (
    <section className="section">
      <div className="container">
        <div className="cta-band">
          <div className="cta-band__copy">
            <h2 className="cta-band__title">{heading}</h2>
            <p className="cta-band__text">{text}</p>
            <div className="cta-band__actions">
              <Link
                className="btn btn--accent btn--lg"
                to="/get-a-quote"
                data-ga-event="quote_cta_click"
                data-ga-label="CTA band"
              >
                {primaryLabel}
                <Icon name="arrow" size={18} />
              </Link>
              {secondaryTo.startsWith('tel:') || secondaryTo.startsWith('mailto:') ? (
                <a className="btn btn--onbrand btn--lg" href={secondaryTo}>
                  {secondaryLabel}
                </a>
              ) : (
                <Link className="btn btn--onbrand btn--lg" to={secondaryTo}>
                  {secondaryLabel}
                </Link>
              )}
            </div>
          </div>

          <div className="cta-band__aside">
            {items.map((item) => (
              <p className="cta-band__aside-item" key={item.text}>
                <Icon name={item.icon} size={17} />
                <span>{item.text}</span>
              </p>
            ))}
            <a
              className="btn btn--onbrand btn--sm"
              href={telHref(settings.phone)}
              data-ga-event="phone_click"
              data-ga-label="CTA band"
            >
              <Icon name="phone" size={16} />
              {settings.phone_display}
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
