/**
 * Photography manifest.
 *
 * Every photo is used under the licence recorded below (surfaced on
 * `/image-credits`). Only real photographs with a verifiable licence are
 * listed — no placeholder art.
 *
 * `width`/`height` are the intrinsic pixel sizes, passed to `<img>` so the
 * browser can reserve space and avoid layout shift.
 */

export interface PhotoCredit {
  title: string;
  author: string;
  license: string;
  source: string;
}

export interface MediaImage {
  /** Image slug, e.g. `cleaned-living-room`. */
  name: string;
  src: string;
  /** Half-width derivative for `srcset`, when one exists. */
  small?: string;
  /** WebP derivative, served first; the JPEG above stays as the fallback. */
  webp?: string;
  webpSmall?: string;
  width: number;
  height: number;
  alt: string;
  /** Absent when a photograph comes from outside the manifest. */
  credit?: PhotoCredit;
}

/** `/images/hero.jpg` → `/images/hero.webp` */
function toWebp(path: string): string {
  return path.replace(/\.(jpe?g|png)$/i, '.webp');
}

function withWebp(entry: MediaImage): MediaImage {
  return {
    ...entry,
    webp: toWebp(entry.src),
    webpSmall: entry.small ? toWebp(entry.small) : undefined,
  };
}

/** Shixart1985's cleaning photography is published under CC BY 2.0. */
const shixart = (title: string, source: string): PhotoCredit => ({
  title,
  author: 'Shixart1985',
  license: 'CC BY 2.0',
  source,
});

