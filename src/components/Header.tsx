/**
 * Sticky site header.
 *
 * Desktop: brand, primary navigation (with a services mega menu), a phone link
 * and the GET A QUOTE call to action. Mobile: brand, menu button and the quote
 * button. The drawer traps focus, closes on Escape and locks body scroll.
 */

import { useEffect, useId, useRef, useState } from 'react';
import { Link, NavLink, useLocation } from 'react-router-dom';

import { PRIMARY_NAV } from '../data/siteContent';
import { classNames, telHref } from '../lib/format';
import { useContent } from '../services/contentService';
import { Brand, useScrolledPast } from './ui';
import { Icon } from './Icon';

export function Header() {
  const { settings, live } = useContent();
  const location = useLocation();
  const stuck = useScrolledPast(10);

  const [drawerOpen, setDrawerOpen] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);

  const drawerRef = useRef<HTMLDivElement | null>(null);
  const closeRef = useRef<HTMLButtonElement | null>(null);
  const panelId = useId();

  const phoneDisplay = settings.phone_display || settings.phone;
  const services = live.services;

  // Close everything on navigation.
  useEffect(() => {
    setDrawerOpen(false);
    setMenuOpen(false);
  }, [location.pathname]);

  // Escape closes the drawer; body scroll is locked while it is open.
  useEffect(() => {
    if (!drawerOpen) return;

    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        setDrawerOpen(false);
        return;
      }
      if (event.key !== 'Tab') return;

      const focusables = drawerRef.current?.querySelectorAll<HTMLElement>(
        'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])',
      );
      if (!focusables || focusables.length === 0) return;
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    };

    document.addEventListener('keydown', onKeyDown);
    document.body.classList.add('is-locked');
    closeRef.current?.focus();

    return () => {
      document.removeEventListener('keydown', onKeyDown);
      document.body.classList.remove('is-locked');
    };
  }, [drawerOpen]);

  return (
    <>
      <div className="topbar">
        <div className="container container--wide topbar__inner">
          <div className="topbar__group">
            <a
              className="topbar__link"
              href={telHref(settings.phone)}
              data-ga-event="phone_click"
              data-ga-label="Header top bar"
            >
              <Icon name="phone" size={15} />
              {phoneDisplay}
            </a>
            <a
              className="topbar__link"
              href={`mailto:${settings.email}`}
              data-ga-event="email_click"
              data-ga-label="Header top bar"
            >
              <Icon name="mail" size={15} />
              {settings.email}
            </a>
          </div>
          <div className="topbar__group">
            <span className="topbar__hours">
              <Icon name="clock" size={15} />
              Residential cleanings Mon – Fri, 8:00 am – 6:00 pm
            </span>
          </div>
        </div>
      </div>

      <header className={classNames('site-header', stuck && 'is-stuck')}>
        <div className="container container--wide site-header__inner">
          <Link to="/" className="brand" aria-label="Maid4Condos home">
            <Brand />
          </Link>

          <nav className="nav" aria-label="Primary">
            <ul className="nav__list">
              <li
                className={classNames('nav__item', menuOpen && 'is-open')}
                onMouseEnter={() => setMenuOpen(true)}
                onMouseLeave={() => setMenuOpen(false)}
              >
                <NavLink
                  to="/services"
                  className={({ isActive }) =>
                    classNames('nav__link', (isActive || location.pathname.startsWith('/services')) && 'is-active')
                  }
                  aria-expanded={menuOpen}
                  aria-controls={panelId}
                  onFocus={() => setMenuOpen(true)}
                >
                  Services
                  <Icon name="chevron" size={15} className="nav__caret" />
                </NavLink>

                <div className="nav__panel" id={panelId}>
                  <div className="nav__panel-grid">
                    {services.slice(0, 6).map((service) => (
                      <Link
                        key={service.slug}
                        to={`/services/${service.slug}`}
                        className="nav__panel-link"
                      >
                        <span className="nav__panel-name">{service.name}</span>
                        <span className="nav__panel-desc">{service.tagline}</span>
                      </Link>
                    ))}
                  </div>
                  <div className="nav__panel-foot">
                    <Link to="/services">Compare every package</Link>
                    <Link
                      to="/get-a-quote"
                      data-ga-event="quote_cta_click"
                      data-ga-label="Services menu"
                    >
                      Get a quote →
                    </Link>
                  </div>
                </div>
              </li>

              {PRIMARY_NAV.filter((item) => item.label !== 'Services').map((item) => (
                <li className="nav__item" key={item.to}>
                  <NavLink
                    to={item.to}
                    className={({ isActive }) => classNames('nav__link', isActive && 'is-active')}
                  >
                    {item.label}
                  </NavLink>
                </li>
              ))}
            </ul>
          </nav>

          <div className="site-header__actions">
            <a
              className="header-phone"
              href={telHref(settings.phone)}
              data-ga-event="phone_click"
              data-ga-label="Header"
            >
              <Icon name="phone" size={16} />
              {phoneDisplay}
            </a>

            <Link
              className="btn btn--primary header-quote"
              to="/get-a-quote"
              data-ga-event="quote_cta_click"
              data-ga-label="Header"
            >
              <span className="header-quote__full">Get a quote</span>
              <span className="header-quote__short">Quote</span>
            </Link>

            <button
              type="button"
              className="nav-toggle"
              aria-expanded={drawerOpen}
              aria-controls="mobile-drawer"
              onClick={() => setDrawerOpen(true)}
            >
              <span className="nav-toggle__bars" aria-hidden="true">
                <span />
                <span />
                <span />
              </span>
              <span className="nav-toggle__label">Menu</span>
            </button>
          </div>
        </div>
      </header>

      <div
        className={classNames('scrim', drawerOpen && 'is-open')}
        onClick={() => setDrawerOpen(false)}
        aria-hidden="true"
      />

      <div
        id="mobile-drawer"
        className={classNames('drawer', drawerOpen && 'is-open')}
        ref={drawerRef}
        role="dialog"
        aria-modal="true"
        aria-label="Site menu"
        aria-hidden={!drawerOpen}
      >
        <div className="drawer__head">
          <Link to="/" className="brand" tabIndex={drawerOpen ? 0 : -1}>
            <Brand size={38} showTagline={false} />
          </Link>
          <button
            type="button"
            className="drawer__close"
            onClick={() => setDrawerOpen(false)}
            ref={closeRef}
            aria-label="Close menu"
            tabIndex={drawerOpen ? 0 : -1}
          >
            <Icon name="close" size={18} />
          </button>
        </div>

        <div className="drawer__body">
          <div>
            <p className="drawer__group-title">Cleaning packages</p>
            <ul className="drawer__list">
              {services.map((service) => (
                <li key={service.slug}>
                  <Link className="drawer__link" to={`/services/${service.slug}`} tabIndex={drawerOpen ? 0 : -1}>
                    {service.name}
                    <Icon name="arrow" size={16} />
                  </Link>
                </li>
              ))}
              <li>
                <Link className="drawer__link" to="/services" tabIndex={drawerOpen ? 0 : -1}>
                  Compare all packages
                  <Icon name="arrow" size={16} />
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <p className="drawer__group-title">Company</p>
            <ul className="drawer__list">
              {PRIMARY_NAV.filter((item) => item.label !== 'Services').map((item) => (
                <li key={item.to}>
                  <Link className="drawer__link" to={item.to} tabIndex={drawerOpen ? 0 : -1}>
                    {item.label}
                    <Icon name="arrow" size={16} />
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <p className="drawer__group-title">Talk to us</p>
            <div className="drawer__contact">
              <a
                className="drawer__link drawer__link--contact"
                href={telHref(settings.phone)}
                data-ga-event="phone_click"
                data-ga-label="Mobile drawer"
                tabIndex={drawerOpen ? 0 : -1}
              >
                <Icon name="phone" size={16} />
                {phoneDisplay}
              </a>
              <a
                className="drawer__link drawer__link--contact"
                href={`mailto:${settings.email}`}
                data-ga-event="email_click"
                data-ga-label="Mobile drawer"
                tabIndex={drawerOpen ? 0 : -1}
              >
                <Icon name="mail" size={16} />
                {settings.email}
              </a>
            </div>
          </div>
        </div>

        <div className="drawer__foot">
          <Link className="btn btn--primary btn--block" to="/get-a-quote" tabIndex={drawerOpen ? 0 : -1}>
            Get a quote
          </Link>
          <Link className="btn btn--secondary btn--block" to="/contact" tabIndex={drawerOpen ? 0 : -1}>
            Contact the office
          </Link>
        </div>
      </div>
    </>
  );
}
