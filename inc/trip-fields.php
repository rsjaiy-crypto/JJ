<?php
/**
 * Trip field schema, getters and sanitisers.
 *
 * This file is the single source of truth for a trip's data model. The meta
 * box UI (trip-meta.php), the save/sanitise routine, the seeder and the
 * single-trip template all read this schema, so none of them can drift apart.
 *
 * All meta keys are stored with the JJ_TRIP_META_PREFIX prefix and are
 * protected (leading underscore) so they stay out of the generic custom
 * fields panel.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

define( 'JJ_TRIP_META_PREFIX', '_jj_trip_' );


/**
 * The full field schema, grouped into meta boxes.
 *
 * Field types: text, textarea, number, url, select, meter, image, gallery, repeater.
 *
 * @return array
 */
function jj_trip_schema() {

    static $schema = null;
    if ( null !== $schema ) {
        return $schema;
    }

    $schema = [

        /* ── Hero & intro ─────────────────────────────────────────── */
        'hero' => [
            'title'  => __( 'Hero & Intro', 'jaiye-journeys' ),
            'fields' => [
                'destination'     => [
                    'type'  => 'text',
                    'label' => __( 'Destination', 'jaiye-journeys' ),
                    'desc'  => __( 'Used in the "Welcome to …" heading. e.g. Peru', 'jaiye-journeys' ),
                ],
                'vibe_tags'       => [
                    'type'  => 'text',
                    'label' => __( 'Vibe tags', 'jaiye-journeys' ),
                    'desc'  => __( 'Three, comma separated. e.g. Small group, Adventure, Culture', 'jaiye-journeys' ),
                ],
                'intro_statement' => [
                    'type'  => 'textarea',
                    'label' => __( 'Intro statement', 'jaiye-journeys' ),
                    'desc'  => __( 'The large centred one-liner under the sub-nav. One sentence.', 'jaiye-journeys' ),
                    'rows'  => 3,
                ],
                'collage_images'  => [
                    'type'  => 'gallery',
                    'label' => __( 'Collage images', 'jaiye-journeys' ),
                    'desc'  => __( 'Five images for the staggered parallax collage.', 'jaiye-journeys' ),
                ],
            ],
        ],

        /* ── Overview & stats ─────────────────────────────────────── */
        'overview' => [
            'title'  => __( 'Overview & Stats', 'jaiye-journeys' ),
            'fields' => [
                'duration'     => [
                    'type'  => 'text',
                    'label' => __( 'Duration', 'jaiye-journeys' ),
                    'desc'  => __( 'e.g. 9 days', 'jaiye-journeys' ),
                ],
                'price_from'   => [
                    'type'  => 'number',
                    'label' => __( 'From price (£)', 'jaiye-journeys' ),
                    'desc'  => __( 'Digits only — the £ and formatting are added by the template.', 'jaiye-journeys' ),
                ],
                'group_size'   => [
                    'type'  => 'text',
                    'label' => __( 'Group size', 'jaiye-journeys' ),
                    'desc'  => __( 'e.g. Max 12 travellers', 'jaiye-journeys' ),
                ],
                'welcome_copy' => [
                    'type'  => 'textarea',
                    'label' => __( 'Welcome copy', 'jaiye-journeys' ),
                    'desc'  => __( 'Two or three short paragraphs. Blank lines separate paragraphs.', 'jaiye-journeys' ),
                    'rows'  => 8,
                ],
            ],
        ],

        /* ── Trip style meters ────────────────────────────────────── */
        'meters' => [
            'title'  => __( 'Trip Style Meters', 'jaiye-journeys' ),
            'desc'   => __( 'Each meter is 0–100. These are an editorial judgement call, not a calculation.', 'jaiye-journeys' ),
            'fields' => [
                'meter_pace'       => [ 'type' => 'meter', 'label' => __( 'Pace', 'jaiye-journeys' ), 'default' => 50 ],
                'meter_culture'    => [ 'type' => 'meter', 'label' => __( 'Culture', 'jaiye-journeys' ), 'default' => 50 ],
                'meter_food'       => [ 'type' => 'meter', 'label' => __( 'Food', 'jaiye-journeys' ), 'default' => 50 ],
                'meter_adventure'  => [ 'type' => 'meter', 'label' => __( 'Adventure', 'jaiye-journeys' ), 'default' => 50 ],
                'meter_nightlife'  => [ 'type' => 'meter', 'label' => __( 'Nightlife', 'jaiye-journeys' ), 'default' => 50 ],
                'meter_relaxation' => [ 'type' => 'meter', 'label' => __( 'Relaxation', 'jaiye-journeys' ), 'default' => 50 ],
            ],
        ],

        /* ── Booking card ─────────────────────────────────────────── */
        'booking' => [
            'title'  => __( 'Booking Card', 'jaiye-journeys' ),
            'fields' => [
                'booking_url'    => [
                    'type'  => 'url',
                    'label' => __( 'Book Now URL', 'jaiye-journeys' ),
                    'desc'  => __( 'Tally form or checkout link.', 'jaiye-journeys' ),
                ],
                'enquiry_url'    => [
                    'type'  => 'url',
                    'label' => __( 'Enquire URL', 'jaiye-journeys' ),
                    'desc'  => __( 'Leave blank to fall back to the Contact page.', 'jaiye-journeys' ),
                ],
                'payment_terms'  => [
                    'type'  => 'textarea',
                    'label' => __( 'Payment terms note', 'jaiye-journeys' ),
                    'desc'  => __( 'Small print under the booking buttons.', 'jaiye-journeys' ),
                    'rows'  => 3,
                ],
                'departures'     => [
                    'type'      => 'repeater',
                    'label'     => __( 'Departures', 'jaiye-journeys' ),
                    'desc'      => __( 'Grouped into year tabs by the Year field.', 'jaiye-journeys' ),
                    'row_label' => __( 'Departure', 'jaiye-journeys' ),
                    'subfields' => [
                        'year'         => [ 'type' => 'text', 'label' => __( 'Year', 'jaiye-journeys' ), 'placeholder' => '2027' ],
                        'dates'        => [ 'type' => 'text', 'label' => __( 'Dates', 'jaiye-journeys' ), 'placeholder' => '12 – 20 May' ],
                        'availability' => [ 'type' => 'text', 'label' => __( 'Availability label', 'jaiye-journeys' ), 'placeholder' => 'Places available' ],
                        'status'       => [
                            'type'    => 'select',
                            'label'   => __( 'Status', 'jaiye-journeys' ),
                            'options' => [
                                'available' => __( 'Available', 'jaiye-journeys' ),
                                'limited'   => __( 'Limited', 'jaiye-journeys' ),
                                'sold-out'  => __( 'Sold out', 'jaiye-journeys' ),
                            ],
                        ],
                        'book_url'     => [ 'type' => 'url', 'label' => __( 'Book URL (optional)', 'jaiye-journeys' ) ],
                    ],
                ],
            ],
        ],

        /* ── Inclusions & route ───────────────────────────────────── */
        'inclusions' => [
            'title'  => __( "What's Included & Route", 'jaiye-journeys' ),
            'fields' => [
                'carousel_images' => [
                    'type'  => 'gallery',
                    'label' => __( 'Carousel images', 'jaiye-journeys' ),
                    'desc'  => __( 'Photo carousel shown above the accordions.', 'jaiye-journeys' ),
                ],
                'included_groups' => [
                    'type'      => 'repeater',
                    'label'     => __( "What's Included", 'jaiye-journeys' ),
                    'desc'      => __( 'Each row is a sub-list inside the accordion.', 'jaiye-journeys' ),
                    'row_label' => __( 'Group', 'jaiye-journeys' ),
                    'subfields' => [
                        'group_title' => [ 'type' => 'text', 'label' => __( 'Group title', 'jaiye-journeys' ), 'placeholder' => 'Accommodation' ],
                        'items'       => [
                            'type'  => 'textarea',
                            'label' => __( 'Items', 'jaiye-journeys' ),
                            'desc'  => __( 'One per line.', 'jaiye-journeys' ),
                            'rows'  => 4,
                        ],
                    ],
                ],
                'not_included'    => [
                    'type'  => 'textarea',
                    'label' => __( "What's Not Included", 'jaiye-journeys' ),
                    'desc'  => __( 'One item per line.', 'jaiye-journeys' ),
                    'rows'  => 6,
                ],
                'route_map'       => [
                    'type'  => 'image',
                    'label' => __( "Where You'll Go — route map", 'jaiye-journeys' ),
                ],
                'route_copy'      => [
                    'type'  => 'textarea',
                    'label' => __( 'Route description', 'jaiye-journeys' ),
                    'rows'  => 4,
                ],
                'strip_images'    => [
                    'type'  => 'gallery',
                    'label' => __( 'Auto-scroll strip images', 'jaiye-journeys' ),
                    'desc'  => __( 'Horizontal marquee strip. Six or more works best.', 'jaiye-journeys' ),
                ],
            ],
        ],

        /* ── Day by day ───────────────────────────────────────────── */
        'itinerary' => [
            'title'  => __( 'Day by Day', 'jaiye-journeys' ),
            'fields' => [
                'days' => [
                    'type'      => 'repeater',
                    'label'     => __( 'Days', 'jaiye-journeys' ),
                    'desc'      => __( 'One row per day. Days are numbered automatically in the tabs.', 'jaiye-journeys' ),
                    'row_label' => __( 'Day', 'jaiye-journeys' ),
                    'subfields' => [
                        'location' => [ 'type' => 'text', 'label' => __( 'Location', 'jaiye-journeys' ) ],
                        'headline' => [ 'type' => 'text', 'label' => __( 'Headline', 'jaiye-journeys' ) ],
                        'body'     => [ 'type' => 'textarea', 'label' => __( 'Body', 'jaiye-journeys' ), 'rows' => 5 ],
                        'image_1'  => [ 'type' => 'image', 'label' => __( 'Image 1', 'jaiye-journeys' ) ],
                        'image_2'  => [ 'type' => 'image', 'label' => __( 'Image 2', 'jaiye-journeys' ) ],
                    ],
                ],
            ],
        ],

        /* ── Trip notes ───────────────────────────────────────────── */
        'notes' => [
            'title'  => __( 'Trip Notes', 'jaiye-journeys' ),
            'fields' => [
                'trip_notes' => [
                    'type'      => 'repeater',
                    'label'     => __( 'Notes', 'jaiye-journeys' ),
                    'desc'      => __( 'Tick "Featured" on one row (usually Arrivals & Departures) to make it the large card.', 'jaiye-journeys' ),
                    'row_label' => __( 'Note', 'jaiye-journeys' ),
                    'subfields' => [
                        'title'    => [ 'type' => 'text', 'label' => __( 'Title', 'jaiye-journeys' ) ],
                        'body'     => [ 'type' => 'textarea', 'label' => __( 'Body', 'jaiye-journeys' ), 'rows' => 4 ],
                        'icon'     => [
                            'type'    => 'select',
                            'label'   => __( 'Icon', 'jaiye-journeys' ),
                            'options' => [
                                'plane'   => __( 'Plane — arrivals/departures', 'jaiye-journeys' ),
                                'bed'     => __( 'Bed — rooms/accommodation', 'jaiye-journeys' ),
                                'luggage' => __( 'Luggage', 'jaiye-journeys' ),
                                'passport'=> __( 'Passport/visas', 'jaiye-journeys' ),
                                'sun'     => __( 'Weather', 'jaiye-journeys' ),
                                'route'   => __( 'Itinerary changes', 'jaiye-journeys' ),
                            ],
                        ],
                        'featured' => [ 'type' => 'checkbox', 'label' => __( 'Featured (large card)', 'jaiye-journeys' ) ],
                    ],
                ],
            ],
        ],

        /* ── Reviews ──────────────────────────────────────────────── */
        'reviews' => [
            'title'  => __( 'Reviews', 'jaiye-journeys' ),
            'fields' => [
                'rating_value' => [
                    'type'  => 'text',
                    'label' => __( 'Rating out of 5', 'jaiye-journeys' ),
                    'desc'  => __( 'e.g. 4.9', 'jaiye-journeys' ),
                ],
                'rating_count' => [
                    'type'  => 'number',
                    'label' => __( 'Number of reviews', 'jaiye-journeys' ),
                ],
                'reviews'      => [
                    'type'      => 'repeater',
                    'label'     => __( 'Reviews', 'jaiye-journeys' ),
                    'row_label' => __( 'Review', 'jaiye-journeys' ),
                    'subfields' => [
                        'name'     => [ 'type' => 'text', 'label' => __( 'Name', 'jaiye-journeys' ) ],
                        'location' => [ 'type' => 'text', 'label' => __( 'Location / trip', 'jaiye-journeys' ) ],
                        'rating'   => [ 'type' => 'number', 'label' => __( 'Stars (1–5)', 'jaiye-journeys' ), 'default' => 5 ],
                        'body'     => [ 'type' => 'textarea', 'label' => __( 'Review', 'jaiye-journeys' ), 'rows' => 4 ],
                    ],
                ],
            ],
        ],

        /* ── Social, FAQ & enquiry ────────────────────────────────── */
        'closing' => [
            'title'  => __( 'Social, FAQ & Enquiry', 'jaiye-journeys' ),
            'fields' => [
                'social_images'  => [
                    'type'  => 'gallery',
                    'label' => __( 'Social strip images', 'jaiye-journeys' ),
                ],
                'social_handle'  => [
                    'type'  => 'text',
                    'label' => __( 'Instagram handle', 'jaiye-journeys' ),
                    'desc'  => __( 'Without the @.', 'jaiye-journeys' ),
                ],
                'faqs'           => [
                    'type'      => 'repeater',
                    'label'     => __( 'FAQs', 'jaiye-journeys' ),
                    'desc'      => __( 'Split into two columns automatically.', 'jaiye-journeys' ),
                    'row_label' => __( 'FAQ', 'jaiye-journeys' ),
                    'subfields' => [
                        'question' => [ 'type' => 'text', 'label' => __( 'Question', 'jaiye-journeys' ) ],
                        'answer'   => [ 'type' => 'textarea', 'label' => __( 'Answer', 'jaiye-journeys' ), 'rows' => 4 ],
                    ],
                ],
                'enquiry_image'  => [
                    'type'  => 'image',
                    'label' => __( 'Enquiry section image', 'jaiye-journeys' ),
                ],
                'enquiry_embed'  => [
                    'type'  => 'url',
                    'label' => __( 'Enquiry form URL', 'jaiye-journeys' ),
                    'desc'  => __( 'Tally form link. Rendered as a button beside the image.', 'jaiye-journeys' ),
                ],
            ],
        ],
    ];

    return $schema;
}


