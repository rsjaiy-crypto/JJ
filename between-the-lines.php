<?php
/*
 * Template Name: Between the Lines
 */

get_header();
?>

<style>
/* ------------------------------------------------------------------
   Between the Lines — all component styles
   ------------------------------------------------------------------ */


/* ── 1. HERO ──────────────────────────────────────────────────────── */

.btl-hero {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100svh;
  overflow: hidden;
  background-color: var(--color-deep-green);
}

.btl-hero__media {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.btl-hero__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center 40%;
}

.btl-hero__overlay {
  position: absolute;
  inset: 0;
  background-color: rgba(8, 31, 28, 0.55);
}

.btl-hero__inner {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: var(--space-16) var(--container-pad);
}

.btl-hero__logo {
  display: block;
  width: 200px;
  height: auto;
}

.btl-hero__sub {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  line-height: 1.35;
  margin-top: 32px;
  max-width: none;
  margin-bottom: 0;
}

.btl-hero__btn {
  display: inline-flex;
  align-items: center;
  margin-top: 40px;
  padding: 14px 36px;
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--fw-regular);
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--color-salmon);
  background-color: transparent;
  border: 1px solid var(--color-salmon);
  border-radius: var(--radius-sm);
  white-space: nowrap;
  transition:
    background-color var(--duration-base) var(--ease-out),
    color var(--duration-base) var(--ease-out);
}

.btl-hero__btn:hover {
  background-color: var(--color-salmon);
  color: #ffffff;
}

@media (min-width: 768px) {
  .btl-hero__logo { width: 280px; }
}


/* ── 2. CONCEPT ───────────────────────────────────────────────────── */

.btl-concept {
  background-color: var(--color-cream);
  padding-block: clamp(64px, 8vw, 96px);
}

.btl-concept__inner {
  display: flex;
  flex-direction: column;
  gap: var(--space-12);
}

.btl-concept__quote {
  font-family: var(--font-heading);
  font-size: var(--text-3xl);
  font-style: italic;
  font-weight: var(--fw-light);
  color: var(--color-forest);
  line-height: 1.25;
  /* reset global blockquote defaults */
  padding-left: 0;
  border-left: none;
}

.btl-concept__body {
  display: flex;
  flex-direction: column;
}

.btl-concept__body p {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.85;
  color: color-mix(in srgb, var(--color-forest) 80%, transparent);
  max-width: none;
  margin: 0;
}

.btl-concept__body p + p {
  margin-top: var(--space-6);
}

.btl-concept__link {
  display: inline-flex;
  align-items: center;
  margin-top: 40px;
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-regular);
  color: var(--color-salmon);
  transition: opacity var(--duration-fast) var(--ease-out);
}

.btl-concept__link:hover {
  color: var(--color-salmon);
  opacity: 0.75;
}

@media (min-width: 768px) {
  .btl-concept__inner {
    flex-direction: row;
    align-items: flex-start;
    gap: var(--space-16);
  }

  .btl-concept__quote {
    flex: 0 0 45%;
  }

  .btl-concept__body {
    flex: 1;
  }
}


/* ── 3. WHAT MAKES BTL DIFFERENT (USP) ───────────────────────────── */

.btl-usp {
  background-color: var(--color-deep-green);
  padding-block: clamp(64px, 8vw, 96px);
}

.btl-usp__overline {
  display: block;
  text-align: center;
  color: var(--color-cream);
  margin-bottom: var(--space-3);
}

.btl-usp__heading {
  font-family: var(--font-heading);
  font-size: var(--text-3xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  text-align: center;
  line-height: 1.2;
  margin-bottom: clamp(48px, 6vw, 72px);
}

.btl-usp__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2px;
}

.btl-usp__tile {
  padding: 48px 40px;
  background-color: rgba(255, 255, 255, 0.08);
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.7s var(--ease-out), transform 0.7s var(--ease-out);
}

.btl-usp__tile.btl-usp__tile--visible {
  opacity: 1;
  transform: translateY(0);
}

