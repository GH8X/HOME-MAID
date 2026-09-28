# Maid4Condos

A new website for **Maid4Condos**, a family run condo cleaning company in
Liberty Village, Toronto, in business since 2014.

Built with **Vite + React + TypeScript**. It is a plain static site: there is no
database, no server and no build-time service to configure. `npm run build`
produces `dist/`, and that folder is the whole website.

## Commands

```bash
npm install
npm run dev        # local dev server
npm run build      # typecheck + production build into dist/
npm run preview    # serve the built output
npm run typecheck  # tsc -b --noEmit
```

## Routes

| Route | Page |
| --- | --- |
| `/` | Homepage |
| `/services` | All six cleaning packages, comparison table, add-ons, FAQs |
| `/services/basic-cleaning` | Basic Clean |
| `/services/basic-plus` | Basic Plus |
| `/services/deep-cleaning` | Deep Clean |
| `/services/deep-plus` | Deep Plus |
| `/services/move-in-move-out` | Move In / Move Out |
| `/services/recurring-cleaning` | Recurring Cleaning (AutoPilot) |
| `/about` | About, our promise, credentials, what we do not do |
| `/faq` | Every published question, by category |
| `/testimonials` | Real client reviews, People Like Us, make a review |
| `/contact` | Phone, email, map, service areas, important customer information |
| `/get-a-quote` | Five-step booking form |
| `/privacy-policy`, `/terms` | Legal |
| `/image-credits` | Photography licences |

## Where the content lives

Everything the site displays is a typed module in `src/data`. Editing one of
these files is the only way to change the website, and nothing else needs to
know:

| File | Holds |
| --- | --- |
| `src/data/settings.ts` | Business facts, contact details, opening hours, review counts, social profiles, every homepage headline and paragraph |
| `src/data/services.ts` | The six packages (checklists, benefits, FAQs), the add-ons and the AutoPilot frequencies |
| `src/data/faqs.ts` | All published FAQs and their categories |
| `src/data/testimonials.ts` | Client reviews |
| `src/data/peopleLikeUs.ts` | Recognition wall, review destinations, survey link |
| `src/data/siteContent.ts` | Navigation, service areas, the service video, important customer information |
| `src/data/pages.ts` | About story plus the privacy and terms copy |
| `src/data/media.ts` | The photography manifest — sizes, alt text, credits |

Components read it all through `useContent()` (`src/services/contentService.tsx`),
so a headline, a package or an area is edited in exactly one place.

### No prices

The public site **never displays a price**. Where the old site showed one, the
site now shows **BOOK NOW**, and pricing is confirmed with the office against
the size and condition of the home. There is no `priceRange` or `offers`
structured data either — structured data must never claim more than the page.

## The opening experience

On a fresh browsing session the site opens with a full-screen brand sequence:
the Maid4Condos mark fades up in CSS 3D, a light sweeps across it, the wordmark
rises letter by letter, and the overlay then pulls apart like a curtain to
reveal the homepage, which has been mounted and painted behind it the whole
time. It is roughly 3.4 seconds on desktop and shorter on mobile.

- Pure CSS 3D (`perspective` + `rotateX` + `translateZ`) — no WebGL, no extra dependency
- `sessionStorage` flag, so it plays once per session and never on navigation
- A discreet **Skip** button
- `prefers-reduced-motion` collapses it to a short plain fade
- The page behind is `inert` while the overlay is up, so nothing is tabbable early
- The wordmark is a `<p>` inside an `aria-hidden` container, so every page still has exactly one `<h1>`

## Forms

`/get-a-quote` is a five-step wizard (details → property → cleaning → schedule →
finishing touches) with live validation and a running summary. Both it and the
contact form work out of the box, with no service to configure.

**How a submission works.** The site is static, so nothing is stored and nothing
is emailed on the visitor's behalf. A successful submission is turned into a
complete written summary which the visitor sends:

- **Send your request** opens their mail client with every detail already written out (`mailto:` to the bookings inbox)
- **Copy the summary** puts the same text on the clipboard
- The full text is also shown under *Read your request*, so it can never be lost
- The phone number and email are one tap away

**Optional form service.** To have submissions land in an inbox automatically,
set `VITE_FORM_ENDPOINT` to a form-endpoint URL (Formspree, Web3Forms,
Formspark and similar). The same payload is then POSTed as JSON alongside the
`mailto:` handoff. No code change is required.

## Analytics

Google Analytics 4 is wired up but entirely optional. Set the measurement ID in
`src/data/settings.ts` (`ga4_measurement_id`) or via `VITE_GA4_MEASUREMENT_ID`.
A blank ID loads no tag at all — no script, no cookies.

Events: `quote_form_start`, `quote_form_step`, `quote_form_submit`,
`contact_form_submit`, `enquiry_send`, `enquiry_copy`, `phone_click`,
`email_click`, `quote_cta_click`, `service_view`, `service_card_click`,
`video_play`, `review_click`, `recognition_click`, `map_click`.

## SEO

Every page sets a unique title, meta description, canonical URL, Open Graph and
Twitter tags through `src/hooks/useSeo.ts`, and emits its own JSON-LD:
`LocalBusiness` + `CleaningService` + `HomeAndConstructionBusiness`,
`Service`, `FAQPage` and `BreadcrumbList`. `public/robots.txt` and
`public/sitemap.xml` are part of the build. There is deliberately no
`aggregateRating` — the company publishes review counts, not an average score.

## Performance

- Every route is lazily loaded, so the homepage never pays for the FAQs and vice versa
- Vendor code is split into its own chunk so it caches independently of content
- Every photograph ships as **WebP first** through `<picture>`, with the JPEG as
  the fallback (roughly 40% smaller payload)
- The service video is a poster image until it is pressed; no third-party player
  loads on page load
- Intrinsic `width`/`height` on every image, so nothing shifts as it loads
- Four runtime dependencies: `react`, `react-dom`, `react-router-dom` and nothing else

## Accessibility

Skip link, visible focus rings, keyboard-trapped mobile drawer, `aria-current`
on navigation, real `<button>` elements for the video and accordions, alt text
on every photograph, and a full `prefers-reduced-motion` path through the intro
and the reveals.

## Photography

Every photograph is used under a licence recorded in `src/data/media.ts` and
listed on `/image-credits`. Replace one by dropping a file into
`public/images/`, adding its WebP twin, and updating the manifest entry.

## Content honesty

Everything published here comes from Maid4Condos. No review, award, statistic,
certification, guarantee or claim was invented, review counts are the counts the
business displays, and each recognition badge links to the real profile behind
it. Policy amounts (a cancellation fee, a referral credit) are written in words
rather than as figures, so no price appears anywhere on the public site.

## Deployment

Static hosting. Install command `npm install`, build command `npm run build`,
output `dist/`.
