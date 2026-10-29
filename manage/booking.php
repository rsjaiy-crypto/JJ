<?php
/**
 * One booking: who, what, the payment plan, a private note, and actions.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

if ( ! $id || 'jj_booking' !== get_post_type( $id ) || 'trash' === get_post_status( $id ) ) {
    echo '<p class="m-muted">' . esc_html__( 'That booking isn’t here any more.', 'jaiye-journeys' ) . '</p>';
    return;
}

$b      = jj_manage_booking( $id );
$cur    = $b['currency'];
$failed = (int) jj_booking_get( $id, 'failed_since' );
$payurl = jj_booking_get( $id, 'pay_url' );
$rows   = [
    __( 'Journey', 'jaiye-journeys' )    => $b['trip'],
    __( 'Dates', 'jaiye-journeys' )      => $b['departure'],
    __( 'Room', 'jaiye-journeys' )       => $b['room'],
    __( 'Travellers', 'jaiye-journeys' ) => $b['travellers'],
    __( 'Paying', 'jaiye-journeys' )     => jj_booking_plan_label( $id ),
    __( 'Total', 'jaiye-journeys' )      => $cur ? jj_booking_money( $b['total'], $cur ) : '',
    __( 'Paid so far', 'jaiye-journeys' ) => $cur ? jj_booking_money( $b['paid'], $cur ) : '',
    __( 'Still to pay', 'jaiye-journeys' ) => $cur ? jj_booking_money( max( 0, $b['total'] - $b['paid'] ), $cur ) : '',
    __( 'Discount', 'jaiye-journeys' )   => $b['code'] && $cur
        /* translators: 1: amount, 2: code */
        ? sprintf( __( '%1$s off with %2$s', 'jaiye-journeys' ), jj_booking_money( $b['discount'], $cur ), $b['code'] )
        : '',
    __( 'Booked on', 'jaiye-journeys' )  => wp_date( 'j F Y, H:i', $b['booked'] ),
];
?>
<p class="m-back"><a href="<?php echo esc_url( jj_manage_url( 'bookings' ) ); ?>">← <?php esc_html_e( 'All bookings', 'jaiye-journeys' ); ?></a></p>
<?php jj_manage_notice(); ?>

<header class="m-head">
  <h1 class="m-title"><?php echo esc_html( $b['name'] ? $b['name'] : $b['email'] ); ?></h1>
  <p>
    <span class="m-pill m-pill--<?php echo esc_attr( $b['tone'] ); ?>"><?php echo esc_html( $b['label'] ); ?></span>
    <?php if ( $b['test'] ) : ?><span class="m-pill m-pill--test"><?php esc_html_e( 'Test', 'jaiye-journeys' ); ?></span><?php endif; ?>
  </p>
</header>

<div class="m-actions">
  <?php if ( $b['email'] ) : ?>
    <a class="m-btn" href="<?php echo esc_url( 'mailto:' . $b['email'] ); ?>"><?php esc_html_e( 'Email guest', 'jaiye-journeys' ); ?></a>
  <?php endif; ?>
  <a class="m-btn m-btn--quiet" href="<?php echo esc_url( jj_booking_dashboard_url( $id ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open in Stripe', 'jaiye-journeys' ); ?></a>
</div>

