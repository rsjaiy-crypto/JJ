<?php
/**
 * Trip style meters.
 *
 * Shared by the full trip page (single-trip.php) and the waitlist page
 * (inc/trip-coming-soon.php), so both always render the section the same way.
 * Must stay free of prices and booking links: the waitlist page shows it.
 *
 * Expects $trip_id.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
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
