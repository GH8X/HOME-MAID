/**
 * Site content — navigation, where we clean, the service video and the customer
 * notices.
 *
 * The About story lives in `src/data/pages.ts` alongside the legal pages. The
 * service area list and the customer notices here are all taken from the
 * published Maid4Condos site. Nothing is invented and no price appears
 * anywhere; where the business sets a policy amount it is described in words
 * rather than as a figure.
 */

export interface NavItem {
  label: string;
  to: string;
}

export const PRIMARY_NAV: readonly NavItem[] = [
  { label: 'Services', to: '/services' },
  { label: 'About', to: '/about' },
  { label: 'FAQ', to: '/faq' },
  { label: 'Testimonials', to: '/testimonials' },
  { label: 'Contact', to: '/contact' },
] as const;

export const SECONDARY_NAV: readonly NavItem[] = [
  { label: 'Privacy policy', to: '/privacy-policy' },
  { label: 'Terms & conditions', to: '/terms' },
  { label: 'Photography credits', to: '/image-credits' },
] as const;

/* -------------------------------------------------------------------------- */
/* Where we clean                                                             */
/* -------------------------------------------------------------------------- */

export interface Area {
  name: string;
  note: string;
}

/**
 * The neighbourhoods Maid4Condos lists, plus the GTA note. The business is
 * explicit that this is not an exhaustive list.
 */
export const SERVICE_AREAS: Area[] = [
  { name: 'Liberty Village', note: 'Our home base — the corporate office is on Atlantic Avenue.' },
  { name: 'Queen West', note: 'Condo corridors from Bathurst to Ossington and beyond.' },
  { name: 'King West', note: 'High-rise towers and lofts in the entertainment district.' },
  { name: 'Fort York', note: 'New builds and lakeside condominium communities.' },
  { name: 'City Place', note: 'Downtown high-rise condominium living.' },
  { name: 'Lakeshore Blvd', note: 'Waterfront condos from Bathurst to the Humber.' },
  { name: 'Distillery District', note: 'Loft conversions and historic brick buildings.' },
  { name: 'Yorkville', note: 'Upscale condominium and boutique residences.' },
  { name: 'Downtown Toronto', note: 'The core, and everything in between.' },
  { name: 'North York', note: 'Condominium communities along the Yonge corridor.' },
  { name: 'Greater Toronto Area', note: 'We currently service within the GTA and surrounding areas.' },
];

/* -------------------------------------------------------------------------- */
/* Service video                                                              */
/* -------------------------------------------------------------------------- */

/**
 * The Maid4Condos service video (Vimeo, published by the company). The
 * thumbnail is our own photography and the player is only loaded when the
 * visitor presses play, so the section costs nothing until it is wanted.
 */
export const SERVICE_VIDEO = {
  title: 'Maid4Condos Residential Cleaning Services',
  eyebrow: 'Watch the service',
  intro:
    'A short look at how a Maid4Condos cleaning comes together — master class trained cleaners, a comprehensive 30+ point checklist and professional grade, biodegradable products.',
  /** Vimeo video id, embedded as https://player.vimeo.com/video/<id>. */
  vimeoId: '248423580',
  /** The original video page, offered as a fallback and a credit. */
  pageUrl: 'https://vimeo.com/248423580',
  /** Photography manifest slug used for the poster frame. */
  poster: 'cleaning-floors',
  /** Where else Maid4Condos publishes cleaning videos. */
  channelUrl: 'https://www.youtube.com/channel/UCpGB_eu4AC7RBVTA4ZfvNw',
} as const;

/* -------------------------------------------------------------------------- */
/* Important customer information                                             */
/* -------------------------------------------------------------------------- */

export interface CustomerNotice {
  title: string;
  text: string;
}

/** What a client needs to know before, during and after a cleaning. */
export const CUSTOMER_NOTICES: CustomerNotice[] = [
  {
    title: 'Office and service hours',
    text: 'Our office is open 9am to 5pm Monday to Friday, and we service clients between 8am and 6pm. We do not currently provide evening or weekend bookings in residential homes.',
  },
  {
    title: 'Arrival windows',
    text: 'You choose either a 2 hour arrival window or a flexible window between 9am and 5pm. We provide an estimated arrival time to allow for traffic, lockouts and last-minute additions.',
  },
  {
    title: 'Getting us in',
    text: 'You can be home, leave a key at the concierge, use a smart keypad, or leave a lockbox and instructions. Key at concierge and lockbox access are the most efficient methods.',
  },
  {
    title: 'How to prepare',
    text: 'Please pick up loose items, garbage, debris and clothing from floors, table tops and countertops before we arrive, so our time is spent cleaning surfaces rather than tidying them.',
  },
  {
    title: 'What you need to provide',
    text: 'We bring Comet, Spic&Span, Magic Eraser, fresh cloths and sponges, a feather duster and a professional grade flat mop. Please provide a vacuum and a toilet brush — or add vacuum delivery, which is already included in the Move In / Move Out package.',
  },
  {
    title: 'Rescheduling and cancellations',
    text: 'Cancelled or rescheduled inside 48 hours of your cleaning, a cancellation fee applies. For same day cancellations, lockouts, or if we cannot gain access within 30 minutes of arrival, we reserve the right to charge the greater of a set fee or 50% of the total service fee.',
  },
  {
    title: 'Accurate sizing matters',
    text: 'Size relates directly to the time we allocate. If your home turns out to be larger than described, or below average condition, we may ask for extra time. You are never charged extra without your approval.',
  },
  {
    title: 'Health and safety limits',
    text: 'We do not move anything heavier than 30 lbs, climb more than 2 ft on a ladder, or clean during or after an infestation. Interior windows up to 9 ft are cleaned; exterior windows are not.',
  },
  {
    title: 'If something is not right',
    text: 'Tell the office within 24 hours and we will send a cleaner back within 3 days to address any area where we fell short. For accidental damage, notify the office within 48 hours.',
  },
  {
    title: 'Referrals, gift cards and tips',
    text: 'Share your referral code and your friends get credit on their first cleaning while you earn M4C credit for each one. Gift cards are available and do not expire. Cleaners can be tipped in cash on site, or by e-transfer to the office.',
  },
];
