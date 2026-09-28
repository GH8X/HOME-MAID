/**
 * Long-form page copy for About, Privacy and Terms.
 *
 * The legal text is the company's own published wording. Tokens in `{{braces}}`
 * are replaced at render time with live values from `site_settings`, so the
 * guarantee or liability figure is updated in one place.
 */

import type { ContentSection } from '../lib/types';

export interface PageContent {
  metaTitle: string;
  metaDescription: string;
  eyebrow: string;
  heading: string;
  intro: string;
  sections: ContentSection[];
}

/* -------------------------------------------------------------------------- */
/* About                                                                      */
/* -------------------------------------------------------------------------- */

export const ABOUT = {
  metaTitle: 'About Maid4Condos | Family Run Toronto Cleaning Company Since 2014',
  metaDescription:
    'Maid4Condos is a family run Toronto condo cleaning company, in business since 2014. Bonded, WSIB covered, background-checked employees and a 24 hour guarantee.',
  eyebrow: 'About us',
  heading: 'Don’t want strangers in your home? Neither would we.',
  intro: 'Here is a bit about who is coming through your door.',
  story: [
    'We tried the corporate life — it was too impersonal. We wanted to support ourselves by doing something that makes a difference in people’s lives. It is no secret that the condo market has been on the rise, and as condo dwellers ourselves we were not satisfied with the cleaning services out there. So we decided to start our own.',
    'Having started from the bottom, we can see the impact we have made on the cleaning industry, and so can our clients. From large property management firms to home owners and unit managers, we attract a variety of customers. In addition to top quality cleaning services, affordable prices and years of service industry experience, our clients trust us because we take their needs into account — truly becoming their partners-in-clean.',
    'Maid4Condos has been in business since 2014. We are a family run business who truly cares about each and every client we have the opportunity to service.',
  ],
  promiseHeading: 'Our promise to you',
  promise: [
    '100% satisfaction guarantee',
    'Attention to detail',
    'Personal touch',
    'Timely & flexible service',
    'Well supervised, master class trained & certified staff',
    'Worry-free & convenient experience',
    'Customer service that goes above & beyond',
    'Fully insured with 5,000,000 in liability',
    'All staff are bonded',
    'All staff covered by WSIB',
    'All staff background checked & cleared',
  ],
  membershipsHeading: 'Credentials and associations',
  memberships: [
    'Maid4Condos is a member of ISSA and ARCSI — the North American associations for commercial and residential cleaning — so we stay current with the latest technology and industry trends.',
    'All Maid4Condos staff are employees, not independent contractors. They are background checked, master class trained and certified, covered by WSIB, bonded and covered by 5,000,000 in liability insurance.',
  ],
  notIncludedHeading: 'What we do not do',
  notIncludedIntro:
    'To keep our team safe and to set clear expectations, the following sit outside our service:',
  notIncluded: [
    'Inside or hard-to-reach light fixtures, exterior windows, blinds, drapes or shades',
    'Inside dishwashers, washing machines or hood fans',
    'Cleaning during or after an infestation',
    'Moving anything heavier than 30 lbs',
    'Climbing higher than 2 ft on a ladder or step stool',
    'Exterior or outdoor cleaning, balconies included',
    'Pet or human waste and bodily fluids',
    'Mold, grease and mildew restoration',
    'Inside fireplaces, soot or ashes',
    'Ironing or clothes folding (folding is included with a laundry extra)',
    'Commercial carpet cleaning or shampooing — we can refer a partner',
  ],
} as const;

/* -------------------------------------------------------------------------- */
/* Privacy policy                                                             */
/* -------------------------------------------------------------------------- */

