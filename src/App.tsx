/**
 * Route table.
 *
 * Every page is lazily loaded so the initial bundle stays small — the homepage
 * never pays for the FAQ, and the FAQ never pays for the quote form. The whole
 * tree sits inside the content provider, which publishes the local data modules.
 */

import { lazy, Suspense } from 'react';
import { Route, Routes } from 'react-router-dom';

import { InlineLoader, ScrollToTop } from './components/ui';
import { PublicLayout } from './layouts/PublicLayout';
import { ContentProvider } from './services/contentService';

const Home = lazy(() => import('./pages/Home').then((module) => ({ default: module.Home })));
const Services = lazy(() =>
  import('./pages/Services').then((module) => ({ default: module.Services })),
);
const ServiceDetail = lazy(() =>
  import('./pages/ServiceDetail').then((module) => ({ default: module.ServiceDetail })),
);
const About = lazy(() => import('./pages/About').then((module) => ({ default: module.About })));
const Faq = lazy(() => import('./pages/Faq').then((module) => ({ default: module.Faq })));
const Testimonials = lazy(() =>
  import('./pages/Testimonials').then((module) => ({ default: module.Testimonials })),
);
const Contact = lazy(() =>
  import('./pages/Contact').then((module) => ({ default: module.Contact })),
);
const GetAQuote = lazy(() =>
  import('./pages/GetAQuote').then((module) => ({ default: module.GetAQuote })),
);
const Privacy = lazy(() =>
  import('./pages/Privacy').then((module) => ({ default: module.Privacy })),
);
const Terms = lazy(() => import('./pages/Terms').then((module) => ({ default: module.Terms })));
const ImageCredits = lazy(() =>
  import('./pages/ImageCredits').then((module) => ({ default: module.ImageCredits })),
);
const NotFound = lazy(() =>
  import('./pages/NotFound').then((module) => ({ default: module.NotFound })),
);

function RouteFallback() {
  return (
    <div className="container section">
      <InlineLoader label="Loading…" />
    </div>
  );
}

export default function App() {
  return (
    <ContentProvider>
      <ScrollToTop />
      <Suspense fallback={<RouteFallback />}>
        <Routes>
          <Route element={<PublicLayout />}>
            <Route index element={<Home />} />
            <Route path="services" element={<Services />} />
            <Route path="services/:slug" element={<ServiceDetail />} />
            <Route path="about" element={<About />} />
            <Route path="faq" element={<Faq />} />
            <Route path="testimonials" element={<Testimonials />} />
            <Route path="contact" element={<Contact />} />
            <Route path="get-a-quote" element={<GetAQuote />} />
            <Route path="privacy-policy" element={<Privacy />} />
            <Route path="terms" element={<Terms />} />
            <Route path="image-credits" element={<ImageCredits />} />
            <Route path="*" element={<NotFound />} />
          </Route>
        </Routes>
      </Suspense>
    </ContentProvider>
  );
}
