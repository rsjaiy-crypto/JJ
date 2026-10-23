<?php
/**
 * Closing call to action for the Journal.
 *
 * Reading Retreats posts (and that category's archive) point to Between the
 * Lines and The List; every other post points to Contact.
 *
 * Expects $jl_cta_btl (bool).
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( ! empty( $jl_cta_btl ) ) : ?>
  <section class="jp-cta" aria-label="<?php esc_attr_e( 'Get on The List for Between the Lines', 'jaiye-journeys' ); ?>">
    <span class="overline jp-cta__overline"><?php esc_html_e( 'Between the Lines', 'jaiye-journeys' ); ?></span>
    <h2 class="jp-cta__title"><?php esc_html_e( 'Read somewhere beautiful.', 'jaiye-journeys' ); ?></h2>
    <p class="jp-cta__body">
      <?php esc_html_e( 'Between the Lines is our series of six-day luxury reading retreats. Get on The List for early access pricing and first news of every new chapter.', 'jaiye-journeys' ); ?>
    </p>
    <div class="jp-cta__actions">
      <a href="<?php echo esc_url( jj_page_url( 'between-the-lines' ) . '#btl-waitlist' ); ?>" class="btn btn--accent">
        <?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?>
      </a>
      <a href="<?php echo esc_url( jj_page_url( 'between-the-lines' ) ); ?>" class="jp-cta__link">
        <?php esc_html_e( 'Explore Between the Lines', 'jaiye-journeys' ); ?>
      </a>
    </div>
  </section>
<?php else : ?>
  <section class="jp-cta" aria-label="<?php esc_attr_e( 'Plan your trip', 'jaiye-journeys' ); ?>">
    <span class="overline jp-cta__overline"><?php esc_html_e( 'Jaiye Journeys', 'jaiye-journeys' ); ?></span>
    <h2 class="jp-cta__title"><?php esc_html_e( 'Ready to make the trip real?', 'jaiye-journeys' ); ?></h2>
    <p class="jp-cta__body">
      <?php esc_html_e( 'From the page to the destination. Let\'s plan your next journey.', 'jaiye-journeys' ); ?>
    </p>
    <div class="jp-cta__actions">
      <a href="<?php echo esc_url( jj_page_url( 'contact' ) ); ?>" class="btn btn--accent">
        <?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?>
      </a>
    </div>
  </section>
<?php endif; ?>
