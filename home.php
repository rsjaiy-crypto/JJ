<?php
/**
 * The Journal: the posts index, at whichever page is set as the Posts page
 * in Settings → Reading (expected to be /journal/).
 *
 * Listing markup: inc/journal-list.php. Styles: css/journal.css.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();

$jl_page_id  = (int) get_option( 'page_for_posts' );
$jl_overline = __( 'Jaiye Journeys', 'jaiye-journeys' );
$jl_title    = $jl_page_id ? get_the_title( $jl_page_id ) : __( 'The Journal', 'jaiye-journeys' );
// The Posts page's own text, if any, is the introduction.
$jl_intro    = $jl_page_id ? wpautop( get_post_field( 'post_content', $jl_page_id ) ) : '';
if ( '' === trim( wp_strip_all_tags( $jl_intro ) ) ) {
    $jl_intro = '<p>' . esc_html__( 'Stories and notes from Jaiye Journeys and Between the Lines.', 'jaiye-journeys' ) . '</p>';
}
$jl_cta_btl = false;

require get_template_directory() . '/inc/journal-list.php';

get_footer();
