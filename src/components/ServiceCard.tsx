/** Service package card used on the homepage and the services index. */

import { Link, useLocation } from 'react-router-dom';

import { trackEvent } from '../lib/analytics';
import type { Service } from '../lib/types';
import { Icon } from './Icon';
import { SmartImage } from './ui';

export function ServiceCard({ service, priority = false }: { service: Service; priority?: boolean }) {
  const location = useLocation();

  return (
    <article className="card card--hover service-card">
      <Link
        to={`/services/${service.slug}`}
        className="card__media service-card__media"
        aria-label={`${service.name} details`}
        onClick={() =>
          trackEvent('service_card_click', { service: service.slug, from: location.pathname })
        }
      >
        <SmartImage
          name={service.image}
          alt={`${service.name} by Maid4Condos in Toronto`}
          sizes="(min-width: 980px) 33vw, (min-width: 620px) 50vw, 100vw"
          priority={priority}
        />
        <span className="service-card__tag">{service.eyebrow}</span>
      </Link>

      <div className="card__body">
        <div className="service-card__header">
          <h3 className="card__title">{service.name}</h3>
        </div>

        <p className="card__text">{service.summary}</p>

        <ul className="service-card__list">
          {service.benefits.slice(0, 3).map((benefit) => (
            <li key={benefit}>
              <Icon name="check" size={15} />
              <span>{benefit}</span>
            </li>
          ))}
        </ul>

        <div className="service-card__actions">
          <Link
            className="btn btn--secondary btn--sm"
            to={`/services/${service.slug}`}
            onClick={() =>
              trackEvent('service_card_click', { service: service.slug, from: location.pathname })
            }
          >
            Learn more
          </Link>
          <Link
            className="btn btn--primary btn--sm"
            to={`/get-a-quote?service=${service.slug}`}
            data-ga-event="quote_cta_click"
            data-ga-label={`Service card: ${service.name}`}
          >
            Book now
          </Link>
        </div>
      </div>
    </article>
  );
}