/**
 * Flatten the schema to key => field definition.
 *
 * @return array
 */
function jj_trip_all_fields() {
    static $flat = null;
    if ( null !== $flat ) {
        return $flat;
    }

    $flat = [];
    foreach ( jj_trip_schema() as $box ) {
        foreach ( $box['fields'] as $key => $field ) {
            $flat[ $key ] = $field;
        }
    }
    return $flat;
}


/**
 * Get a scalar trip field, with the schema default as fallback.
 *
 * @param string   $key     Field key, without prefix.
 * @param int|null $post_id Post ID. Defaults to current post.
 * @param mixed    $default Override default.
 * @return mixed
 */
function jj_trip_field( $key, $post_id = null, $default = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $value   = get_post_meta( $post_id, JJ_TRIP_META_PREFIX . $key, true );

    if ( '' === $value || null === $value ) {
        if ( null !== $default ) {
            return $default;
        }
        $fields = jj_trip_all_fields();
        return isset( $fields[ $key ]['default'] ) ? $fields[ $key ]['default'] : '';
    }

    return $value;
}


/**
 * Get repeater rows for a field. Always returns an array.
 *
 * @param string   $key     Field key, without prefix.
 * @param int|null $post_id Post ID.
 * @return array
 */
function jj_trip_rows( $key, $post_id = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $rows    = get_post_meta( $post_id, JJ_TRIP_META_PREFIX . $key, true );

    if ( ! is_array( $rows ) ) {
        return [];
    }

    // Drop rows where every value is empty — a half-saved blank row shouldn't render.
    return array_values( array_filter( $rows, function ( $row ) {
        if ( ! is_array( $row ) ) {
            return false;
        }
        foreach ( $row as $value ) {
            if ( '' !== trim( (string) $value ) ) {
                return true;
            }
        }
        return false;
    } ) );
}


