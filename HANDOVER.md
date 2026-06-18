# Jaiye Journeys — Build Handover
**Last updated: 28 May 2026**  
Prepared for: continuation in a new Claude Code session

---

## 1. Project Overview

**Jaiye Journeys** is a luxury travel brand founded by Regina Stephanie Jaiyeola (Steph / Regina Jaiy). She is a certified FORA travel advisor offering curated group trips, bespoke private travel planning, literary retreats, and a full concierge service. The name comes from the Yoruba: *Jaiye* means "enjoy life."

### Tech Stack
- **WordPress** (self-hosted, no page builder, no Gutenberg on custom templates)
- **PHP** page templates (WordPress Template Hierarchy)
- **CSS** — mobile-first, CSS custom properties, no preprocessor, no build step
- **JavaScript** — vanilla only (nav toggle, hero slideshow), no framework
- **Fonts** — Google Fonts: Cormorant Garamond (headings, serif) + Jost (body, sans-serif)
- **Forms** — Tally.so (external links, some open in new tab, some link to `/contact/`)
- **Travel network** — FORA advisor platform

### Local Environment
- **App:** LocalWP (formerly Local by Flywheel)
- **Local URL:** `http://jaiye-journeys.local`
- **Theme folder (local):** `/Users/reginajaiy/Local Sites/jaiye-journeys/app/public/wp-content/themes/jaiye-journeys-theme/`
- **WP Admin (local):** `http://jaiye-journeys.local/wp-admin`

### Live Environment
- **Live URL:** `https://jaiyejourneys.com`
- **WP Admin (live):** `https://jaiyejourneys.com/wp-admin`
- **Host:** IONOS (webspace-host.com)

---

## 2. SFTP Connection Details

Config file: `.vscode/sftp.json` at the project root (one level above the theme, alongside the `.vscode/` folder).

| Setting | Value |
|---|---|
| Host | `access-5019170091.webspace-host.com` |
| Port | `22` |
| Protocol | `sftp` |
| Username | `a206430` |
| Remote path | `/wordpress/wp-content/themes/jaiye-journeys-theme` |
| Upload on save | `true` |
| Password | *(in `.vscode/sftp.json` — not recorded here)* |

### SFTP Extension Setup
1. Install the **SFTP** extension by liximomo in VS Code (`liximomo.sftp`).
2. The `.vscode/sftp.json` is already configured — no additional setup needed.
3. `uploadOnSave: true` auto-pushes a file to live every time you save it in VS Code.
4. Manual upload: right-click any file in Explorer → **SFTP: Upload File**.

### Known Issue: "Sync Local → Remote" Hangs Indefinitely
**Problem:** Right-clicking the theme folder and choosing Sync Local → Remote spins forever and uploads nothing.

**Workaround:** Do not use folder-level sync. Instead:
- Rely on `uploadOnSave: true` — saving a file pushes it automatically.
- For a file that didn't auto-upload, right-click it → **SFTP: Upload File**.
- Never use the folder-level "Sync Local → Remote" option.

---

## 3. File Inventory (Theme Root)

| File | Description |
|---|---|
| `style.css` | WordPress theme registration only — metadata (name, URI, version). Contains no visual styles. |
| `functions.php` | Theme setup: registers nav menus (`primary`, `footer`), enqueues Google Fonts + `global.css`, adds theme supports (thumbnails, HTML5, custom logo, etc.), fallback nav, favicon. |
| `global.css` | **All site CSS.** Custom properties, reset, typography, layout utilities, all page-section styles for every template. ~2200+ lines. This is the single source of truth for styling. |
| `header.php` | Site header — sticky nav, logo, hamburger (mobile), skip-to-content link. Includes inline `<style>` for header/nav CSS and vanilla JS toggle script. |
| `footer.php` | Site footer — two-column grid (brand left, links/social right), copyright, GDPR notice. Includes inline `<style>` and nav-close JS. |
| `front-page.php` | **Homepage template.** Auto-loaded by WordPress when static front page is set. Sections: Hero slideshow (4 images), Two Pathways, Services strip, About strip, Testimonials, Upcoming Trips grid. |
| `our-journeys.php` | **Our Journeys template.** Sections: Hero, Group Trips (4 cards), Between the Lines retreats (2 cards), On the Horizon (3 text cards), CTA strip. All content is hard-coded PHP. |
| `about.php` | **About / Founder template.** Sections: Hero, Intro strip, My Story, The Producer Behind the Journey (4 tiles), FORA section, Partners marquee, Testimonial, CTA. Hard-coded content. |
| `between-the-lines.php` | **Between the Lines template.** Dedicated landing page for the literary retreat sub-brand. Exists as a template — assigned to the `/between-the-lines/` page in WP Admin. |
| `contact.php` | **Contact page template.** Enquiry / booking page. |
| `page.php` | Default WordPress page template — used for Privacy Policy, Terms, and any page without a custom template assigned. |
| `single.php` | Single blog post template. Used for any editorial posts (e.g. BTL editorial content). Minimal styling — not yet fully branded. |
| `index.php` | WordPress required fallback. Not used on the live site — all real pages have specific templates. |
| `HANDOVER.md` | This file. |

