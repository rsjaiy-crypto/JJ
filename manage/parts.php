<?php
/**
 * Small pieces the dashboard screens share.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tab bar icons (fixed markup).
 *
 * @param string $name overview|bookings|trips.
 * @return string
 */
function jj_manage_icon( $name ) {
    $paths = [
        'overview' => '<path d="M4 13h6V4H4z"/><path d="M14 20h6v-9h-6z"/><path d="M4 20h6v-3H4z"/><path d="M14 7h6V4h-6z"/>',
        'bookings' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/>',
        'trips'    => '<path d="M3 20l6-14 4 8 3-5 5 11z"/>',
    ];
    return '<svg class="m-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $name ] . '</svg>';
}

/**
 * Places per chapter and room as bars: "5 of 7 sold · 2 left".
 *
 * @param int  $trip_id Trip ID.
 * @param bool $compact Chapter names smaller, for list cards.
 */
function jj_manage_places_html( $trip_id, $compact = false ) {
    $chapters = jj_manage_places( $trip_id );
    if ( ! $chapters ) {
        return;
    }
    $tracked = false;
    foreach ( $chapters as $chapter ) {
        foreach ( $chapter['rooms'] as $room ) {
            $tracked = $tracked || null !== $room['places'];
        }
    }
    if ( ! $tracked ) {
        echo '<p class="m-muted">' . esc_html__( 'Add "Places per chapter" to the rooms to track places sold.', 'jaiye-journeys' ) . '</p>';
        return;
    }
    ?>
    <div class="m-places<?php echo $compact ? ' m-places--compact' : ''; ?>">
      <?php foreach ( $chapters as $chapter ) : ?>
        <div class="m-places__chapter">
          <p class="m-places__label"><?php echo esc_html( $chapter['label'] ); ?></p>
          <?php foreach ( $chapter['rooms'] as $room ) : ?>
            <?php
            if ( null === $room['places'] ) {
                continue;
            }
            $full = $room['sold_out'] || 0 === $room['left'];
            $pct  = $room['places'] ? min( 100, round( 100 * ( $room['sold'] + $room['held'] ) / $room['places'] ) ) : 100;
            ?>
            <div class="m-bar<?php echo $full ? ' is-full' : ''; ?>">
              <div class="m-bar__text">
                <span><?php echo esc_html( $room['name'] ); ?></span>
                <span>
                  <?php
                  if ( $full ) {
                      esc_html_e( 'Sold out', 'jaiye-journeys' );
                  } else {
                      /* translators: 1: sold, 2: total, 3: left */
                      echo esc_html( sprintf( __( '%1$d of %2$d sold · %3$d left', 'jaiye-journeys' ), $room['sold'], $room['places'], $room['left'] ) );
                  }
                  if ( $room['held'] ) {
                      /* translators: %d: places being paid for now */
                      echo ' <em>' . esc_html( sprintf( __( '(%d paying now)', 'jaiye-journeys' ), $room['held'] ) ) . '</em>';
                  }
                  ?>
                </span>
              </div>
              <div class="m-bar__track" role="img" aria-label="<?php echo esc_attr( $pct . '%' ); ?>"><span style="width:<?php echo (int) $pct; ?>%"></span></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php
}

/**
 * One booking as a tappable row.
 *
 * @param array $b From jj_manage_booking().
 */
function jj_manage_booking_row( $b ) {
    ?>
    <a class="m-row" href="<?php echo esc_url( jj_manage_url( 'booking/' . $b['id'] ) ); ?>">
      <span class="m-row__main">
        <strong><?php echo esc_html( $b['name'] ? $b['name'] : $b['email'] ); ?></strong>
        <span class="m-row__sub"><?php echo esc_html( implode( ' · ', array_filter( [ $b['trip'], $b['departure'] ] ) ) ); ?></span>
        <span class="m-row__sub">
          <?php
          /* translators: 1: room, 2: travellers */
          echo esc_html( sprintf( _n( '%1$s · %2$d traveller', '%1$s · %2$d travellers', $b['travellers'], 'jaiye-journeys' ), $b['room'], $b['travellers'] ) );
          ?>
        </span>
      </span>
      <span class="m-row__side">
        <span class="m-pill m-pill--<?php echo esc_attr( $b['tone'] ); ?>"><?php echo esc_html( $b['label'] ); ?></span>
        <?php if ( $b['test'] ) : ?><span class="m-pill m-pill--test"><?php esc_html_e( 'Test', 'jaiye-journeys' ); ?></span><?php endif; ?>
        <?php if ( $b['currency'] ) : ?>
          <span class="m-row__money"><?php echo esc_html( jj_booking_money( $b['paid'], $b['currency'] ) . ' / ' . jj_booking_money( $b['total'], $b['currency'] ) ); ?></span>
        <?php endif; ?>
      </span>
    </a>
    <?php
}

/**
 * A one-line confirmation after saving.
 */
function jj_manage_notice() {
    $messages = [
        'saved'     => __( 'Saved. The trip page is updated.', 'jaiye-journeys' ),
        'note'      => __( 'Note saved.', 'jaiye-journeys' ),
        'cancelled' => __( 'Booking cancelled and moved to the Bin. Its places are free again.', 'jaiye-journeys' ),
        'retry'     => __( 'Tried setting up the payment plan again. Check the status below.', 'jaiye-journeys' ),
    ];
    $done = isset( $_GET['done'] ) ? sanitize_key( wp_unslash( $_GET['done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( isset( $messages[ $done ] ) ) {
        echo '<p class="m-notice" role="status">' . esc_html( $messages[ $done ] ) . '</p>';
    }
}
