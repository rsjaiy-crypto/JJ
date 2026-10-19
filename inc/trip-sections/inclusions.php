<?php
/**
 * What's Included / Not Included / Where You'll Go accordions.
 *
 * Shared by the full trip page (single-trip.php) and the waitlist page
 * (inc/trip-coming-soon.php), so both always render the section the same way.
 * Must stay free of prices and booking links: the waitlist page shows it.
 *
 * Expects $trip_id, $included and $carousel (its first image is the fallback side image).
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
  <?php
  $not_included = jj_trip_lines( jj_trip_field( 'not_included', $trip_id ) );
  $route_map    = absint( jj_trip_field( 'route_map', $trip_id ) );
  $route_copy   = jj_trip_field( 'route_copy', $trip_id );
  ?>
  <?php if ( $included || $not_included || $route_map || $route_copy ) : ?>
    <?php
    // Side image: explicit field first, else reuse the opening carousel shot.
    $inclusions_image = absint( jj_trip_field( 'inclusions_image', $trip_id ) );
    if ( ! $inclusions_image && ! empty( $carousel[0] ) ) {
        $inclusions_image = absint( $carousel[0] );
    }
    ?>
    <section class="trip-inclusions" id="inclusions">
      <div class="container trip-inclusions__grid">
        <div class="trip-inclusions__main">

        <?php if ( $included ) : ?>
          <details class="trip-accordion js-accordion" open>
            <summary class="trip-accordion__summary"><?php esc_html_e( "What's Included", 'jaiye-journeys' ); ?></summary>
            <div class="trip-accordion__body">
              <?php foreach ( $included as $group ) : ?>
                <?php $items = jj_trip_lines( isset( $group['items'] ) ? $group['items'] : '' ); ?>
                <div class="trip-included-group">
                  <?php if ( ! empty( $group['group_title'] ) ) : ?>
                    <h4 class="trip-included-group__title"><?php echo esc_html( $group['group_title'] ); ?></h4>
                  <?php endif; ?>
                  <?php if ( $items ) : ?>
                    <ul class="trip-list" role="list">
                      <?php foreach ( $items as $item ) : ?>
                        <li><?php echo esc_html( $item ); ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </details>
        <?php endif; ?>

        <?php if ( $not_included ) : ?>
          <details class="trip-accordion js-accordion">
            <summary class="trip-accordion__summary"><?php esc_html_e( "What's Not Included", 'jaiye-journeys' ); ?></summary>
            <div class="trip-accordion__body">
              <ul class="trip-list trip-list--excluded" role="list">
                <?php foreach ( $not_included as $item ) : ?>
                  <li><?php echo esc_html( $item ); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </details>
        <?php endif; ?>

        <?php if ( $route_map || $route_copy ) : ?>
          <details class="trip-accordion js-accordion">
            <summary class="trip-accordion__summary"><?php esc_html_e( "Where You'll Go", 'jaiye-journeys' ); ?></summary>
            <div class="trip-accordion__body">
              <?php if ( $route_copy ) : ?>
                <div class="trip-prose"><?php echo wp_kses_post( wpautop( $route_copy ) ); ?></div>
              <?php endif; ?>
              <?php if ( $route_map ) : ?>
                <?php
                echo wp_get_attachment_image( $route_map, 'large', false, [
                    'class'   => 'trip-route-map',
                    'loading' => 'lazy',
                ] );
                ?>
              <?php endif; ?>
            </div>
          </details>
        <?php endif; ?>

        </div><!-- /.trip-inclusions__main -->

        <?php if ( $inclusions_image ) : ?>
          <div class="trip-inclusions__media js-reveal">
            <?php
            echo wp_get_attachment_image( $inclusions_image, 'large', false, [
                'class'   => 'trip-inclusions__img',
                'loading' => 'lazy',
            ] );
            ?>
          </div>
        <?php endif; ?>

      </div>
    </section>
  <?php endif; ?>
