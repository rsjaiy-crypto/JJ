<?php
/**
 * Trip booking page and Stripe Checkout.
 *
 * Each on-sale trip with Rooms gets /trips/<slug>/book/: guests pick a
 * departure, a room, how many travellers and deposit or full payment, and
 * the site opens a Stripe Checkout for exactly that. Prices are always read
 * from the trip here, never from the form, so they can't be altered in the
 * browser.
 *
 * The Stripe key is entered in Trips → Booking Settings and lives in the
 * database, never in this (public) repository. A JJ_STRIPE_SECRET_KEY
 * constant in wp-config.php overrides it.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

define( 'JJ_STRIPE_KEY_OPTION', 'jj_stripe_secret_key' );


/* ── Data ──────────────────────────────────────────────────────────── */

/**
 * A trip's rooms, keyed by their row index so links stay stable when a
 * room is marked sold out. Rooms with no name or price are skipped.
 *
 * @param int $post_id Trip ID.
 * @return array[]
 */
function jj_trip_rooms( $post_id ) {
    $rooms = [];
    foreach ( jj_trip_rows( 'rooms', $post_id ) as $i => $room ) {
        if ( empty( $room['name'] ) || empty( $room['price'] ) || (int) $room['price'] <= 0 ) {
            continue;
        }

        $price   = (int) $room['price'];
        $deposit = isset( $room['deposit'] ) ? (int) $room['deposit'] : 0;

        $rooms[ $i ] = [
            'name'        => $room['name'],
            'description' => isset( $room['description'] ) ? $room['description'] : '',
            'image'       => isset( $room['image'] ) ? absint( $room['image'] ) : 0,
            'price'       => $price,
            'deposit'     => ( $deposit > 0 && $deposit < $price ) ? $deposit : 0,
            'max_guests'  => max( 1, min( 4, isset( $room['max_guests'] ) ? (int) $room['max_guests'] : 2 ) ),
            'sold_out'    => ! empty( $room['sold_out'] ),
        ];
    }
    return $rooms;
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
        if ( isset( $departure['status'] ) && 'sold-out' === $departure['status'] ) {
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
 * The Stripe secret (or restricted) key, or '' if none is set.
 *
 * @return string
 */
function jj_stripe_key() {
    if ( defined( 'JJ_STRIPE_SECRET_KEY' ) && JJ_STRIPE_SECRET_KEY ) {
        return JJ_STRIPE_SECRET_KEY;
    }
    return (string) get_option( JJ_STRIPE_KEY_OPTION, '' );
}

/**
 * Is the booking page switched on for this trip? It needs rooms and a
 * Stripe key; until then the trip keeps its old Book Now link.
 *
 * @param int $post_id Trip ID.
 * @return bool
 */
function jj_trip_booking_ready( $post_id ) {
    return jj_stripe_key() && jj_trip_rooms( $post_id );
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
 * (not on sale, or no rooms/Stripe key yet). Editors can still preview it.
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


/* ── Stripe ────────────────────────────────────────────────────────── */

/**
 * Call the Stripe API.
 *
 * @param string $method GET or POST.
 * @param string $path   e.g. checkout/sessions.
 * @param array  $body   Form parameters.
 * @return array|WP_Error Decoded response.
 */
function jj_stripe_request( $method, $path, $body = [] ) {
    $key = jj_stripe_key();
    if ( ! $key ) {
        return new WP_Error( 'jj_stripe_no_key', 'No Stripe key set.' );
    }

    $response = wp_remote_request( 'https://api.stripe.com/v1/' . $path, [
        'method'  => $method,
        'timeout' => 20,
        'headers' => [ 'Authorization' => 'Bearer ' . $key ],
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

    if ( 'open' !== jj_trip_public_status( $trip_id ) || ! jj_stripe_key() ) {
        $fail( 'closed' );
    }

    // phpcs:disable WordPress.Security.NonceVerification.Missing -- see docblock.
    $room_i  = isset( $_POST['room'] ) ? (int) $_POST['room'] : -1;
    $dep_i   = isset( $_POST['departure'] ) ? (int) $_POST['departure'] : -1;
    $guests  = isset( $_POST['guests'] ) ? (int) $_POST['guests'] : 0;
    $payment = isset( $_POST['payment'] ) ? sanitize_key( wp_unslash( $_POST['payment'] ) ) : 'full';
    $terms   = ! empty( $_POST['terms'] );
    // phpcs:enable

    $rooms      = jj_trip_rooms( $trip_id );
    $departures = jj_trip_bookable_departures( $trip_id );

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

    $is_deposit = ( 'deposit' === $payment && $room['deposit'] );
    $unit       = $is_deposit ? $room['deposit'] : $room['price'];
    $title      = jj_trip_volume_title( $trip_id );
    $dep_label  = isset( $departures[ $dep_i ] ) ? jj_trip_departure_label( $trip_id, $dep_i, $departures[ $dep_i ] ) : '';

    $description = array_filter( [
        $dep_label,
        $is_deposit ? __( 'Deposit per person', 'jaiye-journeys' ) : __( 'Paid in full, per person', 'jaiye-journeys' ),
    ] );

    $metadata = [
        'trip'       => $title,
        'trip_id'    => $trip_id,
        'departure'  => $dep_label,
        'room'       => $room['name'],
        'travellers' => $guests,
        'payment'    => $is_deposit ? 'deposit' : 'full',
    ];

    $body = [
        'mode'                       => 'payment',
        'submit_type'                => 'book',
        'customer_creation'          => 'always',
        'billing_address_collection' => 'required',
        'phone_number_collection'    => [ 'enabled' => 'true' ],
        'line_items'                 => [
            [
                'quantity'   => $guests,
                'price_data' => [
                    'currency'     => 'gbp',
                    'unit_amount'  => $unit * 100,
                    'product_data' => [
                        'name'        => $title . ' — ' . $room['name'],
                        'description' => implode( ' · ', $description ),
                    ],
                ],
            ],
        ],
        'metadata'                   => $metadata,
        'payment_intent_data'        => [
            'description' => $title . ' — ' . $room['name'] . ( $dep_label ? ' — ' . $dep_label : '' ),
            'metadata'    => $metadata,
        ],
        // {CHECKOUT_SESSION_ID} is filled in by Stripe, so it's appended raw.
        'success_url'                => $back . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'                 => add_query_arg( 'chapter', $dep_i + 1, $back ),
    ];

    if ( $is_deposit ) {
        $balance = ( $room['price'] - $room['deposit'] ) * $guests;
        $body['custom_text'] = [
            'submit' => [
                'message' => sprintf(
                    /* translators: %s: balance amount */
                    __( 'Today you pay the deposit. The balance of £%s is due as set out in our booking terms.', 'jaiye-journeys' ),
                    number_format( $balance )
                ),
            ],
        ];
    }

    $session = jj_stripe_request( 'POST', 'checkout/sessions', $body );

    if ( is_wp_error( $session ) || empty( $session['url'] ) || 0 !== strpos( $session['url'], 'https://checkout.stripe.com/' ) ) {
        error_log( 'Jaiye booking: Stripe checkout failed for trip ' . $trip_id . ': ' . ( is_wp_error( $session ) ? $session->get_error_message() : 'no URL' ) ); // phpcs:ignore
        $fail( 'stripe' );
    }

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
    $id = isset( $_GET['session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['session_id'] ) ) : '';
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
}
add_action( 'admin_init', 'jj_booking_settings_register' );

/**
 * Keep the saved key when the box is left blank (it's never shown again);
 * clear it only when "Remove" is ticked.
 *
 * @param string $value Submitted key.
 * @return string
 */
function jj_booking_sanitise_key( $value ) {
    $old = (string) get_option( JJ_STRIPE_KEY_OPTION, '' );

    if ( ! empty( $_POST['jj_stripe_key_remove'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- options.php checks the settings nonce.
        return '';
    }

    $value = trim( (string) $value );
    if ( '' === $value ) {
        return $old;
    }

    if ( ! preg_match( '/^(sk|rk)_(test|live)_[A-Za-z0-9]+$/', $value ) ) {
        add_settings_error( JJ_STRIPE_KEY_OPTION, 'jj_bad_key', __( 'That doesn’t look like a Stripe secret or restricted key (it should start with rk_live_ or sk_live_). Nothing was changed.', 'jaiye-journeys' ) );
        return $old;
    }

    return $value;
}

function jj_booking_settings_page() {
    $key      = jj_stripe_key();
    $constant = defined( 'JJ_STRIPE_SECRET_KEY' ) && JJ_STRIPE_SECRET_KEY;
    ?>
    <div class="wrap">
      <h1><?php esc_html_e( 'Booking Settings', 'jaiye-journeys' ); ?></h1>
      <p><?php esc_html_e( 'Trips with Rooms get a booking page, which sends guests to Stripe to pay. It switches on once a Stripe key is saved here.', 'jaiye-journeys' ); ?></p>

      <form method="post" action="options.php">
        <?php settings_fields( 'jj_booking' ); ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="jj-stripe-key"><?php esc_html_e( 'Stripe key', 'jaiye-journeys' ); ?></label></th>
            <td>
              <?php if ( $key ) : ?>
                <p>
                  <strong><?php echo esc_html( 0 === strpos( $key, substr( $key, 0, 3 ) . 'test_' ) ? __( 'Test mode', 'jaiye-journeys' ) : __( 'Live', 'jaiye-journeys' ) ); ?></strong>
                  · <?php echo esc_html( sprintf( /* translators: %s: last four characters */ __( 'key ending …%s is saved', 'jaiye-journeys' ), substr( $key, -4 ) ) ); ?>
                  <?php if ( $constant ) : ?>
                    <?php esc_html_e( '(set in wp-config.php)', 'jaiye-journeys' ); ?>
                  <?php endif; ?>
                </p>
              <?php endif; ?>
              <?php if ( ! $constant ) : ?>
                <input type="password" id="jj-stripe-key" name="<?php echo esc_attr( JJ_STRIPE_KEY_OPTION ); ?>" value="" class="regular-text" autocomplete="off" placeholder="<?php echo esc_attr( $key ? __( 'Paste a new key to replace it', 'jaiye-journeys' ) : 'rk_live_…' ); ?>">
                <?php if ( $key ) : ?>
                  <p><label><input type="checkbox" name="jj_stripe_key_remove" value="1"> <?php esc_html_e( 'Remove the saved key (turns off booking pages)', 'jaiye-journeys' ); ?></label></p>
                <?php endif; ?>
                <p class="description">
                  <?php esc_html_e( 'In Stripe: Developers → API keys → Create restricted key. Give it "Checkout Sessions: Write" and nothing else, then paste it here. A key starting rk_test_ lets you try bookings with test cards first.', 'jaiye-journeys' ); ?>
                </p>
              <?php endif; ?>
            </td>
          </tr>
        </table>
        <?php if ( ! $constant ) : ?>
          <?php submit_button(); ?>
        <?php endif; ?>
      </form>
    </div>
    <?php
}
