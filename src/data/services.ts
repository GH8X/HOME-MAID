/**
 * Cleaning packages, add-ons and AutoPilot frequencies.
 *
 * Every package name, description, checklist and FAQ answer below is taken from
 * the published Maid4Condos content — nothing is invented. No prices appear
 * anywhere: the public site invites the visitor to book, and pricing is
 * confirmed with the office against the size and condition of the home.
 *
 * Edit a package here and the services index, its detail page, the homepage
 * cards, the navigation and the quote form all follow.
 */

import type { Extra, Frequency, Service } from '../lib/types';

/* -------------------------------------------------------------------------- */
/* Checklists                                                                 */
/* -------------------------------------------------------------------------- */

const BASIC_CHECKLIST = {
  Bathrooms: [
    'Bathtub and shower enclosure',
    'Inside, out and behind toilet',
    'Lights, mirrors, sink, countertops',
    'Vacuum ceiling fan cover',
    'Light switches, door knobs',
    'Empty garbage bin',
    'Vacuum / mop floors',
  ],
  Bedrooms: [
    'Make beds',
    'Tidy any clothing',
    'Dust counters, table tops, picture frames',
    'Clean mirrors and polish ornaments',
    'Wipe switches, door knobs',
    'Vacuum / mop floors',
  ],
  Kitchen: [
    'If dishwasher is empty, start a new load',
    'If dishwasher is full, put away clean dishes',
    'Microwave inside',
    'Stove top',
    'Fridge front polished',
    'Backsplash, counter tops, cupboard fronts',
    'Sink',
    'Wipe light switches & door knobs',
    'Replace garbage and recycling liners',
    'Vacuum / mop floors',
  ],
  'Living & dining room': [
    'General tidying',
    'Tidy sofa seating',
    'Table tops, picture frames, window ledges',
    'Clean mirrors and polish ornaments',
    'Wipe telephones, switches, door knobs',
    'Vacuum / mop floors',
  ],
  'Laundry room': ['Clear air vent', 'Clear lint trap'],
};

const DEEP_CHECKLIST = {
  Bathrooms: [...BASIC_CHECKLIST.Bathrooms, 'Wipe baseboards'],
  Bedrooms: [...BASIC_CHECKLIST.Bedrooms, 'Wipe baseboards', 'Wipe window ledges'],
  Kitchen: [...BASIC_CHECKLIST.Kitchen, 'Wipe baseboards', 'Wipe window ledges'],
  'Living & dining room': [
    'General tidying',
    'Tidy sofa seating',
    'Table tops, picture frames, window ledges',
    'Clean mirrors and dust ornaments',
    'Wipe telephones, switches, door knobs',
    'Vacuum / mop floors',
    'Wipe baseboards',
    'Clean under and behind furniture',
    'Vacuum sofa seating',
  ],
  'Laundry room': ['Clear air vent', 'Clear lint trap', 'Wipe washer and dryer', 'Clean floors'],
};

const MOVE_CHECKLIST = {
  Bathrooms: [...DEEP_CHECKLIST.Bathrooms, 'Wipe vanity inside and out'],
  Bedrooms: [
    'Dust counters, table tops, picture frames',
    'Clean mirrors and polish ornaments',
    'Wipe switches, door knobs',
    'Vacuum / mop floors',
    'Wipe baseboards',
    'Wipe window ledges',
  ],
  Kitchen: [
    'Inside fridge and oven',
    'Stove top',
    'Fridge front polished',
    'Microwave inside',
    'Backsplash, counter tops, cupboard fronts',
    'Sink',
    'Wipe light switches & door knobs',
    'Vacuum / mop floors',
    'Wipe baseboards',
    'Wipe window ledges',
    'Wipe inside cabinets',
    'Wipe inside drawers',
  ],
  'Living & dining room': [
    'Clean mirrors and polish ornaments',
    'Wipe switches, door knobs',
    'Vacuum / mop floors',
    'Wipe baseboards',
    'Wipe window ledges',
  ],
  'Laundry room': ['Clear air vent', 'Clear lint trap', 'Wipe washer and dryer', 'Clean floors'],
};

/* -------------------------------------------------------------------------- */
/* Packages                                                                   */
/* -------------------------------------------------------------------------- */

