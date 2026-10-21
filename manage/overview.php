<?php
/**
 * Dashboard overview: needs attention, places per trip, money, latest.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

$all       = jj_manage_bookings();
$attention = array_values( array_filter( array_map( 'jj_manage_booking', $all ), function ( $b ) {
    return 'alert' === $b['tone'];
} ) );
$money     = jj_manage_money( $all );
$trips     = array_filter( jj_manage_trips(), function ( $trip ) {
    return (bool) jj_trip_rooms( $trip->ID );
} );
?>
<h1 class="m-title"><?php esc_html_e( 'Overview', 'jaiye-journeys' ); ?></h1>

<p class="m-tip js-install-tip" hidden>
  <?php esc_html_e( 'Tip: add this page to your home screen (Share → Add to Home Screen) to open it like an app.', 'jaiye-journeys' ); ?>
  <button type="button" class="m-tip__close js-install-tip-close" aria-label="<?php esc_attr_e( 'Dismiss', 'jaiye-journeys' ); ?>">×</button>
</p>

<?php if ( $attention ) : ?>
  <section class="m-card m-card--alert">
    <h2 class="m-card__title"><?php esc_html_e( 'Needs your attention', 'jaiye-journeys' ); ?></h2>
    <?php foreach ( $attention as $b ) : ?>
      <?php jj_manage_booking_row( $b ); ?>
    <?php endforeach; ?>
  </section>
<?php endif; ?>

<section class="m-stats">
  <div class="m-stat">
    <span class="m-stat__label"><?php esc_html_e( 'Bookings', 'jaiye-journeys' ); ?></span>
    <span class="m-stat__value"><?php echo (int) count( $all ); ?></span>
  </div>
  <div class="m-stat">
    <span class="m-stat__label"><?php esc_html_e( 'Booked', 'jaiye-journeys' ); ?></span>
    <span class="m-stat__value"><?php echo esc_html( jj_manage_money_line( $money, 'booked' ) ); ?></span>
  </div>
  <div class="m-stat">
    <span class="m-stat__label"><?php esc_html_e( 'Received', 'jaiye-journeys' ); ?></span>
    <span class="m-stat__value"><?php echo esc_html( jj_manage_money_line( $money, 'received' ) ); ?></span>
  </div>
  <div class="m-stat">
    <span class="m-stat__label"><?php esc_html_e( 'Still to come', 'jaiye-journeys' ); ?></span>
    <span class="m-stat__value"><?php echo esc_html( jj_manage_money_line( $money, 'due' ) ); ?></span>
  </div>
</section>

<?php foreach ( $trips as $trip ) : ?>
  <section class="m-card">
    <div class="m-card__head">
      <h2 class="m-card__title"><a href="<?php echo esc_url( jj_manage_url( 'trip/' . $trip->ID ) ); ?>"><?php echo esc_html( jj_trip_volume_title( $trip->ID ) ); ?></a></h2>
      <?php echo jj_manage_trip_pill( $trip->ID ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped when built. ?>
    </div>
    <?php jj_manage_places_html( $trip->ID ); ?>
  </section>
<?php endforeach; ?>

<section class="m-card">
  <div class="m-card__head">
    <h2 class="m-card__title"><?php esc_html_e( 'Latest bookings', 'jaiye-journeys' ); ?></h2>
    <a class="m-link" href="<?php echo esc_url( jj_manage_url( 'bookings' ) ); ?>"><?php esc_html_e( 'See all', 'jaiye-journeys' ); ?></a>
  </div>
  <?php if ( $all ) : ?>
    <?php foreach ( array_slice( $all, 0, 5 ) as $id ) : ?>
      <?php jj_manage_booking_row( jj_manage_booking( $id ) ); ?>
    <?php endforeach; ?>
  <?php else : ?>
    <p class="m-muted"><?php esc_html_e( 'No bookings yet. They’ll appear here the moment someone pays.', 'jaiye-journeys' ); ?></p>
  <?php endif; ?>
</section>
