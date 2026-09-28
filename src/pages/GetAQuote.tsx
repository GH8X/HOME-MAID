/** Get a quote — the multi-step wizard plus supporting trust content. */

import { Link } from 'react-router-dom';

import { FaqAccordion } from '../components/FaqAccordion';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { QuoteForm } from '../components/QuoteForm';
import { Reveal, SectionHeading } from '../components/ui';
import { useSeo } from '../hooks/useSeo';
import { telHref } from '../lib/format';
import { breadcrumbSchema, localBusinessSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function GetAQuote() {
  const { settings, live } = useContent();

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'Get a quote', path: '/get-a-quote' },
  ];

  const quoteFaqs = live.faqs
    .filter((faq) => ['Booking', 'Payment & policies'].includes(faq.category))
    .slice(0, 6);

  useSeo({
    title: 'Get a Free Cleaning Quote in Toronto | Maid4Condos',
    description:
      'Request a free, no-obligation quote for condo cleaning in Toronto. Tell us about your space and we will confirm your package, price and visit window.',
    path: '/get-a-quote',
    image: '/images/cleaning-kitchen-cabinets.jpg',
    schema: [
      localBusinessSchema(settings, live.services, live.areas),
      breadcrumbSchema(crumbs),
    ],
  });

  return (
    <>
      <PageHero
        eyebrow="Free quote"
        title="Tell us about your condo"
        intro="Five short steps and we will come back with a clear price and the next available visit window. No obligation, and no charge to get a quote."
        crumbs={crumbs}
        actions={
          <a
            className="btn btn--onbrand btn--lg"
            href={telHref(settings.phone)}
            data-ga-event="phone_click"
            data-ga-label="Quote hero"
          >
            <Icon name="phone" size={17} />
            Or call {settings.phone_display}
          </a>
        }
        meta={[
          { value: '60s', label: 'To complete' },
          { value: 'Free', label: 'No obligation' },
          { value: '20%', label: 'Max recurring discount' },
        ]}
      />

      <section className="section">
        <div className="container container--wide">
          <QuoteForm />
        </div>
      </section>

      <section className="section section--surface">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="What happens next"
              title="From quote to spotless in four steps"
              align="center"
            />
          </Reveal>
          <div className="grid grid--4">
            {[
              { title: 'You send the details', text: 'Property, sizing, package and the window that suits you.' },
              { title: 'We review and price it', text: 'We confirm availability for your building and your date.' },
              { title: 'You approve the quote', text: 'Nothing is booked and nothing is charged without your go-ahead.' },
              { title: 'We clean', text: 'Our team works through the checklist, checks it twice and leaves.' },
            ].map((step, index) => (
              <Reveal key={step.title} delay={index * 50}>
                <article className="step-card">
                  <span className="step-card__index" aria-hidden="true">
                    {index + 1}
                  </span>
                  <h3 className="step-card__title">{step.title}</h3>
                  <p className="step-card__text">{step.text}</p>
                </article>
              </Reveal>
            ))}
          </div>

          <div className="grid grid--3 mt-8">
            <article className="trust-card">
              <span className="trust-card__icon">
                <Icon name="shieldCheck" size={20} />
              </span>
              <h3 className="trust-card__title">Bonded, insured, employed</h3>
              <p className="trust-card__text">
                Everyone who enters your home is a background-checked Maid4Condos employee covered
                by WSIB, with {settings.liability_coverage} in liability coverage.
              </p>
            </article>
            <article className="trust-card">
              <span className="trust-card__icon">
                <Icon name="badge" size={20} />
              </span>
              <h3 className="trust-card__title">The 24 hour guarantee</h3>
              <p className="trust-card__text">{settings.guarantee_text.slice(0, 180)}…</p>
            </article>
            <article className="trust-card">
              <span className="trust-card__icon">
                <Icon name="calendar" size={20} />
              </span>
              <h3 className="trust-card__title">Flexible, no contracts</h3>
              <p className="trust-card__text">
                Recurring AutoPilot clients are not tied to a contract, and future dates can be
                moved or skipped with enough notice.
              </p>
            </article>
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container container--wide">
          <div className="split">
            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Before you ask"
                title="Common quote questions"
                intro="If your question is not here, the full FAQ covers pricing, access, pets, products and cancellations."
              />
              <div className="stack">
                <Link className="btn btn--secondary" to="/faq">
                  Read the full FAQ
                  <Icon name="arrow" size={17} />
                </Link>
                <Link className="btn btn--ghost" to="/services">
                  Compare the packages
                </Link>
              </div>
            </div>
            <div>
              <FaqAccordion items={quoteFaqs} idPrefix="quote-faq" />
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
