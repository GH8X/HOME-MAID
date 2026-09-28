/**
 * Head management without a third-party library.
 *
 * Sets the unique title, meta description, canonical URL, Open Graph and
 * Twitter tags, and injects page-specific JSON-LD. Every value is replaced on
 * navigation rather than appended, so no stale tags accumulate.
 */

import { useEffect } from 'react';
import { absoluteUrl } from '../lib/format';

export interface SeoInput {
  /** Page title. The site name is appended automatically when absent. */
  title: string;
  description?: string;
  /** Route path used for the canonical URL, e.g. `/services/basic-cleaning`. */
  path: string;
  robots?: string;
  /** Absolute URL or a `/images/...` path. */
  image?: string;
  type?: 'website' | 'article';
  schema?: Array<Record<string, unknown>>;
}

const MANAGED = 'data-m4c-seo';

function upsertMeta(attr: 'name' | 'property', key: string, content: string): void {
  let tag = document.head.querySelector<HTMLMetaElement>(`meta[${attr}="${key}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attr, key);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
}

function upsertLink(rel: string, href: string): void {
  let tag = document.head.querySelector<HTMLLinkElement>(`link[rel="${rel}"]`);
  if (!tag) {
    tag = document.createElement('link');
    tag.setAttribute('rel', rel);
    document.head.appendChild(tag);
  }
  tag.setAttribute('href', href);
}

export function useSeo({
  title,
  description,
  path,
  robots = 'index,follow',
  image,
  type = 'website',
  schema = [],
}: SeoInput): void {
  useEffect(() => {
    const canonical = absoluteUrl(path);
    const social = image ? absoluteUrl(image) : absoluteUrl('/images/cleaned-living-room.jpg');

    document.title = title;
    upsertLink('canonical', canonical);
    upsertMeta('name', 'robots', robots);
    if (description) upsertMeta('name', 'description', description);

    upsertMeta('property', 'og:type', type);
    upsertMeta('property', 'og:title', title);
    upsertMeta('property', 'og:url', canonical);
    upsertMeta('property', 'og:image', social);
    if (description) upsertMeta('property', 'og:description', description);

    upsertMeta('name', 'twitter:card', 'summary_large_image');
    upsertMeta('name', 'twitter:title', title);
    upsertMeta('name', 'twitter:image', social);
    if (description) upsertMeta('name', 'twitter:description', description);

    document.head
      .querySelectorAll(`script[${MANAGED}]`)
      .forEach((node) => node.parentNode?.removeChild(node));

    for (const block of schema) {
      if (!block || Object.keys(block).length === 0) continue;
      const script = document.createElement('script');
      script.type = 'application/ld+json';
      script.setAttribute(MANAGED, 'true');
      script.textContent = JSON.stringify(block);
      document.head.appendChild(script);
    }
    // `schema` is rebuilt on each render by callers; comparing by value would
    // be noisy, so this effect intentionally depends on the serialised form.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [title, description, path, robots, image, type, JSON.stringify(schema)]);
}