const RAW_IMAGES: Record<string, MediaImage> = {
  'hero-cleaning-modern-home': {
    name: 'hero-cleaning-modern-home',
    src: '/images/hero-cleaning-modern-home.jpg',
    small: '/images/hero-cleaning-modern-home-900.jpg',
    width: 1920,
    height: 1280,
    alt: 'A professional cleaner vacuuming the floor of a bright, modern Toronto condo living room',
    credit: shixart(
      'Person cleaning living room floor with vacuum cleaner in modern home',
      'https://commons.wikimedia.org/wiki/File:Person_cleaning_living_room_floor_with_vacuum_cleaner_in_modern_home.jpg',
    ),
  },
  'cleaning-kitchen-cabinets': {
    name: 'cleaning-kitchen-cabinets',
    src: '/images/cleaning-kitchen-cabinets.jpg',
    small: '/images/cleaning-kitchen-cabinets-900.jpg',
    width: 1920,
    height: 1280,
    alt: 'Cleaner wiping down the cabinet fronts in a bright condo kitchen',
    credit: shixart(
      'Woman cleaning kitchen cabinets in a bright home',
      'https://commons.wikimedia.org/wiki/File:Woman_cleaning_kitchen_cabinets_in_a_bright_home.jpg',
    ),
  },
  'cleaning-vacuum-kitchen': {
    name: 'cleaning-vacuum-kitchen',
    src: '/images/cleaning-vacuum-kitchen.jpg',
    small: '/images/cleaning-vacuum-kitchen-900.jpg',
    width: 1920,
    height: 2880,
    alt: 'Cleaner vacuuming the floor of a modern condo kitchen',
    credit: shixart(
      'Woman cleaning her home with a vacuum cleaner in a kitchen',
      'https://commons.wikimedia.org/wiki/File:Woman_cleaning_her_home_with_a_vacuum_cleaner_in_a_kitchen.jpg',
    ),
  },
  'cleaning-floors': {
    name: 'cleaning-floors',
    src: '/images/cleaning-floors.jpg',
    small: '/images/cleaning-floors-900.jpg',
    width: 1920,
    height: 1280,
    alt: 'Vacuuming hardwood floors during a deep clean of a downtown condo',
    credit: shixart(
      'Cleaning the floor with a vacuum cleaner in a home setting',
      'https://commons.wikimedia.org/wiki/File:Cleaning_the_floor_with_a_vacuum_cleaner_in_a_home_setting.jpg',
    ),
  },
  'cleaning-vacuum-rug': {
    name: 'cleaning-vacuum-rug',
    src: '/images/cleaning-vacuum-rug.jpg',
    small: '/images/cleaning-vacuum-rug-900.jpg',
    width: 1920,
    height: 2876,
    alt: 'Vacuuming a colourful area rug in a bright condo living room',
    credit: shixart(
      'Vacuum cleaner with bright lights cleaning a colourful rug in a cozy living room',
      'https://commons.wikimedia.org/wiki/File:Vacuum_cleaner_with_bright_lights_cleaning_a_colorful_rug_in_a_cozy_living_room_during_the_day.jpg',
    ),
  },
  'moving-boxes': {
    name: 'moving-boxes',
    src: '/images/moving-boxes.jpg',
    small: '/images/moving-boxes-900.jpg',
    width: 1920,
    height: 1440,
    alt: 'Stacked moving boxes ready for a condo move in or move out cleaning',
    credit: {
      title: 'An Overview of Moving Companies and Their Use of Moving Boxes',
      author: 'brownpau',
      license: 'CC BY 2.0',
      source:
        'https://commons.wikimedia.org/wiki/File:An_Overview_of_Moving_Companies_and_Their_Use_of_Moving_Boxes.jpg',
    },
  },
  'cleaned-living-room': {
    name: 'cleaned-living-room',
    src: '/images/cleaned-living-room.jpg',
    small: '/images/cleaned-living-room-900.jpg',
    width: 1920,
    height: 1281,
    alt: 'Spotless modern condo living room with stylish furniture after a Maid4Condos clean',
    credit: shixart(
      'Modern living room with stylish furniture and a view of the outdoors in a cozy apartment setting',
      'https://commons.wikimedia.org/wiki/File:Modern_living_room_with_stylish_furniture_and_a_view_of_the_outdoors_in_a_cozy_apartment_setting.jpg',
    ),
  },
  'cleaning-bathroom-sink': {
    name: 'cleaning-bathroom-sink',
    src: '/images/cleaning-bathroom-sink.jpg',
    small: '/images/cleaning-bathroom-sink-900.jpg',
    width: 1920,
    height: 1280,
    alt: 'Wiping a bathroom sink and countertop clean in a bright bathroom',
    credit: shixart(
      'Hands washing with soap at a sink in a bright bathroom',
      'https://commons.wikimedia.org/wiki/File:Hands_washing_with_soap_at_a_sink_in_a_bright_bathroom.jpg',
    ),
  },
  'cleaning-kitchen-stove': {
    name: 'cleaning-kitchen-stove',
    src: '/images/cleaning-kitchen-stove.jpg',
    small: '/images/cleaning-kitchen-stove-900.jpg',
    width: 1920,
    height: 2880,
    alt: 'Cleaner spraying and wiping the stovetop in a bright condo kitchen',
    credit: shixart(
      'Woman cleaning kitchen stove with spray cleaner and cloth in bright kitchen setting',
      'https://commons.wikimedia.org/wiki/File:Woman_cleaning_kitchen_stove_with_spray_cleaner_and_cloth_in_bright_kitchen_setting.jpg',
    ),
  },
  'cleaning-windows': {
    name: 'cleaning-windows',
    src: '/images/cleaning-windows.jpg',
    small: '/images/cleaning-windows-900.jpg',
    width: 1920,
    height: 2876,
    alt: 'Squeegee cleaning an interior window to a streak-free finish in a Toronto condo',
    credit: shixart(
      'Handheld window cleaner in use for streak-free shine on glass surface in bright indoor setting',
      'https://commons.wikimedia.org/wiki/File:Handheld_window_cleaner_in_use_for_streak-free_shine_on_glass_surface_in_bright_indoor_setting.jpg',
    ),
  },
  'cleaning-vacuum-detail': {
    name: 'cleaning-vacuum-detail',
    src: '/images/cleaning-vacuum-detail.jpg',
    small: '/images/cleaning-vacuum-detail-900.jpg',
    width: 1920,
    height: 1282,
    alt: 'Vacuum detail work in a well-lit condo interior',
    credit: shixart(
      'Efficient cleaning with modern vacuum technology in a well-lit indoor space',
      'https://commons.wikimedia.org/wiki/File:Efficient_cleaning_with_modern_vacuum_technology_in_a_well-lit_indoor_space.jpg',
    ),
  },
  'clean-kitchen': {
    name: 'clean-kitchen',
    src: '/images/clean-kitchen.jpg',
    width: 768,
    height: 1152,
    alt: 'Bright white condo kitchen with stainless steel appliances and clean countertops',
    credit: {
      title:
        'EFTA00001525 - Bright white kitchen with stainless steel appliances and pendant lights above clean countertops',
      author: 'Federal Bureau of Investigation',
      license: 'Public domain',
      source:
        'https://commons.wikimedia.org/wiki/File:EFTA00001525_-_Bright_white_kitchen_with_stainless_steel_appliances_and_pendant_lights_above_clean_countertops.jpg',
    },
  },
  'clean-bathroom': {
    name: 'clean-bathroom',
    src: '/images/clean-bathroom.jpg',
    width: 768,
    height: 1152,
    alt: 'Freshly cleaned white bathroom with a pedestal sink, mirror and folded towel',
    credit: {
      title:
        'EFTA00000556 - Clean white bathroom featuring a pedestal sink, toilet and large mirror',
      author: 'Federal Bureau of Investigation',
      license: 'Public domain',
      source:
        'https://commons.wikimedia.org/wiki/File:EFTA00000556_-_Clean_white_bathroom_featuring_a_pedestal_sink_toilet_and_large_mirror_with_black_frame_and_a_towel_hanging_on_the_wall.jpg',
    },
  },
  'clean-bathroom-alt': {
    name: 'clean-bathroom-alt',
    src: '/images/clean-bathroom-alt.jpg',
    width: 768,
    height: 1152,
    alt: 'Small white tiled bathroom with a freshly cleaned sink, mirror and countertop',
    credit: {
      title:
        'EFTA00000295 - Small bathroom with white tiles, a toilet, sink, mirror and cleaning supplies',
      author: 'Federal Bureau of Investigation',
      license: 'Public domain',
      source:
        'https://commons.wikimedia.org/wiki/File:EFTA00000295_-_Small_bathroom_with_white_tiles_a_toilet_sink_mirror_and_cleaning_supplies_on_the_tank_A_towel_hangs_on_the_wall.jpg',
    },
  },
};