export const PRIVACY: PageContent = {
  metaTitle: 'Privacy Policy | Maid4Condos',
  metaDescription:
    'How Maid4Condos collects, uses, stores and protects the personal information you share when requesting a quote or contacting our Toronto cleaning team.',
  eyebrow: 'Legal',
  heading: 'Privacy policy',
  intro:
    'This policy explains what information Maid4Condos collects through this website, why we collect it, how long we keep it and the choices you have.',
  sections: [
    {
      paragraphs: [
        'Maid4Condos ("we", "us", "our") operates this website. We are a Toronto cleaning company and this website exists to explain our cleaning services and to let you request a quote or contact our office. This page describes our practices for information collected through the website.',
      ],
    },
    {
      heading: 'What we collect',
      bullets: [
        'Quote requests. Your name, email address, phone number, neighbourhood and postal code, property type, number of bedrooms and bathrooms, approximate square footage, the cleaning service and frequency you selected, any add-ons you chose, your preferred date and arrival window, how we should access your home, and any notes you write in the additional information field.',
        'Messages. If you use the contact form we collect your name, email address, an optional phone number, the topic you selected and your message.',
        'Technical data. Like most websites, our host receives your IP address, browser and device information, and the page you requested. This is used for security, abuse prevention and aggregate traffic reporting.',
      ],
      paragraphs: [
        'We do not ask for payment card details through this website, and we never ask for your social insurance number, banking passwords or other sensitive identifiers.',
      ],
    },
    {
      heading: 'Why we use it',
      bullets: [
        'To prepare and send you a cleaning quote, and to answer your questions.',
        'To schedule and deliver the cleaning service you book.',
        'To send booking confirmations, reminders and service-related notices.',
        'To keep records of requests and cleanings so we can service you consistently.',
        'To detect and prevent spam, abuse and fraud on our forms.',
      ],
      paragraphs: [
        'We rely on your consent when you submit a form, and on our legitimate business interests when we keep records of the work we have done and protect the website from abuse.',
      ],
    },
    {
      heading: 'Who we share it with',
      paragraphs: [
        'We do not sell your personal information. We share it only with the service providers that make the website and our operations work:',
      ],
      bullets: [
        'Forms. A submission is used only to prepare your cleaning and to reply to you. This website keeps no account, no profile and no stored record of your enquiry.',
        'Hosting. This website is hosted on a third-party hosting service.',
        'Email. Quote requests and messages are emailed to our office so we can respond promptly.',
        'Analytics. If analytics is enabled, aggregated and, where configured, anonymised usage data is processed by Google Analytics.',
        'Maps. Our contact page and service area section embed Google Maps, which may set its own cookies.',
      ],
    },
    {
      paragraphs: [
        'We may also disclose information where we are legally required to do so, or to protect the safety and rights of our staff and clients.',
      ],
    },
    {
      heading: 'How long we keep it',
      paragraphs: [
        'Quote requests and messages are kept for as long as needed to respond to you and to maintain our business records. Records relating to completed cleanings are kept for our accounting and service history. You can ask us to delete your enquiry at any time.',
      ],
    },
    {
      heading: 'Cookies',
      paragraphs: [
        'This website sets no advertising cookies. If analytics is enabled, analytics cookies measure how visitors use the site, and you can block or delete them in your browser settings. The embedded map and video player set their own cookies once you interact with them.',
      ],
    },
    {
      heading: 'Security',
      paragraphs: [
        'We protect the information you send us with industry-standard measures: every page is served over an encrypted connection (HTTPS), forms are validated before they are accepted, and access to our office systems is restricted to authorised staff. This website is served as static files, so it has no database and keeps no record of your enquiry — what you send arrives directly in our office inbox.',
      ],
    },
    {
      heading: 'Your choices',
      bullets: [
        'Ask us what personal information we hold about you.',
        'Ask us to correct anything that is inaccurate.',
        'Ask us to delete your enquiry or message.',
        'Ask us to stop contacting you (except where we must retain a record for legal or accounting reasons).',
      ],
      paragraphs: ['To make any of these requests, contact our office using the details below.'],
    },
    {
      heading: 'Changes to this policy',
      paragraphs: [
        'If we change how we handle personal information we will update this page and revise the date at the top. Material changes affecting how we use information you have already given us will be communicated by email where we have an address for you.',
      ],
    },
  ],
};

/* -------------------------------------------------------------------------- */
/* Terms & conditions                                                         */
/* -------------------------------------------------------------------------- */

