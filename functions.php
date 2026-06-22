<?php

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
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Jost:wght@300;400&display=swap',
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
    $items = [
        'our-journeys'              => 'Our Journeys',
        'between-the-lines'         => 'Between the Lines',
        'private-groups-celebrations' => 'Private Groups & Celebrations',
        'about'                     => 'About',
        'contact'                   => 'Contact',
    ];

    echo '<ul class="site-nav__list">';
    foreach ( $items as $slug => $label ) {
        $page = get_page_by_path( $slug );
        $url  = $page ? get_permalink( $page->ID ) : home_url( '/' . $slug . '/' );
        printf(
            '<li class="menu-item"><a href="%s">%s</a></li>',
            esc_url( $url ),
            esc_html( $label )
        );
    }
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
