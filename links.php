<?php
/**
 * Template Name: Links
 *
 * Link-in-bio page (replaces the Flodesk page). Standalone and mobile
 * first: no main menu or full footer, but wp_head() and wp_footer() still
 * run so the theme styles, scripts and cookie banner load. Styles live in
 * global.css under "Links page".
 *
 * Trip cards are read from the trips themselves (image, name, place,
 * dates), like the homepage's Upcoming Trips, so they always match the
 * trip pages. Change which trips show in $jj_links_trips below.
 *
 * @package jaiye-journeys
 */

/* ── PASTE YOUR KLAVIYO DETAILS HERE ──────────────────────────────────
   Replace the two placeholders below with your own values, keeping the
   quote marks. Until both are filled in, the sign-up section stays
   hidden from visitors.
   - Company ID: Klaviyo → Settings → Account → "Public API key / Site ID"
   - Form ID:    Klaviyo → Sign-up forms → your embed form → the ID in its
                 embed code (klaviyo-form-XXXXXX)                         */
$jj_links_klaviyo_company = 'KLAVIYO_COMPANY_ID';
$jj_links_klaviyo_form    = 'KLAVIYO_FORM_ID';
/* ──────────────────────────────────────────────────────────────────── */

// Upcoming trips, in this order, by their page address (slug).
$jj_links_trips = [ 'btl-marrakech', 'jj-bali', 'jj-peru', 'sintra-mist-manuscripts' ];

$jj_links_klaviyo_ready = 'KLAVIYO_COMPANY_ID' !== $jj_links_klaviyo_company && 'KLAVIYO_FORM_ID' !== $jj_links_klaviyo_form
    && '' !== trim( $jj_links_klaviyo_company ) && '' !== trim( $jj_links_klaviyo_form );

// Page title: "Jaiye Journeys | Links" (also wins over Rank Math's title).
$jj_links_title = function () {
    return __( 'Jaiye Journeys | Links', 'jaiye-journeys' );
};
add_filter( 'pre_get_document_title', $jj_links_title, 99 );
add_filter( 'rank_math/frontend/title', $jj_links_title, 99 );

// Klaviyo's onsite script, once, in this template's footer only. The
// site-wide copy (jaiye_klaviyo_tracking) is skipped here so it can't load
// twice.
remove_action( 'wp_footer', 'jaiye_klaviyo_tracking' );
if ( $jj_links_klaviyo_ready ) {
    add_action( 'wp_footer', function () use ( $jj_links_klaviyo_company ) {
        printf(
            '<script async type="text/javascript" src="%s"></script>' . "\n",
            esc_url( 'https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=' . rawurlencode( $jj_links_klaviyo_company ) )
        );
    }, 20 );
}

