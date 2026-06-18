<?php
/**
 * Homepage template.
 * WordPress loads this file for the front page when a static page is set.
 * Hero background: set the Featured Image on this page in WP Admin → the image
 * is retrieved via get_the_post_thumbnail_url() - no inline styles needed.
 *
 * Static section images live in /assets/images/ inside the theme folder.
 */

get_header();

?>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. HERO
       Full-viewport Ken Burns slideshow + dark overlay.
       Mobile: text centred, smaller scale.
       Desktop: text left-aligned, generous breathing room.
       ============================================================ -->
  <section
    class="fp-hero"
    aria-label="<?php esc_attr_e( 'Welcome to Jaiye Journeys', 'jaiye-journeys' ); ?>"
  >
    <div class="fp-hero__slideshow" aria-hidden="true">
      <div class="fp-hero__slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-japan.jpg');"></div>
      <div class="fp-hero__slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-colombia.jpg');"></div>
      <div class="fp-hero__slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-peru.jpg');"></div>
      <div class="fp-hero__slide" style="background-image: url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-desert.jpg');"></div>
    </div>
    <div class="fp-hero__overlay" aria-hidden="true"></div>

    <div class="container fp-hero__inner">
      <div class="fp-hero__content">
        <h1 class="fp-hero__heading">
          <?php esc_html_e( 'Curated Travel', 'jaiye-journeys' ); ?><br>
          <?php esc_html_e( 'to Enjoy Life.', 'jaiye-journeys' ); ?>
        </h1>
        <p class="fp-hero__sub">
          <?php esc_html_e( 'We handle the admin so you can focus on the enjoyment.', 'jaiye-journeys' ); ?>
        </p>
        <a
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          class="btn btn--plan-trip"
        >
          <?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </section><!-- /.fp-hero -->


  <!-- ============================================================
       2. TWO PATHWAYS
       Stacked on mobile. Side-by-side on desktop.
       Forest green overlay transitions in on hover.
       ============================================================ -->
  <section
    class="fp-pathways"
    aria-label="<?php esc_attr_e( 'How we can help you travel', 'jaiye-journeys' ); ?>"
  >
    <div class="fp-pathways__grid">

      <article class="fp-pathway">
        <div class="fp-pathway__media">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/pathway-fit.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Solo traveller exploring a sunlit destination', 'jaiye-journeys' ); ?>"
            class="fp-pathway__img"
            loading="lazy"
          >
        </div>
        <div class="fp-pathway__content">
          <h2 class="fp-pathway__title">
            <?php esc_html_e( 'Plan My Journey', 'jaiye-journeys' ); ?>
          </h2>
          <p class="fp-pathway__desc">
            <?php esc_html_e( 'Bespoke, fully managed itineraries built around your pace and preferences.', 'jaiye-journeys' ); ?>
          </p>
          <a
            href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
            class="btn btn--ghost"
          >
            <?php esc_html_e( 'Get Started', 'jaiye-journeys' ); ?>
          </a>
        </div>
      </article>

      <article class="fp-pathway">
        <div class="fp-pathway__media">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/pathway-group.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Group of travellers celebrating at a destination', 'jaiye-journeys' ); ?>"
            class="fp-pathway__img"
            loading="lazy"
          >
        </div>
        <div class="fp-pathway__content">
          <h2 class="fp-pathway__title">
            <?php esc_html_e( 'Join a Group Trip', 'jaiye-journeys' ); ?>
          </h2>
          <p class="fp-pathway__desc">
            <?php esc_html_e( 'Curated small-group journeys to extraordinary destinations, with like-minded travellers.', 'jaiye-journeys' ); ?>
          </p>
          <a
            href="<?php echo esc_url( home_url( '/our-journeys/#group-trips' ) ); ?>"
            class="btn btn--ghost"
          >
            <?php esc_html_e( 'View Group Trips', 'jaiye-journeys' ); ?>
          </a>
        </div>
      </article>

    </div><!-- /.fp-pathways__grid -->
  </section><!-- /.fp-pathways -->


  <!-- ============================================================
       3. SERVICES STRIP
       Horizontal scroll on mobile (CSS scroll-snap).
       5-column grid on desktop.
       ============================================================ -->
  <section
    class="fp-services"
    aria-label="<?php esc_attr_e( 'Our services', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline">
          <?php esc_html_e( 'What We Do', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'Every Detail, Handled', 'jaiye-journeys' ); ?>
        </h2>
      </header>

      <ul class="fp-services__grid" role="list">

        <li class="fp-service-tile">
			<span class="fp-service-popular">Popular</span>
          <h3 class="fp-service-tile__title">
