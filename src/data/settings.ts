/**
 * Site settings — the business facts, in one place.
 *
 * Every value here is published on maid4condos.com. Nothing is invented: no
 * award, statistic, certification, guarantee or claim appears unless the
 * business publishes it. Contact details, opening hours, review counts, social
 * profiles and review destinations are taken from the live site.
 *
 * To change a phone number, a headline or an office address, edit it here — it
 * propagates to every page, the header, the footer and the structured data.
 */

import type { HowStep, Settings, TrustItem } from '../lib/types';

/* -------------------------------------------------------------------------- */
/* Homepage lists                                                             */
/* -------------------------------------------------------------------------- */

/** The four promises under the hero headline. */
export const HERO_BULLETS: string[] = [
  'Professional Service — background-checked employees',
  'Reliable Cleaning — a 24 hour satisfaction guarantee',
  'Flexible Scheduling — weekly through to one-time',
  'Serving Toronto & the GTA',
];

/** What every Maid4Condos cleaning comes with. */
export const TRUST_ITEMS: TrustItem[] = [
  {
    icon: 'badge',
    title: 'A 24 hour guarantee',
    text: 'Not delighted with the clean? Tell the office within 24 hours and we will return to make it right.',
  },
  {
    icon: 'shield',
    title: 'Bonded & insured',
    text: '5,000,000 in liability coverage, WSIB covered staff and a fully bonded team.',
  },
  {
    icon: 'team',
    title: 'True employees',
    text: 'Every cleaner is a Maid4Condos employee — never an independent contractor.',
  },
  {
    icon: 'spark',
    title: 'Master class trained',
    text: 'Background checked, cleaning master class trained and certified.',
  },
  {
    icon: 'calendar',
    title: 'Flexible scheduling',
    text: 'Weekly, bi-weekly, monthly or one-time — with up to 20% off recurring visits.',
  },
  {
    icon: 'leaf',
    title: 'Considered products',
    text: 'Professional grade, biodegradable products from trusted brands.',
  },
];

/** The five points behind "Why choose Maid4Condos". */
export const WHY_POINTS: string[] = [
  '100% satisfaction guarantee',
  'Attention to detail on every checklist item',
  'A personal touch, and well supervised staff',
  'Timely and flexible service',
  'Customer service that goes above and beyond',
];

/** The four steps from first click to a spotless home. */
export const HOW_STEPS: HowStep[] = [
  {
    title: 'Choose your package',
    text: 'Pick the clean that fits: Basic, Basic Plus, Deep, Deep Plus or Move In / Move Out.',
  },
  {
    title: 'Tell us about your space',
    text: 'Share your neighbourhood, bedrooms, bathrooms and approximate square footage.',
  },
  {
    title: 'Book your cleaning',
    text: 'We confirm the details and the arrival window that works for you.',
  },
  {
    title: 'Enjoy a clean home',
    text: 'Our team arrives, works through the checklist and leaves your space refreshed.',
  },
];

/* -------------------------------------------------------------------------- */
/* Settings                                                                   */
/* -------------------------------------------------------------------------- */

/**
 * Flat key → value map consumed by every component.
 *
 * The four list values are stored as JSON strings so the whole record keeps a
 * single shape; the typed constants above are the source of truth.
 */
