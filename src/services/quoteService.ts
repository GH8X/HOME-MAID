/**
 * Quote and contact submissions.
 *
 * This site is deployed as static files, so there is no server to write to and
 * nothing is stored on the visitor's behalf. Instead of pretending a request
 * was filed, a submission is turned into a complete written summary that the
 * visitor can send to the office — the success screen offers a `mailto:` that
 * opens with everything already filled in, plus a copy button.
 *
 * If the owner connects a form service, set `VITE_FORM_ENDPOINT` and the same
 * submission is also posted there, so enquiries land in an inbox as well.
 */

import { SETTINGS } from '../data/settings';
import type {
  ContactMessageInput,
  PreparedEnquiry,
  QuoteRequestInput,
  SubmitResult,
} from '../lib/types';
import { validateContact, validateQuote } from '../lib/validation';

/** Optional POST endpoint from a form service. Blank = mailto handoff only. */
const FORM_ENDPOINT = (import.meta.env.VITE_FORM_ENDPOINT ?? '').trim();

export const hasFormEndpoint = FORM_ENDPOINT !== '';

/** Keeps the generated `mailto:` inside the limits older mail clients enforce. */
const MAILTO_BODY_LIMIT = 1800;

function detail(label: string, value: string): string | null {
  const trimmed = value.trim();
  return trimmed ? `${label}: ${trimmed}` : null;
}

function joinDetails(rows: Array<string | null>): string {
  return rows.filter((row): row is string => Boolean(row)).join('\n');
}

function buildEnquiry(subject: string, details: string): PreparedEnquiry {
  const body = `${details}\n\nSent from maid4condos.com`;
  const clipped =
    body.length > MAILTO_BODY_LIMIT
      ? `${body.slice(0, MAILTO_BODY_LIMIT - 40).replace(/\n[^\n]*$/, '')}\n\n(Full summary shown on the website.)`
      : body;

  return {
    body,
    summary: subject,
    mailto: `mailto:${SETTINGS.booking_email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(clipped)}`,
  };
}

/** Front-loads the catch-all so the most useful line is never clipped. */
export function quoteEnquiry(input: QuoteRequestInput): PreparedEnquiry {
  const rooms = joinDetails([
    detail('Bedrooms', input.bedrooms),
    detail('Bathrooms', input.bathrooms),
    detail('Approx. size', input.square_footage),
  ]).replace(/\n/g, ' · ');

  const scope = [
    detail('Property', input.property_type),
    rooms,
    detail('Frequency', input.frequency),
    detail('Add-ons', input.extras.join(', ')),
  ]
    .filter(Boolean)
    .join('\n');

  const where = joinDetails([
    detail('Address', input.address),
    detail('Neighbourhood', input.neighbourhood),
    detail('Postal code', input.postal_code),
  ]);

  const when = joinDetails([
    detail('Preferred date', input.preferred_date),
    detail('Arrival window', input.arrival_window),
    detail('Access', input.access_method),
  ]);

  const details = [
    `Name: ${input.full_name.trim()}`,
    detail('Email', input.email),
    detail('Phone', input.phone),
    '',
    `Cleaning requested: ${input.service_name || 'Not selected'}`,
    scope,
    where ? `\nWhere:\n${where}` : '',
    when ? `\nWhen:\n${when}` : '',
    input.notes.trim() ? `\nNotes: ${input.notes.trim()}` : '',
    detail('Heard about us via', input.referral_source),
  ].join('\n');

  return buildEnquiry(`Cleaning request — ${input.service_name || 'Maid4Condos'}`, details);
}

export function contactEnquiry(input: ContactMessageInput): PreparedEnquiry {
  const details = [
    `Name: ${input.full_name.trim()}`,
    detail('Email', input.email),
    detail('Phone', input.phone),
    detail('Topic', input.topic),
    '',
    input.message.trim(),
  ].join('\n');

  return buildEnquiry(`Website enquiry — ${input.topic || 'Maid4Condos'}`, details);
}

async function postToEndpoint(kind: string, payload: unknown): Promise<boolean> {
  if (!hasFormEndpoint) return false;
  try {
    const response = await fetch(FORM_ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ kind, ...(payload as Record<string, unknown>) }),
    });
    return response.ok;
  } catch {
    return false;
  }
}

/**
 * Validates a quote request and prepares it for the visitor to send.
 *
 * Returns the first validation error when the payload is incomplete, so the
 * form can still block a bad submission.
 */
export async function submitQuoteRequest(input: QuoteRequestInput): Promise<SubmitResult> {
  const errors = validateQuote(input);
  const first = Object.values(errors)[0];
  if (first) return { ok: false, message: first };

  await postToEndpoint('quote', input);
  return { ok: true };
}

export async function submitContactMessage(input: ContactMessageInput): Promise<SubmitResult> {
  const errors = validateContact(input);
  const first = Object.values(errors)[0];
  if (first) return { ok: false, message: first };

  await postToEndpoint('message', input);
  return { ok: true };
}
