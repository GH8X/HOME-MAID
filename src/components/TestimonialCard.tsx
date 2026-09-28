/** A single client review. Stars only render when a rating is published. */

import type { Testimonial } from '../lib/types';
import { Icon } from './Icon';
import { Stars } from './ui';

function initials(name: string): string {
  const parts = name.trim().split(/\s+/).slice(0, 2);
  return parts.map((part) => part.charAt(0).toUpperCase()).join('') || 'M';
}

export function TestimonialCard({ testimonial }: { testimonial: Testimonial }) {
  return (
    <figure className="testimonial-card">
      <span className="testimonial-card__mark" aria-hidden="true">
        <Icon name="quote" size={26} />
      </span>

      {testimonial.rating ? <Stars rating={testimonial.rating} /> : null}

      <blockquote className="testimonial-card__quote">“{testimonial.quote}”</blockquote>

      <figcaption className="testimonial-card__foot">
        <span className="testimonial-card__avatar" aria-hidden="true">
          {initials(testimonial.name)}
        </span>
        <span>
          <span className="testimonial-card__name">{testimonial.name}</span>
          <span className="testimonial-card__meta">
            {[testimonial.location, testimonial.service].filter(Boolean).join(' · ') ||
              'Maid4Condos client'}
          </span>
        </span>
      </figcaption>
    </figure>
  );
}
