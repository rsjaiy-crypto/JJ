<?php
/**
 * The owner's dashboard: /manage/, a phone- and tablet-friendly page for
 * bookings, places sold and quick trip edits. Logged-in editors only.
 *
 * Screens (templates in manage/):
 *   /manage/                 Overview: needs attention, places, money, latest
 *   /manage/bookings/        Every booking, filterable
 *   /manage/booking/<id>/    One booking: plan, note, cancel
 *   /manage/trips/           Trips with places sold
 *   /manage/trip/<id>/       Quick edits: status, prices, places, dates
 *
 * It reads and writes the same data as wp-admin (trip fields from
 * inc/trip-fields.php, bookings from inc/trip-booking.php), so the two can't
 * drift. Longer content (itineraries, FAQs) stays in wp-admin.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

/* ── Routing ───────────────────────────────────────────────────────── */

// Changing these needs a JJ_TRIP_REWRITE_VERSION bump to take effect.
function jj_manage_rewrite() {
    add_rewrite_rule( '^manage/?$', 'index.php?jj_manage=home', 'top' );
    add_rewrite_rule( '^manage/(bookings|trips)/?$', 'index.php?jj_manage=$matches[1]', 'top' );
    add_rewrite_rule( '^manage/(booking|trip)/([0-9]+)/?$', 'index.php?jj_manage=$matches[1]&jj_manage_id=$matches[2]', 'top' );
}
add_action( 'init', 'jj_manage_rewrite' );

function jj_manage_query_vars( $vars ) {
    $vars[] = 'jj_manage';
    $vars[] = 'jj_manage_id';
    return $vars;
}
add_filter( 'query_vars', 'jj_manage_query_vars' );

/**
 * A dashboard URL.
 *
 * @param string $path e.g. 'bookings', 'booking/12'.
 * @param array  $args Query args.
 * @return string
 */
function jj_manage_url( $path = '', $args = [] ) {
    $url = home_url( '/manage/' . ( $path ? trailingslashit( $path ) : '' ) );
    return $args ? add_query_arg( $args, $url ) : $url;
}

/**
 * Serve the dashboard: log in first, editors only, never cached or indexed.
 */
function jj_manage_render() {
    $screen = get_query_var( 'jj_manage' );
    if ( ! $screen ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        auth_redirect(); // Back here after logging in.
    }
    if ( ! current_user_can( 'edit_others_posts' ) ) {
        wp_die( esc_html__( 'This page is for the Jaiye Journeys team.', 'jaiye-journeys' ), '', [ 'response' => 403 ] );
    }

    nocache_headers();
    header( 'X-Robots-Tag: noindex, nofollow', true );

    if ( 'POST' === ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        jj_manage_handle_post( $screen, (int) get_query_var( 'jj_manage_id' ) );
    }

    $views = [
        'home'     => 'overview',
        'bookings' => 'bookings',
        'booking'  => 'booking',
        'trips'    => 'trips',
        'trip'     => 'trip',
    ];
    $view = isset( $views[ $screen ] ) ? $views[ $screen ] : 'overview';
    $id   = (int) get_query_var( 'jj_manage_id' );

    require get_template_directory() . '/manage/layout.php';
    exit;
}
add_action( 'template_redirect', 'jj_manage_render', 1 );


/* ── Data ──────────────────────────────────────────────────────────── */

/**
 * Trips with a booking page (they have Rooms), on sale first.
 *
 * @return WP_Post[]
 */
function jj_manage_trips() {
    $trips = get_posts( [
        'post_type'      => 'trip',
        'post_status'    => [ 'publish', 'draft', 'private', 'future', 'pending' ],
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ] );
    $order = [ 'open' => 0, 'coming-soon' => 1, 'announced' => 2, 'sold-out' => 3 ];
    usort( $trips, function ( $a, $b ) use ( $order ) {
        $sa = jj_trip_status( $a->ID );
        $sb = jj_trip_status( $b->ID );
        return ( isset( $order[ $sa ] ) ? $order[ $sa ] : 9 ) - ( isset( $order[ $sb ] ) ? $order[ $sb ] : 9 );
    } );
    return $trips;
}

/**
 * Bookings, newest first.
 *
 * @param array $filter trip (ID), status, q (search), test (bool: include test mode).
 * @return int[] Booking IDs.
 */
