/** Inner-page hero: eyebrow, title, intro, breadcrumbs, CTAs and optional meta. */

import type { ReactNode } from 'react';

import { Breadcrumbs, type Crumb } from './ui';

export interface PageHeroProps {
  eyebrow?: string;
  title: string;
  intro?: string;
  crumbs?: Crumb[];
  actions?: ReactNode;
  meta?: Array<{ value: string; label: string }>;
}

export function PageHero({ eyebrow, title, intro, crumbs, actions, meta }: PageHeroProps) {
  return (
    <section className="page-hero">
      <div className="container container--wide page-hero__inner">
        {crumbs && crumbs.length > 1 ? <Breadcrumbs items={crumbs} /> : null}
        <div className="page-hero__copy">
          {eyebrow ? <p className="eyebrow">{eyebrow}</p> : null}
          <h1 className="page-hero__title">{title}</h1>
          {intro ? <p className="page-hero__intro">{intro}</p> : null}
          {actions ? <div className="page-hero__actions">{actions}</div> : null}
        </div>
        {meta && meta.length ? (
          <div className="page-hero__meta">
            {meta.map((item) => (
              <div className="page-hero__meta-item" key={item.label}>
                <span className="page-hero__meta-value">{item.value}</span>
                <span className="page-hero__meta-label">{item.label}</span>
              </div>
            ))}
          </div>
        ) : null}
      </div>
    </section>
  );
}
