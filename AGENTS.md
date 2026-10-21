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

### Booking page and payment plans

`/trips/<slug>/book/` (`single-trip-book.php`, `inc/trip-booking.php`,
`css/booking.css`, `js/booking.js`). Guests pick a departure, a room (the
trip's **Rooms** repeater), travellers, £ or $, and **payment plan** (default)
or **pay in full** (the room's discounted pay-in-full price).

- Payment plan: Checkout takes the trip's deposit per person
  (`deposit_gbp` / `deposit_usd`, £500 / $700) and saves the card
  (`setup_future_usage=off_session`). The balance is split equally over the
  trip's **Payment plan dates** still ahead (after today); the last payment
  takes any odd pence. With no dates ahead, only pay in full is offered.
  There is no "60 days before departure" rule; the dates are per trip.
- On `checkout.session.completed` the webhook (`/wp-json/jj/v1/stripe`,
  signature-checked) records a **Booking** (Trips → Bookings, CPT
  `jj_booking`, meta `_jj_bk_*`) and creates a Stripe subscription schedule:
  one phase per date, yearly price so nothing renews inside a phase,
  `billing_cycle_anchor=phase_start`, no proration, cancels after the last.
  Idempotency keys stop duplicates.
- Failed / authentication-needed invoices mark the booking overdue; Stripe's
  own retry and card-update emails handle the guest. A daily cron emails the
  owner once after 14 days unpaid. Nothing is ever cancelled automatically.
- Currency: `?currency=` or the £ / $ switch, else country.is lookup of the
  visitor IP (US → $, everyone else £). Fixed once paid, since the schedule
  uses the session's currency.
- Keys: Trips → Booking Settings (`jj_stripe_secret_key`,
  `jj_stripe_webhook_secret`) or `JJ_STRIPE_SECRET_KEY` /
  `JJ_STRIPE_WEBHOOK_SECRET` in wp-config.php. **Never put keys in this
  repo; it's public.** The page is off until rooms, key and webhook secret
  are all set. Test mode offers a "Quick test schedule" (payments 5 min apart).
- Amounts are always computed on the server from the trip's fields; the form
  only sends indexes. `js/booking.js` mirrors `jj_booking_quote()`.
- Discount codes: the owner makes them in Stripe (Product catalogue →
  Coupons, as a promotion code or a coupon ID). On the **payment plan** the
  code comes off the balance, so the deposit stays the same and every later
  payment is smaller. Codes are plan-only **unless listed in Booking
  Settings → "Codes that also work paying in full"** (`jj_booking_full_codes`,
  e.g. the waitlist code BTLREADS10); those also come off the pay-in-full
  price, stacking with its discount. Partner codes stay plan-only. The booking page checks codes via
  `/wp-json/jj/v1/discount` and checkout checks again. Stripe doesn't count
  redemptions (the discount is built into our amounts), so
  `jj_booking_discount_uses()` counts bookings with the code against any
  redemption limit. A live restricted key needs Coupons and Promotion codes:
  Read.

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

## Where things stand (updated 21 October 2026)

A record of what's done, so a new session doesn't redo or undo it. Keep it
current when something here changes.

### Stripe bookings: live

- **Live mode since 20–21 October 2026.** Booking Settings holds a live
  restricted key (`rk_live_`) and the live webhook's signing secret. The
  sandbox was used for testing first and still has its own keys, webhook
  and JAIYEJAIYE10 code.
- **Restricted key permissions** (if a payment plan fails to set up, check
  these first): Write on Checkout Sessions, Customers, Products, Prices,
  Subscriptions; Read on PaymentIntents, Invoices, Coupons, Promotion codes.
- **Live webhook:** `https://jaiyejourneys.com/wp-json/jj/v1/stripe`, snapshot
  payload, the 5 events in Booking Settings. Its auto-generated name in
  Stripe ("sophisticated-sensation") doesn't matter.
- **Stripe account settings the owner chose** (Settings → Billing →
  Subscriptions and emails): Smart Retries, 8 tries in 2 weeks; if all
  retries fail, *leave the subscription overdue* (never cancel, so a plan's
  later instalments aren't lost); emails on failed card payments and
  expiring cards; card updates via a *Stripe-hosted page*; upcoming-renewal
  and trial emails off; customer subscription-management link off (guests
  mustn't cancel their own plan). Receipts on for successful payments and
  refunds. Invoice reminders and one-off invoice retries don't apply.
- **Stripe holds each instalment as a draft invoice for about an hour**
  before charging. That's normal, not a fault.
- **Refunds are manual in Stripe** (refund the payment, and for a payment
  plan also cancel the subscription), then the owner updates the booking in
  Trips → Bookings by hand. See "Next up".
- **JAIYEJAIYE10** (10% off, payment plan only) exists in live Stripe as a
  promotion code.

### Features built and live (October 2026)

- Booking page with deposit + payment plan or pay in full, £ / $, Stripe
  Checkout, webhook, Bookings in wp-admin, owner emails.
- Discount codes (payment plan only, off the balance). See the booking
  section above.
- Prices with pence: room prices, deposits and the From price use the
  `money` field type (`jj_money_clean()`, `jj_money_minor()`,
  `jj_money_display()` in `inc/trip-fields.php`). Booking code works in
  minor units from the saved price onwards.
- Confirmation page: "You're all booked, {name}!" with a one-off confetti
  burst in brand colours (`js/booking.js`; skipped for reduced motion). The
  owner tried and removed a hand-drawn underline under the name; don't
  bring it back.
- Trip Dates card (BTL): one chapter shows **Book Now**; several chapters
  each get **Book Chapter N** and no general Book Now; a departure with
  Status *Sold out* shows a deep green **Chapter N · Sold out** block
  instead of its button. The pill beside the dates is the departure's
  *Availability label* (free text the owner types), not the status.
- Trip section menu (Overview, Chapters…) sticks under the fixed header on
  every screen; on phones it's a swipeable strip that follows the current
  section. The phone bottom bar's Contents list is hidden as redundant.
- Homepage services: an accordion on phones (`js/services.js`); tablet and
  desktop unchanged.
- Trip page **From price in $** for US visitors: optional `price_from_usd`
  field. `js/trip.js` picks £ or $ from the browser's time zone (US zones →
  $; `?currency=` overrides) and adds `currency=` to the page's booking links
  so the booking page matches. With no $ From price, the £ price shows with
  an approximate $ note, as before. Hidden on Waitlist like every price.

### Working with the owner

- Steph isn't a developer and often works from the claude.ai app, sharing
  screenshots. Explain in plain language, step by step, and say exactly
  which screen and button to use.
- Write in **UK English** and **don't use em dashes**.
- Show changes and what was tested, and push to `main` only when the owner
  says "push live". After deploying, ask the owner to check the live page,
  and to press **Update theme** in WP Pusher if it hasn't refreshed.
- Never ask for, or accept, Stripe secret keys or webhook secrets in chat.
  They go straight into Trips → Booking Settings.

## Next up (agreed with the owner, 20 October 2026)

Not built yet. Remove each item from this list once it's live.

1. **Meta pixel, in code (no plugin).** The owner will supply the Pixel ID
   (a public number, fine to commit like `JAIYE_GA4_ID` in `functions.php`)
   and possibly a `facebook-domain-verification` meta tag. Events:
   `PageView` on every page; `ViewContent` on trip pages; `InitiateCheckout`
   when the booking form is submitted; `Purchase` on the booking
   confirmation (`value` = amount paid today, `currency` = the session's).
   Load it only when tracking is allowed, the same way as GA4.
2. **Country-based cookie consent.** Today one opt-in banner shows to
   everyone, and GA4 only loads on "Accept", so declines and ignored banners
   go untracked worldwide. Plan: UK, EU/EEA and Switzerland keep the opt-in
   banner; the US and elsewhere get tracking by default with a footer
   "Cookie settings" opt-out, and Global Privacy Control is honoured. Reuse
   the visitor-country lookup the booking page already does for £ / $. The
   owner is checking the US opt-out approach with whoever reviews the
   privacy policy before this goes live.
3. **Google Consent Mode v2** for GA4, so declined visits send cookieless
   signals.
4. **Cookie banner wording** should mention advertising, not just "to make
   your visit smoother". The Privacy Policy (a WordPress page, content)
   needs Meta adding; tell the owner rather than editing it.
5. **Later, optional:** Meta Conversions API (send `Purchase` from the Stripe
   webhook, respecting the same consent rules), and having the site mark
   bookings Refunded / Cancelled when Stripe reports a refund or a cancelled
   plan (today the owner updates Trips → Bookings by hand).
