<?php
/**
 * Trip booking page, Stripe Checkout and automatic payment plans.
 *
 * Each on-sale trip with Rooms gets /trips/<slug>/book/. Guests pick a
 * departure, room, travellers, currency (£ or $) and either:
 *
 * - Payment plan (default): the deposit now, with the card saved, then the
 *   balance in equal payments on the trip's plan dates still ahead of them,
 *   collected by a Stripe subscription schedule (one phase per date).
 * - Pay in full: the discounted pay-in-full price now.
 *
 * Amounts are always worked out here from the trip's fields, never taken
 * from the form. The schedule is created when Stripe confirms the deposit
 * (checkout.session.completed webhook), and each booking is recorded under
 * Trips → Bookings, where failed payments are tracked. After 14 days unpaid
 * the owner is emailed; nothing is cancelled automatically.
 *
 * Keys live in the database (Trips → Booking Settings), never in this public
 * repository. JJ_STRIPE_SECRET_KEY / JJ_STRIPE_WEBHOOK_SECRET constants in
 * wp-config.php override them.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

define( 'JJ_STRIPE_KEY_OPTION', 'jj_stripe_secret_key' );
define( 'JJ_STRIPE_WEBHOOK_OPTION', 'jj_stripe_webhook_secret' );
define( 'JJ_BOOKING_NOTIFY_OPTION', 'jj_booking_notify_email' );
define( 'JJ_BOOKING_FAST_TEST_OPTION', 'jj_booking_fast_test_plan' );
define( 'JJ_BOOKING_FULL_CODES_OPTION', 'jj_booking_full_codes' );
define( 'JJ_BOOKING_OVERDUE_DAYS', 14 );


/* ── Money ─────────────────────────────────────────────────────────── */

/**
 * Currencies a trip can be sold in.
 *
 * @return array code => symbol
 */
function jj_booking_currencies() {
    return [
        'gbp' => '£',
        'usd' => '$',
    ];
}

/**
 * Format an amount in minor units (pence/cents): £2,950 or £816.67.
 *
 * @param int    $minor    Amount in pence/cents.
 * @param string $currency gbp|usd.
 * @return string
 */
function jj_booking_money( $minor, $currency ) {
    $symbols = jj_booking_currencies();
    $minor   = (int) $minor;
    return $symbols[ $currency ] . number_format( $minor / 100, ( $minor % 100 ) ? 2 : 0 );
}

/**
 * Split a balance into equal payments; the last absorbs any odd pence.
 *
 * @param int $balance Minor units.
 * @param int $count   Number of payments.
 * @return int[]
 */
function jj_booking_split( $balance, $count ) {
    if ( $count < 1 || $balance <= 0 ) {
        return [];
    }
    $each    = intdiv( $balance, $count );
    $amounts = array_fill( 0, $count, $each );
    $amounts[ $count - 1 ] += $balance - $each * $count;
    return $amounts;
}


/* ── Trip data ─────────────────────────────────────────────────────── */

/**
 * A trip's rooms, keyed by row index so links stay stable when a room is
 * marked sold out. Prices are minor units (pence or cents) per person, per
 * currency; a missing pay-in-full price means no discount. Rooms without a name or a
 * £ plan price are skipped.
 *
 * @param int $post_id Trip ID.
 * @return array[]
 */
function jj_trip_rooms( $post_id ) {
    $rooms = [];
    foreach ( jj_trip_rows( 'rooms', $post_id ) as $i => $room ) {
        $gbp = isset( $room['price'] ) ? jj_money_minor( $room['price'] ) : 0;
        if ( empty( $room['name'] ) || $gbp <= 0 ) {
            continue;
        }
        $usd = isset( $room['price_usd'] ) ? jj_money_minor( $room['price_usd'] ) : 0;

        $full_gbp = isset( $room['full_price'] ) ? jj_money_minor( $room['full_price'] ) : 0;
        $full_usd = isset( $room['full_price_usd'] ) ? jj_money_minor( $room['full_price_usd'] ) : 0;

        $rooms[ $i ] = [
            'name'        => $room['name'],
            'description' => isset( $room['description'] ) ? $room['description'] : '',
            'image'       => isset( $room['image'] ) ? absint( $room['image'] ) : 0,
            'prices'      => [
                'gbp' => [ 'plan' => $gbp, 'full' => $full_gbp > 0 ? $full_gbp : $gbp ],
                'usd' => [ 'plan' => $usd, 'full' => $usd ? ( $full_usd > 0 ? $full_usd : $usd ) : 0 ],
            ],
            'max_guests'  => max( 1, min( 4, isset( $room['max_guests'] ) ? (int) $room['max_guests'] : 2 ) ),
            // Guests per chapter; null means no limit.
            'places'      => isset( $room['places'] ) && '' !== trim( (string) $room['places'] ) ? max( 0, (int) $room['places'] ) : null,
            'sold_out'    => ! empty( $room['sold_out'] ),
        ];
    }
    return $rooms;
}

/**
 * Deposit per person, minor units (pence or cents).
 *
 * @param int    $post_id  Trip ID.
 * @param string $currency gbp|usd.
 * @return int
 */
function jj_trip_deposit( $post_id, $currency ) {
    return jj_money_minor( jj_trip_field( 'usd' === $currency ? 'deposit_usd' : 'deposit_gbp', $post_id ) );
}

/**
 * Currencies this trip can be booked in: £ always, $ when every room has a
 * $ price and there's a $ deposit.
 *
 * @param int $post_id Trip ID.
 * @return string[]
 */
function jj_trip_booking_currencies( $post_id ) {
    $out   = [ 'gbp' ];
    $rooms = jj_trip_rooms( $post_id );
    $usd   = $rooms && jj_trip_deposit( $post_id, 'usd' ) > 0;
    foreach ( $rooms as $room ) {
        if ( $room['prices']['usd']['plan'] <= 0 ) {
            $usd = false;
        }
    }
    if ( $usd ) {
        $out[] = 'usd';
    }
    return $out;
}

/**
 * The trip's plan dates still ahead, as timestamps at 9am site time,
 * soonest first. A date is ahead if it's after today.
 *
 * In Stripe test mode, "Quick test schedule" in Booking Settings swaps the
 * real dates for ones five minutes apart, so a whole plan can be watched
 * running in an afternoon.
 *
 * @param int $post_id Trip ID.
 * @return int[]
 */
function jj_trip_plan_dates( $post_id ) {
    $today = current_datetime()->setTime( 0, 0 );
    $dates = [];

    foreach ( jj_trip_rows( 'instalment_dates', $post_id ) as $row ) {
        $date = DateTimeImmutable::createFromFormat( '!Y-m-d', isset( $row['date'] ) ? $row['date'] : '', wp_timezone() );
        if ( $date && $date > $today ) {
            $dates[] = $date->setTime( 9, 0 )->getTimestamp();
        }
    }
    $dates = array_values( array_unique( $dates ) );
    sort( $dates );
    $dates = array_slice( $dates, 0, 24 );

    if ( $dates && 'test' === jj_stripe_mode() && get_option( JJ_BOOKING_FAST_TEST_OPTION ) ) {
        foreach ( $dates as $i => $ts ) {
            $dates[ $i ] = time() + 300 * ( $i + 1 );
        }
    }

    return $dates;
}

/**
 * Departures guests can book, keyed by row index (index + 1 is the chapter).
 *
 * @param int $post_id Trip ID.
 * @return array[]
 */
function jj_trip_bookable_departures( $post_id ) {
    $out = [];
    foreach ( jj_trip_rows( 'departures', $post_id ) as $i => $departure ) {
        if ( ( isset( $departure['status'] ) && 'sold-out' === $departure['status'] ) || jj_trip_departure_full( $post_id, $i ) ) {
            continue;
        }
        $out[ $i ] = $departure;
    }
    return $out;
}

/**
 * How a departure reads on the booking page and in Stripe.
 *
 * @param int   $post_id   Trip ID.
 * @param int   $index     Departure row index.
 * @param array $departure Departure row.
 * @return string e.g. "Chapter 1 · 12 – 20 May 2027"
 */
function jj_trip_departure_label( $post_id, $index, $departure ) {
    $dates = trim( ( isset( $departure['dates'] ) ? $departure['dates'] : '' ) . ' ' . ( isset( $departure['year'] ) ? $departure['year'] : '' ) );

    if ( 'btl' === jj_trip_field( 'brand', $post_id, 'jj-edit' ) ) {
        /* translators: %d: chapter number */
        $chapter = sprintf( __( 'Chapter %d', 'jaiye-journeys' ), $index + 1 );
        return $dates ? $chapter . ' · ' . $dates : $chapter;
    }

    return $dates;
}

/**
 * What a booking costs and when it's paid.
 *
 * @param int    $post_id  Trip ID.
 * @param array  $room     Room from jj_trip_rooms().
 * @param int    $guests   Travellers.
 * @param string $currency gbp|usd.
 * @param string $payment  plan|full.
 * @param array  $discount Optional, from jj_booking_discount(). On the payment
 *                         plan it comes off the balance, so the deposit stays
 *                         the same and every later payment is smaller. Paying
 *                         in full, only codes listed in Booking Settings apply,
 *                         on top of the pay-in-full price.
 * @return array|null today, total, discount (minor units), plan [[ts, minor], …]; null if unavailable.
 */
function jj_booking_quote( $post_id, $room, $guests, $currency, $payment, $discount = null ) {
    $prices = $room['prices'][ $currency ];
    if ( $prices['plan'] <= 0 ) {
        return null;
    }

    if ( 'plan' === $payment ) {
        $dates   = jj_trip_plan_dates( $post_id );
        $deposit = jj_trip_deposit( $post_id, $currency ) * $guests;
        $total   = $prices['plan'] * $guests;
        $off     = jj_booking_discount_amount( $discount, $total, $total - $deposit - count( $dates ) );
        $split   = jj_booking_split( $total - $deposit - $off, count( $dates ) );
        if ( ! $split || $deposit <= 0 ) {
            return null; // No dates ahead, or no deposit: pay in full only.
        }
        return [
            'today'    => $deposit,
            'total'    => $total - $off,
            'discount' => $off,
            'plan'     => array_map( null, $dates, $split ),
        ];
    }

    $total = $prices['full'] * $guests;
    $off   = ! empty( $discount['full'] ) ? jj_booking_discount_amount( $discount, $total, $total - 100 ) : 0;
    return [
        'today'    => $total - $off,
        'total'    => $total - $off,
        'discount' => $off,
        'plan'     => [],
    ];
}