<a href="https://tally.so/r/eq6rp0" target="_blank" rel="noopener" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'The Ticketing Desk', 'jaiye-journeys' ); ?></a>          </h3>
          <p class="fp-service-tile__desc">
            <?php esc_html_e( 'Flights, transfers, and logistics, sourced and booked for you.', 'jaiye-journeys' ); ?>
          </p>
          <span class="fp-service-tile__arrow" aria-hidden="true">→</span>
        </li>

        <li class="fp-service-tile fp-service-tile--no-badge">
          <h3 class="fp-service-tile__title">
            <a href="/contact/" target="_self" style="color: inherit; text-decoration: none;">The Hotel Edit</a>
          </h3>
          <p class="fp-service-tile__desc">
            <?php esc_html_e( 'Handpicked stays that match your style, budget, and travel story.', 'jaiye-journeys' ); ?>
          </p>
          <span class="fp-service-tile__arrow" aria-hidden="true">→</span>
        </li>

        <li class="fp-service-tile fp-service-tile--no-badge">
          <h3 class="fp-service-tile__title">
<a href="/contact/" target="_self" style="color: inherit; text-decoration: none;">The Signature Itinerary</a>          </h3>
          <p class="fp-service-tile__desc">
            <?php esc_html_e( 'A fully crafted day-by-day journey, designed entirely around you.', 'jaiye-journeys' ); ?>
          </p>
          <span class="fp-service-tile__arrow" aria-hidden="true">→</span>
        </li>

        <li class="fp-service-tile fp-service-tile--no-badge">
          <h3 class="fp-service-tile__title">
<a href="/contact/" target="_self" style="color: inherit; text-decoration: none;">Private Groups & Celebrations</a>          </h3>
          <p class="fp-service-tile__desc">
            <?php esc_html_e( 'Milestone moments curated for groups: birthdays, honeymoons, and corporate retreats.', 'jaiye-journeys' ); ?>
          </p>
          <span class="fp-service-tile__arrow" aria-hidden="true">→</span>
        </li>

        <li class="fp-service-tile fp-service-tile--no-badge">
  <h3 class="fp-service-tile__title">
    <a href="https://thejaiyeconcierge.com" target="_blank" rel="noopener" style="color: inherit; text-decoration: none;">The Jaiye Concierge</a>
  </h3>
  <p class="fp-service-tile__desc">Full-service lifestyle and travel management for clients who want everything handled.</p>
  <span class="fp-service-tile__arrow" aria-hidden="true">→</span>