export const SETTINGS: Settings = {
  /* Business ------------------------------------------------------------- */
  site_name: 'Maid4Condos',
  site_legal_name: 'Maid4Condos Inc.',
  site_tagline: 'Condo Cleaning Services You Can Count On',
  site_description:
    'Maid4Condos provides meticulous condo and home cleaning across Toronto and the GTA — basic, deep and move in / move out packages with flexible scheduling and a 24 hour guarantee.',

  /* Homepage ------------------------------------------------------------- */
  home_hero_eyebrow: 'Because time is precious',
  home_hero_title: 'Professional Cleaning Services You Can Count On',
  home_hero_subtitle:
    'Maid4Condos is a family run Toronto cleaning company built for condo living — meticulous attention to detail, transparent pricing and scheduling that flexes around your life.',
  home_hero_image: 'hero-cleaning-modern-home',
  home_hero_bullets: JSON.stringify(HERO_BULLETS),

  home_services_heading: 'Cleaning packages built around condo life',
  home_services_intro:
    'From a detailed everyday clean to a full reset before you move, every package is worked through a written checklist and priced for your home.',

  home_trust_heading: 'Why Toronto keeps us on speed dial',
  home_trust_intro: 'Because time is precious. Here is what every Maid4Condos cleaning comes with.',
  home_trust_items: JSON.stringify(TRUST_ITEMS),

  home_why_heading: 'Because we care',
  home_why_intro:
    'Brooms are great, though they rarely make you fall back in love with your home — but we can. We are not just strangers coming to clean your house; we are your partners-in-clean.',
  home_why_points: JSON.stringify(WHY_POINTS),

  home_how_heading: 'How it works',
  home_how_intro: 'Four simple steps from first click to a spotless home.',
  home_how_steps: JSON.stringify(HOW_STEPS),

  home_video_heading: 'See how a Maid4Condos cleaning actually works',
  home_video_intro:
    'Master class trained cleaners, a comprehensive 30+ point checklist and professional grade products — here is what we bring to every condo we look after.',

  home_areas_intro:
    'Maid4Condos provides cleaning services across the entire Toronto region. These are some of the neighbourhoods we service — we are not limited to them, so tell us your postal code and we will confirm.',

  home_testimonials_heading: 'Trusted in condos across Toronto',
  home_testimonials_intro:
    'Real reviews from Maid4Condos clients. Every quote below is published on maid4condos.com.',

  home_people_heading: 'People Like Us!',
  home_people_intro:
    'We are proud to be recognised by the platforms our clients use to find and rate cleaning services across Toronto.',

  home_review_heading: 'Sharing is caring. We love getting customer feedback!',
  home_review_intro:
    'Your opinion means everything to us — incorporating your feedback is part of our policy. You can leave a review on any of the platforms below.',

  home_final_heading: 'Ready for a cleaner space?',
  home_final_text:
    'Tell us about your condo and we will confirm your cleaning and availability. It takes about a minute.',

  /* SEO ----------------------------------------------------------------- */
  default_meta_title: 'Condo Cleaning Services Toronto | Maid4Condos',
  default_meta_description:
    'Maid4Condos offers professional condo cleaning services in Toronto. Meticulous attention to detail, transparent pricing and flexible scheduling. Book your cleaning today.',
  og_image: 'cleaned-living-room',
  robots_policy: 'index,follow',

  /* Contact ------------------------------------------------------------- */
  phone: '647-822-0601',
  phone_display: '(647) 822-0601',
  email: 'info@maid4condos.com',
  booking_email: 'bookings@maid4condos.com',
  address_street: '60 Atlantic Ave., Suite 200',
  address_city: 'Toronto',
  address_region: 'ON',
  address_postal: 'M6K 1X9',
  address_country: 'Canada',
  office_hours: 'Monday – Friday: 8:00 am – 6:00 pm\nSaturday & Sunday: 9:00 am – 4:00 pm',
  service_hours:
    'Residential cleanings are scheduled Monday – Friday between 8:00 am and 6:00 pm. We do not currently offer evening or weekend residential bookings.',
  map_query: '60 Atlantic Ave, Toronto, ON M6K 1X9',
  opening_hours_schema: 'Mo-Fr 08:00-18:00|Sa-Su 09:00-16:00',

  /* Social & reviews ---------------------------------------------------- */
  social_facebook: 'https://www.facebook.com/Maid4Condos/',
  social_instagram: 'https://www.instagram.com/maid4condos/',
  social_twitter: 'https://twitter.com/maid4condos',
  social_yelp: 'https://www.yelp.ca/biz/maid4condos-toronto-2',
  reviews_site_count: '250',
  reviews_google_count: '77',
  reviews_yelp_count: '31',

  /* Trust --------------------------------------------------------------- */
  guarantee_text:
    'If you are not 100% satisfied with the quality of cleaning of any of the serviced areas, and your home is as described and in average condition, and you advise the office within 24 hours of your service, Maid4Condos will send a cleaner back to address any areas in which we fell short within 3 days of your original scheduled cleaning.',
  liability_coverage: '5,000,000',

  /* Analytics ----------------------------------------------------------- */
  ga4_measurement_id: '',
  analytics_enabled: '1',

  /* Form messages ------------------------------------------------------- */
  quote_success_title: 'Thank you! Your request has been received.',
  quote_success_text:
    'A member of our team will contact you shortly to confirm the details and availability for your cleaning. If you need to reach us sooner, call (647) 822-0601 or email info@maid4condos.com.',
  contact_success_text: 'Thanks for getting in touch. Our team will reply to your message shortly.',
};
