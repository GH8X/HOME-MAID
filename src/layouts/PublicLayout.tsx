/** Public site shell: header, routed content, footer and mobile affordances. */

import { useEffect } from 'react';
import { Outlet, useLocation } from 'react-router-dom';

import { Footer } from '../components/Footer';
import { Header } from '../components/Header';
import { IntroProvider, useIntro } from '../components/Intro';
import { StickyMobileCta, ToTop } from '../components/ui';
import { trackPageView } from '../lib/analytics';
import { classNames } from '../lib/format';
import { useContent } from '../services/contentService';

/**
 * The intro gate lives inside the layout rather than the app root, so a brand
 * sequence can never sit on top of anything outside the public site.
 */
export function PublicLayout() {
  return (
    <IntroProvider>
      <PublicShell />
    </IntroProvider>
  );
}

function PublicShell() {
  const { settings } = useContent();
  const { pathname } = useLocation();
  // Drives the staggered hero entrance for the one beat after the overlay lifts.
  const { phase } = useIntro();

  // GA4 needs the page view sent manually for client-side navigations.
  useEffect(() => {
    const id = window.setTimeout(() => trackPageView(pathname), 120);
    return () => window.clearTimeout(id);
  }, [pathname]);

  return (
    <div
      className={classNames(
        'app-shell',
        phase === 'playing' && 'app-shell--introing',
        phase === 'revealing' && 'app-shell--revealing',
      )}
    >
      <a className="skip-link" href="#main">
        Skip to content
      </a>
      <Header />
      <main className="app-main" id="main">
        <Outlet />
      </main>
      <Footer />
      <StickyMobileCta
        phone={settings.phone}
        phoneDisplay={settings.phone_display || settings.phone}
      />
      <ToTop />
    </div>
  );
}
