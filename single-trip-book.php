<?php
/**
 * Trip booking page: /trips/<slug>/book/.
 *
 * Guests choose their dates, room, number of travellers, currency and
 * payment plan or pay in full, then continue to Stripe Checkout (see
 * inc/trip-booking.php). Works without JavaScript for the choices;
 * js/booking.js keeps prices, the plan and the summary up to date.
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
    $plan_dates = jj_trip_plan_dates( $trip_id );
    $currencies = jj_trip_booking_currencies( $trip_id );
    $currency   = jj_booking_visitor_currency( $currencies );
    $symbols    = jj_booking_currencies();

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
    } elseif ( ! jj_stripe_key() || ! jj_stripe_webhook_secret() ) {
        $preview_note = __( 'Preview: visitors can’t see this page until the Stripe key and webhook secret are saved in Trips → Booking Settings.', 'jaiye-journeys' );
    } elseif ( ! $rooms ) {
        $preview_note = __( 'Preview: add Rooms to this trip to switch on its booking page.', 'jaiye-journeys' );
    } elseif ( 'test' === jj_stripe_mode() ) {
        $preview_note = __( 'Stripe test mode: no real money is taken. Pay with the test card 4242 4242 4242 4242.', 'jaiye-journeys' );
    }

    // First render, before JavaScript takes over the numbers.
    $first_room = isset( $rooms[ $sel_room ] ) ? $rooms[ $sel_room ] : null;
    $first_plan = $first_room ? jj_booking_quote( $trip_id, $first_room, 1, $currency, 'plan' ) : null;
    $first_full = $first_room ? jj_booking_quote( $trip_id, $first_room, 1, $currency, 'full' ) : null;
    $first      = $first_plan ? $first_plan : $first_full;
    $first_dep  = isset( $departures[ $sel_dep ] ) ? jj_trip_departure_label( $trip_id, $sel_dep, $departures[ $sel_dep ] ) : '';

    $date_labels = array_map( function ( $ts ) {
        return wp_date( 'j M Y', $ts );
    }, $plan_dates );

    // Room price lines, one per currency; CSS shows the chosen currency's.
    $room_price = function ( $room ) use ( $currencies ) {
        $out = '';
        foreach ( $currencies as $cur ) {
            $p     = $room['prices'][ $cur ];
            $plan  = jj_booking_money( $p['plan'] * 100, $cur );
            $out  .= '<span class="booking-cur booking-cur--' . esc_attr( $cur ) . '">';
            /* translators: %s: price */
            $out  .= esc_html( sprintf( __( '%s per person', 'jaiye-journeys' ), $plan ) );
            if ( $p['full'] < $p['plan'] ) {
                /* translators: %s: pay-in-full price */
                $out .= '<span class="booking-room__deposit">' . esc_html( sprintf( __( 'or %s paid in full', 'jaiye-journeys' ), jj_booking_money( $p['full'] * 100, $cur ) ) ) . '</span>';
            }
            $out .= '</span>';
        }
        return $out;
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
            $meta     = $session['metadata'];
            $cur      = isset( $meta['currency'] ) ? $meta['currency'] : 'gbp';
            $plan     = jj_booking_unpack_plan( isset( $meta['plan'] ) ? $meta['plan'] : '' );
            $is_plan  = 'plan' === $meta['payment'] && $plan;
            $name     = isset( $session['customer_details']['name'] ) ? strtok( (string) $session['customer_details']['name'], ' ' ) : '';
            $email    = isset( $session['customer_details']['email'] ) ? $session['customer_details']['email'] : '';
            ?>
            <?php
            // Hand-drawn coral underline beneath the guest's name.
            $underline = '<svg class="booking-done__underline" viewBox="0 0 300 24" preserveAspectRatio="none" aria-hidden="true" focusable="false">'
                . '<defs><filter id="jj-pencil"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" seed="3"/><feDisplacementMap in="SourceGraphic" scale="3"/></filter></defs>'
                . '<path pathLength="1" d="M6 13 C 70 8, 160 5, 294 7"/>'
                . '<path pathLength="1" d="M40 19 C 110 14, 190 12, 262 14"/>'
                . '</svg>';
            ?>
            <p class="booking-done__eyebrow"><?php esc_html_e( 'Payment received', 'jaiye-journeys' ); ?></p>
            <h2 class="booking-done__title js-booking-celebrate">
              <?php
              if ( $name ) {
                  printf(
                      /* translators: %s: guest's first name */
                      esc_html__( 'You’re all booked, %s', 'jaiye-journeys' ),
                      '<span class="booking-done__name">' . esc_html( $name ) . $underline . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput -- name escaped, SVG is fixed markup.
                  );
              } else {
                  echo esc_html__( 'You’re', 'jaiye-journeys' ) . ' <span class="booking-done__name">' . esc_html__( 'all booked', 'jaiye-journeys' ) . $underline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG is fixed markup.
              }
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
                <dt><?php echo esc_html( $is_plan ? __( 'Deposit paid', 'jaiye-journeys' ) : __( 'Paid in full', 'jaiye-journeys' ) ); ?></dt>
                <dd><?php echo esc_html( jj_booking_money( $session['amount_total'], $cur ) ); ?></dd>
              </div>
              <?php if ( $is_plan ) : ?>
                <?php foreach ( $plan as $payment ) : ?>
                  <div><dt><?php echo esc_html( wp_date( 'j M Y', $payment[0] ) ); ?></dt><dd><?php echo esc_html( jj_booking_money( $payment[1], $cur ) ); ?></dd></div>
                <?php endforeach; ?>
              <?php endif; ?>
            </dl>
            <?php if ( $is_plan ) : ?>
              <p class="booking-done__copy"><?php esc_html_e( 'Your card is saved securely with Stripe and the payments above are taken automatically. We’ll email you if one ever needs your attention.', 'jaiye-journeys' ); ?></p>
            <?php endif; ?>
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
              data-currency="<?php echo esc_attr( $currency ); ?>"
              data-dates="<?php echo esc_attr( wp_json_encode( $date_labels ) ); ?>"
              <?php foreach ( $currencies as $cur ) : ?>
                data-deposit-<?php echo esc_attr( $cur ); ?>="<?php echo (int) jj_trip_deposit( $trip_id, $cur ); ?>"
              <?php endforeach; ?>>
          <input type="hidden" name="jj_checkout" value="1">
          <input type="hidden" name="currency" value="<?php echo esc_attr( $currency ); ?>">

          <div class="booking-form__steps">

            <?php if ( count( $currencies ) > 1 ) : ?>
              <p class="booking-currency" aria-label="<?php esc_attr_e( 'Currency', 'jaiye-journeys' ); ?>">
                <?php foreach ( $currencies as $cur ) : ?>
                  <a href="<?php echo esc_url( add_query_arg( [ 'currency' => $cur, 'chapter' => $sel_dep + 1 ], $book_url ) . '#booking' ); ?>"
                     class="booking-currency__option js-currency<?php echo $cur === $currency ? ' is-active' : ''; ?>"
                     data-currency="<?php echo esc_attr( $cur ); ?>"
                     <?php echo $cur === $currency ? 'aria-current="true"' : ''; ?>><?php echo esc_html( $symbols[ $cur ] ); ?></a>
                <?php endforeach; ?>
              </p>
            <?php endif; ?>

            <?php $step = 0; ?>

            <?php /* ── Dates ── */ ?>
            <?php if ( count( $departures ) > 1 ) : ?>
              <fieldset class="booking-step">
                <legend class="booking-step__title">
                  <span class="booking-step__num"><?php echo (int) ++$step; ?></span>
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
                <span class="booking-step__num"><?php echo (int) ++$step; ?></span>
                <?php esc_html_e( 'Choose your room', 'jaiye-journeys' ); ?>
              </legend>
              <div class="booking-rooms">
                <?php foreach ( $rooms as $i => $room ) : ?>
                  <label class="booking-room<?php echo $room['sold_out'] ? ' is-sold-out' : ''; ?>">
                    <input type="radio" name="room" value="<?php echo (int) $i; ?>"
                           data-name="<?php echo esc_attr( $room['name'] ); ?>"
                           <?php foreach ( $currencies as $cur ) : ?>
                             data-plan-<?php echo esc_attr( $cur ); ?>="<?php echo (int) $room['prices'][ $cur ]['plan']; ?>"
                             data-full-<?php echo esc_attr( $cur ); ?>="<?php echo (int) $room['prices'][ $cur ]['full']; ?>"
                           <?php endforeach; ?>
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
                          <?php echo $room_price( $room ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped when built. ?>
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
                <span class="booking-step__num"><?php echo (int) ++$step; ?></span>
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
            <?php if ( $first_plan ) : ?>
              <fieldset class="booking-step">
                <legend class="booking-step__title">
                  <span class="booking-step__num"><?php echo (int) ++$step; ?></span>
                  <?php esc_html_e( 'How would you like to pay?', 'jaiye-journeys' ); ?>
                </legend>
                <div class="booking-choices">
                  <label class="booking-choice js-booking-plan">
                    <input type="radio" name="payment" value="plan" checked>
                    <span class="booking-choice__text"><?php esc_html_e( 'Payment plan', 'jaiye-journeys' ); ?></span>
                    <span class="booking-choice__note" data-note="plan">
                      <?php
                      echo esc_html( sprintf(
                          /* translators: 1: deposit, 2: number of payments */
                          _n( '%1$s deposit today, then %2$d automatic payment', '%1$s deposit today, then %2$d automatic payments', count( $plan_dates ), 'jaiye-journeys' ),
                          jj_booking_money( $first_plan['today'], $currency ),
                          count( $plan_dates )
                      ) );
                      ?>
                    </span>
                  </label>
                  <label class="booking-choice">
                    <input type="radio" name="payment" value="full">
                    <span class="booking-choice__text"><?php esc_html_e( 'Pay in full', 'jaiye-journeys' ); ?></span>
                    <span class="booking-choice__note" data-note="full">
                      <?php
                      $saving = $first_plan['total'] - $first_full['total'];
                      echo esc_html(
                          $saving > 0
                              /* translators: 1: total, 2: saving */
                              ? sprintf( __( '%1$s today, saving %2$s', 'jaiye-journeys' ), jj_booking_money( $first_full['total'], $currency ), jj_booking_money( $saving, $currency ) )
                              /* translators: %s: total */
                              : sprintf( __( '%s today, nothing more to pay', 'jaiye-journeys' ), jj_booking_money( $first_full['total'], $currency ) )
                      );
                      ?>
                    </span>
                  </label>
                </div>
              </fieldset>
            <?php else : ?>
              <input type="hidden" name="payment" value="full">
            <?php endif; ?>
          </div>

          <?php /* ── Summary ── */ ?>
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
                <span data-summary="total"><?php echo esc_html( $first ? jj_booking_money( $first['total'], $currency ) : '' ); ?></span>
              </div>
              <div class="booking-summary__row booking-summary__row--due">
                <span><?php esc_html_e( 'Due today', 'jaiye-journeys' ); ?></span>
                <span data-summary="due"><?php echo esc_html( $first ? jj_booking_money( $first['today'], $currency ) : '' ); ?></span>
              </div>
              <ol class="booking-summary__plan" data-summary="plan" <?php echo ( $first && $first['plan'] ) ? '' : 'hidden'; ?>>
                <?php if ( $first ) : ?>
                  <?php foreach ( $first['plan'] as $payment ) : ?>
                    <li><span><?php echo esc_html( wp_date( 'j M Y', $payment[0] ) ); ?></span><span><?php echo esc_html( jj_booking_money( $payment[1], $currency ) ); ?></span></li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ol>
              <p class="booking-summary__balance" data-summary="plannote" <?php echo ( $first && $first['plan'] ) ? '' : 'hidden'; ?>>
                <?php esc_html_e( 'Your card is saved securely with Stripe and charged automatically on these dates.', 'jaiye-journeys' ); ?>
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
