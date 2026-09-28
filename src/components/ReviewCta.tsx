/**
 * "Make a review" — where a client can rate Maid4Condos.
 *
 * The three destinations and the survey link are the ones the business
 * publishes, each with the review count it displays. No review or count is
 * invented, and no rating is shown as an average because the source does not
 * publish one.
 */

import { REVIEW_DESTINATIONS, SURVEY_URL } from '../data/peopleLikeUs';
import { trackEvent } from '../lib/analytics';
import { useContent } from '../services/contentService';
import { Icon } from './Icon';
import { Reveal, SectionHeading } from './ui';

const SURVEY_TEXT =
  'Give it to us straight. Our simple survey allows you to easily convey your cleaning needs and we will incorporate your feedback into our ever evolving strategies.';

export function ReviewCta() {
  const { settings } = useContent();

  return (
    <section className="section" id="make-a-review">
      <div className="container container--wide">
        <Reveal>
          <SectionHeading
            eyebrow="Make a review"
            title={settings.home_review_heading}
            intro={settings.home_review_intro}
            align="center"
          />
        </Reveal>

        <div className="review-grid">
          {REVIEW_DESTINATIONS.map((destination, index) => (
            <Reveal key={destination.platform} delay={index * 60}>
              <article className="review-card">
                <p className="review-card__figure">
                  <span className="review-card__count">{destination.count}</span>
                  <span className="review-card__label">reviews on {destination.platform}</span>
                </p>
                <div className="review-card__actions">
                  <a
                    className="btn btn--primary btn--sm"
                    href={destination.writeUrl}
                    target="_blank"
                    rel="noreferrer noopener"
                    onClick={() => trackEvent('review_click', { platform: destination.platform })}
                  >
                    Write a review
                  </a>
                  <a
                    className="btn btn--ghost btn--sm"
                    href={destination.readUrl}
                    target="_blank"
                    rel="noreferrer noopener"
                  >
                    Read reviews
                  </a>
                </div>
              </article>
            </Reveal>
          ))}
        </div>

        <Reveal delay={120}>
          <div className="callout callout--wide">
            <p className="callout__title">
              <Icon name="clipboard" size={19} />
              We love getting customer feedback
            </p>
            <p className="callout__text">{SURVEY_TEXT}</p>
            <a
              className="btn btn--secondary"
              href={SURVEY_URL}
              target="_blank"
              rel="noreferrer noopener"
              onClick={() => trackEvent('review_click', { platform: 'survey' })}
            >
              Take our survey
              <Icon name="arrow" size={17} />
            </a>
          </div>
        </Reveal>
      </div>
    </section>
  );
}
