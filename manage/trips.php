<?php
/**
 * Every trip with its status and places sold.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="m-title"><?php esc_html_e( 'Trips', 'jaiye-journeys' ); ?></h1>
<?php jj_manage_notice(); ?>

<?php foreach ( jj_manage_trips() as $trip ) : ?>
  <?php
  $ids   = jj_manage_bookings( [ 'trip' => $trip->ID ] );
  $money = jj_manage_money( $ids );
  ?>
  <a class="m-card m-card--link" href="<?php echo esc_url( jj_manage_url( 'trip/' . $trip->ID ) ); ?>">
    <div class="m-card__head">
      <h2 class="m-card__title"><?php echo esc_html( jj_trip_volume_title( $trip->ID ) ); ?></h2>
      <?php echo jj_manage_trip_pill( $trip->ID ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped when built. ?>
    </div>
    <?php if ( 'publish' !== $trip->post_status ) : ?>
      <p class="m-muted"><?php esc_html_e( 'Not published yet', 'jaiye-journeys' ); ?></p>
    <?php endif; ?>
    <?php if ( $ids ) : ?>
      <p class="m-muted">
        <?php
        /* translators: %d: number of bookings */
        echo esc_html( sprintf( _n( '%d booking', '%d bookings', count( $ids ), 'jaiye-journeys' ), count( $ids ) ) . ' · ' . jj_manage_money_line( $money, 'received' ) . ' ' . __( 'received', 'jaiye-journeys' ) );
        ?>
      </p>
    <?php endif; ?>
    <?php jj_manage_places_html( $trip->ID, true ); ?>
  </a>
<?php endforeach; ?>