/* ── Discount codes ────────────────────────────────────────────────── */

/**
 * How much a discount takes off a total.
 *
 * @param array|null $discount From jj_booking_discount().
 * @param int        $total    Total, minor units.
 * @param int        $most     The most it may take off: on a plan the balance
 *                             less a penny per payment, so the plan still
 *                             runs; paying in full, all but £1 / $1.
 * @return int Minor units.
 */
function jj_booking_discount_amount( $discount, $total, $most ) {
    if ( ! $discount ) {
        return 0;
    }
    $off = $discount['percent'] ? (int) round( $total * $discount['percent'] / 100 ) : (int) $discount['amount'];
    return max( 0, min( $off, $most ) );
}

/**
 * Check a discount code made in Stripe (Product catalogue → Coupons): either
 * a promotion code guests type, or a coupon's own ID. Codes are for payment
 * plans, since the pay-in-full price is already lower, except those listed
 * in Booking Settings ("Codes that also work paying in full"), which stack
 * on the pay-in-full price.
 *
 * The discount is built into the amounts the site charges rather than
 * attached in Stripe, so Stripe doesn't count redemptions. The site counts
 * its own bookings with the code against any redemption limit instead.
 *
 * @param string $code     As typed.
 * @param string $currency gbp|usd.
 * @return array|WP_Error code, percent, amount (minor units), label, full.
 */
function jj_booking_discount( $code, $currency ) {
    $code    = strtoupper( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $code ) );
    $invalid = new WP_Error( 'jj_code', __( 'Sorry, that code isn’t recognised.', 'jaiye-journeys' ) );
    if ( '' === $code || strlen( $code ) > 64 ) {
        return $invalid;
    }

    // A minute's cache keeps repeat checks of the same code off Stripe.
    $cache = 'jj_code_' . jj_stripe_mode() . '_' . md5( $code );
    $found = get_transient( $cache );
    if ( false === $found ) {
        $found = jj_booking_discount_lookup( $code );
        if ( is_wp_error( $found ) ) {
            error_log( 'Jaiye booking: discount code lookup failed: ' . $found->get_error_message() ); // phpcs:ignore
            return new WP_Error( 'jj_code', __( 'We couldn’t check that code just now. Please try again.', 'jaiye-journeys' ) );
        }
        set_transient( $cache, $found, MINUTE_IN_SECONDS );
    }
    if ( ! $found ) {
        return $invalid;
    }

    $coupon = $found['coupon'];
    $promo  = $found['promo'];
    if ( empty( $coupon['valid'] ) || ( ! empty( $promo['expires_at'] ) && $promo['expires_at'] < time() ) ) {
        return new WP_Error( 'jj_code', __( 'Sorry, that code has expired.', 'jaiye-journeys' ) );
    }

    $limits = array_filter( [
        isset( $promo['max_redemptions'] ) ? (int) $promo['max_redemptions'] : 0,
        isset( $coupon['max_redemptions'] ) ? (int) $coupon['max_redemptions'] : 0,
    ] );
    if ( $limits && jj_booking_discount_uses( $code ) >= min( $limits ) ) {
        return new WP_Error( 'jj_code', __( 'Sorry, that code has been fully used.', 'jaiye-journeys' ) );
    }

    $symbols = jj_booking_currencies();
    if ( ! empty( $coupon['percent_off'] ) ) {
        $percent = (float) $coupon['percent_off'];
        return [
            'code'    => $code,
            'percent' => $percent,
            'amount'  => 0,
            /* translators: %s: percentage */
            'label'   => sprintf( __( '%s%% off', 'jaiye-journeys' ), rtrim( rtrim( number_format( $percent, 2 ), '0' ), '.' ) ),
            'full'    => in_array( $code, jj_booking_full_codes(), true ),
        ];
    }

    $amount = 0;
    if ( ! empty( $coupon['currency_options'][ $currency ]['amount_off'] ) ) {
        $amount = (int) $coupon['currency_options'][ $currency ]['amount_off'];
    } elseif ( ! empty( $coupon['amount_off'] ) && isset( $coupon['currency'] ) && $coupon['currency'] === $currency ) {
        $amount = (int) $coupon['amount_off'];
    }
    if ( $amount <= 0 ) {
        return new WP_Error( 'jj_code', sprintf(
            /* translators: %s: currency symbol */
            __( 'Sorry, that code can’t be used when paying in %s.', 'jaiye-journeys' ),
            $symbols[ $currency ]
        ) );
    }
    return [
        'code'    => $code,
        'percent' => 0,
        'amount'  => $amount,
        /* translators: %s: amount */
        'label'   => sprintf( __( '%s off', 'jaiye-journeys' ), jj_booking_money( $amount, $currency ) ),
        'full'    => in_array( $code, jj_booking_full_codes(), true ),
    ];
}

/**
 * Codes that also work when paying in full (Booking Settings), upper case.
 *
 * @return string[]
 */
function jj_booking_full_codes() {
    return array_values( array_filter( preg_split( '/[\s,]+/', strtoupper( (string) get_option( JJ_BOOKING_FULL_CODES_OPTION, '' ) ) ) ) );
}

/**
 * Find a code in Stripe: an active promotion code first, then a coupon ID.
 *
 * @param string $code Upper case.
 * @return array|WP_Error coupon and promo fields, [] if there's no such code.
 */
function jj_booking_discount_lookup( $code ) {
    $list = jj_stripe_request( 'GET', 'promotion_codes', [ 'code' => $code, 'active' => 'true', 'limit' => 1 ] );
    if ( is_wp_error( $list ) ) {
        return $list;
    }

    $promo = ! empty( $list['data'][0] ) ? $list['data'][0] : null;
    if ( $promo ) {
        // Newer API versions put the coupon under "promotion".
        $coupon = isset( $promo['coupon'] ) ? $promo['coupon'] : ( isset( $promo['promotion']['coupon'] ) ? $promo['promotion']['coupon'] : null );
        if ( is_string( $coupon ) ) {
            $coupon = jj_stripe_request( 'GET', 'coupons/' . rawurlencode( $coupon ) );
            if ( is_wp_error( $coupon ) ) {
                return $coupon;
            }
        }
        if ( ! is_array( $coupon ) ) {
            return [];
        }
    } else {
        // Not a promotion code: try it as a coupon ID. Stripe answers an
        // unknown ID with an error, which here just means "no such code".
        $coupon = jj_stripe_request( 'GET', 'coupons/' . rawurlencode( $code ) );
        if ( is_wp_error( $coupon ) ) {
            return [];
        }
        $promo = [];
    }

    return [
        'coupon' => array_intersect_key( $coupon, array_flip( [ 'valid', 'percent_off', 'amount_off', 'currency', 'currency_options', 'max_redemptions' ] ) ),
        'promo'  => array_intersect_key( $promo, array_flip( [ 'expires_at', 'max_redemptions' ] ) ),
    ];
}

/**
 * Bookings that used a code, in the current Stripe mode.
 *
 * @param string $code Upper case.
 * @return int
 */
function jj_booking_discount_uses( $code ) {
    return count( get_posts( [
        'post_type'      => 'jj_booking',
        'post_status'    => [ 'publish', 'private', 'draft', 'pending' ],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery
            [ 'key' => '_jj_bk_code', 'value' => $code ],
            [ 'key' => '_jj_bk_mode', 'value' => jj_stripe_mode() ],
        ],
    ] ) );
}


/* ── Places (room inventory) ───────────────────────────────────────── */

/*
 * Each room type can have a number of places per chapter (Rooms → "Places
 * per chapter"; blank is no limit). Paid bookings count against it, plus
 * places held while a guest is on Stripe's payment page, so the last place
 * can't be sold twice. A cancelled booking frees its places once it's moved
 * to the Bin. Only bookings in the current Stripe mode count, so test
 * bookings never use up live places.
 */

define( 'JJ_TRIP_HOLDS_META', '_jj_trip_holds' );

/**
 * Places taken per departure and room: paid bookings plus live holds.
 *
 * @param int  $trip_id Trip ID.
 * @param bool $fresh   Count again rather than reuse this request's count
 *                      (checkout does, once it holds the lock).
 * @return array [ departure index => [ room index => travellers ] ]
 */
function jj_trip_places_taken( $trip_id, $fresh = false ) {
    static $cache = [];
    if ( ! $fresh && isset( $cache[ $trip_id ] ) ) {
        return $cache[ $trip_id ];
    }

    $taken = [];
    $add   = function ( $dep, $room, $guests ) use ( &$taken ) {
        if ( $dep >= 0 && $room >= 0 && $guests > 0 ) {
            $taken[ $dep ][ $room ] = ( isset( $taken[ $dep ][ $room ] ) ? $taken[ $dep ][ $room ] : 0 ) + $guests;
        }
    };

    // Older bookings stored only the room name and dates label: match those.
    $room_by_name = [];
    foreach ( jj_trip_rows( 'rooms', $trip_id ) as $i => $room ) {
        if ( ! empty( $room['name'] ) ) {
            $room_by_name[ $room['name'] ] = $i;
        }
    }
    $dep_by_label = [];
    foreach ( jj_trip_rows( 'departures', $trip_id ) as $i => $departure ) {
        $dep_by_label[ jj_trip_departure_label( $trip_id, $i, $departure ) ] = $i;
    }

    $bookings = get_posts( [
        'post_type'      => 'jj_booking',
        'post_status'    => [ 'publish', 'private', 'draft', 'pending' ],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery
            [ 'key' => '_jj_bk_trip_id', 'value' => (int) $trip_id ],
            [ 'key' => '_jj_bk_mode', 'value' => jj_stripe_mode() ],
        ],
    ] );
    foreach ( $bookings as $booking_id ) {
        $dep  = jj_booking_get( $booking_id, 'dep_i' );
        $room = jj_booking_get( $booking_id, 'room_i' );
        if ( '' === $dep ) {
            $label = jj_booking_get( $booking_id, 'departure' );
            $dep   = isset( $dep_by_label[ $label ] ) ? $dep_by_label[ $label ] : ( count( $dep_by_label ) <= 1 ? 0 : -1 );
        }
        if ( '' === $room ) {
            $name = jj_booking_get( $booking_id, 'room' );
            $room = isset( $room_by_name[ $name ] ) ? $room_by_name[ $name ] : -1;
        }
        $add( (int) $dep, (int) $room, (int) jj_booking_get( $booking_id, 'travellers' ) );
    }

    foreach ( jj_trip_holds( $trip_id ) as $hold ) {
        $add( (int) $hold['dep'], (int) $hold['room'], (int) $hold['guests'] );
    }

    $cache[ $trip_id ] = $taken;
    return $taken;
}

