/** Shared renderer for the two long-form legal pages. */

import { Icon } from '../components/Icon';
import { PageHero } from '../components/PageHero';
import { interpolateSections, type PageContent } from '../data/pages';
import { useSeo } from '../hooks/useSeo';
import { telHref } from '../lib/format';
import { breadcrumbSchema } from '../lib/schema';
import { useContent } from '../services/contentService';

export interface LegalPageProps {
  content: PageContent;
  path: string;
  crumbLabel: string;
}

export function LegalPage({ content, path, crumbLabel }: LegalPageProps) {
  const { settings } = useContent();

  const crumbs = [
    { name: 'Home', path: '/' },
    { name: crumbLabel, path },
  ];

  const sections = interpolateSections(content.sections, {
    guarantee: settings.guarantee_text,
    liability: settings.liability_coverage,
  });

  useSeo({
    title: content.metaTitle,
    description: content.metaDescription,
    path,
    schema: [breadcrumbSchema(crumbs)],
  });

  const updated = new Date().toLocaleDateString('en-CA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  return (
    <>
      <PageHero
        eyebrow={content.eyebrow}
        title={content.heading}
        intro={content.intro}
        crumbs={crumbs}
      />

      <section className="section">
        <div className="container container--narrow">
          <div className="legal-copy prose">
            <p className="prose__updated">Last updated: {updated}</p>

            {sections.map((section, index) => (
              <section key={section.heading ?? `section-${index}`}>
                {section.heading ? <h2>{section.heading}</h2> : null}
                {section.paragraphs?.map((paragraph) => (
                  <p key={paragraph.slice(0, 48)}>{paragraph}</p>
                ))}
                {section.bullets?.length ? (
                  <ul>
                    {section.bullets.map((bullet) => (
                      <li key={bullet}>{bullet}</li>
                    ))}
                  </ul>
                ) : null}
              </section>
            ))}

            <h2>Contact</h2>
            <address className="legal-copy__address" style={{ marginTop: '1rem' }}>
              <strong>{settings.site_legal_name}</strong>
              <span>{settings.address_street}</span>
              <span>
                {settings.address_city}, {settings.address_region} {settings.address_postal}
              </span>
              <span>{settings.address_country}</span>
              <a
                href={`mailto:${settings.email}`}
                data-ga-event="email_click"
                data-ga-label="Legal page"
              >
                {settings.email}
              </a>
              <a
                href={telHref(settings.phone)}
                data-ga-event="phone_click"
                data-ga-label="Legal page"
              >
                {settings.phone_display}
              </a>
            </address>

            <p className="prose__note">
              <Icon name="info" size={16} /> This page describes our own website and services. It is
              written in plain language so it is actually readable — if anything is unclear, call the
              office on {settings.phone_display} and we will explain it.
            </p>
          </div>
        </div>
      </section>
    </>
  );
}
