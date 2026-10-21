<?php
/**
 * Every booking, filterable by trip and status, searchable by guest.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only filters.
$filter = [
    'trip'   => isset( $_GET['trip'] ) ? absint( $_GET['trip'] ) : 0,
    'status' => isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '',
    'q'      => isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
    'test'   => ! empty( $_GET['test'] ),
];
// phpcs:enable
$ids      = jj_manage_bookings( $filter );
$statuses = [
    ''           => __( 'All statuses', 'jaiye-journeys' ),
    'deposit'    => jj_booking_status_label( '' ),
    'on-plan'    => jj_booking_status_label( 'on-plan' ),
    'paid'       => jj_booking_status_label( 'paid' ),
    'overdue'    => jj_booking_status_label( 'overdue' ),
    'plan-error' => jj_booking_status_label( 'plan-error' ),
];
?>
<h1 class="m-title"><?php esc_html_e( 'Bookings', 'jaiye-journeys' ); ?></h1>
<?php jj_manage_notice(); ?>

<form class="m-filters js-filters" method="get" action="<?php echo esc_url( jj_manage_url( 'bookings' ) ); ?>">
  <label class="m-sr" for="m-q"><?php esc_html_e( 'Search', 'jaiye-journeys' ); ?></label>
  <input id="m-q" class="m-input" type="search" name="q" value="<?php echo esc_attr( $filter['q'] ); ?>" placeholder="<?php esc_attr_e( 'Search name or email', 'jaiye-journeys' ); ?>">
  <label class="m-sr" for="m-trip"><?php esc_html_e( 'Trip', 'jaiye-journeys' ); ?></label>
  <select id="m-trip" class="m-input" name="trip">
    <option value=""><?php esc_html_e( 'All trips', 'jaiye-journeys' ); ?></option>
    <?php foreach ( jj_manage_trips() as $trip ) : ?>
      <?php if ( jj_trip_rooms( $trip->ID ) ) : ?>
        <option value="<?php echo (int) $trip->ID; ?>" <?php selected( $filter['trip'], $trip->ID ); ?>><?php echo esc_html( jj_trip_volume_title( $trip->ID ) ); ?></option>
      <?php endif; ?>
    <?php endforeach; ?>
  </select>
  <label class="m-sr" for="m-status"><?php esc_html_e( 'Status', 'jaiye-journeys' ); ?></label>
  <select id="m-status" class="m-input" name="status">
    <?php foreach ( $statuses as $key => $label ) : ?>
      <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $filter['status'], $key ); ?>><?php echo esc_html( $label ); ?></option>
    <?php endforeach; ?>
  </select>
  <label class="m-check"><input type="checkbox" name="test" value="1" <?php checked( $filter['test'] ); ?>> <?php esc_html_e( 'Include test bookings', 'jaiye-journeys' ); ?></label>
  <button class="m-btn m-btn--quiet js-filters-submit" type="submit"><?php esc_html_e( 'Show', 'jaiye-journeys' ); ?></button>
</form>

<section class="m-card">
  <p class="m-muted">
    <?php
    /* translators: %d: number of bookings */
    echo esc_html( sprintf( _n( '%d booking', '%d bookings', count( $ids ), 'jaiye-journeys' ), count( $ids ) ) );
    if ( $ids ) {
        echo ' · ' . esc_html( jj_manage_money_line( jj_manage_money( $ids ), 'received' ) ) . ' ' . esc_html__( 'received', 'jaiye-journeys' );
    }
    ?>
  </p>
  <?php foreach ( $ids as $id ) : ?>
    <?php jj_manage_booking_row( jj_manage_booking( $id ) ); ?>
  <?php endforeach; ?>
</section>
