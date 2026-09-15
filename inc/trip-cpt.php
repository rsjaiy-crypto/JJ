<?php
/**
 * Trip custom post type.
 *
 * Registers the `trip` CPT behind the /trips/ URL space, which is what the
 * homepage "Find Out More" buttons already link to (front-page.php links to
 * /trips/jj-bali/ and /trips/jj-cape-town/). Before this file existed nothing
 * served that URL space at all, so both links 404'd.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bump this whenever the CPT slug, rewrite args or archive settings change.
 * It drives the rewrite flush below — see jj_trip_maybe_flush_rewrites().
 */
define( 'JJ_TRIP_REWRITE_VERSION', '1.0.0' );


/**
 * Register the Trip post type.
 */
function jj_register_trip_post_type() {

    $labels = [
        'name'               => __( 'Trips', 'jaiye-journeys' ),
        'singular_name'      => __( 'Trip', 'jaiye-journeys' ),
        'menu_name'          => __( 'Trips', 'jaiye-journeys' ),
        'add_new'            => __( 'Add New', 'jaiye-journeys' ),
        'add_new_item'       => __( 'Add New Trip', 'jaiye-journeys' ),
        'edit_item'          => __( 'Edit Trip', 'jaiye-journeys' ),
        'new_item'           => __( 'New Trip', 'jaiye-journeys' ),
        'view_item'          => __( 'View Trip', 'jaiye-journeys' ),
        'view_items'         => __( 'View Trips', 'jaiye-journeys' ),
        'search_items'       => __( 'Search Trips', 'jaiye-journeys' ),
        'not_found'          => __( 'No trips found.', 'jaiye-journeys' ),
        'not_found_in_trash' => __( 'No trips found in Trash.', 'jaiye-journeys' ),
        'all_items'          => __( 'All Trips', 'jaiye-journeys' ),
        'archives'           => __( 'Trip Archives', 'jaiye-journeys' ),
        'featured_image'     => __( 'Hero Image', 'jaiye-journeys' ),
        'set_featured_image' => __( 'Set hero image', 'jaiye-journeys' ),
    ];

    register_post_type( 'trip', [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_in_rest'       => false, // Classic editor — meta boxes carry the content.
        'menu_position'      => 20,    // Just under Pages.
        'menu_icon'          => 'dashicons-palmtree',
        'hierarchical'       => false,
        'has_archive'        => 'trips',
        'rewrite'            => [
            'slug'       => 'trips',
            'with_front' => false,
        ],
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ],
    ] );
}
add_action( 'init', 'jj_register_trip_post_type' );


/**
 * Flush rewrite rules when the CPT's URL structure changes.
 *
 * This theme deploys over SFTP (uploadOnSave) rather than by activating the
 * theme, so none of the usual activation hooks ever fire on live. Without this,
 * /trips/<slug>/ keeps 404ing after deploy until someone manually re-saves
 * Settings → Permalinks. Gating on a stored version keeps it to a single flush
 * rather than flushing on every request, which would be expensive.
 */
function jj_trip_maybe_flush_rewrites() {
    if ( get_option( 'jj_trip_rewrite_version' ) === JJ_TRIP_REWRITE_VERSION ) {
        return;
    }

    flush_rewrite_rules( false );
    update_option( 'jj_trip_rewrite_version', JJ_TRIP_REWRITE_VERSION );
}
add_action( 'init', 'jj_trip_maybe_flush_rewrites', 99 );


/**
 * Load single-trip.php / archive-trip.php from the theme root.
 *
 * WordPress finds these by convention already; this filter only exists so the
 * templates keep working if the theme is ever restructured into subfolders.
 */
function jj_trip_template_hierarchy( $template ) {
    if ( is_singular( 'trip' ) ) {
        $found = locate_template( [ 'single-trip.php' ] );
        if ( $found ) {
            return $found;
        }
    }

    if ( is_post_type_archive( 'trip' ) ) {
        $found = locate_template( [ 'archive-trip.php' ] );
        if ( $found ) {
            return $found;
        }
    }

    return $template;
}
add_filter( 'template_include', 'jj_trip_template_hierarchy' );


/**
 * Send the /trips/ archive to the Our Journeys hub.
 *
 * Our Journeys is the canonical listing — it's the URL already in the primary
 * nav. Redirecting rather than rendering a second grid keeps one page for
 * search engines to index instead of two with identical content.
 *
 * Individual trips keep their /trips/<slug>/ URLs, so existing links (the
 * homepage cards included) are unaffected.
 */
function jj_trip_archive_redirect() {
    if ( ! is_post_type_archive( 'trip' ) ) {
        return;
    }

    $page = get_page_by_path( 'our-journeys' );
    if ( ! $page ) {
        return; // No hub page to send them to — render the archive as a fallback.
    }

    wp_safe_redirect( get_permalink( $page->ID ), 301 );
    exit;
}
add_action( 'template_redirect', 'jj_trip_archive_redirect' );


/**
 * Match the browser tab / search-result title to the on-page Volume title.
 *
 * Only the singular front-end string is touched — the underlying post_title
 * used by admin screens is unaffected.
 *
 * @param array $parts Document title parts.
 * @return array
 */
function jj_trip_document_title_parts( $parts ) {
    if ( ! is_singular( 'trip' ) ) {
        return $parts;
    }

    $post_id = get_queried_object_id();
    if ( 'btl' !== jj_trip_field( 'brand', $post_id, 'jj-edit' ) ) {
        return $parts;
    }

    $parts['title'] = jj_trip_volume_title( $post_id );

    return $parts;
}
add_filter( 'document_title_parts', 'jj_trip_document_title_parts' );


/**
 * Give trip singles a body class so the header can go transparent over the hero.
 */
function jj_trip_body_class( $classes ) {
    if ( is_singular( 'trip' ) ) {
        $classes[] = 'has-transparent-header';
        $classes[] = 'is-trip-single';
    }
    return $classes;
}
add_filter( 'body_class', 'jj_trip_body_class' );
