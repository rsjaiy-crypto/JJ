<?php
/**
 * One-click seeder for sample trips.
 *
 * Creates the Peru sample trip plus stubs for jj-bali and jj-cape-town, which
 * the homepage already links to (front-page.php). Idempotent: existing trips
 * are left alone, so clicking twice is safe.
 *
 * This exists because the theme is deployed over SFTP with no WP-CLI
 * available, so there's no other scripted way to get content into the
 * database. Delete this file once the real trips are written.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;


/**
 * Register the Tools → Seed JJ Trips page.
 */
function jj_trip_seed_menu() {
    add_management_page(
        __( 'Seed JJ Trips', 'jaiye-journeys' ),
        __( 'Seed JJ Trips', 'jaiye-journeys' ),
        'manage_options',
        'jj-seed-trips',
        'jj_trip_seed_page'
    );
}
add_action( 'admin_menu', 'jj_trip_seed_menu' );


/**
 * Render the seeder screen and handle its form submission.
 */
function jj_trip_seed_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to do this.', 'jaiye-journeys' ) );
    }

    $results = [];

    if (
        isset( $_POST['jj_seed_submit'], $_POST['jj_seed_nonce'] )
        && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['jj_seed_nonce'] ) ), 'jj_seed_trips' )
    ) {
        $results = jj_trip_run_seed();
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Seed JJ Trips', 'jaiye-journeys' ); ?></h1>

        <p>
            <?php esc_html_e( 'Creates the Peru sample trip with placeholder content, plus stubs for the Bali and Cape Town trips the homepage links to. Existing trips are never overwritten.', 'jaiye-journeys' ); ?>
        </p>

        <?php if ( $results ) : ?>
            <div class="notice notice-success">
                <ul style="margin:12px 0;">
                    <?php foreach ( $results as $line ) : ?>
                        <li><?php echo esc_html( $line ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <p>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'trip' ) ); ?>" class="button button-secondary">
                    <?php esc_html_e( 'View all trips', 'jaiye-journeys' ); ?>
                </a>
            </p>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field( 'jj_seed_trips', 'jj_seed_nonce' ); ?>
            <p>
                <button type="submit" name="jj_seed_submit" value="1" class="button button-primary">
                    <?php esc_html_e( 'Seed trips now', 'jaiye-journeys' ); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}


/**
 * Import a theme image into the media library, once.
 *
 * Tracked by a meta key so repeated runs reuse the same attachment rather
 * than filling the library with duplicates.
 *
 * @param string $filename File name inside assets/images/.
 * @return int Attachment ID, or 0 on failure.
 */
function jj_trip_seed_image( $filename ) {

    // Already imported?
    $existing = get_posts( [
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_jj_seed_source',   // phpcs:ignore WordPress.DB.SlowDBQuery
        'meta_value'     => $filename,           // phpcs:ignore WordPress.DB.SlowDBQuery
    ] );

    if ( ! empty( $existing ) ) {
        return (int) $existing[0];
    }

    $source = get_template_directory() . '/assets/images/' . $filename;
    if ( ! file_exists( $source ) ) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload = wp_upload_bits( $filename, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    if ( ! empty( $upload['error'] ) ) {
        return 0;
    }

    $filetype = wp_check_filetype( $upload['file'], null );

    $attachment_id = wp_insert_attachment( [
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ], $upload['file'] );

    if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
        return 0;
    }

    wp_update_attachment_metadata(
        $attachment_id,
        wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
    );

    update_post_meta( $attachment_id, '_jj_seed_source', $filename );

    return (int) $attachment_id;
}


/**
 * Turn a list of theme filenames into a comma-separated attachment ID string.
 *
 * @param string[] $filenames File names inside assets/images/.
 * @return string
 */
function jj_trip_seed_gallery( $filenames ) {
    $ids = [];

    foreach ( $filenames as $filename ) {
        $id = jj_trip_seed_image( $filename );
        if ( $id ) {
            $ids[] = $id;
        }
    }

    return implode( ',', $ids );
}


/**
 * Create the seed trips.
 *
 * @return string[] Human-readable result lines.
 */
