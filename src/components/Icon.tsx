/**
 * Inline SVG icon set.
 *
 * Inline rather than an icon font or sprite: no extra network request, and
 * every glyph inherits `currentColor`. Strokes are kept consistent at 1.75 on
 * a 24×24 grid so the set reads as one family.
 */

import type { IconName } from '../lib/types';
import type { CSSProperties, ReactNode } from 'react';

const PATHS: Record<IconName, ReactNode> = {
  badge: (
    <>
      <circle cx="12" cy="9.5" r="5.5" />
      <path d="M8.6 14.2 7 21l5-2.6L17 21l-1.6-6.8" />
    </>
  ),
  shield: (
    <>
      <path d="M12 3 20 6v5.5c0 4.3-3.1 7.9-8 9.5-4.9-1.6-8-5.2-8-9.5V6z" />
    </>
  ),
  shieldCheck: (
    <>
      <path d="M12 3 20 6v5.5c0 4.3-3.1 7.9-8 9.5-4.9-1.6-8-5.2-8-9.5V6z" />
      <path d="m9 11.8 2.2 2.2L15.4 9.8" />
    </>
  ),
  team: (
    <>
      <circle cx="9" cy="8.5" r="3.2" />
      <path d="M3.5 20a5.5 5.5 0 0 1 11 0" />
      <path d="M16 5.8a3.2 3.2 0 0 1 0 6.2" />
      <path d="M17.4 14.6A5.5 5.5 0 0 1 20.5 20" />
    </>
  ),
  spark: (
    <>
      <path d="M12 3.2l2 5.3 5.3 2-5.3 2-2 5.3-2-5.3-5.3-2 5.3-2z" />
    </>
  ),
  sparkles: (
    <>
      <path d="M11 3.5l1.5 4 4 1.5-4 1.5-1.5 4-1.5-4-4-1.5 4-1.5z" />
      <path d="M18 14l.85 2.15L21 17l-2.15.85L18 20l-.85-2.15L15 17l2.15-.85z" />
      <path d="M5.5 13.5l.6 1.5 1.5.6-1.5.6-.6 1.5-.6-1.5L3.4 16l1.5-.6z" />
    </>
  ),
  calendar: (
    <>
      <rect x="3.5" y="5" width="17" height="15.5" rx="3" />
      <path d="M3.5 10h17M8 3.2v3.4M16 3.2v3.4" />
    </>
  ),
  leaf: (
    <>
      <path d="M20 4.5c0 8-5.2 12-10.5 12H6.5C6.5 10 11 4.5 20 4.5z" />
      <path d="M4 20c1.6-5.4 5-9.4 10-12" />
    </>
  ),
  phone: (
    <>
      <path d="M6.2 3.8h3l1.5 4-2 1.3a11.5 11.5 0 0 0 6.2 6.2l1.3-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.2 6a2 2 0 0 1 2-2.2z" />
    </>
  ),
  mail: (
    <>
      <rect x="3" y="5.5" width="18" height="13" rx="3" />
      <path d="m4.5 8 6.4 4.6a2 2 0 0 0 2.2 0L19.5 8" />
    </>
  ),
  pin: (
    <>
      <path d="M12 21c4-4.4 6.5-7.6 6.5-10.6a6.5 6.5 0 0 0-13 0C5.5 13.4 8 16.6 12 21z" />
      <circle cx="12" cy="10.3" r="2.4" />
    </>
  ),
  clock: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <path d="M12 7.4V12l3.2 1.9" />
    </>
  ),
  check: <path d="m5 12.8 4.3 4.2L19 7" />,
  checkCircle: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <path d="m8.2 12.4 2.6 2.6 5-5.4" />
    </>
  ),
  arrow: <path d="M4.5 12h14m-5.4-5.4L18.5 12l-5.4 5.4" />,
  arrowUp: <path d="M12 19V5m-5.4 5.4L12 5l5.4 5.4" />,
  menu: <path d="M4 7h16M4 12h16M4 17h16" />,
  close: <path d="M6 6l12 12M18 6 6 18" />,
  quote: (
    <>
      <path d="M9.5 6.5C6.9 8 5.4 10.3 5.4 13.2c0 2.5 1.4 4.3 3.5 4.3 1.8 0 3.1-1.2 3.1-3 0-1.7-1.1-2.8-2.7-2.8h-.5c.3-1.4 1.3-2.6 2.8-3.5zM19 6.5c-2.6 1.5-4.1 3.8-4.1 6.7 0 2.5 1.4 4.3 3.5 4.3 1.8 0 3.1-1.2 3.1-3 0-1.7-1.1-2.8-2.7-2.8h-.5c.3-1.4 1.3-2.6 2.8-3.5z" />
    </>
  ),
  star: <path d="m12 3.8 2.6 5.4 5.9.8-4.3 4.1 1.1 5.9-5.3-2.9-5.3 2.9 1.1-5.9L3.5 10l5.9-.8z" />,
  chevron: <path d="m6.5 9.5 5.5 5.5 5.5-5.5" />,
  plus: <path d="M12 5.5v13M5.5 12h13" />,
  minus: <path d="M5.5 12h13" />,
  trash: (
    <>
      <path d="M4.5 7h15M9.5 7V5.2a1.7 1.7 0 0 1 1.7-1.7h1.6a1.7 1.7 0 0 1 1.7 1.7V7" />
      <path d="M6.3 7l.9 12.1a2 2 0 0 0 2 1.9h5.6a2 2 0 0 0 2-1.9L17.7 7" />
      <path d="M10.5 11v6M13.5 11v6" />
    </>
  ),
  edit: (
    <>
      <path d="M4.5 19.5h4L20 8a2.1 2.1 0 0 0-3-3L5.4 16.6z" />
      <path d="m15.5 6.5 3 3" />
    </>
  ),
  eye: (
    <>
      <path d="M2.8 12S6.3 6 12 6s9.2 6 9.2 6-3.5 6-9.2 6-9.2-6-9.2-6z" />
      <circle cx="12" cy="12" r="2.7" />
    </>
  ),
  eyeOff: (
    <>
      <path d="M4 4l16 16" />
      <path d="M9.6 5.3A9.9 9.9 0 0 1 12 5c5.7 0 9.2 7 9.2 7a17 17 0 0 1-2.9 3.7" />
      <path d="M6.4 7.3A16.6 16.6 0 0 0 2.8 12s3.5 7 9.2 7a9.6 9.6 0 0 0 4.1-.9" />
      <path d="M10.2 10.3a2.7 2.7 0 0 0 3.6 3.6" />
    </>
  ),
  upload: (
    <>
      <path d="M12 16V4.8m-4 4L12 4.6l4 4.2" />
      <path d="M4.5 15.5v2.3a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-2.3" />
    </>
  ),
  logout: (
    <>
      <path d="M9.5 20H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3.5" />
      <path d="M15 8.5 18.5 12 15 15.5M18.5 12H9" />
    </>
  ),
  dashboard: (
    <>
      <rect x="3.6" y="3.6" width="7.2" height="7.2" rx="2" />
      <rect x="13.2" y="3.6" width="7.2" height="7.2" rx="2" />
      <rect x="3.6" y="13.2" width="7.2" height="7.2" rx="2" />
      <rect x="13.2" y="13.2" width="7.2" height="7.2" rx="2" />
    </>
  ),
  services: (
    <>
      <path d="M12 3.2l2 5.3 5.3 2-5.3 2-2 5.3-2-5.3-5.3-2 5.3-2z" />
      <path d="M18.5 16.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7z" />
    </>
  ),
  faq: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <path d="M9.6 9.5a2.5 2.5 0 0 1 4.8.9c0 1.7-2.4 2-2.4 3.6" />
      <path d="M12 17h.01" />
    </>
  ),
  testimonial: (
    <>
      <path d="M20 14.5a2.5 2.5 0 0 1-2.5 2.5H9l-4.5 3.4V6.5A2.5 2.5 0 0 1 7 4h10.5A2.5 2.5 0 0 1 20 6.5z" />
      <path d="M8.5 9.5h7M8.5 12.8h4.5" />
    </>
  ),
  settings: (
    <>
      <path d="M5 7h6M15 7h4M5 17h4M13 17h6" />
      <circle cx="13" cy="7" r="2.2" />
      <circle cx="11" cy="17" r="2.2" />
    </>
  ),
  image: (
    <>
      <rect x="3.5" y="4.8" width="17" height="14.4" rx="3" />
      <circle cx="9" cy="10.2" r="1.7" />
      <path d="m4.5 17.5 4.6-4.2 3.4 3 2.7-2.4 4.3 3.9" />
    </>
  ),
  inbox: (
    <>
      <path d="M3.6 12.4 6 5.6a2 2 0 0 1 1.9-1.4h8.2A2 2 0 0 1 18 5.6l2.4 6.8" />
      <path d="M3.6 12.4h4.2l1.3 2.4h5.8l1.3-2.4h4.2v4.8a2.4 2.4 0 0 1-2.4 2.4H6a2.4 2.4 0 0 1-2.4-2.4z" />
    </>
  ),
  external: (
    <>
      <path d="M13.5 5h5.5v5.5" />
      <path d="M19 5l-7.5 7.5" />
      <path d="M18 14.5v3A2.5 2.5 0 0 1 15.5 20h-9A2.5 2.5 0 0 1 4 17.5v-9A2.5 2.5 0 0 1 6.5 6h3" />
    </>
  ),
  search: (
    <>
      <circle cx="11" cy="11" r="6.5" />
      <path d="m20 20-3.7-3.7" />
    </>
  ),
  alert: (
    <>
      <path d="M12 4.5 21 19.5H3z" />
      <path d="M12 10v4M12 17h.01" />
    </>
  ),
  info: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <path d="M12 11v5M12 8h.01" />
    </>
  ),
  home: (
    <>
      <path d="M4 10.6 12 4l8 6.6" />
      <path d="M6 9.8V19a1.6 1.6 0 0 0 1.6 1.6h8.8A1.6 1.6 0 0 0 18 19V9.8" />
      <path d="M10 20.6v-5.4h4v5.4" />
    </>
  ),
  refresh: (
    <>
      <path d="M20 11a8 8 0 0 0-13.8-5.3L4 8" />
      <path d="M4 4.2V8h3.8" />
      <path d="M4 13a8 8 0 0 0 13.8 5.3L20 16" />
      <path d="M20 19.8V16h-3.8" />
    </>
  ),
  lock: (
    <>
      <rect x="4.8" y="10.2" width="14.4" height="10" rx="3" />
      <path d="M8.4 10.2V7.8a3.6 3.6 0 0 1 7.2 0v2.4" />
    </>
  ),
  user: (
    <>
      <circle cx="12" cy="8.4" r="3.6" />
      <path d="M5 20a7 7 0 0 1 14 0" />
    </>
  ),
  layers: (
    <>
      <path d="m12 3.6 8.4 4.2L12 12 3.6 7.8z" />
      <path d="m3.6 12.6 8.4 4.2 8.4-4.2" />
      <path d="m3.6 17 8.4 4.2L20.4 17" />
    </>
  ),
  tag: (
    <>
      <path d="M4 10.6V5.2A1.2 1.2 0 0 1 5.2 4h5.4a2 2 0 0 1 1.4.6l7.4 7.4a2 2 0 0 1 0 2.8l-5.2 5.2a2 2 0 0 1-2.8 0L4.6 12a2 2 0 0 1-.6-1.4z" />
      <path d="M8 8h.01" />
    </>
  ),
  map: (
    <>
      <path d="M9.4 4.6 4 6.8v12.6l5.4-2.2 5.2 2.2 5.4-2.2V4.6l-5.4 2.2z" />
      <path d="M9.4 4.6v12.6M14.6 6.8v12.6" />
    </>
  ),
  building: (
    <>
      <path d="M5.4 20.4V5.6a2 2 0 0 1 2-2h9.2a2 2 0 0 1 2 2v14.8" />
      <path d="M4 20.4h16M9 7.8h2M13 7.8h2M9 11.8h2M13 11.8h2M9 15.8h6" />
    </>
  ),
  clipboard: (
    <>
      <path d="M9 4.6H7.6a2 2 0 0 0-2 2v11.8a2 2 0 0 0 2 2h8.8a2 2 0 0 0 2-2V6.6a2 2 0 0 0-2-2H15" />
      <rect x="9" y="3" width="6" height="3.4" rx="1.2" />
      <path d="m9.4 13.4 1.8 1.8 3.4-3.6" />
    </>
  ),
  play: <path d="M9 6.6 18 12l-9 5.4V6.6z" />,
};

export interface IconProps {
  name: IconName;
  size?: number;
  className?: string;
  strokeWidth?: number;
  style?: CSSProperties;
  /** Decorative icons are hidden from assistive tech by default. */
  title?: string;
}

export function Icon({ name, size = 20, className, strokeWidth = 1.75, style, title }: IconProps) {
  const content = PATHS[name] ?? PATHS.spark;
  return (
    <svg
      className={className ? `icon ${className}` : 'icon'}
      style={style}
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth={strokeWidth}
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden={title ? undefined : true}
      role={title ? 'img' : undefined}
      focusable="false"
    >
      {title ? <title>{title}</title> : null}
      {content}
    </svg>
  );
}
