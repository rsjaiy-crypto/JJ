<?php
/*
 * Template Name: JJ Edit
 *
 * Used for every standard Jaiye Journeys group trip ("Edit") page —
 * Peru, Cape Town, Islands, Bahia, and future Edits. Content is
 * hard-coded per trip — duplicate this file (or a page using it) for
 * each new Edit and edit the blocks marked "EDIT PER TRIP". See
 * HANDOVER.md for the wider theme conventions this template follows.
 *
 * Sibling template: template-btl-retreat.php (same structure, BTL
 * brand treatment — open-book pace-scale marker, named days, BTL
 * chapters cross-sell).
 */

get_header();
?>

<main id="main-content" class="site-main">

  <!-- ============================================================
       HERO — background image, eyebrow/title/intro, glance bar,
       jump nav. All inside the hero per the mockup.
       ============================================================ -->
  <header
    class="jje-hero"
    style="background-image: linear-gradient(180deg, rgba(9,57,35,0.35), rgba(8,31,28,0.75)), url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-peru.jpg' ); ?>');"
  >
    <div class="container">

      <!-- EDIT PER TRIP: eyebrow, title, intro line -->
      <p class="jje-eyebrow"><?php esc_html_e( 'The Jaiye Journeys Edit', 'jaiye-journeys' ); ?></p>
      <h1 class="jje-hero__title"><?php esc_html_e( "The Director's Cut: Peru", 'jaiye-journeys' ); ?></h1>
      <div class="jje-hero__copy">
        <p><?php esc_html_e( "Nine days across the Sacred Valley and Machu Picchu, run the way we'd actually do it, no queue, no filler, no group-trip drama.", 'jaiye-journeys' ); ?></p>
      </div>

      <!-- EDIT PER TRIP: glance bar facts, pace marker position, activity chips -->
      <div class="jje-glance">

        <div class="jje-glance__scale">
          <p class="jje-glance__label"><?php esc_html_e( 'Pace', 'jaiye-journeys' ); ?></p>
          <div class="jje-scale-row">
            <span><?php esc_html_e( 'Relaxed', 'jaiye-journeys' ); ?></span>
            <span><?php esc_html_e( 'Adventurous', 'jaiye-journeys' ); ?></span>
          </div>
          <div class="jje-scale-track">
            <!-- EDIT PER TRIP: adjust `left` below (0%–100%) to reflect this trip's pace -->
            <div class="jje-scale-marker" style="left: 68%;"></div>
          </div>
        </div>

        <div class="jje-glance__divider"></div>

        <div class="jje-glance__facts">
          <div class="jje-glance__item">
            <p><?php esc_html_e( 'Location', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'Cusco & Sacred Valley', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="jje-glance__item">
            <p><?php esc_html_e( 'Accommodation', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'Boutique hotels, curated', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="jje-glance__item">
            <p><?php esc_html_e( 'Investment from', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'TBC pp', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="jje-glance__item">
            <p><?php esc_html_e( 'Dates', 'jaiye-journeys' ); ?></p>
            <p><?php esc_html_e( 'TBC 2027', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="jje-glance__divider"></div>

        <div class="jje-glance__chips">
          <span class="jje-chip"><?php esc_html_e( 'Machu Picchu sunrise', 'jaiye-journeys' ); ?></span>
          <span class="jje-chip"><?php esc_html_e( 'Sacred Valley', 'jaiye-journeys' ); ?></span>
          <span class="jje-chip"><?php esc_html_e( 'Cusco nights', 'jaiye-journeys' ); ?></span>
        </div>

        <div class="jje-glance__cta">
          <a href="<?php echo esc_url( 'https://tally.so/r/1ApXBp' ); ?>" target="_blank" rel="noopener" class="btn btn--accent">
            <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
          </a>
        </div>

      </div><!-- /.jje-glance -->

      <nav class="jje-jump-nav" aria-label="<?php esc_attr_e( 'Page sections', 'jaiye-journeys' ); ?>">
        <a href="#location"><?php esc_html_e( 'Location', 'jaiye-journeys' ); ?></a>
        <a href="#itinerary"><?php esc_html_e( 'Itinerary', 'jaiye-journeys' ); ?></a>
        <a href="#rooms"><?php esc_html_e( 'Rooms', 'jaiye-journeys' ); ?></a>
        <a href="#investment"><?php esc_html_e( 'Investment', 'jaiye-journeys' ); ?></a>
        <a href="#faq"><?php esc_html_e( 'FAQ', 'jaiye-journeys' ); ?></a>
      </nav>

    </div><!-- /.container -->
  </header><!-- /.jje-hero -->


  <div class="jje-body">
    <div class="container container--narrow">

      <!-- ============================================================
           1. PRODUCER'S NOTE
           JJ "it girl" register: warmer, more playful than BTL.
           ============================================================ -->
      <!-- EDIT PER TRIP: producer's note copy -->
      <section id="producers-note" class="jje-section">
        <h2><?php esc_html_e( "The Producer's Note", 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "Okay, Peru. This is the one I get asked about the most, and honestly? It deserves the hype. We're doing the Sacred Valley properly, not the version where you're herded through a checklist with forty strangers and a laminated sign.", 'jaiye-journeys' ); ?></p>
        <p><?php esc_html_e( "Small group, boutique stays, and a sunrise at Machu Picchu that actually feels like yours. I've built in real downtime too, because nobody needs to be Instagram-ready at 6am every single day of their holiday.", 'jaiye-journeys' ); ?></p>
        <p><?php esc_html_e( "This is the trip for the girl who wants the bucket-list moment without the bucket-list chaos. I've handled the logistics. You just need to show up.", 'jaiye-journeys' ); ?></p>
      </section>

      <!-- EDIT PER TRIP: image break photo + caption -->
      <div class="jje-image-break jje-tone-forest">
        <span class="jje-image-break__caption"><?php esc_html_e( 'The Sacred Valley, Peru', 'jaiye-journeys' ); ?></span>
      </div>

      <!-- ============================================================
           2. WHO THIS IS FOR
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="who-for" class="jje-section">
        <h2><?php esc_html_e( 'Who This Is For', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "The friend group that's been talking about 'doing Machu Picchu' for three years and never actually booked it. The girl who wants a proper adventure but still wants a good pillow at the end of the day. Come solo and leave with a group chat, or bring the crew.", 'jaiye-journeys' ); ?></p>
      </section>

      <!-- ============================================================
           3. HOW IT WORKS
           ============================================================ -->
      <section id="how-it-works" class="jje-section">
        <h2><?php esc_html_e( 'How It Works', 'jaiye-journeys' ); ?></h2>
        <div class="jje-steps">
          <div class="jje-step-card">
            <h3><?php esc_html_e( 'Reserve', 'jaiye-journeys' ); ?></h3>
            <p><?php esc_html_e( 'Secure your spot with a holding deposit. Refundable until the cohort is confirmed.', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="jje-step-card">
            <h3><?php esc_html_e( 'Green Lit', 'jaiye-journeys' ); ?></h3>
            <p><?php esc_html_e( 'Once the minimum is reached, your place locks in and the trip is confirmed.', 'jaiye-journeys' ); ?></p>
          </div>
          <div class="jje-step-card">
            <h3><?php esc_html_e( 'Arrive', 'jaiye-journeys' ); ?></h3>
            <p><?php esc_html_e( 'Land, meet the group, and let the itinerary take it from there.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>
      </section>

      <!-- ============================================================
           4. THE LOCATION
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="location" class="jje-section">
        <h2><?php esc_html_e( 'The Location', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "Cusco is where we start, high-altitude, cobblestoned, and stunning even with jet lag. From there we move through the Sacred Valley, colour and markets and mountains on every side, before the big one: Machu Picchu itself.", 'jaiye-journeys' ); ?></p>
        <p><?php esc_html_e( "We've built in acclimatisation days, because nobody enjoys a bucket-list moment while dizzy. Every stay is chosen for comfort as much as location.", 'jaiye-journeys' ); ?></p>
      </section>

      <!-- EDIT PER TRIP: image break photo + caption -->
      <div class="jje-image-break">
        <span class="jje-image-break__caption"><?php esc_html_e( 'Machu Picchu at sunrise', 'jaiye-journeys' ); ?></span>
      </div>

      <!-- ============================================================
           5. MEET YOUR HOST
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="host" class="jje-section">
        <h2><?php esc_html_e( 'Meet Your Host', 'jaiye-journeys' ); ?></h2>
        <div class="jje-host">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Steph, founder of Jaiye Journeys', 'jaiye-journeys' ); ?>"
            class="jje-host__photo"
            loading="lazy"
          >
          <p><?php esc_html_e( "Steph has done this route more times than she can count, and she's still tweaking it every trip to make it better. She's there for the whole thing, not just the welcome drinks, so if something needs sorting, she's already on it.", 'jaiye-journeys' ); ?></p>
        </div>
      </section>

      <!-- ============================================================
           6. THE ITINERARY
           Edit day naming: numbered days.
           ============================================================ -->
      <!-- EDIT PER TRIP: every day title and description -->
      <section id="itinerary" class="jje-section">
        <h2><?php esc_html_e( 'The Itinerary', 'jaiye-journeys' ); ?></h2>

        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 1', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Arrival in Cusco', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Land, settle in, and take it easy while you acclimatise. Welcome dinner in the evening.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 2', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Cusco, Properly', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'A guided walk through the old town, San Pedro Market, and the best coffee in the city.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 3', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Into the Sacred Valley', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Pisac ruins and market in the morning, then on to a boutique stay in the heart of the valley.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 4', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Ollantaytambo', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Explore the fortress town and terraces before the scenic train toward Aguas Calientes.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 5', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Machu Picchu', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'The main event. Sunrise entry with a private guide, ahead of the crowds.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 6', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Back to Cusco, Slow', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'A relaxed return journey, free afternoon, spa time if you want it.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 7', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Free Day', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'Optional add-ons available, or just enjoy Cusco at your own pace.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 8', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Farewell Dinner', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'The whole group together for one last night, closing out the trip properly.', 'jaiye-journeys' ); ?></p>
        </div>
        <div class="jje-itin-item">
          <p class="jje-itin-item__day"><?php esc_html_e( 'Day 9', 'jaiye-journeys' ); ?></p>
          <h3><?php esc_html_e( 'Departure', 'jaiye-journeys' ); ?></h3>
          <p><?php esc_html_e( 'No-rush checkout and transfers to the airport whenever your flight is.', 'jaiye-journeys' ); ?></p>
        </div>

      </section>

      <!-- EDIT PER TRIP: image break photo + caption -->
      <div class="jje-image-break jje-tone-salmon">
        <span class="jje-image-break__caption"><?php esc_html_e( 'Golden hour over the Sacred Valley', 'jaiye-journeys' ); ?></span>
      </div>

      <!-- ============================================================
           7. THE ROOMS
           ============================================================ -->
      <!-- EDIT PER TRIP -->
      <section id="rooms" class="jje-section">
        <h2><?php esc_html_e( 'The Rooms', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( 'Boutique hotels across Cusco, the Sacred Valley, and Aguas Calientes, a mix of twins and doubles. Full room configuration and solo pricing confirmed once the group is finalised.', 'jaiye-journeys' ); ?></p>
      </section>

      <!-- ============================================================
           8. THE INVESTMENT (accordion)
           ============================================================ -->
      <!-- EDIT PER TRIP: all five accordion bodies -->
      <section id="investment" class="jje-section">
        <h2><?php esc_html_e( 'The Investment', 'jaiye-journeys' ); ?></h2>

        <details class="jje-accordion-item" open>
          <summary><?php esc_html_e( "What's Included", 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'All accommodation, daily breakfast, the Machu Picchu entry and train, guided excursions in Cusco and the Sacred Valley, and all internal transfers.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( "What's Not Included", 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Flights, travel insurance, most lunches and dinners, and optional add-on excursions.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( 'Payment Terms', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'A holding deposit secures your spot, refundable until the cohort reaches its minimum. The remaining balance is due 60 days before departure. Payment plans and Klarna available at checkout.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( 'Getting There', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Fly into Cusco (via Lima). Full transfer and flight-timing guidance is shared in your welcome pack.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( 'Next Steps', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( "Once your place is secured, you'll receive a confirmation email. Around 60 days out, you'll be added to a WhatsApp group with the rest of the cohort.", 'jaiye-journeys' ); ?></p>
        </details>
      </section>

      <!-- ============================================================
           9. GALLERY
           ============================================================ -->
      <!-- EDIT PER TRIP: replace placeholder blocks with real photography once available -->
      <section id="gallery" class="jje-section">
        <h2><?php esc_html_e( 'Gallery', 'jaiye-journeys' ); ?></h2>
        <div class="jje-gallery-grid">
          <div></div><div></div><div></div><div></div>
          <div></div><div></div><div></div><div></div>
        </div>
      </section>

      <!-- ============================================================
           10. FAQ (accordion)
           ============================================================ -->
      <!-- EDIT PER TRIP: FAQ copy is largely reusable across Edits, review per trip -->
      <section id="faq" class="jje-section">
        <h2><?php esc_html_e( 'Questions', 'jaiye-journeys' ); ?></h2>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( 'Is there a waitlist?', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Yes. Join to be notified the moment booking opens and to get first access before spaces are shared more widely.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( "What happens if the cohort doesn't reach its minimum?", 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Holding deposits are returned in full. Nothing becomes non-refundable until the trip is officially green lit.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( 'Am I responsible for my own flights?', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( 'Yes. We recommend booking as soon as your spot is confirmed. The Ticketing Desk can help you find the right fare.', 'jaiye-journeys' ); ?></p>
        </details>
        <details class="jje-accordion-item">
          <summary><?php esc_html_e( 'Are dietary requirements catered for?', 'jaiye-journeys' ); ?></summary>
          <p><?php esc_html_e( "Yes, you'll receive a preference sheet ahead of the trip.", 'jaiye-journeys' ); ?></p>
        </details>
      </section>

      <!-- ============================================================
           11. UPCOMING TRIPS YOU'LL LOVE
           Edits → cross-sell to other JJ Edits only, never BTL.
           ============================================================ -->
      <!-- EDIT PER TRIP: swap in the two Edits to cross-sell (excluding this one) -->
      <section id="upcoming" class="jje-section">
        <h2><?php esc_html_e( "Upcoming Edits You'll Love", 'jaiye-journeys' ); ?></h2>
        <div class="jje-upcoming-grid">
          <a href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>" class="jje-upcoming-card">
            <div class="jje-upcoming-card__img" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-cape-town.jpg' ); ?>');"></div>
            <div class="jje-upcoming-card__body">
              <h3><?php esc_html_e( 'The Cape Town Edit', 'jaiye-journeys' ); ?></h3>
              <p><?php esc_html_e( 'TBC 2027 · From £TBC', 'jaiye-journeys' ); ?></p>
            </div>
          </a>
          <a href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>" class="jje-upcoming-card">
            <div class="jje-upcoming-card__img" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-bali.jpg' ); ?>');"></div>
            <div class="jje-upcoming-card__body">
              <h3><?php esc_html_e( 'The Islands Cut', 'jaiye-journeys' ); ?></h3>
              <p><?php esc_html_e( 'May 2027 · From £TBC', 'jaiye-journeys' ); ?></p>
            </div>
          </a>
        </div>
      </section>

      <!-- ============================================================
           12. SIGN-OFF
           ============================================================ -->
      <section class="jje-signoff">
        <h2><?php esc_html_e( 'This One Is Ready for You', 'jaiye-journeys' ); ?></h2>
        <p><?php esc_html_e( "All that's left is your call. Reply with your dates and I'll get everything locked in. Steph x", 'jaiye-journeys' ); ?></p>
        <a href="<?php echo esc_url( 'https://tally.so/r/1ApXBp' ); ?>" target="_blank" rel="noopener" class="btn btn--accent">
          <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
        </a>
      </section>

    </div><!-- /.container -->
  </div><!-- /.jje-body -->

</main><!-- /#main-content -->

<!-- ============================================================
     MOBILE STICKY BAR — fixed to viewport bottom, mobile only
     ============================================================ -->
<!-- EDIT PER TRIP: price -->
<div class="jje-sticky-bar">
  <div class="jje-sticky-bar__price">
    <p><?php esc_html_e( 'From', 'jaiye-journeys' ); ?></p>
    <p><?php esc_html_e( 'TBC pp', 'jaiye-journeys' ); ?></p>
  </div>
  <a href="<?php echo esc_url( 'https://tally.so/r/1ApXBp' ); ?>" target="_blank" rel="noopener" class="btn btn--accent btn--sm">
    <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
  </a>
</div>

<style>
/* ------------------------------------------------------------------
   JJ Edit template — all component styles
   Prefix: jje- (JJ Edit). Structurally identical to
   template-btl-retreat.php's btlr- rules; kept as a separate,
   duplicated stylesheet per trip-template so each brand's page can
   be tuned independently without touching the other.
   ------------------------------------------------------------------ */

/* ── Hero ─────────────────────────────────────────────────────────── */

.jje-hero {
  position: relative;
  background-color: var(--color-forest);
  background-size: cover;
  background-position: center;
  padding-block: var(--space-16) var(--space-10);
  color: var(--color-cream);
}

.jje-eyebrow {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--color-salmon);
  margin-bottom: var(--space-2);
}

.jje-hero__title {
  font-family: var(--font-heading);
  font-size: var(--text-4xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  margin-bottom: var(--space-8);
}

.jje-hero__copy p {
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

.jje-glance {
  background: var(--color-cream);
  border-radius: var(--radius-lg);
  padding: var(--space-5) var(--space-6);
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: var(--space-6);
}

.jje-glance__divider {
  width: 0.5px;
  align-self: stretch;
  background: color-mix(in srgb, var(--color-forest) 15%, transparent);
}

.jje-glance__scale {
  width: 150px;
  flex-shrink: 0;
}

.jje-glance__label {
  font-size: 10.5px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--color-forest);
  opacity: 0.55;
  margin: 0 0 var(--space-2);
}

.jje-scale-row {
  display: flex;
  justify-content: space-between;
  font-size: 9.5px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--color-forest);
  margin-bottom: var(--space-2);
}

.jje-scale-track {
  position: relative;
  height: 2px;
  background: var(--color-sage);
  border-radius: var(--radius-sm);
}

/* Plain filled-circle marker — no icon. Straightforward intensity
   indicator, not a branded literary device (that stays on BTL only). */
.jje-scale-marker {
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--color-salmon);
}

.jje-glance__facts {
  display: flex;
  gap: var(--space-5);
  flex-wrap: wrap;
}

.jje-glance__item p:first-child {
  font-size: 10.5px;
  color: var(--color-forest);
  opacity: 0.6;
  margin: 0 0 3px;
}

.jje-glance__item p:last-child {
  font-size: 13.5px;
  font-weight: var(--fw-semibold);
  color: var(--color-forest);
  margin: 0;
  white-space: nowrap;
}

.jje-glance__chips {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  flex-wrap: wrap;
}

.jje-chip {
  font-size: 11px;
  color: var(--color-forest);
  background: color-mix(in srgb, var(--color-forest) 8%, transparent);
  border-radius: var(--radius-full);
  padding: 3px 9px;
  white-space: nowrap;
}

.jje-glance__cta {
  margin-left: auto;
}

/* ── Jump nav ─────────────────────────────────────────────────────── */

.jje-jump-nav {
  margin-top: var(--space-4);
  display: flex;
  gap: var(--space-5);
  flex-wrap: wrap;
}

.jje-jump-nav a {
  font-size: var(--text-sm);
  color: var(--color-cream);
  opacity: 0.8;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.jje-jump-nav a:hover {
  color: var(--color-salmon);
  opacity: 1;
}

/* ── Page body / sections ─────────────────────────────────────────── */

.jje-body {
  padding-block: var(--section-gap);
}

.jje-section {
  margin-bottom: var(--space-12);
}

.jje-section h2 {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-4);
}

.jje-section p {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.7;
  color: var(--color-text);
  opacity: 0.85;
  margin: 0 0 var(--space-3);
}

/* ── Image break bands ────────────────────────────────────────────── */

.jje-image-break {
  height: 320px;
  border-radius: var(--radius-lg);
  margin-bottom: var(--space-12);
  background: var(--color-sage);
  background-size: cover;
  background-position: center;
  position: relative;
  overflow: hidden;
}

.jje-image-break.jje-tone-forest { background-color: var(--color-forest); }
.jje-image-break.jje-tone-salmon { background-color: var(--color-salmon); }

.jje-image-break__caption {
  position: absolute;
  left: var(--space-5);
  bottom: var(--space-4);
  color: var(--color-cream);
  font-family: var(--font-heading);
  font-size: var(--text-md);
}

/* ── How it works: step cards ─────────────────────────────────────── */

.jje-steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-4);
}

.jje-step-card {
  background: var(--color-cream);
  border-radius: var(--radius-md);
  padding: var(--space-5);
}

.jje-step-card h3 {
  font-family: var(--font-heading);
  font-size: var(--text-lg);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-2);
}

.jje-step-card p {
  font-size: var(--text-sm);
  margin: 0;
}

/* ── Host ─────────────────────────────────────────────────────────── */

.jje-host {
  display: flex;
  gap: var(--space-5);
  align-items: center;
}

.jje-host__photo {
  width: 88px;
  height: 88px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  background: var(--color-sage);
}

/* ── Itinerary ────────────────────────────────────────────────────── */

.jje-itin-item {
  border-bottom: 0.5px solid color-mix(in srgb, var(--color-forest) 15%, transparent);
  padding-block: var(--space-4);
}

.jje-itin-item:last-child {
  border-bottom: none;
}

.jje-itin-item__day {
  font-size: 11px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--color-salmon);
  margin: 0;
}

.jje-itin-item h3 {
  font-family: var(--font-heading);
  font-size: var(--text-lg);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin: 4px 0 var(--space-2);
}

/* ── Accordion ────────────────────────────────────────────────────── */

.jje-accordion-item {
  border-bottom: 0.5px solid color-mix(in srgb, var(--color-forest) 15%, transparent);
}

.jje-accordion-item summary {
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

.jje-accordion-item summary::-webkit-details-marker {
  display: none;
}

.jje-accordion-item summary::after {
  content: '+';
  font-size: 18px;
  color: var(--color-salmon);
  flex-shrink: 0;
  margin-left: var(--space-4);
}

.jje-accordion-item[open] summary::after {
  content: '\2212';
}

.jje-accordion-item p {
  padding-bottom: var(--space-4);
}

/* ── Gallery ──────────────────────────────────────────────────────── */

.jje-gallery-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-2);
}

.jje-gallery-grid div {
  aspect-ratio: 1;
  background: var(--color-sage);
  border-radius: var(--radius-sm);
  opacity: 0.6;
}

/* ── Upcoming trips ───────────────────────────────────────────────── */

.jje-upcoming-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
}