---

## 4. Assets Inventory (`assets/images/`)

### Brand & UI
| File | Used for |
|---|---|
| `logo.png` | Header logo — dark version, on cream/light backgrounds |
| `logo-white.png` | Footer logo — white version, on dark green background |
| `favicon.png` | Browser tab favicon, output via `functions.php` |

### Homepage Hero Slideshow
| File | Used for |
|---|---|
| `hero-japan.jpg` | Hero slide 1 |
| `hero-colombia.jpg` | Hero slide 2 |
| `hero-peru.jpg` | Hero slide 3 |
| `hero-desert.jpg` | Hero slide 4 |
| `hero.jpg` | Legacy hero — fallback if no WP featured image is set on front page |

### Homepage — Two Pathway Cards
| File | Used for |
|---|---|
| `pathway-fit.jpg` | "Plan My Journey" card — solo traveller image |
| `pathway-group.jpg` | "Join a Group Trip" card — group travellers image |

### Founder / About Page Photos
| File | Used for |
|---|---|
| `steph.jpg` | Homepage About strip — portrait of Steph |
| `steph-hero.jpg` | About page hero — full viewport background |
| `steph-intro.jpg` | About page intro section — right column image |
| `steph-story.jpg` | About page My Story section — left column image |
| `steph-marrakech.jpg` | About page — Steph in Marrakech |
| `steph-desert-quad.jpg` | About page — Steph on desert quad |
| `steph-water.jpg` | About page — Steph near water |

### Trip Cards
| File | Trip | Used in |
|---|---|---|
| `trip-cape-town.jpg` | The Cape Town Edit | `our-journeys.php` + homepage |
| `trip-bali.jpg` | The Islands Edit (Bali) | `our-journeys.php` + homepage |
| `trip-bahia.jpg` | The Bahia Edit | `our-journeys.php` |
| `trip-peru.jpg` | The Director's Cut (Peru) | `our-journeys.php` |
| `trip-btl-prologue.jpg` | The Prologue (BTL event) | `our-journeys.php` + homepage |
| `trip-morocco.jpg` | The Essaouira Edition | `our-journeys.php` + homepage |

### Between the Lines
| File | Used for |
|---|---|
| `btl-hero.jpg` | BTL page hero background |
| `btl-kindle-beach.jpg` | BTL page section image |
| `btl-reading-pool.jpg` | BTL page section image |
| `btl-logo-between-the-lines.svg` | BTL logo — SVG light version |
| `btl-logo-between-the-lines-dark.png` | BTL logo — PNG dark version |
| `btl-logo-green.svg` | BTL green logo — SVG |
| `btl-logo-green-dark.png` | BTL green logo — PNG dark |
| `btl-logo-reading-retreats.svg` | BTL "Reading Retreats" wordmark — SVG |
| `btl-logo-reading-retreats-dark.png` | BTL "Reading Retreats" wordmark — PNG dark |
| `the-authors-note-logo.png` | Partner brand — The Author's Note |

### Atmospheric / Editorial Images
| File | Used for |
|---|---|
| `travel-bali-pool.jpg` | Lifestyle/editorial — pool |
| `travel-lobby.jpg` | Lifestyle/editorial — hotel lobby |
| `travel-riad-pool.jpg` | Lifestyle/editorial — riad pool |
| `travel-sea.jpg` | Lifestyle/editorial — seascape |
| `travel-tea.jpg` | Lifestyle/editorial — tea service |
| `travel-terrace.jpg` | Lifestyle/editorial — terrace |
| `travel-tuscany.jpg` | Lifestyle/editorial — Tuscany |
| `travel-yacht.jpg` | Lifestyle/editorial — yacht |

