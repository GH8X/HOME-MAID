/**
 * "People Like Us!" — the recognition wall, and where clients leave a review.
 *
 * Both blocks come from the same section of the original Maid4Condos homepage.
 * Every badge and every link below is one the business publishes on its own
 * site, so nothing here is invented: the accreditation is real, the profile
 * behind each link is real, and the review counts are the counts Maid4Condos
 * displays. No award, rating or review total has been added or rounded.
 */

export interface Recognition {
  /** The platform or publication. */
  name: string;
  /** What the recognition actually is, in the business's own terms. */
  note: string;
  /** The page the badge links to, or null where the badge is not linked. */
  url: string | null;
  /** A short label for the badge tile. */
  tag: string;
}

export const PEOPLE_LIKE_US: Recognition[] = [
  {
    name: 'HomeStars',
    note: 'Verified and reviewed on HomeStars, Canada’s home improvement marketplace.',
    url: 'https://homestars.com/companies/2813076-maid4condos',
    tag: 'Verified',
  },
  {
    name: 'HomeStars Best of Award',
    note: 'Best of the Best and Best of the Year winner in 2023 and 2024.',
    url: 'https://homestars.com/companies/2813076-maid4condos',
    tag: '2023 & 2024',
  },
  {
    name: 'Google Reviews',
    note: 'Rated by clients on Google, where our reviews stay open to everyone.',
    url: 'https://g.page/Maid4Condos?gm',
    tag: 'Google',
  },
  {
    name: 'Yelp',
    note: 'Reviewed on Yelp by condo owners and renters across Toronto.',
    url: 'https://www.yelp.ca/biz/maid4condos-toronto-2',
    tag: 'Yelp',
  },
  {
    name: 'Trustpilot',
    note: 'Independently reviewed on Trustpilot, where the score is not ours to edit.',
    url: 'https://ca.trustpilot.com/review/maid4condos.com',
    tag: 'Trustpilot',
  },
  {
    name: 'Three Best Rated',
    note: 'Listed among the top three house cleaning services in Toronto.',
    url: 'https://threebestrated.ca/house-cleaning-services-in-toronto-on',
    tag: 'Top 3',
  },
  {
    name: 'Forbes',
    note: 'Named one of Canada’s Best Startup Employers by Forbes.',
    url: 'https://www.forbes.com/companies/maid4condos/?list=canadas-best-startup-employers',
    tag: 'Forbes',
  },
  {
    name: 'Better Business Bureau',
    note: 'Accredited business in good standing with the BBB.',
    url: 'https://www.bbb.org/ca/on/toronto/profile/maid-service/maid4condos-inc-0107-1366777/',
    tag: 'Accredited',
  },
  {
    name: 'Toronto Star',
    note: 'Featured in the Toronto Star.',
    url: null,
    tag: 'Press',
  },
];

export interface ReviewDestination {
  /** Where the review lives. */
  platform: string;
  /** The review count the business publishes for that platform. */
  count: string;
  /** Read the reviews. */
  readUrl: string;
  /** Leave a review. */
  writeUrl: string;
}

export const REVIEW_DESTINATIONS: ReviewDestination[] = [
  {
    platform: 'maid4condos.com',
    count: '250',
    readUrl: 'https://www.maid4condos.com/reviews',
    writeUrl: 'https://www.maid4condos.com/add-a-review/',
  },
  {
    platform: 'Yelp',
    count: '31',
    readUrl: 'https://www.yelp.ca/biz/maid4condos-toronto-2',
    writeUrl: 'https://www.yelp.ca/writeareview/biz/bah3voBeGkH6pqYKQMUlMQ',
  },
  {
    platform: 'Google',
    count: '77',
    readUrl: 'https://g.page/Maid4Condos?gm',
    writeUrl: 'https://www.google.ca/search?q=maid4condos#lrd=0x882b3518a63dc8dd:0xfbfafaf1a17e591b,3,',
  },
];

/** The client feedback survey Maid4Condos links from the same section. */
export const SURVEY_URL = 'https://www.maid4condos.com/survey';
