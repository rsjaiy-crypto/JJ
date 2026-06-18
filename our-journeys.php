<?php
/*
 * Template Name: Our Journeys
 */

get_header();
?>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. HERO
       50vh, forest green background, cream text.
       ============================================================ -->
  <section
    class="oj-hero"
    aria-label="<?php esc_attr_e( 'Our Journeys', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="oj-hero__content">
        <h1 class="oj-hero__heading">
          <?php esc_html_e( 'Our Journeys', 'jaiye-journeys' ); ?>
        </h1>
        <p class="oj-hero__sub">
          <?php esc_html_e( 'Curated group experiences and luxury literary retreats. Your next chapter starts here.', 'jaiye-journeys' ); ?>
        </p>
      </div>
    </div>
  </section><!-- /.oj-hero -->


  <!-- ============================================================
       2. GROUP TRIPS
       Cream bg. Card grid: 1 col → 2 col → 3 col.
       ============================================================ -->
  <section
    class="oj-section oj-section--group"
    id="group-trips"
    aria-label="<?php esc_attr_e( 'Group trips', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline">
          <?php esc_html_e( 'Group Trips', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'Group Trips', 'jaiye-journeys' ); ?>
        </h2>
        <p class="section-header__sub">
          <?php esc_html_e( 'Open-access curated trips for the Jaiye Journeys community.', 'jaiye-journeys' ); ?>
        </p>
      </header>

      <ul class="oj-grid" role="list">

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
                <?php esc_html_e( 'Cape Town, South Africa', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2028-01"><?php esc_html_e( 'January 2028', 'jaiye-journeys' ); ?></time>
              </p>
              <p class="trip-card__desc">
                <?php esc_html_e( 'Sun, culture, and the most dramatic coastline on earth. Cape Town is for the ones who want it all.', 'jaiye-journeys' ); ?>
              </p>
              <a
                href="https://tally.so/r/aQa9D2"
                class="btn btn--secondary btn--sm trip-card__cta"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
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
                <?php esc_html_e( 'The Islands Cut', 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'Bali, Gilis + Komodo, Indonesia', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2028-05"><?php esc_html_e( 'May 2028', 'jaiye-journeys' ); ?></time>
              </p>
              <p class="trip-card__desc">
                <?php esc_html_e( "Rice terraces, turquoise water, and island-hopping through some of the world's most breathtaking archipelagos.", 'jaiye-journeys' ); ?>
              </p>
              <a
                href="https://tally.so/r/RGVxVj"
                class="btn btn--secondary btn--sm trip-card__cta"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-bahia.jpg' ); ?>"
                alt="<?php esc_attr_e( 'Colonial architecture and coastline of Bahia, Brazil', 'jaiye-journeys' ); ?>"
                class="trip-card__img"
                loading="lazy"
              >
            </div>
            <div class="trip-card__body">
              <p class="trip-card__type">
                <span class="overline"><?php esc_html_e( 'Group Trip', 'jaiye-journeys' ); ?></span>
              </p>
              <h3 class="trip-card__title">
                <?php esc_html_e( 'The Bahia Edit', 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'Bahia, Brazil', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2028-07"><?php esc_html_e( 'July 2028', 'jaiye-journeys' ); ?></time>
              </p>
              <p class="trip-card__desc">
                <?php esc_html_e( 'Afro-Brazilian culture, colonial architecture, and coastline that never ends. Bahia is a full sensory experience.', 'jaiye-journeys' ); ?>
              </p>
              <a
                href="https://tally.so/r/xXlrzr"
                class="btn btn--secondary btn--sm trip-card__cta"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-peru.jpg' ); ?>"
                alt="<?php esc_attr_e( 'Ancient ruins and dramatic landscapes of Peru', 'jaiye-journeys' ); ?>"
                class="trip-card__img"
                loading="lazy"
              >
            </div>
            <div class="trip-card__body">
              <p class="trip-card__type">
                <span class="overline"><?php esc_html_e( 'Group Trip', 'jaiye-journeys' ); ?></span>
              </p>
              <h3 class="trip-card__title">
                <?php esc_html_e( "The Director's Cut", 'jaiye-journeys' ); ?>
              </h3>
              <p class="trip-card__destination">
                <?php esc_html_e( 'Peru', 'jaiye-journeys' ); ?>
              </p>
              <p class="trip-card__dates">
                <time datetime="2028-10"><?php esc_html_e( 'October 2028', 'jaiye-journeys' ); ?></time>
              </p>
              <p class="trip-card__desc">
                <?php esc_html_e( 'Ancient civilisations, dramatic landscapes, and the kind of trip that changes how you see the world.', 'jaiye-journeys' ); ?>
              </p>
              <a
                href="https://tally.so/r/1ApXBp"
                class="btn btn--secondary btn--sm trip-card__cta"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

      </ul><!-- /.oj-grid -->
    </div><!-- /.container -->
  </section><!-- /.oj-section--group -->


  <!-- ============================================================
       3. BETWEEN THE LINES
       Sage (#b1bb9e) background. Two-card grid.
       ============================================================ -->
  <section
    class="oj-section oj-section--btl"
    aria-label="<?php esc_attr_e( 'Between the Lines literary retreats', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline">
          <?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?>
        </h2>
        <p class="section-header__sub">
          <?php esc_html_e( 'Luxury literary retreats for serious readers who want to live inside the feeling of their favourite books.', 'jaiye-journeys' ); ?>
        </p>
      </header>

      <p class="oj-btl__intro">
        <?php esc_html_e( 'Between the Lines is our literary retreat series — exclusively co-branded experiences produced in partnership with book community leaders. Each retreat is intimate, intentional, and unlike anything else in the travel space.', 'jaiye-journeys' ); ?>
      </p>

      <ul class="oj-grid oj-grid--2col" role="list">

        <li>
          <article class="trip-card">
            <div class="trip-card__media">
              <img
                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-btl-prologue.jpg' ); ?>"
                alt="<?php esc_attr_e( 'The Prologue — a literary gathering in the UK', 'jaiye-journeys' ); ?>"
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
              <p class="trip-card__desc">
                <?php esc_html_e( 'Our first gathering. An intimate literary event to introduce the Between the Lines community before the full retreats begin.', 'jaiye-journeys' ); ?>
              </p>
              <a
                href="https://tally.so/r/Gxq2JL"
                class="btn btn--secondary btn--sm trip-card__cta"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
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
                <time datetime="2027-05"><?php esc_html_e( 'May 2027', 'jaiye-journeys' ); ?></time>
              </p>
              <p class="trip-card__desc">
                <?php esc_html_e( 'Six days of reading, rest, and radical joy on the Moroccan Atlantic coast. Our first full literary residency.', 'jaiye-journeys' ); ?>
              </p>
              <a
                href="https://tally.so/r/Gxq2JL"
                class="btn btn--secondary btn--sm trip-card__cta"
                target="_blank"
                rel="noopener noreferrer"
              >
                <?php esc_html_e( 'Join Waitlist', 'jaiye-journeys' ); ?>
              </a>
            </div>
          </article>
        </li>

      </ul><!-- /.oj-grid -->
    </div><!-- /.container -->
  </section><!-- /.oj-section--btl -->


  <!-- ============================================================
       4. ON THE HORIZON
       Cream background. Minimal text-only cards.
       ============================================================ -->
  <section
    class="oj-section oj-section--horizon"
    aria-label="<?php esc_attr_e( 'Trips on the horizon', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline">
          <?php esc_html_e( 'Coming Soon', 'jaiye-journeys' ); ?>
        </p>
        <h2 class="section-header__title">
          <?php esc_html_e( 'On the Horizon', 'jaiye-journeys' ); ?>
        </h2>
        <p class="section-header__sub">
          <?php esc_html_e( 'We plan ahead. Here is what is coming.', 'jaiye-journeys' ); ?>
        </p>
      </header>

      <ul class="oj-grid oj-grid--3col" role="list">

        <li>
          <article class="horizon-card">
            <p class="horizon-card__type">
              <span class="overline"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="horizon-card__title">
              <?php esc_html_e( 'BTL Chapter 2', 'jaiye-journeys' ); ?>
            </h3>
            <p class="horizon-card__destination">
              <?php esc_html_e( 'Sintra, Portugal', 'jaiye-journeys' ); ?>
            </p>
            <p class="horizon-card__dates">
              <time datetime="2028-09"><?php esc_html_e( 'September 2028', 'jaiye-journeys' ); ?></time>
            </p>
            <a
              href="https://tally.so/r/Gxq2JL"
              class="btn btn--secondary btn--sm horizon-card__cta"
              target="_blank"
              rel="noopener noreferrer"
            >
              <?php esc_html_e( 'Notify Me', 'jaiye-journeys' ); ?>
            </a>
          </article>
        </li>

        <li>
          <article class="horizon-card">
            <p class="horizon-card__type">
              <span class="overline"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="horizon-card__title">
              <?php esc_html_e( 'BTL Chapter 3', 'jaiye-journeys' ); ?>
            </h3>
            <p class="horizon-card__destination">
              <?php esc_html_e( 'Oaxaca, Mexico', 'jaiye-journeys' ); ?>
            </p>
            <p class="horizon-card__dates">
              <time datetime="2029-04"><?php esc_html_e( 'April 2029', 'jaiye-journeys' ); ?></time>
            </p>
            <a
              href="https://tally.so/r/Gxq2JL"
              class="btn btn--secondary btn--sm horizon-card__cta"
              target="_blank"
              rel="noopener noreferrer"
            >
              <?php esc_html_e( 'Notify Me', 'jaiye-journeys' ); ?>
            </a>
          </article>
        </li>

        <li>
          <article class="horizon-card">
            <p class="horizon-card__type">
              <span class="overline"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></span>
            </p>
            <h3 class="horizon-card__title">
              <?php esc_html_e( 'BTL Chapter 4', 'jaiye-journeys' ); ?>
            </h3>
            <p class="horizon-card__destination">
              <?php esc_html_e( 'Tuscany, Italy', 'jaiye-journeys' ); ?>
            </p>
            <p class="horizon-card__dates">
              <time datetime="2029-09"><?php esc_html_e( 'September 2029', 'jaiye-journeys' ); ?></time>
            </p>
            <a
              href="https://tally.so/r/Gxq2JL"
              class="btn btn--secondary btn--sm horizon-card__cta"
              target="_blank"
              rel="noopener noreferrer"
            >
              <?php esc_html_e( 'Notify Me', 'jaiye-journeys' ); ?>
            </a>
          </article>
        </li>

      </ul><!-- /.oj-grid -->
    </div><!-- /.container -->
  </section><!-- /.oj-section--horizon -->


  <!-- ============================================================
       5. CTA STRIP
       Forest green background, cream text.
       ============================================================ -->
  <section
    class="oj-cta"
    aria-label="<?php esc_attr_e( 'Get in touch', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="oj-cta__inner">
        <h2 class="oj-cta__heading">
          <?php esc_html_e( 'Not sure which trip is for you?', 'jaiye-journeys' ); ?>
        </h2>
        <p class="oj-cta__sub">
          <?php esc_html_e( 'Get in touch and we will help you find your perfect journey.', 'jaiye-journeys' ); ?>
        </p>
        <a
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          class="btn btn--ghost"
          aria-label="<?php esc_attr_e( 'Talk to us — contact Jaiye Journeys', 'jaiye-journeys' ); ?>"
        >
          <?php esc_html_e( 'Talk to Us', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </section><!-- /.oj-cta -->

</main><!-- /#main-content -->

<?php get_footer(); ?>