### FORA & Partner Programme Logos
| File | Programme |
|---|---|
| `fora-badge.png` | FORA advisor badge |
| `Fora-Vector.png` | FORA wordmark/monogram |
| `partner-Virtuoso.png` | Virtuoso |
| `partner-Four-Seasons.png` | Four Seasons |
| `partner-rosewood.png` | Rosewood |
| `partner-The_Leading_Hotels-of-the-World.png` | Leading Hotels of the World |
| `partner-Oetker-Collection.png` | Oetker Collection |
| `partner-dorchester-collection.png` | Dorchester Collection |
| `partner-belmond-bellini-club.png` | Belmond Bellini Club *(active reference)* |
| `Partner-Bellini-Club.png` | Belmond Bellini Club *(orphan — safe to delete)* |
| `partner-one-only.png` | One&Only |
| `partner-Design-Hotels.png` | Design Hotels |
| `partner-Hyatt-Prive.png` | Hyatt Privé |
| `partner-hilton-impresario.png` | Hilton Impresario |
| `partner-InterContinental-Hotels-Group-Logo.png` | IHG |
| `partner-jumeirah.png` | Jumeirah |
| `partner-Standard-Hotels.png` | The Standard Hotels |
| `partner-firmdale.png` | Firmdale Hotels |
| `partner-slh.png` | Small Luxury Hotels |
| `partner-preferred-hotels-and-resorts.png` | Preferred Hotels & Resorts |
| `partner-relais.png` | Relais & Châteaux |
| `partner-Fontenille.png` | Fontenille |
| `partner-COOLROOMS.png` | Coolrooms |
| `partner-Club1897.png` | Club 1897 |
| `partner-Couture.png` | Couture Hotels |
| `partner-Omni.png` | Omni Hotels |
| `partner-RF.Knights.Black.png` | RF Knights |
| `partner-fan-club.png` | Fan Club |
| `partner-mercer.png` | Mercer Hotels |
| `partner-pennclub.png` | Penn Club |

---

## 5. CSS Custom Properties (`global.css` `:root`)

### Colours
```css
--color-cream:        #f5e9db   /* warm off-white — primary page background */
--color-forest:       #093923   /* dark green — header bg, primary headings */
--color-salmon:       #e48168   /* coral/salmon — accent, badges, CTA highlights */
--color-sage:         #b1bb9e   /* muted green — secondary accent, BTL section bg */
--color-deep-green:   #081f1c   /* near-black — body text, footer bg */
--color-bright-green: #2e8d36   /* vivid green — CTA button fill */

/* Semantic aliases */
--color-bg:           var(--color-cream)
--color-surface:      #ffffff
--color-text:         var(--color-deep-green)
--color-text-muted:   var(--color-forest)
--color-accent:       var(--color-salmon)
--color-accent-alt:   var(--color-sage)
--color-cta:          var(--color-bright-green)
--color-border:       color-mix(in srgb, var(--color-sage) 40%, transparent)
```

### Typography
```css
--font-heading:  'Cormorant Garamond', Georgia, serif
--font-body:     'Jost', system-ui, sans-serif

--fw-light:    300
--fw-regular:  400
--fw-semibold: 600

/* Fluid type scale (mobile → desktop) */
--text-xs:   12 → 14px    --text-sm:   14 → 16px
--text-base: 16 → 18px    --text-md:   18 → 22px
--text-lg:   20 → 26px    --text-xl:   24 → 34px
--text-2xl:  30 → 46px    --text-3xl:  36 → 58px
--text-4xl:  44 → 74px    --text-5xl:  52 → 92px
```

### Spacing
```css
--space-1:  4px    --space-2:  8px    --space-3:  12px
--space-4:  16px   --space-5:  20px   --space-6:  24px
--space-8:  32px   --space-10: 40px   --space-12: 48px
--space-16: 64px   --space-20: 80px   --space-24: 96px   --space-32: 128px

--section-gap:    clamp(48px, 8vw, 96px)
--section-gap-sm: clamp(32px, 5vw, 64px)
```

### Layout
```css
--container-max:    1440px
--container-wide:   1280px
--container-narrow: 720px
--container-pad:    clamp(16px, 5vw, 64px)
```

### Radii
```css
--radius-sm: 4px   --radius-md: 8px   --radius-lg: 16px
--radius-xl: 24px  --radius-full: 9999px
```

### Transitions & Z-index
```css
--ease-out:      cubic-bezier(0.22, 1, 0.36, 1)
--ease-in-out:   cubic-bezier(0.4, 0, 0.2, 1)
--duration-fast: 150ms   --duration-base: 250ms   --duration-slow: 400ms

--z-below: -1   --z-base: 0    --z-raised: 10   --z-overlay: 100
--z-modal: 200  --z-nav:  300  --z-toast:  400
```

---

## 6. Current Site Structure

