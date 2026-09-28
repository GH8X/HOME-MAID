/** Contact — phone, email, address, hours, a message form, the map and areas. */

import { Link } from 'react-router-dom';

import { ContactForm } from '../components/ContactForm';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { Reveal, SectionHeading } from '../components/ui';
import { CUSTOMER_NOTICES } from '../data/siteContent';
import { useSeo } from '../hooks/useSeo';
import { telHref } from '../lib/format';
import { breadcrumbSchema, localBusinessSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function Contact() {
  const { settings, live } = useContent();

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'Contact', path: '/contact' },
  ];

  const socials = [
    { label: 'Facebook', url: settings.social_facebook },
    { label: 'Instagram', url: settings.social_instagram },
    { label: 'X', url: settings.social_twitter },
  ].filter((entry) => Boolean(entry.url));

  const mapSrc = `https://www.google.com/maps?q=${encodeURIComponent(
    settings.map_query || `${settings.address_street} ${settings.address_city}`,
  )}&output=embed`;
  const mapLink = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
    settings.map_query || `${settings.address_street} ${settings.address_city}`,
  )}`;

  useSeo({
    title: 'Contact Maid4Condos | Toronto Condo Cleaning',
    description: `Call ${settings.phone_display} or send a message to Maid4Condos. Office at ${settings.address_street}, ${settings.address_city}, serving Toronto and the GTA.`,
    path: '/contact',
    image: '/images/cleaning-floors.jpg',
    schema: [
      localBusinessSchema(settings, live.services, live.areas),
      breadcrumbSchema(crumbs),
    ],
  });

  return (
    <>
      <PageHero
        eyebrow="Contact"
        title="Talk to the Maid4Condos office"
        intro="Questions about a clean, a building, or a quote you have already requested? Call, email or send us a message — a real person replies."
        crumbs={crumbs}
        actions={
          <>
            <a
              className="btn btn--accent btn--lg"
              href={telHref(settings.phone)}
              data-ga-event="phone_click"
              data-ga-label="Contact hero"
            >
              <Icon name="phone" size={18} />
              {settings.phone_display}
            </a>
            <Link className="btn btn--onbrand btn--lg" to="/get-a-quote">
              Get a quote
            </Link>
          </>
        }
      />

      <section className="section">
        <div className="container container--wide">
          <div className="contact-cards">
            <article className="contact-card">
              <span className="contact-card__icon">
                <Icon name="phone" size={19} />
              </span>
              <span className="contact-card__title">Phone</span>
              <a
                className="contact-card__value"
                href={telHref(settings.phone)}
                data-ga-event="phone_click"
                data-ga-label="Contact card"
              >
                {settings.phone_display}
              </a>
              <span className="contact-card__meta">
                The fastest way to reach us. Office hours below.
              </span>
            </article>

            <article className="contact-card">
              <span className="contact-card__icon">
                <Icon name="mail" size={19} />
              </span>
              <span className="contact-card__title">General email</span>
              <a
                className="contact-card__value"
                href={`mailto:${settings.email}`}
                data-ga-event="email_click"
                data-ga-label="Contact card"
              >
                {settings.email}
              </a>
              <span className="contact-card__meta">For general questions about our services.</span>
            </article>

            <article className="contact-card">
              <span className="contact-card__icon">
                <Icon name="clipboard" size={19} />
              </span>
              <span className="contact-card__title">Bookings</span>
              <a
                className="contact-card__value"
                href={`mailto:${settings.booking_email}`}
                data-ga-event="email_click"
                data-ga-label="Contact card"
              >
                {settings.booking_email}
              </a>
              <span className="contact-card__meta">
                Bookings, changes, special instructions and tips.
              </span>
            </article>

            <article className="contact-card">
              <span className="contact-card__icon">
                <Icon name="pin" size={19} />
              </span>
              <span className="contact-card__title">Office</span>
              <span className="contact-card__value">
                {settings.address_street}
                <br />
                {settings.address_city}, {settings.address_region} {settings.address_postal}
              </span>
              <span className="contact-card__meta">{settings.address_country} · Liberty Village</span>
            </article>

            <article className="contact-card">
              <span className="contact-card__icon">
                <Icon name="clock" size={19} />
              </span>
              <span className="contact-card__title">Office hours</span>
              <span className="contact-card__value">
                {settings.office_hours.split('\n').map((line) => (
                  <span key={line}>
                    {line}
                    <br />
                  </span>
                ))}
              </span>
              <span className="contact-card__meta">
                Residential cleanings run Monday – Friday, 8:00 am – 6:00 pm.
              </span>
            </article>

            <article className="contact-card">
              <span className="contact-card__icon">
                <Icon name="map" size={19} />
              </span>
              <span className="contact-card__title">Service area</span>
              <span className="contact-card__value">Toronto & the GTA</span>
              <span className="contact-card__meta">
                {live.areas
                  .slice(0, 6)
                  .map((area) => area.name)
                  .join(' · ')}{' '}
                and more.
              </span>
            </article>

            {socials.length ? (
              <article className="contact-card">
                <span className="contact-card__icon">
                  <Icon name="external" size={19} />
                </span>
                <span className="contact-card__title">Social</span>
                <span className="contact-card__value">Follow Maid4Condos</span>
                <div className="footer__social">
                  {socials.map((entry) => (
                    <a
                      key={entry.label}
                      className="social-link social-link--light"
                      href={entry.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label={`Maid4Condos on ${entry.label}`}
                    >
                      <Icon name="external" size={17} />
                    </a>
                  ))}
                </div>
                <span className="contact-card__meta">
                  Booking reminders, cleaning tips and before-and-after photos.
                </span>
              </article>
            ) : null}
          </div>
        </div>
      </section>

      <section className="section section--surface">
        <div className="container container--wide">
          <div className="contact-layout">
            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Send a message"
                title="How can we help?"
                intro="Tell us what you need and we will reply during office hours. For a cleaning quote, the quote form gets you a faster answer."
              />
              <ContactForm />
            </div>

            <aside className="service-layout__aside">
              <div className="info-card info-card--brand">
                <h2 className="info-card__title">Skip the wait</h2>
                <p className="card__text">
                  The quote form asks the handful of questions we need, so we can price your clean
                  on the first reply.
                </p>
                <Link className="btn btn--accent btn--block" to="/get-a-quote">
                  Go to the quote form
                </Link>
              </div>

              <div className="info-card">
                <h2 className="info-card__title">Service window</h2>
                <p className="card__text">{settings.service_hours}</p>
                <ul className="check-list">
                  <li>
                    <Icon name="check" size={16} />
                    <span>Two-hour or flexible arrival windows</span>
                  </li>
                  <li>
                    <Icon name="check" size={16} />
                    <span>Key at concierge or lockbox access welcomed</span>
                  </li>
                  <li>
                    <Icon name="check" size={16} />
                    <span>Email reminder 5 days before, SMS 3 days before</span>
                  </li>
                </ul>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container container--wide">
          <SectionHeading
            eyebrow="Find us"
            title="Liberty Village, Toronto"
            intro="Our corporate office is on Atlantic Avenue. Cleanings happen at your place — we come to you."
          />

          <div className="map-frame">
            <iframe
              title={`Map showing ${settings.address_street}, ${settings.address_city}`}
              src={mapSrc}
              loading="lazy"
              referrerPolicy="no-referrer-when-downgrade"
              allowFullScreen
            />
            <div className="map-frame__overlay">
              <strong>Maid4Condos</strong>
              <span>
                {settings.address_street}, {settings.address_city} {settings.address_postal}
              </span>
              <a
                href={mapLink}
                target="_blank"
                rel="noopener noreferrer"
                className="btn btn--secondary btn--sm"
                data-ga-event="map_click"
                data-ga-label="Contact map"
              >
                <Icon name="external" size={15} />
                Open in Google Maps
              </a>
            </div>
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="Good to know"
              title="Important customer information"
              intro="Everything a client needs to know before, during and after a Maid4Condos cleaning."
            />
          </Reveal>
          <div className="grid grid--2">
            {CUSTOMER_NOTICES.map((notice, index) => (
              <Reveal key={notice.title} delay={index * 30}>
                <article className="trust-card">
                  <span className="trust-card__icon">
                    <Icon name="info" size={20} />
                  </span>
                  <h3 className="trust-card__title">{notice.title}</h3>
                  <p className="trust-card__text">{notice.text}</p>
                </article>
              </Reveal>
            ))}
          </div>
          <p className="form-note mt-6">
            The full detail sits in our <Link to="/terms">service terms</Link> and{' '}
            <Link to="/faq">frequently asked questions</Link>.
          </p>
        </div>
      </section>

      <section className="section section--sand">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="Where we clean"
              title="Service areas across Toronto"
              intro={settings.home_areas_intro}
            />
          </Reveal>
          <div className="grid grid--3">
            {live.areas.map((area, index) => (
              <Reveal key={area.name} delay={index * 25}>
                <div className="area-card">
                  <span className="area-card__name">
                    <Icon name="pin" size={16} />
                    {area.name}
                  </span>
                  {area.note ? <span className="area-card__note">{area.note}</span> : null}
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
