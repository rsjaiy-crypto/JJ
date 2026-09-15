<?php
/**
 * Shared trip card.
 *
 * Renders one trip in a grid, carrying the data attributes the Journeys page
 * filters on. Used by our-journeys.php; kept separate so any future listing
 * renders an identical card.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;


/**
 * Render a single trip card.
 *
 * @param int $post_id Trip post ID.
 */
function jj_trip_card( $post_id ) {

    $brand       = jj_trip_field( 'brand', $post_id, 'jj-edit' );
    $title       = jj_trip_volume_title( $post_id );
    $region      = jj_trip_field( 'region', $post_id );
    $pace_band   = jj_trip_pace_band( jj_trip_field( 'meter_pace', $post_id, 50 ) );
    $coming_soon = jj_trip_is_coming_soon( $post_id );
    $price       = jj_trip_field( 'price_from', $post_id );
    $duration    = jj_trip_field( 'duration', $post_id );
    $destination = jj_trip_field( 'destination', $post_id );
    $year        = jj_trip_earliest_year( $post_id );
    ?>
    <article
      class="jcard<?php echo $coming_soon ? ' jcard--soon' : ''; ?>"
      data-brand="<?php echo esc_attr( $brand ); ?>"
      data-region="<?php echo esc_attr( $region ); ?>"
      data-pace="<?php echo esc_attr( $pace_band ); ?>"
      data-status="<?php echo esc_attr( $coming_soon ? 'coming-soon' : 'open' ); ?>"
      data-price="<?php echo esc_attr( $price ? (int) $price : 0 ); ?>"
      data-year="<?php echo esc_attr( $year ); ?>"
      data-title="<?php echo esc_attr( $title ); ?>"
    >
      <a class="jcard__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">

        <div class="jcard__media">
          <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <?php
            echo get_the_post_thumbnail( $post_id, 'medium_large', [
                'class'   => 'jcard__img',
                'loading' => 'lazy',
            ] );
            ?>
          <?php else : ?>
            <span class="jcard__img jcard__img--placeholder" aria-hidden="true"></span>
          <?php endif; ?>

          <span class="jcard__badge jcard__badge--<?php echo esc_attr( $brand ); ?>">
            <?php echo esc_html( jj_trip_brand_label( $brand ) ); ?>
          </span>

          <?php if ( $coming_soon ) : ?>
            <span class="jcard__badge jcard__badge--soon">
              <?php esc_html_e( 'Coming Soon', 'jaiye-journeys' ); ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="jcard__body">
          <h3 class="jcard__title"><?php echo esc_html( $title ); ?></h3>

          <?php if ( $destination ) : ?>
            <p class="jcard__destination"><?php echo esc_html( $destination ); ?></p>
          <?php endif; ?>

          <?php jj_render_trip_vibe_tags( $post_id, false ); // Static: the whole card is already a link, so no second layer of tap targets inside it. ?>

          <p class="jcard__meta">
            <span class="jcard__pace jcard__pace--<?php echo esc_attr( $pace_band ); ?>">
              <?php echo esc_html( jj_trip_pace_label( $pace_band ) ); ?>
            </span>
            <?php if ( $duration ) : ?>
              <span class="jcard__duration"><?php echo esc_html( $duration ); ?></span>
            <?php endif; ?>
          </p>

          <p class="jcard__foot">
            <?php if ( $coming_soon ) : ?>
              <span class="jcard__cta"><?php esc_html_e( 'Join the waitlist', 'jaiye-journeys' ); ?></span>
            <?php else : ?>
              <?php if ( $price ) : ?>
                <span class="jcard__price">
                  <?php
                  printf(
                      /* translators: %s: formatted price */
                      esc_html__( 'From £%s', 'jaiye-journeys' ),
                      esc_html( number_format_i18n( (int) $price ) )
                  );
                  ?>
                </span>
              <?php endif; ?>
              <span class="jcard__cta"><?php esc_html_e( 'Find out more', 'jaiye-journeys' ); ?></span>
            <?php endif; ?>
          </p>
        </div>

      </a>
    </article>
    <?php
}
