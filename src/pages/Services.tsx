/** Services index — all six packages, a comparison table, add-ons and FAQs. */

import { Link } from 'react-router-dom';

import { CtaBand } from '../components/CtaBand';
import { FaqAccordion } from '../components/FaqAccordion';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { ServiceCard } from '../components/ServiceCard';
import { Reveal, SectionHeading } from '../components/ui';
import { useSeo } from '../hooks/useSeo';
import { breadcrumbSchema, localBusinessSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function Services() {
  const { settings, live } = useContent();

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'Services', path: '/services' },
  ];

  const generalFaqs = live.faqs.filter((faq) =>
    ['Booking', 'Payment & policies'].includes(faq.category),
  );

  useSeo({
    title: 'Condo Cleaning Packages & Prices Toronto | Maid4Condos',
    description:
      'Compare Maid4Condos cleaning packages: Basic, Basic Plus, Deep, Deep Plus, Move In / Move Out and recurring AutoPilot schedules, with starting prices for Toronto.',
    path: '/services',
    image: '/images/cleaning-kitchen-cabinets.jpg',
    schema: [
      localBusinessSchema(settings, live.services, live.areas),
      breadcrumbSchema(crumbs),
    ],
  });

  return (
    <>
      <PageHero
        eyebrow="Services"
        title="Cleaning packages built around condo life"
        intro="Every Maid4Condos visit follows a written checklist, and every package is priced transparently. Choose the depth of clean and how often you would like us — we confirm the final price before anything is booked."
        crumbs={crumbs}
        actions={
          <>
            <Link className="btn btn--accent btn--lg" to="/get-a-quote" data-ga-event="quote_cta_click" data-ga-label="Services hero">
              Get a quote
              <Icon name="arrow" size={18} />
            </Link>
            <Link className="btn btn--onbrand btn--lg" to="/contact">
              Ask a question
            </Link>
          </>
        }
        meta={[
          { value: `${live.services.length}`, label: 'Packages' },
          { value: '30+', label: 'Checklist points' },
          { value: '20%', label: 'Max recurring discount' },
          { value: '2014', label: 'Serving Toronto since' },
        ]}
      />

      <section className="section">
        <div className="container container--wide">
          <div className="grid grid--3">
            {live.services.map((service, index) => (
              <Reveal key={service.slug} delay={index * 50}>
                <ServiceCard service={service} priority={index < 3} />
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      <section className="section section--surface">
        <div className="container container--wide">
          <SectionHeading
            eyebrow="Side by side"
            title="Which clean is right for my condo?"
            intro="A quick comparison of what each package covers. Every cleaning is booked with the office, who confirm the details and availability for your home."
          />

          <div className="table-scroll">
            <table className="compare-table">
              <caption className="visually-hidden">
                Comparison of Maid4Condos cleaning packages
              </caption>
              <thead>
                <tr>
                  <th scope="col">Package</th>
                  <th scope="col">Best for</th>
                  <th scope="col">Time</th>
                  <th scope="col">Frequencies</th>
                  <th scope="col">Book</th>
                </tr>
              </thead>
              <tbody>
                {live.services.map((service) => (
                  <tr key={service.slug}>
                    <th scope="row">
                      <Link to={`/services/${service.slug}`}>{service.name}</Link>
                    </th>
                    <td>{service.who_for[0] ?? service.summary}</td>
                    <td>{service.duration_note}</td>
                    <td>{service.best_paired}</td>
                    <td>
                      <Link
                        className="btn btn--primary btn--sm"
                        to={`/get-a-quote?service=${service.slug}`}
                        data-ga-event="quote_cta_click"
                        data-ga-label={`Compare table: ${service.name}`}
                      >
                        Book now
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="grid grid--3 mt-8">
            {live.frequencies.map((frequency) => (
              <div className="info-card" key={frequency.slug}>
                <div className="info-card__row">
                  <span className="info-card__label">Schedule</span>
                  <span className="info-card__value">
                    {frequency.label}
                    {frequency.recommended ? ' · recommended' : ''}
                  </span>
                </div>
                <p className="info-card__value" style={{ textAlign: 'left' }}>
                  <strong>{frequency.discount}</strong>
                </p>
                <p className="card__text">{frequency.note}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container container--wide">
          <SectionHeading
            eyebrow="Add-ons"
            title="Finishing touches for any clean"
            intro="Add any of these to a visit when you book, or call the office to add one to a service you have already reserved."
          />

          <div className="grid grid--3">
            {live.extras.map((extra, index) => (
              <Reveal key={extra.slug} delay={index * 40}>
                <article className="extra-card">
                  <h3 className="extra-card__title">
                    <Icon name="spark" size={18} />
                    {extra.name}
                  </h3>
                  <p className="extra-card__text">{extra.summary}</p>
                  {extra.details ? <p className="extra-card__detail">{extra.details}</p> : null}
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      <section className="section section--sand">
        <div className="container container--wide">
          <div className="split">
            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Booking questions"
                title="Before you book"
                intro="The answers people ask us most before their first cleaning."
              />
              <div>
                <Link className="btn btn--secondary" to="/faq">
                  See all {live.faqs.length} questions
                  <Icon name="arrow" size={17} />
                </Link>
              </div>
            </div>
            <div>
              <FaqAccordion items={generalFaqs.slice(0, 5)} idPrefix="services-faq" />
            </div>
          </div>
        </div>
      </section>

      <CtaBand
        heading="Not sure which package fits?"
        text="Tell us about your condo and we will recommend the right package and confirm a price — no obligation, no pressure."
        secondaryLabel="Call the office"
        secondaryTo={`tel:${settings.phone.replace(/[^\d]/g, '')}`}
      />
    </>
  );
}
