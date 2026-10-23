<?php
/**
 * Journal archives: categories (e.g. Reading Retreats), tags, author and
 * dates. Trips have their own archive-trip.php.
 *
 * Listing markup: inc/journal-list.php. Styles: css/journal.css.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();

$jl_overline = __( 'The Journal', 'jaiye-journeys' );
$jl_title    = ( is_category() || is_tag() ) ? single_term_title( '', false ) : wp_strip_all_tags( get_the_archive_title() );
$jl_intro    = get_the_archive_description();
$jl_cta_btl  = is_category( JJ_JOURNAL_BTL_CATEGORY );

require get_template_directory() . '/inc/journal-list.php';

get_footer();
