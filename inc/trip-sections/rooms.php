<?php
/**
 * Rooms: each room type from the trip's Rooms repeater (the same rooms the
 * booking page sells), with photo, description and price per person.
 *
 * Shows what's available; booking itself stays with the page's Book
 * buttons. With several chapters, each room lists its availability per
 * chapter. Waitlist shows the rooms with no prices or availability.
 *
 * Expects $trip_id, $rooms, $waitlist, $sold_out, $is_btl.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
  <?php if ( $rooms ) : ?>
    <?php
    $room_deps = jj_trip_rows( 'departures', $trip_id );
    $room_keys = $room_deps ? array_keys( $room_deps ) : [ 0 ];
    // $ prices only when every room has one, as on the booking page.
    $room_usd  = ! $waitlist && in_array( 'usd', jj_trip_booking_currencies( $trip_id ), true );
    $room_curs = $room_usd ? [ 'gbp', 'usd' ] : [ 'gbp' ];
    ?>
    <section class="trip-rooms" id="rooms">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Rooms', 'jaiye-journeys' ); ?></h2>

        <div class="trip-rooms__grid">
          <?php foreach ( $rooms as $i => $room ) : ?>
            <?php
            // Availability per chapter: sold out, a few places left, or
            // available. Same rule as the booking page: numbers only at 3 or
            // fewer. A sold-out trip shows every room sold out.
            $avail = [];
            foreach ( $room_keys as $d ) {
                $left    = jj_trip_places_left( $trip_id, $d, $i );
                $dep_off = $room_deps && isset( $room_deps[ $d ]['status'] ) && 'sold-out' === $room_deps[ $d ]['status'];
                if ( $sold_out || $room['sold_out'] || $dep_off || 0 === $left ) {
                    $avail[ $d ] = [ 'sold', __( 'Sold out', 'jaiye-journeys' ) ];
                } elseif ( null !== $left && $left <= 3 ) {
                    /* translators: %d: places left */
                    $avail[ $d ] = [ 'few', sprintf( _n( 'Only %d place left', 'Only %d places left', $left, 'jaiye-journeys' ), $left ) ];
                } else {
                    $avail[ $d ] = [ 'open', __( 'Available', 'jaiye-journeys' ) ];
                }
            }
            $room_sold = ! $waitlist && ! in_array( 'open', array_column( $avail, 0 ), true ) && ! in_array( 'few', array_column( $avail, 0 ), true );
            ?>
            <article class="trip-room js-reveal<?php echo $room_sold ? ' is-sold-out' : ''; ?>">
              <?php if ( $room['image'] ) : ?>
                <div class="trip-room__media">
                  <?php echo wp_get_attachment_image( $room['image'], 'medium_large', false, [ 'class' => 'trip-room__img', 'loading' => 'lazy' ] ); ?>
                </div>
              <?php endif; ?>
              <div class="trip-room__body">
                <h3 class="trip-room__name"><?php echo esc_html( $room['name'] ); ?></h3>
                <?php if ( $room['description'] ) : ?>
                  <p class="trip-room__desc"><?php echo esc_html( $room['description'] ); ?></p>
                <?php endif; ?>

                <?php if ( ! $waitlist ) : ?>
                  <?php if ( $room_sold ) : ?>
                    <p class="trip-room__status"><?php esc_html_e( 'Sold out', 'jaiye-journeys' ); ?></p>
                  <?php elseif ( count( $avail ) > 1 ) : ?>
                    <ul class="trip-room__avail" role="list">
                      <?php foreach ( $avail as $d => $a ) : ?>
                        <li class="trip-room__chapter is-<?php echo esc_attr( $a[0] ); ?>">
                          <span><?php echo esc_html( $is_btl ? sprintf( /* translators: %d: chapter number */ __( 'Chapter %d', 'jaiye-journeys' ), $d + 1 ) : trim( ( isset( $room_deps[ $d ]['dates'] ) ? $room_deps[ $d ]['dates'] : '' ) . ' ' . ( isset( $room_deps[ $d ]['year'] ) ? $room_deps[ $d ]['year'] : '' ) ) ); ?></span>
                          <span><?php echo esc_html( $a[1] ); ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php elseif ( 'few' === $avail[ $room_keys[0] ][0] ) : ?>
                    <p class="trip-room__status"><?php echo esc_html( $avail[ $room_keys[0] ][1] ); ?></p>
                  <?php endif; ?>
                <?php endif; ?>

                <?php if ( ! $waitlist && ! $room_sold ) : ?>
                  <?php foreach ( $room_curs as $cur ) : ?>
                    <?php $p = $room['prices'][ $cur ]; ?>
                    <p class="trip-room__price<?php echo $room_usd ? ' js-cur js-cur--' . esc_attr( $cur ) : ''; ?>"<?php echo 'usd' === $cur ? ' hidden' : ''; ?>>
                      <span class="trip-room__amount"><?php echo esc_html( jj_booking_money( $p['plan'], $cur ) ); ?></span>
                      <?php esc_html_e( 'per person', 'jaiye-journeys' ); ?>
                      <?php if ( $p['full'] < $p['plan'] ) : ?>
                        <span class="trip-room__full">
                          <?php
                          /* translators: %s: pay-in-full price */
                          echo esc_html( sprintf( __( 'or %s if you pay in full', 'jaiye-journeys' ), jj_booking_money( $p['full'], $cur ) ) );
                          ?>
                        </span>
                      <?php endif; ?>
                    </p>
                  <?php endforeach; ?>

                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
