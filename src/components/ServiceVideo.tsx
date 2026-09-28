/**
 * The Maid4Condos service video.
 *
 * The player is not embedded until the visitor presses play, so the section
 * costs one image and no third-party JavaScript until it is wanted — no video
 * iframe, no Vimeo script and no cookies on page load.
 */

import { useState } from 'react';
import { Link } from 'react-router-dom';

import { image } from '../data/media';
import { SERVICE_VIDEO } from '../data/siteContent';
import { trackEvent } from '../lib/analytics';
import { useContent } from '../services/contentService';
import { Icon } from './Icon';
import { Reveal, SectionHeading, SmartImage } from './ui';

const HIGHLIGHTS = [
  'Master class trained, background-checked cleaners',
  'A comprehensive 30+ point checklist, checked twice',
  'Professional grade products that are biodegradable',
];

export function ServiceVideo() {
  const { settings } = useContent();
  const [playing, setPlaying] = useState(false);

  return (
    <section className="section" id="service-video">
      <div className="container container--wide">
        <div className="split split--media-first">
          <Reveal>
            <div className="video-frame">
              {playing ? (
                <iframe
                  className="video-frame__player"
                  src={`https://player.vimeo.com/video/${SERVICE_VIDEO.vimeoId}?autoplay=1&title=0&byline=0&portrait=0`}
                  title={SERVICE_VIDEO.title}
                  allow="autoplay; fullscreen; picture-in-picture"
                  allowFullScreen
                />
              ) : (
                <button
                  type="button"
                  className="video-frame__poster"
                  aria-label={`Play the video: ${SERVICE_VIDEO.title}`}
                  onClick={() => {
                    setPlaying(true);
                    trackEvent('video_play', { video: SERVICE_VIDEO.vimeoId });
                  }}
                >
                  <SmartImage
                    name={SERVICE_VIDEO.poster}
                    alt={image(SERVICE_VIDEO.poster).alt}
                    sizes="(min-width: 980px) 56vw, 100vw"
                  />
                  <span className="video-frame__scrim" aria-hidden="true" />
                  <span className="video-frame__play" aria-hidden="true">
                    <Icon name="play" size={26} />
                  </span>
                  <span className="video-frame__caption">
                    <span className="video-frame__caption-title">{SERVICE_VIDEO.title}</span>
                    <span className="video-frame__caption-note">Press play to watch</span>
                  </span>
                </button>
              )}
            </div>
          </Reveal>

          <div className="stack stack--lg">
            <SectionHeading
              eyebrow={SERVICE_VIDEO.eyebrow}
              title={settings.home_video_heading}
              intro={settings.home_video_intro}
            />

            <ul className="check-list">
              {HIGHLIGHTS.map((line) => (
                <li key={line}>
                  <Icon name="check" size={16} />
                  <span>{line}</span>
                </li>
              ))}
            </ul>

            <div className="hero__actions">
              <Link
                className="btn btn--primary btn--lg"
                to="/get-a-quote"
                data-ga-event="quote_cta_click"
                data-ga-label="Service video"
              >
                Book now
                <Icon name="arrow" size={18} />
              </Link>
              <a
                className="btn btn--secondary btn--lg"
                href={SERVICE_VIDEO.channelUrl}
                target="_blank"
                rel="noreferrer noopener"
              >
                More cleaning videos
                <Icon name="external" size={17} />
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