.btl-usp__tile-title {
  font-family: var(--font-heading);
  font-size: var(--text-xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  line-height: 1.2;
  margin-bottom: 16px;
}

.btl-usp__tile-body {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--fw-light);
  color: var(--color-sage);
  line-height: 1.8;
  max-width: none;
  margin: 0;
}

@media (min-width: 768px) {
  .btl-usp__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1200px) {
  .btl-usp__grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (prefers-reduced-motion: reduce) {
  .btl-usp__tile {
    opacity: 1;
    transform: none;
    transition: none;
  }
}


/* ── 4. THE AUTHOR'S NOTE ─────────────────────────────────────────── */

.btl-tan {
  background-color: var(--color-forest);
  padding-block: clamp(64px, 8vw, 96px);
}

.btl-tan__inner {
  display: flex;
  flex-direction: column;
  gap: var(--space-12);
}

.btl-tan__logo-col {
  display: flex;
  align-items: center;
  justify-content: center;
}

.btl-tan__logo {
  max-width: 320px;
  width: 100%;
  display: block;
  margin-inline: auto;
  height: auto;
}

.btl-tan__overline {
  color: var(--color-sage);
}

.btl-tan__heading {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  line-height: 1.25;
  margin-top: 12px;
}

.btl-tan__body {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  color: var(--color-sage);
  line-height: 1.8;
  margin-top: 24px;
  max-width: none;
  margin-bottom: 0;
}

.btl-tan__body + .btl-tan__body {
  margin-top: 16px;
}

@media (min-width: 768px) {
  .btl-tan__inner {
    flex-direction: row;
    align-items: center;
    gap: var(--space-16);
  }

  .btl-tan__logo-col {
    flex: 0 0 40%;
  }

  .btl-tan__content {
    flex: 1;
  }
}


/* ── 5. READING IMAGES ────────────────────────────────────────────── */

.btl-images {
  background-color: var(--color-cream);
}

.btl-images__grid {
  display: grid;
  grid-template-columns: 1fr;
}

.btl-images__item {
  height: 300px;
  overflow: hidden;
}

.btl-images__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}

@media (min-width: 768px) {
  .btl-images__grid {
    grid-template-columns: repeat(3, 1fr);
  }

  .btl-images__item {
    height: 480px;
  }
}


/* ── 6. PIPELINE ──────────────────────────────────────────────────── */

.btl-pipeline {
  background-color: var(--color-cream);
  padding-block: clamp(64px, 8vw, 96px);
}

.btl-pipeline__overline {
  display: block;
  text-align: center;
  margin-bottom: var(--space-3);
}

.btl-pipeline__heading {
  font-family: var(--font-heading);
  font-size: var(--text-3xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  text-align: center;
  margin-bottom: var(--space-12);
}

.btl-pipeline__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-6);
  align-items: stretch;
}

.btl-pipeline__grid > li {
  display: flex;
}

.btl-pipeline-card {
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: var(--space-3);
  padding: 32px 24px;
  background-color: var(--color-surface);
  border-radius: var(--radius-md);
  box-shadow: 0 2px 20px color-mix(in srgb, var(--color-deep-green) 8%, transparent);
}

.btl-pipeline-card__label {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--color-salmon);
  max-width: none;
  margin: 0;
}

.btl-pipeline-card__title {
  font-family: var(--font-heading);
  font-size: var(--text-xl);
  font-weight: var(--fw-regular);
  color: var(--color-forest);
  line-height: 1.2;
}

.btl-pipeline-card__date {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--fw-light);
  color: var(--color-sage);
  max-width: none;
  margin: 0;
}

.btl-pipeline-card__desc {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--fw-light);
  line-height: 1.65;
  color: color-mix(in srgb, var(--color-forest) 70%, transparent);
  max-width: none;
  margin: 0;
}

.btl-pipeline-card__pill {
  display: inline-block;
  align-self: flex-start;
  margin-top: auto;
  padding: 4px 12px;
  border-radius: var(--radius-full);
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.06em;
}

.btl-pipeline-card__pill--coming {
  background-color: var(--color-salmon);
  color: var(--color-cream);
}

.btl-pipeline-card__pill--open {
  background-color: var(--color-forest);
  color: var(--color-cream);
}

