<?php
/**
 * Between the Lines waitlist: a native pop-up form that sends sign-ups
 * straight to Klaviyo's public client API (company ID, never a private
 * key) when the visitor presses submit.
 *
 * Opened by any link with data-btl-waitlist on the Between the Lines page
 * and pages using the BTL Retreat template, and on arrival at
 * /between-the-lines/?waitlist=btl (the link-in-bio). It loads no Klaviyo
 * script and sets no cookies, so it works whatever the visitor chose on
 * the cookie banner.
 *
 * Markup: inc/btl-waitlist-form.php. Behaviour: js/btl-waitlist.js.
 * Styles: css/btl-waitlist.css.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

// Klaviyo list "Between the Lines: The List". Joining it triggers the BTL
// welcome flow, so it must stay exact.
define( 'JJ_BTL_WAITLIST_LIST', 'Sq3GFK' );

// Backup sign-up shown if Klaviyo can't be reached.
define( 'JJ_BTL_WAITLIST_FALLBACK', 'https://tally.so/r/Gxq2JL' );

/**
 * Pages that carry the form.
 *
 * @return bool
 */
function jj_btl_waitlist_here() {
    return is_page_template( [ 'between-the-lines.php', 'template-btl-retreat.php' ] );
}

function jj_btl_waitlist_assets() {
    if ( ! jj_btl_waitlist_here() ) {
        return;
    }
    wp_enqueue_style(
        'jaiye-btl-waitlist',
        get_template_directory_uri() . '/css/btl-waitlist.css',
        [ 'jaiye-global' ],
        filemtime( get_template_directory() . '/css/btl-waitlist.css' )
    );
    wp_enqueue_script(
        'jaiye-btl-waitlist',
        get_template_directory_uri() . '/js/btl-waitlist.js',
        [],
        filemtime( get_template_directory() . '/js/btl-waitlist.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'jj_btl_waitlist_assets' );

function jj_btl_waitlist_render() {
    if ( jj_btl_waitlist_here() ) {
        require get_template_directory() . '/inc/btl-waitlist-form.php';
    }
}
add_action( 'wp_footer', 'jj_btl_waitlist_render', 5 );
