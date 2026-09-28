/** Formatting helpers. Pure functions only — safe in any module. */

/** The canonical origin used for canonical tags, OG URLs and the sitemap. */
export const SITE_ORIGIN = (
  import.meta.env.VITE_SITE_URL || 'https://www.maid4condos.com'
).replace(/\/+$/, '');

export function absoluteUrl(path = '/'): string {
  if (/^https?:\/\//i.test(path)) return path;
  const suffix = path.startsWith('/') ? path : `/${path}`;
  return `${SITE_ORIGIN}${suffix === '/' ? '/' : suffix.replace(/\/+$/, '')}`;
}

/** Phone numbers become usable `tel:` hrefs, keeping any leading plus. */
export function telHref(phone: string): string {
  const plus = phone.trim().startsWith('+') ? '+' : '';
  return `tel:${plus}${phone.replace(/[^\d]/g, '')}`;
}

export function classNames(
  ...parts: Array<string | false | null | undefined>
): string {
  return parts.filter(Boolean).join(' ');
}

/** Trims to whole words so excerpts never cut mid-word. */
export function excerpt(text: string, max = 160): string {
  const clean = text.replace(/\s+/g, ' ').trim();
  if (clean.length <= max) return clean;
  const cut = clean.slice(0, max);
  const lastSpace = cut.lastIndexOf(' ');
  return `${cut.slice(0, lastSpace > 40 ? lastSpace : max).trimEnd()}…`;
}

export function titleCaseFromSlug(slug: string): string {
  return slug
    .split(/[-_]/)
    .filter(Boolean)
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

/** Splits a textarea into clean, non-empty lines. */
export function toLines(value: string | null | undefined): string[] {
  if (!value) return [];
  return value
    .split('\n')
    .map((line) => line.replace(/^[-•*]\s*/, '').trim())
    .filter(Boolean);
}

/** Safe JSON parse with a typed fallback. */
export function parseJson<T>(value: string | null | undefined, fallback: T): T {
  if (!value) return fallback;
  try {
    const parsed = JSON.parse(value);
    return (parsed ?? fallback) as T;
  } catch {
    return fallback;
  }
}

export function formatDate(value: string | null | undefined): string {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  return date.toLocaleDateString('en-CA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

export function formatDateTime(value: string | null | undefined): string {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  return date.toLocaleString('en-CA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  });
}

/** Groups a flat list into ordered `[label, items[]]` pairs. */
export function entriesOf<T>(record: Record<string, T>): Array<[string, T]> {
  return Object.entries(record);
}
