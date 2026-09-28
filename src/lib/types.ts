/**
 * Shared content model.
 *
 * The site is statically built: every page reads the plain data modules in
 * `src/data`. There is no database, no admin and no fetched rows, so the shapes
 * here describe authored content only — no ids, no publish flags, no sort
 * orders. Order in the data file is the order on the page.
 *
 * Note the absence of a price field: the public site never displays one.
 */

export interface ServiceFaq {
  question: string;
  answer: string;
}

/** An ordered grouping such as "Bathrooms" followed by its checklist items. */
export type Checklist = Record<string, string[]>;

export interface Service {
  /** URL segment — `/services/<slug>`. */
  slug: string;
  name: string;
  eyebrow: string;
  tagline: string;
  summary: string;
  /** Image slug resolved through `src/data/media.ts`. */
  image: string;
  hero_image: string;
  duration_note: string;
  best_paired: string;
  meta_title: string;
  meta_description: string;
  intro: string[];
  who_for: string[];
  checklist: Checklist;
  benefits: string[];
  faqs: ServiceFaq[];
}

export interface Faq {
  category: string;
  question: string;
  answer: string;
}

export interface Testimonial {
  name: string;
  location: string | null;
  service: string | null;
  quote: string;
  /**
   * Only set when the source publishes a rating for that review. Every review
   * Maid4Condos publishes carries a name and a quote but no score, so this is
   * absent throughout — the card simply renders no stars.
   */
  rating?: number | null;
}

export interface Extra {
  slug: string;
  name: string;
  summary: string;
  details: string | null;
}

export interface Frequency {
  slug: string;
  label: string;
  discount: string;
  note: string;
  recommended: boolean;
}

/** Flattened key → value settings map, as consumed by the site. */
export type Settings = Record<string, string>;

/** A homepage trust indicator, stored as JSON inside a setting. */
export interface TrustItem {
  icon: IconName;
  title: string;
  text: string;
}

export interface HowStep {
  title: string;
  text: string;
}

export type IconName =
  | 'badge'
  | 'shield'
  | 'shieldCheck'
  | 'team'
  | 'spark'
  | 'sparkles'
  | 'calendar'
  | 'leaf'
  | 'phone'
  | 'mail'
  | 'pin'
  | 'clock'
  | 'check'
  | 'checkCircle'
  | 'arrow'
  | 'arrowUp'
  | 'menu'
  | 'close'
  | 'quote'
  | 'star'
  | 'chevron'
  | 'plus'
  | 'minus'
  | 'trash'
  | 'edit'
  | 'eye'
  | 'eyeOff'
  | 'upload'
  | 'logout'
  | 'dashboard'
  | 'services'
  | 'faq'
  | 'testimonial'
  | 'settings'
  | 'image'
  | 'inbox'
  | 'external'
  | 'search'
  | 'alert'
  | 'info'
  | 'home'
  | 'refresh'
  | 'lock'
  | 'user'
  | 'layers'
  | 'tag'
  | 'map'
  | 'building'
  | 'clipboard'
  | 'play';

/** A block of long-form copy (about, privacy, terms, service intros). */
export interface ContentSection {
  heading?: string;
  paragraphs?: string[];
  bullets?: string[];
  ordered?: boolean;
}

/* -------------------------------------------------------------------------- */
/* Enquiries                                                                  */
/* -------------------------------------------------------------------------- */

export interface QuoteRequestInput {
  full_name: string;
  email: string;
  phone: string;

  property_type: string;
  bedrooms: string;
  bathrooms: string;
  square_footage: string;

  service_slug: string;
  service_name: string;
  frequency: string;

  extras: string[];

  address: string;
  neighbourhood: string;
  postal_code: string;
  preferred_date: string;
  arrival_window: string;
  access_method: string;

  notes: string;
  referral_source: string;
}

export interface ContactMessageInput {
  full_name: string;
  email: string;
  phone: string;
  topic: string;
  message: string;
}

export interface SubmitResult {
  ok: boolean;
  message?: string;
}

/**
 * A submission the visitor can carry away with them.
 *
 * The site is static, so a booking is handed to the visitor as a written
 * summary they can send on, rather than silently stored in a browser.
 */
export interface PreparedEnquiry {
  /** Plain-text summary suitable for an email body. */
  body: string;
  /** `mailto:` link carrying the summary to the bookings inbox. */
  mailto: string;
  /** One-line human summary of what was requested. */
  summary: string;
}
