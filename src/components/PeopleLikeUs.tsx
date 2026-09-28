/**
 * "People Like Us!" — the recognition wall.
 *
 * Every entry links to the real profile the badge points at on the original
 * site. Nothing is claimed that the business does not already publish.
 */

import { PEOPLE_LIKE_US } from '../data/peopleLikeUs';
import { trackEvent } from '../lib/analytics';
import { classNames } from '../lib/format';
import { useContent } from '../services/contentService';
import { Icon } from './Icon';
import { Reveal, SectionHeading } from './ui';

export function PeopleLikeUs({ tone = 'surface' }: { tone?: 'surface' | 'plain' }) {
  const { settings } = useContent();

  return (
    <section className={classNames('section', tone === 'surface' && 'section--surface')}>
      <div className="container container--wide">
        <Reveal>
          <SectionHeading
            eyebrow="Recognition"
            title={settings.home_people_heading}
            intro={settings.home_people_intro}
            align="center"
          />
        </Reveal>

        <div className="recognition-grid">
          {PEOPLE_LIKE_US.map((item, index) => {
            const body = (
              <>
                <span className="recognition-card__tag">{item.tag}</span>
                <span className="recognition-card__name">{item.name}</span>
                <span className="recognition-card__note">{item.note}</span>
                {item.url ? (
                  <span className="recognition-card__link">
                    View profile
                    <Icon name="external" size={15} />
                  </span>
                ) : null}
              </>
            );

            return (
              <Reveal key={item.name} delay={index * 40}>
                {item.url ? (
                  <a
                    className="recognition-card recognition-card--link"
                    href={item.url}
                    target="_blank"
                    rel="noreferrer noopener"
                    onClick={() => trackEvent('recognition_click', { platform: item.name })}
                  >
                    {body}
                  </a>
                ) : (
                  <div className="recognition-card">{body}</div>
                )}
              </Reveal>
            );
          })}
        </div>
      </div>
    </section>
  );
}
