<?php
/**
 * Trip booking page: /trips/<slug>/book/.
 *
 * Guests choose their dates, room, number of travellers and deposit or full
 * payment, then continue to Stripe Checkout (see inc/trip-booking.php).
 * Works without JavaScript; js/booking.js keeps the summary up to date.
 * Only reachable while the trip is On sale, apart from editors previewing it.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $trip_id    = get_the_ID();
    $brand      = jj_trip_field( 'brand', $trip_id, 'jj-edit' );
    $is_btl     = ( 'btl' === $brand );
    $title      = jj_trip_volume_title( $trip_id );
    $trip_url   = get_permalink( $trip_id );
    $book_url   = jj_trip_booking_page_url( $trip_id );
    $rooms      = jj_trip_rooms( $trip_id );
    $departures = jj_trip_bookable_departures( $trip_id );
    $has_dates  = (bool) jj_trip_rows( 'departures', $trip_id );
    $terms_note = jj_trip_field( 'payment_terms', $trip_id );

    $enquiry_url = jj_trip_field( 'enquiry_url', $trip_id );
    if ( empty( $enquiry_url ) ) {
        $enquiry_url = home_url( '/contact/' );
    }

    $returning = isset( $_GET['session_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $session   = $returning ? jj_trip_completed_session( $trip_id ) : null;

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $error = isset( $_GET['booking_error'] ) ? sanitize_key( wp_unslash( $_GET['booking_error'] ) ) : '';
    $error_messages = [
        'closed'    => __( 'Booking for this journey has just closed. Enquire and we’ll help.', 'jaiye-journeys' ),
        'room'      => __( 'Please choose a room that’s still available.', 'jaiye-journeys' ),
        'departure' => __( 'Please choose your dates.', 'jaiye-journeys' ),
        'guests'    => __( 'Please choose how many travellers. Each room shows how many it takes.', 'jaiye-journeys' ),
        'terms'     => __( 'Please confirm you’ve read the booking terms.', 'jaiye-journeys' ),
        'stripe'    => __( 'We couldn’t open the secure payment page just now. Please try again, or enquire and we’ll help.', 'jaiye-journeys' ),
    ];

    // Preselect the chapter a guest clicked "Book" on, else the first open one.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $want_dep = isset( $_GET['chapter'] ) ? (int) $_GET['chapter'] - 1 : -1;
    $sel_dep  = isset( $departures[ $want_dep ] ) ? $want_dep : ( $departures ? array_key_first( $departures ) : -1 );

    $open_rooms = array_filter( $rooms, function ( $room ) {
        return ! $room['sold_out'];
    } );
    $sel_room = $open_rooms ? array_key_first( $open_rooms ) : -1;
    $max_all  = $rooms ? max( array_column( $rooms, 'max_guests' ) ) : 1;

    $can_book = $open_rooms && ( ! $has_dates || $departures );

    // Editors can preview the page before it's live; tell them why visitors can't.
    $preview_note = '';
    if ( 'open' !== jj_trip_public_status( $trip_id ) ) {
        $preview_note = __( 'Preview: visitors can’t see this page until the trip is On sale.', 'jaiye-journeys' );
    } elseif ( ! jj_stripe_key() ) {
        $preview_note = __( 'Preview: visitors can’t see this page until a Stripe key is saved in Trips → Booking Settings.', 'jaiye-journeys' );
    } elseif ( ! $rooms ) {
        $preview_note = __( 'Preview: add Rooms to this trip to switch on its booking page.', 'jaiye-journeys' );
    }

    $money = function ( $amount ) {
        return '£' . number_format_i18n( (int) $amount );
    };
    ?>

<main id="main-content" class="trip trip--<?php echo esc_attr( $brand ); ?> trip--booking">

  <header class="trip-hero trip-hero--booking">
    <div class="trip-hero__media">
      <?php if ( has_post_thumbnail() ) : ?>
        <?php
        the_post_thumbnail( 'full', [
            'class'   => 'trip-hero__img',
            'loading' => 'eager',
            'alt'     => '',
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
                ? __( 'Between the Lines · Reserve your place', 'jaiye-journeys' )
                : __( 'Reserve your place', 'jaiye-journeys' )
        );
        ?>
      </p>
      <h1 class="trip-hero__title"><?php echo esc_html( $title ); ?></h1>
    </div>

    <span class="trip-hero__sentinel" aria-hidden="true"></span>
  </header>

  <section class="booking" id="booking">
    <div class="container">

      <p class="booking__back">
        <a href="<?php echo esc_url( $trip_url ); ?>">&larr; <?php esc_html_e( 'Back to the journey', 'jaiye-journeys' ); ?></a>
      </p>

      <?php if ( $returning ) : ?>
        <!-- Back from Stripe -->
        <div class="booking-done">
          <?php if ( $session ) : ?>
            <?php
            $meta  = $session['metadata'];
            $name  = isset( $session['customer_details']['name'] ) ? strtok( (string) $session['customer_details']['name'], ' ' ) : '';
            $email = isset( $session['customer_details']['email'] ) ? $session['customer_details']['email'] : '';
            ?>
            <p class="booking-done__eyebrow"><?php esc_html_e( 'Payment received', 'jaiye-journeys' ); ?></p>
            <h2 class="booking-done__title">
              <?php
              echo esc_html(
                  $name
                      /* translators: %s: guest's first name */
                      ? sprintf( __( 'You’re booked, %s', 'jaiye-journeys' ), $name )
                      : __( 'You’re booked', 'jaiye-journeys' )
              );
              ?>
            </h2>
            <?php if ( $email ) : ?>
              <p class="booking-done__copy">
                <?php
                /* translators: %s: email address */
                echo esc_html( sprintf( __( 'Your receipt is on its way to %s. We’ll be in touch personally with everything you need for the journey ahead.', 'jaiye-journeys' ), $email ) );
                ?>
              </p>
            <?php endif; ?>
            <dl class="booking-done__details">
              <div><dt><?php esc_html_e( 'Journey', 'jaiye-journeys' ); ?></dt><dd><?php echo esc_html( $title ); ?></dd></div>
              <?php if ( ! empty( $meta['departure'] ) ) : ?>
                <div><dt><?php esc_html_e( 'Dates', 'jaiye-journeys' ); ?></dt><dd><?php echo esc_html( $meta['departure'] ); ?></dd></div>
              <?php endif; ?>
              <div><dt><?php esc_html_e( 'Room', 'jaiye-journeys' ); ?></dt><dd><?php echo esc_html( $meta['room'] ); ?></dd></div>
              <div><dt><?php esc_html_e( 'Travellers', 'jaiye-journeys' ); ?></dt><dd><?php echo esc_html( $meta['travellers'] ); ?></dd></div>
              <div>
                <dt><?php echo esc_html( 'deposit' === $meta['payment'] ? __( 'Deposit paid', 'jaiye-journeys' ) : __( 'Paid', 'jaiye-journeys' ) ); ?></dt>
                <dd><?php echo esc_html( $money( $session['amount_total'] / 100 ) ); ?></dd>
              </div>
            </dl>
          <?php else : ?>
            <h2 class="booking-done__title"><?php esc_html_e( 'Thank you', 'jaiye-journeys' ); ?></h2>
            <p class="booking-done__copy">
              <?php esc_html_e( 'If your payment went through, Stripe will email your receipt shortly and we’ll be in touch. Any questions at all, just get in touch.', 'jaiye-journeys' ); ?>
            </p>
          <?php endif; ?>
          <a href="<?php echo esc_url( $trip_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Back to the journey', 'jaiye-journeys' ); ?></a>
        </div>

      <?php elseif ( ! $can_book ) : ?>
        <div class="booking-done">
          <h2 class="booking-done__title"><?php esc_html_e( 'Fully booked', 'jaiye-journeys' ); ?></h2>
          <p class="booking-done__copy"><?php esc_html_e( 'Every place on this journey has been taken. Enquire and we’ll let you know if one opens up.', 'jaiye-journeys' ); ?></p>
          <a href="<?php echo esc_url( $enquiry_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Enquire', 'jaiye-journeys' ); ?></a>
        </div>

      <?php else : ?>

        <?php if ( $preview_note ) : ?>
          <p class="booking__notice booking__notice--preview"><?php echo esc_html( $preview_note ); ?></p>
        <?php endif; ?>

        <?php if ( isset( $error_messages[ $error ] ) ) : ?>
          <p class="booking__notice" role="alert"><?php echo esc_html( $error_messages[ $error ] ); ?></p>
        <?php endif; ?>

        <form class="booking-form js-booking" method="post" action="<?php echo esc_url( $book_url ); ?>#booking"
              data-rate="<?php echo esc_attr( jj_fx_gbp_usd_rate() ); ?>">
          <input type="hidden" name="jj_checkout" value="1">

          <div class="booking-form__steps">

            <?php /* ── Dates ── */ ?>
            <?php if ( count( $departures ) > 1 ) : ?>
              <fieldset class="booking-step">
                <legend class="booking-step__title">
                  <span class="booking-step__num">1</span>
                  <?php echo esc_html( $is_btl ? __( 'Choose your chapter', 'jaiye-journeys' ) : __( 'Choose your dates', 'jaiye-journeys' ) ); ?>
                </legend>
                <div class="booking-choices">
                  <?php foreach ( $departures as $i => $departure ) : ?>
                    <?php $label = jj_trip_departure_label( $trip_id, $i, $departure ); ?>
                    <label class="booking-choice">
                      <input type="radio" name="departure" value="<?php echo (int) $i; ?>" data-label="<?php echo esc_attr( $label ); ?>" <?php checked( $sel_dep, $i ); ?> required>
                      <span class="booking-choice__text"><?php echo esc_html( $label ); ?></span>
                      <?php if ( ! empty( $departure['availability'] ) ) : ?>
                        <span class="booking-choice__note"><?php echo esc_html( $departure['availability'] ); ?></span>
                      <?php endif; ?>
                    </label>
                  <?php endforeach; ?>
                </div>
              </fieldset>
            <?php else : ?>
              <input type="hidden" name="departure" value="<?php echo (int) $sel_dep; ?>">
            <?php endif; ?>

            <?php /* ── Room ── */ ?>
            <fieldset class="booking-step">
              <legend class="booking-step__title">
                <span class="booking-step__num"><?php echo count( $departures ) > 1 ? 2 : 1; ?></span>
                <?php esc_html_e( 'Choose your room', 'jaiye-journeys' ); ?>
              </legend>
              <div class="booking-rooms">
                <?php foreach ( $rooms as $i => $room ) : ?>
                  <label class="booking-room<?php echo $room['sold_out'] ? ' is-sold-out' : ''; ?>">
                    <input type="radio" name="room" value="<?php echo (int) $i; ?>"
                           data-name="<?php echo esc_attr( $room['name'] ); ?>"
                           data-price="<?php echo (int) $room['price']; ?>"
                           data-deposit="<?php echo (int) $room['deposit']; ?>"
                           data-max="<?php echo (int) $room['max_guests']; ?>"
                           <?php checked( $sel_room, $i ); ?>
                           <?php disabled( $room['sold_out'] ); ?>
                           required>
                    <?php if ( $room['image'] ) : ?>
                      <span class="booking-room__media">
                        <?php echo wp_get_attachment_image( $room['image'], 'medium_large', false, [ 'class' => 'booking-room__img', 'loading' => 'lazy', 'alt' => '' ] ); ?>
                      </span>
                    <?php endif; ?>
                    <span class="booking-room__body">
                      <span class="booking-room__name"><?php echo esc_html( $room['name'] ); ?></span>
                      <?php if ( $room['description'] ) : ?>
                        <span class="booking-room__desc"><?php echo esc_html( $room['description'] ); ?></span>
                      <?php endif; ?>
                      <span class="booking-room__price">
                        <?php if ( $room['sold_out'] ) : ?>
                          <?php esc_html_e( 'Sold out', 'jaiye-journeys' ); ?>
                        <?php else : ?>
                          <?php
                          /* translators: %s: price */
                          echo esc_html( sprintf( __( '%s per person', 'jaiye-journeys' ), $money( $room['price'] ) ) );
                          ?>
                          <?php if ( $room['deposit'] ) : ?>
                            <span class="booking-room__deposit">
                              <?php
                              /* translators: %s: deposit */
                              echo esc_html( sprintf( __( '%s deposit', 'jaiye-journeys' ), $money( $room['deposit'] ) ) );
                              ?>
                            </span>
                          <?php endif; ?>
                        <?php endif; ?>
                      </span>
                    </span>
                  </label>
                <?php endforeach; ?>
              </div>
            </fieldset>

            <?php /* ── Travellers ── */ ?>
            <fieldset class="booking-step">
              <legend class="booking-step__title">
                <span class="booking-step__num"><?php echo count( $departures ) > 1 ? 3 : 2; ?></span>
                <?php esc_html_e( 'Travellers', 'jaiye-journeys' ); ?>
              </legend>
              <div class="booking-pills">
                <?php for ( $n = 1; $n <= $max_all; $n++ ) : ?>
                  <label class="booking-pill">
                    <input type="radio" name="guests" value="<?php echo (int) $n; ?>" <?php checked( 1, $n ); ?> required>
                    <span><?php echo (int) $n; ?></span>
                  </label>
                <?php endfor; ?>
              </div>
              <p class="booking-step__hint js-booking-guest-hint"><?php esc_html_e( 'Prices are per person.', 'jaiye-journeys' ); ?></p>
            </fieldset>

            <?php /* ── Payment ── */ ?>
            <?php if ( array_filter( array_column( $rooms, 'deposit' ) ) ) : ?>
              <fieldset class="booking-step js-booking-payment">
                <legend class="booking-step__title">
                  <span class="booking-step__num"><?php echo count( $departures ) > 1 ? 4 : 3; ?></span>
                  <?php esc_html_e( 'How would you like to pay?', 'jaiye-journeys' ); ?>
                </legend>
                <div class="booking-choices">
                  <label class="booking-choice js-booking-deposit">
                    <input type="radio" name="payment" value="deposit" checked>
                    <span class="booking-choice__text"><?php esc_html_e( 'Pay a deposit', 'jaiye-journeys' ); ?></span>
                    <span class="booking-choice__note"><?php esc_html_e( 'Secure your place today and pay the balance later', 'jaiye-journeys' ); ?></span>
                  </label>
                  <label class="booking-choice">
                    <input type="radio" name="payment" value="full">
                    <span class="booking-choice__text"><?php esc_html_e( 'Pay in full', 'jaiye-journeys' ); ?></span>
                    <span class="booking-choice__note"><?php esc_html_e( 'Settle everything now', 'jaiye-journeys' ); ?></span>
                  </label>
                </div>
              </fieldset>
            <?php else : ?>
              <input type="hidden" name="payment" value="full">
            <?php endif; ?>
          </div>

          <?php /* ── Summary ── */ ?>
          <?php
          $first_room = isset( $rooms[ $sel_room ] ) ? $rooms[ $sel_room ] : null;
          $first_dep  = isset( $departures[ $sel_dep ] ) ? jj_trip_departure_label( $trip_id, $sel_dep, $departures[ $sel_dep ] ) : '';
          $first_now  = $first_room ? ( $first_room['deposit'] ? $first_room['deposit'] : $first_room['price'] ) : 0;
          ?>
          <aside class="booking-summary" aria-label="<?php esc_attr_e( 'Your booking', 'jaiye-journeys' ); ?>">
            <h2 class="booking-summary__title"><?php esc_html_e( 'Your booking', 'jaiye-journeys' ); ?></h2>
            <dl class="booking-summary__list">
              <div><dt><?php esc_html_e( 'Journey', 'jaiye-journeys' ); ?></dt><dd><?php echo esc_html( $title ); ?></dd></div>
              <?php if ( $first_dep ) : ?>
                <div><dt><?php esc_html_e( 'Dates', 'jaiye-journeys' ); ?></dt><dd data-summary="departure"><?php echo esc_html( $first_dep ); ?></dd></div>
              <?php endif; ?>
              <div><dt><?php esc_html_e( 'Room', 'jaiye-journeys' ); ?></dt><dd data-summary="room"><?php echo esc_html( $first_room ? $first_room['name'] : '' ); ?></dd></div>
              <div><dt><?php esc_html_e( 'Travellers', 'jaiye-journeys' ); ?></dt><dd data-summary="guests">1</dd></div>
            </dl>

            <div class="booking-summary__totals">
              <div class="booking-summary__row">
                <span><?php esc_html_e( 'Journey total', 'jaiye-journeys' ); ?></span>
                <span data-summary="total"><?php echo esc_html( $first_room ? $money( $first_room['price'] ) : '' ); ?></span>
              </div>
              <div class="booking-summary__row booking-summary__row--due">
                <span><?php esc_html_e( 'Due today', 'jaiye-journeys' ); ?></span>
                <span data-summary="due"><?php echo esc_html( $money( $first_now ) ); ?></span>
              </div>
              <p class="booking-summary__fx" data-summary="fx" hidden></p>
              <p class="booking-summary__balance" data-summary="balance" <?php echo ( $first_room && $first_room['deposit'] ) ? '' : 'hidden'; ?>>
                <?php
                if ( $first_room && $first_room['deposit'] ) {
                    /* translators: %s: balance amount */
                    echo esc_html( sprintf( __( 'Balance of %s due later', 'jaiye-journeys' ), $money( $first_room['price'] - $first_room['deposit'] ) ) );
                }
                ?>
              </p>
            </div>

            <?php if ( $terms_note ) : ?>
              <p class="booking-summary__terms"><?php echo esc_html( $terms_note ); ?></p>
            <?php endif; ?>

            <label class="booking-summary__agree">
              <input type="checkbox" name="terms" value="1" required>
              <span>
                <?php
                printf(
                    /* translators: %s: link to the booking terms */
                    esc_html__( 'I’ve read and agree to the %s.', 'jaiye-journeys' ),
                    '<a href="' . esc_url( home_url( '/terms/' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'booking terms', 'jaiye-journeys' ) . '</a>'
                );
                ?>
              </span>
            </label>

            <button type="submit" class="btn btn--accent booking-summary__submit"><?php esc_html_e( 'Continue to secure payment', 'jaiye-journeys' ); ?></button>

            <p class="booking-summary__secure">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
              <?php esc_html_e( 'You’ll pay on Stripe’s secure checkout.', 'jaiye-journeys' ); ?>
            </p>
          </aside>
        </form>

      <?php endif; ?>
    </div>
  </section>

</main>

<?php
endwhile;

get_footer();
