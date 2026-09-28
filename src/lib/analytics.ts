/**
 * Google Analytics 4.
 *
 * The measurement ID is configurable rather than hardcoded: set
 * `ga4_measurement_id` in `src/data/settings.ts`, or provide
 * `VITE_GA4_MEASUREMENT_ID`. When neither is present nothing is loaded — no
 * script tag, no cookies.
 */

declare global {
  interface Window {
    dataLayer?: unknown[];
    gtag?: (...args: unknown[]) => void;
  }
}

/** Every conversion event the site is allowed to send. */
export const ALLOWED_EVENTS = [
  'quote_form_start',
  'quote_form_step',
  'quote_form_submit',
  'contact_form_submit',
  'enquiry_send',
  'enquiry_copy',
  'phone_click',
  'email_click',
  'quote_cta_click',
  'service_view',
  'map_click',
  'service_card_click',
  'video_play',
  'review_click',
  'recognition_click',
] as const;

export type AnalyticsEvent = (typeof ALLOWED_EVENTS)[number];

const allowed = new Set<string>(ALLOWED_EVENTS);

let initialised = false;
let activeId: string | null = null;

const envId = import.meta.env.VITE_GA4_MEASUREMENT_ID?.trim() || '';

function isValidMeasurementId(value: string): boolean {
  return /^G-[A-Z0-9]{4,}$/i.test(value) || /^GT-[A-Z0-9]{4,}$/i.test(value);
}

/** Loads gtag.js once. Safe to call repeatedly and safe to never call. */
export function initAnalytics(measurementId: string | null | undefined): void {
  const id = (measurementId || envId || '').trim();
  if (!id || !isValidMeasurementId(id) || id === activeId) return;

  activeId = id;

  const script = document.createElement('script');
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`;
  document.head.appendChild(script);

  window.dataLayer = window.dataLayer || [];
  window.gtag = function gtag(...args: unknown[]) {
    window.dataLayer?.push(args);
  };
  window.gtag('js', new Date());
  // Analytics storage is denied until the visitor's browser signals otherwise,
  // with IP anonymisation on to keep the data aggregate.
  window.gtag('config', id, {
    anonymize_ip: true,
    send_page_view: true,
  });

  initialised = true;
}

export function isAnalyticsReady(): boolean {
  return initialised;
}

/** Sends a custom event. Unknown event names are ignored. */
export function trackEvent(
  name: AnalyticsEvent,
  params: Record<string, string | number | boolean | undefined> = {},
): void {
  if (!allowed.has(name)) return;
  if (typeof window.gtag !== 'function') return;
  const clean: Record<string, string | number | boolean> = {};
  for (const [key, value] of Object.entries(params)) {
    if (value !== undefined) clean[key] = value;
  }
  window.gtag('event', name, clean);
}

/** Sends a virtual page view on client-side route changes. */
export function trackPageView(path: string, title?: string): void {
  if (!activeId || typeof window.gtag !== 'function') return;
  window.gtag('event', 'page_view', {
    page_path: path,
    page_location: window.location.href,
    page_title: title ?? document.title,
  });
}

/** Wires `data-ga-event` attributes to GA4 wherever they appear. */
export function installDeclarativeTracking(): () => void {
  const handler = (event: MouseEvent) => {
    const target = (event.target as HTMLElement | null)?.closest?.('[data-ga-event]');
    if (!(target instanceof HTMLElement)) return;
    const name = target.dataset.gaEvent;
    if (!name || !allowed.has(name)) return;
    trackEvent(name as AnalyticsEvent, {
      label: target.dataset.gaLabel || undefined,
      destination: target.getAttribute('href') || undefined,
    });
  };

  document.addEventListener('click', handler, true);
  return () => document.removeEventListener('click', handler, true);
}