/**
 * Get attachment IDs for a gallery field.
 *
 * @param string   $key     Field key, without prefix.
 * @param int|null $post_id Post ID.
 * @return int[]
 */
function jj_trip_gallery( $key, $post_id = null ) {
    $raw = jj_trip_field( $key, $post_id );
    if ( empty( $raw ) ) {
        return [];
    }

    $ids = array_map( 'absint', explode( ',', (string) $raw ) );
    return array_values( array_filter( $ids ) );
}


/**
 * Split a textarea into trimmed non-empty lines.
 *
 * @param string $value Raw textarea value.
 * @return string[]
 */
function jj_trip_lines( $value ) {
    if ( empty( $value ) ) {
        return [];
    }

    $lines = preg_split( '/\r\n|\r|\n/', (string) $value );
    $lines = array_map( 'trim', $lines );
    return array_values( array_filter( $lines, 'strlen' ) );
}


/**
 * Return the inline SVG for a trip note icon.
 *
 * Icons are inlined rather than loaded as files so they inherit currentColor
 * and add no extra requests. Output is a fixed internal string — safe to echo.
 *
 * @param string $icon Icon key from the schema's select options.
 * @return string SVG markup.
 */
function jj_trip_note_icon( $icon ) {

    $icons = [
        'plane'    => '<path d="M2 13l19-7-7 19-2.5-8.5L2 13z"/>',
        'bed'      => '<path d="M3 17v-6h18v6"/><path d="M3 11V7"/><path d="M21 17v2"/><path d="M3 17v2"/><circle cx="7.5" cy="10.5" r="1.5"/>',
        'luggage'  => '<rect x="5" y="7" width="14" height="13" rx="2"/><path d="M9 7V4h6v3"/><path d="M10 11v5"/><path d="M14 11v5"/>',
        'passport' => '<rect x="5" y="3" width="14" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M9 17h6"/>',
        'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.9 4.9l1.4 1.4"/><path d="M17.7 17.7l1.4 1.4"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M4.9 19.1l1.4-1.4"/><path d="M17.7 6.3l1.4-1.4"/>',
        'route'    => '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8.5 6H14a4 4 0 0 1 0 8H10a4 4 0 0 0 0 8h-.5"/>',
    ];

    $path = isset( $icons[ $icon ] ) ? $icons[ $icon ] : $icons['route'];

    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}


/**
 * Sanitise a single value according to its field type.
 *
 * @param mixed  $value Raw submitted value.
 * @param string $type  Field type from the schema.
 * @return mixed
 */
function jj_trip_sanitise_value( $value, $type ) {
    switch ( $type ) {
        case 'textarea':
            return sanitize_textarea_field( (string) $value );

        case 'url':
            return esc_url_raw( trim( (string) $value ) );

        case 'number':
            return '' === trim( (string) $value ) ? '' : (string) absint( $value );

        case 'meter':
            return (string) max( 0, min( 100, absint( $value ) ) );

        case 'image':
            return (string) absint( $value );

        case 'checkbox':
            return $value ? '1' : '';

        case 'gallery':
            $ids = array_map( 'absint', explode( ',', (string) $value ) );
            $ids = array_values( array_filter( $ids ) );
            return implode( ',', $ids );

        case 'text':
        case 'select':
        default:
            return sanitize_text_field( (string) $value );
    }
}
