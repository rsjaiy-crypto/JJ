<?php
/**
 * Cookie consent banner.
 * Included via get_template_part( 'cookie-consent' ) in footer.php.
 * Skips rendering entirely once a consent decision has already been stored,
 * so repeat visitors never see it flash on load.
 */

if ( isset( $_COOKIE['jj_cookie_consent'] ) ) {
    return;
}
?>

<div class="cookie-consent" id="cookie-consent" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e( 'Cookie consent', 'jaiye-journeys' ); ?>">
  <div class="cookie-consent__inner">

    <p class="cookie-consent__text">
      <?php esc_html_e( 'We use cookies to make your visit smoother.', 'jaiye-journeys' ); ?>
      <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">
        <?php esc_html_e( 'Privacy Policy', 'jaiye-journeys' ); ?>
      </a>
    </p>

    <div class="cookie-consent__actions">
      <button type="button" class="btn btn--secondary btn--sm cookie-consent__btn" data-consent="declined">
        <?php esc_html_e( 'Decline', 'jaiye-journeys' ); ?>
      </button>
      <button type="button" class="btn btn--accent btn--sm cookie-consent__btn" data-consent="accepted">
        <?php esc_html_e( 'Accept', 'jaiye-journeys' ); ?>
      </button>
    </div>

  </div>
</div><!-- /#cookie-consent -->