function jj_trip_run_seed() {

    $results = [];

    foreach ( jj_trip_seed_definitions() as $slug => $definition ) {

        $existing = get_page_by_path( $slug, OBJECT, 'trip' );

        if ( $existing ) {
            $results[] = sprintf(
                /* translators: %s: trip slug */
                __( 'Skipped "%s" — a trip with that slug already exists.', 'jaiye-journeys' ),
                $slug
            );
            continue;
        }

        $post_id = wp_insert_post( [
            'post_type'    => 'trip',
            'post_status'  => 'publish',
            'post_title'   => $definition['title'],
            'post_name'    => $slug,
            'post_content' => '',
        ], true );

        if ( is_wp_error( $post_id ) ) {
            $results[] = sprintf(
                /* translators: 1: trip slug, 2: error message */
                __( 'Failed to create "%1$s": %2$s', 'jaiye-journeys' ),
                $slug,
                $post_id->get_error_message()
            );
            continue;
        }

        if ( ! empty( $definition['hero'] ) ) {
            $hero_id = jj_trip_seed_image( $definition['hero'] );
            if ( $hero_id ) {
                set_post_thumbnail( $post_id, $hero_id );
            }
        }

        foreach ( $definition['meta'] as $key => $value ) {
            update_post_meta( $post_id, JJ_TRIP_META_PREFIX . $key, $value );
        }

        $results[] = sprintf(
            /* translators: 1: trip title, 2: permalink */
            __( 'Created "%1$s" at %2$s', 'jaiye-journeys' ),
            $definition['title'],
            get_permalink( $post_id )
        );
    }

    // New posts mean new permalinks — make sure the rules know about them.
    flush_rewrite_rules( false );

    return $results;
}


/**
 * The seed content itself.
 *
 * Peru is fully populated as the review sample. Bali and Cape Town are
 * deliberately light — they exist to stop the homepage links 404ing and are
 * meant to be filled in properly in wp-admin.
 *
 * @return array
 */