<?php if ( 'alert' === $b['tone'] ) : ?>
  <section class="m-card m-card--alert">
    <?php if ( 'plan-error' === $b['status'] ) : ?>
      <h2 class="m-card__title"><?php esc_html_e( 'The payment plan didn’t set up', 'jaiye-journeys' ); ?></h2>
      <p><?php esc_html_e( 'The deposit was taken, but Stripe didn’t create the later payments.', 'jaiye-journeys' ); ?></p>
      <?php if ( $b['error'] ) : ?>
        <p class="m-muted"><?php echo esc_html( sprintf( /* translators: %s: Stripe's message */ __( 'Stripe said: %s', 'jaiye-journeys' ), $b['error'] ) ); ?></p>
      <?php endif; ?>
      <form method="post">
        <?php wp_nonce_field( 'jj_manage_retry_' . $id ); ?>
        <input type="hidden" name="jj_action" value="retry">
        <button class="m-btn" type="submit"><?php esc_html_e( 'Set up the payment plan again', 'jaiye-journeys' ); ?></button>
      </form>
    <?php else : ?>
      <h2 class="m-card__title"><?php esc_html_e( 'A payment is overdue', 'jaiye-journeys' ); ?></h2>
      <p>
        <?php
        if ( $failed ) {
            /* translators: %s: date */
            echo esc_html( sprintf( __( 'Unpaid since %s. Stripe keeps retrying and emails the guest to update their card.', 'jaiye-journeys' ), wp_date( 'j F Y', $failed ) ) );
        } else {
            esc_html_e( 'Stripe keeps retrying and emails the guest to update their card.', 'jaiye-journeys' );
        }
        ?>
      </p>
      <?php if ( $payurl ) : ?>
        <p><?php esc_html_e( 'Payment link you can send them:', 'jaiye-journeys' ); ?> <a href="<?php echo esc_url( $payurl ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'open link', 'jaiye-journeys' ); ?></a></p>
      <?php endif; ?>
    <?php endif; ?>
  </section>
<?php endif; ?>

<section class="m-card">
  <dl class="m-dl">
    <?php if ( $b['email'] ) : ?>
      <dt><?php esc_html_e( 'Email', 'jaiye-journeys' ); ?></dt>
      <dd><a href="<?php echo esc_url( 'mailto:' . $b['email'] ); ?>"><?php echo esc_html( $b['email'] ); ?></a></dd>
    <?php endif; ?>
    <?php foreach ( $rows as $label => $value ) : ?>
      <?php if ( '' !== (string) $value ) : ?>
        <dt><?php echo esc_html( $label ); ?></dt>
        <dd><?php echo esc_html( $value ); ?></dd>
      <?php endif; ?>
    <?php endforeach; ?>
  </dl>
</section>

<?php if ( $b['plan'] ) : ?>
  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'Payment plan', 'jaiye-journeys' ); ?></h2>
    <ol class="m-plan">
      <li class="is-paid">
        <span><?php esc_html_e( 'Deposit, at booking', 'jaiye-journeys' ); ?></span>
        <span><?php esc_html_e( 'Paid', 'jaiye-journeys' ); ?></span>
      </li>
      <?php foreach ( $b['plan'] as $i => $payment ) : ?>
        <?php $paid = $i < $b['made']; ?>
        <li class="<?php echo $paid ? 'is-paid' : ( $i === $b['made'] ? 'is-next' : '' ); ?>">
          <span><?php echo esc_html( wp_date( 'j M Y', $payment[0] ) . ' · ' . jj_booking_money( $payment[1], $cur ) ); ?></span>
          <span><?php echo $paid ? esc_html__( 'Paid', 'jaiye-journeys' ) : ( $i === $b['made'] ? esc_html__( 'Next', 'jaiye-journeys' ) : '' ); ?></span>
        </li>
      <?php endforeach; ?>
    </ol>
  </section>
<?php endif; ?>

