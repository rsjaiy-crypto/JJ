<?php
/**
 * Day by Day tabbed itinerary.
 *
 * Shared by the full trip page (single-trip.php) and the waitlist page
 * (inc/trip-coming-soon.php), so both always render the section the same way.
 * Must stay free of prices and booking links: the waitlist page shows it.
 *
 * Expects $days.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
  <?php if ( $days ) : ?>
    <section class="trip-days" id="day-by-day">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Day by Day', 'jaiye-journeys' ); ?></h2>

        <div class="trip-days__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Itinerary days', 'jaiye-journeys' ); ?>">
          <?php foreach ( $days as $i => $day ) : ?>
            <button type="button"
                    class="trip-days__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
                    role="tab"
                    id="day-tab-<?php echo (int) ( $i + 1 ); ?>"
                    aria-controls="day-panel-<?php echo (int) ( $i + 1 ); ?>"
                    aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
                    tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
              <?php
              // BTL chapters use named days (The Prologue, The Crescendo…);
              // Edits stay plainly numbered.
              if ( ! empty( $day['day_name'] ) ) {
                  echo esc_html( $day['day_name'] );
              } else {
                  printf(
                      /* translators: %d: day number */
                      esc_html__( 'Day %d', 'jaiye-journeys' ),
                      (int) ( $i + 1 )
                  );
              }
              ?>
            </button>
          <?php endforeach; ?>
        </div>

        <?php foreach ( $days as $i => $day ) : ?>
          <div class="trip-days__panel<?php echo 0 === $i ? ' is-active' : ''; ?>"
               id="day-panel-<?php echo (int) ( $i + 1 ); ?>"
               role="tabpanel"
               aria-labelledby="day-tab-<?php echo (int) ( $i + 1 ); ?>"
               <?php echo 0 === $i ? '' : 'hidden'; ?>>
            <div class="trip-day">
              <div class="trip-day__copy">
                <?php if ( ! empty( $day['location'] ) ) : ?>
                  <p class="trip-day__location overline"><?php echo esc_html( $day['location'] ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $day['headline'] ) ) : ?>
                  <h3 class="trip-day__headline"><?php echo esc_html( $day['headline'] ); ?></h3>
                <?php endif; ?>
                <?php if ( ! empty( $day['body'] ) ) : ?>
                  <div class="trip-prose"><?php echo wp_kses_post( wpautop( $day['body'] ) ); ?></div>
                <?php endif; ?>
              </div>

              <?php if ( ! empty( $day['image_1'] ) || ! empty( $day['image_2'] ) ) : ?>
                <div class="trip-day__media">
                  <?php foreach ( [ 'image_1', 'image_2' ] as $image_key ) : ?>
                    <?php if ( ! empty( $day[ $image_key ] ) ) : ?>
                      <?php
                      echo wp_get_attachment_image( absint( $day[ $image_key ] ), 'large', false, [
                          'class'   => 'trip-day__img',
                          'loading' => 'lazy',
                      ] );
                      ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
