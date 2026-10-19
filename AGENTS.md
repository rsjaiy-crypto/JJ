# Jaiye Journeys theme — instructions for AI coding agents

Read this before changing anything. It is the current source of truth; where
HANDOVER.md disagrees (it predates the GitHub workflow), this file wins.

This repository is the WordPress theme for https://jaiyejourneys.com — a
luxury group-travel and literary-retreat brand run by Steph (Regina) Jaiyeola.
Classic PHP templates, vanilla JS, plain CSS with custom properties. No build
step, no page builder, no plugins required by the theme.

## How changes go live

- **Pushing to `main` deploys to the live site within about a minute.** The
  WP Pusher plugin on the server pulls this repo into `wp-content/themes/JJ`.
  There is no staging site. Treat every push to `main` as a production deploy.
- The local LocalWP copy is out of date and not used. Don't rely on it.
- Don't upload over SFTP. The `.vscode/sftp.json` paths are stale, and the
  `/wordpress/...` and `old-jaiye-journeys-theme` folders on the server are
  leftovers. Never commit `.vscode/sftp.json` (it holds the server password).

## How the owner wants you to work

1. Create a branch. Never commit straight to `main`.
2. Make the change and test it (see Testing).
3. Show the owner the diff and what you tested, in plain language. The owner
   isn't a developer.
4. Merge to `main` and push **only when the owner says so** ("merge", "push
   live"). Afterwards, check the live pages load and show the change.

## Code vs content

- **Code (this repo):** layout, styles, templates, field definitions.
- **Content (WordPress database, edited in wp-admin):** trip itineraries,
  dates, prices, FAQs, sales status, images. Don't hard-code trip details in
  templates. Read them from the trip's fields so pages and cards can't drift.

## Trips

Trips are a custom post type (`/trips/<slug>/`). `inc/trip-fields.php` is the
single source of truth for every trip field: the admin meta boxes
(`inc/trip-meta.php`), saving and the templates all read its schema. Meta keys
are prefixed `_jj_trip_`.

### Sales Status

Stored in `_jj_trip_status`. **The stored keys don't match the labels**, for
historical reasons. Don't rename them, because saved trips depend on them.

| Label in wp-admin | Stored key   | Trip page shows |
|-------------------|--------------|-----------------|
| Coming soon       | `announced`   | Short page (`inc/trip-coming-soon.php`): hero, facts, "Get on The List" |
| Waitlist          | `coming-soon` | Full trip page with **no prices, payment terms, availability or Book links**; every Book becomes "Get on The List" |
| On sale           | `open`        | Full page with booking |
| Sold out          | `sold-out`    | Full page, Book links removed |

- `jj_trip_status()` applies the optional "Go on sale at" / "Sold out from"
  times, so it's correct to the minute without cron.
- `jj_trip_public_status()` is what visitors see. Use it for the page and for
  every card label so they always agree.
- Pre-sale pages must not expose prices anywhere in the page source (fields,
  `data-` attributes, structured data). Deposit amounts the owner wrote in FAQ
  text are fine.
- Status can be changed per trip, or in Quick Edit / Bulk Edit on the Trips list.

### Booking page

`/trips/<slug>/book/` (`single-trip-book.php`, `inc/trip-booking.php`,
`css/booking.css`, `js/booking.js`). Guests pick a departure, a room (the
trip's **Rooms** repeater), travellers and deposit or full payment, and the
site creates a Stripe Checkout Session and redirects to it.

- Switched on per trip by adding Rooms, once a Stripe key is saved in
  Trips → Booking Settings (stored in the `jj_stripe_secret_key` option, or a
  `JJ_STRIPE_SECRET_KEY` constant in wp-config.php). **Never put the key in
  this repo; it's public.** Without both, Book buttons keep the old Book Now URL.
- Only reachable while the trip is On sale (editors can preview it).
- Amounts are always computed on the server from the trip's fields; the form
  only sends indexes. Stripe metadata carries trip, departure, room,
  travellers and deposit/full.
- Stripe returns guests to the same URL with `?session_id=`, which shows the
  confirmation after checking the session is paid.

### Cards and listings

- Homepage "Upcoming Trips" = trips ticked **Show on homepage**, soonest first.
- Our Journeys = all published trips, sorted by first departure date.
- Between the Lines chapter cards = published retreats (`brand` = `btl`).
- Every card uses `jj_trip_status_tag()` and the call to action "Step inside".
  Every trip page ends with the "Other journeys" button.

## House style and wording

- **Marrakesh** is spelled with an S, everywhere. (Slugs and image file names
  like `btl-marrakech` stay as they are, so links don't break.)
- Between the Lines retreats: a **volume** is a residency (a destination) and
  a **chapter** is one retreat week (one departure row). Volumes are numbered
  by their first chapter's date, e.g. "Volume 1: Marrakesh"; chapters are
  numbered within their volume. Volume names: Marrakesh = "Scents and Sounds",
  Sintra = "Mists and Mysteries".
- The waitlist is called **The List** ("Get on The List").
- The owner dictates messages with Wispr Flow, so spellings in their requests
  can be transcription errors. Ask when a name looks off.

## Design tokens

Defined in `global.css` `:root`. Use the variables, not hex values.
Fonts: **Bodoni Moda** (`--font-heading`) and **Jost** (`--font-body`).
Colours: cream `#f5e9db`, forest `#093923`, salmon/coral `#e48168`, sage
`#b1bb9e`, deep green `#081f1c`. Mobile-first, and every change must work at
390px wide with no horizontal scroll.

## Testing

There's no WordPress test environment. At minimum:

- `php -l` every PHP file you touch.
- For template changes, render the template with WordPress functions stubbed
  and sample data (e.g. `jj_trip_seed_definitions()` in `inc/trip-seed.php`),
  then compare the output with `main` to confirm only the intended parts
  changed. Test every Sales Status the change could affect.
- Check layout in a headless browser at desktop width and at 390px.
- After deploying, load the live pages and check for PHP errors and the change.

## Rewrites

If you change the trip post type's slug or rewrite settings, bump
`JJ_TRIP_REWRITE_VERSION` in `inc/trip-cpt.php`. Deploys don't reactivate the
theme, so this is what flushes the rewrite rules.
