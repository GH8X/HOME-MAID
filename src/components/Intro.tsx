/**
 * Cinematic opening sequence.
 *
 * A full-bleed intro that plays once per browsing session, before the visitor
 * ever sees the homepage. The page underneath is fully mounted and painted
 * from the first frame — the overlay only covers it — so the hero is already
 * loaded and there is nothing to wait for.
 *
 * Timeline (desktop, 3.4s):
 *   0.00  overlay paints, deep evergreen ground, soft bloom drifts in
 *   0.35  logo mark fades up and settles forward in 3D
 *   0.70  a light sweeps across the mark
 *   0.76  wordmark letters rise in sequence
 *   1.15  hairline rule draws outward, tagline fades
 *   2.10  hold — the mark drifts a little closer
 *   2.60  collapse: the overlay pulls apart from the centre
 *   3.40  gone, homepage staggers in behind it
 *
 * The overlay is CSS-only: no WebGL, no animation library, no extra bytes on
 * the critical path. All motion is transform/opacity/clip-path, so it stays
 * composited and smooth on mid-range phones.
 *
 * Accessibility: `prefers-reduced-motion` collapses the whole thing to a plain
 * 1.1s fade with the branding held still and visible, and the skip control is a
 * real focusable button rather than a synthetic click target.
 */

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useRef,
  useState,
  type ReactNode,
} from 'react';

import { BrandMark } from './ui';

/** sessionStorage, so a new tab or a new session replays the intro. */
const STORAGE_KEY = 'm4c.intro.v1';

type Phase =
  /** Never played this session, or already finished — the site is just a site. */
  | 'idle'
  /** Overlay is up, covering the homepage. */
  | 'playing'
  /** Overlay is collapsing; the homepage hero staggers in behind it. */
  | 'revealing';

interface IntroValue {
  phase: Phase;
}

const IntroContext = createContext<IntroValue>({ phase: 'idle' });

export function useIntro(): IntroValue {
  return useContext(IntroContext);
}

/* -------------------------------------------------------------------------- */
/* Environment probes                                                         */
/* -------------------------------------------------------------------------- */

function query(queryString: string): boolean {
  if (typeof window === 'undefined' || !window.matchMedia) return false;
  return window.matchMedia(queryString).matches;
}

function seenThisSession(): boolean {
  try {
    return window.sessionStorage.getItem(STORAGE_KEY) === '1';
  } catch {
    // Private-mode Safari and blocked storage: play it once rather than never.
    return false;
  }
}

function rememberThisSession(): void {
  try {
    window.sessionStorage.setItem(STORAGE_KEY, '1');
  } catch {
    /* nothing to do — worst case the intro replays on the next navigation */
  }
}

interface Tempo {
  /** How long the branding holds before the exit begins. */
  readonly hold: number;
  /** Overlay collapse. */
  readonly exit: number;
  /** Homepage stagger window after the overlay clears. */
  readonly reveal: number;
  /** Small screens: same story, trimmed. */
  readonly light: boolean;
}

function tempo(): Tempo {
  if (query('(prefers-reduced-motion: reduce)')) {
    // No choreography, no light sweep, no 3D — just an elegant fade.
    return { hold: 700, exit: 400, reveal: 0, light: false };
  }
  if (query('(max-width: 767px), (max-height: 620px)')) {
    return { hold: 2050, exit: 560, reveal: 700, light: false };
  }
  return { hold: 2600, exit: 800, reveal: 900, light: true };
}

/* -------------------------------------------------------------------------- */
/* Provider                                                                   */
/* -------------------------------------------------------------------------- */

