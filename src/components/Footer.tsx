/** Site footer — service list, navigation, contact details, areas and legal. */

import { Link } from 'react-router-dom';

import { PRIMARY_NAV, SECONDARY_NAV } from '../data/siteContent';
import { telHref } from '../lib/format';
import { useContent } from '../services/contentService';
import { BrandMark } from './ui';
import { Icon } from './Icon';

export function Footer() {
  const { settings, live } = useContent();
  const year = new Date().getFullYear();

  const socials = [
    { label: 'Facebook', url: settings.social_facebook },
    { label: 'Instagram', url: settings.social_instagram },
    { label: 'X', url: settings.social_twitter },
  ].filter((entry) => Boolean(entry.url));

  return (
    <footer className="site-footer">
      <div className="container container--wide">
        <div className="footer__grid">
          <div className="footer-col footer-col--brand">
            <Link to="/" className="footer__brand">
              <BrandMark size={42} className="brand__mark" />
              <span className="footer__brand-name">Maid4Condos</span>
            </Link>
            <p className="footer__about">{settings.site_description}</p>
            <ul className="footer__list">
              <li className="footer__contact-item">
                <Icon name="badge" size={16} />
                <span>
                  In business since 2014 · Family run · Bonded, WSIB covered, with{' '}
                  {settings.liability_coverage} in liability coverage
                </span>
              </li>
            </ul>
            {socials.length ? (
              <div className="footer__social">
                {socials.map((entry) => (
                  <a
                    key={entry.label}
                    className="social-link"
                    href={entry.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label={`Maid4Condos on ${entry.label}`}
                  >
                    <Icon name="external" size={17} />
                  </a>
                ))}
              </div>
            ) : null}
          </div>

          <div className="footer-col">
            <h2 className="footer__heading">Cleaning packages</h2>
            <ul className="footer__list">
              {live.services.map((service) => (
                <li key={service.slug}>
                  <Link to={`/services/${service.slug}`}>{service.name}</Link>
                </li>
              ))}
              <li>
                <Link to="/get-a-quote">Get a quote</Link>
              </li>
            </ul>
          </div>

          <div className="footer-col">
            <h2 className="footer__heading">Company</h2>
            <ul className="footer__list">
              {PRIMARY_NAV.map((item) => (
                <li key={item.to}>
                  <Link to={item.to}>{item.label}</Link>
                </li>
              ))}
              {SECONDARY_NAV.map((item) => (
                <li key={item.to}>
                  <Link to={item.to}>{item.label}</Link>
                </li>
              ))}
            </ul>
          </div>

          <div className="footer-col">
            <h2 className="footer__heading">Contact</h2>
            <ul className="footer__list">
              <li className="footer__contact-item">
                <Icon name="phone" size={16} />
                <a
                  href={telHref(settings.phone)}
                  data-ga-event="phone_click"
                  data-ga-label="Footer"
                >
                  {settings.phone_display}
                </a>
              </li>
              <li className="footer__contact-item">
                <Icon name="mail" size={16} />
                <a
                  href={`mailto:${settings.email}`}
                  data-ga-event="email_click"
                  data-ga-label="Footer"
                >
                  {settings.email}
                </a>
              </li>
              <li className="footer__contact-item">
                <Icon name="pin" size={16} />
                <span>
                  {settings.address_street}
                  <br />
                  {settings.address_city}, {settings.address_region} {settings.address_postal}
                  <br />
                  {settings.address_country}
                </span>
              </li>
              <li className="footer__contact-item">
                <Icon name="clock" size={16} />
                <span>
                  {settings.office_hours.split('\n').map((line) => (
                    <span key={line}>
                      {line}
                      <br />
                    </span>
                  ))}
                </span>
              </li>
            </ul>
          </div>
        </div>

        <div className="footer__grid footer__grid--areas">
          <div className="footer-col footer__areas-block">
            <h2 className="footer__heading">Service areas</h2>
            <ul className="footer__areas">
              {live.areas.map((area) => (
                <li key={area.name}>{area.name}</li>
              ))}
            </ul>
          </div>
        </div>

        <div className="footer__bottom">
          <span>
            © {year} {settings.site_legal_name}. All rights reserved.
          </span>
          <ul className="footer__legal">
            {SECONDARY_NAV.map((item) => (
              <li key={item.to}>
                <Link to={item.to}>{item.label}</Link>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </footer>
  );
}
