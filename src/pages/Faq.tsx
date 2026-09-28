/** FAQ — every published question, filterable by category. */

import { useMemo, useState } from 'react';
import { Link } from 'react-router-dom';

import { CtaBand } from '../components/CtaBand';
import { FaqGroups, groupFaqs } from '../components/FaqAccordion';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { classNames } from '../lib/format';
import { useSeo } from '../hooks/useSeo';
import { breadcrumbSchema, faqSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function Faq() {
  const { settings, live, faqCategories } = useContent();
  const [active, setActive] = useState<string>('All');

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'FAQ', path: '/faq' },
  ];

  const visible = useMemo(
    () => (active === 'All' ? live.faqs : live.faqs.filter((faq) => faq.category === active)),
    [active, live.faqs],
  );

  const groups = useMemo(() => groupFaqs(visible), [visible]);

  useSeo({
    title: 'Cleaning Service FAQs | Maid4Condos Toronto',
    description:
      'Answers about booking, arrival windows, access, products, pets, pricing, cancellations, guarantees and what Maid4Condos does not do — from our Toronto cleaning team.',
    path: '/faq',
    image: '/images/cleaned-living-room.jpg',
    schema: [faqSchema(live.faqs), breadcrumbSchema(crumbs)],
  });

  return (
    <>
      <PageHero
        eyebrow="Help centre"
        title="Frequently asked questions"
        intro={`${live.faqs.length} answers covering booking, your clean, payment and policies, hours, and the jobs we do not take on. If your question is not here, call the office on ${settings.phone_display}.`}
        crumbs={crumbs}
        actions={
          <>
            <Link className="btn btn--accent btn--lg" to="/get-a-quote" data-ga-event="quote_cta_click" data-ga-label="FAQ hero">
              Get a quote
              <Icon name="arrow" size={18} />
            </Link>
            <Link className="btn btn--onbrand btn--lg" to="/contact">
              Ask us directly
            </Link>
          </>
        }
      />

      <section className="section">
        <div className="container container--wide">
          <div className="faq-filters" role="group" aria-label="Filter questions by category">
            {['All', ...faqCategories].map((category) => (
              <button
                key={category}
                type="button"
                className={classNames('faq-filter', active === category && 'is-active')}
                aria-pressed={active === category}
                onClick={() => setActive(category)}
              >
                {category}
                {category === 'All' ? ` (${live.faqs.length})` : ''}
              </button>
            ))}
          </div>

          {groups.length ? (
            <FaqGroups groups={groups} idPrefix="faq-page" />
          ) : (
            <p className="muted">No questions in that category yet.</p>
          )}
        </div>
      </section>

      <section className="section section--surface">
        <div className="container container--wide">
          <div className="grid grid--3">
            <article className="info-card">
              <h2 className="info-card__title">
                <Icon name="phone" size={18} /> Prefer to talk?
              </h2>
              <p className="card__text">
                Our office is open Monday to Friday, 8:00 am to 6:00 pm, and Saturday and Sunday
                9:00 am to 4:00 pm.
              </p>
              <a
                className="btn btn--secondary"
                href={`tel:${settings.phone.replace(/[^\d]/g, '')}`}
                data-ga-event="phone_click"
                data-ga-label="FAQ page"
              >
                {settings.phone_display}
              </a>
            </article>
            <article className="info-card">
              <h2 className="info-card__title">
                <Icon name="mail" size={18} /> Email the office
              </h2>
              <p className="card__text">
                For bookings, changes to an existing visit or special instructions, email our
                booking team directly.
              </p>
              <a
                className="btn btn--secondary"
                href={`mailto:${settings.booking_email}`}
                data-ga-event="email_click"
                data-ga-label="FAQ page"
              >
                {settings.booking_email}
              </a>
            </article>
            <article className="info-card">
              <h2 className="info-card__title">
                <Icon name="clipboard" size={18} /> Ready to book?
              </h2>
              <p className="card__text">
                Tell us about your condo and we will confirm the package, price and visit window.
              </p>
              <Link className="btn btn--primary" to="/get-a-quote">
                Get a quote
              </Link>
            </article>
          </div>
        </div>
      </section>

      <CtaBand
        heading="Still have a question?"
        text="Send us a message and a real person from the Maid4Condos office will get back to you."
        secondaryLabel="Contact us"
        secondaryTo="/contact"
      />
    </>
  );
}
