/**
 * Small shared primitives used across the site.
 */

import { useEffect, useId, useRef, useState, type ReactNode } from 'react';
import { Link, useLocation } from 'react-router-dom';

import { image } from '../data/media';
import { classNames } from '../lib/format';
import { Icon } from './Icon';

/* -------------------------------------------------------------------------- */
/* Brand                                                                      */
/* -------------------------------------------------------------------------- */

export function BrandMark({
  size = 42,
  className,
}: {
  size?: number;
  className?: string;
}) {
  // The mark renders in several places at once, so the gradient needs its own
  // id each time rather than duplicating one id across the document.
  const gradientId = `m4c-brand-${useId().replace(/[:]/g, '')}`;

  return (
    <svg
      className={className}
      width={size}
      height={size}
      viewBox="0 0 64 64"
      role="img"
      aria-label="Maid4Condos"
      focusable="false"
    >
      <defs>
        <linearGradient id={gradientId} x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stopColor="#1a8f7d" />
          <stop offset="1" stopColor="#0b3f3a" />
        </linearGradient>
      </defs>
      <rect width="64" height="64" rx="18" fill={`url(#${gradientId})`} />
      <path d="M32 13.5 47 26v6.2H17V26z" fill="#fff" opacity="0.96" />
      <path
        d="M20.5 34h23v14.2a2.3 2.3 0 0 1-2.3 2.3H22.8a2.3 2.3 0 0 1-2.3-2.3z"
        fill="#fff"
        opacity="0.82"
      />
      <path d="M32 30.2l1.9 4.6 4.6 1.9-4.6 1.9-1.9 4.6-1.9-4.6-4.6-1.9 4.6-1.9z" fill="#0b3f3a" opacity="0.9" />
    </svg>
  );
}

export function Brand({
  size = 42,
  showTagline = true,
  className,
}: {
  size?: number;
  showTagline?: boolean;
  className?: string;
}) {
  return (
    <span className={classNames('brand', className)}>
      <BrandMark size={size} className="brand__mark" />
      <span className="brand__text">
        <span className="brand__name">Maid4Condos</span>
        {showTagline ? <span className="brand__tag">Condo cleaning, Toronto</span> : null}
      </span>
    </span>
  );
}

/* -------------------------------------------------------------------------- */
/* Images                                                                     */
/* -------------------------------------------------------------------------- */

export interface SmartImageProps {
  /** Manifest slug, or a full URL for a photograph hosted elsewhere. */
  name: string | null | undefined;
  alt?: string;
  sizes?: string;
  className?: string;
  /** Above-the-fold images load eagerly and synchronously. */
  priority?: boolean;
}

/**
 * Renders a photo with:
 *   - WebP offered first and the JPEG kept as the fallback,
 *   - a responsive `srcset` including the half-width derivative,
 *   - intrinsic `width`/`height` so the browser reserves space (no layout shift),
 *   - lazy loading and async decoding unless the image is above the fold.
 */
export function SmartImage({ name, alt, sizes = '100vw', className, priority = false }: SmartImageProps) {
  const media = image(name);
  const responsive = Boolean(media.small);
  const jpegSet = responsive ? `${media.small} 960w, ${media.src} ${media.width}w` : undefined;
  const webpSet =
    responsive && media.webp && media.webpSmall
      ? `${media.webpSmall} 960w, ${media.webp} ${media.width}w`
      : media.webp;

  return (
    <picture>
      {webpSet ? <source type="image/webp" srcSet={webpSet} sizes={responsive ? sizes : undefined} /> : null}
      <img
        className={className}
        src={media.src}
        srcSet={jpegSet}
        sizes={responsive ? sizes : undefined}
        width={media.width}
        height={media.height}
        alt={alt ?? media.alt}
        loading={priority ? 'eager' : 'lazy'}
        decoding={priority ? 'sync' : 'async'}
      />
    </picture>
  );
}

/* -------------------------------------------------------------------------- */
/* Motion                                                                     */
/* -------------------------------------------------------------------------- */

function prefersReducedMotion(): boolean {
  if (typeof window === 'undefined' || !window.matchMedia) return false;
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/** Fades and lifts its children the first time they scroll into view. */
export function Reveal({
  children,
  delay = 0,
  className,
}: {
  children: ReactNode;
  delay?: number;
  className?: string;
}) {
  const ref = useRef<HTMLDivElement | null>(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    if (prefersReducedMotion()) {
      setVisible(true);
      return;
    }
    const node = ref.current;
    if (!node) return;

    if (typeof IntersectionObserver === 'undefined') {
      setVisible(true);
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (entry.isIntersecting) {
            setVisible(true);
            observer.disconnect();
          }
        }
      },
      { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    observer.observe(node);
    return () => observer.disconnect();
  }, []);

  return (
    <div
      ref={ref}
      className={classNames('reveal', visible && 'is-visible', className)}
      style={delay ? { transitionDelay: `${delay}ms` } : undefined}
    >
      {children}
    </div>
  );
}

