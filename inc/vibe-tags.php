<?php
/**
 * Vibe tags — the single source of truth for tag copy.
 *
 * Every place a vibe tag appears on the site (a trip's hero, its card on the
 * Our Journeys grid, the homepage promo cards) pulls its label and definition
 * from this one array. Updating what a tag says is editing this file, not
 * hunting through templates.
 *
 * Labels are derived from the key automatically (bucket-list → "Bucket
 * List"), so adding a new tag is just adding one line here.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;


/**
 * The tag definitions.
 *
 * @return array<string, string> Tag key => one-line definition.
 */
function jj_vibe_tag_definitions() {
    static $tags = null;

    if ( null === $tags ) {
        $tags = [
            'slow'            => __( "Nobody's rushing you here. Not even on checkout day.", 'jaiye-journeys' ),
            'main-character'  => __( 'For once, this trip is just about you.', 'jaiye-journeys' ),
            'bucket-list'     => __( 'The one that stays with you for years.', 'jaiye-journeys' ),
            'contrast'        => __( 'Sweeping landscapes that never settle on just one.', 'jaiye-journeys' ),
            'sensory'         => __( "This one you don't just see. You taste it, touch it, breathe it in.", 'jaiye-journeys' ),
            'storybook'       => __( "It doesn't quite feel real. Almost otherworldly.", 'jaiye-journeys' ),
            'wild'            => __( 'Up close with the animals, out in the elements.', 'jaiye-journeys' ),
            'community'       => __( 'Come alone or bring your friends. Either way, you leave with a community.', 'jaiye-journeys' ),
            'foodie'          => __( 'A love letter to the local food scene.', 'jaiye-journeys' ),
        ];
    }

    return $tags;
}


/**
 * A tag's definition sentence.
 *
 * @param string $key Tag key.
 * @return string Empty string for an unknown key — callers should skip
 *                rendering rather than show a pill with no meaning behind it.
 */
function jj_vibe_tag_definition( $key ) {
    $tags = jj_vibe_tag_definitions();
    return isset( $tags[ $key ] ) ? $tags[ $key ] : '';
}


/**
 * A tag's display label, derived from its key.
 *
 * @param string $key Tag key, e.g. "bucket-list".
 * @return string e.g. "Bucket List".
 */
function jj_vibe_tag_label( $key ) {
    return ucwords( str_replace( '-', ' ', $key ) );
}


/**
 * Render a trip's vibe tag pills.
 *
 * The 3-tag cap is enforced here, not left to convention — pass a 4th key
 * and it's simply never rendered.
 *
 * Two display modes:
 * - Interactive (default): a hoverable/focusable pill that reveals its
 *   definition in a small popover. Used on a trip's own hero, where there's
 *   room for the interaction and it's the first thing a visitor reads after
 *   the title.
 * - Static ($interactive = false): a plain pill for tight spaces like a grid
 *   card, where the card itself is one big link and a second layer of
 *   hoverable/tappable controls inside it would be awkward. The definition
 *   is still wired up via aria-describedby, so screen reader users get it
 *   either way — sighted card visitors just don't see a hover state.
 *
 * @param string[] $tag_keys    Up to 3 tag keys. Anything beyond the 3rd is dropped.
 * @param string   $id_prefix   Unique per trip (e.g. the post ID), so tooltip
 *                               ids never collide when several trips render
 *                               on the same page (the Our Journeys grid).
 * @param bool     $interactive Whether to render the hoverable/tappable version.
 */
function jj_render_vibe_tags( $tag_keys, $id_prefix = '', $interactive = true ) {

    $tag_keys = array_slice( array_values( array_filter( (array) $tag_keys ) ), 0, 3 );

    if ( empty( $tag_keys ) ) {
        return;
    }

    $wrapper_class = $interactive ? 'vibe-tags' : 'vibe-tags vibe-tags--static';

    echo '<ul class="' . esc_attr( $wrapper_class ) . '" role="list">';

    foreach ( $tag_keys as $key ) {

        $definition = jj_vibe_tag_definition( $key );
        if ( ! $definition ) {
            continue; // Unknown / retired key — skip rather than render an empty pill.
        }

        $label  = jj_vibe_tag_label( $key );
        $tip_id = 'vibe-tip-' . sanitize_html_class( ( $id_prefix ? $id_prefix . '-' : '' ) . $key );

        echo '<li class="vibe-tag">';

        if ( $interactive ) {
            printf(
                '<button type="button" class="vibe-tag__btn js-vibe-tag" aria-describedby="%s" aria-expanded="false">%s</button>',
                esc_attr( $tip_id ),
                esc_html( $label )
            );
            printf(
                '<span class="vibe-tag__tip" id="%s" role="tooltip">%s</span>',
                esc_attr( $tip_id ),
                esc_html( $definition )
            );
        } else {
            printf(
                '<span class="vibe-tag__label" aria-describedby="%s">%s</span>',
                esc_attr( $tip_id ),
                esc_html( $label )
            );
            printf(
                '<span class="sr-only" id="%s">%s</span>',
                esc_attr( $tip_id ),
                esc_html( $definition )
            );
        }

        echo '</li>';
    }

    echo '</ul>';
}


/**
 * Render a trip's vibe tags straight from its stored meta.
 *
 * Thin convenience wrapper around jj_render_vibe_tags() for the common case
 * of "this trip's own tags, keyed by its own post ID" — which is every call
 * site except front-page.php's hardcoded promo cards.
 *
 * @param int  $post_id     Trip post ID.
 * @param bool $interactive Whether to render the hoverable/tappable version.
 */
function jj_render_trip_vibe_tags( $post_id, $interactive = true ) {
    $keys = jj_trip_vibe_tag_keys( $post_id );
    jj_render_vibe_tags( $keys, (string) $post_id, $interactive );
}
