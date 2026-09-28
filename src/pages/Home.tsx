/**
 * Homepage.
 *
 * Hero → trust → packages → why → how it works → the service video → where we
 * clean → reviews → People Like Us → make a review → FAQ → final CTA.
 */

import { Link } from 'react-router-dom';

import { CtaBand } from '../components/CtaBand';
import { FaqAccordion } from '../components/FaqAccordion';
import { Icon } from '../components/Icon';
import { PeopleLikeUs } from '../components/PeopleLikeUs';
import { ReviewCta } from '../components/ReviewCta';
import { ServiceCard } from '../components/ServiceCard';
import { ServiceVideo } from '../components/ServiceVideo';
import { TestimonialCard } from '../components/TestimonialCard';
import { BrandMark, Reveal, SectionHeading, SmartImage } from '../components/ui';
import { image } from '../data/media';
import { useSeo } from '../hooks/useSeo';
import { parseJson, telHref } from '../lib/format';
import { breadcrumbSchema, localBusinessSchema } from '../lib/schema';
import type { HowStep, TrustItem } from '../lib/types';
import { useContent } from '../services/contentService';

const HERO_BADGES: Array<{ icon: TrustItem['icon']; title: string; text: string }> = [
  { icon: 'shieldCheck', title: 'Professional Service', text: 'Background-checked employees' },
  { icon: 'checkCircle', title: 'Reliable Cleaning', text: '24 hour satisfaction guarantee' },
  { icon: 'calendar', title: 'Flexible Scheduling', text: 'Weekly through to one-time' },
  { icon: 'pin', title: 'Serving Toronto & GTA', text: 'Condo specialists since 2014' },
];

