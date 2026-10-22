<?php
/**
 * The Between the Lines waitlist pop-up. Rendered in the footer by
 * jj_btl_waitlist_render(); hidden until opened.
 *
 * Field names are the Klaviyo profile fields and properties they save to;
 * the welcome emails depend on them, so don't rename them or their values.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog class="btlw" id="btl-waitlist" aria-labelledby="btlw-title">
  <div class="btlw__panel">
    <button type="button" class="btlw__close js-btlw-close" aria-label="<?php esc_attr_e( 'Close', 'jaiye-journeys' ); ?>">
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
    </button>

    <div class="btlw__body js-btlw-body">
      <h2 class="btlw__title" id="btlw-title" tabindex="-1"><?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?></h2>
      <p class="btlw__intro"><?php esc_html_e( 'Between the Lines is where readers go to live inside the feeling of their favourite books. Six-day reading residencies with radical rest, real connection and nowhere you need to be. Be the first to hear about each new Volume.', 'jaiye-journeys' ); ?></p>

      <form class="btlw__form js-btlw-form"
            data-company="<?php echo esc_attr( JAIYE_KLAVIYO_COMPANY_ID ); ?>"
            data-list="<?php echo esc_attr( JJ_BTL_WAITLIST_LIST ); ?>"
            novalidate>

        <div class="btlw__row">
          <div class="btlw__field">
            <label for="btlw-first"><?php esc_html_e( 'First name', 'jaiye-journeys' ); ?> <span class="btlw__req" aria-hidden="true">*</span></label>
            <input id="btlw-first" name="first_name" type="text" autocomplete="given-name" required>
          </div>
          <div class="btlw__field">
            <label for="btlw-last"><?php esc_html_e( 'Last name', 'jaiye-journeys' ); ?> <span class="btlw__opt"><?php esc_html_e( '(optional)', 'jaiye-journeys' ); ?></span></label>
            <input id="btlw-last" name="last_name" type="text" autocomplete="family-name">
          </div>
        </div>

        <div class="btlw__field">
          <label for="btlw-email"><?php esc_html_e( 'Email address', 'jaiye-journeys' ); ?> <span class="btlw__req" aria-hidden="true">*</span></label>
          <input id="btlw-email" name="email" type="email" autocomplete="email" inputmode="email" required>
        </div>

        <fieldset class="btlw__field btlw__choices">
          <legend><?php esc_html_e( 'Where are you based?', 'jaiye-journeys' ); ?> <span class="btlw__req" aria-hidden="true">*</span></legend>
          <div class="btlw__options">
            <?php foreach ( [ 'UK', 'US', 'Elsewhere' ] as $i => $country ) : ?>
              <label class="btlw__option"><input type="radio" name="Country" value="<?php echo esc_attr( $country ); ?>"<?php echo 0 === $i ? ' required' : ''; ?>> <span><?php echo esc_html( $country ); ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <div class="btlw__field">
          <label for="btlw-where"><?php esc_html_e( 'Whereabouts? (optional)', 'jaiye-journeys' ); ?></label>
          <input id="btlw-where" name="Whereabouts" type="text" autocomplete="address-level2" placeholder="<?php esc_attr_e( 'Town, city or county', 'jaiye-journeys' ); ?>">
        </div>

        <div class="btlw__field">
          <label for="btlw-volume"><?php esc_html_e( 'Which Volume are you interested in?', 'jaiye-journeys' ); ?> <span class="btlw__req" aria-hidden="true">*</span></label>
          <select id="btlw-volume" name="BTL Volume interest" required>
            <option value=""><?php esc_html_e( 'Choose a Volume', 'jaiye-journeys' ); ?></option>
            <?php // Saved values are what the emails use; Marrakech keeps its original spelling there. ?>
            <option value="Marrakech"><?php esc_html_e( 'Volume 1: Marrakesh, Morocco', 'jaiye-journeys' ); ?></option>
            <option value="Sintra"><?php esc_html_e( 'Volume 2: Sintra, Portugal', 'jaiye-journeys' ); ?></option>
            <option value="Both"><?php esc_html_e( 'Both 2027 residencies', 'jaiye-journeys' ); ?></option>
          </select>
        </div>

        <fieldset class="btlw__field btlw__choices">
          <legend><?php esc_html_e( 'Are you part of a book club or reading community?', 'jaiye-journeys' ); ?> <span class="btlw__opt"><?php esc_html_e( '(optional)', 'jaiye-journeys' ); ?></span></legend>
          <div class="btlw__options">
            <label class="btlw__option"><input type="radio" name="Book club member" value="Yes" class="js-btlw-club"> <span><?php esc_html_e( 'Yes', 'jaiye-journeys' ); ?></span></label>
            <label class="btlw__option"><input type="radio" name="Book club member" value="No" class="js-btlw-club"> <span><?php esc_html_e( 'No', 'jaiye-journeys' ); ?></span></label>
          </div>
        </fieldset>

        <div class="btlw__field js-btlw-club-name" hidden>
          <label for="btlw-club"><?php esc_html_e( 'Which one?', 'jaiye-journeys' ); ?> <span class="btlw__opt"><?php esc_html_e( '(optional)', 'jaiye-journeys' ); ?></span></label>
          <input id="btlw-club" name="Book club name" type="text">
        </div>

        <fieldset class="btlw__field btlw__choices">
          <legend><?php esc_html_e( 'How did you hear about us? Tick all that apply.', 'jaiye-journeys' ); ?> <span class="btlw__opt"><?php esc_html_e( '(optional)', 'jaiye-journeys' ); ?></span></legend>
          <div class="btlw__options btlw__options--stack">
            <?php
            $sources = [
                'Instagram'        => __( 'Instagram', 'jaiye-journeys' ),
                'TikTok'           => __( 'TikTok', 'jaiye-journeys' ),
                'Google'           => __( 'Google', 'jaiye-journeys' ),
                'A friend told me' => __( 'A friend told me', 'jaiye-journeys' ),
                'Travel advisor'   => __( 'Fora / Virtuoso / travel advisor network', 'jaiye-journeys' ),
                'Returning client' => __( 'Returning client', 'jaiye-journeys' ),
                'Other'            => __( 'Other', 'jaiye-journeys' ),
            ];
            ?>
            <?php foreach ( $sources as $value => $label ) : ?>
              <label class="btlw__option"><input type="checkbox" name="Heard about us" value="<?php echo esc_attr( $value ); ?>"<?php echo 'A friend told me' === $value ? ' class="js-btlw-friend"' : ''; ?>> <span><?php echo esc_html( $label ); ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <div class="btlw__field js-btlw-referrer" hidden>
          <label for="btlw-ref"><?php esc_html_e( 'Who referred you? Their full name, please, so we can thank them.', 'jaiye-journeys' ); ?> <span class="btlw__req" aria-hidden="true">*</span></label>
          <input id="btlw-ref" name="Referred by" type="text" autocomplete="off">
        </div>

        <?php /* Honeypot: people never see or fill this; bots do. */ ?>
        <div class="btlw__hp" aria-hidden="true">
          <label for="btlw-website">Website</label>
          <input id="btlw-website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>

        <p class="btlw__error js-btlw-error" role="alert" hidden></p>

        <button type="submit" class="btlw__submit js-btlw-submit"><?php esc_html_e( 'Get on The List', 'jaiye-journeys' ); ?></button>

        <p class="btlw__small">
          <?php
          printf(
              /* translators: %s: privacy policy link */
              esc_html__( 'By joining, you agree to hear from Between the Lines and Jaiye Journeys by email, including prices, booking dates and new trips. You can unsubscribe at any time. Read our %s.', 'jaiye-journeys' ),
              '<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'privacy policy', 'jaiye-journeys' ) . '</a>'
          );
          ?>
        </p>
      </form>
    </div>

    <div class="btlw__done js-btlw-done" hidden>
      <h2 class="btlw__title" tabindex="-1"><?php esc_html_e( 'You’re on The List.', 'jaiye-journeys' ); ?></h2>
      <p><?php esc_html_e( 'Keep an eye on your inbox. I’ll walk you through how a residency runs, and you’ll hear about every new Volume first. The best stories happen in the margins. I’ll see you there.', 'jaiye-journeys' ); ?></p>
    </div>

    <template class="js-btlw-error-text">
      <?php
      printf(
          /* translators: %s: backup form link */
          esc_html__( 'Sorry, something went wrong and we couldn’t add you just now. Please try again, or %s.', 'jaiye-journeys' ),
          '<a href="' . esc_url( JJ_BTL_WAITLIST_FALLBACK ) . '" target="_blank" rel="noopener">' . esc_html__( 'join using our backup form', 'jaiye-journeys' ) . '</a>'
      );
      ?>
    </template>
  </div>
</dialog>