export const TERMS: PageContent = {
  metaTitle: 'Terms & Conditions | Maid4Condos',
  metaDescription:
    'The terms that apply to Maid4Condos cleaning services in Toronto: quotes and pricing, booking and access, our 24 hour guarantee, cancellation fees and what is not included.',
  eyebrow: 'Legal',
  heading: 'Service terms & conditions',
  intro:
    'These terms apply to residential cleaning services provided by Maid4Condos and to the use of this website. By booking a cleaning you agree to them.',
  sections: [
    {
      heading: '1. Quotes and pricing',
      paragraphs: [
        'Quotes are prepared from the information you provide about your property: sizing, room count, number of bathrooms and the condition of the space. We do not publish package prices on this website; the price for your cleaning is confirmed with you before anything is booked.',
        'If, on arrival, the property is materially larger than described or is below average condition, we may contact you to request additional time. Additional time is charged at an hourly rate agreed with you at the time of the request. We will not charge you for extra time without your approval. If we cannot reach you during an active service to report the condition or request extra time, we will stop at the maximum time allocated to that service.',
        'Frequency discounts — 20% weekly, 15% bi-weekly and 5% monthly — apply to recurring AutoPilot schedules. Custom rates are available for daily service and high-volume requirements.',
      ],
    },
    {
      heading: '2. Booking and payment',
      paragraphs: [
        'When you book through this website or over the phone you are charged at the time of booking. Recurring schedules do not require a contract for residential clients, but by using our services you agree to these terms. Corporate and commercial clients are covered by separate service agreements.',
      ],
    },
    {
      heading: '3. Scheduling and arrival windows',
      paragraphs: [
        'We use either two-hour arrival windows or a flexible window during which we will clean between 9am and 5pm. Your chosen window is recorded at the time of booking. Arrival times are estimates because traffic, lockouts and same-day service changes are outside our control. Residential cleanings are scheduled Monday to Friday between 8am and 6pm; we do not currently offer evening or weekend residential bookings.',
      ],
    },
    {
      heading: '4. Access to your home',
      paragraphs: [
        'You choose how we access the property: you will be home, key at concierge, smart key pad, or lockbox and instructions. If access arrangements change, please tell our office as early as possible. If we cannot gain access within 30 minutes of arrival, the lockout provisions in section 7 apply.',
      ],
    },
    {
      heading: '5. Our satisfaction guarantee',
      paragraphs: [
        '{{guarantee}}',
        'The guarantee depends on the property being as described and in average condition at the time of the service, and on you contacting our office within 24 hours of the cleaning.',
      ],
    },
    {
      heading: '6. What is not included',
      paragraphs: [
        'To protect the health and safety of our staff, and to set clear expectations, the following are outside the scope of our services:',
      ],
      bullets: [
        'Inside or hard-to-reach light fixtures, exterior windows, blinds, drapes or shades.',
        'Inside dishwashers, washing machines or range hood fans.',
        'Cleaning during or after an infestation.',
        'Moving anything heavier than 30 lbs, or climbing higher than 2 ft on a ladder or step stool.',
        'Exterior or outdoor cleaning, including balconies.',
        'Pet or human waste and bodily fluids, including litter boxes, pet messes and overflowed toilets.',
        'Restoration of severely worn, stained, mildewed or mould infested caulking and grout. Mild surface presence can usually be addressed; where an infestation may pose an air-quality risk we reserve the right to remove our staff for health and safety, and our cancellation policy applies.',
        'Cleaning inside fireplaces, soot or ashes.',
        'Ironing or clothes folding. Folding is included where you have added a laundry load.',
        'Commercial carpet cleaning or shampooing — we can refer you to a partner.',
        'Post-renovation cleaning where construction debris or an active construction zone is involved. We are a finishing crew: we complete the final detailed clean before a property is returned or delivered.',
      ],
    },
    {
      heading: '7. Cancellations, rescheduling and lockouts',
      bullets: [
        'For scheduled cleanings, a cancellation fee of 50 dollars applies if a service is cancelled or rescheduled within 48 hours of your scheduled cleaning.',
        'For same-day cancellations, lockouts or rescheduling, or where we cannot gain access within 30 minutes of our arrival, we reserve the right to charge the greater of 75 dollars or 50% of the total service fee.',
        'Recurring service cancellations: our cancellation policy applies to each visit. To cancel a recurring schedule before four or more completed visits, you will be charged the difference between your discounted frequency rate and a one-time cleaning fee for the prior visits.',
      ],
    },
    {
      heading: '8. Damage and liability',
      paragraphs: [
        'Our cleaners conduct themselves professionally in your home at all times. In the event of accidental damage, notify our office within 48 hours of your service. We cannot guarantee reimbursement for damage reported more than 48 hours after the end of the appointment, and we ask that you are reachable so we can assess and repair any damage.',
        'Maid4Condos carries {{liability}} in liability insurance, our staff are covered by WSIB and all staff are bonded.',
      ],
    },
    {
      heading: '9. Preparation and clutter',
      paragraphs: [
        'Please pick up loose items, garbage, debris and loose clothing from floors, table tops and countertops before we arrive. This allows our staff to spend the allocated time cleaning surfaces rather than tidying. We will work around highly cluttered areas and storage areas to the best of our ability.',
      ],
    },
    {
      heading: '10. Pets',
      paragraphs: [
        'We are happy to clean in homes with pets. Please tell us in advance so we can note it on your work order, and let us know about allergies to specific products.',
      ],
    },
    {
      heading: '11. Feedback',
      paragraphs: [
        'After every cleaning you will receive a feedback tool. We genuinely value your opinion — client comments and suggestions are how our training and quality control teams continue to improve.',
      ],
    },
    {
      heading: '12. Website use',
      paragraphs: [
        'The content on this website is provided for information about our services. Photographs are used under the licences listed on our photography credits page. If you would like to reuse any of our own written content, please ask first.',
      ],
    },
    {
      heading: '13. Governing law',
      paragraphs: [
        'These terms are governed by the laws of the Province of Ontario and the applicable laws of Canada.',
      ],
    },
  ],
};

/** Replaces `{{token}}` placeholders with live settings values. */
export function interpolate(text: string, values: Record<string, string>): string {
  return text.replace(/\{\{(\w+)\}\}/g, (match, key: string) => values[key] ?? match);
}

export function interpolateSections(
  sections: ContentSection[],
  values: Record<string, string>,
): ContentSection[] {
  return sections.map((section) => ({
    ...section,
    paragraphs: section.paragraphs?.map((line) => interpolate(line, values)),
    bullets: section.bullets?.map((line) => interpolate(line, values)),
  }));
}

/* -------------------------------------------------------------------------- */
/* 404                                                                        */
/* -------------------------------------------------------------------------- */

export const NOT_FOUND = {
  metaTitle: 'Page not found | Maid4Condos',
  heading: 'That page has been tidied away',
  intro:
    'The page you were looking for does not exist or has moved. Here are the places most people are heading next.',
};
