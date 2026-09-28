/// <reference types="vite/client" />

/**
 * Environment variables.
 *
 * The site is static and needs no backend: with none of these set it still
 * builds and runs completely. Everything is optional.
 */
interface ImportMetaEnv {
  /** The origin used for canonical URLs, Open Graph tags and the sitemap. */
  readonly VITE_SITE_URL?: string;
  /** GA4 measurement ID. The same value can be set in `src/data/settings.ts`. */
  readonly VITE_GA4_MEASUREMENT_ID?: string;
  /**
   * Optional POST endpoint from a form service. When set, quote and contact
   * submissions are also delivered there.
   */
  readonly VITE_FORM_ENDPOINT?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