export const SERVICES: Service[] = [
  {
    slug: 'basic-cleaning',
    name: 'Basic Clean',
    eyebrow: 'Everyday upkeep',
    tagline: 'A 30+ point checklist that is anything but basic',
    summary:
      'Our foundation package: a detailed 30+ point clean that we check twice, built for condos that are already in average condition.',
    image: 'cleaning-kitchen-cabinets',
    hero_image: 'cleaning-kitchen-cabinets',
    duration_note: 'Most condos are complete in under 4 hours.',
    best_paired: 'Weekly or bi-weekly AutoPilot schedule',
    meta_title: 'Basic Cleaning Package Toronto | Maid4Condos',
    meta_description:
      'A detailed 30+ point basic cleaning checklist for Toronto condos — checked twice so the job is always done right. Book your cleaning with Maid4Condos.',
    intro: [
      'You are looking for a Basic Cleaning Package that is anything but basic. With a detailed 30+ point checklist for a simple yet thorough clean — and a second check just to be sure we have done the job right — this package is perfect if you crave a tidy home but cannot find the time to do it yourself.',
      'We recommend pairing the Basic Clean with a weekly or bi-weekly AutoPilot schedule so your home is effectively maintained. AutoPilot provides up to 20% off per visit.',
    ],
    who_for: [
      'Condos and apartments in average condition',
      'Busy professionals who want their home reset regularly',
      'Renters and owners who want a dependable recurring clean',
      'Anyone maintaining a home that has been cleaned within the last few weeks',
    ],
    checklist: BASIC_CHECKLIST,
    benefits: [
      'Everything on the 30+ point checklist, verified twice',
      'The most economical way to keep a condo consistently clean',
      'Up to 20% off per visit on an AutoPilot schedule',
      'All standard supplies and equipment included',
    ],
    faqs: [
      {
        question: 'What condition does my condo need to be in?',
        answer:
          'A basic cleaning is designed for homes in average condition. If it has been a while, or if there has been a lot of activity, a Basic Plus or Deep Clean gives us the extra time the space needs.',
      },
      {
        question: 'Can I add the fridge, oven or windows?',
        answer:
          'Yes. Inside Oven, Inside Fridge, Inside Windows, a laundry load, vacuum delivery and GermBlasters disinfection can each be added to a basic cleaning.',
      },
      {
        question: 'How often should I book?',
        answer:
          'Most clients choose weekly or bi-weekly. Bi-weekly is our recommended maintenance schedule and comes with a 15% discount per visit.',
      },
    ],
  },
  {
    slug: 'basic-plus',
    name: 'Basic Plus',
    eyebrow: 'A little extra love',
    tagline: 'The same checklist with 10–15% more time on site',
    summary:
      'Identical to the Basic Clean checklist, with 10–15% more time allocated for homes that need extra attention.',
    image: 'cleaning-vacuum-kitchen',
    hero_image: 'cleaning-vacuum-kitchen',
    duration_note: '10–15% more time than a Basic Clean.',
    best_paired: 'Weekly or bi-weekly AutoPilot schedule',
    meta_title: 'Basic Plus Cleaning Package Toronto | Maid4Condos',
    meta_description:
      'Go beyond our basic cleaning package. Basic Plus gives 10–15% more time so your Toronto condo gets the attention it deserves. Book your cleaning.',
    intro: [
      'Does your home feel like it has lost its sparkle? If you are recovering from hosting house guests, or simply have not cleaned in a while, or you have pets or kids — the Basic Plus Cleaning Package is for you.',
      'This package includes the same checklist items as the Basic Clean, but provides an additional 10–15% more time on site when a little extra love and care is needed.',
    ],
    who_for: [
      'Homes with pets or children',
      'Condos recovering after hosting guests or a busy stretch',
      'Anyone who wants the full checklist done without rushing',
      'Clients stepping back into a regular cleaning routine',
    ],
    checklist: BASIC_CHECKLIST,
    benefits: [
      'The complete Basic checklist, minus the time pressure',
      'Ideal when pets, kids or guests have left their mark',
      'Same up to 20% AutoPilot discount when booked on a schedule',
      'Supplies and equipment included',
    ],
    faqs: [
      {
        question: 'How is this different from the Basic Clean?',
        answer:
          'The checklist is identical. Basic Plus simply allocates 10–15% more time to the visit so we can work through everything without rushing.',
      },
      {
        question: 'Should I choose Basic Plus or a Deep Clean?',
        answer:
          'Choose Basic Plus if the home is generally maintained and just needs more time. Choose a Deep Clean if baseboards, under furniture, window ledges and build-up need addressing.',
      },
      {
        question: 'Can I combine it with extras?',
        answer:
          'Absolutely — oven, fridge, interior windows, laundry, vacuum delivery and GermBlasters disinfection can all be added.',
      },
    ],
  },
  {
    slug: 'deep-cleaning',
    name: 'Deep Clean',
    eyebrow: 'Reset your space',
    tagline: 'Every inch of your condo, spotless',
    summary:
      'A thorough top-to-bottom clean that clears built-up dirt, dust and grime — baseboards, under furniture and all the places a routine clean misses.',
    image: 'cleaning-floors',
    hero_image: 'cleaning-floors',
    duration_note: 'Booked with extra time for detail work.',
    best_paired: 'A Deep Clean first, then AutoPilot maintenance',
    meta_title: 'Residential Deep Cleaning Services Toronto | Maid4Condos',
    meta_description:
      'Our deep cleaning package makes your Toronto condo look brand new — baseboards, under furniture and built-up grime addressed. Book with Maid4Condos.',
    intro: [
      'A condo deep cleaning by Maid4Condos is what you need if your regular cleanings have fallen behind. Our deep cleaning service is perfect for those who want to feel like they are walking into their home for the first time.',
      'With our custom deep cleaning service we make sure every inch of your condo or apartment is spotless — removing dirt, dust, stains and grime. After the initial deep clean, scheduled visits are advised to keep it that way, or you can let Maid4Condos maintain it on a weekly, bi-weekly or monthly basis.',
      'A deep clean should be done at least once a year to keep you and your family healthy.',
    ],
    who_for: [
      'Homes where regular cleaning has fallen behind',
      'Spring cleaning, or preparing a property to list for sale',
      'Before hosting family for the holidays or a get-together',
      'First-time clients who want a true reset before a maintenance schedule',
    ],
    checklist: DEEP_CHECKLIST,
    benefits: [
      'A healthy home — built-up allergens, grime and dust removed',
      'More free time in the weeks and months afterwards',
      'Improves how a property shows to potential buyers',
      'A clean, calm home to walk into after a long day',
    ],
    faqs: [
      {
        question: 'How long does a deep clean take?',
        answer:
          'Deep cleans are allocated significantly more time than a basic clean. The exact duration depends on the size, the number of bathrooms and the condition of the property.',
      },
      {
        question: 'Should the condo be empty?',
        answer:
          'No. A deep clean is for an occupied home. If the property will be completely empty or vacant, the Move In / Move Out Clean is the right package.',
      },
      {
        question: 'How often should I deep clean?',
        answer:
          'At least once a year. Many clients start with a deep clean and then maintain the result with a recurring basic or basic plus schedule.',
      },
    ],
  },
  {
    slug: 'deep-plus',
    name: 'Deep Plus',
    eyebrow: 'Extra elbow grease',
    tagline: 'The deep clean checklist with more time to work',
    summary:
      'The full deep cleaning checklist with 10–15% more time for homes that need serious attention or have not been deep cleaned in a long while.',
    image: 'cleaning-vacuum-rug',
    hero_image: 'cleaning-vacuum-rug',
    duration_note: '10–15% more time than a Deep Clean.',
    best_paired: 'AutoPilot maintenance after the reset',
    meta_title: 'Deep Plus Cleaning Package Toronto | Maid4Condos',
    meta_description:
      'Deep Plus gives the full deep cleaning checklist 10–15% more time for homes that need extra attention. Book Toronto condo cleaning with Maid4Condos.',
    intro: [
      'Does your home feel like it needs a little extra elbow grease? If you have just hosted guests, or you simply have not deep cleaned in a while, or you have pets or kids — the Deep Plus Cleaning Package may be for you.',
      'This package includes the same checklist items as the Deep Clean, but provides 10–15% more time on site when a little extra love and care is needed.',
    ],
    who_for: [
      'Condos that have gone a long time between deep cleans',
      'Larger units or homes with multiple bathrooms',
      'Households with pets or children',
      'Clients who want the deep clean result without time pressure',
    ],
    checklist: DEEP_CHECKLIST,
    benefits: [
      'The full deep clean checklist without the rush',
      'Built for larger units and busier households',
      'Removes build-up that a basic clean simply cannot reach',
      'Puts you in the best position to maintain with AutoPilot',
    ],
    faqs: [
      {
        question: 'Deep Clean or Deep Plus?',
        answer:
          'The checklist is the same. Deep Plus adds 10–15% more time, which is the right call when it has been a long while, or the home is larger than average.',
      },
      {
        question: 'Can you clean behind heavy furniture?',
        answer:
          'For the health and safety of our cleaners we do not move anything heavier than 30 lbs. If you need an area behind heavy furniture cleaned, please move the item before we arrive.',
      },
      {
        question: 'Do you clean inside appliances?',
        answer:
          'Inside Oven and Inside Fridge can be added to any deep clean — and both are included by default in the Move In / Move Out package.',
      },
    ],
  },
  {
    slug: 'move-in-move-out',
    name: 'Move In / Move Out',
    eyebrow: 'Showroom ready',
    tagline: 'A complete reset for empty condos',
    summary:
      'Designed for vacant units: inside and outside of everything, including appliances, cabinets, drawers and air vents. Supplies and equipment included.',
    image: 'moving-boxes',
    hero_image: 'moving-boxes',
    duration_note: 'Supplies, equipment and the vacuum are included.',
    best_paired: 'One-time, before or after your move',
    meta_title: 'Move In / Move Out Cleaning Service Toronto | Maid4Condos',
    meta_description:
      'Toronto move in and move out cleaning for empty condos — appliances, cabinets, drawers and vents cleaned inside and out. Book with Maid4Condos.',
    intro: [
      'Moving out of your condo, or into a new one? Leave behind a clean, well cared for home — or start life fresh in your new place with a custom condo cleaning by Maid4Condos. We specialise in move in and move out condo cleaning.',
      'One of the most challenging things about moving is knowing there is a tonne of cleaning to be done. Your landlord may also require a deep clean of the unit before you leave — why not let the professionals take care of it?',
      'This service is designed for empty spaces and to get them back into like-new shape. We clean inside and outside of everything, including appliances, cabinets, drawers and air vents. If you need to get your space showroom ready, this is the service for you.',
      'A move in / move out cleaning is also ideal for real estate agents showing units, tenants who want their deposit back, and landlords preparing a unit for a new tenant.',
    ],
    who_for: [
      'Tenants moving out who want their unit left in excellent condition',
      'Buyers and renters moving into a freshly cleaned space',
      'Landlords and property managers turning a unit over',
      'Real estate agents preparing a condo to show',
    ],
    checklist: MOVE_CHECKLIST,
    benefits: [
      'Cabinets, drawers, appliances and vents cleaned inside and out',
      'All supplies, equipment and the vacuum are included',
      'Helps protect your deposit and satisfy lease requirements',
      'Gets a unit showroom ready for photos, showings or a new tenant',
    ],
    faqs: [
      {
        question: 'Does the unit need to be empty?',
        answer:
          'Yes — this package is designed for vacant spaces so we can reach inside cabinets, drawers and appliances without obstruction. For an occupied home, choose a Deep Clean or Deep Plus.',
      },
      {
        question: 'Is the vacuum included?',
        answer:
          'Yes. All supplies, equipment and cleaning products are included for moving cleanings.',
      },
      {
        question: 'Do you remove construction debris or clean a renovation site?',
        answer:
          'No. We are a finishing crew: we do the final detailed clean before a property is returned or delivered. We do not work in construction zones or remove construction debris.',
      },
    ],
  },
  {
    slug: 'recurring-cleaning',
    name: 'Recurring Cleaning (AutoPilot)',
    eyebrow: 'Set it and relax',
    tagline: 'Weekly, bi-weekly or monthly — save up to 20%',
    summary:
      'Our AutoPilot schedule keeps your condo consistently clean. Choose weekly, bi-weekly or monthly, save up to 20% per visit, and change or skip dates with enough notice.',
    image: 'cleaned-living-room',
    hero_image: 'cleaned-living-room',
    duration_note: 'No contracts required for residential AutoPilot clients.',
    best_paired: 'Starts with a Basic, Basic Plus or Deep Clean',
    meta_title: 'Recurring Cleaning Schedule Toronto | Maid4Condos AutoPilot',
    meta_description:
      'Weekly, bi-weekly or monthly condo cleaning in Toronto with up to 20% off per visit. Join AutoPilot by Maid4Condos and never think about cleaning again.',
    intro: [
      'The more often we service your home, the less it costs you per visit. AutoPilot is our recurring cleaning schedule: choose weekly, bi-weekly or monthly and we take care of the rest.',
      'With an AutoPilot schedule, Maid4Condos brings the cleaning supplies you need so you do not have to buy them (unless you require specific products due to allergies or environmental preferences).',
      'Weekly and bi-weekly visits are not only more cost effective, they are also healthier. A routine clean reduces allergens, bacteria and other health concerns for your family.',
    ],
    who_for: [
      'Busy households and professionals with full schedules',
      'Families, or anyone who entertains often',
      'Elderly or disabled clients who need a regular helping hand',
      'Condo owners and renters who want a permanently tidy home',
    ],
    checklist: {
      Weekly: [
        'Ideal for busy lifestyles and lots of social obligations',
        '20% off every visit — the most economical service',
        'A consistently maintained, allergen-light home',
      ],
      'Bi-weekly (recommended)': [
        'Our most popular schedule and recommended maintenance interval',
        '15% off every visit',
        'Great balance of upkeep and value for most homes',
      ],
      Monthly: [
        'Typically paired with a deep cleaning package for a thorough service',
        '5% off every visit',
        'Suits homes that do not entertain often',
      ],
    },
    benefits: [
      'Up to 20% off every visit depending on frequency',
      'No contract required for residential AutoPilot clients',
      'Reschedule or skip a visit with enough notice, no problem',
      'Email reminder 5 days before and SMS reminder 3 days before each visit',
      'If your date lands on a holiday we move it to the next available date and let you know',
    ],
    faqs: [
      {
        question: 'Do I have to sign a contract?',
        answer:
          'No. AutoPilot clients do not commit to a contract, but by using our services you do agree to our Cleaning Service Agreement terms. Corporate clients have separate service agreements.',
      },
      {
        question: 'Can I change a future date?',
        answer:
          'Yes. Let us know as far in advance as possible and we will reschedule, skip or change a future date — as long as you are outside the cancellation policy there is no issue.',
      },
      {
        question: 'What happens if my cleaning falls on a holiday?',
        answer:
          'We move your cleaning to the next available date and communicate the change to you. By default we move it to the following day unless that day is already booked.',
      },
      {
        question: 'How do frequency discounts work?',
        answer:
          'Weekly visits are discounted 20%, bi-weekly 15% and monthly 5%. Customisable rates are available for daily service or high-volume requirements.',
      },
    ],
  },
];