.btl-pipeline-card__pill--horizon {
  background-color: var(--color-sage);
  color: var(--color-forest);
}

@media (min-width: 768px) {
  .btl-pipeline__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1200px) {
  .btl-pipeline__grid {
    grid-template-columns: repeat(3, 1fr);
  }
}


/* ── 7. WAITLIST CTA ──────────────────────────────────────────────── */

.btl-waitlist {
  background-color: var(--color-deep-green);
  text-align: center;
  padding-block: clamp(64px, 8vw, 96px);
  padding-inline: var(--container-pad);
  overflow: visible;
}

.btl-waitlist__logo {
  display: block;
  width: 80px;
  height: auto;
  margin-inline: auto;
  margin-bottom: 32px;
}

.btl-waitlist__heading {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-3xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  line-height: 1.2;
}

.btl-waitlist__body {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  color: var(--color-sage);
  line-height: 1.75;
  max-width: 480px;
  margin-inline: auto;
  margin-top: 16px;
  margin-bottom: 0;
}

.btl-waitlist__btn {
  display: inline-flex;
  align-items: center;
  margin-top: 40px;
  padding: var(--space-4) var(--space-10);
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--fw-regular);
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--color-cream);
  background-color: var(--color-salmon);
  border: 1px solid var(--color-salmon);
  border-radius: var(--radius-sm);
  white-space: nowrap;
  transition:
    background-color var(--duration-base) var(--ease-out),
    border-color var(--duration-base) var(--ease-out),
    transform var(--duration-fast) var(--ease-out);
}

.btl-waitlist__btn:hover {
  background-color: color-mix(in srgb, var(--color-salmon) 82%, black);
  border-color: color-mix(in srgb, var(--color-salmon) 82%, black);
  color: var(--color-cream);
  transform: translateY(-1px);
}