/**
 * Places left for one room in one departure, or null for no limit.
 *
 * @return int|null
 */
function jj_trip_places_left( $trip_id, $dep_i, $room_i, $fresh = false ) {
    $rooms = jj_trip_rooms( $trip_id );
    if ( ! isset( $rooms[ $room_i ] ) || null === $rooms[ $room_i ]['places'] ) {
        return null;
    }
    $taken = jj_trip_places_taken( $trip_id, $fresh );
    $used  = isset( $taken[ $dep_i ][ $room_i ] ) ? $taken[ $dep_i ][ $room_i ] : 0;
    return max( 0, $rooms[ $room_i ]['places'] - $used );
}

/**
 * Is every room in a departure full or marked sold out? A room with no
 * place limit never fills, so its departure stays open.
 *
 * @return bool
 */
function jj_trip_departure_full( $trip_id, $dep_i ) {
    $rooms = jj_trip_rooms( $trip_id );
    if ( ! $rooms ) {
        return false;
    }
    foreach ( $rooms as $room_i => $room ) {
        if ( ! $room['sold_out'] && 0 !== jj_trip_places_left( $trip_id, $dep_i, $room_i ) ) {
            return false;
        }
    }
    return true;
}

/**
 * Unexpired holds: places kept for guests on Stripe's payment page.
 *
 * @return array[] [ session id => [ dep, room, guests, until ] ]
 */
function jj_trip_holds( $trip_id ) {
    $holds = get_post_meta( $trip_id, JJ_TRIP_HOLDS_META, true );
    if ( ! is_array( $holds ) ) {
        return [];
    }
    return array_filter( $holds, function ( $hold ) {
        return isset( $hold['until'] ) && $hold['until'] > time();
    } );
}

function jj_trip_hold_places( $trip_id, $session_id, $dep_i, $room_i, $guests, $until ) {
    $holds                = jj_trip_holds( $trip_id );
    $holds[ $session_id ] = [ 'dep' => (int) $dep_i, 'room' => (int) $room_i, 'guests' => (int) $guests, 'until' => (int) $until ];
    update_post_meta( $trip_id, JJ_TRIP_HOLDS_META, $holds );
}

function jj_trip_release_hold( $trip_id, $session_id ) {
    $holds = jj_trip_holds( $trip_id );
    if ( isset( $holds[ $session_id ] ) ) {
        unset( $holds[ $session_id ] );
        update_post_meta( $trip_id, JJ_TRIP_HOLDS_META, $holds );
    }
}

/**
 * One checkout at a time per trip, so two guests can't both take the last
 * place. add_option() only succeeds for the first caller; a lock left by a
 * request that died is ignored after a minute.
 *
 * @return bool Whether the lock was taken.
 */
function jj_trip_lock( $trip_id ) {
    $name = 'jj_booking_lock_' . (int) $trip_id;
    for ( $try = 0; $try < 20; $try++ ) {
        if ( add_option( $name, time(), '', 'no' ) ) {
            return true;
        }
        if ( (int) get_option( $name ) < time() - MINUTE_IN_SECONDS ) {
            delete_option( $name );
            continue;
        }
        usleep( 250000 );
    }
    return false;
}

function jj_trip_unlock( $trip_id ) {
    delete_option( 'jj_booking_lock_' . (int) $trip_id );
}


/**
 * "Places sold" box beside each trip in wp-admin: per chapter and room,
 * places booked, held on the payment page right now, and left.
 */
function jj_trip_places_meta_box() {
    add_meta_box( 'jj-trip-places', __( 'Places sold', 'jaiye-journeys' ), 'jj_trip_places_box', 'trip', 'side', 'high' );
}
add_action( 'add_meta_boxes_trip', 'jj_trip_places_meta_box' );

function jj_trip_places_box( $post ) {
    $trip_id = $post->ID;
    $rooms   = array_filter( jj_trip_rooms( $trip_id ), function ( $room ) {
        return null !== $room['places'];
    } );
    if ( ! $rooms ) {
        echo '<p>' . esc_html__( 'Fill in "Places per chapter" on a room (in Rooms) and this box will count its bookings.', 'jaiye-journeys' ) . '</p>';
        return;
    }

    $held = [];
    foreach ( jj_trip_holds( $trip_id ) as $hold ) {
        $held[ $hold['dep'] ][ $hold['room'] ] = ( isset( $held[ $hold['dep'] ][ $hold['room'] ] ) ? $held[ $hold['dep'] ][ $hold['room'] ] : 0 ) + $hold['guests'];
    }
    $taken      = jj_trip_places_taken( $trip_id );
    $departures = jj_trip_rows( 'departures', $trip_id );
    $deps       = $departures ? $departures : [ [] ];

    foreach ( $deps as $d => $departure ) {
        if ( $departures ) {
            printf( '<p><strong>%s</strong></p>', esc_html( jj_trip_departure_label( $trip_id, $d, $departure ) ) );
        }
        echo '<table class="widefat striped" style="margin-bottom:12px"><tbody>';
        foreach ( $rooms as $r => $room ) {
            $hold = isset( $held[ $d ][ $r ] ) ? $held[ $d ][ $r ] : 0;
            $sold = ( isset( $taken[ $d ][ $r ] ) ? $taken[ $d ][ $r ] : 0 ) - $hold;
            $left = max( 0, $room['places'] - $sold - $hold );
            printf(
                '<tr><td>%s</td><td style="text-align:right;white-space:nowrap">%s</td></tr>',
                esc_html( $room['name'] ),
                esc_html(
                    sprintf(
                        /* translators: 1: places sold, 2: places in total, 3: places left */
                        __( '%1$d of %2$d sold · %3$d left', 'jaiye-journeys' ),
                        $sold,
                        $room['places'],
                        $left
                    ) . ( $hold ? ' ' . sprintf( /* translators: %d: places held */ __( '(%d paying now)', 'jaiye-journeys' ), $hold ) : '' )
                )
            );
        }
        echo '</tbody></table>';
    }
    echo '<p class="description">' . esc_html__( 'Counts paid bookings in Trips → Bookings. Move a cancelled booking to the Bin to free its places.', 'jaiye-journeys' ) . '</p>';
}


/* ── Stripe ────────────────────────────────────────────────────────── */

function jj_stripe_key() {
    if ( defined( 'JJ_STRIPE_SECRET_KEY' ) && JJ_STRIPE_SECRET_KEY ) {
        return JJ_STRIPE_SECRET_KEY;
    }
    return (string) get_option( JJ_STRIPE_KEY_OPTION, '' );
}

function jj_stripe_webhook_secret() {
    if ( defined( 'JJ_STRIPE_WEBHOOK_SECRET' ) && JJ_STRIPE_WEBHOOK_SECRET ) {
        return JJ_STRIPE_WEBHOOK_SECRET;
    }
    return (string) get_option( JJ_STRIPE_WEBHOOK_OPTION, '' );
}

/**
 * @return string test|live|'' (no key).
 */
function jj_stripe_mode() {
    $key = jj_stripe_key();
    if ( ! $key ) {
        return '';
    }
    return false !== strpos( $key, '_test_' ) ? 'test' : 'live';
}

/**
 * Call the Stripe API.
 *
 * @param string $method      GET or POST.
 * @param string $path        e.g. checkout/sessions.
 * @param array  $body        Parameters.
 * @param string $idempotency Optional Idempotency-Key, so a retried webhook
 *                            can't create a second schedule.
 * @return array|WP_Error Decoded response.
 */
function jj_stripe_request( $method, $path, $body = [], $idempotency = '' ) {
    $key = jj_stripe_key();
    if ( ! $key ) {
        return new WP_Error( 'jj_stripe_no_key', 'No Stripe key set.' );
    }

    $headers = [ 'Authorization' => 'Bearer ' . $key ];
    if ( $idempotency ) {
        $headers['Idempotency-Key'] = $idempotency;
    }

    $url = 'https://api.stripe.com/v1/' . $path;
    if ( 'GET' === $method && $body ) {
        $url .= '?' . http_build_query( $body, '', '&' );
        $body = [];
    }

    $response = wp_remote_request( $url, [
        'method'  => $method,
        'timeout' => 25,
        'headers' => $headers,
        'body'    => $body ? http_build_query( $body, '', '&' ) : null,
    ] );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    $code = wp_remote_retrieve_response_code( $response );

    if ( $code >= 300 || ! is_array( $data ) ) {
        $message = isset( $data['error']['message'] ) ? $data['error']['message'] : 'HTTP ' . $code;
        return new WP_Error( 'jj_stripe_error', $message );
    }

    return $data;
}

/**
 * Is the booking page switched on for this trip? It needs rooms, a Stripe
 * key and the webhook secret (without the webhook, payment plans would
 * never be set up); until then the trip keeps its old Book Now link.
 *
 * @param int $post_id Trip ID.
 * @return bool
 */
function jj_trip_booking_ready( $post_id ) {
    return jj_stripe_key() && jj_stripe_webhook_secret() && jj_trip_rooms( $post_id );
}

/**
 * URL of a trip's booking page.
 *
 * @param int      $post_id   Trip ID.
 * @param int|null $departure Departure row index to preselect.
 * @return string
 */
function jj_trip_booking_page_url( $post_id, $departure = null ) {
    $url = trailingslashit( get_permalink( $post_id ) ) . 'book/';
    if ( null !== $departure ) {
        $url = add_query_arg( 'chapter', (int) $departure + 1, $url );
    }
    return $url;
}


/* ── Visitor currency ──────────────────────────────────────────────── */

