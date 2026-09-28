/**
 * Content context.
 *
 * The whole site reads its content from the plain data modules in `src/data`.
 * There is no database and nothing to fetch, so the provider has no loading
 * state and no error path — it simply publishes the content that ships with the
 * build, which is also what the static host serves.
 *
 * `useContent()` remains the single access point for pages and components, so
 * moving a headline or a package between data files never touches the UI.
 */

import { createContext, useContext, useEffect, useMemo, type ReactNode } from 'react';

import { FAQS } from '../data/faqs';
import { EXTRAS, FREQUENCIES, SERVICES } from '../data/services';
import { SETTINGS } from '../data/settings';
import { SERVICE_AREAS, type Area } from '../data/siteContent';
import { TESTIMONIALS } from '../data/testimonials';
import { initAnalytics, installDeclarativeTracking, trackPageView } from '../lib/analytics';
import type { Extra, Faq, Frequency, Service, Settings, Testimonial } from '../lib/types';

export interface ContentBundle {
  services: Service[];
  faqs: Faq[];
  testimonials: Testimonial[];
  areas: Area[];
  extras: Extra[];
  frequencies: Frequency[];
  settings: Settings;
}

/** Everything the site has, in one object. Built once — the data is constant. */
export const CONTENT: ContentBundle = {
  services: SERVICES,
  faqs: FAQS,
  testimonials: TESTIMONIALS,
  areas: SERVICE_AREAS,
  extras: EXTRAS,
  frequencies: FREQUENCIES,
  settings: SETTINGS,
};

export interface ContentContextValue extends ContentBundle {
  /**
   * The same collections under the name the pages already use. Kept so a page
   * reads `live.services` / `live.faqs` regardless of where the data moved.
   */
  live: {
    services: Service[];
    faqs: Faq[];
    testimonials: Testimonial[];
    areas: Area[];
    extras: Extra[];
    frequencies: Frequency[];
  };
  serviceBySlug: (slug: string) => Service | undefined;
  faqCategories: string[];
}

const ContentContext = createContext<ContentContextValue | null>(null);

export function ContentProvider({ children }: { children: ReactNode }) {
  // Analytics is configured from the settings file so the measurement ID stays
  // in one place. A blank ID (or analytics switched off) loads no tags at all.
  useEffect(() => {
    if (SETTINGS.analytics_enabled === '0') return;
    initAnalytics(SETTINGS.ga4_measurement_id);
    return installDeclarativeTracking();
  }, []);

  const value = useMemo<ContentContextValue>(() => {
    const live = {
      services: CONTENT.services,
      faqs: CONTENT.faqs,
      testimonials: CONTENT.testimonials,
      areas: CONTENT.areas,
      extras: CONTENT.extras,
      frequencies: CONTENT.frequencies,
    };

    return {
      ...CONTENT,
      live,
      serviceBySlug: (slug: string) => live.services.find((service) => service.slug === slug),
      faqCategories: Array.from(new Set(live.faqs.map((faq) => faq.category))),
    };
  }, []);

  return <ContentContext.Provider value={value}>{children}</ContentContext.Provider>;
}

export function useContent(): ContentContextValue {
  const context = useContext(ContentContext);
  if (!context) {
    throw new Error('useContent must be used inside <ContentProvider>.');
  }
  return context;
}

/** Fire-and-forget GA4 page view for client-side navigations. */
export function useAnalyticsPageView(path: string, title?: string): void {
  useEffect(() => {
    trackPageView(path, title);
  }, [path, title]);
}