function jj_manage_bookings( $filter = [] ) {
    $meta = [];
    if ( empty( $filter['test'] ) ) {
        $meta[] = [ 'key' => '_jj_bk_mode', 'value' => 'live' ];
    }
    if ( ! empty( $filter['trip'] ) ) {
        $meta[] = [ 'key' => '_jj_bk_trip_id', 'value' => (int) $filter['trip'] ];
    }
    if ( ! empty( $filter['status'] ) ) {
        // "Deposit paid" is stored as no status at all.
        $meta[] = 'deposit' === $filter['status']
            ? [ 'key' => '_jj_bk_status', 'compare' => 'NOT EXISTS' ]
            : [ 'key' => '_jj_bk_status', 'value' => $filter['status'] ];
    }

    $args = [
        'post_type'      => 'jj_booking',
        'post_status'    => [ 'publish', 'private', 'draft', 'pending' ],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    if ( $meta ) {
        $args['meta_query'] = $meta; // phpcs:ignore WordPress.DB.SlowDBQuery
    }
    $ids = get_posts( $args );

    // Search name, email and booking title.
    if ( ! empty( $filter['q'] ) ) {
        $q   = strtolower( $filter['q'] );
        $ids = array_values( array_filter( $ids, function ( $id ) use ( $q ) {
            $hay = strtolower( get_the_title( $id ) . ' ' . jj_booking_get( $id, 'name' ) . ' ' . jj_booking_get( $id, 'email' ) );
            return false !== strpos( $hay, $q );
        } ) );
    }
    return $ids;
}

/**
 * Everything a dashboard card or page shows about one booking.
 *
 * @param int $id Booking ID.
 * @return array
 */
function jj_manage_booking( $id ) {
    $currency = jj_booking_get( $id, 'currency' );
    $plan     = jj_booking_unpack_plan( jj_booking_get( $id, 'plan' ) );
    $made     = (int) jj_booking_get( $id, 'payments' );
    $status   = jj_booking_get( $id, 'status' );
    $next     = ( 'plan' === jj_booking_get( $id, 'payment' ) && isset( $plan[ $made ] ) ) ? $plan[ $made ] : null;

    return [
        'id'         => $id,
        'name'       => jj_booking_get( $id, 'name' ),
        'email'      => jj_booking_get( $id, 'email' ),
        'trip_id'    => (int) jj_booking_get( $id, 'trip_id' ),
        'trip'       => jj_booking_get( $id, 'trip' ),
        'departure'  => jj_booking_get( $id, 'departure' ),
        'room'       => jj_booking_get( $id, 'room' ),
        'travellers' => (int) jj_booking_get( $id, 'travellers' ),
        'payment'    => jj_booking_get( $id, 'payment' ),
        'currency'   => $currency,
        'total'      => (int) jj_booking_get( $id, 'total' ),
        'paid'       => (int) jj_booking_get( $id, 'paid' ),
        'code'       => jj_booking_get( $id, 'code' ),
        'discount'   => (int) jj_booking_get( $id, 'discount' ),
        'plan'       => $plan,
        'plan_kind'  => jj_booking_get( $id, 'plan_kind' ),
        'made'       => $made,
        'next'       => $next,
        'status'     => $status,
        'label'      => jj_booking_status_label( $status ),
        'tone'       => in_array( $status, [ 'overdue', 'plan-error' ], true ) ? 'alert' : ( 'paid' === $status ? 'done' : 'ok' ),
        'test'       => 'test' === jj_booking_get( $id, 'mode' ),
        'note'       => jj_booking_get( $id, 'note' ),
        'booked'     => get_post_time( 'U', true, $id ),
        'error'      => jj_booking_get( $id, 'error' ),
    ];
}

/**
 * Money across bookings, per currency: booked (totals), received, to come.
 *
 * @param int[] $ids Booking IDs.
 * @return array [ currency => [ booked, received, due ] ] in minor units.
 */
function jj_manage_money( $ids ) {
    $out = [];
    foreach ( $ids as $id ) {
        $cur = jj_booking_get( $id, 'currency' );
        if ( ! $cur ) {
            continue;
        }
        if ( ! isset( $out[ $cur ] ) ) {
            $out[ $cur ] = [ 'booked' => 0, 'received' => 0, 'due' => 0 ];
        }
        $total = (int) jj_booking_get( $id, 'total' );
        $paid  = (int) jj_booking_get( $id, 'paid' );
        $out[ $cur ]['booked']   += $total;
        $out[ $cur ]['received'] += $paid;
        $out[ $cur ]['due']      += max( 0, $total - $paid );
    }
    return $out;
}

/**
 * Places per chapter and room for one trip, for the bars.
 *
 * @param int $trip_id Trip ID.
 * @return array[] [ [ label, rooms => [ [ name, places|null, sold, held, left|null ] ] ] ]
 */
function jj_manage_places( $trip_id ) {
    $rooms = jj_trip_rooms( $trip_id );
    if ( ! $rooms ) {
        return [];
    }
    $taken = jj_trip_places_taken( $trip_id );
    $held  = [];
    foreach ( jj_trip_holds( $trip_id ) as $hold ) {
        $held[ $hold['dep'] ][ $hold['room'] ] = ( isset( $held[ $hold['dep'] ][ $hold['room'] ] ) ? $held[ $hold['dep'] ][ $hold['room'] ] : 0 ) + $hold['guests'];
    }

    $departures = jj_trip_rows( 'departures', $trip_id );
    $out        = [];
    foreach ( $departures ? $departures : [ [] ] as $d => $departure ) {
        $row = [
            'label' => $departures ? jj_trip_departure_label( $trip_id, $d, $departure ) : __( 'Places', 'jaiye-journeys' ),
            'rooms' => [],
        ];
        foreach ( $rooms as $r => $room ) {
            $h               = isset( $held[ $d ][ $r ] ) ? $held[ $d ][ $r ] : 0;
            $sold            = ( isset( $taken[ $d ][ $r ] ) ? $taken[ $d ][ $r ] : 0 ) - $h;
            $row['rooms'][] = [
                'name'     => $room['name'],
                'places'   => $room['places'],
                'sold'     => $sold,
                'held'     => $h,
                'left'     => null === $room['places'] ? null : max( 0, $room['places'] - $sold - $h ),
                'sold_out' => $room['sold_out'],
            ];
        }
        $out[] = $row;
    }
    return $out;
}

/**
 * A trip field's schema entry, or a repeater subfield's.
 *
 * @param string $key Field key.
 * @param string $sub Subfield key, for repeaters.
 * @return array|null
 */
function jj_manage_field( $key, $sub = '' ) {
    $fields = jj_trip_all_fields();
    if ( ! isset( $fields[ $key ] ) ) {
        return null;
    }
    if ( '' === $sub ) {
        return $fields[ $key ];
    }
    return isset( $fields[ $key ]['subfields'][ $sub ] ) ? $fields[ $key ]['subfields'][ $sub ] : null;
}


/* ── Saving ────────────────────────────────────────────────────────── */

/**
 * Form posts from the dashboard. Every form carries a nonce for its action
 * and the object it changes; each ends in a redirect so a refresh can't
 * post twice.
 *
 * @param string $screen Screen the form was on.
 * @param int    $id     Booking or trip ID from the URL.
 */
function jj_manage_handle_post( $screen, $id ) {
    // phpcs:disable WordPress.Security.NonceVerification.Missing -- checked per action below.
    $action = isset( $_POST['jj_action'] ) ? sanitize_key( wp_unslash( $_POST['jj_action'] ) ) : '';
    // phpcs:enable

    if ( 'booking' === $screen && $id && 'jj_booking' === get_post_type( $id ) ) {
        check_admin_referer( 'jj_manage_' . $action . '_' . $id );
        if ( ! current_user_can( 'edit_post', $id ) ) {
            wp_die( esc_html__( 'Not allowed.', 'jaiye-journeys' ), '', [ 'response' => 403 ] );
        }

        if ( 'note' === $action ) {
            $note = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
            jj_booking_set( $id, 'note', $note );
            wp_safe_redirect( jj_manage_url( 'booking/' . $id, [ 'done' => 'note' ] ) );
            exit;
        }

        if ( 'cancel' === $action ) {
            // To the Bin, so its places free up; it can be restored in wp-admin.
            wp_trash_post( $id );
            wp_safe_redirect( jj_manage_url( 'bookings', [ 'done' => 'cancelled' ] ) );
            exit;
        }

        if ( 'retry' === $action ) {
            $session = jj_stripe_request( 'GET', 'checkout/sessions/' . jj_booking_get( $id, 'session' ) );
            if ( ! is_wp_error( $session ) ) {
                jj_booking_record_checkout( $session );
            }
            wp_safe_redirect( jj_manage_url( 'booking/' . $id, [ 'done' => 'retry' ] ) );
            exit;
        }
    }

    if ( 'trip' === $screen && $id && 'trip' === get_post_type( $id ) && 'trip' === $action ) {
        check_admin_referer( 'jj_manage_trip_' . $id );
        if ( ! current_user_can( 'edit_post', $id ) ) {
            wp_die( esc_html__( 'Not allowed.', 'jaiye-journeys' ), '', [ 'response' => 403 ] );
        }
        jj_manage_save_trip( $id );
        wp_safe_redirect( jj_manage_url( 'trip/' . $id, [ 'done' => 'saved' ] ) );
        exit;
    }

    wp_safe_redirect( jj_manage_url() );
    exit;
}

/**
 * Save the trip quick-edit form. Only the fields on the form change; every
 * other field, and every other column of a room or chapter row, is kept.
 * Values go through the same per-type cleaning as wp-admin.
 *
 * @param int $trip_id Trip ID.
 */
function jj_manage_save_trip( $trip_id ) {
    // phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput -- nonce checked by the caller; values cleaned by type below.
    $post = wp_unslash( $_POST );

    foreach ( [ 'status', 'sales_open_at', 'sales_close_at', 'price_from', 'price_from_usd', 'deposit_gbp', 'deposit_usd' ] as $key ) {
        $field = jj_manage_field( $key );
        if ( ! $field || ! isset( $post[ $key ] ) ) {
            continue;
        }
        $value = jj_trip_sanitise_value( $post[ $key ], $field['type'] );
        if ( 'status' === $key && ! isset( $field['options'][ $value ] ) ) {
            continue;
        }
        if ( '' === $value ) {
            delete_post_meta( $trip_id, JJ_TRIP_META_PREFIX . $key );
        } else {
            update_post_meta( $trip_id, JJ_TRIP_META_PREFIX . $key, $value );
        }
    }

    // Rooms and chapters: change only the columns on the form, row by row.
    $columns = [
        'rooms'      => [ 'price', 'full_price', 'price_usd', 'full_price_usd', 'places', 'sold_out' ],
        'departures' => [ 'availability', 'status' ],
    ];
    foreach ( $columns as $key => $subs ) {
        if ( ! isset( $post[ $key ] ) || ! is_array( $post[ $key ] ) ) {
            continue;
        }
        $rows = get_post_meta( $trip_id, JJ_TRIP_META_PREFIX . $key, true );
        if ( ! is_array( $rows ) ) {
            continue;
        }
        foreach ( $post[ $key ] as $i => $submitted ) {
            if ( ! isset( $rows[ $i ] ) || ! is_array( $submitted ) ) {
                continue;
            }
            foreach ( $subs as $sub ) {
                $field = jj_manage_field( $key, $sub );
                if ( ! $field ) {
                    continue;
                }
                if ( 'checkbox' === $field['type'] ) {
                    $rows[ $i ][ $sub ] = empty( $submitted[ $sub ] ) ? '' : '1';
                } elseif ( isset( $submitted[ $sub ] ) ) {
                    $value = jj_trip_sanitise_value( $submitted[ $sub ], $field['type'] );
                    if ( 'select' === $field['type'] && '' !== $value && ! isset( $field['options'][ $value ] ) ) {
                        continue;
                    }
                    $rows[ $i ][ $sub ] = $value;
                }
            }
        }
        update_post_meta( $trip_id, JJ_TRIP_META_PREFIX . $key, $rows );
    }

    // Payment plan dates, monthly and twice a month: each whole list, blanks
    // dropped, so a date can be added or removed.
    $field = jj_manage_field( 'instalment_dates', 'date' );
    foreach ( [ 'plan_dates' => 'instalment_dates', 'twice_dates' => 'instalment_dates_twice' ] as $input => $key ) {
        if ( ! isset( $post[ $input ] ) || ! is_array( $post[ $input ] ) ) {
            continue;
        }
        $dates = [];
        foreach ( $post[ $input ] as $raw ) {
            $value = $field ? jj_trip_sanitise_value( $raw, $field['type'] ) : '';
            if ( '' !== $value ) {
                $dates[] = [ 'date' => $value ];
            }
        }
        if ( $dates ) {
            update_post_meta( $trip_id, JJ_TRIP_META_PREFIX . $key, $dates );
        } else {
            delete_post_meta( $trip_id, JJ_TRIP_META_PREFIX . $key );
        }
    }
    // phpcs:enable
}


/* ── Small helpers for the templates ───────────────────────────────── */

/**
 * £ and $ amounts side by side, e.g. "£12,400 · $4,950".
 *
 * @param array  $money From jj_manage_money().
 * @param string $key   booked|received|due.
 * @return string
 */
function jj_manage_money_line( $money, $key ) {
    $parts = [];
    foreach ( $money as $cur => $sums ) {
        $parts[] = jj_booking_money( $sums[ $key ], $cur );
    }
    return $parts ? implode( ' · ', $parts ) : jj_booking_money( 0, 'gbp' );
}

/**
 * Status pill for a trip.
 *
 * @param int $trip_id Trip ID.
 * @return string Escaped HTML.
 */
function jj_manage_trip_pill( $trip_id ) {
    $status = jj_trip_status( $trip_id );
    $field  = jj_manage_field( 'status' );
    $label  = isset( $field['options'][ $status ] ) ? $field['options'][ $status ] : $status;
    return '<span class="m-pill m-pill--' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}
