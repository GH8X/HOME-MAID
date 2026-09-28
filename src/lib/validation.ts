/** Shared form validation. Used by both the UI (live) and the submit service. */

import type { ContactMessageInput, QuoteRequestInput } from './types';

export type FieldErrors<K extends string = string> = Partial<Record<K, string>>;

export function isEmail(value: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(value.trim());
}

/** North American friendly: 10+ digits once separators are stripped. */
export function isPhone(value: string): boolean {
  const digits = value.replace(/\D/g, '');
  return digits.length >= 10 && digits.length <= 15;
}

export function isText(value: string, min = 2, max = 120): boolean {
  const length = value.trim().length;
  return length >= min && length <= max;
}

/** Rejects anything before today (the office books forward, not backward). */
export function isTodayOrLater(value: string): boolean {
  if (!value) return false;
  const chosen = new Date(`${value}T00:00:00`);
  if (Number.isNaN(chosen.getTime())) return false;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return chosen.getTime() >= today.getTime();
}

export const QUOTE_STEPS = [
  { id: 'contact', label: 'Your details' },
  { id: 'property', label: 'Your property' },
  { id: 'service', label: 'Your clean' },
  { id: 'schedule', label: 'Schedule' },
  { id: 'extras', label: 'Finishing touches' },
] as const;

export type QuoteStepId = (typeof QUOTE_STEPS)[number]['id'];

export const PROPERTY_TYPES = [
  'Condo',
  'Apartment',
  'Townhouse',
  'House',
  'Office / commercial space',
] as const;

export const BEDROOM_OPTIONS = ['Studio', '1', '2', '3', '4', '5+'] as const;

export const BATHROOM_OPTIONS = ['1', '2', '3', '4+'] as const;

export const SIZE_OPTIONS = [
  'Under 500 sq ft',
  '500 – 750 sq ft',
  '750 – 1,000 sq ft',
  '1,000 – 1,500 sq ft',
  '1,500 – 2,000 sq ft',
  'Over 2,000 sq ft',
  'Not sure',
] as const;

export const ARRIVAL_WINDOWS = [
  'Morning — 8:00 am to 10:00 am',
  'Midday — 10:00 am to 12:00 pm',
  'Afternoon — 12:00 pm to 2:00 pm',
  'Late afternoon — 2:00 pm to 4:00 pm',
  'Flexible — any time between 9:00 am and 5:00 pm',
] as const;

export const ACCESS_METHODS = [
  'I will be home',
  'Key at concierge',
  'Smart key pad',
  'Lockbox and instructions',
  'Building manager will provide access',
] as const;

export const REFERRAL_SOURCES = [
  'Google search',
  'Referred by a friend',
  'Referred by a neighbour',
  'Social media',
  'Saw a Maid4Condos cleaning in my building',
  'Real estate agent or landlord',
  'Returning client',
  'Other',
] as const;

export const CONTACT_TOPICS = [
  'New cleaning quote',
  'Existing booking',
  'Recurring AutoPilot schedule',
  'Add an extra to my clean',
  'Billing question',
  'Feedback or a concern',
  'Commercial or property management',
  'Something else',
] as const;

export const EXTRAS = [
  { slug: 'germblasters', label: 'GermBlasters Disinfection' },
  { slug: 'inside-oven', label: 'Inside Oven' },
  { slug: 'inside-fridge', label: 'Inside Fridge' },
  { slug: 'inside-windows', label: 'Inside Windows' },
  { slug: 'vacuum-delivery', label: 'Vacuum Delivery' },
  { slug: 'laundry-load', label: 'Laundry Load' },
] as const;

export type ErrorMap = FieldErrors<keyof QuoteRequestInput>;

