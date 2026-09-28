/**
 * Structured data (schema.org JSON-LD).
 *
 * Only facts the business publishes are emitted. In particular there is no
 * `aggregateRating`: the company publishes review *counts*, not an average
 * score, so any rating object here would be invented. There is no `priceRange`
 * or `offers` either — the public site does not publish prices, and structured
 * data must never say more than the page does.
 */

import { image } from '../data/media';
import type { Area } from '../data/siteContent';
import { absoluteUrl } from './format';
import type { Faq, Service, Settings } from './types';

type Json = Record<string, unknown>;

const DAY_NAMES: Record<string, string> = {
  Mo: 'Monday',
  Tu: 'Tuesday',
  We: 'Wednesday',
  Th: 'Thursday',
  Fr: 'Friday',
  Sa: 'Saturday',
  Su: 'Sunday',
};

const DAY_ORDER = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

function expandDayRange(range: string): string[] {
  const [start, end] = range.split('-');
  const startIndex = DAY_ORDER.indexOf(start);
  if (startIndex === -1) return [];
  const endIndex = end ? DAY_ORDER.indexOf(end) : startIndex;
  const lastIndex = endIndex === -1 ? startIndex : endIndex;
  return Array.from({ length: lastIndex - startIndex + 1 }, (_, offset) =>
    DAY_NAMES[DAY_ORDER[startIndex + offset]],
  ).filter(Boolean);
}

/**
 * Parses the published `Mo-Fr 08:00-18:00|Sa-Su 09:00-16:00` notation into
 * schema.org opening hours.
 */
export function openingHoursSchema(value: string | undefined): Json[] {
  if (!value) return [];
  const entries: Json[] = [];

  for (const raw of value.split('|')) {
    const entry = raw.trim();
    if (!entry) continue;

    const [dayPart, timePart] = entry.split(/\s+/);
    const [opens, closes] = (timePart ?? '').split('-');
    const dayOfWeek = expandDayRange(dayPart ?? '');
    if (!dayOfWeek.length || !opens || !closes) continue;

    entries.push({
      '@type': 'OpeningHoursSpecification',
      dayOfWeek,
      opens,
      closes,
    });
  }

  return entries;
}

export function businessId(): string {
  return `${absoluteUrl('/')}#business`;
}

export function localBusinessSchema(
  settings: Settings,
  services: Service[],
  areas: Area[],
): Json {
  const social = [
    settings.social_facebook,
    settings.social_instagram,
    settings.social_twitter,
    settings.social_yelp,
  ]
    .filter(Boolean)
    .map((url) => url as string);

  const schema: Json = {
    '@context': 'https://schema.org',
    '@type': ['LocalBusiness', 'CleaningService', 'HomeAndConstructionBusiness'],
    '@id': businessId(),
    name: settings.site_name,
    legalName: settings.site_legal_name,
    description: settings.site_description,
    url: absoluteUrl('/'),
    telephone: settings.phone,
    email: settings.email,
    image: absoluteUrl(image(settings.og_image).src),
    logo: absoluteUrl('/images/logo.svg'),
    address: {
      '@type': 'PostalAddress',
      streetAddress: settings.address_street,
      addressLocality: settings.address_city,
      addressRegion: settings.address_region,
      postalCode: settings.address_postal,
      addressCountry: 'CA',
    },
    areaServed: areas.map((area) => ({ '@type': 'City', name: area.name })),
    openingHoursSpecification: openingHoursSchema(settings.opening_hours_schema),
    hasOfferCatalog: {
      '@type': 'OfferCatalog',
      name: 'Condo cleaning packages',
      itemListElement: services.map((service) => ({
        '@type': 'Offer',
        url: absoluteUrl(`/services/${service.slug}`),
        itemOffered: {
          '@type': 'Service',
          name: service.name,
          description: service.summary,
        },
      })),
    },
  };

  if (social.length) schema.sameAs = social;

  return schema;
}

export function serviceSchema(service: Service, settings: Settings): Json {
  const schema: Json = {
    '@context': 'https://schema.org',
    '@type': 'Service',
    name: service.name,
    serviceType: `${service.name} — condo and residential cleaning in Toronto`,
    description: service.meta_description || service.summary,
    url: absoluteUrl(`/services/${service.slug}`),
    image: absoluteUrl(image(service.hero_image).src),
    provider: { '@id': businessId() },
    areaServed: {
      '@type': 'City',
      name: settings.address_city || 'Toronto',
    },
  };

  return schema;
}

export function faqSchema(items: Array<Pick<Faq, 'question' | 'answer'>>): Json {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: items.map((item) => ({
      '@type': 'Question',
      name: item.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: item.answer,
      },
    })),
  };
}

export interface Crumb {
  name: string;
  path: string;
}

export function breadcrumbSchema(crumbs: Crumb[]): Json {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: crumbs.map((crumb, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: crumb.name,
      item: absoluteUrl(crumb.path),
    })),
  };
}
