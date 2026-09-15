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
    $vibe_tags   = array_filter( array_map( 'trim', explode( ',', (string) jj_trip_field( 'vibe_tags', $trip_id ) ) ) );
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

    // Not-yet-bookable trips get a short waitlist page instead of the full
    // itinerary — a template full of empty sections reads as unfinished.
    if ( jj_trip_is_coming_soon( $trip_id ) ) {
        require get_template_directory() . '/inc/trip-coming-soon.php';
        continue;
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
      <?php if ( $is_btl ) : ?>
        <p class="trip-hero__eyebrow"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></p>
      <?php endif; ?>
      <h1 class="trip-hero__title"><?php the_title(); ?></h1>

      <?php if ( $vibe_tags ) : ?>
        <ul class="trip-hero__tags" role="list">
          <?php foreach ( array_slice( $vibe_tags, 0, 3 ) as $tag ) : ?>
            <li class="trip-hero__tag"><?php echo esc_html( $tag ); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <span class="trip-hero__sentinel" aria-hidden="true"></span>
  </header>


  <!-- ============================================================
       2. STICKY ANCHOR SUB-NAV
       ============================================================ -->
  <nav class="trip-subnav" id="trip-subnav" aria-label="<?php esc_attr_e( 'Trip sections', 'jaiye-journeys' ); ?>">
    <div class="container trip-subnav__inner">
      <ul class="trip-subnav__list" role="list">
        <?php if ( $days ) : ?>
          <li><a href="#day-by-day" class="trip-subnav__link"><?php esc_html_e( 'Day by Day', 'jaiye-journeys' ); ?></a></li>
        <?php endif; ?>
        <?php if ( $notes ) : ?>
          <li><a href="#trip-notes" class="trip-subnav__link"><?php esc_html_e( 'Trip Notes', 'jaiye-journeys' ); ?></a></li>
        <?php endif; ?>
        <?php if ( $reviews ) : ?>
          <li><a href="#reviews" class="trip-subnav__link"><?php esc_html_e( 'Reviews', 'jaiye-journeys' ); ?></a></li>
        <?php endif; ?>
        <li><a href="#enquire" class="trip-subnav__link"><?php esc_html_e( 'Enquire', 'jaiye-journeys' ); ?></a></li>
      </ul>

      <?php if ( $booking_url ) : ?>
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
            <li class="trip-stat">
              <span class="trip-stat__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M15 6.5a4 4 0 0 0-6.5 3v3.5a3 3 0 0 1-1.5 2.6h9"/><path d="M7 11h6"/></svg>
              </span>
              <span class="trip-stat__label"><?php esc_html_e( 'From', 'jaiye-journeys' ); ?></span>
              <span class="trip-stat__value">&pound;<?php echo esc_html( number_format_i18n( (int) $price_from ) ); ?></span>
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

        <?php
        $meters = [
            'meter_pace'       => __( 'Pace', 'jaiye-journeys' ),
            'meter_culture'    => __( 'Culture', 'jaiye-journeys' ),
            'meter_food'       => __( 'Food', 'jaiye-journeys' ),
            'meter_adventure'  => __( 'Adventure', 'jaiye-journeys' ),
            'meter_nightlife'  => __( 'Nightlife', 'jaiye-journeys' ),
            'meter_relaxation' => __( 'Relaxation', 'jaiye-journeys' ),
        ];
        ?>
        <div class="trip-meters js-reveal">
          <h3 class="trip-meters__title"><?php esc_html_e( 'Trip style', 'jaiye-journeys' ); ?></h3>
          <?php foreach ( $meters as $meter_key => $meter_label ) : ?>
            <?php $meter_value = absint( jj_trip_field( $meter_key, $trip_id, 50 ) ); ?>
            <div class="trip-meter">
              <span class="trip-meter__label"><?php echo esc_html( $meter_label ); ?></span>
              <span class="trip-meter__track"
                    role="img"
                    aria-label="<?php echo esc_attr( sprintf( '%1$s: %2$d out of 100', $meter_label, $meter_value ) ); ?>">
                <span class="trip-meter__fill js-meter-fill" style="--meter-value: <?php echo esc_attr( $meter_value ); ?>%;"></span>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Booking card: sticky on desktop, bottom bar on mobile -->
      <aside class="trip-booking" id="book" aria-label="<?php esc_attr_e( 'Trip dates and booking', 'jaiye-journeys' ); ?>">
        <div class="trip-booking__card">
          <h3 class="trip-booking__title"><?php esc_html_e( 'Trip Dates', 'jaiye-journeys' ); ?></h3>

          <?php if ( $departures ) : ?>
            <?php
            // Group departures into year tabs, preserving entry order.
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
                      <?php if ( ! empty( $departure['availability'] ) ) : ?>
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

          <div class="trip-booking__actions">
            <?php if ( $booking_url ) : ?>
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

  <?php
  $not_included = jj_trip_lines( jj_trip_field( 'not_included', $trip_id ) );
  $route_map    = absint( jj_trip_field( 'route_map', $trip_id ) );
  $route_copy   = jj_trip_field( 'route_copy', $trip_id );
  ?>
  <?php if ( $included || $not_included || $route_map || $route_copy ) : ?>
    <?php
    // Side image: explicit field first, else reuse the opening carousel shot.
    $inclusions_image = absint( jj_trip_field( 'inclusions_image', $trip_id ) );
    if ( ! $inclusions_image && ! empty( $carousel[0] ) ) {
        $inclusions_image = absint( $carousel[0] );
    }
    ?>
    <section class="trip-inclusions" id="inclusions">
      <div class="container trip-inclusions__grid">
        <div class="trip-inclusions__main">

        <?php if ( $included ) : ?>
          <details class="trip-accordion js-accordion" open>
            <summary class="trip-accordion__summary"><?php esc_html_e( "What's Included", 'jaiye-journeys' ); ?></summary>
            <div class="trip-accordion__body">
              <?php foreach ( $included as $group ) : ?>
                <?php $items = jj_trip_lines( isset( $group['items'] ) ? $group['items'] : '' ); ?>
                <div class="trip-included-group">
                  <?php if ( ! empty( $group['group_title'] ) ) : ?>
                    <h4 class="trip-included-group__title"><?php echo esc_html( $group['group_title'] ); ?></h4>
                  <?php endif; ?>
                  <?php if ( $items ) : ?>
                    <ul class="trip-list" role="list">
                      <?php foreach ( $items as $item ) : ?>
                        <li><?php echo esc_html( $item ); ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </details>
        <?php endif; ?>

        <?php if ( $not_included ) : ?>
          <details class="trip-accordion js-accordion">
            <summary class="trip-accordion__summary"><?php esc_html_e( "What's Not Included", 'jaiye-journeys' ); ?></summary>
            <div class="trip-accordion__body">
              <ul class="trip-list trip-list--excluded" role="list">
                <?php foreach ( $not_included as $item ) : ?>
                  <li><?php echo esc_html( $item ); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </details>
        <?php endif; ?>

        <?php if ( $route_map || $route_copy ) : ?>
          <details class="trip-accordion js-accordion">
            <summary class="trip-accordion__summary"><?php esc_html_e( "Where You'll Go", 'jaiye-journeys' ); ?></summary>
            <div class="trip-accordion__body">
              <?php if ( $route_copy ) : ?>
                <div class="trip-prose"><?php echo wp_kses_post( wpautop( $route_copy ) ); ?></div>
              <?php endif; ?>
              <?php if ( $route_map ) : ?>
                <?php
                echo wp_get_attachment_image( $route_map, 'large', false, [
                    'class'   => 'trip-route-map',
                    'loading' => 'lazy',
                ] );
                ?>
              <?php endif; ?>
            </div>
          </details>
        <?php endif; ?>

        </div><!-- /.trip-inclusions__main -->

        <?php if ( $inclusions_image ) : ?>
          <div class="trip-inclusions__media js-reveal">
            <?php
            echo wp_get_attachment_image( $inclusions_image, 'large', false, [
                'class'   => 'trip-inclusions__img',
                'loading' => 'lazy',
            ] );
            ?>
          </div>
        <?php endif; ?>

      </div>
    </section>
  <?php endif; ?>


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
  <?php if ( $days ) : ?>
    <section class="trip-days" id="day-by-day">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Day by Day', 'jaiye-journeys' ); ?></h2>

        <div class="trip-days__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Itinerary days', 'jaiye-journeys' ); ?>">
          <?php foreach ( $days as $i => $day ) : ?>
            <button type="button"
                    class="trip-days__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
                    role="tab"
                    id="day-tab-<?php echo (int) ( $i + 1 ); ?>"
                    aria-controls="day-panel-<?php echo (int) ( $i + 1 ); ?>"
                    aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
                    tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
              <?php
              // BTL chapters use named days (The Prologue, The Crescendo…);
              // Edits stay plainly numbered.
              if ( ! empty( $day['day_name'] ) ) {
                  echo esc_html( $day['day_name'] );
              } else {
                  printf(
                      /* translators: %d: day number */
                      esc_html__( 'Day %d', 'jaiye-journeys' ),
                      (int) ( $i + 1 )
                  );
              }
              ?>
            </button>
          <?php endforeach; ?>
        </div>

        <?php foreach ( $days as $i => $day ) : ?>
          <div class="trip-days__panel<?php echo 0 === $i ? ' is-active' : ''; ?>"
               id="day-panel-<?php echo (int) ( $i + 1 ); ?>"
               role="tabpanel"
               aria-labelledby="day-tab-<?php echo (int) ( $i + 1 ); ?>"
               <?php echo 0 === $i ? '' : 'hidden'; ?>>
            <div class="trip-day">
              <div class="trip-day__copy">
                <?php if ( ! empty( $day['location'] ) ) : ?>
                  <p class="trip-day__location overline"><?php echo esc_html( $day['location'] ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $day['headline'] ) ) : ?>
                  <h3 class="trip-day__headline"><?php echo esc_html( $day['headline'] ); ?></h3>
                <?php endif; ?>
                <?php if ( ! empty( $day['body'] ) ) : ?>
                  <div class="trip-prose"><?php echo wp_kses_post( wpautop( $day['body'] ) ); ?></div>
                <?php endif; ?>
              </div>

              <?php if ( ! empty( $day['image_1'] ) || ! empty( $day['image_2'] ) ) : ?>
                <div class="trip-day__media">
                  <?php foreach ( [ 'image_1', 'image_2' ] as $image_key ) : ?>
                    <?php if ( ! empty( $day[ $image_key ] ) ) : ?>
                      <?php
                      echo wp_get_attachment_image( absint( $day[ $image_key ] ), 'large', false, [
                          'class'   => 'trip-day__img',
                          'loading' => 'lazy',
                      ] );
                      ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       9. TRIP NOTES
       ============================================================ -->
  <?php if ( $notes ) : ?>
    <section class="trip-notes" id="trip-notes">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Trip Notes', 'jaiye-journeys' ); ?></h2>

        <?php
        // Split out the featured note so it can anchor the left column.
        $featured_note = null;
        $stack_notes   = [];

        foreach ( $notes as $note ) {
            if ( null === $featured_note && ! empty( $note['featured'] ) ) {
                $featured_note = $note;
            } else {
                $stack_notes[] = $note;
            }
        }
        ?>

        <div class="trip-notes__layout<?php echo $featured_note ? '' : ' trip-notes__layout--no-feature'; ?>">

          <?php if ( $featured_note ) : ?>
            <article class="trip-note-feature js-reveal">
              <span class="trip-note-feature__icon" aria-hidden="true">
                <?php echo jj_trip_note_icon( isset( $featured_note['icon'] ) ? $featured_note['icon'] : 'plane' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline SVG. ?>
              </span>
              <?php if ( ! empty( $featured_note['title'] ) ) : ?>
                <h3 class="trip-note-feature__title"><?php echo esc_html( $featured_note['title'] ); ?></h3>
              <?php endif; ?>
              <?php if ( ! empty( $featured_note['body'] ) ) : ?>
                <div class="trip-note-feature__body"><?php echo wp_kses_post( wpautop( $featured_note['body'] ) ); ?></div>
              <?php endif; ?>
            </article>
          <?php endif; ?>

          <?php if ( $stack_notes ) : ?>
            <!--
              Scroll accordion. Defaults to every panel open; trip.js adds
              .is-interactive to switch on collapsing, so this stays readable
              with JS off or under prefers-reduced-motion.
            -->
            <div class="trip-note-stack js-note-stack">
              <?php foreach ( $stack_notes as $index => $note ) : ?>
                <article class="trip-note-item<?php echo 0 === $index ? ' is-open' : ''; ?>">
                  <h3 class="trip-note-item__heading">
                    <button type="button"
                            class="trip-note-item__trigger"
                            aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                            aria-controls="note-panel-<?php echo (int) $index; ?>">
                      <span class="trip-note-item__icon" aria-hidden="true">
                        <?php echo jj_trip_note_icon( isset( $note['icon'] ) ? $note['icon'] : 'route' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline SVG. ?>
                      </span>
                      <span class="trip-note-item__title"><?php echo esc_html( isset( $note['title'] ) ? $note['title'] : '' ); ?></span>
                      <span class="trip-note-item__chevron" aria-hidden="true"></span>
                    </button>
                  </h3>
                  <div class="trip-note-item__panel" id="note-panel-<?php echo (int) $index; ?>">
                    <div class="trip-note-item__panel-inner">
                      <?php if ( ! empty( $note['body'] ) ) : ?>
                        <?php echo wp_kses_post( wpautop( $note['body'] ) ); ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>
  <?php endif; ?>


  <!-- ============================================================
       10. MID-PAGE CTA BANNER
       ============================================================ -->
  <section class="trip-cta">
    <div class="container trip-cta__inner js-reveal">
      <h2 class="trip-cta__title"><?php esc_html_e( 'Ready to secure your place?', 'jaiye-journeys' ); ?></h2>
      <div class="trip-cta__actions">
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
  <?php if ( $faqs ) : ?>
    <section class="trip-faq" id="faq">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Frequently Asked Questions', 'jaiye-journeys' ); ?></h2>
        <div class="trip-faq__columns">
          <?php foreach ( $faqs as $faq ) : ?>
            <details class="trip-accordion js-accordion">
              <summary class="trip-accordion__summary"><?php echo esc_html( isset( $faq['question'] ) ? $faq['question'] : '' ); ?></summary>
              <div class="trip-accordion__body">
                <div class="trip-prose"><?php echo wp_kses_post( wpautop( isset( $faq['answer'] ) ? $faq['answer'] : '' ) ); ?></div>
              </div>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>


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


  <!-- Mobile sticky booking bar (hidden on desktop) -->
  <div class="trip-mobile-bar" id="trip-mobile-bar">
    <div class="trip-mobile-bar__price">
      <?php if ( $price_from ) : ?>
        <span class="trip-mobile-bar__label"><?php esc_html_e( 'From', 'jaiye-journeys' ); ?></span>
        <span class="trip-mobile-bar__value">&pound;<?php echo esc_html( number_format_i18n( (int) $price_from ) ); ?></span>
      <?php endif; ?>
    </div>
    <?php if ( $booking_url ) : ?>
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