<?php
$change = 'plan' === $b['payment'] && $cur ? jj_booking_plan_options( $id ) : null;
?>
<?php if ( $change && ( $change['options'] || $change['reason'] ) && 'paid' !== $b['status'] ) : ?>
  <details class="m-card m-replan"<?php echo isset( $_GET['done'] ) && 'replan-failed' === $_GET['done'] ? ' open' : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>>
    <summary><?php esc_html_e( 'Change payment plan', 'jaiye-journeys' ); ?></summary>
    <?php if ( $change['reason'] ) : ?>
      <p class="m-muted"><?php echo esc_html( $change['reason'] ); ?></p>
    <?php else : ?>
      <p>
        <?php
        /* translators: %s: amount */
        echo esc_html( sprintf( __( '%s still to pay. Payments already made stay as they are; the rest is spread over the new plan’s dates still ahead, on the same saved card. Nothing is charged today unless you choose "Pay the rest now".', 'jaiye-journeys' ), jj_booking_money( $change['owed'], $cur ) ) );
        ?>
      </p>
      <form method="post" class="js-confirm" data-confirm="<?php esc_attr_e( 'Change this guest’s payment plan to the one you’ve chosen?', 'jaiye-journeys' ); ?>">
        <?php wp_nonce_field( 'jj_manage_replan_' . $id ); ?>
        <input type="hidden" name="jj_action" value="replan">
        <?php $first_kind = array_key_first( $change['options'] ); ?>
        <?php foreach ( $change['options'] as $kind => $option ) : ?>
          <label class="m-replan__option">
            <input type="radio" name="kind" value="<?php echo esc_attr( $kind ); ?>"<?php checked( $first_kind, $kind ); ?>>
            <span>
              <strong><?php echo esc_html( $option[0] ); ?></strong>
              <span class="m-muted">
                <?php
                if ( 'now' === $kind ) {
                    /* translators: %s: amount */
                    echo esc_html( sprintf( __( '%s charged today', 'jaiye-journeys' ), jj_booking_money( $option[1][0][1], $cur ) ) );
                } else {
                    echo esc_html( jj_booking_plan_sentence( $option[1], $cur ) );
                }
                ?>
              </span>
            </span>
          </label>
        <?php endforeach; ?>
        <button class="m-btn" type="submit"><?php esc_html_e( 'Change plan', 'jaiye-journeys' ); ?></button>
      </form>
      <p class="m-muted"><?php esc_html_e( 'Let the guest know their new dates. Stripe doesn’t email them about the change.', 'jaiye-journeys' ); ?></p>
    <?php endif; ?>
  </details>
<?php endif; ?>

<?php $plan_log = jj_booking_get( $id, 'plan_log' ); ?>
<?php if ( $plan_log ) : ?>
  <section class="m-card">
    <h2 class="m-card__title"><?php esc_html_e( 'Plan changes', 'jaiye-journeys' ); ?></h2>
    <p class="m-muted"><?php echo nl2br( esc_html( $plan_log ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped first. ?></p>
  </section>
<?php endif; ?>

<section class="m-card">
  <h2 class="m-card__title"><?php esc_html_e( 'Private note', 'jaiye-journeys' ); ?></h2>
  <form method="post">
    <?php wp_nonce_field( 'jj_manage_note_' . $id ); ?>
    <input type="hidden" name="jj_action" value="note">
    <label class="m-sr" for="m-note"><?php esc_html_e( 'Note', 'jaiye-journeys' ); ?></label>
    <textarea id="m-note" class="m-input" name="note" rows="4" placeholder="<?php esc_attr_e( 'Dietary needs, room-mate, anything to remember. Only you see this.', 'jaiye-journeys' ); ?>"><?php echo esc_textarea( $b['note'] ); ?></textarea>
    <button class="m-btn" type="submit"><?php esc_html_e( 'Save note', 'jaiye-journeys' ); ?></button>
  </form>
</section>

<details class="m-card m-danger">
  <summary><?php esc_html_e( 'Cancel this booking', 'jaiye-journeys' ); ?></summary>
  <p><?php esc_html_e( 'This frees the guest’s places on the trip. It doesn’t refund anyone or stop their payment plan, so do those in Stripe first: refund the payment, and for a payment plan cancel the subscription.', 'jaiye-journeys' ); ?></p>
  <p class="m-muted"><?php esc_html_e( 'The booking moves to the Bin in wp-admin, where you can restore it.', 'jaiye-journeys' ); ?></p>
  <form method="post" class="js-confirm" data-confirm="<?php esc_attr_e( 'Cancel this booking and free its places? Have you refunded it in Stripe?', 'jaiye-journeys' ); ?>">
    <?php wp_nonce_field( 'jj_manage_cancel_' . $id ); ?>
    <input type="hidden" name="jj_action" value="cancel">
    <button class="m-btn m-btn--danger" type="submit"><?php esc_html_e( 'Cancel booking', 'jaiye-journeys' ); ?></button>
  </form>
</details>

<p class="m-foot"><a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php esc_html_e( 'Open in wp-admin', 'jaiye-journeys' ); ?></a></p>