| Page | Slug | Template file | Notes |
|---|---|---|---|
| Homepage | `/` | `front-page.php` | Auto-loaded by WordPress when static front page is set |
| Our Journeys | `/our-journeys/` | `our-journeys.php` | Template Name: Our Journeys |
| About | `/about/` | `about.php` | Template Name: About |
| Contact | `/contact/` | `contact.php` | Template Name: Contact |
| Between the Lines | `/between-the-lines/` | `between-the-lines.php` | Template Name: Between the Lines |
| Privacy Policy | `/privacy-policy/` | `page.php` | Default template, needs legal copy |
| Terms | `/terms/` | `page.php` | Default template, needs legal copy |

### WordPress Setup Requirements
- **Reading settings:** Settings → Reading → "A static page" → Homepage = Home page
- **Menus:** Assign a menu to `Primary Navigation` and `Footer Navigation` locations. Nav items: Our Journeys, Between the Lines, About, Contact.
- **Template assignment:** Each custom-template page must have its template selected in Page Attributes → Template in WP Admin.

### Tally Form URLs
| Form purpose | URL |
|---|---|
| The Ticketing Desk (flights/transfers) | `https://tally.so/r/eq6rp0` |
| Cape Town waitlist | `https://tally.so/r/aQa9D2` |
| Islands Cut (Bali) waitlist | `https://tally.so/r/RGVxVj` |
| Bahia waitlist | `https://tally.so/r/xXlrzr` |
| Director's Cut (Peru) waitlist | `https://tally.so/r/1ApXBp` |
| Between the Lines (Prologue + all BTL) | `https://tally.so/r/Gxq2JL` |

---

## 7. Current Outstanding Issues

### 1. SFTP "Sync Local → Remote" hangs indefinitely
- **Status:** Unresolved
- **Impact:** Cannot bulk-sync the theme folder; must upload file by file
- **Workaround:** Use `uploadOnSave` (automatic on Cmd+S) or right-click individual files → SFTP: Upload File
- **Do not use** the folder-level "Sync Local → Remote" option

### 2. Empty ruleset warning in `global.css` at line 843
- **Status:** Cosmetic — does not affect rendering
- **Detail:** `.fp-pathways {}` contains only a comment (`/* Full bleed — no container */`) and no CSS declarations. CSS linters flag this as an empty ruleset.
- **Fix if desired:** Delete the ruleset entirely (the comment is informational only and not needed), or add a real declaration like `position: relative;` to silence the linter.

### 3. `single.php` is minimal and unstyled
- **Status:** Template exists but has not been designed to match the brand
- **Impact:** Any blog posts (e.g. BTL editorial content) render without branded styling

### 4. `Partner-Bellini-Club.png` is an orphan file
- **Status:** Safe to delete
- **Detail:** The active reference in `about.php` uses `partner-belmond-bellini-club.png`. `Partner-Bellini-Club.png` (capital P) is a duplicate with no active references.

---

## 8. What Was Completed Today (28 May 2026)

### Earlier in the session — Ticketing Desk link
**`front-page.php`**
- Added `style="color: inherit; text-decoration: none;"` to the existing `<a>` tag on the Ticketing Desk service tile title. The `href`, `target="_blank"`, and `rel="noopener"` were already present.

### Main session — four changes

**Change 1 — Between the Lines service tile replaced**
- Removed the "Between the Lines" tile from the homepage services strip (it is a sub-brand / product line, not a core service offering alongside flights and hotels).
- Replaced with a new fifth tile: **The Jaiye Concierge**
  - Copy: "Full-service lifestyle and travel management for clients who want everything handled."
  - Title links to `/contact/` with `target="_self"`

**Change 2 — All remaining service tile titles wrapped in links**
- The Hotel Edit → `/contact/`, `target="_self"`, `style="color: inherit; text-decoration: none;"`
- The Signature Itinerary → same
- Private Groups & Celebrations → same
- The Jaiye Concierge → same (included in Change 1 above)
- The Ticketing Desk — link already in place; kept unchanged

**Change 3 — "Popular" badge on The Ticketing Desk**
- Added `<span class="fp-service-popular">Popular</span>` above the `<h3>` inside the Ticketing Desk tile.
- Added `.fp-service-popular` to `global.css` (after `.fp-service-tile__arrow`):
  ```css
  .fp-service-popular {
    display: inline-block;
    background: var(--color-salmon);
    color: white;
    font-family: var(--font-body);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: var(--radius-full);
    margin-bottom: 12px;
  }
  ```