.btl-waitlist__btn:active {
  transform: translateY(0);
}
</style>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. HERO
       Full viewport. btl-hero.jpg bg, dark overlay, centred content.
       ============================================================ -->
  <section
    class="btl-hero"
    aria-label="<?php esc_attr_e( 'Between the Lines — luxury literary residency', 'jaiye-journeys' ); ?>"
  >
    <div class="btl-hero__media" aria-hidden="true">
      <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-hero.jpg' ); ?>"
        alt=""
        class="btl-hero__img"
        loading="eager"
        fetchpriority="high"
      >
      <div class="btl-hero__overlay"></div>
    </div>

    <div class="btl-hero__inner">
      <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-logo-between-the-lines.svg' ); ?>"
        alt="<?php esc_attr_e( 'Between the Lines', 'jaiye-journeys' ); ?>"
        class="btl-hero__logo"
        loading="eager"
      >
      <p class="btl-hero__sub">
        <?php esc_html_e( 'A luxury literary residency by Jaiye Journeys', 'jaiye-journeys' ); ?>
      </p>
      <a
        href="https://tally.so/r/Gxq2JL"
        class="btl-hero__btn"
        target="_blank"
        rel="noopener noreferrer"
      >
        <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
      </a>
    </div>
  </section><!-- /.btl-hero -->


  <!-- ============================================================
       2. THE CONCEPT
       Cream bg. Pull quote left, body copy right.
       ============================================================ -->
  <section
    class="btl-concept"
    aria-label="<?php esc_attr_e( 'About Between the Lines', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="btl-concept__inner">

        <blockquote class="btl-concept__quote">
          <?php esc_html_e( '"Where serious readers go to live inside the feeling of their favourite books."', 'jaiye-journeys' ); ?>
        </blockquote>

        <div class="btl-concept__body">
          <p>
            <?php esc_html_e( 'Between the Lines is a luxury literary residency -- a series of curated retreats designed for people who love books and believe that the best stories happen when you give yourself the space to actually live in them.', 'jaiye-journeys' ); ?>
          </p>
          <p>
            <?php esc_html_e( 'Each retreat is built around four things: radical rest, authentic culture, genuine community, and the kind of unhurried reading time that real life rarely allows. No packed schedules. No 10am checkouts. Just you, your book, and a beautifully produced experience in a destination worth writing home about.', 'jaiye-journeys' ); ?>
          </p>
          <p>
            <?php esc_html_e( 'Every retreat is limited to 12 guests. Every detail is handled. All you need to bring is your reading list.', 'jaiye-journeys' ); ?>
          </p>
          <a
            href="https://tally.so/r/Gxq2JL"
            class="btl-concept__link"
            target="_blank"
            rel="noopener noreferrer"
          >
            <?php esc_html_e( 'Secure your place →', 'jaiye-journeys' ); ?>
          </a>
        </div>

      </div>
    </div>
  </section><!-- /.btl-concept -->


  <!-- ============================================================
       3. WHAT MAKES BTL DIFFERENT
       Cream bg. Six animated fade-up tiles.
       1-col mobile, 2-col tablet, 3-col desktop.
       ============================================================ -->
  <section
    class="btl-usp"
    aria-label="<?php esc_attr_e( 'What makes Between the Lines different', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <span class="overline btl-usp__overline">
        <?php esc_html_e( 'Why Between the Lines', 'jaiye-journeys' ); ?>
      </span>
      <h2 class="btl-usp__heading">
        <?php esc_html_e( 'What makes our reading retreats different.', 'jaiye-journeys' ); ?>
      </h2>
    </div>

    <ul class="btl-usp__grid" role="list">

      <li class="btl-usp__tile" data-delay="0">
        <h3 class="btl-usp__tile-title">
          <?php esc_html_e( 'The Private Estate Residency', 'jaiye-journeys' ); ?>
        </h3>
        <p class="btl-usp__tile-body">
          <?php esc_html_e( 'We don\'t do hotels. We do full property buyouts. For the duration of your residency, the villa, estate, or riad is your private home and yours alone.', 'jaiye-journeys' ); ?>
        </p>
      </li>

      <li class="btl-usp__tile" data-delay="100">
        <h3 class="btl-usp__tile-title">
          <?php esc_html_e( 'The No-Rush Promise', 'jaiye-journeys' ); ?>
        </h3>
        <p class="btl-usp__tile-body">
          <?php esc_html_e( 'Most trips end with a 10am checkout. Not here. The last day of your residency is your most peaceful -- keep your room, take one last dip in the pool, and leave when you\'re ready.', 'jaiye-journeys' ); ?>
        </p>
      </li>

      <li class="btl-usp__tile" data-delay="200">
        <h3 class="btl-usp__tile-title">
          <?php esc_html_e( 'The Living Literary Salon', 'jaiye-journeys' ); ?>
        </h3>
        <p class="btl-usp__tile-body">
          <?php esc_html_e( 'From Trope Debates over fine wine to curated author conversations, the books are just the beginning. This is not your average book club.', 'jaiye-journeys' ); ?>
        </p>
      </li>

      <li class="btl-usp__tile" data-delay="300">
        <h3 class="btl-usp__tile-title">
          <?php esc_html_e( 'Editorial-Style Curation', 'jaiye-journeys' ); ?>
        </h3>
        <p class="btl-usp__tile-body">
          <?php esc_html_e( 'Led by a Creative Director, every inch of the residency is scouted for its feel. We produce environments that are visually stunning, culturally rich, and intentionally beautiful.', 'jaiye-journeys' ); ?>
        </p>
      </li>

      <li class="btl-usp__tile" data-delay="400">
        <h3 class="btl-usp__tile-title">
          <?php esc_html_e( 'Curated Community', 'jaiye-journeys' ); ?>
        </h3>
        <p class="btl-usp__tile-body">
          <?php esc_html_e( 'You aren\'t just travelling -- you\'re joining a lineage. Through The Shared Ink, our communal journal, you\'ll find notes and book recommendations from every guest who came before you.', 'jaiye-journeys' ); ?>
        </p>
      </li>

      <li class="btl-usp__tile" data-delay="500">
        <h3 class="btl-usp__tile-title">
          <?php esc_html_e( 'The Narrative Arc', 'jaiye-journeys' ); ?>
        </h3>
        <p class="btl-usp__tile-body">
          <?php esc_html_e( 'It begins with a surprise luxury gift weeks before you fly, and ends six months later with a handwritten letter from your past self.', 'jaiye-journeys' ); ?>
        </p>
      </li>

    </ul>

    <script>
    (function() {
      var tiles = document.querySelectorAll('.btl-usp__tile');
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            var delay = entry.target.dataset.delay || 0;
            setTimeout(function() {
              entry.target.classList.add('btl-usp__tile--visible');
            }, parseInt(delay));
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15 });
      tiles.forEach(function(tile) { observer.observe(tile); });
    })();
    </script>
  </section><!-- /.btl-usp -->


  <!-- ============================================================
       4. READING IMAGES
       Three images flush edge to edge, no captions.
       ============================================================ -->
  <div
    class="btl-images"
    aria-hidden="true"
  >
    <div class="btl-images__grid">

      <div class="btl-images__item">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-kindle-beach.jpg' ); ?>"
          alt=""
          class="btl-images__img"
          loading="lazy"
        >
      </div>

      <div class="btl-images__item">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-reading-pool.jpg' ); ?>"
          alt=""
          class="btl-images__img"
          loading="lazy"
        >
      </div>

      <div class="btl-images__item">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-hero.jpg' ); ?>"
          alt=""
          class="btl-images__img"
          loading="lazy"
        >
      </div>

    </div>
  </div><!-- /.btl-images -->


  <!-- ============================================================
       5. THE AUTHOR'S NOTE
       Forest green bg. Logo left (40%), content right (60%).
       Stacked on mobile, two-column on desktop.
       ============================================================ -->
  <section
    class="btl-tan"
    aria-label="<?php esc_attr_e( 'The Author\'s Note — BTL virtual literary lounge', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="btl-tan__inner">

        <div class="btl-tan__logo-col">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/the-authors-note-logo.png' ); ?>"
            alt="<?php esc_attr_e( 'The Author\'s Note', 'jaiye-journeys' ); ?>"
            class="btl-tan__logo"
            loading="lazy"
          >
        </div>

        <div class="btl-tan__content">
          <span class="overline btl-tan__overline">
            <?php esc_html_e( 'Coming Soon', 'jaiye-journeys' ); ?>
          </span>
          <h2 class="btl-tan__heading">
            <?php esc_html_e( 'Before the retreats, there is the conversation.', 'jaiye-journeys' ); ?>
          </h2>
          <p class="btl-tan__body">
            <?php esc_html_e( 'The Author\'s Note is BTL\'s literary community -- a monthly space for readers to gather, hear from authors, and go deeper into the books that matter. Live author Q&As, fireside-style conversations, and in-person sessions at every retreat.', 'jaiye-journeys' ); ?>
          </p>
          <p class="btl-tan__body">
            <?php esc_html_e( 'Think curated reading lists, intimate book conversations, and early access to everything BTL. It is where the story begins.', 'jaiye-journeys' ); ?>
          </p>
          <p class="btl-tan__body">
            <?php esc_html_e( 'It is where the BTL community begins -- before anyone books a flight.', 'jaiye-journeys' ); ?>
          </p>
          <p class="btl-tan__body">
            <?php esc_html_e( 'We are building it now. We\'ll let you know when we open the doors.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div>
    </div>
  </section><!-- /.btl-tan -->


  <!-- ============================================================
       6. THE PIPELINE
       Cream bg. Three retreat cards.
       ============================================================ -->
  <section
    class="btl-pipeline"
    aria-label="<?php esc_attr_e( 'The BTL chapter plan', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <span class="overline btl-pipeline__overline">
        <?php esc_html_e( 'The BTL Chapter Plan', 'jaiye-journeys' ); ?>
      </span>
      <h2 class="btl-pipeline__heading">
        <?php esc_html_e( 'Every chapter has its season.', 'jaiye-journeys' ); ?>
      </h2>

      <ul class="btl-pipeline__grid" role="list">

        <li>
          <article class="btl-pipeline-card">
            <p class="btl-pipeline-card__label">
              <?php esc_html_e( 'The Prologue', 'jaiye-journeys' ); ?>
            </p>
            <h3 class="btl-pipeline-card__title">
              <?php esc_html_e( 'Our Soft Launch', 'jaiye-journeys' ); ?>
            </h3>
            <p class="btl-pipeline-card__date">
              <time datetime="2026-09"><?php esc_html_e( 'September / October 2026', 'jaiye-journeys' ); ?></time>
            </p>
            <p class="btl-pipeline-card__desc">
              <?php esc_html_e( 'The first in-person BTL experience. Small, intimate, and closer to home.', 'jaiye-journeys' ); ?>
            </p>
            <span class="btl-pipeline-card__pill btl-pipeline-card__pill--open">
              <?php esc_html_e( 'Open Soon', 'jaiye-journeys' ); ?>
            </span>
          </article>
        </li>

        <li>
          <article class="btl-pipeline-card">
            <p class="btl-pipeline-card__label">
              <?php esc_html_e( 'Chapter One', 'jaiye-journeys' ); ?>
            </p>
            <h3 class="btl-pipeline-card__title">
              <?php esc_html_e( 'The Essaouira Edition', 'jaiye-journeys' ); ?>
            </h3>
            <p class="btl-pipeline-card__date">
              <time datetime="2027-05"><?php esc_html_e( 'May 2027', 'jaiye-journeys' ); ?></time>
            </p>
            <p class="btl-pipeline-card__desc">
              <?php esc_html_e( 'The first full BTL residency. Moroccan coastal air, medina mornings, and long reading afternoons by the Atlantic.', 'jaiye-journeys' ); ?>
            </p>
            <span class="btl-pipeline-card__pill btl-pipeline-card__pill--horizon">
              <?php esc_html_e( 'On the Horizon', 'jaiye-journeys' ); ?>
            </span>
          </article>
        </li>

        <li>
          <article class="btl-pipeline-card">
            <p class="btl-pipeline-card__label">
              <?php esc_html_e( 'Chapter Two', 'jaiye-journeys' ); ?>
            </p>
            <h3 class="btl-pipeline-card__title">
              <?php esc_html_e( 'The Sintra Edition', 'jaiye-journeys' ); ?>
            </h3>
            <p class="btl-pipeline-card__date">
              <time datetime="2027"><?php esc_html_e( 'TBC 2027', 'jaiye-journeys' ); ?></time>
            </p>
            <p class="btl-pipeline-card__desc">
              <?php esc_html_e( 'Fairy-tale palaces, forested hills, and a literary atmosphere that feels like it was made for readers.', 'jaiye-journeys' ); ?>
            </p>
            <span class="btl-pipeline-card__pill btl-pipeline-card__pill--horizon">
              <?php esc_html_e( 'On the Horizon', 'jaiye-journeys' ); ?>
            </span>
          </article>
        </li>

      </ul>
    </div>
  </section><!-- /.btl-pipeline -->


  <!-- ============================================================
       7. WAITLIST CTA
       Deep green bg. Monogram, heading, body, salmon button.
       ============================================================ -->
  <section
    class="btl-waitlist"
    aria-label="<?php esc_attr_e( 'Join the Between the Lines waitlist', 'jaiye-journeys' ); ?>"
  >
    <img
      src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-logo-green-dark.png' ); ?>"
      alt=""
      class="btl-waitlist__logo"
      loading="lazy"
      aria-hidden="true"
    >
    <h2 class="btl-waitlist__heading">
      <?php esc_html_e( 'The best stories begin with a single page.', 'jaiye-journeys' ); ?>
    </h2>
    <p class="btl-waitlist__body">
      <?php esc_html_e( 'Join the waitlist to be first to know when retreats open, receive early access pricing, and become part of the BTL community before the doors open.', 'jaiye-journeys' ); ?>
    </p>
    <a
      href="https://tally.so/r/Gxq2JL"
      class="btl-waitlist__btn"
      target="_blank"
      rel="noopener noreferrer"
    >
      <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
    </a>
  </section><!-- /.btl-waitlist -->

</main><!-- /#main-content -->

<?php get_footer(); ?>