</li>

      </ul><!-- /.fp-services__grid -->
    </div><!-- /.container -->
  </section><!-- /.fp-services -->


  <!-- ============================================================
       4. ABOUT STRIP
       Desktop: photo left, text right.
       Mobile: text first (via CSS order), photo below.
       ============================================================ -->
  <section
    class="fp-about"
    aria-label="<?php esc_attr_e( 'About Jaiye Journeys', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="fp-about__inner">

        <figure class="fp-about__photo">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph.jpg?v=2' ); ?>"
            alt="<?php esc_attr_e( 'Steph, founder of Jaiye Journeys', 'jaiye-journeys' ); ?>"
            class="fp-about__img"
            loading="lazy"
          >
        </figure>

        <div class="fp-about__text">
          <p class="overline fp-about__eyebrow">
            <?php esc_html_e( 'About Jaiye Journeys', 'jaiye-journeys' ); ?>
          </p>
          <h2 class="fp-about__heading">
            <?php esc_html_e( 'Travel That Feels Like You', 'jaiye-journeys' ); ?>
          </h2>
          <p class="fp-about__body">
            <?php esc_html_e( 'Jaiye Journeys was born out of a simple belief: travel should be a joy from the very first step of planning, not a source of overwhelm. Founded by Steph, a passionate traveller and detail-obsessed planner, we take care of every logistical layer so your only job is to show up and enjoy.', 'jaiye-journeys' ); ?>
          </p>
          <p class="fp-about__body">
            <?php esc_html_e( 'Whether you are chasing a solo adventure, planning a group celebration, or dreaming of a destination you have never quite made it to - we make it happen, beautifully.', 'jaiye-journeys' ); ?>
          </p>
          <a
            href="<?php echo esc_url( home_url( '/about/' ) ); ?>"
            class="btn btn--ghost fp-about__cta"
          >
            <?php esc_html_e( 'Meet Steph', 'jaiye-journeys' ); ?>
          </a>
        </div>

      </div><!-- /.fp-about__inner -->
    </div><!-- /.container -->
  </section><!-- /.fp-about -->


  <!-- ============================================================
       5. TESTIMONIALS
       Mobile: CSS scroll-snap carousel (no JS needed).
       Desktop: three-column grid.
       Cream bg, forest green text, salmon on quotation marks.
       ============================================================ -->
  <section
    class="fp-testimonials"
    aria-label="<?php esc_attr_e( 'What our travellers say', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline">
          <?php esc_html_e( 'Testimonials', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'Stories from the Road', 'jaiye-journeys' ); ?>
        </h2>
      </header>

      <div
        class="fp-testimonials__track"
        role="list"
        aria-label="<?php esc_attr_e( 'Client testimonials', 'jaiye-journeys' ); ?>"
      >

        <article class="fp-testimonial" role="listitem">
          <blockquote class="fp-testimonial__quote">
            <p><?php esc_html_e( 'Working with Jaiye Journeys took the stress away from planning all the logistics and allowed me to just be excited to travel.', 'jaiye-journeys' ); ?></p>
            <footer class="fp-testimonial__footer">
              <cite class="fp-testimonial__cite">Caroline S, Denver, Colorado</cite>
            </footer>
          </blockquote>
        </article>

        <article class="fp-testimonial" role="listitem">
          <blockquote class="fp-testimonial__quote">
            <p><?php esc_html_e( 'Steph gave me the feeling that I could truly enjoy my holiday because everything was taken care of. Even when things go awry, Steph made sure the experience was seamless.', 'jaiye-journeys' ); ?></p>
            <footer class="fp-testimonial__footer">
              <cite class="fp-testimonial__cite">Anca B, Vancouver, Canada</cite>
            </footer>
          </blockquote>
        </article>

        <article class="fp-testimonial" role="listitem">
          <blockquote class="fp-testimonial__quote">
            <p><?php esc_html_e( 'How streamlined the planning and booking process can actually be. I was contacted every step of the way.', 'jaiye-journeys' ); ?></p>
            <footer class="fp-testimonial__footer">
              <cite class="fp-testimonial__cite">Sam S, London, UK</cite>
            </footer>
          </blockquote>
        </article>

      </div><!-- /.fp-testimonials__track -->
    </div><!-- /.container -->
  </section><!-- /.fp-testimonials -->


  <!-- ============================================================
       6. UPCOMING TRIPS
       Mobile: single column.
       Tablet: two columns.
       Desktop: three columns.
       Card: image, destination, dates, CTA.
       ============================================================ -->
  <section
    class="fp-trips"
    aria-label="<?php esc_attr_e( 'Upcoming group trips', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline">
          <?php esc_html_e( 'Join Us Next', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'Upcoming Trips', 'jaiye-journeys' ); ?>
        </h2>
      </header>

      <ul class="fp-trips__grid" role="list">

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-btl-prologue.jpg' ); ?>"
                alt="<?php esc_attr_e( 'The Prologue - Between the Lines UK event', 'jaiye-journeys' ); ?>"
                class="trip-card__img"
                loading="lazy"
              >
            </div>
            <div class="trip-card__body">
              <p class="trip-card__type">
                <span class="overline"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></span>
              </p>
              <h3 class="trip-card__title">
                <?php esc_html_e( 'The Prologue', 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'UK', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2026-09"><?php esc_html_e( 'September / October 2026', 'jaiye-journeys' ); ?></time>
              </p>
              <a
                href="<?php echo esc_url( home_url( '/trips/the-prologue/' ) ); ?>"
                class="btn btn--secondary btn--sm trip-card__cta"
              >
                <?php esc_html_e( 'Find Out More', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-cape-town.jpg' ); ?>"
                alt="<?php esc_attr_e( 'Cape Town coastline with Table Mountain in the background', 'jaiye-journeys' ); ?>"
                class="trip-card__img"
                loading="lazy"
              >
            </div>
            <div class="trip-card__body">
              <p class="trip-card__type">
                <span class="overline"><?php esc_html_e( 'Group Trip', 'jaiye-journeys' ); ?></span>
              </p>
              <h3 class="trip-card__title">
                <?php esc_html_e( 'The Cape Town Edit', 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'South Africa', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2027-01"><?php esc_html_e( 'January 2027', 'jaiye-journeys' ); ?></time>
              </p>
              <a
                href="<?php echo esc_url( home_url( '/trips/jj-cape-town/' ) ); ?>"
                class="btn btn--secondary btn--sm trip-card__cta"
              >
                <?php esc_html_e( 'Find Out More', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-morocco.jpg' ); ?>"
                alt="<?php esc_attr_e( 'Blue and white streets of Essaouira, Morocco', 'jaiye-journeys' ); ?>"
                class="trip-card__img"
                loading="lazy"
              >
            </div>
            <div class="trip-card__body">
              <p class="trip-card__type">
                <span class="overline"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></span>
              </p>
              <h3 class="trip-card__title">
                <?php esc_html_e( 'The Essaouira Edition', 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'Essaouira, Morocco', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2027-04"><?php esc_html_e( 'April 2027', 'jaiye-journeys' ); ?></time>
              </p>
              <a
                href="<?php echo esc_url( home_url( '/trips/btl-chapter-1/' ) ); ?>"
                class="btn btn--secondary btn--sm trip-card__cta"
              >
                <?php esc_html_e( 'Find Out More', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-bali.jpg' ); ?>"
                alt="<?php esc_attr_e( 'Turquoise waters and tropical coastline of Bali, Indonesia', 'jaiye-journeys' ); ?>"
                class="trip-card__img"
                loading="lazy"
              >
            </div>
            <div class="trip-card__body">
              <p class="trip-card__type">
                <span class="overline"><?php esc_html_e( 'Group Trip', 'jaiye-journeys' ); ?></span>
              </p>
              <h3 class="trip-card__title">
                <?php esc_html_e( 'The Islands Edit', 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'Indonesia', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2027-05"><?php esc_html_e( 'May 2027', 'jaiye-journeys' ); ?></time>
              </p>
              <a
                href="<?php echo esc_url( home_url( '/trips/jj-bali/' ) ); ?>"
                class="btn btn--secondary btn--sm trip-card__cta"
              >
                <?php esc_html_e( 'Find Out More', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

      </ul><!-- /.fp-trips__grid -->

      <div class="fp-trips__footer">
        <a
          href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>"
          class="btn btn--primary"
        >
          <?php esc_html_e( 'View All Trips', 'jaiye-journeys' ); ?>
        </a>
      </div>

    </div><!-- /.container -->
  </section><!-- /.fp-trips -->

</main><!-- /#main-content -->

<?php get_footer(); ?>