/**
 * $ for visitors in the US, £ for everyone else, unless they've picked one
 * with the £ / $ switch (?currency=).
 *
 * The country comes from the free country.is lookup of the visitor's IP,
 * cached for a day per IP (stored hashed). Any failure falls back to £.
 *
 * @param string[] $available Currencies this trip sells in.
 * @return string
 */
function jj_booking_visitor_currency( $available ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $asked = isset( $_GET['currency'] ) ? sanitize_key( wp_unslash( $_GET['currency'] ) ) : '';
    if ( in_array( $asked, $available, true ) ) {
        return $asked;
    }
    if ( ! in_array( 'usd', $available, true ) ) {
        return 'gbp';
    }

    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
        return 'gbp';
    }

    $cache   = 'jj_geo_' . md5( wp_salt() . $ip );
    $country = get_transient( $cache );
    if ( false === $country ) {
        $response = wp_remote_get( 'https://api.country.is/' . rawurlencode( $ip ), [ 'timeout' => 2 ] );
        $data     = is_wp_error( $response ) ? null : json_decode( wp_remote_retrieve_body( $response ), true );
        $country  = isset( $data['country'] ) ? strtoupper( substr( (string) $data['country'], 0, 2 ) ) : '';
        set_transient( $cache, $country, DAY_IN_SECONDS );
    }

    return 'US' === $country ? 'usd' : 'gbp';
}


/* ── Routing ───────────────────────────────────────────────────────── */

/**
 * /trips/<slug>/book/ → the trip, with jj_book set.
 * Changing this needs a JJ_TRIP_REWRITE_VERSION bump to take effect.
 */
function jj_trip_booking_rewrite() {
    add_rewrite_rule( '^trips/([^/]+)/book/?$', 'index.php?trip=$matches[1]&jj_book=1', 'top' );
}
add_action( 'init', 'jj_trip_booking_rewrite' );

function jj_trip_booking_query_vars( $vars ) {
    $vars[] = 'jj_book';
    return $vars;
}
add_filter( 'query_vars', 'jj_trip_booking_query_vars' );

/**
 * Is this request the booking page?
 *
 * @return bool
 */
function jj_is_trip_booking_page() {
    return is_singular( 'trip' ) && get_query_var( 'jj_book' );
}

/**
 * Send visitors back to the trip page when it isn't bookable right now
 * (not on sale, or not set up yet). Editors can still preview it.
 * Also handles the booking form's POST.
 */
function jj_trip_booking_guard() {
    if ( ! jj_is_trip_booking_page() ) {
        return;
    }

    $trip_id = get_queried_object_id();
    nocache_headers();

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['jj_checkout'] ) ) {
        jj_trip_start_checkout( $trip_id );
        exit;
    }

    $preview = current_user_can( 'edit_post', $trip_id );
    $open    = 'open' === jj_trip_public_status( $trip_id );

    // The confirmation must show even if the trip sold out a moment later.
    $returning = isset( $_GET['session_id'] );

    if ( ! $returning && ! $preview && ( ! $open || ! jj_trip_booking_ready( $trip_id ) ) ) {
        wp_safe_redirect( get_permalink( $trip_id ) );
        exit;
    }
}
add_action( 'template_redirect', 'jj_trip_booking_guard' );

/**
 * Keep the booking page out of search results.
 */
function jj_trip_booking_robots( $robots ) {
    if ( jj_is_trip_booking_page() ) {
        $robots['noindex']  = true;
        $robots['nofollow'] = true;
    }
    return $robots;
}
add_filter( 'wp_robots', 'jj_trip_booking_robots' );

function jj_trip_booking_document_title( $parts ) {
    if ( jj_is_trip_booking_page() ) {
        $parts['title'] = sprintf( /* translators: %s: trip title */ __( 'Book %s', 'jaiye-journeys' ), jj_trip_volume_title( get_queried_object_id() ) );
    }
    return $parts;
}
add_filter( 'document_title_parts', 'jj_trip_booking_document_title', 20 );


/* ── Checkout ──────────────────────────────────────────────────────── */

/**
 * Pack a plan into session metadata: "ts:amount;ts:amount".
 *
 * @param array $plan [[ts, minor], …].
 * @return string
 */
function jj_booking_pack_plan( $plan ) {
    return implode( ';', array_map( function ( $p ) {
        return (int) $p[0] . ':' . (int) $p[1];
    }, $plan ) );
}

/**
 * @param string $packed From jj_booking_pack_plan().
 * @return array [[ts, minor], …]
 */
function jj_booking_unpack_plan( $packed ) {
    $plan = [];
    foreach ( array_filter( explode( ';', (string) $packed ) ) as $pair ) {
        $bits = explode( ':', $pair );
        if ( 2 === count( $bits ) ) {
            $plan[] = [ (int) $bits[0], (int) $bits[1] ];
        }
    }
    return $plan;
}

/**
 * "£816.67 on 1 Dec, £816.67 on 1 Jan and £816.66 on 1 Feb".
 *
 * @param array  $plan     [[ts, minor], …].
 * @param string $currency gbp|usd.
 * @return string
 */
function jj_booking_plan_sentence( $plan, $currency ) {
    $parts = array_map( function ( $p ) use ( $currency ) {
        /* translators: 1: amount, 2: date */
        return sprintf( __( '%1$s on %2$s', 'jaiye-journeys' ), jj_booking_money( $p[1], $currency ), wp_date( 'j M Y', $p[0] ) );
    }, $plan );

    if ( count( $parts ) < 2 ) {
        return implode( '', $parts );
    }
    $last = array_pop( $parts );
    return implode( ', ', $parts ) . ' ' . __( 'and', 'jaiye-journeys' ) . ' ' . $last;
}

/**
 * Validate the booking form and redirect to Stripe Checkout.
 *
 * No nonce: visitors are logged out and pages may be cached, and the worst a
 * forged request can do is open a checkout page. Every amount comes from the
 * trip, not the form.
 *
 * @param int $trip_id Trip ID.
 */
