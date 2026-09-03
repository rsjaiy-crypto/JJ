<?php
/*
 * Template Name: BTL Retreat
 *
 * Used for every Between the Lines chapter page (Marrakech, Sintra, and
 * future chapters). Content is hard-coded per trip — duplicate this file
 * (or a page using it) for each new chapter and edit the blocks marked
 * "EDIT PER TRIP". See HANDOVER.md for the wider theme conventions this
 * template follows.
 *
 * Sibling template: template-jj-edit.php (same structure, JJ Edit brand
 * treatment — plain pace-scale marker, numbered days, JJ Edits cross-sell).
 */

get_header();
?>

<main id="main-content" class="site-main">

  <!-- ============================================================
       HERO — background image, eyebrow/title/intro, glance bar,
       jump nav. All inside the hero per the mockup.
       ============================================================ -->
  <header
    class="btlr-hero"
    style="background-image: linear-gradient(180deg, rgba(9,57,35,0.35), rgba(8,31,28,0.75)), url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-hero.jpg' ); ?>');"
  >
    <div class="container">

      <!-- EDIT PER TRIP: eyebrow, title, intro line -->
      <p class="btlr-eyebrow"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></p>
      <h1 class="btlr-hero__title"><?php esc_html_e( 'Chapter One: Marrakech', 'jaiye-journeys' ); ?></h1>
      <div class="btlr-hero__copy">
        <p><?php esc_html_e( 'Six days in a private riad near Bab Aylane, built around one loud city and a door that shuts it out completely.', 'jaiye-journeys' ); ?></p>
      </div>

      <!-- EDIT PER TRIP: glance bar facts, pace marker position, activity chips -->
      <div class="btlr-glance">

        <div class="btlr-glance__scale">
          <p class="btlr-glance__label"><?php esc_html_e( 'Pace', 'jaiye-journeys' ); ?></p>
          <div class="btlr-scale-row">
            <span><?php esc_html_e( 'In the margins', 'jaiye-journeys' ); ?></span>
            <span><?php esc_html_e( 'Off the page', 'jaiye-journeys' ); ?></span>
          </div>
          <div class="btlr-scale-track">
            <!-- EDIT PER TRIP: adjust `left` below (0%–100%) to reflect this chapter's pace -->
            <div class="btlr-scale-marker" style="left: 58%;">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 6.5C10.5 5.2 8.2 4.5 6 4.5c-1.1 0-2.1.15-3 .45v13c.9-.3 1.9-.45 3-.45 2.2 0 4.5.7 6 2 1.5-1.3 3.8-2 6-2 1.1 0 2.1.15 3 .45v-13c-.9-.3-1.9-.45-3-.45-2.2 0-4.5.7-6 2z" stroke="#093923" stroke-width="1.4" stroke-linejoin="round"/>
                <path d="M12 6.5V20" stroke="#093923" stroke-width="1.4"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="btlr-glance__divider"></div>

        <div class="btlr-glance__facts">
          <div class="btlr-glance__item">
            <p><?php esc_html_e( 'Location', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'Marrakech', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="btlr-glance__item">
            <p><?php esc_html_e( 'Accommodation', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'Palazzo Montefiore', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="btlr-glance__item">
            <p><?php esc_html_e( 'Investment from', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'TBC pp', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="btlr-glance__item">
            <p><?php esc_html_e( 'Dates', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( '18–23 Mar 2027', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="btlr-glance__divider"></div>

        <div class="btlr-glance__chips">
          <span class="btlr-chip"><?php esc_html_e( 'Agafay evening', 'jaiye-journeys' ); ?></span>
          <span class="btlr-chip"><?php esc_html_e( 'Riad hammam', 'jaiye-journeys' ); ?></span>
          <span class="btlr-chip"><?php esc_html_e( 'Souks & garden', 'jaiye-journeys' ); ?></span>
        </div>

        <div class="btlr-glance__cta">
          <a href="<?php echo esc_url( 'https://tally.so/r/Gxq2JL' ); ?>" target="_blank" rel="noopener" class="btn btn--accent">
            <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
          </a>
        </div>

      </div><!-- /.btlr-glance -->

      <nav class="btlr-jump-nav" aria-label="<?php esc_attr_e( 'Page sections', 'jaiye-journeys' ); ?>">
        <a href="#location"><?php esc_html_e( 'Location', 'jaiye-journeys' ); ?></a>
        <a href="#itinerary"><?php esc_html_e( 'Itinerary', 'jaiye-journeys' ); ?></a>
        <a href="#rooms"><?php esc_html_e( 'Rooms', 'jaiye-journeys' ); ?></a>
        <a href="#investment"><?php esc_html_e( 'Investment', 'jaiye-journeys' ); ?></a>
        <a href="#faq"><?php esc_html_e( 'FAQ', 'jaiye-journeys' ); ?></a>
      </nav>

    </div><!-- /.container -->
  </header><!-- /.btlr-hero -->


  <div class="btlr-body">
    <div class="container container--narrow">

      <!-- ============================================================
           1. PRODUCER'S NOTE
           ============================================================ -->
      <!-- EDIT PER TRIP: producer's note copy, literary/editorial voice -->
      <section id="producers-note" class="btlr-section">
        <h2><?php esc_html_e( "The Producer's Note", 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "For this first chapter, I've designed Marrakech around a single contrast: the loudest city I know, and a door that shuts it out completely.", 'jaiye-journeys' ); ?></p>
        <p><?php esc_html_e( "Palazzo Montefiore sits in the medina near Bab Aylane, behind walls that hold a private hammam, a sun terrace, and a rooftop looking out over the rooftops of the old city. You will not hear the souks from inside. That's not an accident, it's the entire point of a riad.", 'jaiye-journeys' ); ?></p>
        <p><?php esc_html_e( "Most of the week stays close in, restful, and unhurried. One day leaves the city behind entirely for the Agafay, a landscape that looks more like the moon than a desert. This isn't a highlights tour. It's what I would do myself, given a week here and nowhere else to be.", 'jaiye-journeys' ); ?></p>
      </section>

      <!-- EDIT PER TRIP: image break photo + caption -->
      <div class="btlr-image-break btlr-tone-forest">
        <span class="btlr-image-break__caption"><?php esc_html_e( 'Palazzo Montefiore, Marrakech', 'jaiye-journeys' ); ?></span>
      </div>

      <!-- ============================================================
           2. WHO THIS IS FOR
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="who-for" class="btlr-section">
        <h2><?php esc_html_e( 'Who This Is For', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "The woman who's done the group trip where someone didn't pay on time and she ended up managing everyone's feelings on her own holiday. She's not looking to be convinced of rest, she's looking for someone who arranges it properly. Solo, or with the one friend who actually reads.", 'jaiye-journeys' ); ?></p>
      </section>

      <!-- ============================================================
           3. HOW IT WORKS
           ============================================================ -->
      <section id="how-it-works" class="btlr-section">
        <h2><?php esc_html_e( 'How It Works', 'jaiye-journeys' ); ?></h2>
        <div class="btlr-steps">
          <div class="btlr-step-card">
            <h3><?php esc_html_e( 'Reserve', 'jaiye-journeys' ); ?></h3>
            <p><?php esc_html_e( 'Secure your spot with a holding deposit. Refundable until the cohort is confirmed.', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="btlr-step-card">
            <h3><?php esc_html_e( 'Green Lit', 'jaiye-journeys' ); ?></h3>
            <p><?php esc_html_e( 'Once the minimum is reached, your place locks in and the villa contract is signed.', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="btlr-step-card">
            <h3><?php esc_html_e( 'Arrive', 'jaiye-journeys' ); ?></h3>
            <p><?php esc_html_e( 'Land, hand over your bags, and let the week take it from there.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>
      </section>

      <!-- ============================================================
           4. THE LOCATION
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="location" class="btlr-section">
        <h2><?php esc_html_e( 'The Location', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( 'Marrakech holds two cities at once: the medina, dense and alive with the souks, and the quiet interior of a riad built to shut all of it out. Palazzo Montefiore sits within walking distance of Bab Aylane, twelve rooms arranged around a courtyard, with an in-house hammam and a rooftop that catches the evening light over the old city.', 'jaiye-journeys' ); ?></p>
        <p><?php esc_html_e( 'Beyond the riad, the Agafay desert sits under wide open sky less than an hour away, a landscape that feels like nowhere else on the itinerary.', 'jaiye-journeys' ); ?></p>
      </section>

      <!-- EDIT PER TRIP: image break photo + caption -->
      <div class="btlr-image-break">
        <span class="btlr-image-break__caption"><?php esc_html_e( 'The Agafay, an hour from the medina', 'jaiye-journeys' ); ?></span>
      </div>

      <!-- ============================================================
           5. MEET YOUR HOST
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="host" class="btlr-section">
        <h2><?php esc_html_e( 'Meet Your Host', 'jaiye-journeys' ); ?></h2>
        <div class="btlr-host">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Steph, founder of Jaiye Journeys and host of Between the Lines', 'jaiye-journeys' ); ?>"
            class="btlr-host__photo"
            loading="lazy"
          >
          <p><?php esc_html_e( "Marrakech is the city Steph knows best of anywhere she's travelled. She's on-site for the full retreat, not dropping in for the welcome dinner and leaving the rest to a local team. Every choice this week comes from somewhere she's actually stood.", 'jaiye-journeys' ); ?></p>
        </div>
      </section>

      <!-- ============================================================
           6. THE ITINERARY
           BTL day naming: The Prologue / Grounding / named days /
           The Crescendo / The Epilogue.
           ============================================================ -->
      <!-- EDIT PER TRIP: every day label, title, and description -->
      <section id="itinerary" class="btlr-section">
        <h2><?php esc_html_e( 'The Itinerary', 'jaiye-journeys' ); ?></h2>

        <div class="btlr-itin-item">
          <p class="btlr-itin-item__day"><?php esc_html_e( 'Day One', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'The Prologue', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Arrival at Palazzo Montefiore. A welcome gala on the rooftop, "Dust & Gold" neutrals, as the city\'s noise falls away behind you.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="btlr-itin-item">
          <p class="btlr-itin-item__day"><?php esc_html_e( 'Day Two', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Grounding', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'An unstructured day. No itinerary to follow, just the riad, the courtyard, and time to arrive properly.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="btlr-itin-item">
          <p class="btlr-itin-item__day"><?php esc_html_e( 'Day Three', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Secrets of the Souks', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Le Jardin Secret, a hidden Saadian-era garden, followed by a guided walk through the souks and dinner in the medina.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="btlr-itin-item">
          <p class="btlr-itin-item__day"><?php esc_html_e( 'Day Four', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'The Agafay', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( "The chapter's signature day. A private desert camp from mid-afternoon: camel or quad ride at sunset, hammam, fire dancing, and Gnawa music under the stars.", 'jaiye-journeys' ); ?></p>
        </div>
        <div class="btlr-itin-item">
          <p class="btlr-itin-item__day"><?php esc_html_e( 'Day Five', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'The Crescendo', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Book club on the rooftop. Farewell afternoon tea at the Royal Mansour, closing the week on a quiet, looked-after note.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="btlr-itin-item">
          <p class="btlr-itin-item__day"><?php esc_html_e( 'Day Six', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'The Epilogue', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'No-rush departure. Stay by the pool until the last possible moment.', 'jaiye-journeys' ); ?></p>
        </div>

      </section>

      <!-- EDIT PER TRIP: image break photo + caption -->
      <div class="btlr-image-break btlr-tone-salmon">
        <span class="btlr-image-break__caption"><?php esc_html_e( 'Fire dancing under the Agafay sky', 'jaiye-journeys' ); ?></span>
      </div>

      <!-- ============================================================
           7. THE ROOMS
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="rooms" class="btlr-section">
        <h2><?php esc_html_e( 'The Rooms', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( 'Twelve rooms across the riad, a mix of doubles and suites. Full room configuration and solo pricing confirmed once the buyout is finalised.', 'jaiye-journeys' ); ?></p>
      </section>

      <!-- ============================================================
           8. THE INVESTMENT (accordion)
           ============================================================ -->
      <!-- EDIT PER TRIP: all five accordion bodies -->
      <section id="investment" class="btlr-section">
        <h2><?php esc_html_e( 'The Investment', 'jaiye-journeys' ); ?></h2>

        <details class="btlr-accordion-item" open>
          <summary><?php esc_html_e( "What's Included", 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Full riad buyout, all breakfasts and dinners, the Agafay desert day in full, guided souks and Secret Garden visit, Royal Mansour afternoon tea, all internal transfers.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( "What's Not Included", 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Flights, travel insurance, additional spa treatments, and shopping.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( 'Payment Terms', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'A holding deposit secures your spot, refundable until the cohort reaches its minimum. The remaining balance is due 60 days before departure. Payment plans and Klarna available at checkout.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( 'Getting There', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Arrive from 1pm. The riad sits within the medina near Bab Aylane; full transfer details are shared in your welcome pack.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( 'Next Steps', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( "Once your place is secured, you'll receive a confirmation email. Around 60 days out, you'll be added to a WhatsApp group with the rest of the cohort.", 'jaiye-journeys' ); ?></p>
        </details>
      </section>

      <!-- ============================================================
           9. GALLERY
           ============================================================ -->
      <!-- EDIT PER TRIP: replace placeholder blocks with real photography once available -->
      <section id="gallery" class="btlr-section">
        <h2><?php esc_html_e( 'Gallery', 'jaiye-journeys' ); ?></h2>
        <div class="btlr-gallery-grid">
          <div></div><div></div><div></div><div></div>
          <div></div><div></div><div></div><div></div>
        </div>
      </section>

      <!-- ============================================================
           10. FAQ (accordion)
           ============================================================ -->
      <!-- EDIT PER TRIP: FAQ copy is largely reusable across BTL chapters, review per trip -->
      <section id="faq" class="btlr-section">
        <h2><?php esc_html_e( 'Questions', 'jaiye-journeys' ); ?></h2>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( 'Is there a waitlist?', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Yes. Join to be notified the moment booking opens and to get first access before spaces are shared more widely.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( "What happens if the cohort doesn't reach its minimum?", 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Holding deposits are returned in full. Nothing becomes non-refundable until the retreat is officially green lit.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( 'Am I responsible for my own flights?', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Yes. We recommend booking as soon as your spot is confirmed. The Ticketing Desk can help you find the right fare.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="btlr-accordion-item">
          <summary><?php esc_html_e( 'Are dietary requirements catered for?', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( "Yes, you'll receive a preference sheet ahead of the retreat.", 'jaiye-journeys' ); ?></p>
        </details>
      </section>

      <!-- ============================================================
           11. UPCOMING TRIPS YOU'LL LOVE
           BTL → cross-sell to other BTL chapters only, never JJ Edits.
           ============================================================ -->
      <!-- EDIT PER TRIP: swap in the two chapters to cross-sell (excluding this one) -->
      <section id="upcoming" class="btlr-section">
        <h2><?php esc_html_e( "Upcoming Chapters You'll Love", 'jaiye-journeys' ); ?></h2>
        <div class="btlr-upcoming-grid">
          <a href="<?php echo esc_url( home_url( '/between-the-lines/' ) ); ?>" class="btlr-upcoming-card">
            <div class="btlr-upcoming-card__img" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-btl-prologue.jpg' ); ?>');"></div>
            <div class="btlr-upcoming-card__body">
              <h3><?php esc_html_e( 'BTL Chapter Two: Sintra', 'jaiye-journeys' ); ?></h3>
              <p><?php esc_html_e( 'September 2027 · From £TBC', 'jaiye-journeys' ); ?></p>
            </div>
          </a>
          <a href="<?php echo esc_url( home_url( '/between-the-lines/' ) ); ?>" class="btlr-upcoming-card">
            <div class="btlr-upcoming-card__img" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/btl-kindle-beach.jpg' ); ?>');"></div>
            <div class="btlr-upcoming-card__body">
              <h3><?php esc_html_e( 'The Prologue', 'jaiye-journeys' ); ?></h3>
              <p><?php esc_html_e( "Sept/Oct 2026 · art'otel Hoxton", 'jaiye-journeys' ); ?></p>
            </div>
          </a>
        </div>
      </section>

      <!-- ============================================================
           12. SIGN-OFF
           ============================================================ -->
      <section class="btlr-signoff">
        <h2><?php esc_html_e( 'This Chapter Is Ready for You', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "All that's left is your call. Reply with your dates and I'll get everything locked in. Steph x", 'jaiye-journeys' ); ?></p>
        <a href="<?php echo esc_url( 'https://tally.so/r/Gxq2JL' ); ?>" target="_blank" rel="noopener" class="btn btn--accent">
          <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
        </a>
      </section>

    </div><!-- /.container -->
  </div><!-- /.btlr-body -->

</main><!-- /#main-content -->

<!-- ============================================================
     MOBILE STICKY BAR — fixed to viewport bottom, mobile only
     ============================================================ -->
<!-- EDIT PER TRIP: price -->
<div class="btlr-sticky-bar">
  <div class="btlr-sticky-bar__price">
    <p><?php esc_html_e( 'From', 'jaiye-journeys' ); ?></p>
    <p><?php esc_html_e( 'TBC pp', 'jaiye-journeys' ); ?></p>
  </div>
  <a href="<?php echo esc_url( 'https://tally.so/r/Gxq2JL' ); ?>" target="_blank" rel="noopener" class="btn btn--accent btn--sm">
    <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
  </a>
</div>

<style>
/* ------------------------------------------------------------------
   BTL Retreat template — all component styles
   Prefix: btlr- (kept distinct from between-the-lines.php's btl-
   prefix, which belongs to the standalone BTL landing page).
   ------------------------------------------------------------------ */

/* ── Hero ─────────────────────────────────────────────────────────── */

.btlr-hero {
  position: relative;
  background-color: var(--color-forest);
  background-size: cover;
  background-position: center;
  padding-block: var(--space-16) var(--space-10);
  color: var(--color-cream);
}

.btlr-eyebrow {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--color-salmon);
  margin-bottom: var(--space-2);
}

.btlr-hero__title {
  font-family: var(--font-heading);
  font-size: var(--text-4xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  margin-bottom: var(--space-8);
}

.btlr-hero__copy p {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.7;
  color: var(--color-cream);
  opacity: 0.9;
  max-width: 46ch;
  margin: 0 0 var(--space-8);
}

/* ── Glance bar ───────────────────────────────────────────────────── */

.btlr-glance {
  background: var(--color-cream);
  border-radius: var(--radius-lg);
  padding: var(--space-5) var(--space-6);
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: var(--space-6);
}

.btlr-glance__divider {
  width: 0.5px;
  align-self: stretch;
  background: color-mix(in srgb, var(--color-forest) 15%, transparent);
}

.btlr-glance__scale {
  width: 150px;
  flex-shrink: 0;
}

.btlr-glance__label {
  font-size: 10.5px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--color-forest);
  opacity: 0.55;
  margin: 0 0 var(--space-2);
}

.btlr-scale-row {
  display: flex;
  justify-content: space-between;
  font-size: 9.5px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--color-forest);
  margin-bottom: var(--space-2);
}

.btlr-scale-track {
  position: relative;
  height: 2px;
  background: var(--color-sage);
  border-radius: var(--radius-sm);
}

.btlr-scale-marker {
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: var(--color-cream);
  border: 1.5px solid var(--color-forest);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btlr-scale-marker svg {
  width: 15px;
  height: 15px;
}

.btlr-glance__facts {
  display: flex;
  gap: var(--space-5);
  flex-wrap: wrap;
}

.btlr-glance__item p:first-child {
  font-size: 10.5px;
  color: var(--color-forest);
  opacity: 0.6;
  margin: 0 0 3px;
}

.btlr-glance__item p:last-child {
  font-size: 13.5px;
  font-weight: var(--fw-semibold);
  color: var(--color-forest);
  margin: 0;
  white-space: nowrap;
}

.btlr-glance__chips {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  flex-wrap: wrap;
}

.btlr-chip {
  font-size: 11px;
  color: var(--color-forest);
  background: color-mix(in srgb, var(--color-forest) 8%, transparent);
  border-radius: var(--radius-full);
  padding: 3px 9px;
  white-space: nowrap;
}

.btlr-glance__cta {
  margin-left: auto;
}

/* ── Jump nav ─────────────────────────────────────────────────────── */

.btlr-jump-nav {
  margin-top: var(--space-4);
  display: flex;
  gap: var(--space-5);
  flex-wrap: wrap;
}

.btlr-jump-nav a {
  font-size: var(--text-sm);
  color: var(--color-cream);
  opacity: 0.8;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.btlr-jump-nav a:hover {
  color: var(--color-salmon);
  opacity: 1;
}

/* ── Page body / sections ─────────────────────────────────────────── */

.btlr-body {
  padding-block: var(--section-gap);
}

.btlr-section {
  margin-bottom: var(--space-12);
}

.btlr-section h2 {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-4);
}

.btlr-section p {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.7;
  color: var(--color-text);
  opacity: 0.85;
  margin: 0 0 var(--space-3);
}

/* ── Image break bands ────────────────────────────────────────────── */

.btlr-image-break {
  height: 320px;
  border-radius: var(--radius-lg);
  margin-bottom: var(--space-12);
  background: var(--color-sage);
  background-size: cover;
  background-position: center;
  position: relative;
  overflow: hidden;
}

.btlr-image-break.btlr-tone-forest { background-color: var(--color-forest); }
.btlr-image-break.btlr-tone-salmon { background-color: var(--color-salmon); }

.btlr-image-break__caption {
  position: absolute;
  left: var(--space-5);
  bottom: var(--space-4);
  color: var(--color-cream);
  font-family: var(--font-heading);
  font-size: var(--text-md);
}

/* ── How it works: step cards ─────────────────────────────────────── */

.btlr-steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-4);
}

.btlr-step-card {
  background: var(--color-cream);
  border-radius: var(--radius-md);
  padding: var(--space-5);
}

.btlr-step-card h3 {
  font-family: var(--font-heading);
  font-size: var(--text-lg);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-2);
}

.btlr-step-card p {
  font-size: var(--text-sm);
  margin: 0;
}

/* ── Host ─────────────────────────────────────────────────────────── */

.btlr-host {
  display: flex;
  gap: var(--space-5);
  align-items: center;
}

.btlr-host__photo {
  width: 88px;
  height: 88px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  background: var(--color-sage);
}

/* ── Itinerary ────────────────────────────────────────────────────── */

.btlr-itin-item {
  border-bottom: 0.5px solid color-mix(in srgb, var(--color-forest) 15%, transparent);
  padding-block: var(--space-4);
}

.btlr-itin-item:last-child {
  border-bottom: none;
}

.btlr-itin-item__day {
  font-size: 11px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--color-salmon);
  margin: 0;
}

.btlr-itin-item h3 {
  font-family: var(--font-heading);
  font-size: var(--text-lg);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin: 4px 0 var(--space-2);
}

/* ── Accordion ────────────────────────────────────────────────────── */

.btlr-accordion-item {
  border-bottom: 0.5px solid color-mix(in srgb, var(--color-forest) 15%, transparent);
}

.btlr-accordion-item summary {
  padding-block: var(--space-4);
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-semibold);
  cursor: pointer;
  list-style: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--color-deep-green);
}

.btlr-accordion-item summary::-webkit-details-marker {
  display: none;
}

.btlr-accordion-item summary::after {
  content: '+';
  font-size: 18px;
  color: var(--color-salmon);
  flex-shrink: 0;
  margin-left: var(--space-4);
}

.btlr-accordion-item[open] summary::after {
  content: '\2212';
}

.btlr-accordion-item p {
  padding-bottom: var(--space-4);
}

/* ── Gallery ──────────────────────────────────────────────────────── */

.btlr-gallery-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-2);
}

.btlr-gallery-grid div {
  aspect-ratio: 1;
  background: var(--color-sage);
  border-radius: var(--radius-sm);
  opacity: 0.6;
}

/* ── Upcoming trips ───────────────────────────────────────────────── */

.btlr-upcoming-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
}

.btlr-upcoming-card {
  display: block;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--color-cream);
}

.btlr-upcoming-card__img {
  height: 120px;
  background-color: var(--color-forest);
  background-size: cover;
  background-position: center;
}

.btlr-upcoming-card__body {
  padding: var(--space-4);
}

.btlr-upcoming-card__body h3 {
  font-family: var(--font-heading);
  font-size: var(--text-lg);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: 4px;
}

.btlr-upcoming-card__body p {
  font-size: 12.5px;
  margin: 0;
}

/* ── Sign-off ─────────────────────────────────────────────────────── */

.btlr-signoff {
  background: var(--color-forest);
  color: var(--color-cream);
  border-radius: var(--radius-lg);
  padding: var(--space-10);
  text-align: center;
}

.btlr-signoff h2 {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  margin-bottom: var(--space-3);
}

.btlr-signoff p {
  color: var(--color-cream);
  opacity: 0.85;
  margin-bottom: var(--space-6);
}

/* ── Mobile sticky bar ────────────────────────────────────────────── */

.btlr-sticky-bar {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--color-forest);
  color: var(--color-cream);
  padding: var(--space-3) var(--space-5);
  align-items: center;
  justify-content: space-between;
  z-index: var(--z-nav);
  border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.btlr-sticky-bar__price p:first-child {
  font-size: 10.5px;
  opacity: 0.7;
  margin: 0;
}

.btlr-sticky-bar__price p:last-child {
  font-size: var(--text-sm);
  font-weight: var(--fw-semibold);
  margin: 2px 0 0;
}

/* ── Breakpoints ──────────────────────────────────────────────────── */

@media (max-width: 900px) {
  .btlr-steps {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .btlr-hero {
    padding-block: var(--space-10) var(--space-6);
  }

  .btlr-hero__title {
    font-size: var(--text-3xl);
    margin-bottom: var(--space-5);
  }

  .btlr-hero__copy p {
    margin-bottom: var(--space-5);
  }

  .btlr-glance {
    padding: var(--space-4);
    gap: var(--space-4);
  }

  .btlr-glance__divider {
    display: none;
  }

  .btlr-glance__cta {
    margin-left: 0;
    width: 100%;
  }

  .btlr-glance__cta .btn {
    width: 100%;
  }

  .btlr-jump-nav {
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: var(--space-4);
  }

  .btlr-image-break {
    height: 180px;
    margin-bottom: var(--space-8);
  }

  .btlr-image-break__caption {
    font-size: var(--text-sm);
  }

  .btlr-host {
    flex-direction: column;
    text-align: center;
  }

  .btlr-gallery-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .btlr-upcoming-grid {
    grid-template-columns: 1fr;
  }

  .btlr-signoff {
    padding: var(--space-6) var(--space-5);
  }

  /* Reserve room at the bottom of the viewport for the sticky bar */
  .btlr-body {
    padding-bottom: calc(var(--section-gap) + 72px);
  }

  .btlr-sticky-bar {
    display: flex;
  }
}
</style>

<?php get_footer(); ?>
