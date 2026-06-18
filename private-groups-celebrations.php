<?php
/*
 * Template Name: Private Groups & Celebrations
 */

get_header();
?>

<main id="main-content" class="site-main pgc-page">

  <!-- ============================================================
       1. HERO
       Deep-green bg. Two-column: text left, stacked film frames right.
       Frames are purely decorative, hidden on mobile.
       ============================================================ -->
  <header
    class="pgc-hero"
    aria-label="<?php esc_attr_e( 'Private Groups & Celebrations', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-hero__grid">

        <div data-reveal>
          <p class="overline pgc-kicker pgc-hero__kicker">
            <?php esc_html_e( 'Private Groups &amp; Celebrations', 'jaiye-journeys' ); ?>
          </p>
          <h1 class="pgc-hero__heading">
            <?php esc_html_e( 'You bring the people.', 'jaiye-journeys' ); ?>
            <br><em><?php esc_html_e( 'We curate the vibe.', 'jaiye-journeys' ); ?></em>
          </h1>
          <p class="pgc-hero__sub">
            <?php esc_html_e( 'Family reunions. Hen weekends. Milestone birthdays. Weddings far from home. Tell us who\'s coming and what you\'re celebrating, and we\'ll design the trip around it.', 'jaiye-journeys' ); ?>
          </p>
          <div class="pgc-hero__actions">
            <a
              class="btn btn--accent"
              href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
            >
              <?php esc_html_e( 'Start Planning', 'jaiye-journeys' ); ?>
            </a>
            <a
              class="btn btn--ghost"
              href="#sheet"
            >
              <?php esc_html_e( 'See What We Plan', 'jaiye-journeys' ); ?>
            </a>
          </div>
          <p class="pgc-hero__note">
            <?php esc_html_e( 'No deposit required for a first conversation.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <!-- Decorative film stills — desktop only, aria-hidden -->
        <div class="pgc-hero__frames" aria-hidden="true">
          <div class="pgc-frame-still pgc-frame-still--a" data-label="reunion.jpg"></div>
          <div class="pgc-frame-still pgc-frame-still--b" data-label="hen-do.jpg"></div>
          <div class="pgc-frame-still pgc-frame-still--c" data-label="wedding.jpg"></div>
        </div>

      </div>
    </div>
  </header><!-- /.pgc-hero -->


  <!-- ============================================================
       2. INTRO STRIP
       Dark bg, border-top/bottom. Two-column: h2 left / body right.
       ============================================================ -->
  <section
    class="pgc-intro"
    aria-label="<?php esc_attr_e( 'About private group trips', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-intro__grid">

        <div data-reveal>
          <p class="overline pgc-kicker">
            <?php esc_html_e( 'The Brief', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-intro__heading">
            <?php esc_html_e( 'If you can picture it, we can plan it.', 'jaiye-journeys' ); ?>
          </h2>
        </div>

        <div data-reveal>
          <p class="pgc-intro__body">
            <?php esc_html_e( 'The trips below are what people ask us for most, but they are not the only trips we plan. Send us your version of a celebration, however unusual, and we\'ll tell you straight away whether it\'s something we can build.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div>
    </div>
  </section><!-- /.pgc-intro -->


  <!-- ============================================================
       3. CONTACT SHEET / CELEBRATION TYPES
       Dark bg. 2×2 film-frame grid + 1 full-width salmon "open" frame.
       Photo slots named for future photography swap-in.
       ============================================================ -->
  <section
    class="pgc-sheet"
    id="sheet"
    aria-label="<?php esc_attr_e( 'Celebration types we plan', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <div class="pgc-sheet__head" data-reveal>
        <p class="overline pgc-kicker">
          <?php esc_html_e( 'What We Plan', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="pgc-sheet__title">
          <?php esc_html_e( 'A contact sheet of the celebrations we\'ve built.', 'jaiye-journeys' ); ?>
        </h2>
      </div>

      <div class="pgc-frame-grid">

        <article class="pgc-frame" data-reveal tabindex="0">
          <p class="pgc-frame__num">Frame 01</p>
          <div
            class="pgc-frame__photo"
            data-img="family-reunion.jpg"
            role="img"
            aria-label="<?php esc_attr_e( 'Image placeholder: family-reunion.jpg', 'jaiye-journeys' ); ?>"
          ></div>
          <h3 class="pgc-frame__title">
            <?php esc_html_e( 'Family Reunions', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-frame__lead">
            <?php esc_html_e( 'Three generations, one villa, and a seating plan that keeps the peace.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-frame__detail">
            <?php esc_html_e( 'We split the cost across each household, sort the dietary requirements nobody mentions until day two, and build in enough downtime that the trip still feels like a holiday rather than a reunion tour.', 'jaiye-journeys' ); ?>
          </p>
        </article>

        <article class="pgc-frame" data-reveal tabindex="0">
          <p class="pgc-frame__num">Frame 02</p>
          <div
            class="pgc-frame__photo"
            data-img="hen-weekend.jpg"
            role="img"
            aria-label="<?php esc_attr_e( 'Image placeholder: hen-weekend.jpg', 'jaiye-journeys' ); ?>"
          ></div>
          <h3 class="pgc-frame__title">
            <?php esc_html_e( 'Hen Weekends', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-frame__caption">
            <?php esc_html_e( 'The UK\'s pre-wedding tradition, the equivalent of a bachelorette weekend.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-frame__lead">
            <?php esc_html_e( 'The bride doesn\'t lift a finger. Everyone else handles the group chat, badly. We take it off their hands.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-frame__detail">
            <?php esc_html_e( 'Activities, accommodation, and a kitty system so nobody\'s chasing payments, built around what the bride actually wants rather than what a Pinterest board says a hen weekend should look like.', 'jaiye-journeys' ); ?>
          </p>
        </article>

        <article class="pgc-frame" data-reveal tabindex="0">
          <p class="pgc-frame__num">Frame 03</p>
          <div
            class="pgc-frame__photo"
            data-img="milestone-birthday.jpg"
            role="img"
            aria-label="<?php esc_attr_e( 'Image placeholder: milestone-birthday.jpg', 'jaiye-journeys' ); ?>"
          ></div>
          <h3 class="pgc-frame__title">
            <?php esc_html_e( 'Milestone Birthdays', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-frame__lead">
            <?php esc_html_e( 'Turning 30, 40, or the one nobody likes to say out loud.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-frame__detail">
            <?php esc_html_e( 'We design the trip around the milestone, not just the date. A weekend with the right people, somewhere that means something, built so the host gets to enjoy the day instead of running it.', 'jaiye-journeys' ); ?>
          </p>
        </article>

        <article class="pgc-frame" data-reveal tabindex="0">
          <p class="pgc-frame__num">Frame 04</p>
          <div
            class="pgc-frame__photo"
            data-img="destination-wedding.jpg"
            role="img"
            aria-label="<?php esc_attr_e( 'Image placeholder: destination-wedding.jpg', 'jaiye-journeys' ); ?>"
          ></div>
          <h3 class="pgc-frame__title">
            <?php esc_html_e( 'Destination Weddings', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-frame__lead">
            <?php esc_html_e( 'Your wedding, your guest list, somewhere none of you have been before.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-frame__detail">
            <?php esc_html_e( 'Hotel blocks, group flights, and a guest portal so people can book their own rooms without calling you about it. You focus on the wedding. We handle everyone else\'s logistics.', 'jaiye-journeys' ); ?>
          </p>
        </article>

        <!-- Frame 05 — salmon "open" slot, full-width, no photo placeholder -->
        <article class="pgc-frame pgc-frame--open" data-reveal tabindex="0">
          <p class="pgc-frame__num">
            <?php esc_html_e( 'Frame 05 &mdash; Untitled', 'jaiye-journeys' ); ?>
          </p>
          <h3 class="pgc-frame__title">
            <?php esc_html_e( 'Something Else Entirely', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-frame__lead">
            <?php esc_html_e( 'A friendship group\'s first trip together in a decade. A retirement send-off. A baby shower somewhere warm.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-frame__detail">
            <?php esc_html_e( 'If it involves a group of people and a reason to celebrate, tell us about it. We\'ll let you know what\'s possible.', 'jaiye-journeys' ); ?>
          </p>
        </article>

      </div><!-- /.pgc-frame-grid -->
    </div><!-- /.container -->
  </section><!-- /.pgc-sheet -->


  <!-- ============================================================
       4. BETWEEN THE LINES CALLOUT
       Cream bg. Two-column: text + button left, pull quote right.
       ============================================================ -->
  <section
    class="pgc-retreat"
    aria-label="<?php esc_attr_e( 'Between the Lines reading retreats', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-retreat__grid">

        <div data-reveal>
          <p class="overline pgc-retreat__kicker">
            <?php esc_html_e( 'Also Under This Roof', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-retreat__heading">
            <?php esc_html_e( 'Between the Lines: a retreat for readers.', 'jaiye-journeys' ); ?>
          </h2>
          <p class="pgc-retreat__body">
            <?php esc_html_e( 'Not every celebration needs a guest list. Between the Lines is our literary residency, built for book clubs and reading communities who want a week of rest, good food, and better conversation than their group chat usually allows.', 'jaiye-journeys' ); ?>
          </p>
          <a
            class="btn btn--primary"
            href="<?php echo esc_url( home_url( '/between-the-lines/' ) ); ?>"
          >
            <?php esc_html_e( 'Visit Between the Lines', 'jaiye-journeys' ); ?>
          </a>
        </div>

        <div data-reveal>
          <blockquote class="pgc-retreat__quote">
            <p><?php esc_html_e( '&ldquo;The best stories happen in the margins, and the best retreats happen away from your inbox.&rdquo;', 'jaiye-journeys' ); ?></p>
          </blockquote>
        </div>

      </div>
    </div>
  </section><!-- /.pgc-retreat -->


  <!-- ============================================================
       5. HOW IT WORKS — PROCESS
       Dark bg. Four-step horizontal layout with animated connector line.
       ============================================================ -->
  <section
    class="pgc-process"
    aria-label="<?php esc_attr_e( 'How the planning process works', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <div class="pgc-process__head" data-reveal>
        <p class="overline pgc-kicker">
          <?php esc_html_e( 'How It Works', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="pgc-process__heading">
          <?php esc_html_e( 'Four steps, no chaos.', 'jaiye-journeys' ); ?>
        </h2>
      </div>

      <div class="pgc-process__row">
        <!-- Connector line — revealed by JS when row enters view -->
        <div class="pgc-process__line" aria-hidden="true"></div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">01</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Inquire', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'You tell us the occasion, the headcount, and the vibe you\'re after.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">02</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Design', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'We build out the options. Accommodation, activities, and logistics, matched to your group and your budget.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">03</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Confirm', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'You lock it in with a deposit. Everyone else pays their share through their own link.', 'jaiye-journeys' ); ?>
          </p>
        </div>

        <div class="pgc-process__step" data-reveal>
          <div class="pgc-process__dot" aria-hidden="true">04</div>
          <h3 class="pgc-process__step-title">
            <?php esc_html_e( 'Jaiye!', 'jaiye-journeys' ); ?>
          </h3>
          <p class="pgc-process__step-body">
            <?php esc_html_e( 'You turn up and enjoy your own party. We\'re handling the rest, from the group chat to the final bill.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div><!-- /.pgc-process__row -->
    </div><!-- /.container -->
  </section><!-- /.pgc-process -->


  <!-- ============================================================
       6. WHAT'S INCLUDED
       Forest bg. Two-column: heading left, list right.
       ============================================================ -->
  <section
    class="pgc-included"
    aria-label="<?php esc_attr_e( 'What is included in a private group trip', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-included__grid">

        <div data-reveal>
          <p class="overline pgc-kicker">
            <?php esc_html_e( 'What We Handle', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-included__heading">
            <?php esc_html_e( 'The admin nobody wants to do.', 'jaiye-journeys' ); ?>
          </h2>
        </div>

        <ul class="pgc-included__list" data-reveal>
          <li>
            <?php esc_html_e( 'Individual payment links, so you\'re not chasing twelve people for money.', 'jaiye-journeys' ); ?>
          </li>
          <li>
            <?php esc_html_e( 'A custom itinerary app each guest can check without messaging you for the address again.', 'jaiye-journeys' ); ?>
          </li>
          <li>
            <?php esc_html_e( 'Hotel and venue sourcing through our FORA partnerships, with VIP perks you can\'t book direct.', 'jaiye-journeys' ); ?>
          </li>
          <li>
            <?php esc_html_e( 'Dietary requirements, transfers, and the small details that turn a group trip into a smooth one.', 'jaiye-journeys' ); ?>
          </li>
        </ul>

      </div>
    </div>
  </section><!-- /.pgc-included -->


  <!-- ============================================================
       7. THE INVESTMENT
       Dark bg. Large guest-minimum number left, text right.
       ============================================================ -->
  <section
    class="pgc-invest"
    aria-label="<?php esc_attr_e( 'Pricing and investment information', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-invest__grid">

        <div data-reveal>
          <p class="pgc-invest__num">
            10<span><?php esc_html_e( 'Guest Minimum', 'jaiye-journeys' ); ?></span>
          </p>
        </div>

        <div data-reveal>
          <p class="overline pgc-kicker">
            <?php esc_html_e( 'The Investment', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="pgc-invest__heading">
            <?php esc_html_e( 'Every group is quoted individually.', 'jaiye-journeys' ); ?>
          </h2>
          <p class="pgc-invest__body">
            <?php esc_html_e( 'Private group trips start from a minimum of 10 guests. Beyond that, the price depends on destination, headcount, and how hands-on you want us to be.', 'jaiye-journeys' ); ?>
          </p>
          <p class="pgc-invest__body">
            <?php esc_html_e( 'Tell us the brief and we\'ll come back with real numbers, not a generic price list.', 'jaiye-journeys' ); ?>
          </p>
        </div>

      </div>
    </div>
  </section><!-- /.pgc-invest -->


  <!-- ============================================================
       8. FINAL CTA
       Salmon gradient bg. Centred text + button.
       ============================================================ -->
  <section
    class="pgc-final"
    aria-label="<?php esc_attr_e( 'Start planning your group trip', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="pgc-final__inner">
        <h2 class="pgc-final__heading" data-reveal>
          <?php esc_html_e( 'Your people are waiting for an invitation.', 'jaiye-journeys' ); ?>
        </h2>
        <p class="pgc-final__sub" data-reveal>
          <?php esc_html_e( 'Tell us who\'s coming, what you\'re celebrating, and where you\'ve always wanted to take them. We\'ll do the rest.', 'jaiye-journeys' ); ?>
        </p>
        <a
          class="btn btn--primary"
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          data-reveal
          aria-label="<?php esc_attr_e( 'Start planning — contact Jaiye Journeys', 'jaiye-journeys' ); ?>"
        >
          <?php esc_html_e( 'Start Planning', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </section><!-- /.pgc-final -->

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