.jje-upcoming-card {
  display: block;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--color-cream);
}

.jje-upcoming-card__img {
  height: 120px;
  background-color: var(--color-forest);
  background-size: cover;
  background-position: center;
}

.jje-upcoming-card__body {
  padding: var(--space-4);
}

.jje-upcoming-card__body h3 {
  font-family: var(--font-heading);
  font-size: var(--text-lg);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: 4px;
}

.jje-upcoming-card__body p {
  font-size: 12.5px;
  margin: 0;
}

/* ── Sign-off ─────────────────────────────────────────────────────── */

.jje-signoff {
  background: var(--color-forest);
  color: var(--color-cream);
  border-radius: var(--radius-lg);
  padding: var(--space-10);
  text-align: center;
}

.jje-signoff h2 {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  margin-bottom: var(--space-3);
}

.jje-signoff p {
  color: var(--color-cream);
  opacity: 0.85;
  margin-bottom: var(--space-6);
}

/* ── Mobile sticky bar ────────────────────────────────────────────── */

.jje-sticky-bar {
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

.jje-sticky-bar__price p:first-child {
  font-size: 10.5px;
  opacity: 0.7;
  margin: 0;
}

.jje-sticky-bar__price p:last-child {
  font-size: var(--text-sm);
  font-weight: var(--fw-semibold);
  margin: 2px 0 0;
}

/* ── Breakpoints ──────────────────────────────────────────────────── */

@media (max-width: 900px) {
  .jje-steps {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .jje-hero {
    padding-block: var(--space-10) var(--space-6);
  }

  .jje-hero__title {
    font-size: var(--text-3xl);
    margin-bottom: var(--space-5);
  }

  .jje-hero__copy p {
    margin-bottom: var(--space-5);
  }

  .jje-glance {
    padding: var(--space-4);
    gap: var(--space-4);
  }

  .jje-glance__divider {
    display: none;
  }

  .jje-glance__cta {
    margin-left: 0;
    width: 100%;
  }

  .jje-glance__cta .btn {
    width: 100%;
  }

  .jje-jump-nav {
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: var(--space-4);
  }

  .jje-image-break {
    height: 180px;
    margin-bottom: var(--space-8);
  }

  .jje-image-break__caption {
    font-size: var(--text-sm);
  }

  .jje-host {
    flex-direction: column;
    text-align: center;
  }

  .jje-gallery-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .jje-upcoming-grid {
    grid-template-columns: 1fr;
  }

  .jje-signoff {
    padding: var(--space-6) var(--space-5);
  }

  /* Reserve room at the bottom of the viewport for the sticky bar */
  .jje-body {
    padding-bottom: calc(var(--section-gap) + 72px);
  }

  .jje-sticky-bar {
    display: flex;
  }
}
</style>

<?php get_footer(); ?>