$jj_links_buttons = [
    [ __( 'The Ticketing Desk', 'jaiye-journeys' ), 'https://tally.so/r/eq6rp0', true ],
    [ __( 'Private Groups & Celebrations', 'jaiye-journeys' ), home_url( '/private-groups-celebrations/' ), false ],
    [ __( 'The Business Desk', 'jaiye-journeys' ), home_url( '/business-desk/' ), false ],
    [ __( 'Between the Lines', 'jaiye-journeys' ), home_url( '/between-the-lines/' ), false ],
    [ __( 'The Jaiye Concierge', 'jaiye-journeys' ), 'https://thejaiyeconcierge.com', true ],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>

<body <?php body_class( 'links-page' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content">
  <?php esc_html_e( 'Skip to content', 'jaiye-journeys' ); ?>
</a>

<main id="main-content" class="links">

  <!-- 1–3. Logo, Steph, tagline -->
  <header class="links__intro">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="links__logo" rel="home">
      <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>"
        alt="<?php esc_attr_e( 'Jaiye Journeys, go to the homepage', 'jaiye-journeys' ); ?>"
        class="links__logo-img"
      >
    </a>
    <img
      src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph.jpg' ); ?>"
      alt="<?php esc_attr_e( 'Steph Jaiyeola, founder of Jaiye Journeys', 'jaiye-journeys' ); ?>"
      class="links__photo"
      width="112"
      height="112"
    >
    <h1 class="links__tagline"><?php esc_html_e( 'Curated Travel to Enjoy Life.', 'jaiye-journeys' ); ?></h1>
    <p class="links__subline"><?php esc_html_e( 'The trip everyone remembers. The planning nobody sees.', 'jaiye-journeys' ); ?></p>
  </header>

  <!-- 4. Primary button -->
  <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary links__btn links__btn--main">
    <?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?>
  </a>

  <!-- 5. Upcoming trips (same cards as the homepage) -->
  <?php
  $jj_links_trip_ids = [];
  foreach ( $jj_links_trips as $jj_slug ) {
      $jj_trip = get_page_by_path( $jj_slug, OBJECT, 'trip' );
      if ( $jj_trip && 'publish' === $jj_trip->post_status ) {
          $jj_links_trip_ids[] = $jj_trip->ID;
      }
  }
  ?>
  <?php if ( $jj_links_trip_ids ) : ?>
    <section class="links__section" aria-labelledby="links-trips-title">
      <h2 class="links__heading" id="links-trips-title"><?php esc_html_e( 'Upcoming Trips', 'jaiye-journeys' ); ?></h2>
      <ul class="links__trips" role="list">
        <?php foreach ( $jj_links_trip_ids as $jj_trip_id ) : ?>
          <?php
          $jj_btl   = ( 'btl' === jj_trip_field( 'brand', $jj_trip_id, 'jj-edit' ) );
          $jj_title = jj_trip_volume_title( $jj_trip_id );
          $jj_name  = $jj_btl ? jj_trip_field( 'volume_name', $jj_trip_id ) : '';
          $jj_dest  = jj_trip_field( 'destination', $jj_trip_id );
          $jj_when  = jj_trip_first_departure_label( $jj_trip_id );
          ?>
          <li>
            <article class="trip-card links__trip">
              <?php if ( has_post_thumbnail( $jj_trip_id ) ) : ?>
                <div class="trip-card__media">
                  <?php
                  echo get_the_post_thumbnail( $jj_trip_id, 'medium_large', [
                      'class'   => 'trip-card__img',
                      'loading' => 'lazy',
                      'alt'     => $jj_dest ? $jj_dest : get_the_title( $jj_trip_id ),
                  ] );
                  ?>
                </div>
              <?php endif; ?>
              <div class="trip-card__body">
                <?php jj_trip_status_tag( $jj_trip_id ); ?>
                <p class="trip-card__type">
                  <span class="overline">
                    <?php echo esc_html( $jj_btl ? __( 'Between the Lines', 'jaiye-journeys' ) : __( 'Group Trip', 'jaiye-journeys' ) ); ?>
                  </span>
                </p>
                <h3 class="trip-card__title">
                  <?php echo esc_html( $jj_name ? $jj_title . ', ' . $jj_name : $jj_title ); ?>
                </h3>
                <?php if ( $jj_dest ) : ?>
                  <p class="trip-card__destination"><?php echo esc_html( $jj_dest ); ?></p>
                <?php endif; ?>
                <?php if ( $jj_when ) : ?>
                  <p class="trip-card__dates"><?php echo esc_html( $jj_when ); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url( get_permalink( $jj_trip_id ) ); ?>" class="btn btn--secondary btn--sm trip-card__cta">
                  <?php esc_html_e( 'Step inside', 'jaiye-journeys' ); ?>
                  <span class="sr-only"><?php echo esc_html( $jj_title ); ?></span>
                </a>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

  <!-- 6. Link buttons -->
  <nav class="links__section links__list" aria-label="<?php esc_attr_e( 'Jaiye Journeys links', 'jaiye-journeys' ); ?>">
    <?php foreach ( $jj_links_buttons as $jj_button ) : ?>
      <a
        href="<?php echo esc_url( $jj_button[1] ); ?>"
        class="btn btn--secondary links__btn"
        <?php if ( $jj_button[2] ) : ?>target="_blank" rel="noopener"<?php endif; ?>
      >
        <?php echo esc_html( $jj_button[0] ); ?>
        <?php if ( $jj_button[2] ) : ?><span class="sr-only"><?php esc_html_e( '(opens in a new tab)', 'jaiye-journeys' ); ?></span><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- 7. Email sign-up (Klaviyo embed) -->
  <?php if ( $jj_links_klaviyo_ready ) : ?>
    <section class="links__section links__signup" aria-labelledby="links-signup-title">
      <h2 class="links__heading" id="links-signup-title"><?php esc_html_e( 'Be first to hear about new trips', 'jaiye-journeys' ); ?></h2>
      <div class="klaviyo-form-<?php echo esc_attr( $jj_links_klaviyo_form ); ?>"></div>
      <p class="links__small">
        <?php
        printf(
            /* translators: %s: privacy policy link */
            esc_html__( 'By signing up, you agree to receive emails from Jaiye Journeys about trips, retreats and offers. You can unsubscribe at any time. We send our emails through Klaviyo, which stores data in the United States. Read our %s.', 'jaiye-journeys' ),
            '<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'privacy policy', 'jaiye-journeys' ) . '</a>'
        );
        ?>
      </p>
    </section>
  <?php elseif ( current_user_can( 'edit_pages' ) ) : ?>
    <p class="links__notice"><?php esc_html_e( 'Only you can see this: add your Klaviyo company ID and form ID at the top of links.php to show the email sign-up here.', 'jaiye-journeys' ); ?></p>
  <?php endif; ?>

  <!-- 8. Social icons -->
  <ul class="links__social" role="list">
    <li>
      <a href="https://www.instagram.com/jaiyejourneys" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Jaiye Journeys on Instagram (opens in a new tab)', 'jaiye-journeys' ); ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <circle cx="12" cy="12" r="4"/>
          <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/>
        </svg>
      </a>
    </li>
    <li>
      <a href="https://tiktok.com/@JaiyeJourneys" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Jaiye Journeys on TikTok (opens in a new tab)', 'jaiye-journeys' ); ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
          <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V9.05a8.16 8.16 0 0 0 4.78 1.52V7.12a4.85 4.85 0 0 1-1.01-.43z"/>
        </svg>
      </a>
    </li>
    <li>
      <a href="https://www.linkedin.com/company/jaiye-journeys/" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Jaiye Journeys on LinkedIn (opens in a new tab)', 'jaiye-journeys' ); ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
          <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
          <rect x="2" y="9" width="4" height="12"/>
          <circle cx="4" cy="4" r="2"/>
        </svg>
      </a>
    </li>
  </ul>

</main>

<!-- 9. Footer line -->
<footer class="links__footer">
  <p>
    &copy; <?php esc_html_e( 'Jaiye Journeys', 'jaiye-journeys' ); ?>
    <span aria-hidden="true">·</span>
    <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'jaiye-journeys' ); ?></a>
  </p>
</footer>

<?php get_template_part( 'cookie-consent' ); ?>

<?php wp_footer(); ?>
</body>
</html>