function jj_trip_seed_definitions() {

    return [

        /* ── Peru: the full sample ────────────────────────────────── */
        'jj-peru' => [
            'title' => "The Director's Cut 2: Peru",
            'hero'  => 'hero-peru.jpg',
            'meta'  => [
                'destination'     => 'Peru',
                'vibe_tags'       => 'Small group, High altitude, Bucket list',
                'intro_statement' => 'Nine days across the Sacred Valley and Machu Picchu, run the way we would actually do it — no queue, no filler, no group-trip drama.',
                'collage_images'  => jj_trip_seed_gallery( [
                    'trip-peru.jpg',
                    'travel-terrace.jpg',
                    'travel-tea.jpg',
                    'travel-sea.jpg',
                    'travel-lobby.jpg',
                ] ),

                'duration'     => '9 days',
                'price_from'   => '3250',
                'group_size'   => 'Max 12 travellers',
                'welcome_copy' => "Peru is the trip people ask us about most, and it earns the hype. We do the Sacred Valley properly rather than the version where you are herded through a checklist behind a laminated sign.\n\nSmall group, boutique stays, and a sunrise at Machu Picchu that actually feels like yours. We build in real downtime too, because nobody needs to be camera-ready at 6am every day of their holiday.\n\nThis is for the traveller who wants the bucket-list moment without the bucket-list chaos. We handle the logistics. You show up.",

                'meter_pace'       => '65',
                'meter_culture'    => '85',
                'meter_food'       => '75',
                'meter_adventure'  => '80',
                'meter_nightlife'  => '35',
                'meter_relaxation' => '45',

                'booking_url'   => 'https://tally.so/r/1ApXBp',
                'enquiry_url'   => '',
                'payment_terms' => 'A holding deposit secures your spot, refundable until the cohort reaches its minimum. Balance due 60 days before departure. Payment plans and Klarna available at checkout.',

                'departures' => [
                    [
                        'year'         => '2027',
                        'dates'        => '14 – 22 May',
                        'availability' => 'Places available',
                        'status'       => 'available',
                        'book_url'     => '',
                    ],
                    [
                        'year'         => '2027',
                        'dates'        => '10 – 18 September',
                        'availability' => 'Only 3 places left',
                        'status'       => 'limited',
                        'book_url'     => '',
                    ],
                    [
                        'year'         => '2028',
                        'dates'        => '12 – 20 May',
                        'availability' => 'Places available',
                        'status'       => 'available',
                        'book_url'     => '',
                    ],
                ],

                'carousel_images' => jj_trip_seed_gallery( [
                    'trip-peru.jpg',
                    'travel-terrace.jpg',
                    'travel-riad-pool.jpg',
                    'travel-tuscany.jpg',
                ] ),

                'included_groups' => [
                    [
                        'group_title' => 'Accommodation',
                        'items'       => "8 nights in boutique hotels\nDaily breakfast\nPrivate en-suite room (twin or double)",
                    ],
                    [
                        'group_title' => 'Experiences',
                        'items'       => "Sunrise entry to Machu Picchu with a private guide\nGuided walking tour of Cusco old town\nPisac ruins and market\nOllantaytambo fortress\nWelcome and farewell dinners",
                    ],
                    [
                        'group_title' => 'Travel',
                        'items'       => "All internal transfers\nScenic train to Aguas Calientes\nAirport pickup and drop-off",
                    ],
                ],

                'not_included' => "International flights\nTravel insurance (required)\nMost lunches and dinners\nOptional add-on excursions\nTips and personal spending",

                'route_copy' => 'Cusco to the Sacred Valley, on to Aguas Calientes for Machu Picchu, then a relaxed return to Cusco for the final two nights.',

                'strip_images' => jj_trip_seed_gallery( [
                    'travel-sea.jpg',
                    'travel-tea.jpg',
                    'travel-terrace.jpg',
                    'travel-tuscany.jpg',
                    'travel-lobby.jpg',
                    'travel-yacht.jpg',
                ] ),

                'days' => [
                    [
                        'location' => 'Cusco',
                        'headline' => 'Arrival and altitude',
                        'body'     => 'Land, settle in, and take it slowly while you acclimatise. Coca tea on the terrace, an easy welcome dinner, and an early night.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-lobby.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'travel-tea.jpg' ),
                    ],
                    [
                        'location' => 'Cusco',
                        'headline' => 'Cusco, properly',
                        'body'     => 'A guided walk through the old town, San Pedro Market, and the best coffee in the city. Afternoon free.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-terrace.jpg' ),
                        'image_2'  => '',
                    ],
                    [
                        'location' => 'Sacred Valley',
                        'headline' => 'Into the valley',
                        'body'     => 'Pisac ruins and market in the morning, then on to a boutique stay in the heart of the Sacred Valley.',
                        'image_1'  => (string) jj_trip_seed_image( 'trip-peru.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'travel-tuscany.jpg' ),
                    ],
                    [
                        'location' => 'Ollantaytambo',
                        'headline' => 'Fortress town',
                        'body'     => 'Explore the terraces and fortress before the scenic train down to Aguas Calientes.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-sea.jpg' ),
                        'image_2'  => '',
                    ],
                    [
                        'location' => 'Machu Picchu',
                        'headline' => 'The main event',
                        'body'     => 'Sunrise entry with a private guide, ahead of the day-trip crowds. The rest of the day is yours.',
                        'image_1'  => (string) jj_trip_seed_image( 'trip-peru.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'travel-terrace.jpg' ),
                    ],
                    [
                        'location' => 'Cusco',
                        'headline' => 'Back, slowly',
                        'body'     => 'A relaxed return journey, free afternoon, spa time if you want it.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-riad-pool.jpg' ),
                        'image_2'  => '',
                    ],
                    [
                        'location' => 'Cusco',
                        'headline' => 'Free day',
                        'body'     => 'Optional add-ons available, or simply enjoy Cusco at your own pace.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-tea.jpg' ),
                        'image_2'  => '',
                    ],
                    [
                        'location' => 'Cusco',
                        'headline' => 'Farewell dinner',
                        'body'     => 'The whole group together for one last night, closing the trip out properly.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-lobby.jpg' ),
                        'image_2'  => '',
                    ],
                    [
                        'location' => 'Departure',
                        'headline' => 'No-rush checkout',
                        'body'     => 'Transfers to the airport whenever your flight is. Late checkout available on request.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-sea.jpg' ),
                        'image_2'  => '',
                    ],
                ],

                'trip_notes' => [
                    [
                        'title'    => 'Arrivals & Departures',
                        'body'     => "Fly into Cusco (CUZ), usually via Lima. Arrivals from 10am on day one; departures any time on day nine. We will send flight guidance once your place is confirmed, and the Ticketing Desk can book it for you.",
                        'icon'     => 'plane',
                        'featured' => '1',
                    ],
                    [
                        'title'    => 'Room upgrades',
                        'body'     => 'Single occupancy and suite upgrades are available at a supplement on request.',
                        'icon'     => 'bed',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Luggage',
                        'body'     => 'One checked bag plus hand luggage. A small day pack is essential for Machu Picchu.',
                        'icon'     => 'luggage',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Visas & passports',
                        'body'     => 'UK passport holders do not need a visa for stays under 183 days. Your passport must have six months validity.',
                        'icon'     => 'passport',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Weather',
                        'body'     => 'Dry season days are 18–22°C and sunny; nights drop close to freezing. Layers are non-negotiable.',
                        'icon'     => 'sun',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Itinerary changes',
                        'body'     => 'Weather and rail schedules occasionally shift the running order. The experiences stay the same.',
                        'icon'     => 'route',
                        'featured' => '',
                    ],
                ],

                'rating_value' => '4.9',
                'rating_count' => '38',

                'reviews' => [
                    [
                        'name'     => 'Placeholder reviewer one',
                        'location' => 'Sample review',
                        'rating'   => '5',
                        'body'     => 'Placeholder review copy for layout purposes — swap this for a real traveller review before publishing.',
                    ],
                    [
                        'name'     => 'Placeholder reviewer two',
                        'location' => 'Sample review',
                        'rating'   => '5',
                        'body'     => 'Placeholder review copy for layout purposes — swap this for a real traveller review before publishing.',
                    ],
                    [
                        'name'     => 'Placeholder reviewer three',
                        'location' => 'Sample review',
                        'rating'   => '4',
                        'body'     => 'Placeholder review copy for layout purposes — swap this for a real traveller review before publishing.',
                    ],
                ],

                'social_images' => jj_trip_seed_gallery( [
                    'travel-tea.jpg',
                    'travel-sea.jpg',
                    'travel-terrace.jpg',
                    'travel-tuscany.jpg',
                ] ),
                'social_handle' => 'jaiyejourneys',

                'faqs' => [
                    [
                        'question' => 'How fit do I need to be?',
                        'answer'   => 'Moderate. You should be comfortable walking for two to three hours and managing uneven ground and steps at altitude.',
                    ],
                    [
                        'question' => 'Will altitude be a problem?',
                        'answer'   => 'We build in two acclimatisation days in Cusco before anything strenuous, which is what most itineraries skip.',
                    ],
                    [
                        'question' => 'Can I come solo?',
                        'answer'   => 'Most people do. You can share a twin with another solo traveller or pay the single supplement for your own room.',
                    ],
                    [
                        'question' => 'Are flights included?',
                        'answer'   => 'No. We recommend booking once your place is confirmed, and the Ticketing Desk can find the right fare for you.',
                    ],
                    [
                        'question' => 'What happens if the group does not fill?',
                        'answer'   => 'Holding deposits are returned in full. Nothing becomes non-refundable until the trip is green lit.',
                    ],
                    [
                        'question' => 'Are dietary requirements catered for?',
                        'answer'   => 'Yes. You will get a preference sheet ahead of departure and we brief every venue in advance.',
                    ],
                ],

                'enquiry_image' => (string) jj_trip_seed_image( 'travel-terrace.jpg' ),
                'enquiry_embed' => 'https://tally.so/r/1ApXBp',
            ],
        ],

        /* ── Bali stub: fixes the homepage 404 ────────────────────── */
        'jj-bali' => [
            'title' => 'The Islands Edit',
            'hero'  => 'trip-bali.jpg',
            'meta'  => [
                'destination'     => 'Bali, Gilis & Komodo, Indonesia',
                'vibe_tags'       => 'Islands, Small group, Slow travel',
                'intro_statement' => 'Placeholder intro — replace this with the real Islands Edit copy before publishing.',
                'duration'        => 'TBC',
                'price_from'      => '',
                'group_size'      => 'Max 12 travellers',
                'welcome_copy'    => 'Placeholder copy. This trip was created so the homepage link resolves; fill it in properly in Trips → The Islands Edit.',
                'booking_url'     => 'https://tally.so/r/RGVxVj',
                'payment_terms'   => 'A holding deposit secures your spot, refundable until the cohort reaches its minimum.',
            ],
        ],

        /* ── Cape Town stub: fixes the homepage 404 ───────────────── */
        'jj-cape-town' => [
            'title' => 'The Cape Town Edit',
            'hero'  => 'trip-cape-town.jpg',
            'meta'  => [
                'destination'     => 'Cape Town, South Africa',
                'vibe_tags'       => 'City, Coast, Food',
                'intro_statement' => 'Placeholder intro — replace this with the real Cape Town Edit copy before publishing.',
                'duration'        => 'TBC',
                'price_from'      => '',
                'group_size'      => 'Max 12 travellers',
                'welcome_copy'    => 'Placeholder copy. This trip was created so the homepage link resolves; fill it in properly in Trips → The Cape Town Edit.',
                'booking_url'     => 'https://tally.so/r/aQa9D2',
                'payment_terms'   => 'A holding deposit secures your spot, refundable until the cohort reaches its minimum.',
            ],
        ],
    ];
}