function jj_trip_start_checkout( $trip_id ) {
    $back = jj_trip_booking_page_url( $trip_id );
    $fail = function ( $code ) use ( $back ) {
        wp_safe_redirect( add_query_arg( 'booking_error', $code, $back ) . '#booking' );
        exit;
    };

    // Editors can run test-mode bookings before a trip goes on sale.
    $test_preview = 'test' === jj_stripe_mode() && current_user_can( 'edit_post', $trip_id );
    if ( ( 'open' !== jj_trip_public_status( $trip_id ) && ! $test_preview ) || ! jj_trip_booking_ready( $trip_id ) ) {
        $fail( 'closed' );
    }

    // phpcs:disable WordPress.Security.NonceVerification.Missing -- see docblock.
    $room_i   = isset( $_POST['room'] ) ? (int) $_POST['room'] : -1;
    $dep_i    = isset( $_POST['departure'] ) ? (int) $_POST['departure'] : -1;
    $guests   = isset( $_POST['guests'] ) ? (int) $_POST['guests'] : 0;
    $payment  = isset( $_POST['payment'] ) && 'full' === $_POST['payment'] ? 'full' : 'plan';
    $currency = isset( $_POST['currency'] ) ? sanitize_key( wp_unslash( $_POST['currency'] ) ) : 'gbp';
    $terms    = ! empty( $_POST['terms'] );
    $code     = isset( $_POST['discount_code'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_code'] ) ) : '';
    // phpcs:enable

    $rooms      = jj_trip_rooms( $trip_id );
    $departures = jj_trip_bookable_departures( $trip_id );

    if ( ! in_array( $currency, jj_trip_booking_currencies( $trip_id ), true ) ) {
        $currency = 'gbp';
    }
    if ( ! isset( $rooms[ $room_i ] ) || $rooms[ $room_i ]['sold_out'] ) {
        $fail( 'room' );
    }
    if ( jj_trip_rows( 'departures', $trip_id ) && ! isset( $departures[ $dep_i ] ) ) {
        $fail( 'departure' );
    }
    $room = $rooms[ $room_i ];
    if ( $guests < 1 || $guests > $room['max_guests'] ) {
        $fail( 'guests' );
    }
    if ( ! $terms ) {
        $fail( 'terms' );
    }

    // Places: count again under a lock, so the last place can't be sold
    // twice. A trip without departure rows keeps its places under 0.
    $dep_key = jj_trip_rows( 'departures', $trip_id ) ? $dep_i : 0;
    if ( ! jj_trip_lock( $trip_id ) ) {
        $fail( 'busy' );
    }
    $fail_open = $fail;
    $fail      = function ( $code ) use ( $fail_open, $trip_id ) {
        jj_trip_unlock( $trip_id );
        $fail_open( $code );
    };
    $left = jj_trip_places_left( $trip_id, $dep_key, $room_i, true );
    if ( null !== $left && $guests > $left ) {
        $fail( 0 === $left ? 'full' : 'places' );
    }

    // Discount codes are for the payment plan, apart from those Booking
    // Settings lets guests use when paying in full.
    $discount = null;
    if ( '' !== trim( $code ) ) {
        $discount = jj_booking_discount( $code, $currency );
        if ( is_wp_error( $discount ) ) {
            $fail( 'code' );
        }
        if ( 'full' === $payment && empty( $discount['full'] ) ) {
            $fail( 'codeplan' );
        }
    }

    $quote = jj_booking_quote( $trip_id, $room, $guests, $currency, $payment, $discount );
    if ( ! $quote && 'plan' === $payment ) {
        // The last plan date passed while they were choosing: pay in full.
        $payment = 'full';
        $quote   = jj_booking_quote( $trip_id, $room, $guests, $currency, 'full' );
    }
    if ( ! $quote ) {
        $fail( 'room' );
    }

    $title     = jj_trip_volume_title( $trip_id );
    $dep_label = isset( $departures[ $dep_i ] ) ? jj_trip_departure_label( $trip_id, $dep_i, $departures[ $dep_i ] ) : '';
    $is_plan   = 'plan' === $payment;

    $metadata = [
        'trip'       => $title,
        'trip_id'    => $trip_id,
        'dep_i'      => $dep_key,
        'room_i'     => $room_i,
        'departure'  => $dep_label,
        'room'       => $room['name'],
        'travellers' => $guests,
        'payment'    => $payment,
        'currency'   => $currency,
        'total'      => $quote['total'],
        'plan'       => jj_booking_pack_plan( $quote['plan'] ),
    ];
    if ( $quote['discount'] ) {
        $metadata['code']     = $discount['code'];
        $metadata['discount'] = $quote['discount'];
    }

    $line_name = $title . ' · ' . $room['name'];
    // Charge per person where today's amount splits evenly; a discount on a
    // pay-in-full booking may not, so then it's one line for the booking.
    $per_person = 0 === $quote['today'] % $guests;
    $line_desc  = array_filter( [
        $dep_label,
        $is_plan
            ? __( 'Deposit per person', 'jaiye-journeys' )
            : ( $per_person
                ? __( 'Paid in full, per person', 'jaiye-journeys' )
                /* translators: %d: number of travellers */
                : sprintf( _n( 'Paid in full, %d traveller', 'Paid in full, %d travellers', $guests, 'jaiye-journeys' ), $guests ) ),
    ] );

    $body = [
        'mode'                       => 'payment',
        'submit_type'                => 'book',
        'customer_creation'          => 'always',
        'billing_address_collection' => 'required',
        'phone_number_collection'    => [ 'enabled' => 'true' ],
        // Places are held until then; 30 minutes is Stripe's shortest.
        'expires_at'                 => time() + 30 * MINUTE_IN_SECONDS,
        'line_items'                 => [
            [
                'quantity'   => $per_person ? $guests : 1,
                'price_data' => [
                    'currency'     => $currency,
                    'unit_amount'  => $per_person ? intdiv( $quote['today'], $guests ) : $quote['today'],
                    'product_data' => [
                        'name'        => $line_name,
                        'description' => implode( ' · ', $line_desc ),
                    ],
                ],
            ],
        ],
        'metadata'                   => $metadata,
        'payment_intent_data'        => [
            'description' => $line_name . ( $dep_label ? ' · ' . $dep_label : '' ),
            'metadata'    => $metadata,
        ],
        // {CHECKOUT_SESSION_ID} is filled in by Stripe, so it's appended raw.
        'success_url'                => $back . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'                 => add_query_arg( [ 'chapter' => $dep_i + 1, 'currency' => $currency ], $back ),
    ];

    if ( $is_plan ) {
        // Save the card for the plan; Stripe collects authentication (SCA)
        // now so the later payments can be taken while they're away.
        $body['payment_intent_data']['setup_future_usage'] = 'off_session';
        $body['custom_text'] = [
            'submit' => [
                'message' => sprintf(
                    /* translators: 1: deposit, 2: list of payments */
                    __( 'Today you pay the %1$s deposit. By paying, you agree that Jaiye Journeys will save this card and charge it %2$s, as set out in our booking terms.', 'jaiye-journeys' ),
                    jj_booking_money( $quote['today'], $currency ),
                    jj_booking_plan_sentence( $quote['plan'], $currency )
                ),
            ],
        ];
    }

    $session = jj_stripe_request( 'POST', 'checkout/sessions', $body );

    if ( is_wp_error( $session ) || empty( $session['url'] ) || 0 !== strpos( $session['url'], 'https://checkout.stripe.com/' ) ) {
        error_log( 'Jaiye booking: Stripe checkout failed for trip ' . $trip_id . ': ' . ( is_wp_error( $session ) ? $session->get_error_message() : 'no URL' ) ); // phpcs:ignore
        $fail( 'stripe' );
    }

    jj_trip_hold_places( $trip_id, $session['id'], $dep_key, $room_i, $guests, $body['expires_at'] + MINUTE_IN_SECONDS );
    jj_trip_unlock( $trip_id );

    // wp_redirect, not wp_safe_redirect: the destination is Stripe, checked above.
    wp_redirect( $session['url'], 303 ); // phpcs:ignore WordPress.Security.SafeRedirect
    exit;
}

/**
 * The paid Checkout Session a guest has just returned from, or null.
 *
 * @param int $trip_id Trip ID.
 * @return array|null
 */
function jj_trip_completed_session( $trip_id ) {
    $id = isset( $_GET['session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['session_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( ! preg_match( '/^cs_(test|live)_[A-Za-z0-9]+$/', $id ) ) {
        return null;
    }

    $session = jj_stripe_request( 'GET', 'checkout/sessions/' . $id );
    if ( is_wp_error( $session )
        || ! isset( $session['metadata']['trip_id'] )
        || (int) $session['metadata']['trip_id'] !== (int) $trip_id
        || 'paid' !== ( isset( $session['payment_status'] ) ? $session['payment_status'] : '' ) ) {
        return null;
    }

    return $session;
}


/* ── Bookings (wp-admin record) ────────────────────────────────────── */

function jj_register_booking_post_type() {
    register_post_type( 'jj_booking', [
        'labels'          => [
            'name'          => __( 'Bookings', 'jaiye-journeys' ),
            'singular_name' => __( 'Booking', 'jaiye-journeys' ),
            'all_items'     => __( 'Bookings', 'jaiye-journeys' ),
            'edit_item'     => __( 'Booking', 'jaiye-journeys' ),
            'not_found'     => __( 'No bookings yet.', 'jaiye-journeys' ),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=trip',
        'supports'        => [ 'title' ],
        'capability_type' => 'post',
        'capabilities'    => [ 'create_posts' => 'do_not_allow' ],
        'map_meta_cap'    => true,
    ] );
}
add_action( 'init', 'jj_register_booking_post_type' );

/**
 * Booking meta helpers (prefix _jj_bk_).
 */
function jj_booking_get( $booking_id, $key ) {
    return get_post_meta( $booking_id, '_jj_bk_' . $key, true );
}

function jj_booking_set( $booking_id, $key, $value ) {
    update_post_meta( $booking_id, '_jj_bk_' . $key, $value );
}

/**
 * Find a booking by a stored field.
 *
 * @return int Booking ID or 0.
 */
function jj_booking_find( $key, $value ) {
    if ( '' === (string) $value ) {
        return 0;
    }
    $ids = get_posts( [
        'post_type'      => 'jj_booking',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_jj_bk_' . $key,
        'meta_value'     => $value,
    ] );
    return $ids ? (int) $ids[0] : 0;
}

/**
 * Email the owner.
 */
function jj_booking_notify( $subject, $lines ) {
    $to = get_option( JJ_BOOKING_NOTIFY_OPTION );
    if ( ! is_email( $to ) ) {
        $to = get_option( 'admin_email' );
    }
    $mode = 'test' === jj_stripe_mode() ? '[TEST] ' : '';
    // Optional lines are passed as null; '' keeps a blank line.
    wp_mail( $to, $mode . $subject, implode( "\n", array_filter( $lines, 'is_string' ) ) );
}

function jj_booking_dashboard_url( $booking_id ) {
    $customer = jj_booking_get( $booking_id, 'customer' );
    $base     = 'https://dashboard.stripe.com/' . ( 'test' === jj_booking_get( $booking_id, 'mode' ) ? 'test/' : '' );
    return $customer ? $base . 'customers/' . $customer : $base . 'payments';
}

/**
 * Record a paid Checkout Session and, for a payment plan, create its
 * subscription schedule. Safe to run twice for the same session.
 *
 * @param array $session Checkout Session from the webhook.
 */
function jj_booking_record_checkout( $session ) {
    $meta = isset( $session['metadata'] ) ? $session['metadata'] : [];
    if ( empty( $meta['trip_id'] ) ) {
        return; // Not one of ours.
    }

    // Paid: the booking now counts against the places, not the hold.
    jj_trip_release_hold( (int) $meta['trip_id'], $session['id'] );

    $booking_id = jj_booking_find( 'session', $session['id'] );
    if ( $booking_id && ( 'full' === jj_booking_get( $booking_id, 'payment' ) || jj_booking_get( $booking_id, 'schedule' ) ) ) {
        return; // Already done.
    }

    $name  = isset( $session['customer_details']['name'] ) ? $session['customer_details']['name'] : '';
    $email = isset( $session['customer_details']['email'] ) ? $session['customer_details']['email'] : '';

    if ( ! $booking_id ) {
        $booking_id = wp_insert_post( [
            'post_type'   => 'jj_booking',
            'post_status' => 'publish',
            'post_title'  => implode( ' · ', array_filter( [ $name ? $name : $email, $meta['trip'], $meta['room'] ] ) ),
        ] );
        if ( ! $booking_id || is_wp_error( $booking_id ) ) {
            return;
        }

        $fields = [
            'session'    => $session['id'],
            'mode'       => ! empty( $session['livemode'] ) ? 'live' : 'test',
            'trip_id'    => (int) $meta['trip_id'],
            'trip'       => $meta['trip'],
            'departure'  => $meta['departure'],
            'room'       => $meta['room'],
            'travellers' => (int) $meta['travellers'],
            'payment'    => $meta['payment'],
            'currency'   => $meta['currency'],
            'total'      => (int) $meta['total'],
            'plan'       => $meta['plan'],
            'dep_i'      => isset( $meta['dep_i'] ) ? (int) $meta['dep_i'] : '',
            'room_i'     => isset( $meta['room_i'] ) ? (int) $meta['room_i'] : '',
            'code'       => isset( $meta['code'] ) ? $meta['code'] : '',
            'discount'   => isset( $meta['discount'] ) ? (int) $meta['discount'] : 0,
            'paid'       => (int) $session['amount_total'],
            'payments'   => 0,
            'customer'   => isset( $session['customer'] ) ? $session['customer'] : '',
            'name'       => $name,
            'email'      => $email,
        ];
        foreach ( $fields as $key => $value ) {
            jj_booking_set( $booking_id, $key, $value );
        }

        $currency = $meta['currency'];
        jj_booking_notify(
            sprintf( 'New booking: %s, %s', $meta['trip'], $name ),
            [
                sprintf( '%s (%s) booked %s.', $name, $email, $meta['trip'] ),
                'Dates: ' . $meta['departure'],
                'Room: ' . $meta['room'] . ' · Travellers: ' . $meta['travellers'],
                'Paid today: ' . jj_booking_money( $session['amount_total'], $currency ),
                ! empty( $meta['code'] ) ? sprintf( 'Discount code: %s, %s off', $meta['code'], jj_booking_money( $meta['discount'], $currency ) ) : null,
                'plan' === $meta['payment']
                    ? 'Payment plan: ' . jj_booking_plan_sentence( jj_booking_unpack_plan( $meta['plan'] ), $currency )
                    : 'Paid in full.',
                '',
                admin_url( 'post.php?action=edit&post=' . $booking_id ),
            ]
        );
    }

    if ( 'plan' !== $meta['payment'] ) {
        jj_booking_set( $booking_id, 'status', 'paid' );
        return;
    }

    $result = jj_booking_create_schedule( $booking_id, $session );
    if ( is_wp_error( $result ) ) {
        jj_booking_set( $booking_id, 'status', 'plan-error' );
        jj_booking_set( $booking_id, 'error', $result->get_error_message() );
        jj_booking_notify(
            'Action needed: payment plan not set up for ' . $name,
            [
                'The deposit was paid, but Stripe refused to set up the payment plan:',
                $result->get_error_message(),
                '',
                'Check the Stripe key permissions in Trips → Booking Settings, then open this booking and press "Set up the payment plan again".',
                admin_url( 'post.php?action=edit&post=' . $booking_id ),
            ]
        );
        return;
    }

    jj_booking_set( $booking_id, 'schedule', $result['id'] );
    jj_booking_set( $booking_id, 'status', 'on-plan' );
    delete_post_meta( $booking_id, '_jj_bk_error' );
}

/**
 * Create the Stripe subscription schedule for a plan booking: one phase per
 * payment date, each billed at its start, ending after the final date.
 *
 * Each phase uses a yearly price so nothing renews inside a phase whatever
 * the gap between dates; proration is off, so leaving a phase never credits
 * anything back.
 *
 * @param int   $booking_id Booking ID.
 * @param array $session    Checkout Session.
 * @return array|WP_Error Schedule.
 */
function jj_booking_create_schedule( $booking_id, $session ) {
    $plan = jj_booking_unpack_plan( jj_booking_get( $booking_id, 'plan' ) );
    if ( ! $plan ) {
        return new WP_Error( 'jj_no_plan', 'The booking has no payment dates.' );
    }

    // Quick test schedule: count the five-minute gaps from now, in case the
    // test checkout took longer than the first gap.
    if ( 'test' === jj_stripe_mode() && get_option( JJ_BOOKING_FAST_TEST_OPTION ) ) {
        foreach ( $plan as $i => $payment ) {
            $plan[ $i ][0] = time() + 300 * ( $i + 1 );
        }
        jj_booking_set( $booking_id, 'plan', jj_booking_pack_plan( $plan ) );
    }

    $full = jj_stripe_request( 'GET', 'checkout/sessions/' . $session['id'], [ 'expand' => [ 'payment_intent' ] ] );
    if ( is_wp_error( $full ) ) {
        return $full;
    }
    $method = isset( $full['payment_intent']['payment_method'] ) ? $full['payment_intent']['payment_method'] : '';
    if ( is_array( $method ) ) {
        $method = $method['id'];
    }
    $customer = isset( $full['customer'] ) ? $full['customer'] : '';
    if ( is_array( $customer ) ) {
        $customer = $customer['id'];
    }
    if ( ! $method || ! $customer ) {
        return new WP_Error( 'jj_no_card', 'Stripe didn’t return a saved card for this booking.' );
    }
    jj_booking_set( $booking_id, 'customer', $customer );

    $product = jj_booking_product( (int) jj_booking_get( $booking_id, 'trip_id' ), jj_booking_get( $booking_id, 'trip' ) );
    if ( is_wp_error( $product ) ) {
        return $product;
    }

    $currency = jj_booking_get( $booking_id, 'currency' );
    $count    = count( $plan );
    $phases   = [];
    foreach ( $plan as $i => $payment ) {
        $phases[] = [
            'items'              => [
                [
                    'quantity'   => 1,
                    'price_data' => [
                        'currency'    => $currency,
                        'product'     => $product,
                        'unit_amount' => $payment[1],
                        'recurring'   => [ 'interval' => 'year' ],
                    ],
                ],
            ],
            'end_date'           => isset( $plan[ $i + 1 ] ) ? $plan[ $i + 1 ][0] : $payment[0] + DAY_IN_SECONDS,
            'billing_cycle_anchor' => 'phase_start',
            'proration_behavior' => 'none',
            'metadata'           => [
                'booking_id' => $booking_id,
                'payment'    => ( $i + 1 ) . ' of ' . $count,
            ],
        ];
    }

    // A first date that passed while the guest was paying is charged now.
    $start = $plan[0][0] > time() + 60 ? $plan[0][0] : 'now';

    return jj_stripe_request( 'POST', 'subscription_schedules', [
        'customer'         => $customer,
        'start_date'       => $start,
        'end_behavior'     => 'cancel',
        'default_settings' => [
            'default_payment_method' => $method,
            'collection_method'      => 'charge_automatically',
        ],
        'phases'           => $phases,
        'metadata'         => [
            'booking_id' => $booking_id,
            'trip'       => jj_booking_get( $booking_id, 'trip' ),
            'room'       => jj_booking_get( $booking_id, 'room' ),
            'departure'  => jj_booking_get( $booking_id, 'departure' ),
        ],
    ], 'jj-plan-' . $session['id'] );
}

/**
 * The Stripe product a trip's plan payments are billed under, created on
 * first use (separately for test and live mode).
 *
 * @return string|WP_Error Product ID.
 */
function jj_booking_product( $trip_id, $title ) {
    $key     = '_jj_stripe_product_' . jj_stripe_mode();
    $product = get_post_meta( $trip_id, $key, true );
    if ( $product ) {
        return $product;
    }

    $created = jj_stripe_request( 'POST', 'products', [
        'name'     => $title . ' · payment plan',
        'metadata' => [ 'trip_id' => $trip_id ],
    ], 'jj-product-' . jj_stripe_mode() . '-' . $trip_id );
    if ( is_wp_error( $created ) ) {
        return $created;
    }

    update_post_meta( $trip_id, $key, $created['id'] );
    return $created['id'];
}

/**
 * Which booking an invoice belongs to, via the subscription's metadata.
 *
 * @param array $invoice Invoice.
 * @return int Booking ID or 0.
 */
function jj_booking_from_invoice( $invoice ) {
    $details = [];
    if ( isset( $invoice['parent']['subscription_details'] ) ) {
        $details = $invoice['parent']['subscription_details'];
    } elseif ( isset( $invoice['subscription_details'] ) ) {
        $details = $invoice['subscription_details'];
    }

    if ( ! empty( $details['metadata']['booking_id'] ) ) {
        return (int) $details['metadata']['booking_id'];
    }

    $subscription = isset( $details['subscription'] ) ? $details['subscription'] : ( isset( $invoice['subscription'] ) ? $invoice['subscription'] : '' );
    if ( is_array( $subscription ) ) {
        $subscription = $subscription['id'];
    }
    if ( ! $subscription ) {
        return 0;
    }

    $sub = jj_stripe_request( 'GET', 'subscriptions/' . $subscription );
    return ( ! is_wp_error( $sub ) && ! empty( $sub['metadata']['booking_id'] ) ) ? (int) $sub['metadata']['booking_id'] : 0;
}


/* ── Webhook: /wp-json/jj/v1/stripe ────────────────────────────────── */

function jj_booking_rest_routes() {
    register_rest_route( 'jj/v1', '/stripe', [
        'methods'             => 'POST',
        'callback'            => 'jj_booking_webhook',
        'permission_callback' => '__return_true', // Authenticated by Stripe's signature below.
    ] );
}
add_action( 'rest_api_init', 'jj_booking_rest_routes' );

function jj_booking_discount_route() {
    register_rest_route( 'jj/v1', '/discount', [
        'methods'             => 'GET',
        'callback'            => 'jj_booking_discount_check',
        'permission_callback' => '__return_true', // Public: the booking page checks codes as guests type them.
        'args'                => [
            'trip'     => [ 'required' => true, 'type' => 'integer' ],
            'code'     => [ 'required' => true, 'type' => 'string' ],
            'currency' => [ 'required' => true, 'type' => 'string' ],
        ],
    ] );
}
add_action( 'rest_api_init', 'jj_booking_discount_route' );

/**
 * The booking page's "Apply" button. The discount is checked again when
 * the guest pays, so this only drives what the page shows.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function jj_booking_discount_check( $request ) {
    // A guest typing a code makes a handful of checks; stop anyone guessing.
    $ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $limit = 'jj_code_tries_' . md5( $ip );
    $tries = (int) get_transient( $limit );
    if ( $tries >= 20 ) {
        return new WP_REST_Response( [ 'message' => __( 'Too many tries. Please wait a few minutes and try again.', 'jaiye-journeys' ) ], 429 );
    }
    set_transient( $limit, $tries + 1, 10 * MINUTE_IN_SECONDS );

    $trip_id  = (int) $request['trip'];
    $currency = sanitize_key( $request['currency'] );
    if ( 'trip' !== get_post_type( $trip_id ) || ! jj_trip_booking_ready( $trip_id )
        || ! in_array( $currency, jj_trip_booking_currencies( $trip_id ), true ) ) {
        return new WP_REST_Response( [ 'message' => __( 'Sorry, that code isn’t recognised.', 'jaiye-journeys' ) ], 400 );
    }

    $discount = jj_booking_discount( $request['code'], $currency );
    if ( is_wp_error( $discount ) ) {
        return new WP_REST_Response( [ 'message' => $discount->get_error_message() ], 400 );
    }
    return new WP_REST_Response( $discount, 200 );
}

/**
 * Check the Stripe-Signature header (HMAC-SHA256 of "timestamp.payload").
 *
 * @return bool
 */
function jj_booking_verify_signature( $payload, $header, $secret ) {
    $timestamp  = 0;
    $signatures = [];
    foreach ( explode( ',', (string) $header ) as $part ) {
        $kv = explode( '=', trim( $part ), 2 );
        if ( 2 !== count( $kv ) ) {
            continue;
        }
        if ( 't' === $kv[0] ) {
            $timestamp = (int) $kv[1];
        } elseif ( 'v1' === $kv[0] ) {
            $signatures[] = $kv[1];
        }
    }
    if ( ! $timestamp || ! $signatures || abs( time() - $timestamp ) > 300 ) {
        return false;
    }

    $expected = hash_hmac( 'sha256', $timestamp . '.' . $payload, $secret );
    foreach ( $signatures as $signature ) {
        if ( hash_equals( $expected, $signature ) ) {
            return true;
        }
    }
    return false;
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function jj_booking_webhook( $request ) {
    $secret  = jj_stripe_webhook_secret();
    $payload = $request->get_body();

    if ( ! $secret || ! jj_booking_verify_signature( $payload, $request->get_header( 'stripe-signature' ), $secret ) ) {
        return new WP_REST_Response( [ 'error' => 'bad signature' ], 400 );
    }

    $event  = json_decode( $payload, true );
    $object = isset( $event['data']['object'] ) ? $event['data']['object'] : [];
    $type   = isset( $event['type'] ) ? $event['type'] : '';

    switch ( $type ) {
        case 'checkout.session.completed':
        case 'checkout.session.async_payment_succeeded':
            if ( isset( $object['payment_status'] ) && 'paid' === $object['payment_status'] ) {
                jj_booking_record_checkout( $object );
            }
            break;

        case 'invoice.paid':
            $booking_id = jj_booking_from_invoice( $object );
            if ( $booking_id && ! empty( $object['amount_paid'] ) ) {
                $made = (int) jj_booking_get( $booking_id, 'payments' ) + 1;
                $plan = jj_booking_unpack_plan( jj_booking_get( $booking_id, 'plan' ) );
                jj_booking_set( $booking_id, 'payments', $made );
                jj_booking_set( $booking_id, 'paid', (int) jj_booking_get( $booking_id, 'paid' ) + (int) $object['amount_paid'] );
                jj_booking_set( $booking_id, 'status', $made >= count( $plan ) ? 'paid' : 'on-plan' );
                foreach ( [ 'failed_since', 'pay_url', 'overdue_notified' ] as $key ) {
                    delete_post_meta( $booking_id, '_jj_bk_' . $key );
                }
            }
            break;

        case 'invoice.payment_failed':
        case 'invoice.payment_action_required':
            $booking_id = jj_booking_from_invoice( $object );
            if ( $booking_id ) {
                if ( ! jj_booking_get( $booking_id, 'failed_since' ) ) {
                    jj_booking_set( $booking_id, 'failed_since', time() );
                }
                if ( ! empty( $object['hosted_invoice_url'] ) ) {
                    jj_booking_set( $booking_id, 'pay_url', $object['hosted_invoice_url'] );
                }
                jj_booking_set( $booking_id, 'status', 'overdue' );
            }
            break;
    }

    return new WP_REST_Response( [ 'received' => true ], 200 );
}


/* ── 14 days unpaid: tell the owner ────────────────────────────────── */

function jj_booking_schedule_cron() {
    if ( ! wp_next_scheduled( 'jj_booking_overdue_check' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'jj_booking_overdue_check' );
    }
}
add_action( 'init', 'jj_booking_schedule_cron' );

/**
 * Email the owner once about each booking with a payment unpaid for 14
 * days. The booking stays as it is; what happens next is their call.
 */
function jj_booking_overdue_check() {
    $ids = get_posts( [
        'post_type'      => 'jj_booking',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'     => '_jj_bk_failed_since',
                'value'   => time() - JJ_BOOKING_OVERDUE_DAYS * DAY_IN_SECONDS,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ],
            [
                'key'     => '_jj_bk_overdue_notified',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ] );

    foreach ( $ids as $booking_id ) {
        jj_booking_notify(
            sprintf( 'Payment unpaid for %d days: %s', JJ_BOOKING_OVERDUE_DAYS, get_the_title( $booking_id ) ),
            [
                sprintf(
                    'A payment-plan payment for %s (%s) has been unpaid since %s. Stripe has retried it and emailed them a link to update their card.',
                    jj_booking_get( $booking_id, 'name' ),
                    jj_booking_get( $booking_id, 'email' ),
                    wp_date( 'j F Y', (int) jj_booking_get( $booking_id, 'failed_since' ) )
                ),
                'The booking has not been cancelled. What happens next is up to you.',
                '',
                'Booking: ' . admin_url( 'post.php?action=edit&post=' . $booking_id ),
                'Stripe: ' . jj_booking_dashboard_url( $booking_id ),
            ]
        );
        jj_booking_set( $booking_id, 'overdue_notified', time() );
    }
}
add_action( 'jj_booking_overdue_check', 'jj_booking_overdue_check' );


/* ── Bookings admin screens ────────────────────────────────────────── */

function jj_booking_status_label( $status ) {
    $labels = [
        'on-plan'    => __( 'On payment plan', 'jaiye-journeys' ),
        'paid'       => __( 'Paid in full', 'jaiye-journeys' ),
        'overdue'    => __( 'Payment overdue', 'jaiye-journeys' ),
        'plan-error' => __( 'Plan not set up: action needed', 'jaiye-journeys' ),
    ];
    return isset( $labels[ $status ] ) ? $labels[ $status ] : __( 'Deposit paid', 'jaiye-journeys' );
}

function jj_booking_columns( $columns ) {
    return [
        'cb'         => $columns['cb'],
        'title'      => __( 'Booking', 'jaiye-journeys' ),
        'jj_dates'   => __( 'Dates', 'jaiye-journeys' ),
        'jj_status'  => __( 'Status', 'jaiye-journeys' ),
        'jj_paid'    => __( 'Paid so far', 'jaiye-journeys' ),
        'date'       => __( 'Booked', 'jaiye-journeys' ),
    ];
}
add_filter( 'manage_jj_booking_posts_columns', 'jj_booking_columns' );

function jj_booking_column( $column, $booking_id ) {
    $currency = jj_booking_get( $booking_id, 'currency' );
    switch ( $column ) {
        case 'jj_dates':
            echo esc_html( jj_booking_get( $booking_id, 'departure' ) );
            break;
        case 'jj_status':
            echo esc_html( jj_booking_status_label( jj_booking_get( $booking_id, 'status' ) ) );
            if ( 'test' === jj_booking_get( $booking_id, 'mode' ) ) {
                echo ' <em>(' . esc_html__( 'test', 'jaiye-journeys' ) . ')</em>';
            }
            break;
        case 'jj_paid':
            if ( $currency ) {
                echo esc_html( jj_booking_money( jj_booking_get( $booking_id, 'paid' ), $currency ) . ' / ' . jj_booking_money( jj_booking_get( $booking_id, 'total' ), $currency ) );
            }
            break;
    }
}
add_action( 'manage_jj_booking_posts_custom_column', 'jj_booking_column', 10, 2 );

function jj_booking_meta_boxes() {
    add_meta_box( 'jj-booking-details', __( 'Booking details', 'jaiye-journeys' ), 'jj_booking_details_box', 'jj_booking', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'jj_booking_meta_boxes' );

function jj_booking_details_box( $post ) {
    $id       = $post->ID;
    $currency = jj_booking_get( $id, 'currency' );
    $plan     = jj_booking_unpack_plan( jj_booking_get( $id, 'plan' ) );
    $made     = (int) jj_booking_get( $id, 'payments' );
    $rows     = [
        __( 'Status', 'jaiye-journeys' )     => jj_booking_status_label( jj_booking_get( $id, 'status' ) ) . ( 'test' === jj_booking_get( $id, 'mode' ) ? ' (Stripe test mode)' : '' ),
        __( 'Guest', 'jaiye-journeys' )      => trim( jj_booking_get( $id, 'name' ) . ' · ' . jj_booking_get( $id, 'email' ), ' ·' ),
        __( 'Journey', 'jaiye-journeys' )    => jj_booking_get( $id, 'trip' ),
        __( 'Dates', 'jaiye-journeys' )      => jj_booking_get( $id, 'departure' ),
        __( 'Room', 'jaiye-journeys' )       => jj_booking_get( $id, 'room' ),
        __( 'Travellers', 'jaiye-journeys' ) => jj_booking_get( $id, 'travellers' ),
        __( 'Total', 'jaiye-journeys' )      => $currency ? jj_booking_money( jj_booking_get( $id, 'total' ), $currency ) : '',
        __( 'Discount', 'jaiye-journeys' )   => jj_booking_get( $id, 'code' ) && $currency
            /* translators: 1: amount, 2: code */
            ? sprintf( __( '%1$s off with %2$s', 'jaiye-journeys' ), jj_booking_money( jj_booking_get( $id, 'discount' ), $currency ), jj_booking_get( $id, 'code' ) )
            : '',
        __( 'Paid so far', 'jaiye-journeys' ) => $currency ? jj_booking_money( jj_booking_get( $id, 'paid' ), $currency ) : '',
    ];
    if ( '' === $rows[ __( 'Discount', 'jaiye-journeys' ) ] ) {
        unset( $rows[ __( 'Discount', 'jaiye-journeys' ) ] );
    }
    echo '<table class="widefat striped"><tbody>';
    foreach ( $rows as $label => $value ) {
        printf( '<tr><th style="width:12em">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ) );
    }
    echo '</tbody></table>';

    if ( $plan ) {
        echo '<h4>' . esc_html__( 'Payment plan', 'jaiye-journeys' ) . '</h4><ol>';
        foreach ( $plan as $i => $payment ) {
            printf(
                '<li>%s · %s%s</li>',
                esc_html( wp_date( 'j F Y', $payment[0] ) ),
                esc_html( jj_booking_money( $payment[1], $currency ) ),
                $i < $made ? ' · <strong>' . esc_html__( 'paid', 'jaiye-journeys' ) . '</strong>' : ''
            );
        }
        echo '</ol>';
    }

    $failed = (int) jj_booking_get( $id, 'failed_since' );
    if ( $failed ) {
        printf(
            '<p><strong>%s</strong> %s</p>',
            esc_html__( 'A payment has been unpaid since', 'jaiye-journeys' ),
            esc_html( wp_date( 'j F Y', $failed ) )
        );
        $pay_url = jj_booking_get( $id, 'pay_url' );
        if ( $pay_url ) {
            printf( '<p>%s <a href="%s" target="_blank" rel="noopener">%s</a></p>', esc_html__( 'Payment link you can send them:', 'jaiye-journeys' ), esc_url( $pay_url ), esc_html( $pay_url ) );
        }
    }

    $error = jj_booking_get( $id, 'error' );
    if ( 'plan-error' === jj_booking_get( $id, 'status' ) ) {
        printf( '<p style="color:#b32d2e"><strong>%s</strong> %s</p>', esc_html__( 'Stripe said:', 'jaiye-journeys' ), esc_html( $error ) );
        printf(
            '<p><a class="button button-primary" href="%s">%s</a></p>',
            esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=jj_booking_retry&booking=' . $id ), 'jj_booking_retry_' . $id ) ),
            esc_html__( 'Set up the payment plan again', 'jaiye-journeys' )
        );
    }

    printf( '<p><a href="%s" target="_blank" rel="noopener">%s</a></p>', esc_url( jj_booking_dashboard_url( $id ) ), esc_html__( 'Open in Stripe', 'jaiye-journeys' ) );
}

/**
 * Retry a payment plan that failed to set up (e.g. after fixing the key's
 * permissions). The same idempotency key means it can never create two.
 */
function jj_booking_retry() {
    $id = isset( $_GET['booking'] ) ? absint( $_GET['booking'] ) : 0;
    check_admin_referer( 'jj_booking_retry_' . $id );
    if ( ! current_user_can( 'edit_post', $id ) ) {
        wp_die( esc_html__( 'Not allowed.', 'jaiye-journeys' ) );
    }

    $session = jj_stripe_request( 'GET', 'checkout/sessions/' . jj_booking_get( $id, 'session' ) );
    if ( ! is_wp_error( $session ) ) {
        jj_booking_record_checkout( $session );
    }
    wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $id ) );
    exit;
}
add_action( 'admin_post_jj_booking_retry', 'jj_booking_retry' );


/* ── Booking Settings screen ───────────────────────────────────────── */

function jj_booking_settings_menu() {
    add_submenu_page(
        'edit.php?post_type=trip',
        __( 'Booking Settings', 'jaiye-journeys' ),
        __( 'Booking Settings', 'jaiye-journeys' ),
        'manage_options',
        'jj-booking-settings',
        'jj_booking_settings_page'
    );
}
add_action( 'admin_menu', 'jj_booking_settings_menu' );

function jj_booking_settings_register() {
    register_setting( 'jj_booking', JJ_STRIPE_KEY_OPTION, [
        'type'              => 'string',
        'sanitize_callback' => 'jj_booking_sanitise_key',
        'default'           => '',
    ] );
    register_setting( 'jj_booking', JJ_STRIPE_WEBHOOK_OPTION, [
        'type'              => 'string',
        'sanitize_callback' => 'jj_booking_sanitise_webhook_secret',
        'default'           => '',
    ] );
    register_setting( 'jj_booking', JJ_BOOKING_NOTIFY_OPTION, [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default'           => '',
    ] );
    register_setting( 'jj_booking', JJ_BOOKING_FULL_CODES_OPTION, [
        'type'              => 'string',
        'sanitize_callback' => function ( $value ) {
            preg_match_all( '/[A-Z0-9_-]+/', strtoupper( (string) $value ), $codes );
            return implode( ', ', array_unique( $codes[0] ) );
        },
        'default'           => '',
    ] );
    register_setting( 'jj_booking', JJ_BOOKING_FAST_TEST_OPTION, [
        'type'              => 'string',
        'sanitize_callback' => function ( $value ) {
            return $value ? '1' : '';
        },
        'default'           => '',
    ] );
}
add_action( 'admin_init', 'jj_booking_settings_register' );

/**
 * Keep a saved secret when its box is left blank (it's never shown again);
 * clear it only when its "Remove" box is ticked.
 */
function jj_booking_sanitise_secret( $value, $option, $pattern, $message ) {
    $old = (string) get_option( $option, '' );

    if ( ! empty( $_POST[ $option . '_remove' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- options.php checks the settings nonce.
        return '';
    }

    $value = trim( (string) $value );
    if ( '' === $value ) {
        return $old;
    }
    if ( ! preg_match( $pattern, $value ) ) {
        add_settings_error( $option, $option . '_bad', $message );
        return $old;
    }
    return $value;
}

function jj_booking_sanitise_key( $value ) {
    return jj_booking_sanitise_secret( $value, JJ_STRIPE_KEY_OPTION, '/^(sk|rk)_(test|live)_[A-Za-z0-9]+$/', __( 'That doesn’t look like a Stripe secret or restricted key (it starts sk_ or rk_). Nothing was changed.', 'jaiye-journeys' ) );
}

function jj_booking_sanitise_webhook_secret( $value ) {
    return jj_booking_sanitise_secret( $value, JJ_STRIPE_WEBHOOK_OPTION, '/^whsec_[A-Za-z0-9]+$/', __( 'That doesn’t look like a webhook signing secret (it starts whsec_). Nothing was changed.', 'jaiye-journeys' ) );
}

/**
 * One secret field: saved state, a replace box and a remove tick.
 */
function jj_booking_secret_field( $option, $constant, $placeholder ) {
    $value = defined( $constant ) && constant( $constant ) ? constant( $constant ) : (string) get_option( $option, '' );
    if ( $value ) {
        echo '<p>' . esc_html( sprintf( /* translators: %s: last four characters */ __( 'Saved: ending …%s', 'jaiye-journeys' ), substr( $value, -4 ) ) );
        if ( defined( $constant ) && constant( $constant ) ) {
            echo ' ' . esc_html__( '(set in wp-config.php)', 'jaiye-journeys' ) . '</p>';
            return;
        }
        echo '</p>';
    }
    printf(
        '<input type="password" name="%s" value="" class="regular-text" autocomplete="off" placeholder="%s">',
        esc_attr( $option ),
        esc_attr( $value ? __( 'Paste a new one to replace it', 'jaiye-journeys' ) : $placeholder )
    );
    if ( $value ) {
        printf( '<p><label><input type="checkbox" name="%s_remove" value="1"> %s</label></p>', esc_attr( $option ), esc_html__( 'Remove (turns off booking pages)', 'jaiye-journeys' ) );
    }
}

function jj_booking_settings_page() {
    $mode = jj_stripe_mode();
    ?>
    <div class="wrap">
      <h1><?php esc_html_e( 'Booking Settings', 'jaiye-journeys' ); ?></h1>
      <p><?php esc_html_e( 'Trips with Rooms get a booking page that takes payment through Stripe. It switches on once the key and webhook secret are both saved.', 'jaiye-journeys' ); ?></p>
      <?php if ( $mode ) : ?>
        <p><strong><?php echo esc_html( 'test' === $mode ? __( 'Stripe TEST mode: no real money is taken. Use Stripe’s test cards.', 'jaiye-journeys' ) : __( 'Stripe LIVE mode: real payments.', 'jaiye-journeys' ) ); ?></strong></p>
      <?php endif; ?>

      <form method="post" action="options.php">
        <?php settings_fields( 'jj_booking' ); ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><?php esc_html_e( 'Stripe key', 'jaiye-journeys' ); ?></th>
            <td>
              <?php jj_booking_secret_field( JJ_STRIPE_KEY_OPTION, 'JJ_STRIPE_SECRET_KEY', 'sk_test_… or rk_live_…' ); ?>
              <p class="description"><?php esc_html_e( 'For testing, your test-mode secret key (sk_test_…). For launch, a live restricted key (rk_live_…) with the permissions listed in the setup notes.', 'jaiye-journeys' ); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Webhook signing secret', 'jaiye-journeys' ); ?></th>
            <td>
              <?php jj_booking_secret_field( JJ_STRIPE_WEBHOOK_OPTION, 'JJ_STRIPE_WEBHOOK_SECRET', 'whsec_…' ); ?>
              <p class="description">
                <?php esc_html_e( 'In Stripe: Developers → Webhooks → Add endpoint. Endpoint URL:', 'jaiye-journeys' ); ?>
                <code><?php echo esc_html( rest_url( 'jj/v1/stripe' ) ); ?></code><br>
                <?php esc_html_e( 'Events: checkout.session.completed, checkout.session.async_payment_succeeded, invoice.paid, invoice.payment_failed, invoice.payment_action_required. Then copy its signing secret here. Test and live mode each have their own.', 'jaiye-journeys' ); ?>
              </p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="jj-notify"><?php esc_html_e( 'Send booking emails to', 'jaiye-journeys' ); ?></label></th>
            <td>
              <input type="email" id="jj-notify" name="<?php echo esc_attr( JJ_BOOKING_NOTIFY_OPTION ); ?>" value="<?php echo esc_attr( get_option( JJ_BOOKING_NOTIFY_OPTION ) ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>">
              <p class="description"><?php esc_html_e( 'New bookings, payment plans that couldn’t be set up, and payments unpaid for 14 days. Blank uses the site admin email.', 'jaiye-journeys' ); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="jj-full-codes"><?php esc_html_e( 'Codes that also work paying in full', 'jaiye-journeys' ); ?></label></th>
            <td>
              <input type="text" id="jj-full-codes" name="<?php echo esc_attr( JJ_BOOKING_FULL_CODES_OPTION ); ?>" value="<?php echo esc_attr( get_option( JJ_BOOKING_FULL_CODES_OPTION ) ); ?>" class="regular-text" placeholder="BTLREADS10">
              <p class="description"><?php esc_html_e( 'Discount codes normally work with the payment plan only. Codes listed here (separate several with commas) also work when paying in full, on top of the pay-in-full price. Make the codes themselves in Stripe.', 'jaiye-journeys' ); ?></p>
            </td>
          </tr>
          <?php if ( 'test' === $mode ) : ?>
            <tr>
              <th scope="row"><?php esc_html_e( 'Quick test schedule', 'jaiye-journeys' ); ?></th>
              <td>
                <label>
                  <input type="checkbox" name="<?php echo esc_attr( JJ_BOOKING_FAST_TEST_OPTION ); ?>" value="1" <?php checked( get_option( JJ_BOOKING_FAST_TEST_OPTION ), '1' ); ?>>
                  <?php esc_html_e( 'Charge test payment plans every 5 minutes instead of on the real dates, so you can watch a whole plan run. Test mode only.', 'jaiye-journeys' ); ?>
                </label>
              </td>
            </tr>
          <?php endif; ?>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
