<?php
/**
 * Frequently Asked Questions (two columns).
 *
 * Shared by the full trip page (single-trip.php) and the waitlist page
 * (inc/trip-coming-soon.php), so both always render the section the same way.
 * Must stay free of prices and booking links: the waitlist page shows it.
 *
 * Expects $faqs.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
  <?php if ( $faqs ) : ?>
    <section class="trip-faq" id="faq">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Frequently Asked Questions', 'jaiye-journeys' ); ?></h2>
        <div class="trip-faq__columns">
          <?php foreach ( $faqs as $faq ) : ?>
            <details class="trip-accordion js-accordion">
              <summary class="trip-accordion__summary"><?php echo esc_html( isset( $faq['question'] ) ? $faq['question'] : '' ); ?></summary>
              <div class="trip-accordion__body">
                <div class="trip-prose"><?php echo wp_kses_post( wpautop( isset( $faq['answer'] ) ? $faq['answer'] : '' ) ); ?></div>
              </div>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
