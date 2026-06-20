<?php
/*
 * Template Name: Private Groups & Celebrations
 */

get_header();
?>

<main id="main-content" class="site-main pgc-page">

  <!-- ============================================================
       1. HERO
       Forest green bg (matches Our Journeys hero). Clean, single column.
       ============================================================ -->
  <header
    class="pgc-hero"
    aria-label="<?php esc_attr_e( 'Private Groups & Celebrations', 'jaiye-journeys' ); ?>"
  >
    <video
      class="pgc-hero__video"
      autoplay
      muted
      loop
      playsinline
      aria-hidden="true"
    >
      <source src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/milestone-birthday.mov' ); ?>" type="video/mp4">
    </video>
    <div class="pgc-hero__overlay" aria-hidden="true"></div>
    <div class="container">
      <div class="pgc-hero__content">
        <p class="overline pgc-hero__overline">
          <?php esc_html_e( 'Private Groups &amp; Celebrations', 'jaiye-journeys' ); ?>
        </p>
        <h1 class="pgc-hero__heading">
          <?php esc_html_e( 'You bring the people.', 'jaiye-journeys' ); ?><br>
          <em><?php esc_html_e( 'We handle the rest.', 'jaiye-journeys' ); ?></em>
        </h1>
        <p class="pgc-hero__sub">
          <?php esc_html_e( 'Family reunions, hen weekends, milestone birthdays, destination weddings. Tell us who is coming and what you are celebrating — we will design the trip around it.', 'jaiye-journeys' ); ?>
        </p>
        <div class="pgc-hero__actions">
          <a
            class="btn btn--ghost"
            href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          >
            <?php esc_html_e( 'Start Planning', 'jaiye-journeys' ); ?>
          </a>
          <a
            class="pgc-hero__anchor"
            href="#what-we-plan"
          >
            <?php esc_html_e( 'See what we plan', 'jaiye-journeys' ); ?>
          </a>
        </div>
        <p class="pgc-hero__note">
          <?php esc_html_e( 'No commitment required for an initial conversation.', 'jaiye-journeys' ); ?>
        </p>
      </div>
    </div>
  </header><!-- /.pgc-hero -->


  <!-- ============================================================
       2. INTRO STRIP
       Cream bg. Two-column: heading left, body right.
       ============================================================ -->
  <section
    class="pgc-intro"
    aria-label="<?php esc_attr_e( 'About private group trips', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-intro__grid">

        <div data-reveal>
          <p class="overline">
            <?php esc_html_e( 'The Brief', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-intro__heading">
            <?php esc_html_e( 'If you can picture it, we can plan it.', 'jaiye-journeys' ); ?>
          </h2>
        </div>

        <div data-reveal>
          <p class="pgc-intro__body">
            <?php esc_html_e( 'The celebrations below are what people come to us for most — but they are not the only occasions we plan. Tell us your brief, however unusual, and we will let you know what is possible.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div>
    </div>
  </section><!-- /.pgc-intro -->


  <!-- ============================================================
       3. CELEBRATION TYPES
       Cream bg. White cards (trip-card pattern), 2×2 grid +
       full-width forest green invitation card for open briefs.
       Photo slots labelled for photography swap-in.
       ============================================================ -->
  <section
    class="pgc-sheet"
    id="what-we-plan"
    aria-label="<?php esc_attr_e( 'Celebration types we plan', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center" data-reveal>
        <p class="overline">
          <?php esc_html_e( 'What We Plan', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'Built around every kind of occasion.', 'jaiye-journeys' ); ?>
        </h2>
      </header>

      <div class="pgc-cel-grid">

        <!-- Card 1: Family Reunions -->
        <article class="trip-card pgc-cel-card" data-reveal>
          <div class="pgc-cel-card__media">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/family-reunions.jpg' ); ?>"
              alt="<?php esc_attr_e( 'A multi-generational family gathered together on a group trip', 'jaiye-journeys' ); ?>"
              class="trip-card__img"
              loading="lazy"
            >
          </div>
          <div class="trip-card__body">
            <p class="trip-card__type">
              <span class="overline"><?php esc_html_e( 'Family Reunions', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="trip-card__title">
              <?php esc_html_e( 'Three generations, one destination.', 'jaiye-journeys' ); ?>
            </h3>
            <p class="trip-card__desc">
              <?php esc_html_e( 'We coordinate accommodation across households, split costs, and build in enough breathing room for everyone — including the ones who just want to sit by the pool.', 'jaiye-journeys' ); ?>
            </p>
          </div>
        </article>

        <!-- Card 2: Hen Weekends -->
        <article class="trip-card pgc-cel-card" data-reveal>
          <div class="pgc-cel-card__media">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/hen-weekend.jpg' ); ?>"
              alt="<?php esc_attr_e( 'A group of women celebrating a hen weekend abroad', 'jaiye-journeys' ); ?>"
              class="trip-card__img"
              loading="lazy"
            >
          </div>
          <div class="trip-card__body">
            <p class="trip-card__type">
              <span class="overline"><?php esc_html_e( 'Hen Weekends', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="trip-card__title">
              <?php esc_html_e( 'The bride arrives. We handled the rest.', 'jaiye-journeys' ); ?>
            </h3>
            <p class="pgc-cel__caption">
              <?php esc_html_e( 'The UK\'s pre-wedding tradition — the equivalent of a bachelorette weekend.', 'jaiye-journeys' ); ?>
            </p>
            <p class="trip-card__desc">
              <?php esc_html_e( 'Activities, accommodation, and a payment system so nobody is chasing transfers. Built around what the bride actually wants, not what a template suggests.', 'jaiye-journeys' ); ?>
            </p>
          </div>
        </article>

        <!-- Card 3: Milestone Birthdays -->
        <article class="trip-card pgc-cel-card" data-reveal>
          <div class="pgc-cel-card__media">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/milestone-birthday.jpg' ); ?>"
              alt="<?php esc_attr_e( 'Friends celebrating a milestone birthday on a group trip', 'jaiye-journeys' ); ?>"
              class="trip-card__img"
              loading="lazy"
            >
          </div>
          <div class="trip-card__body">
            <p class="trip-card__type">
              <span class="overline"><?php esc_html_e( 'Milestone Birthdays', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="trip-card__title">
              <?php esc_html_e( '30, 40, 50 — whichever one deserves a proper trip.', 'jaiye-journeys' ); ?>
            </h3>
            <p class="trip-card__desc">
              <?php esc_html_e( 'The right people, somewhere that means something, and an itinerary that runs itself so the host can actually be present on the day.', 'jaiye-journeys' ); ?>
            </p>
          </div>
        </article>

        <!-- Card 4: Destination Weddings -->
        <article class="trip-card pgc-cel-card" data-reveal>
          <div class="pgc-cel-card__media">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/destination-wedding.jpg' ); ?>"
              alt="<?php esc_attr_e( 'A couple celebrating their destination wedding abroad', 'jaiye-journeys' ); ?>"
              class="trip-card__img"
              loading="lazy"
            >
          </div>
          <div class="trip-card__body">
            <p class="trip-card__type">
              <span class="overline"><?php esc_html_e( 'Destination Weddings', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="trip-card__title">
              <?php esc_html_e( 'Your wedding, your guest list, somewhere worth travelling to.', 'jaiye-journeys' ); ?>
            </h3>
            <p class="trip-card__desc">
              <?php esc_html_e( 'Room blocks, group transfers, and a guest portal so your guests can sort their own bookings without calling you about it. You focus on getting married. We handle the rest.', 'jaiye-journeys' ); ?>
            </p>
          </div>
        </article>

        <!-- Card 5: Something Else Entirely — full-width forest green -->
        <article class="pgc-invite-card" data-reveal>
          <div class="pgc-invite-card__inner">
            <p class="overline pgc-invite-card__overline">
              <?php esc_html_e( 'Something Else Entirely', 'jaiye-journeys' ); ?>
            </p>
            <h3 class="pgc-invite-card__heading">
              <?php esc_html_e( 'A long-overdue reunion. A retirement send-off. A baby shower somewhere warm.', 'jaiye-journeys' ); ?>
            </h3>
            <p class="pgc-invite-card__body">
              <?php esc_html_e( 'If it is a group of people and a reason to celebrate, tell us about it. We will let you know what is possible.', 'jaiye-journeys' ); ?>
            </p>
            <a
              class="btn btn--ghost"
              href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
            >
              <?php esc_html_e( 'Tell Us About Your Celebration', 'jaiye-journeys' ); ?>
            </a>
          </div>
        </article>

      </div><!-- /.pgc-cel-grid -->
    </div><!-- /.container -->
  </section><!-- /.pgc-sheet -->


  <!-- ============================================================
       4. BETWEEN THE LINES CALLOUT
       Sage bg. Two-column: text + button left, pull quote right.
       ============================================================ -->
  <section
    class="pgc-retreat"
    aria-label="<?php esc_attr_e( 'Between the Lines reading retreats', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-retreat__grid">

        <div data-reveal>
          <p class="overline pgc-retreat__overline">
            <?php esc_html_e( 'Also Under This Roof', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-retreat__heading">
            <?php esc_html_e( 'Between the Lines — a retreat for readers.', 'jaiye-journeys' ); ?>
          </h2>
          <p class="pgc-retreat__body">
            <?php esc_html_e( 'Not every celebration needs a guest list. Between the Lines is our literary residency for book clubs and reading communities who want a week of genuine rest, good food, and better conversation.', 'jaiye-journeys' ); ?>
          </p>
          <a
            class="btn btn--primary"
            href="<?php echo esc_url( home_url( '/between-the-lines/' ) ); ?>"
          >
            <?php esc_html_e( 'Explore Between the Lines', 'jaiye-journeys' ); ?>
          </a>
        </div>

        <div data-reveal>
          <blockquote class="pgc-retreat__quote">
            <p>
              <?php esc_html_e( '&ldquo;The best stories happen in the margins. The best retreats happen away from the inbox.&rdquo;', 'jaiye-journeys' ); ?>
            </p>
          </blockquote>
        </div>

      </div>
    </div>
  </section><!-- /.pgc-retreat -->


  <!-- ============================================================
       5. HOW IT WORKS
       Forest green bg. Four-step layout with animated connector line.
       ============================================================ -->
  <section
    class="pgc-process"
    aria-label="<?php esc_attr_e( 'How the planning process works', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <div class="pgc-process__head" data-reveal>
        <p class="overline pgc-process__overline">
          <?php esc_html_e( 'How It Works', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="pgc-process__heading">
          <?php esc_html_e( 'Four steps, no chaos.', 'jaiye-journeys' ); ?>
        </h2>
      </div>

      <div class="pgc-process__row">
        <div class="pgc-process__line" aria-hidden="true"></div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">01</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Inquire', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'Tell us the occasion, the headcount, and the experience you have in mind.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">02</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Design', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'We build out the options — accommodation, activities, and logistics matched to your group.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">03</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Confirm', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'Lock it in with a deposit. Everyone else pays their share through their own link.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">04</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Jaiye!', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'Show up. Everything else is handled.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div><!-- /.pgc-process__row -->
    </div><!-- /.container -->
  </section><!-- /.pgc-process -->


  <!-- ============================================================
       6. WHAT'S INCLUDED
       Cream bg. Two-column: heading left, list right.
       ============================================================ -->
  <section
    class="pgc-included"
    aria-label="<?php esc_attr_e( 'What is included in a private group trip', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-included__grid">

        <div data-reveal>
          <p class="overline">
            <?php esc_html_e( 'What We Handle', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-included__heading">
            <?php esc_html_e( 'The admin nobody wants to do.', 'jaiye-journeys' ); ?>
          </h2>
        </div>

        <ul class="pgc-included__list" data-reveal>
          <li>
            <?php esc_html_e( 'Individual payment links — so nobody is chasing transfers.', 'jaiye-journeys' ); ?>
          </li>
          <li>
            <?php esc_html_e( 'A custom itinerary each guest can access on their phone, without messaging you for the details.', 'jaiye-journeys' ); ?>
          </li>
          <li>
            <?php esc_html_e( 'Hotel and venue sourcing through our FORA partnerships, with VIP perks you cannot book directly.', 'jaiye-journeys' ); ?>
          </li>
          <li>
            <?php esc_html_e( 'Dietary requirements, transfers, and the small logistics that make a group trip feel seamless.', 'jaiye-journeys' ); ?>
          </li>
        </ul>

      </div>
    </div>
  </section><!-- /.pgc-included -->


  <!-- ============================================================
       7. THE INVESTMENT
       Cream bg. Large guest minimum number left, text right.
       ============================================================ -->
  <section
    class="pgc-invest"
    aria-label="<?php esc_attr_e( 'Pricing and group size information', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-invest__grid">

        <div data-reveal>
          <p class="pgc-invest__num">
            10<span><?php esc_html_e( 'Guest Minimum', 'jaiye-journeys' ); ?></span>
          </p>
        </div>

        <div data-reveal>
          <p class="overline">
            <?php esc_html_e( 'The Investment', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-invest__heading">
            <?php esc_html_e( 'Every group is quoted individually.', 'jaiye-journeys' ); ?>
          </h2>
          <p class="pgc-invest__body">
            <?php esc_html_e( 'Private group trips start from ten guests. The price depends on the destination, the headcount, and how hands-on you would like us to be.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-invest__body">
            <?php esc_html_e( 'Send us the brief and we will come back with a real proposal — not a price list.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div>
    </div>
  </section><!-- /.pgc-invest -->


  <!-- ============================================================
       8. FINAL CTA
       Forest green bg (matches oj-cta). Centred.
       ============================================================ -->
  <section
    class="pgc-cta"
    aria-label="<?php esc_attr_e( 'Start planning your group trip', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-cta__inner">
        <h2 class="pgc-cta__heading" data-reveal>
          <?php esc_html_e( 'Ready to bring your group together?', 'jaiye-journeys' ); ?>
        </h2>
        <p class="pgc-cta__sub" data-reveal>
          <?php esc_html_e( 'Tell us what you are celebrating, who is coming, and where in the world you would like to take them. We will handle the rest.', 'jaiye-journeys' ); ?>
        </p>
        <a
          class="btn btn--ghost"
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          data-reveal
          aria-label="<?php esc_attr_e( 'Get in touch — contact Jaiye Journeys', 'jaiye-journeys' ); ?>"
        >
          <?php esc_html_e( 'Get in Touch', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </section><!-- /.pgc-cta -->

</main><!-- /#main-content -->

<noscript>
  <style>
    .pgc-page [data-reveal] { opacity: 1 !important; transform: none !important; }
  </style>
</noscript>

<script>
(function () {
  'use strict';

  /* ---- Scroll-reveal ------------------------------------------ */
  var els = document.querySelectorAll('.pgc-page [data-reveal]');

  if (!('IntersectionObserver' in window)) {
    els.forEach(function (el) { el.classList.add('is-visible'); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    els.forEach(function (el) { io.observe(el); });
  }

  /* ---- Process connector line — reveal when row enters view ---- */
  var line = document.querySelector('.pgc-process__line');
  if (line && 'IntersectionObserver' in window) {
    var lineIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          line.classList.add('is-visible');
          lineIO.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });
    lineIO.observe(line.parentElement);
  } else if (line) {
    line.classList.add('is-visible');
  }
})();
</script>

<?php get_footer(); ?>