export function Home() {
  const { settings, live, faqCategories } = useContent();

  const trustItems = parseJson<TrustItem[]>(settings.home_trust_items, []);
  const whyPoints = parseJson<string[]>(settings.home_why_points, []);
  const howSteps = parseJson<HowStep[]>(settings.home_how_steps, []);
  const heroBullets = parseJson<string[]>(settings.home_hero_bullets, []);

  const previewFaqs = live.faqs.slice(0, 5);
  const previewTestimonials = live.testimonials.slice(0, 4);

  useSeo({
    title: settings.default_meta_title,
    description: settings.default_meta_description,
    path: '/',
    image: image(settings.og_image).src,
    robots: settings.robots_policy || 'index,follow',
    schema: [
      localBusinessSchema(settings, live.services, live.areas),
      breadcrumbSchema([{ name: 'Home', path: '/' }]),
    ],
  });

  return (
    <>
      {/* Hero ------------------------------------------------------------- */}
      <section className="hero">
        <div className="container container--wide hero__inner">
          <div className="hero__copy">
            <p className="eyebrow">{settings.home_hero_eyebrow}</p>
            <h1 className="hero__title">{settings.home_hero_title}</h1>
            <p className="hero__text">{settings.home_hero_subtitle}</p>

            <div className="hero__actions">
              <Link
                className="btn btn--primary btn--lg"
                to="/get-a-quote"
                data-ga-event="quote_cta_click"
                data-ga-label="Homepage hero"
              >
                Get a quote
                <Icon name="arrow" size={18} />
              </Link>
              <Link className="btn btn--secondary btn--lg" to="/services">
                Explore services
              </Link>
            </div>

            <ul className="hero__bullets">
              {heroBullets.map((bullet) => (
                <li key={bullet}>
                  <Icon name="check" size={16} />
                  <span>{bullet}</span>
                </li>
              ))}
            </ul>
          </div>

          <div className="hero__media">
            <figure className="hero__figure">
              <SmartImage
                name={settings.home_hero_image}
                priority
                sizes="(min-width: 980px) 48vw, 100vw"
                alt={image(settings.home_hero_image).alt}
              />
            </figure>

            <div className="hero__rating">
              <span className="hero__rating-value">{settings.reviews_site_count}</span>
              <span className="hero__rating-label">Client reviews</span>
            </div>

            <div className="hero__floating">
              <p className="hero__floating-title">
                <Icon name="badge" size={18} />
                The 24 hour guarantee
              </p>
              <p className="hero__floating-text">
                Tell the office within 24 hours and we will return within 3 days to make it right.
              </p>
              <a
                className="btn btn--secondary btn--sm"
                href={telHref(settings.phone)}
                data-ga-event="phone_click"
                data-ga-label="Homepage hero card"
              >
                <Icon name="phone" size={16} />
                {settings.phone_display}
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Trust strip ------------------------------------------------------ */}
      <section className="section section--tight">
        <div className="container container--wide">
          <Reveal>
            <div className="trust-strip">
              {HERO_BADGES.map((badge) => (
                <div className="trust-strip__item" key={badge.title}>
                  <span className="trust-strip__icon">
                    <Icon name={badge.icon} size={19} />
                  </span>
                  <span>
                    <span className="trust-strip__title">{badge.title}</span>
                    <span className="trust-strip__text">{badge.text}</span>
                  </span>
                </div>
              ))}
            </div>
          </Reveal>
        </div>
      </section>

      {/* Services --------------------------------------------------------- */}
      <section className="section section--surface" id="services">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="Our packages"
              title={settings.home_services_heading}
              intro={settings.home_services_intro}
            />
          </Reveal>

          <div className="grid grid--3">
            {live.services.map((service, index) => (
              <Reveal key={service.slug} delay={index * 60}>
                <ServiceCard service={service} priority={index < 3} />
              </Reveal>
            ))}
          </div>

          <div className="center-row mt-8">
            <Link className="btn btn--secondary" to="/services">
              Compare every package
              <Icon name="arrow" size={17} />
            </Link>
          </div>
        </div>
      </section>

      {/* Why choose ------------------------------------------------------- */}
      <section className="section">
        <div className="container container--wide">
          <div className="split split--media-first">
            <div className="why-media">
              <figure className="why-media__figure why-media__figure--tall">
                <SmartImage
                  name="cleaning-kitchen-stove"
                  sizes="(min-width: 900px) 24vw, 48vw"
                  alt="Maid4Condos cleaner wiping down a stovetop during a condo deep clean"
                />
              </figure>
              <figure className="why-media__figure">
                <SmartImage
                  name="clean-bathroom"
                  sizes="(min-width: 900px) 24vw, 48vw"
                  alt="Freshly cleaned white bathroom in a Toronto condo"
                />
              </figure>
              <figure className="why-media__figure">
                <SmartImage
                  name="clean-kitchen"
                  sizes="(min-width: 900px) 24vw, 48vw"
                  alt="Clean modern kitchen with polished countertops after a Maid4Condos visit"
                />
              </figure>
            </div>

            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Why Maid4Condos"
                title={settings.home_why_heading}
                intro={settings.home_why_intro}
              />
              <ul className="why-points">
                {whyPoints.map((point) => (
                  <li key={point}>
                    <Icon name="check" size={17} />
                    <span>{point}</span>
                  </li>
                ))}
              </ul>
              <div className="callout">
                <p className="callout__title">
                  <Icon name="shieldCheck" size={19} />
                  Bonded, insured and truly employed
                </p>
                <p className="callout__text">
                  Every cleaner is a Maid4Condos employee — never a contractor — with 5,000,000 in
                  liability coverage, WSIB coverage and bonding. We are members of ISSA and ARCSI.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Trust grid ------------------------------------------------------- */}
      <section className="section section--sand">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="What you can count on"
              title={settings.home_trust_heading}
              intro={settings.home_trust_intro}
              align="center"
            />
          </Reveal>
          <div className="grid grid--3">
            {trustItems.map((item, index) => (
              <Reveal key={item.title} delay={index * 50}>
                <article className="trust-card">
                  <span className="trust-card__icon">
                    <Icon name={item.icon} size={21} />
                  </span>
                  <h3 className="trust-card__title">{item.title}</h3>
                  <p className="trust-card__text">{item.text}</p>
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* How it works ----------------------------------------------------- */}
      <section className="section">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="From click to clean"
              title={settings.home_how_heading}
              intro={settings.home_how_intro}
              align="center"
            />
          </Reveal>
          <div className="grid grid--4">
            {howSteps.map((step, index) => (
              <Reveal key={step.title} delay={index * 60}>
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
        </div>
      </section>

      {/* Service video ---------------------------------------------------- */}
      <ServiceVideo />

      {/* Service areas ---------------------------------------------------- */}
      <section className="section section--surface">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="Where we clean"
              title="Toronto neighbourhoods we service"
              intro={settings.home_areas_intro}
            />
          </Reveal>
          <div className="grid grid--3">
            {live.areas.slice(0, 9).map((area, index) => (
              <Reveal key={area.name} delay={index * 35}>
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
          <p className="form-note mt-6">
            Not on the list? Send your postal code with your quote request and we will confirm
            whether we cover your building.
          </p>
        </div>
      </section>

      {/* Testimonials ----------------------------------------------------- */}
      <section className="section">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="Client reviews"
              title={settings.home_testimonials_heading}
              intro={settings.home_testimonials_intro}
            />
          </Reveal>
          <div className="grid grid--masonry">
            {previewTestimonials.map((testimonial) => (
              <TestimonialCard
                key={`${testimonial.name}-${testimonial.quote.slice(0, 12)}`}
                testimonial={testimonial}
              />
            ))}
          </div>
          <div className="center-row mt-8">
            <Link className="btn btn--secondary" to="/testimonials">
              Read more reviews
              <Icon name="arrow" size={17} />
            </Link>
          </div>
        </div>
      </section>

      {/* People like us + make a review ------------------------------------ */}
      <PeopleLikeUs />
      <ReviewCta />

      {/* FAQ preview ------------------------------------------------------ */}
      <section className="section section--sand">
        <div className="container container--wide">
          <div className="split">
            <div className="stack stack--lg">
              <SectionHeading
                eyebrow="Good to know"
                title="Questions we hear every week"
                intro={`Answers drawn from our full FAQ — ${live.faqs.length} questions covering booking, products, policies and what we do not do.`}
              />
              <ul className="tag-list">
                {faqCategories.map((category) => (
                  <li key={category}>
                    <span className="badge badge--outline">{category}</span>
                  </li>
                ))}
              </ul>
              <div>
                <Link className="btn btn--primary" to="/faq">
                  Read the full FAQ
                  <Icon name="arrow" size={17} />
                </Link>
              </div>
            </div>

            <div>
              <FaqAccordion items={previewFaqs} idPrefix="home-faq" />
            </div>
          </div>
        </div>
      </section>

      {/* Final CTA -------------------------------------------------------- */}
      <Reveal>
        <CtaBand
          heading={settings.home_final_heading}
          text={settings.home_final_text}
          secondaryLabel="Call the office"
          secondaryTo={telHref(settings.phone)}
          asideItems={[
            { icon: 'clipboard', text: 'A clear, no-obligation quote' },
            { icon: 'checkCircle', text: 'Trusted in condos across Toronto since 2014' },
            { icon: 'leaf', text: 'Professional grade, biodegradable products' },
          ]}
        />
      </Reveal>

      {/* Footer brand strip ---------------------------------------------- */}
      <section className="section section--tight">
        <div className="container container--wide">
          <div className="trust-strip">
            <div className="trust-strip__item">
              <BrandMark size={44} className="brand__mark" />
              <span>
                <span className="trust-strip__title">Family run since 2014</span>
                <span className="trust-strip__text">
                  Maid4Condos is a Toronto company, not a franchise or a marketplace.
                </span>
              </span>
            </div>
            <div className="trust-strip__item">
              <span className="trust-strip__icon">
                <Icon name="building" size={19} />
              </span>
              <span>
                <span className="trust-strip__title">60 Atlantic Ave., Suite 200</span>
                <span className="trust-strip__text">Liberty Village, Toronto ON M6K 1X9</span>
              </span>
            </div>
            <div className="trust-strip__item">
              <span className="trust-strip__icon">
                <Icon name="clock" size={19} />
              </span>
              <span>
                <span className="trust-strip__title">Office hours</span>
                <span className="trust-strip__text">
                  Mon – Fri 8:00 am – 6:00 pm · Sat & Sun 9:00 am – 4:00 pm
                </span>
              </span>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