// Every entry gets its WebP counterpart derived from the JPEG path, so the
// markup can offer WebP first and fall back to JPEG automatically.
export const IMAGES: Record<string, MediaImage> = Object.fromEntries(
  Object.entries(RAW_IMAGES).map(([key, value]) => [key, withWebp(value)]),
);

export const FALLBACK_IMAGE = 'cleaned-living-room';

/**
 * Resolves an image reference to a displayable photo.
 *
 * Accepts a manifest slug (the default) or a full URL/path, so a photograph
 * can be swapped for one hosted elsewhere without touching a component. Never
 * throws: an unknown value falls back to a known good photo.
 */
export function image(name: string | null | undefined): MediaImage {
  if (!name) return IMAGES[FALLBACK_IMAGE];

  const known = IMAGES[name];
  if (known) return known;

  if (/^(https?:)?\/\//i.test(name) || name.startsWith('/')) {
    return { name, src: name, width: 1600, height: 1067, alt: '' };
  }

  return IMAGES[FALLBACK_IMAGE];
}

/** Unique credits, for the `/image-credits` page. */
export function photoCredits(): MediaImage[] {
  const seen = new Set<string>();
  return Object.values(IMAGES).filter((entry) => {
    if (!entry.credit) return false;
    if (seen.has(entry.src)) return false;
    seen.add(entry.src);
    return true;
  });
}
