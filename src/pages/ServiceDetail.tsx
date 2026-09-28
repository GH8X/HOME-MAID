/** Service detail — one page per cleaning package. */

import { useEffect } from 'react';
import { Link, useParams } from 'react-router-dom';

import { FaqAccordion } from '../components/FaqAccordion';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { ServiceCard } from '../components/ServiceCard';
import { Reveal, SectionHeading, SmartImage } from '../components/ui';
import { NotFound } from './NotFound';
import { useSeo } from '../hooks/useSeo';
import { trackEvent } from '../lib/analytics';
import { entriesOf, telHref } from '../lib/format';
import { breadcrumbSchema, faqSchema, serviceSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function ServiceDetail() {
  const { slug = '' } = useParams();
  const { settings, live, serviceBySlug } = useContent();
  const service = serviceBySlug(slug);

  useEffect(() => {
    if (service) trackEvent('service_view', { service: service.slug });
  }, [service]);

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'Services', path: '/services' },
    { name: service?.name ?? 'Package', path: `/services/${slug}` },
  ];

  useSeo({
    title: service?.meta_title || `${service?.name ?? 'Service'} | Maid4Condos`,
    description: service?.meta_description || service?.summary,
    path: `/services/${slug}`,
    image: service ? `/images/${service.hero_image}.jpg` : undefined,
    schema: service
      ? [
          serviceSchema(service, settings),
          breadcrumbSchema(crumbs),
          ...(service.faqs.length ? [faqSchema(service.faqs)] : []),
        ]
      : [],
  });

  if (!service) return <NotFound />;

  const checklistSections = entriesOf(service.checklist);
  const totalItems = checklistSections.reduce((sum, [, items]) => sum + items.length, 0);
  const related = live.services.filter((entry) => entry.slug !== service.slug).slice(0, 3);

  return (
    <>
      <PageHero
        eyebrow={service.eyebrow}
        title={service.name}
        intro={service.tagline}
        crumbs={crumbs}
        actions={
          <>
            <Link
              className="btn btn--accent btn--lg"
              to={`/get-a-quote?service=${service.slug}`}
              data-ga-event="quote_cta_click"
              data-ga-label={`Service hero: ${service.name}`}
            >
              Book now
              <Icon name="arrow" size={18} />
            </Link>
            <a
              className="btn btn--onbrand btn--lg"
              href={telHref(settings.phone)}
              data-ga-event="phone_click"
              data-ga-label={`Service hero: ${service.name}`}
            >
              <Icon name="phone" size={17} />
              {settings.phone_display}
            </a>
          </>
        }
        meta={[
          { value: '24 hours', label: 'Satisfaction guarantee' },
          { value: `${totalItems}`, label: 'Checklist points' },
          { value: `${service.faqs.length}`, label: 'FAQs' },
        ]}
      />

      <section className="section">
        <div className="container container--wide">
          <div className="service-layout">
            <div className="stack stack--lg">
              <Reveal>
                <figure className="service-media">
                  <SmartImage
                    name={service.hero_image}
                    priority
                    sizes="(min-width: 1000px) 62vw, 100vw"
                    alt={`${service.name} cleaning in a Toronto condo by Maid4Condos`}
                  />
                </figure>
              </Reveal>

              <div className="prose">
                {service.intro.map((paragraph) => (
                  <p key={paragraph.slice(0, 40)}>{paragraph}</p>
                ))}
              </div>

              <div className="callout">
                <p className="callout__title">
                  <Icon name="user" size={19} />
                  Who this package is for
                </p>
                <ul className="check-list">
                  {service.who_for.map((line) => (
                    <li key={line}>
                      <Icon name="check" size={16} />
                      <span>{line}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

            <aside className="service-layout__aside">
              <div className="info-card info-card--brand">
                <h2 className="info-card__title">At a glance</h2>
                <div className="info-card__row">
                  <span className="info-card__label">Pricing</span>
                  <span className="info-card__value">
                    Confirmed with the office when you book
                  </span>
                </div>
                <div className="info-card__row">
                  <span className="info-card__label">Time on site</span>
                  <span className="info-card__value">{service.duration_note}</span>
                </div>
                <div className="info-card__row">
                  <span className="info-card__label">Best paired with</span>
                  <span className="info-card__value">{service.best_paired}</span>
                </div>
                <Link
                  className="btn btn--accent btn--block"
                  to={`/get-a-quote?service=${service.slug}`}
                  data-ga-event="quote_cta_click"
                  data-ga-label={`Service sidebar: ${service.name}`}
                >
                  Request this clean
                </Link>
                <p className="field__hint" style={{ color: 'inherit' }}>
                  Starting prices are published by Maid4Condos. Your final price is confirmed after
                  we review your sizing and the condition of the space.
                </p>
              </div>

              <div className="info-card">
                <h2 className="info-card__title">Our guarantee</h2>
                <p className="card__text">{settings.guarantee_text}</p>
              </div>

              <div className="info-card">
                <h2 className="info-card__title">Scheduling</h2>
                <p className="card__text">{settings.service_hours}</p>
                <div className="info-card__row">
                  <span className="info-card__label">Office</span>
                  <span className="info-card__value">
                    {settings.office_hours.split('\n').join(' · ')}
                  </span>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <section className="section section--surface">
        <div className="container container--wide">
          <SectionHeading
            eyebrow="What's included"
            title={`The ${service.name} checklist`}
            intro={`${totalItems} points, grouped by room. Our team works through the list and then checks it a second time before leaving.`}
          />

          <div className="checklist">
            {checklistSections.map(([section, items], index) => (
              <Reveal key={section} delay={index * 40}>
                <div className="checklist__group">
                  <h3 className="checklist__group-title">
                    <Icon name="checkCircle" size={18} />
                    {section}
                    <span className="checklist__group-count">{items.length}</span>
                  </h3>
                  <ul className="checklist__items">
                    {items.map((item) => (
                      <li key={item}>
                        <Icon name="check" size={15} />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container container--wide">
          <div className="split">
            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Why clients choose it"
                title="What this package gives you"
              />
              <ul className="why-points">
                {service.benefits.map((benefit) => (
                  <li key={benefit}>
                    <Icon name="check" size={17} />
                    <span>{benefit}</span>
                  </li>
                ))}
              </ul>
              <div className="callout callout--accent">
                <p className="callout__title">
                  <Icon name="spark" size={19} />
                  Extras you can add
                </p>
                <ul className="tag-list">
                  {live.extras.map((extra) => (
                    <li key={extra.slug}>
                      <span className="badge">{extra.name}</span>
                    </li>
                  ))}
                </ul>
                <p className="callout__text">
                  {service.slug === 'move-in-move-out'
                    ? 'Inside Oven and Inside Fridge are already included with this package.'
                    : 'Tell us which extras you would like when you request your quote.'}
                </p>
              </div>
            </div>

            <div className="stack stack--lg">
              <SectionHeading eyebrow="Good to know" title={`${service.name} FAQs`} />
              {service.faqs.length ? (
                <FaqAccordion items={service.faqs} idPrefix={`service-${service.slug}`} />
              ) : null}
              <div className="info-card">
                <h2 className="info-card__title">What is not included</h2>
                <ul className="check-list check-list--cross">
                  {[
                    'Inside or hard-to-reach light fixtures and exterior windows',
                    'Inside dishwashers, washing machines or hood fans',
                    'Moving anything heavier than 30 lbs',
                    'Exterior cleaning, including balconies',
                  ].map((line) => (
                    <li key={line}>
                      <Icon name="close" size={15} />
                      <span>{line}</span>
                    </li>
                  ))}
                </ul>
                <Link to="/terms">Read the full terms</Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="section section--sand">
        <div className="container container--wide">
          <SectionHeading
            eyebrow="Keep exploring"
            title="Other cleaning packages"
            intro="Many clients start with a reset and then maintain it with a recurring schedule."
          />
          <div className="grid grid--3">
            {related.map((entry) => (
              <ServiceCard key={entry.slug} service={entry} />
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <div className="cta-band">
            <div className="cta-band__copy">
              <h2 className="cta-band__title">Ready to book your {service.name}?</h2>
              <p className="cta-band__text">
                Tell us about your condo and we will confirm your price and the visit window that
                suits you.
              </p>
              <div className="cta-band__actions">
                <Link
                  className="btn btn--accent btn--lg"
                  to={`/get-a-quote?service=${service.slug}`}
                  data-ga-event="quote_cta_click"
                  data-ga-label={`Service footer: ${service.name}`}
                >
                  Get a quote
                  <Icon name="arrow" size={18} />
                </Link>
                <Link className="btn btn--onbrand btn--lg" to="/contact">
                  Contact the office
                </Link>
              </div>
            </div>
            <div className="cta-band__aside">
              <p className="cta-band__aside-item">
                <Icon name="phone" size={17} />
                <span>
                  <strong style={{ color: '#fff' }}>{settings.phone_display}</strong>
                  <br />
                  {settings.office_hours.split('\n')[0]}
                </span>
              </p>
              <p className="cta-band__aside-item">
                <Icon name="checkCircle" size={17} />
                <span>{settings.guarantee_text.slice(0, 120)}…</span>
              </p>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