**Change 4 — Pathway card title renamed**
- "Plan My Own Trip" → **"Plan My Journey"**
- The title previously implied the client plans their own trip, which contradicts the JJ brand positioning (JJ handles everything). The description and button text were already correctly framed — only the title needed updating.

---

## 9. What Still Needs Doing

- Verify all Tally form links are live and pointing to the correct forms.
- Review `between-the-lines.php` — the BTL service tile was removed from the homepage, but the dedicated page and our-journeys.php cards remain. Confirm the BTL page is complete and connected.
- Confirm `contact.php` is live and the enquiry form is submitting correctly.
- Mobile testing — especially the services strip horizontal scroll and pathway card layout.
- Style `single.php` to match brand if blog posts are being used.
- Clean up `Partner-Bellini-Club.png` orphan image.
- Decide whether `hero.jpg` (legacy) is still needed or can be deleted.
- Privacy Policy and Terms pages need legal copy from Steph.

---

## 10. Key Decisions Made

| Decision | Choice | Rationale |
|---|---|---|
| No page builder | Custom PHP templates only | Full control over markup; no Gutenberg/Elementor overhead |
| Single CSS file | `global.css` | Simple to maintain at this scale; no build step needed |
| Fluid type scale | CSS `clamp()` | Smooth scaling across all viewports without brittle breakpoints |
| Font pairing | Cormorant Garamond (headings) + Jost (body) | Cormorant = luxury/editorial; Jost = modern/clean — matches the JJ brand voice |
| Primary palette | Forest `#093923`, Cream `#f5e9db`, Salmon `#e48168` | Forest = nature/calm, Cream = warmth/luxury, Salmon = distinctive energy |
| Service tile links | Internal services → `/contact/`; Ticketing Desk → Tally form | Ticketing Desk is direct/self-service; other services need a conversation first |
| Service tile link style | `color: inherit; text-decoration: none;` on all title links | Titles read as headings — the tile itself communicates interactivity |
| "Popular" badge | Salmon pill on Ticketing Desk only | It is the lowest-friction entry point and worth surfacing |
| Pathway card framing | Both cards communicate JJ as the planner | Core brand position: JJ handles everything, client just chooses the path |
| Between the Lines removal from services strip | Removed; replaced with The Jaiye Concierge | BTL is a sub-brand with its own page; the services strip should show core service offerings |
| Class naming convention | BEM-inspired with page prefix: `fp-` (front page), `oj-` (our journeys), `ab-` (about) | Avoids collisions between page-specific styles in a single CSS file |
| All content hard-coded | No CMS-managed content on custom pages | Small, stable site — Steph controls copy and it rarely changes; CMS overhead not justified |
| Mobile-first breakpoints | 320px base → 768px tablet → 1200px desktop | Standard progressive enhancement |

---

## 11. How to Start a New Claude Code Session

1. **Open VS Code.**
2. **Open the theme folder:**  
   File → Open Folder → `/Users/reginajaiy/Local Sites/jaiye-journeys/app/public/wp-content/themes/jaiye-journeys-theme/`
3. **Open the integrated Terminal:**  
   `` Ctrl+` `` or Terminal → New Terminal
4. **Launch Claude Code:**
   ```
   claude
   ```
5. **Paste this as your first message:**
   > Read HANDOVER.md and pick up where we left off. The theme is at `/Users/reginajaiy/Local Sites/jaiye-journeys/app/public/wp-content/themes/jaiye-journeys-theme/`.

Keep LocalWP running in the background so you can preview changes at `http://jaiye-journeys.local` as you work.

---

## 12. Workflow for Making Live Changes

1. **Claude Code edits the file** in the theme directory.
2. **You press Cmd+S** in VS Code. The `uploadOnSave` setting in `sftp.json` pushes the file to the live server automatically.
3. **Hard refresh the live site** to verify: `https://jaiyejourneys.com` → Cmd+Shift+R (bypasses browser cache).
4. **If uploadOnSave did not fire** (SFTP connection dropped or timed out): right-click the changed file in the VS Code Explorer → **SFTP: Upload File**.

> **Never use** "Sync Local → Remote" on the theme folder — it hangs indefinitely without uploading. Always upload file by file.

---

## 13. File Paths Reference

```
Theme root (local):
/Users/reginajaiy/Local Sites/jaiye-journeys/app/public/wp-content/themes/jaiye-journeys-theme/

WordPress install root (local):
/Users/reginajaiy/Local Sites/jaiye-journeys/app/public/

SFTP remote root (live):
/wordpress/wp-content/themes/jaiye-journeys-theme/

SFTP config:
/Users/reginajaiy/Local Sites/jaiye-journeys/.vscode/sftp.json
```
