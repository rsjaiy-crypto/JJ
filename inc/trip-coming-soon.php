<?php
/**
 * Condensed "coming soon" trip page.
 *
 * Rendered instead of the full 14-section template when a trip's status is
 * coming-soon. Deliberately short: a thin page with empty itinerary, pricing
 * and review sections reads as unfinished and converts worse than no page at
 * all. This one has a single job — capture the waitlist signup.
 *
 * Expects $trip_id, $brand, $is_btl from single-trip.php.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

$cs_destination = jj_trip_field( 'destination', $trip_id );
$cs_region      = jj_trip_region_label( jj_trip_field( 'region', $trip_id ) );
$cs_intro       = jj_trip_field( 'intro_statement', $trip_id );
$cs_copy        = jj_trip_field( 'welcome_copy', $trip_id );
$cs_duration    = jj_trip_field( 'duration', $trip_id );
$cs_collage     = jj_trip_gallery( 'collage_images', $trip_id );
$cs_departures  = jj_trip_rows( 'departures', $trip_id );
$cs_tags        = array_filter( array_map( 'trim', explode( ',', (string) jj_trip_field( 'vibe_tags', $trip_id ) ) ) );

$cs_waitlist = jj_trip_field( 'booking_url', $trip_id );
if ( ! $cs_waitlist ) {
    $cs_waitlist = jj_trip_field( 'enquiry_embed', $trip_id );
}
if ( ! $cs_waitlist ) {
    $cs_waitlist = jj_trip_field( 'enquiry_url', $trip_id );
}
if ( ! $cs_waitlist ) {
    $cs_waitlist = home_url( '/contact/' );
}

// Rough window: first departure's dates, else its year.
$cs_window = '';
if ( ! empty( $cs_departures[0] ) ) {
    $first     = $cs_departures[0];
    $cs_window = trim( ( isset( $first['dates'] ) ? $first['dates'] : '' ) . ' ' . ( isset( $first['year'] ) ? $first['year'] : '' ) );
}
?>

<main id="main-content" class="trip trip--<?php echo esc_attr( $brand ); ?> trip--soon">

  <header class="trip-hero trip-hero--soon">
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
      <p class="trip-hero__eyebrow">
        <?php
        echo esc_html(
            $is_btl
                ? __( 'Between the Lines · Coming Soon', 'jaiye-journeys' )
                : __( 'Coming Soon', 'jaiye-journeys' )
        );
        ?>
      </p>

      <h1 class="trip-hero__title"><?php echo esc_html( jj_trip_volume_title( $trip_id ) ); ?></h1>

      <?php if ( $cs_tags ) : ?>
        <ul class="trip-hero__tags" role="list">
          <?php foreach ( array_slice( $cs_tags, 0, 3 ) as $cs_tag ) : ?>
            <li class="trip-hero__tag"><?php echo esc_html( $cs_tag ); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <span class="trip-hero__sentinel" aria-hidden="true"></span>
  </header>

  <section class="trip-soon">
    <div class="container container--narrow">

      <?php if ( $cs_intro ) : ?>
        <p class="trip-soon__statement js-reveal"><?php echo esc_html( $cs_intro ); ?></p>
      <?php endif; ?>

      <?php if ( $cs_copy ) : ?>
        <div class="trip-prose js-reveal"><?php echo wp_kses_post( wpautop( $cs_copy ) ); ?></div>
      <?php endif; ?>

      <!-- What's known so far. Honest about what isn't settled yet. -->
      <dl class="trip-soon__facts js-reveal">
        <?php if ( $cs_destination || $cs_region ) : ?>
          <div class="trip-soon__fact">
            <dt><?php esc_html_e( 'Where', 'jaiye-journeys' ); ?></dt>
            <dd><?php echo esc_html( $cs_destination ? $cs_destination : $cs_region ); ?></dd>
          </div>
        <?php endif; ?>

        <div class="trip-soon__fact">
          <dt><?php esc_html_e( 'When', 'jaiye-journeys' ); ?></dt>
          <dd><?php echo esc_html( $cs_window ? $cs_window : __( 'Dates being confirmed', 'jaiye-journeys' ) ); ?></dd>
        </div>

        <?php if ( $cs_duration ) : ?>
          <div class="trip-soon__fact">
            <dt><?php esc_html_e( 'Length', 'jaiye-journeys' ); ?></dt>
            <dd><?php echo esc_html( $cs_duration ); ?></dd>
          </div>
        <?php endif; ?>

        <div class="trip-soon__fact">
          <dt><?php esc_html_e( 'Price', 'jaiye-journeys' ); ?></dt>
          <dd><?php esc_html_e( 'Announced to the waitlist first', 'jaiye-journeys' ); ?></dd>
        </div>
      </dl>

      <div class="trip-soon__cta js-reveal">
        <h2 class="trip-soon__cta-title"><?php esc_html_e( 'Be first to know', 'jaiye-journeys' ); ?></h2>
        <p class="trip-soon__cta-copy">
          <?php esc_html_e( 'Join the waitlist and you will get the dates, the full itinerary and first access to places before this chapter is shared more widely. No deposit, no commitment.', 'jaiye-journeys' ); ?>
        </p>
        <a href="<?php echo esc_url( $cs_waitlist ); ?>" class="btn btn--accent" target="_blank" rel="noopener">
          <?php esc_html_e( 'Join the Waitlist', 'jaiye-journeys' ); ?>
        </a>
      </div>

    </div>

    <?php if ( $cs_collage ) : ?>
      <div class="container">
        <div class="trip-soon__gallery js-reveal">
          <?php foreach ( array_slice( $cs_collage, 0, 3 ) as $cs_image ) : ?>
            <?php
            echo wp_get_attachment_image( $cs_image, 'large', false, [
                'class'   => 'trip-soon__img',
                'loading' => 'lazy',
            ] );
            ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="container container--narrow">
      <p class="trip-soon__back">
        <a href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>">
          <?php esc_html_e( 'See journeys you can book now', 'jaiye-journeys' ); ?>
        </a>
      </p>
    </div>
  </section>

</main>
