<?php
/*
 * Template Name: Our Journeys
 *
 * The single journeys hub. Previously this page hardcoded every trip card by
 * hand in two separate sections (group trips, then BTL retreats); it now pulls
 * live from the `trip` post type and filters client-side, so adding a trip in
 * wp-admin is all that's needed for it to appear here.
 *
 * /trips/ redirects here — see jj_trip_archive_redirect() in inc/trip-cpt.php.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();

require_once get_template_directory() . '/inc/trip-card.php';

$trips = get_posts( [
    'post_type'      => 'trip',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
] );

// Only offer a filter option for a region that actually has trips behind it.
$regions_present = [];
foreach ( $trips as $trip ) {
    $region = jj_trip_field( 'region', $trip->ID );
    if ( $region && ! isset( $regions_present[ $region ] ) ) {
        $regions_present[ $region ] = jj_trip_region_label( $region );
    }
}
asort( $regions_present );

// Same for pace: don't offer a band that would return an empty grid.
$paces_present = [];
foreach ( $trips as $trip ) {
    $band = jj_trip_pace_band( jj_trip_field( 'meter_pace', $trip->ID, 50 ) );
    $paces_present[ $band ] = jj_trip_pace_label( $band );
}
$pace_order    = [ 'slow' => 0, 'balanced' => 1, 'fast' => 2 ];
uksort( $paces_present, function ( $a, $b ) use ( $pace_order ) {
    return $pace_order[ $a ] <=> $pace_order[ $b ];
} );

$counts = [
    'all'     => count( $trips ),
    'jj-edit' => 0,
    'btl'     => 0,
];
foreach ( $trips as $trip ) {
    $brand = jj_trip_field( 'brand', $trip->ID, 'jj-edit' );
    if ( isset( $counts[ $brand ] ) ) {
        $counts[ $brand ]++;
    }
}
?>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. HERO
       ============================================================ -->
  <section class="oj-hero" aria-label="<?php esc_attr_e( 'Our Journeys', 'jaiye-journeys' ); ?>">
    <div class="container">
      <div class="oj-hero__content">
        <h1 class="oj-hero__heading"><?php esc_html_e( 'Our Journeys', 'jaiye-journeys' ); ?></h1>
        <p class="oj-hero__sub">
          <?php esc_html_e( 'Curated group trips and literary reading retreats. Filter by where you want to go, what kind of journey it is, and how fast you want it to move.', 'jaiye-journeys' ); ?>
        </p>
      </div>
    </div>
  </section>


  <!-- ============================================================
       2. FILTERABLE JOURNEYS GRID
       ============================================================ -->
  <section class="jfilter" id="journeys" aria-label="<?php esc_attr_e( 'All journeys', 'jaiye-journeys' ); ?>">
    <div class="container">

      <?php if ( ! $trips ) : ?>

        <p class="jfilter__empty-state">
          <?php esc_html_e( 'No journeys are published yet.', 'jaiye-journeys' ); ?>
        </p>

      <?php else : ?>

        <!--
          Filtering is progressive enhancement: with JS off every trip stays
          visible and the controls simply do nothing, so the page is never
          empty or unusable.
        -->
        <div class="jfilter__controls js-jfilter">

          <div class="jfilter__chips" role="group" aria-label="<?php esc_attr_e( 'Filter by journey type', 'jaiye-journeys' ); ?>">
            <button type="button" class="jchip is-active" data-filter="brand" data-value="all" aria-pressed="true">
              <?php esc_html_e( 'All Journeys', 'jaiye-journeys' ); ?>
              <span class="jchip__count"><?php echo esc_html( $counts['all'] ); ?></span>
            </button>
            <button type="button" class="jchip" data-filter="brand" data-value="jj-edit" aria-pressed="false">
              <?php esc_html_e( 'Group Trips', 'jaiye-journeys' ); ?>
              <span class="jchip__count"><?php echo esc_html( $counts['jj-edit'] ); ?></span>
            </button>
            <button type="button" class="jchip" data-filter="brand" data-value="btl" aria-pressed="false">
              <?php esc_html_e( 'Reading Retreats', 'jaiye-journeys' ); ?>
              <span class="jchip__count"><?php echo esc_html( $counts['btl'] ); ?></span>
            </button>
          </div>

          <div class="jfilter__row">

            <?php if ( count( $regions_present ) > 1 ) : ?>
              <label class="jfilter__field">
                <span class="jfilter__label"><?php esc_html_e( 'Destination', 'jaiye-journeys' ); ?></span>
                <select class="jfilter__select" data-filter="region">
                  <option value="all"><?php esc_html_e( 'Anywhere', 'jaiye-journeys' ); ?></option>
                  <?php foreach ( $regions_present as $key => $label ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            <?php endif; ?>

            <?php if ( count( $paces_present ) > 1 ) : ?>
              <label class="jfilter__field">
                <span class="jfilter__label"><?php esc_html_e( 'Pace', 'jaiye-journeys' ); ?></span>
                <select class="jfilter__select" data-filter="pace">
                  <option value="all"><?php esc_html_e( 'Any pace', 'jaiye-journeys' ); ?></option>
                  <?php foreach ( $paces_present as $band => $label ) : ?>
                    <option value="<?php echo esc_attr( $band ); ?>"><?php echo esc_html( $label ); ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            <?php endif; ?>

            <label class="jfilter__field">
              <span class="jfilter__label"><?php esc_html_e( 'Sort', 'jaiye-journeys' ); ?></span>
              <select class="jfilter__select" data-sort>
                <option value="soonest"><?php esc_html_e( 'Soonest first', 'jaiye-journeys' ); ?></option>
                <option value="price-asc"><?php esc_html_e( 'Price: low to high', 'jaiye-journeys' ); ?></option>
                <option value="price-desc"><?php esc_html_e( 'Price: high to low', 'jaiye-journeys' ); ?></option>
                <option value="az"><?php esc_html_e( 'A–Z', 'jaiye-journeys' ); ?></option>
              </select>
            </label>

            <button type="button" class="jfilter__reset js-jfilter-reset" hidden>
              <?php esc_html_e( 'Clear filters', 'jaiye-journeys' ); ?>
            </button>

          </div>

          <p class="jfilter__status" role="status" aria-live="polite"></p>

        </div><!-- /.jfilter__controls -->

        <div class="jfilter__grid js-jfilter-grid">
          <?php foreach ( $trips as $trip ) : ?>
            <?php jj_trip_card( $trip->ID ); ?>
          <?php endforeach; ?>
        </div>

        <p class="jfilter__empty js-jfilter-empty" hidden>
          <?php esc_html_e( 'No journeys match those filters yet. Try widening your search, or get in touch and we will build something for you.', 'jaiye-journeys' ); ?>
        </p>

      <?php endif; ?>

    </div>
  </section>


  <!-- ============================================================
       3. CTA STRIP
       ============================================================ -->
  <section class="oj-cta" aria-label="<?php esc_attr_e( 'Get in touch', 'jaiye-journeys' ); ?>">
    <div class="container">
      <div class="oj-cta__inner">
        <h2 class="oj-cta__heading">
          <?php esc_html_e( 'Not sure which journey is for you?', 'jaiye-journeys' ); ?>
        </h2>
        <p class="oj-cta__sub">
          <?php esc_html_e( 'Get in touch and we will help you find your perfect journey.', 'jaiye-journeys' ); ?>
        </p>
        <a
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          class="btn btn--ghost"
          aria-label="<?php esc_attr_e( 'Talk to us — contact Jaiye Journeys', 'jaiye-journeys' ); ?>"
        >
          <?php esc_html_e( 'Talk to Us', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </section>

</main><!-- /#main-content -->

<?php get_footer(); ?>
