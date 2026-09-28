# Maid4Condos — website

A complete rebuild of [maid4condos.com](https://www.maid4condos.com/) as a fast, mobile-first,
conversion-focused website for a Toronto condo cleaning company.

Built with **HTML5, CSS3, vanilla JavaScript, PHP 7.4+ and MySQL** — no frameworks, no build step,
no Composer dependencies. Upload it to any standard PHP host and it runs.

**Requirements:** PHP 7.4 or newer (developed and tested on PHP 8.1) with the `pdo_mysql`
extension, MySQL 5.7+ / MariaDB 10.3+, and `mod_rewrite` for clean URLs. `mbstring` is used when
present and falls back to single-byte handling when it is not. MySQL is optional — without it the
site serves the verified built-in content and simply cannot store quote requests.

---

## Quick start

### 1. Upload the files

Copy the whole project to your web root (or a subdirectory). The only folder that must be writable
is `/uploads` (and `/storage` if your host restricts session storage).

### 2. Point the site at your domain

Either edit `config/config.php` (`app.base_url`) or create `config/config.local.php`:

```php
<?php
return [
    'app' => ['base_url' => 'https://www.maid4condos.com'],
];
```

`base_url` also drives canonical URLs, Open Graph tags, structured data and the sitemap.
Leave it `null` and the site auto-detects the host.

### 3. Connect MySQL (optional but recommended)

```php
<?php
// config/config.local.php  — never commit this file
return [
    'app' => ['env' => 'production', 'debug' => false],
    'db'  => [
        'enabled' => true,
        'host'    => 'localhost',
        'name'    => 'your_database',
        'user'    => 'your_user',
        'pass'    => 'your_password',
    ],
];
```

Or set the environment variables `M4C_DB_HOST`, `M4C_DB_NAME`, `M4C_DB_USER`, `M4C_DB_PASS`.

Then open **`/database/install.php`** once. It will:

1. create the tables from `database/schema.sql`,
2. import the real Maid4Condos content (services, FAQs, testimonials, areas) into the database,
3. create your administrator account.

**Delete the `/database` folder when you are done.**

> The website works perfectly *without* a database. Every page falls back to the verified content in
> `includes/seed.php`, so nothing is ever blank or placeholder.

### 4. Sign in to the admin panel

`https://your-domain.com/admin`

The administrator system is completely separate from the public website — only rows in the `admins`
table can sign in, and passwords are stored with `password_hash()`. There is no public login or
signup anywhere on the site.

---

## What you can manage in the admin panel

| Screen | What it controls |
| --- | --- |
| **Overview** | Content counts, latest enquiries, SEO/analytics status, content import |
| **Quote requests** | Every submitted quote with status tracking, notes and CSV export |
| **Messages** | Contact form submissions |
| **Services** | Full CRUD: descriptions, checklists, pricing, imagery, SEO metadata, publish state |
| **Page sections** | Homepage hero / trust / why-us / how-it-works / final CTA and the About page |
| **FAQs** | Categories, questions, answers, ordering, publish state |
| **Testimonials** | Reviews, location, service, optional rating, publish state |
| **Service areas** | Neighbourhoods shown on the site and in structured data |
| **Add-ons** | Extras offered in the quote form |
| **Schedules** | AutoPilot frequencies and their discounts |
| **Navigation** | Header and footer links |
| **Media** | Upload images (JPG/PNG/WebP/SVG) and manage the library |
| **Settings & SEO** | Business details, meta defaults, contact info, social links, guarantees, GA4, form copy |
| **Administrators** | Owner-only account management |

Every list supports **add, edit, delete, publish and unpublish**.

---

## Tracking conversions with Google Analytics 4

1. Create a GA4 property and copy the measurement ID (`G-XXXXXXXXXX`).
2. Paste it into **Admin → Settings → Analytics → Google Analytics 4 measurement ID**.

Events that are reported automatically (nothing is hardcoded in the templates):

| Event | Fires when |
| --- | --- |
| `quote_form_submit` | The quote thank-you screen is shown |
| `contact_form_submit` | The contact form is submitted |
| `phone_click` | Any `tel:` link is clicked |
| `email_click` | Any `mailto:` link is clicked |
| `quote_cta_click` | A “Get a quote” call-to-action is clicked |
| `service_view` | A service detail page is viewed |
| `map_click` | A directions link is clicked |

---

## Transactional email

Quote requests and contact messages are emailed to the address in **Settings → Contact details**.

Two transports are supported (`Settings` or `config.local.php`):

* **`mail`** (default) — uses PHP `mail()`. Works on nearly every shared host, and is what this
  build ships with.
* **`elastic`** (optional upgrade) — Elastic Email v4 HTTP API for better deliverability,
  no Composer required. Not enabled by default; switch to it later if inbox placement needs it.

To enable Elastic Email later:

1. Create an account and generate an API key.
2. Set `M4C_MAIL_TRANSPORT=elastic` and `M4C_MAIL_API_KEY=your-key` in the server environment
   (or in `config/config.local.php`), and verify your sending domain with the provider.

Every enquiry is also saved to MySQL, so nothing is ever lost if email delivery fails.

---

## Project structure

```
/                        Public pages (extensionless URLs via .htaccess)
├── index.php            Homepage
├── about.php  faq.php  testimonials.php  contact.php  get-a-quote.php
├── privacy-policy.php  terms.php  image-credits.php  404.php  sitemap.php
├── services/
│   ├── index.php        /services
│   ├── _service.php     Shared controller for a single package
│   └── basic-cleaning/  basic-plus/  deep-cleaning/  deep-plus/
│       move-in-move-out/  recurring-cleaning/   → each with an index.php
├── admin/               Administrator panel (separate from the public site)
│   └── includes/        admin bootstrap, layout, CRUD engine
├── assets/
│   ├── css/main.css     Public design system
│   ├── css/admin.css    Admin design system
│   ├── js/main.js       Navigation, accordions, forms, analytics events
│   ├── js/admin.js      Admin interactions
│   └── images/          Photography + brand marks (see CREDITS.md)
├── components/          Reusable partials: header, footer, hero, service card,
│                        FAQ accordion, testimonial, CTA, forms, breadcrumbs, icons
├── config/              config.php, database.php (PDO), config.local.php (yours)
├── database/            schema.sql + one-time installer
├── includes/            bootstrap, helpers, security, auth, content repository,
│                        seeder, SEO, analytics, mailer, form handlers
├── pages/               Page views rendered by the controllers
├── storage/             Runtime state (form throttling). Not web accessible.
├── uploads/             Uploaded media. Script execution disabled.
├── robots.txt  sitemap.xml
└── .htaccess
```

---

## Security

* **Prepared statements everywhere** (PDO, `ATTR_EMULATE_PREPARES = false`) — no string-built SQL.
* **CSRF tokens** on every state-changing form, verified with `hash_equals()`.
* **Output escaping** through `e()` (`htmlspecialchars` with `ENT_QUOTES`).
* **Admin authentication** using `password_hash()` / `password_verify()`, session fingerprinting,
  idle timeout, `session_regenerate_id()` on sign-in, and login throttling.
* **Upload validation** — real MIME check via `finfo`, extension allow-list, size limit, minimum
  dimensions, SVG sanitiser, randomised filenames, and script execution disabled in `/uploads`.
* **Spam protection** — honeypot field, minimum submit time, per-bucket throttling.
* **Hardened headers** — Content Security Policy, `X-Content-Type-Options`, `X-Frame-Options`,
  `Referrer-Policy`, `Permissions-Policy`, HSTS on HTTPS.
* **Directory protection** — `/config`, `/includes`, `/pages`, `/components` and `/storage` are
  blocked at the web server level, as are `.sql`, `.md`, `.log` and `.ini` files.

---

## Performance & SEO

* Mobile-first CSS (~1 file, no framework), deferred vanilla JS (~1 file), zero libraries.
* Responsive `<picture>` output when AVIF/WebP siblings exist next to a JPEG — drop
  `cleaning-windows.avif` beside `cleaning-windows.jpg` and it is served automatically.
* `srcset`/`sizes`, explicit `width`/`height` (no layout shift), native lazy loading, one eager
  hero image with `fetchpriority="high"`.
* Browser caching and gzip via `.htaccess`, plus `preconnect` + non-blocking font loading.
* Per-page titles, meta descriptions, canonicals, Open Graph and Twitter cards.
* Structured data: `LocalBusiness` (address, area served, opening hours, offer catalogue),
  `Service`, `FAQPage`, `BreadcrumbList`.
* Dynamic `/sitemap.xml` generated from live content + `robots.txt`.

---

## Notes for whoever maintains this next

* **Legal copy**: `pages/privacy.php` and `pages/terms.php` are written from the policies published
  on the existing site. Have them reviewed before launch.
* **Photography**: the bundled images are licensed from Wikimedia Commons — attribution is on
  `/image-credits` (kept out of search results) and in `assets/images/CREDITS.md`. Replace them with
  your own team and client photos by dropping files into `assets/images/` using the same filenames,
  or upload through **Admin → Media**.
* **Content rule**: every fact on the site (services, checklists, prices, service areas, FAQs,
  testimonials, guarantees, insurance) comes from the published Maid4Condos website. Nothing was
  invented — please keep it that way when editing.
* Clean URLs need `mod_rewrite`. Without it, add the `.php` extension to the URL and everything
  still works.
