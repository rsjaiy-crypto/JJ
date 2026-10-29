<?php
/**
 * Quick edits for one trip: sales status and times, prices, deposits,
 * places per room, chapter labels and payment plan dates. Everything else
 * (itinerary, photos, FAQs) stays in wp-admin.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

if ( ! $id || 'trip' !== get_post_type( $id ) ) {
    echo '<p class="m-muted">' . esc_html__( 'That trip isn’t here.', 'jaiye-journeys' ) . '</p>';
    return;
}

$meta = function ( $key ) use ( $id ) {
    return get_post_meta( $id, JJ_TRIP_META_PREFIX . $key, true );
};
$rows = function ( $key ) use ( $meta ) {
    $value = $meta( $key );
    return is_array( $value ) ? $value : [];
};
$val = function ( $row, $key ) {
    return isset( $row[ $key ] ) ? (string) $row[ $key ] : '';
};
// Stored "Y-m-d H:i" → datetime-local's "Y-m-d\TH:i".
$local = function ( $value ) {
    return $value ? str_replace( ' ', 'T', (string) $value ) : '';
};

$status_field = jj_manage_field( 'status' );
$dep_field    = jj_manage_field( 'departures', 'status' );
$stored       = $meta( 'status' );
$stored       = $stored ? $stored : $status_field['default'];
$effective    = jj_trip_status( $id );
$rooms        = $rows( 'rooms' );
$departures   = $rows( 'departures' );
$plan_dates   = $rows( 'instalment_dates' );
$twice_dates  = $rows( 'instalment_dates_twice' );
$is_btl       = 'btl' === jj_trip_field( 'brand', $id, 'jj-edit' );
?>
<p class="m-back"><a href="<?php echo esc_url( jj_manage_url( 'trips' ) ); ?>">← <?php esc_html_e( 'All trips', 'jaiye-journeys' ); ?></a></p>
<?php jj_manage_notice(); ?>

<header class="m-head">
  <h1 class="m-title"><?php echo esc_html( jj_trip_volume_title( $id ) ); ?></h1>
  <p><?php echo jj_manage_trip_pill( $id ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped when built. ?></p>
</header>

<div class="m-actions">
  <a class="m-btn m-btn--quiet" href="<?php echo esc_url( get_permalink( $id ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View trip page', 'jaiye-journeys' ); ?></a>
  <a class="m-btn m-btn--quiet" href="<?php echo esc_url( jj_manage_url( 'bookings', [ 'trip' => $id ] ) ); ?>"><?php esc_html_e( 'Bookings', 'jaiye-journeys' ); ?></a>
</div>

<?php if ( jj_trip_rooms( $id ) ) : ?>
  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'Places sold', 'jaiye-journeys' ); ?></h2>
    <?php jj_manage_places_html( $id ); ?>
  </section>
<?php endif; ?>

<form method="post" class="m-form">
  <?php wp_nonce_field( 'jj_manage_trip_' . $id ); ?>
  <input type="hidden" name="jj_action" value="trip">

  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'Sales status', 'jaiye-journeys' ); ?></h2>
    <label class="m-field">
      <span><?php esc_html_e( 'Status', 'jaiye-journeys' ); ?></span>
      <select class="m-input" name="status">
        <?php foreach ( $status_field['options'] as $key => $label ) : ?>
          <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $stored, $key ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php if ( $effective !== $stored && isset( $status_field['options'][ $effective ] ) ) : ?>
      <p class="m-hint">
        <?php
        /* translators: %s: status */
        echo esc_html( sprintf( __( 'Visitors see %s right now, because of the times below.', 'jaiye-journeys' ), $status_field['options'][ $effective ] ) );
        ?>
      </p>
    <?php endif; ?>
    <label class="m-field">
      <span><?php esc_html_e( 'Go on sale at (optional)', 'jaiye-journeys' ); ?></span>
      <input class="m-input" type="datetime-local" name="sales_open_at" value="<?php echo esc_attr( $local( $meta( 'sales_open_at' ) ) ); ?>">
    </label>
    <label class="m-field">
      <span><?php esc_html_e( 'Sold out from (optional)', 'jaiye-journeys' ); ?></span>
      <input class="m-input" type="datetime-local" name="sales_close_at" value="<?php echo esc_attr( $local( $meta( 'sales_close_at' ) ) ); ?>">
    </label>
    <p class="m-hint"><?php esc_html_e( 'UK time. Clear a box to switch by hand instead.', 'jaiye-journeys' ); ?></p>
  </section>

  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'From price and deposits', 'jaiye-journeys' ); ?></h2>
    <div class="m-grid">
      <label class="m-field"><span><?php esc_html_e( 'From price (£)', 'jaiye-journeys' ); ?></span>
        <input class="m-input" type="text" inputmode="decimal" name="price_from" value="<?php echo esc_attr( $meta( 'price_from' ) ); ?>"></label>
      <label class="m-field"><span><?php esc_html_e( 'From price ($)', 'jaiye-journeys' ); ?></span>
        <input class="m-input" type="text" inputmode="decimal" name="price_from_usd" value="<?php echo esc_attr( $meta( 'price_from_usd' ) ); ?>"></label>
      <label class="m-field"><span><?php esc_html_e( 'Deposit per person (£)', 'jaiye-journeys' ); ?></span>
        <input class="m-input" type="text" inputmode="decimal" name="deposit_gbp" value="<?php echo esc_attr( $meta( 'deposit_gbp' ) ); ?>" placeholder="500"></label>
      <label class="m-field"><span><?php esc_html_e( 'Deposit per person ($)', 'jaiye-journeys' ); ?></span>
        <input class="m-input" type="text" inputmode="decimal" name="deposit_usd" value="<?php echo esc_attr( $meta( 'deposit_usd' ) ); ?>" placeholder="700"></label>
    </div>
  </section>

  <?php if ( $rooms ) : ?>
    <section class="m-card">
      <h2 class="m-card__title"><?php esc_html_e( 'Rooms', 'jaiye-journeys' ); ?></h2>
      <p class="m-hint"><?php esc_html_e( 'Prices are per person. Places per chapter counts guests (4 shared rooms is 8 places); blank means no limit.', 'jaiye-journeys' ); ?></p>
      <?php foreach ( $rooms as $i => $room ) : ?>
        <fieldset class="m-group">
          <legend><?php echo esc_html( $val( $room, 'name' ) ? $val( $room, 'name' ) : sprintf( /* translators: %d: room number */ __( 'Room %d', 'jaiye-journeys' ), $i + 1 ) ); ?></legend>
          <div class="m-grid">
            <label class="m-field"><span><?php esc_html_e( 'Plan price (£)', 'jaiye-journeys' ); ?></span>
              <input class="m-input" type="text" inputmode="decimal" name="rooms[<?php echo (int) $i; ?>][price]" value="<?php echo esc_attr( $val( $room, 'price' ) ); ?>"></label>
            <label class="m-field"><span><?php esc_html_e( 'Pay-in-full price (£)', 'jaiye-journeys' ); ?></span>
              <input class="m-input" type="text" inputmode="decimal" name="rooms[<?php echo (int) $i; ?>][full_price]" value="<?php echo esc_attr( $val( $room, 'full_price' ) ); ?>"></label>
            <label class="m-field"><span><?php esc_html_e( 'Plan price ($)', 'jaiye-journeys' ); ?></span>
              <input class="m-input" type="text" inputmode="decimal" name="rooms[<?php echo (int) $i; ?>][price_usd]" value="<?php echo esc_attr( $val( $room, 'price_usd' ) ); ?>"></label>
            <label class="m-field"><span><?php esc_html_e( 'Pay-in-full price ($)', 'jaiye-journeys' ); ?></span>
              <input class="m-input" type="text" inputmode="decimal" name="rooms[<?php echo (int) $i; ?>][full_price_usd]" value="<?php echo esc_attr( $val( $room, 'full_price_usd' ) ); ?>"></label>
            <label class="m-field"><span><?php esc_html_e( 'Places per chapter', 'jaiye-journeys' ); ?></span>
              <input class="m-input" type="number" min="0" inputmode="numeric" name="rooms[<?php echo (int) $i; ?>][places]" value="<?php echo esc_attr( $val( $room, 'places' ) ); ?>"></label>
            <label class="m-check m-check--field">
              <input type="hidden" name="rooms[<?php echo (int) $i; ?>][sold_out]" value="">
              <input type="checkbox" name="rooms[<?php echo (int) $i; ?>][sold_out]" value="1" <?php checked( '' !== $val( $room, 'sold_out' ) ); ?>>
              <?php esc_html_e( 'Sold out (every chapter)', 'jaiye-journeys' ); ?>
            </label>
          </div>
        </fieldset>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>

  <?php if ( $departures ) : ?>
    <section class="m-card">
      <h2 class="m-card__title"><?php echo $is_btl ? esc_html__( 'Chapters', 'jaiye-journeys' ) : esc_html__( 'Departures', 'jaiye-journeys' ); ?></h2>
      <p class="m-hint"><?php esc_html_e( 'The label is the little pill beside the dates on the trip page, e.g. "4 places left".', 'jaiye-journeys' ); ?></p>
      <?php foreach ( $departures as $i => $departure ) : ?>
        <fieldset class="m-group">
          <legend><?php echo esc_html( jj_trip_departure_label( $id, $i, $departure ) ); ?></legend>
          <div class="m-grid">
            <label class="m-field"><span><?php esc_html_e( 'Availability label', 'jaiye-journeys' ); ?></span>
              <input class="m-input" type="text" name="departures[<?php echo (int) $i; ?>][availability]" value="<?php echo esc_attr( $val( $departure, 'availability' ) ); ?>" placeholder="<?php esc_attr_e( 'Places available', 'jaiye-journeys' ); ?>"></label>
            <label class="m-field"><span><?php esc_html_e( 'Status', 'jaiye-journeys' ); ?></span>
              <select class="m-input" name="departures[<?php echo (int) $i; ?>][status]">
                <option value=""><?php esc_html_e( '(none)', 'jaiye-journeys' ); ?></option>
                <?php foreach ( $dep_field['options'] as $key => $label ) : ?>
                  <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $val( $departure, 'status' ), $key ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
              </select></label>
          </div>
        </fieldset>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>

  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'Payment plan dates (monthly)', 'jaiye-journeys' ); ?></h2>
    <p class="m-hint"><?php esc_html_e( 'When the balance is collected after the deposit. New guests pay in equal parts on the dates still ahead. Changing these doesn’t change plans already booked. Clear a date to remove it.', 'jaiye-journeys' ); ?></p>
    <input type="hidden" name="plan_dates[]" value="">
    <div class="m-dates js-dates">
      <?php foreach ( $plan_dates as $row ) : ?>
        <input class="m-input" type="date" name="plan_dates[]" value="<?php echo esc_attr( $val( $row, 'date' ) ); ?>" aria-label="<?php esc_attr_e( 'Payment date', 'jaiye-journeys' ); ?>">
      <?php endforeach; ?>
      <input class="m-input" type="date" name="plan_dates[]" value="" aria-label="<?php esc_attr_e( 'Add a payment date', 'jaiye-journeys' ); ?>">
    </div>
    <button type="button" class="m-btn m-btn--quiet js-add-date"><?php esc_html_e( 'Add another date', 'jaiye-journeys' ); ?></button>
  </section>

  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'Twice-a-month plan dates', 'jaiye-journeys' ); ?></h2>
    <p class="m-hint"><?php esc_html_e( 'Optional. Add dates here (e.g. the 1st and 15th of each month) to also offer "Twice a month" on the booking page. Same deposit, then equal payments on the dates still ahead. Leave empty for the monthly plan only.', 'jaiye-journeys' ); ?></p>
    <input type="hidden" name="twice_dates[]" value="">
    <div class="m-dates js-dates">
      <?php foreach ( $twice_dates as $row ) : ?>
        <input class="m-input" type="date" name="twice_dates[]" value="<?php echo esc_attr( $val( $row, 'date' ) ); ?>" aria-label="<?php esc_attr_e( 'Payment date', 'jaiye-journeys' ); ?>">
      <?php endforeach; ?>
      <input class="m-input" type="date" name="twice_dates[]" value="" aria-label="<?php esc_attr_e( 'Add a payment date', 'jaiye-journeys' ); ?>">
    </div>
    <button type="button" class="m-btn m-btn--quiet js-add-date"><?php esc_html_e( 'Add another date', 'jaiye-journeys' ); ?></button>
  </section>

  <div class="m-save">
    <button class="m-btn m-btn--wide" type="submit"><?php esc_html_e( 'Save changes', 'jaiye-journeys' ); ?></button>
  </div>
</form>

<p class="m-foot"><a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php esc_html_e( 'Itinerary, photos and everything else: open in wp-admin', 'jaiye-journeys' ); ?></a></p>