/* -------------------------------------------------------------------------- */
/* Headings & text                                                            */
/* -------------------------------------------------------------------------- */

export function SectionHeading({
  eyebrow,
  title,
  intro,
  align = 'start',
  level = 2,
  children,
}: {
  eyebrow?: string;
  title: string;
  intro?: string;
  align?: 'start' | 'center';
  level?: 1 | 2 | 3;
  children?: ReactNode;
}) {
  const Heading = (level === 1 ? 'h1' : level === 3 ? 'h3' : 'h2') as 'h1' | 'h2' | 'h3';
  return (
    <div className={classNames('section-head', align === 'center' && 'section-head--center')}>
      {eyebrow ? <p className="eyebrow">{eyebrow}</p> : null}
      <Heading>{title}</Heading>
      {intro ? <p className="section-head__intro">{intro}</p> : null}
      {children}
    </div>
  );
}

export function Stars({ rating, size = 15 }: { rating: number; size?: number }) {
  const rounded = Math.round(rating);
  return (
    <span className="testimonial-card__stars" aria-label={`${rating} out of 5`}>
      {Array.from({ length: 5 }, (_, index) => (
        <Icon key={index} name="star" size={size} />
      ))}
      <span className="visually-hidden">{rounded} of 5</span>
    </span>
  );
}

/* -------------------------------------------------------------------------- */
/* Breadcrumbs                                                                */
/* -------------------------------------------------------------------------- */

export interface Crumb {
  name: string;
  path: string;
}

export function Breadcrumbs({ items }: { items: Crumb[] }) {
  return (
    <nav className="breadcrumbs" aria-label="Breadcrumb">
      <ol>
        {items.map((item, index) => {
          const isLast = index === items.length - 1;
          return (
            <li key={`${item.path}-${item.name}`}>
              {isLast ? (
                <span aria-current="page">{item.name}</span>
              ) : (
                <Link to={item.path}>{item.name}</Link>
              )}
              {isLast ? null : (
                <span className="breadcrumbs__sep" aria-hidden="true">
                  /
                </span>
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}

/* -------------------------------------------------------------------------- */
/* Scroll helpers                                                             */
/* -------------------------------------------------------------------------- */

/** Resets scroll position on client-side navigation. */
export function ScrollToTop() {
  const { pathname } = useLocation();
  useEffect(() => {
    window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
  }, [pathname]);
  return null;
}

/** Small hook for "has the visitor scrolled past this point". */
export function useScrolledPast(threshold: number): boolean {
  const [passed, setPassed] = useState(false);
  useEffect(() => {
    const onScroll = () => setPassed(window.scrollY > threshold);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, [threshold]);
  return passed;
}

export function ToTop() {
  const visible = useScrolledPast(900);
  return (
    <button
      type="button"
      className={classNames('to-top', visible && 'is-visible')}
      onClick={() => window.scrollTo({ top: 0, behavior: prefersReducedMotion() ? 'auto' : 'smooth' })}
      aria-label="Back to top"
      aria-hidden={!visible}
      tabIndex={visible ? 0 : -1}
    >
      <Icon name="arrowUp" size={18} />
    </button>
  );
}

export function StickyMobileCta({ phone, phoneDisplay }: { phone: string; phoneDisplay: string }) {
  const visible = useScrolledPast(420);
  return (
    <div className={classNames('sticky-cta', visible && 'is-visible')} aria-hidden={!visible}>
      <a
        className="btn btn--secondary"
        href={`tel:${phone.replace(/[^\d]/g, '')}`}
        data-ga-event="phone_click"
        data-ga-label="Sticky mobile bar"
        tabIndex={visible ? 0 : -1}
      >
        <Icon name="phone" size={17} />
        {phoneDisplay}
      </a>
      <Link
        className="btn btn--primary"
        to="/get-a-quote"
        data-ga-event="quote_cta_click"
        data-ga-label="Sticky mobile bar"
        tabIndex={visible ? 0 : -1}
      >
        Get a quote
      </Link>
    </div>
  );
}

/* -------------------------------------------------------------------------- */
/* Misc                                                                       */
/* -------------------------------------------------------------------------- */

export function Badge({
  children,
  tone = 'default',
  className,
}: {
  children: ReactNode;
  tone?: 'default' | 'brand' | 'accent' | 'outline' | 'success' | 'warn' | 'danger' | 'info';
  className?: string;
}) {
  return (
    <span className={classNames('badge', tone !== 'default' && `badge--${tone}`, className)}>
      {children}
    </span>
  );
}

export function InlineLoader({ label = 'Loading…' }: { label?: string }) {
  return (
    <div className="loading-block">
      <span className="spinner spinner--ink" />
      <span>{label}</span>
    </div>
  );
}

export function EmptyState({
  title,
  text,
  children,
}: {
  title: string;
  text?: string;
  children?: ReactNode;
}) {
  return (
    <div className="empty-state">
      <span className="empty-state__icon">
        <Icon name="sparkles" size={22} />
      </span>
      <strong>{title}</strong>
      {text ? <p>{text}</p> : null}
      {children}
    </div>
  );
}
