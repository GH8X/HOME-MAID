/** 404 — a genuinely useful dead end. */

import { Link } from 'react-router-dom';

import { Icon } from '../components/Icon';
import { NOT_FOUND } from '../data/pages';
import { useSeo } from '../hooks/useSeo';
import { useContent } from '../services/contentService';

export function NotFound() {
  const { live } = useContent();

  useSeo({
    title: NOT_FOUND.metaTitle,
    description: 'The page you were looking for does not exist or has moved.',
    path: '/404',
    robots: 'noindex,follow',
  });

  return (
    <section className="section">
      <div className="container container--narrow not-found">
        <span className="not-found__code" aria-hidden="true">
          404
        </span>
        <h1>{NOT_FOUND.heading}</h1>
        <p className="section-head__intro">{NOT_FOUND.intro}</p>

        <div className="not-found__links">
          <Link className="btn btn--primary" to="/get-a-quote" data-ga-event="quote_cta_click" data-ga-label="404">
            Get a quote
            <Icon name="arrow" size={17} />
          </Link>
          <Link className="btn btn--secondary" to="/services">
            All cleaning packages
          </Link>
          <Link className="btn btn--ghost" to="/">
            Back to home
          </Link>
        </div>

        <div className="grid grid--3 mt-8">
          {live.services.slice(0, 3).map((service) => (
            <Link className="area-card" key={service.slug} to={`/services/${service.slug}`}>
              <span className="area-card__name">
                <Icon name="services" size={16} />
                {service.name}
              </span>
              <span className="area-card__note">{service.tagline}</span>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}