export function IntroProvider({ children }: { children: ReactNode }) {
  // Read once, purely. The flag is written in an effect, so React's StrictMode
  // double-invoke can't turn the intro off on the second pass.
  const [phase, setPhase] = useState<Phase>(() => (seenThisSession() ? 'idle' : 'playing'));
  const [leaving, setLeaving] = useState(false);
  const timers = useRef<number[]>([]);

  const clearTimers = useCallback(() => {
    timers.current.forEach((id) => window.clearTimeout(id));
    timers.current = [];
  }, []);

  /** Collapse the overlay, then hand the page over to its entrance animation. */
  const finish = useCallback(() => {
    setLeaving(true);
    const { exit, reveal } = tempo();
    timers.current.push(
      window.setTimeout(() => {
        setPhase('revealing');
      }, exit),
      window.setTimeout(() => {
        setPhase('idle');
      }, exit + reveal),
    );
  }, []);

  // Arm the autoplay timer. Re-runs only if the phase resets to 'playing'.
  useEffect(() => {
    if (phase !== 'playing') return;

    rememberThisSession();
    const { hold } = tempo();
    timers.current.push(window.setTimeout(finish, hold));

    return clearTimers;
  }, [phase, finish, clearTimers]);

  // The page behind the overlay is a normal, scrollable document; freezing it
  // keeps the first gesture from landing on a page the visitor has not seen.
  // Keyed on the boolean, not the phase, so the lock survives the handover from
  // 'playing' to 'revealing' without a restore-then-refreeze flicker.
  const locked = phase !== 'idle';
  useEffect(() => {
    if (!locked) return;
    const { body, documentElement } = document;
    const scrollY = window.scrollY;
    body.style.position = 'fixed';
    body.style.top = `-${scrollY}px`;
    body.style.width = '100%';
    documentElement.style.overflow = 'hidden';

    // The page behind the overlay is fully rendered, so without this a keyboard
    // user could tab into links they have not seen yet. `inert` drops the
    // subtree out of the tab order while leaving the skip control reachable,
    // because the overlay is a sibling of `.app-shell`, not a child of it.
    const shell = document.querySelector('.app-shell');
    shell?.setAttribute('inert', '');

    return () => {
      body.style.position = '';
      body.style.top = '';
      body.style.width = '';
      documentElement.style.overflow = '';
      shell?.removeAttribute('inert');
      window.scrollTo(0, scrollY);
    };
  }, [locked]);

  useEffect(() => clearTimers, [clearTimers]);

  return (
    <IntroContext.Provider value={{ phase }}>
      {children}
      {phase !== 'idle' ? <IntroOverlay leaving={leaving} onSkip={finish} /> : null}
    </IntroContext.Provider>
  );
}

/* -------------------------------------------------------------------------- */
/* Overlay                                                                    */
/* -------------------------------------------------------------------------- */

function IntroOverlay({ leaving, onSkip }: { leaving: boolean; onSkip: () => void }) {
  const reduced = query('(prefers-reduced-motion: reduce)');
  const { light } = tempo();

  return (
    <div        className={['intro', leaving ? 'intro--leaving' : '', light ? 'intro--light' : '']
        .filter(Boolean)
        .join(' ')}
      data-testid="intro-overlay"
    >
      <div className="intro__bloom intro__bloom--a" aria-hidden="true" />
      <div className="intro__bloom intro__bloom--b" aria-hidden="true" />
      <div className="intro__vignette" aria-hidden="true" />

      {/* The wordmark is decorative here: the page underneath owns the only
          <h1>, and the header already announces the brand. Announcing it twice
          would give assistive tech — and crawlers — a second page heading. */}
      <div className="intro__stage" aria-hidden="true">
        <div className="intro__mark">
          <span className="intro__mark-glow" aria-hidden="true" />
          <BrandMark size={96} className="intro__mark-svg" />
          <span className="intro__sweep" aria-hidden="true" />
        </div>

        <p className="intro__word">
          {'Maid4Condos'.split('').map((letter, index) => (
            <span
              className="intro__letter"
              key={`${letter}-${index}`}
              style={{ ['--i' as string]: index }}
            >
              {letter}
            </span>
          ))}
        </p>

        <span className="intro__rule" aria-hidden="true" />
        <p className="intro__tag">Condo cleaning, Toronto</p>
      </div>

      <button type="button" className="intro__skip" onClick={onSkip}>
        {reduced ? 'Continue' : 'Skip'}
        <span className="intro__skip-line" aria-hidden="true" />
      </button>
    </div>
  );
}
