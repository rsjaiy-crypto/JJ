<?php
/**
 * Single trip template.
 *
 * All content comes from the meta schema in inc/trip-fields.php — nothing here
 * is hardcoded per trip. Every section degrades gracefully: if a field group is
 * empty the whole section is skipped rather than rendering an empty shell.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $trip_id     = get_the_ID();
    $destination = jj_trip_field( 'destination', $trip_id );
    $booking_url = jj_trip_field( 'booking_url', $trip_id );
    $enquiry_url = jj_trip_field( 'enquiry_url', $trip_id );

    if ( empty( $enquiry_url ) ) {
        $enquiry_url = home_url( '/contact/' );
    }

    $days       = jj_trip_rows( 'days', $trip_id );
    $departures = jj_trip_rows( 'departures', $trip_id );
    $reviews    = jj_trip_rows( 'reviews', $trip_id );
    $faqs       = jj_trip_rows( 'faqs', $trip_id );
    $notes      = jj_trip_rows( 'trip_notes', $trip_id );
    $included   = jj_trip_rows( 'included_groups', $trip_id );

    $price_from = jj_trip_field( 'price_from', $trip_id );

    // Brand switch: 'btl' gets the literary treatment (named days, BTL eyebrow).
    $brand    = jj_trip_field( 'brand', $trip_id, 'jj-edit' );
    $is_btl   = ( 'btl' === $brand );

    $status   = jj_trip_status( $trip_id );
    $waitlist = ( 'coming-soon' === $status );

    // Coming soon gets the short "Get on The List" page. So does a Waitlist
    // trip with no Day by Day yet until the one-off status review on the
    // Trips screen is done, so nothing changed for those before the owner
    // chose which trips move to Coming soon.
    if ( 'announced' === $status || ( $waitlist && ! $days && ! get_option( JJ_TRIP_PRESALE_REVIEW ) ) ) {
        require get_template_directory() . '/inc/trip-coming-soon.php';
        continue;
    }

    // Waitlist is this full page with nothing about price or booking: no
    // price, booking buttons, payment terms or departure availability.
    // Every "Book" becomes "Get on The List". Prices go to The List first.
    $waitlist_url = $waitlist ? jj_trip_waitlist_url( $trip_id ) : '';
    if ( $waitlist ) {
        $booking_url = '';
        $price_from  = '';
    }

    // Sold out keeps the full page (it still sells the next departure) but
    // drops every Book link; the CTAs below fall back to Enquire.
    $sold_out = jj_trip_is_sold_out( $trip_id );
    if ( $sold_out ) {
        $booking_url = '';
    }
    ?>

<main id="main-content" class="trip trip--<?php echo esc_attr( $brand ); ?>">

  <!-- ============================================================
       1. HERO
       Header sits transparent over this and turns solid on scroll
       (see .has-transparent-header in css/trip.css).
       ============================================================ -->
  <header class="trip-hero">
    <div class="trip-hero__media">
      <?php if ( has_post_thumbnail() ) : ?>
        <?php
        the_post_thumbnail( 'full', [
            'class'   => 'trip-hero__img',
            'loading' => 'eager',
            'alt'     => esc_attr( get_the_title() ),
        ] );
        ?>
      <?php endif; ?>
      <div class="trip-hero__overlay" aria-hidden="true"></div>
    </div>

    <div class="container trip-hero__inner">
      <?php
      $eyebrow = [];
      if ( $is_btl ) {
          $eyebrow[] = __( 'Between the Lines', 'jaiye-journeys' );
      }
      if ( $waitlist ) {
          $eyebrow[] = __( 'Waitlist Open', 'jaiye-journeys' );
      } elseif ( $sold_out ) {
          $eyebrow[] = __( 'Sold Out', 'jaiye-journeys' );
      }
      ?>
      <?php if ( $eyebrow ) : ?>
        <p class="trip-hero__eyebrow">
          <?php echo esc_html( implode( ' · ', $eyebrow ) ); ?>
        </p>
      <?php endif; ?>
      <h1 class="trip-hero__title"><?php echo esc_html( jj_trip_volume_title( $trip_id ) ); ?></h1>

      <?php jj_render_trip_vibe_tags( $trip_id ); ?>
    </div>

    <span class="trip-hero__sentinel" aria-hidden="true"></span>
  </header>


  <!-- ============================================================
       2. TABLE OF CONTENTS
       Sticky bar on desktop; on phones the same list opens from the
       Contents button in the bottom bar. One entry per section that
       actually renders, in page order.
       ============================================================ -->
  <?php
  $has_inclusions = $included
      || jj_trip_field( 'not_included', $trip_id )
      || jj_trip_field( 'route_map', $trip_id )
      || jj_trip_field( 'route_copy', $trip_id );

  $toc = [ 'welcome' => __( 'Overview', 'jaiye-journeys' ) ];
  if ( $departures ) {
      $toc['book'] = $is_btl ? __( 'Chapters', 'jaiye-journeys' ) : __( 'Dates', 'jaiye-journeys' );
  }
  if ( $has_inclusions ) {
      $toc['inclusions'] = __( "What's Included", 'jaiye-journeys' );
  }
  if ( $days ) {
      $toc['day-by-day'] = __( 'Day by Day', 'jaiye-journeys' );
  }
  if ( $notes ) {
      $toc['trip-notes'] = __( 'Trip Notes', 'jaiye-journeys' );
  }
  if ( $reviews ) {
      $toc['reviews'] = __( 'Reviews', 'jaiye-journeys' );
  }
  if ( $faqs ) {
      $toc['faq'] = __( 'FAQs', 'jaiye-journeys' );
  }
  $toc['enquire'] = __( 'Enquire', 'jaiye-journeys' );
  ?>
  <nav class="trip-subnav" id="trip-subnav" aria-label="<?php esc_attr_e( 'Trip sections', 'jaiye-journeys' ); ?>">
    <div class="container trip-subnav__inner">
      <ul class="trip-subnav__list" role="list">
        <?php foreach ( $toc as $toc_id => $toc_label ) : ?>
          <li><a href="#<?php echo esc_attr( $toc_id ); ?>" class="trip-subnav__link"><?php echo esc_html( $toc_label ); ?></a></li>
        <?php endforeach; ?>
      </ul>

      <?php if ( $waitlist ) : ?>
        <a href="<?php echo esc_url( $waitlist_url ); ?>" class="btn btn--accent btn--sm trip-subnav__cta" target="_blank" rel="noopener">
          <?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?>
        </a>
      <?php elseif ( $booking_url ) : ?>
        <a href="<?php echo esc_url( $booking_url ); ?>" class="btn btn--accent btn--sm trip-subnav__cta" target="_blank" rel="noopener">
          <?php esc_html_e( 'Book Now', 'jaiye-journeys' ); ?>
        </a>
      <?php endif; ?>
    </div>
  </nav>


  <!-- ============================================================
       3. INTRO STATEMENT
       ============================================================ -->
  <?php $intro = jj_trip_field( 'intro_statement', $trip_id ); ?>
  <?php if ( $intro ) : ?>
    <section class="trip-intro">
      <div class="container container--narrow">
        <p class="trip-intro__statement js-reveal"><?php echo esc_html( $intro ); ?></p>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       4. STAGGERED COLLAGE (per-image parallax)
       ============================================================ -->
  <?php $collage = jj_trip_gallery( 'collage_images', $trip_id ); ?>
  <?php if ( $collage ) : ?>
    <section class="trip-collage" aria-label="<?php esc_attr_e( 'Trip photography', 'jaiye-journeys' ); ?>">
      <div class="container">
        <div class="trip-collage__grid">
          <?php foreach ( array_slice( $collage, 0, 5 ) as $i => $image_id ) : ?>
            <figure class="trip-collage__item trip-collage__item--<?php echo (int) ( $i + 1 ); ?> js-parallax" data-parallax-speed="<?php echo esc_attr( [ '0.06', '-0.04', '0.08', '-0.06', '0.05' ][ $i ] ); ?>">
              <?php
              echo wp_get_attachment_image( $image_id, 'large', false, [
                  'class'   => 'trip-collage__img',
                  'loading' => 'lazy',
              ] );
              ?>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       5. WELCOME + STATS + METERS | STICKY BOOKING CARD
       ============================================================ -->
  <section class="trip-welcome" id="welcome">
    <div class="container trip-welcome__grid">

      <div class="trip-welcome__main">
        <h2 class="trip-heading js-reveal">
          <?php
          printf(
              /* translators: %s: destination name */
              esc_html__( 'Welcome to %s', 'jaiye-journeys' ),
              esc_html( $destination ? $destination : get_the_title() )
          );
          ?>
        </h2>

        <ul class="trip-stats js-reveal" role="list">
          <?php if ( jj_trip_field( 'duration', $trip_id ) ) : ?>
            <li class="trip-stat">
              <span class="trip-stat__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
              </span>
              <span class="trip-stat__label"><?php esc_html_e( 'Duration', 'jaiye-journeys' ); ?></span>
              <span class="trip-stat__value"><?php echo esc_html( jj_trip_field( 'duration', $trip_id ) ); ?></span>
            </li>
          <?php endif; ?>

          <?php if ( $price_from ) : ?>
            <?php $price_usd = jj_fx_format_usd_estimate( $price_from ); ?>
            <li class="trip-stat<?php echo $price_usd ? ' trip-stat--price' : ''; ?>">
              <span class="trip-stat__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M15 6.5a4 4 0 0 0-6.5 3v3.5a3 3 0 0 1-1.5 2.6h9"/><path d="M7 11h6"/></svg>
              </span>
              <span class="trip-stat__label"><?php esc_html_e( 'From', 'jaiye-journeys' ); ?></span>
              <span class="trip-stat__value">&pound;<?php echo esc_html( number_format_i18n( (int) $price_from ) ); ?></span>
              <?php if ( $price_usd ) : ?>
                <span class="trip-stat__fx">
                  <?php
                  printf(
                      /* translators: %s: approximate USD amount, e.g. $4,100 */
                      esc_html__( 'approx. %s USD', 'jaiye-journeys' ),
                      esc_html( $price_usd )
                  );
                  ?>
                </span>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php if ( jj_trip_field( 'group_size', $trip_id ) ) : ?>
            <li class="trip-stat">
              <span class="trip-stat__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="9" cy="8" r="3"/><path d="M3 19a6 6 0 0 1 12 0"/><path d="M16 5.5a3 3 0 0 1 0 5.5"/><path d="M18 19a6 6 0 0 0-2-4.5"/></svg>
              </span>
              <span class="trip-stat__label"><?php esc_html_e( 'Group size', 'jaiye-journeys' ); ?></span>
              <span class="trip-stat__value"><?php echo esc_html( jj_trip_field( 'group_size', $trip_id ) ); ?></span>
            </li>
          <?php endif; ?>
        </ul>

        <?php $welcome_copy = jj_trip_field( 'welcome_copy', $trip_id ); ?>
        <?php if ( $welcome_copy ) : ?>
          <div class="trip-prose js-reveal">
            <?php echo wp_kses_post( wpautop( $welcome_copy ) ); ?>
          </div>
        <?php endif; ?>

        <?php require get_template_directory() . '/inc/trip-sections/meters.php'; ?>
      </div>

      <!-- Booking card: sticky on desktop, bottom bar on mobile -->
      <aside class="trip-booking" id="book" aria-label="<?php esc_attr_e( 'Trip dates and booking', 'jaiye-journeys' ); ?>">
        <div class="trip-booking__card">
          <h3 class="trip-booking__title"><?php esc_html_e( 'Trip Dates', 'jaiye-journeys' ); ?></h3>

          <?php if ( $departures && $is_btl ) : ?>
            <!--
              BTL: each departure is its own Chapter — Marrakesh is the
              Volume, every cohort that travels there is the next chapter of
              it. Auto-numbered by row order, not grouped by year, so adding
              a departure in wp-admin is all it takes for "Chapter 4" to exist.
            -->
            <?php if ( count( $departures ) > 1 ) : ?>
              <div class="trip-booking__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Chapter', 'jaiye-journeys' ); ?>">
                <?php foreach ( $departures as $i => $departure ) : ?>
                  <button type="button"
                          class="trip-booking__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
                          role="tab"
                          id="chapter-tab-<?php echo (int) $i; ?>"
                          aria-controls="chapter-panel-<?php echo (int) $i; ?>"
                          aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>">
                    <?php
                    printf(
                        /* translators: %d: chapter number */
                        esc_html__( 'Chapter %d', 'jaiye-journeys' ),
                        (int) ( $i + 1 )
                    );
                    ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php foreach ( $departures as $i => $departure ) : ?>
              <div class="trip-booking__panel<?php echo 0 === $i ? ' is-active' : ''; ?>"
                   id="chapter-panel-<?php echo (int) $i; ?>"
                   role="tabpanel"
                   aria-labelledby="chapter-tab-<?php echo (int) $i; ?>"
                   <?php echo 0 === $i ? '' : 'hidden'; ?>>
                <?php if ( 1 === count( $departures ) ) : ?>
                  <p class="trip-booking__chapter-label"><?php esc_html_e( 'Chapter 1', 'jaiye-journeys' ); ?></p>
                <?php endif; ?>
                <ul class="trip-departures" role="list">
                  <li class="trip-departure">
                    <span class="trip-departure__dates">
                      <?php
                      $dep_text = trim( ( isset( $departure['dates'] ) ? $departure['dates'] : '' ) . ' ' . ( isset( $departure['year'] ) ? $departure['year'] : '' ) );
                      echo esc_html( $dep_text );
                      ?>
                    </span>
                    <?php if ( ! $waitlist && ! empty( $departure['availability'] ) ) : ?>
                      <span class="trip-departure__status trip-departure__status--<?php echo esc_attr( ! empty( $departure['status'] ) ? $departure['status'] : 'available' ); ?>">
                        <?php echo esc_html( $departure['availability'] ); ?>
                      </span>
                    <?php endif; ?>
                  </li>
                </ul>
                <?php if ( ! $sold_out && ! $waitlist && ! empty( $departure['book_url'] ) ) : ?>
                  <a href="<?php echo esc_url( $departure['book_url'] ); ?>" class="btn btn--accent trip-booking__chapter-cta" target="_blank" rel="noopener">
                    <?php
                    printf(
                        /* translators: %d: chapter number */
                        esc_html__( 'Book Chapter %d', 'jaiye-journeys' ),
                        (int) ( $i + 1 )
                    );
                    ?>
                  </a>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>

          <?php elseif ( $departures ) : ?>
            <?php
            // JJ Edits keep the plain year-tab grouping.
            $by_year = [];
            foreach ( $departures as $departure ) {
                $year = $departure['year'] ? $departure['year'] : __( 'Dates', 'jaiye-journeys' );
                $by_year[ $year ][] = $departure;
            }
            $years = array_keys( $by_year );
            ?>

            <?php if ( count( $years ) > 1 ) : ?>
              <div class="trip-booking__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Departure year', 'jaiye-journeys' ); ?>">
                <?php foreach ( $years as $i => $year ) : ?>
                  <button type="button"
                          class="trip-booking__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
                          role="tab"
                          id="year-tab-<?php echo esc_attr( sanitize_title( $year ) ); ?>"
                          aria-controls="year-panel-<?php echo esc_attr( sanitize_title( $year ) ); ?>"
                          aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>">
                    <?php echo esc_html( $year ); ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php foreach ( $years as $i => $year ) : ?>
              <div class="trip-booking__panel<?php echo 0 === $i ? ' is-active' : ''; ?>"
                   id="year-panel-<?php echo esc_attr( sanitize_title( $year ) ); ?>"
                   role="tabpanel"
                   aria-labelledby="year-tab-<?php echo esc_attr( sanitize_title( $year ) ); ?>"
                   <?php echo 0 === $i ? '' : 'hidden'; ?>>
                <ul class="trip-departures" role="list">
                  <?php foreach ( $by_year[ $year ] as $departure ) : ?>
                    <li class="trip-departure">
                      <span class="trip-departure__dates"><?php echo esc_html( isset( $departure['dates'] ) ? $departure['dates'] : '' ); ?></span>
                      <?php if ( ! $waitlist && ! empty( $departure['availability'] ) ) : ?>
                        <span class="trip-departure__status trip-departure__status--<?php echo esc_attr( ! empty( $departure['status'] ) ? $departure['status'] : 'available' ); ?>">
                          <?php echo esc_html( $departure['availability'] ); ?>
                        </span>
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ( $waitlist ) : ?>
            <div class="trip-booking__actions">
              <p class="trip-booking__waitlist"><?php esc_html_e( 'Prices and first access go to The List before anyone else. No deposit, no commitment.', 'jaiye-journeys' ); ?></p>
              <a href="<?php echo esc_url( $waitlist_url ); ?>" class="btn btn--accent" target="_blank" rel="noopener">
                <?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?>
              </a>
            </div>
          <?php else : ?>
          <div class="trip-booking__actions">
            <?php if ( $sold_out ) : ?>
              <p class="trip-booking__sold-out"><?php esc_html_e( 'This trip is sold out. Enquire to hear about the next departure.', 'jaiye-journeys' ); ?></p>
            <?php elseif ( $booking_url ) : ?>
              <a href="<?php echo esc_url( $booking_url ); ?>" class="btn btn--accent" target="_blank" rel="noopener">
                <?php esc_html_e( 'Book Now', 'jaiye-journeys' ); ?>
              </a>
            <?php endif; ?>
            <a href="<?php echo esc_url( $enquiry_url ); ?>" class="btn btn--secondary">
              <?php esc_html_e( 'Enquire', 'jaiye-journeys' ); ?>
            </a>
          </div>

          <?php $terms = jj_trip_field( 'payment_terms', $trip_id ); ?>
          <?php if ( $terms ) : ?>
            <p class="trip-booking__terms"><?php echo esc_html( $terms ); ?></p>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      </aside>

    </div>
  </section>


  <!-- ============================================================
       6. CAROUSEL + INCLUSIONS ACCORDIONS
       ============================================================ -->
  <?php $carousel = jj_trip_gallery( 'carousel_images', $trip_id ); ?>
  <?php if ( $carousel ) : ?>
    <section class="trip-carousel js-carousel" aria-label="<?php esc_attr_e( 'Trip gallery', 'jaiye-journeys' ); ?>">
      <div class="trip-carousel__viewport">
        <ul class="trip-carousel__track" role="list">
          <?php foreach ( $carousel as $image_id ) : ?>
            <li class="trip-carousel__slide">
              <?php
              echo wp_get_attachment_image( $image_id, 'large', false, [
                  'class'   => 'trip-carousel__img',
                  'loading' => 'lazy',
              ] );
              ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="trip-carousel__controls">
        <button type="button" class="trip-carousel__btn js-carousel-prev" aria-label="<?php esc_attr_e( 'Previous image', 'jaiye-journeys' ); ?>">&larr;</button>
        <button type="button" class="trip-carousel__btn js-carousel-next" aria-label="<?php esc_attr_e( 'Next image', 'jaiye-journeys' ); ?>">&rarr;</button>
      </div>
    </section>
  <?php endif; ?>

  <?php require get_template_directory() . '/inc/trip-sections/inclusions.php'; ?>


  <!-- ============================================================
       7. AUTO-SCROLLING PHOTO STRIP
       Duplicated once in markup so the marquee can loop seamlessly.
       ============================================================ -->
  <?php $strip = jj_trip_gallery( 'strip_images', $trip_id ); ?>
  <?php if ( $strip ) : ?>
    <section class="trip-strip" aria-label="<?php esc_attr_e( 'Trip photography', 'jaiye-journeys' ); ?>">
      <div class="trip-strip__track js-marquee">
        <?php for ( $pass = 0; $pass < 2; $pass++ ) : ?>
          <?php foreach ( $strip as $image_id ) : ?>
            <div class="trip-strip__item" <?php echo 1 === $pass ? 'aria-hidden="true"' : ''; ?>>
              <?php
              echo wp_get_attachment_image( $image_id, 'medium_large', false, [
                  'class'   => 'trip-strip__img',
                  'loading' => 'lazy',
              ] );
              ?>
            </div>
          <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       8. DAY BY DAY (tabbed)
       ============================================================ -->
  <?php require get_template_directory() . '/inc/trip-sections/days.php'; ?>


  <!-- ============================================================
       9. TRIP NOTES
       ============================================================ -->
  <?php require get_template_directory() . '/inc/trip-sections/notes.php'; ?>


  <!-- ============================================================
       10. MID-PAGE CTA BANNER
       ============================================================ -->
  <section class="trip-cta">
    <div class="container trip-cta__inner js-reveal">
      <h2 class="trip-cta__title">
        <?php
        if ( $waitlist ) {
            esc_html_e( 'Be first to know', 'jaiye-journeys' );
        } elseif ( $sold_out ) {
            esc_html_e( 'Want a place on the next one?', 'jaiye-journeys' );
        } else {
            esc_html_e( 'Ready to secure your place?', 'jaiye-journeys' );
        }
        ?>
      </h2>
      <div class="trip-cta__actions">
        <?php if ( $waitlist ) : ?>
          <a href="<?php echo esc_url( $waitlist_url ); ?>" class="btn btn--accent" target="_blank" rel="noopener">
            <?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?>
          </a>
        <?php endif; ?>
        <?php if ( $booking_url ) : ?>
          <a href="<?php echo esc_url( $booking_url ); ?>" class="btn btn--accent" target="_blank" rel="noopener">
            <?php esc_html_e( 'Book Now', 'jaiye-journeys' ); ?>
          </a>
        <?php endif; ?>
        <a href="<?php echo esc_url( $enquiry_url ); ?>" class="btn btn--ghost">
          <?php esc_html_e( 'Enquire', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </section>


  <!-- ============================================================
       11. REVIEWS
       ============================================================ -->
  <?php if ( $reviews ) : ?>
    <?php
    $rating_value = jj_trip_field( 'rating_value', $trip_id );
    $rating_count = jj_trip_field( 'rating_count', $trip_id );
    ?>
    <section class="trip-reviews" id="reviews">
      <div class="container">
        <div class="trip-reviews__summary js-reveal">
          <?php if ( $rating_value ) : ?>
            <p class="trip-reviews__score"><?php echo esc_html( $rating_value ); ?><span>/5</span></p>
            <p class="trip-reviews__stars" aria-hidden="true">
              <?php echo esc_html( str_repeat( '★', min( 5, (int) round( (float) $rating_value ) ) ) ); ?>
            </p>
          <?php endif; ?>
          <?php if ( $rating_count ) : ?>
            <p class="trip-reviews__count">
              <?php
              printf(
                  /* translators: %s: number of reviews */
                  esc_html__( 'Based on %s traveller reviews', 'jaiye-journeys' ),
                  esc_html( number_format_i18n( (int) $rating_count ) )
              );
              ?>
            </p>
          <?php endif; ?>
        </div>

        <div class="trip-reviews__carousel js-carousel">
          <div class="trip-carousel__viewport">
            <ul class="trip-carousel__track" role="list">
              <?php foreach ( $reviews as $review ) : ?>
                <li class="trip-carousel__slide trip-review">
                  <?php if ( ! empty( $review['rating'] ) ) : ?>
                    <p class="trip-review__stars" aria-label="<?php echo esc_attr( sprintf( '%d out of 5', (int) $review['rating'] ) ); ?>">
                      <?php echo esc_html( str_repeat( '★', min( 5, absint( $review['rating'] ) ) ) ); ?>
                    </p>
                  <?php endif; ?>
                  <?php if ( ! empty( $review['body'] ) ) : ?>
                    <blockquote class="trip-review__body"><?php echo esc_html( $review['body'] ); ?></blockquote>
                  <?php endif; ?>
                  <p class="trip-review__meta">
                    <span class="trip-review__name"><?php echo esc_html( isset( $review['name'] ) ? $review['name'] : '' ); ?></span>
                    <?php if ( ! empty( $review['location'] ) ) : ?>
                      <span class="trip-review__location"><?php echo esc_html( $review['location'] ); ?></span>
                    <?php endif; ?>
                  </p>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="trip-carousel__controls">
            <button type="button" class="trip-carousel__btn js-carousel-prev" aria-label="<?php esc_attr_e( 'Previous review', 'jaiye-journeys' ); ?>">&larr;</button>
            <button type="button" class="trip-carousel__btn js-carousel-next" aria-label="<?php esc_attr_e( 'Next review', 'jaiye-journeys' ); ?>">&rarr;</button>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       12. SOCIAL STRIP
       ============================================================ -->
  <?php
  $social        = jj_trip_gallery( 'social_images', $trip_id );
  $social_handle = jj_trip_field( 'social_handle', $trip_id );
  ?>
  <?php if ( $social ) : ?>
    <section class="trip-social" aria-label="<?php esc_attr_e( 'Social', 'jaiye-journeys' ); ?>">
      <div class="container">
        <?php if ( $social_handle ) : ?>
          <h2 class="trip-heading trip-heading--center js-reveal">
            <a href="<?php echo esc_url( 'https://instagram.com/' . ltrim( $social_handle, '@' ) ); ?>" target="_blank" rel="noopener">
              @<?php echo esc_html( ltrim( $social_handle, '@' ) ); ?>
            </a>
          </h2>
        <?php endif; ?>

        <div class="trip-social__grid">
          <?php foreach ( $social as $image_id ) : ?>
            <div class="trip-social__item js-reveal">
              <?php
              echo wp_get_attachment_image( $image_id, 'medium_large', false, [
                  'class'   => 'trip-social__img',
                  'loading' => 'lazy',
              ] );
              ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       13. FAQ (two columns)
       ============================================================ -->
  <?php require get_template_directory() . '/inc/trip-sections/faq.php'; ?>


  <!-- ============================================================
       14. ENQUIRY
       ============================================================ -->
  <section class="trip-enquire" id="enquire">
    <div class="container trip-enquire__grid">
      <?php $enquiry_image = absint( jj_trip_field( 'enquiry_image', $trip_id ) ); ?>
      <?php if ( $enquiry_image ) : ?>
        <div class="trip-enquire__media js-reveal">
          <?php
          echo wp_get_attachment_image( $enquiry_image, 'large', false, [
              'class'   => 'trip-enquire__img',
              'loading' => 'lazy',
          ] );
          ?>
        </div>
      <?php endif; ?>

      <div class="trip-enquire__body js-reveal">
        <h2 class="trip-heading"><?php esc_html_e( 'Still deciding?', 'jaiye-journeys' ); ?></h2>
        <p class="trip-enquire__copy">
          <?php
          printf(
              /* translators: %s: destination name */
              esc_html__( 'Send us your questions about %s and we will come back to you personally — no automated reply, no pressure.', 'jaiye-journeys' ),
              esc_html( $destination ? $destination : get_the_title() )
          );
          ?>
        </p>
        <?php $enquiry_embed = jj_trip_field( 'enquiry_embed', $trip_id ); ?>
        <div class="trip-enquire__actions">
          <a href="<?php echo esc_url( $enquiry_embed ? $enquiry_embed : $enquiry_url ); ?>"
             class="btn btn--primary"
             <?php echo $enquiry_embed ? 'target="_blank" rel="noopener"' : ''; ?>>
            <?php esc_html_e( 'Send an Enquiry', 'jaiye-journeys' ); ?>
          </a>
        </div>
      </div>
    </div>
  </section>


  <!-- Mobile sticky booking bar (hidden on desktop), with the contents list -->
  <div class="trip-mobile-bar" id="trip-mobile-bar">
    <details class="trip-toc js-toc">
      <summary class="trip-toc__toggle"><?php esc_html_e( 'Contents', 'jaiye-journeys' ); ?></summary>
      <nav class="trip-toc__panel" aria-label="<?php esc_attr_e( 'Trip sections', 'jaiye-journeys' ); ?>">
        <ul class="trip-toc__list" role="list">
          <?php foreach ( $toc as $toc_id => $toc_label ) : ?>
            <li><a href="#<?php echo esc_attr( $toc_id ); ?>" class="trip-toc__link"><?php echo esc_html( $toc_label ); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </details>
    <div class="trip-mobile-bar__price">
      <?php if ( $price_from ) : ?>
        <span class="trip-mobile-bar__label"><?php esc_html_e( 'From', 'jaiye-journeys' ); ?></span>
        <span class="trip-mobile-bar__value">&pound;<?php echo esc_html( number_format_i18n( (int) $price_from ) ); ?></span>
      <?php endif; ?>
    </div>
    <?php if ( $waitlist ) : ?>
      <a href="<?php echo esc_url( $waitlist_url ); ?>" class="btn btn--accent btn--sm" target="_blank" rel="noopener">
        <?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?>
      </a>
    <?php elseif ( $booking_url ) : ?>
      <a href="<?php echo esc_url( $booking_url ); ?>" class="btn btn--accent btn--sm" target="_blank" rel="noopener">
        <?php esc_html_e( 'Book Now', 'jaiye-journeys' ); ?>
      </a>
    <?php else : ?>
      <a href="<?php echo esc_url( $enquiry_url ); ?>" class="btn btn--accent btn--sm">
        <?php esc_html_e( 'Enquire', 'jaiye-journeys' ); ?>
      </a>
    <?php endif; ?>
  </div>

</main>

<?php
endwhile;

get_footer();