export const EMPTY_QUOTE: QuoteRequestInput = {
  full_name: '',
  email: '',
  phone: '',
  property_type: 'Condo',
  bedrooms: '1',
  bathrooms: '1',
  square_footage: '',
  service_slug: '',
  service_name: '',
  frequency: 'bi-weekly',
  extras: [],
  address: '',
  neighbourhood: '',
  postal_code: '',
  preferred_date: '',
  arrival_window: '',
  access_method: '',
  notes: '',
  referral_source: '',
};

const POSTAL = /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/;

export function validateQuoteStep(
  step: QuoteStepId,
  input: QuoteRequestInput,
): ErrorMap {
  const errors: ErrorMap = {};

  if (step === 'contact') {
    if (!isText(input.full_name, 2, 80)) errors.full_name = 'Please enter your full name.';
    if (!isEmail(input.email)) errors.email = 'Please enter a valid email address.';
    if (!isPhone(input.phone)) errors.phone = 'Please enter a phone number we can reach you on.';
  }

  if (step === 'property') {
    if (!input.property_type) errors.property_type = 'Please choose your property type.';
    if (!input.bedrooms) errors.bedrooms = 'Please choose the number of bedrooms.';
    if (!input.bathrooms) errors.bathrooms = 'Please choose the number of bathrooms.';
    if (!input.square_footage) errors.square_footage = 'Please choose an approximate size.';
    if (!isText(input.neighbourhood, 2, 80)) {
      errors.neighbourhood = 'Which neighbourhood or area is the property in?';
    }
    if (input.postal_code && !POSTAL.test(input.postal_code.trim())) {
      errors.postal_code = 'That does not look like a Canadian postal code.';
    }
  }

  if (step === 'service') {
    if (!input.service_slug) errors.service_slug = 'Please choose the cleaning you need.';
    if (!input.frequency) errors.frequency = 'Please choose how often you would like us.';
  }

  if (step === 'schedule') {
    if (!input.preferred_date) {
      errors.preferred_date = 'Please choose a preferred date.';
    } else if (!isTodayOrLater(input.preferred_date)) {
      errors.preferred_date = 'Please choose today or a future date.';
    }
    if (!input.arrival_window) errors.arrival_window = 'Please choose an arrival window.';
    if (!input.access_method) errors.access_method = 'Please tell us how we can access the property.';
    if (!isText(input.address, 4, 160)) {
      errors.address = 'Please enter the street address for the cleaning.';
    }
  }

  // Step 5 is free-form: no required fields beyond what earlier steps enforce.

  return errors;
}

export function validateQuote(input: QuoteRequestInput): ErrorMap {
  return QUOTE_STEPS.reduce<ErrorMap>(
    (errors, step) => ({ ...errors, ...validateQuoteStep(step.id, input) }),
    {},
  );
}

export function firstIncompleteStep(input: QuoteRequestInput): number {
  const index = QUOTE_STEPS.findIndex(
    (step) => Object.keys(validateQuoteStep(step.id, input)).length > 0,
  );
  return index === -1 ? QUOTE_STEPS.length - 1 : index;
}

export type ContactErrorMap = FieldErrors<keyof ContactMessageInput>;

export const EMPTY_CONTACT: ContactMessageInput = {
  full_name: '',
  email: '',
  phone: '',
  topic: '',
  message: '',
};

export function validateContact(input: ContactMessageInput): ContactErrorMap {
  const errors: ContactErrorMap = {};
  if (!isText(input.full_name, 2, 80)) errors.full_name = 'Please enter your name.';
  if (!isEmail(input.email)) errors.email = 'Please enter a valid email address.';
  if (input.phone && !isPhone(input.phone)) {
    errors.phone = 'Please enter a valid phone number, or leave it blank.';
  }
  if (!input.topic) errors.topic = 'Please choose a topic.';
  if (!isText(input.message, 10, 4000)) {
    errors.message = 'Please tell us a little more (at least 10 characters).';
  }
  return errors;
}

export function hasErrors(errors: Record<string, string | undefined>): boolean {
  return Object.values(errors).some(Boolean);
}
