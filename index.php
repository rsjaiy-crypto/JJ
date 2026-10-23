<?php
/**
 * Fallback template. WordPress uses it for anything no other template
 * covers, such as search results, so it shows a Journal-style listing
 * rather than a blank page.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();

$jl_overline = __( 'The Journal', 'jaiye-journeys' );
$jl_title    = is_search()
    /* translators: %s: search terms */
    ? sprintf( __( 'Results for "%s"', 'jaiye-journeys' ), get_search_query( false ) )
    : __( 'The Journal', 'jaiye-journeys' );
$jl_intro    = '';
$jl_cta_btl  = false;

require get_template_directory() . '/inc/journal-list.php';

get_footer();
