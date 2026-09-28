/** About — the family story, our promise, credentials and what we do not do. */

import { Link } from 'react-router-dom';

import { CtaBand } from '../components/CtaBand';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { BrandMark, Reveal, SectionHeading, SmartImage } from '../components/ui';
import { ABOUT } from '../data/pages';
import { useSeo } from '../hooks/useSeo';
import { telHref } from '../lib/format';
import { breadcrumbSchema, localBusinessSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function About() {
  const { settings, live } = useContent();

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'About', path: '/about' },
  ];

  useSeo({
    title: ABOUT.metaTitle,
    description: ABOUT.metaDescription,
    path: '/about',
    image: '/images/hero-cleaning-modern-home.jpg',
    schema: [
      localBusinessSchema(settings, live.services, live.areas),
      breadcrumbSchema(crumbs),
    ],
  });

  return (
    <>
      <PageHero
        eyebrow={ABOUT.eyebrow}
        title={ABOUT.heading}
        intro={ABOUT.intro}
        crumbs={crumbs}
        actions={
          <>
            <Link className="btn btn--accent btn--lg" to="/get-a-quote" data-ga-event="quote_cta_click" data-ga-label="About hero">
              Get a quote
              <Icon name="arrow" size={18} />
            </Link>
            <Link className="btn btn--onbrand btn--lg" to="/testimonials">
              Read client reviews
            </Link>
          </>
        }
        meta={[
          { value: '2014', label: 'In business since' },
          { value: '100%', label: 'Employee cleaners' },
          { value: settings.liability_coverage, label: 'Liability coverage (CAD)' },
        ]}
      />

      <section className="section">
        <div className="container container--wide">
          <div className="split">
            <div className="stack stack--lg">
              <SectionHeading eyebrow="Our story" title="Partners-in-clean, not strangers with a mop" />
              <div className="prose">
                {ABOUT.story.map((paragraph) => (
                  <p key={paragraph.slice(0, 40)}>{paragraph}</p>
                ))}
              </div>

              <div className="stat-row">
                <div className="stat">
                  <span className="stat__value">2014</span>
                  <span className="stat__label">Founded in Toronto</span>
                </div>
                <div className="stat">
                  <span className="stat__value">{settings.reviews_site_count}</span>
                  <span className="stat__label">Reviews on maid4condos.com</span>
                </div>
                <div className="stat">
                  <span className="stat__value">{settings.reviews_google_count}</span>
                  <span className="stat__label">Reviews on Google</span>
                </div>
              </div>
            </div>

            <div className="why-media">
              <figure className="why-media__figure why-media__figure--tall">
                <SmartImage
                  name="cleaning-vacuum-rug"
                  sizes="(min-width: 900px) 24vw, 48vw"
                  alt="Maid4Condos cleaner vacuuming a rug in a Toronto condo living room"
                />
              </figure>
              <figure className="why-media__figure">
                <SmartImage
                  name="cleaning-bathroom-sink"
                  sizes="(min-width: 900px) 24vw, 48vw"
                  alt="Cleaning a bathroom sink and countertop during a condo clean"
                />
              </figure>
              <figure className="why-media__figure">
                <SmartImage
                  name="cleaning-vacuum-detail"
                  sizes="(min-width: 900px) 24vw, 48vw"
                  alt="Detail vacuum work in a well-lit condo interior"
                />
              </figure>
            </div>
          </div>
        </div>
      </section>

      <section className="section section--surface">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="What we promise"
              title={ABOUT.promiseHeading}
              intro="Eleven commitments that apply to every single visit."
              align="center"
            />
          </Reveal>
          <div className="grid grid--3">
            {ABOUT.promise.map((item, index) => (
              <Reveal key={item} delay={index * 30}>
                <article className="trust-card">
                  <span className="trust-card__icon">
                    <Icon name="check" size={20} />
                  </span>
                  <p className="trust-card__title">{item}</p>
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container container--wide">
          <div className="split split--media-first">
            <div className="stack stack--lg">
              <span className="badge badge--brand">
                <BrandMark size={18} />
                Employees, not contractors
              </span>
              <SectionHeading eyebrow="Credentials" title={ABOUT.membershipsHeading} />
              <div className="prose">
                {ABOUT.memberships.map((paragraph) => (
                  <p key={paragraph.slice(0, 40)}>{paragraph}</p>
                ))}
              </div>
              <div className="grid grid--2">
                {[
                  { icon: 'shieldCheck' as const, title: 'Bonded & insured', text: `${settings.liability_coverage} in liability coverage, WSIB covered, fully bonded.` },
                  { icon: 'badge' as const, title: 'ISSA & ARCSI members', text: 'North American cleaning associations for residential and commercial.' },
                  { icon: 'team' as const, title: 'Background checked', text: 'Vetted for character, trained for skill, master class certified.' },
                  { icon: 'leaf' as const, title: 'Considered products', text: 'Professional grade and biodegradable, from trusted brands.' },
                ].map((item) => (
                  <article className="trust-card" key={item.title}>
                    <span className="trust-card__icon">
                      <Icon name={item.icon} size={20} />
                    </span>
                    <h3 className="trust-card__title">{item.title}</h3>
                    <p className="trust-card__text">{item.text}</p>
                  </article>
                ))}
              </div>
            </div>

            <div className="callout callout--accent">
              <p className="callout__title">
                <Icon name="alert" size={19} />
                {ABOUT.notIncludedHeading}
              </p>
              <p className="callout__text">{ABOUT.notIncludedIntro}</p>
              <ul className="check-list check-list--cross">
                {ABOUT.notIncluded.map((line) => (
                  <li key={line}>
                    <Icon name="close" size={15} />
                    <span>{line}</span>
                  </li>
                ))}
              </ul>
              <Link className="btn btn--secondary btn--sm" to="/terms">
                Read the full terms
              </Link>
            </div>
          </div>
        </div>
      </section>

      <section className="section section--brand">
        <div className="container container--wide">
          <div className="split">
            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Our guarantee"
                title="If it is not right, we come back"
                intro="The Maid4Condos guarantee, in full."
              />
              <p className="section-head__intro">{settings.guarantee_text}</p>
            </div>
            <div className="cta-band__aside">
              <p className="cta-band__aside-item">
                <Icon name="phone" size={17} />
                <span>{settings.phone_display}</span>
              </p>
              <p className="cta-band__aside-item">
                <Icon name="mail" size={17} />
                <span>{settings.email}</span>
              </p>
              <p className="cta-band__aside-item">
                <Icon name="pin" size={17} />
                <span>
                  {settings.address_street}, {settings.address_city} {settings.address_postal}
                </span>
              </p>
              <a className="btn btn--light" href={telHref(settings.phone)} data-ga-event="phone_click" data-ga-label="About guarantee">
                Call the office
              </a>
            </div>
          </div>
        </div>
      </section>

      <CtaBand
        heading="Meet the team behind the clean"
        text="Tell us about your condo and we will match you with the right package and the right cleaners."
        secondaryLabel="Read client reviews"
        secondaryTo="/testimonials"
      />
    </>
  );
}
