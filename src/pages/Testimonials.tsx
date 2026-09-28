/**
 * Testimonials — only reviews published by Maid4Condos. No aggregateRating is
 * emitted because the company publishes review counts, not an average score.
 */

import { Link } from 'react-router-dom';

import { CtaBand } from '../components/CtaBand';
import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { PeopleLikeUs } from '../components/PeopleLikeUs';
import { ReviewCta } from '../components/ReviewCta';
import { TestimonialCard } from '../components/TestimonialCard';
import { Reveal, SectionHeading } from '../components/ui';
import { useSeo } from '../hooks/useSeo';
import { breadcrumbSchema, localBusinessSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export function Testimonials() {
  const { settings, live } = useContent();

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'Testimonials', path: '/testimonials' },
  ];

  useSeo({
    title: 'Maid4Condos Reviews from Toronto Condo Clients',
    description:
      'Read reviews from Maid4Condos clients in Toronto and North York — on punctuality, attention to detail, flexibility and trusting us with a key.',
    path: '/testimonials',
    image: '/images/cleaned-living-room.jpg',
    schema: [
      localBusinessSchema(settings, live.services, live.areas),
      breadcrumbSchema(crumbs),
    ],
  });

  return (
    <>
      <PageHero
        eyebrow="Client reviews"
        title="What Toronto condo clients say"
        intro="Every review on this page is published by Maid4Condos. We have not written, edited or invented a single one — including the short ones."
        crumbs={crumbs}
        actions={
          <>
            <Link className="btn btn--accent btn--lg" to="/get-a-quote" data-ga-event="quote_cta_click" data-ga-label="Testimonials hero">
              Get a quote
              <Icon name="arrow" size={18} />
            </Link>
            <Link className="btn btn--onbrand btn--lg" to="/services">
              Explore services
            </Link>
          </>
        }
        meta={[
          { value: settings.reviews_site_count, label: 'On maid4condos.com' },
          { value: settings.reviews_google_count, label: 'On Google' },
          { value: settings.reviews_yelp_count, label: 'On Yelp' },
        ]}
      />

      <section className="section">
        <div className="container container--wide">
          <div className="grid grid--masonry">
            {live.testimonials.map((testimonial) => (
              <TestimonialCard
                key={`${testimonial.name}-${testimonial.quote.slice(0, 16)}`}
                testimonial={testimonial}
              />
            ))}
          </div>

          <p className="form-note mt-8">
            Reviews are reproduced exactly as published on maid4condos.com, Google and Yelp. Longer
            reviews have been left in full.
          </p>
        </div>
      </section>

      <PeopleLikeUs />

      <ReviewCta />

      <section className="section section--surface">
        <div className="container container--wide">
          <Reveal>
            <SectionHeading
              eyebrow="Themes"
              title="What comes up again and again"
              intro="Four things our clients mention most often in their own words."
              align="center"
            />
          </Reveal>
          <div className="grid grid--4">
            {[
              {
                icon: 'clock' as const,
                title: 'Punctual and dependable',
                text: '“Always on time and does more than advertised” — Ahmed N',
              },
              {
                icon: 'sparkles' as const,
                title: 'Genuine attention to detail',
                text: '“A great deal of attention is paid to the smallest of detail” — Dave',
              },
              {
                icon: 'calendar' as const,
                title: 'Flexible with schedules',
                text: '“Always make an effort to accommodate my schedule” — Amy',
              },
              {
                icon: 'lock' as const,
                title: 'Trusted with a key',
                text: '“I trust them with the key to my place and never have to worry” — Amy',
              },
            ].map((item, index) => (
              <Reveal key={item.title} delay={index * 50}>
                <article className="trust-card">
                  <span className="trust-card__icon">
                    <Icon name={item.icon} size={20} />
                  </span>
                  <h3 className="trust-card__title">{item.title}</h3>
                  <p className="trust-card__text">{item.text}</p>
                </article>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      <CtaBand
        heading="Join them on the schedule"
        text="Tell us about your condo and we will confirm your cleaning and availability — most requests are answered the same business day."
        secondaryLabel="Call the office"
        secondaryTo={`tel:${settings.phone.replace(/[^\d]/g, '')}`}
      />
    </>
  );
}