/* -------------------------------------------------------------------------- */
/* Add-ons                                                                    */
/* -------------------------------------------------------------------------- */

export const EXTRAS: Extra[] = [
  {
    slug: 'germblasters',
    name: 'GermBlasters Disinfection',
    summary:
      'Hospital-grade, Health Canada and EPA approved disinfection that is non-toxic, fragrance free and safe around children and pets.',
    details:
      'A molecular-level disinfection service for homes, offices and commercial spaces. The solution has a 0-0-0 SDS rating, is NSF certified (no rinse required) for food contact surfaces, and is effective against a broad spectrum of viruses and bacteria.',
  },
  {
    slug: 'inside-oven',
    name: 'Inside Oven',
    summary:
      'A full interior oven clean, available with Basic, Basic Plus and Deep cleanings — and included by default in the Move In / Move Out package.',
    details:
      'If your oven has a self-cleaning cycle, please start it at least 24 hours before your service for the best results. Our staff arrive equipped with Easy-Off.',
  },
  {
    slug: 'inside-fridge',
    name: 'Inside Fridge',
    summary:
      'All shelving, drawers and nooks cleaned inside your refrigerator. Included by default in the Move In / Move Out package.',
    details:
      'Please purge anything you no longer want or need before we arrive. We are not able to effectively clean freezers and instead give them a quick wipe.',
  },
  {
    slug: 'inside-windows',
    name: 'Inside Windows',
    summary:
      'Interior window cleaning that brings more light into your home, for Basic, Basic Plus, Deep and Move In / Move Out cleanings.',
    details:
      'Please remove all screens before we arrive to avoid fragile clips being broken. We clean interior windows up to 9 ft tall; exterior windows are not included.',
  },
  {
    slug: 'vacuum-delivery',
    name: 'Vacuum Delivery',
    summary:
      'If you do not have a vacuum, we will bring one so floors, surfaces, baseboards, ceiling vents and under-furniture areas can be properly cleaned.',
    details: 'The Move In / Move Out package already includes the vacuum by default.',
  },
  {
    slug: 'laundry-load',
    name: 'Laundry Load',
    summary:
      'One wash and fold load, most often used for bedding linens. Additional loads can be arranged — just let us know.',
    details:
      'If you book this extra, please contact our office to tell us exactly what you would like laundered so we can plan the visit accordingly.',
  },
];

/* -------------------------------------------------------------------------- */
/* AutoPilot frequencies                                                      */
/* -------------------------------------------------------------------------- */

export const FREQUENCIES: Frequency[] = [
  {
    slug: 'weekly',
    label: 'Weekly',
    discount: '20% off',
    note: 'Most economical — ideal for busy lifestyles.',
    recommended: false,
  },
  {
    slug: 'bi-weekly',
    label: 'Bi-weekly',
    discount: '15% off',
    note: 'Our most popular schedule and recommended interval.',
    recommended: true,
  },
  {
    slug: 'monthly',
    label: 'Monthly',
    discount: '5% off',
    note: 'Suits homes that do not entertain often.',
    recommended: false,
  },
  {
    slug: 'one-time',
    label: 'One time',
    discount: 'No discount',
    note: 'A single visit with no ongoing commitment.',
    recommended: false,
  },
];
