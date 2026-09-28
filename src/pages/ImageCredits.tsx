/** Photography credits — required by the CC BY licences on our imagery. */

import { PageHero } from '../components/PageHero';
import { SmartImage } from '../components/ui';
import { photoCredits } from '../data/media';
import { useSeo } from '../hooks/useSeo';
import { breadcrumbSchema } from '../lib/schema';

export function ImageCredits() {
  const crumbs = [
    { name: 'Home', path: '/' },
    { name: 'Photography credits', path: '/image-credits' },
  ];

  const credits = photoCredits();

  useSeo({
    title: 'Photography Credits | Maid4Condos',
    description:
      'Attribution and licence details for the photography used on the Maid4Condos website, including Creative Commons and public domain sources.',
    path: '/image-credits',
    robots: 'noindex,follow',
    schema: [breadcrumbSchema(crumbs)],
  });

  return (
    <>
      <PageHero
        eyebrow="Legal"
        title="Photography credits"
        intro="The photographs on this website come from Wikimedia Commons and are used under the licences listed below. Creative Commons Attribution licences require the attribution shown here, so this page stays public."
        crumbs={crumbs}
      />

      <section className="section">
        <div className="container container--narrow">
          <div className="credits-list">
            {credits.map((entry) => (
              <article className="credit-card" key={entry.src}>
                <div className="credit-card__thumb">
                  <SmartImage
                    name={entry.name}
                    sizes="140px"
                    alt={entry.alt}
                  />
                </div>
                <div>
                  <p className="credit-card__title">
                    “{entry.credit?.title ?? entry.name}”
                  </p>
                  <div className="credit-card__meta">
                    <span>
                      Photographer: <strong>{entry.credit?.author}</strong>
                    </span>
                    <span>
                      Licence: <strong>{entry.credit?.license}</strong>
                    </span>
                    {entry.credit?.source ? (
                      <a href={entry.credit.source} target="_blank" rel="noopener noreferrer">
                        View the original file on Wikimedia Commons
                      </a>
                    ) : null}
                  </div>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
