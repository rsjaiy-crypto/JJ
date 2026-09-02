<?php

// ============================================================
// Google Analytics 4
// ============================================================

define( 'JAIYE_GA4_ID', 'G-13CW9QRTBE' );


// ============================================================
// Theme Setup
// ============================================================

function jaiye_theme_setup() {
    load_theme_textdomain( 'jaiye-journeys', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ] );

    add_theme_support( 'custom-logo', [
        'height'               => 80,
        'width'                => 220,
        'flex-height'          => true,
        'flex-width'           => true,
        'unlink-homepage-logo' => false,
        'header-text'          => [ 'site-title', 'site-description' ],
    ] );

    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'jaiye-journeys' ),
        'footer'  => __( 'Footer Navigation', 'jaiye-journeys' ),
    ] );
}
add_action( 'after_setup_theme', 'jaiye_theme_setup' );


// ============================================================
// Enqueue Assets
// ============================================================

function jaiye_enqueue_assets() {
    $theme   = wp_get_theme();
    $version = $theme->get( 'Version' );

    // Google Fonts
    wp_enqueue_style(
        'jaiye-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Jost:wght@300;400;600&display=swap',
        [],
        null
    );

    // Global CSS foundation
    wp_enqueue_style(
        'jaiye-global',
        get_template_directory_uri() . '/global.css',
        [ 'jaiye-fonts' ],
        filemtime( get_template_directory() . '/global.css' )
    );

    // Main theme stylesheet (style.css — WordPress requirement)
    wp_enqueue_style(
        'jaiye-style',
        get_stylesheet_uri(),
        [ 'jaiye-global' ],
        $version
    );

    // Testimonials carousel
    wp_enqueue_script(
        'jaiye-carousel',
        get_template_directory_uri() . '/js/carousel.js',
        [],
        filemtime( get_template_directory() . '/js/carousel.js' ),
        true
    );

    // Cookie consent banner
    wp_enqueue_script(
        'jaiye-cookie-consent',
        get_template_directory_uri() . '/js/cookie-consent.js',
        [],
        filemtime( get_template_directory() . '/js/cookie-consent.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'jaiye_enqueue_assets' );


// ============================================================
// Google Fonts Preconnect
// ============================================================

function jaiye_resource_hints( $hints, $relation_type ) {
    if ( 'preconnect' !== $relation_type ) {
        return $hints;
    }

    $hints[] = [ 'href' => 'https://fonts.googleapis.com' ];
    $hints[] = [
        'href'        => 'https://fonts.gstatic.com',
        'crossorigin' => 'anonymous',
    ];

    return $hints;
}
add_filter( 'wp_resource_hints', 'jaiye_resource_hints', 10, 2 );


// ============================================================
// Fallback Nav Menu
// Renders when no menu is assigned to the 'primary' location.
// ============================================================

function jaiye_fallback_nav_menu() {
    $leading_items = [
        'our-journeys'      => 'Our Journeys',
        'between-the-lines' => 'Between the Lines',
    ];

    $trailing_items = [
        'about'   => 'About',
        'contact' => 'Contact',
    ];

    echo '<ul class="site-nav__list">';

    foreach ( $leading_items as $slug => $label ) {
        $page = get_page_by_path( $slug );
        $url  = $page ? get_permalink( $page->ID ) : home_url( '/' . $slug . '/' );
        printf(
            '<li class="menu-item"><a href="%s">%s</a></li>',
            esc_url( $url ),
            esc_html( $label )
        );
    }

    // Services dropdown: Ticketing Desk (Tally), Business Desk, Private Groups & Celebrations.
    $bd_page  = get_page_by_path( 'business-desk' );
    $bd_url   = $bd_page ? get_permalink( $bd_page->ID ) : home_url( '/business-desk/' );
    $pgc_page = get_page_by_path( 'private-groups-celebrations' );
    $pgc_url  = $pgc_page ? get_permalink( $pgc_page->ID ) : home_url( '/private-groups-celebrations/' );
    ?>
    <li class="menu-item menu-item--has-dropdown">
      <button type="button" class="site-nav__dropdown-trigger" aria-expanded="false" aria-controls="services-submenu">
        <?php esc_html_e( 'Services', 'jaiye-journeys' ); ?>
        <span class="site-nav__dropdown-caret" aria-hidden="true">&#9662;</span>
      </button>
      <ul class="site-nav__submenu" id="services-submenu">
        <li>
          <a href="<?php echo esc_url( 'https://tally.so/r/eq6rp0' ); ?>" target="_blank" rel="noopener">
            <?php esc_html_e( 'The Ticketing Desk', 'jaiye-journeys' ); ?>
          </a>
        </li>
        <li>
          <a href="<?php echo esc_url( $bd_url ); ?>">
            <?php esc_html_e( 'The Business Desk', 'jaiye-journeys' ); ?>
          </a>
        </li>
        <li>
          <a href="<?php echo esc_url( $pgc_url ); ?>">
            <?php esc_html_e( 'Private Groups & Celebrations', 'jaiye-journeys' ); ?>
          </a>
        </li>
      </ul>
    </li>
    <?php
    foreach ( $trailing_items as $slug => $label ) {
        $page = get_page_by_path( $slug );
        $url  = $page ? get_permalink( $page->ID ) : home_url( '/' . $slug . '/' );
        printf(
            '<li class="menu-item"><a href="%s">%s</a></li>',
            esc_url( $url ),
            esc_html( $label )
        );
    }
    printf(
        '<li class="menu-item"><a href="%s" target="_blank" rel="noopener">%s</a></li>',
        esc_url( 'https://thejaiyeconcierge.com' ),
        esc_html( 'The Jaiye Concierge' )
    );
    echo '</ul>';
}


// ============================================================
// Excerpt
// ============================================================

function jaiye_excerpt_length() {
    return 24;
}
add_filter( 'excerpt_length', 'jaiye_excerpt_length' );

function jaiye_excerpt_more() {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'jaiye_excerpt_more' );


// ============================================================
// Body Classes
// ============================================================

function jaiye_body_classes( $classes ) {
    if ( ! is_singular() ) {
        $classes[] = 'archive-view';
    }
    if ( is_front_page() ) {
        $classes[] = 'is-front-page';
    }
    return $classes;
}
add_filter( 'body_class', 'jaiye_body_classes' );


// ============================================================
// Favicon
// ============================================================

function jaiye_favicon() {
    echo '<link rel="icon" type="image/png" href="' . esc_url( get_template_directory_uri() . '/assets/images/favicon.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'jaiye_favicon' );


// ============================================================
// GA4 Tracking
// Gated by cookie consent — only fires once the visitor has
// accepted via the cookie banner. Won't retroactively fire if
// consent is given after this page has already loaded; the
// consent script forces a reload on Accept to cover that case.
// ============================================================

function jaiye_ga4_tracking() {
    if ( ! defined( 'JAIYE_GA4_ID' ) || empty( JAIYE_GA4_ID ) ) {
        return;
    }
    if ( ! isset( $_COOKIE['jj_cookie_consent'] ) || $_COOKIE['jj_cookie_consent'] !== 'accepted' ) {
        return;
    }
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( JAIYE_GA4_ID ); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js( JAIYE_GA4_ID ); ?>');
    </script>
    <?php
}
add_action( 'wp_head', 'jaiye_ga4_tracking' );
