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
                'region'          => 'south-america',
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

        /* ── Marrakech: the BTL chapter sample ────────────────────── */
        'btl-marrakech' => [
            'title' => 'Chapter One: Marrakech',
            'hero'  => 'btl-hero.jpg',
            'meta'  => [
                'brand'           => 'btl',
                'destination'     => 'Marrakech',
                'region'          => 'africa',
                'vibe_tags'       => 'Riad buyout, Literary, Slow',
                'intro_statement' => 'Six days in a private riad near Bab Aylane, built around one loud city and a door that shuts it out completely.',
                'collage_images'  => jj_trip_seed_gallery( [
                    'trip-morocco.jpg',
                    'travel-riad-pool.jpg',
                    'btl-reading-pool.jpg',
                    'travel-tea.jpg',
                    'btl-kindle-beach.jpg',
                ] ),

                'duration'     => '6 days',
                'price_from'   => '2850',
                'group_size'   => 'Twelve rooms, whole-riad buyout',
                'welcome_copy' => "Marrakech holds two cities at once: the medina, dense and alive with the souks, and the quiet interior of a riad built to shut all of it out.\n\nPalazzo Montefiore sits within walking distance of Bab Aylane — twelve rooms around a courtyard, an in-house hammam, and a rooftop that catches the evening light over the old city. You will not hear the souks from inside. That is not an accident; it is the entire point of a riad.\n\nMost of the week stays close in, restful and unhurried. One day leaves the city behind entirely for the Agafay, a landscape that looks more like the moon than a desert.",

                // Weighted toward rest: this chapter sits in the margins.
                'meter_pace'       => '30',
                'meter_culture'    => '75',
                'meter_food'       => '70',
                'meter_adventure'  => '45',
                'meter_nightlife'  => '20',
                'meter_relaxation' => '90',

                'booking_url'   => 'https://tally.so/r/Gxq2JL',
                'enquiry_url'   => '',
                'payment_terms' => 'A holding deposit secures your spot, refundable until the cohort reaches its minimum. Balance due 60 days before departure. Payment plans and Klarna available at checkout.',

                'departures' => [
                    [
                        'year'         => '2027',
                        'dates'        => '18 – 23 March',
                        'availability' => 'Waitlist open',
                        'status'       => 'limited',
                        'book_url'     => '',
                    ],
                ],

                'carousel_images' => jj_trip_seed_gallery( [
                    'travel-riad-pool.jpg',
                    'trip-morocco.jpg',
                    'travel-tea.jpg',
                    'travel-terrace.jpg',
                ] ),

                'inclusions_image' => (string) jj_trip_seed_image( 'travel-riad-pool.jpg' ),

                'included_groups' => [
                    [
                        'group_title' => 'The riad',
                        'items'       => "Full private buyout of Palazzo Montefiore\n5 nights, twelve rooms around the courtyard\nAll breakfasts and dinners\nUse of the in-house hammam and sun terrace",
                    ],
                    [
                        'group_title' => 'The chapter',
                        'items'       => "The Agafay desert day in full\nGuided walk through the souks\nLe Jardin Secret, the Saadian-era garden\nAfternoon tea at the Royal Mansour\nRooftop book club",
                    ],
                    [
                        'group_title' => 'Travel',
                        'items'       => "All internal transfers\nAirport pickup and drop-off",
                    ],
                ],

                'not_included' => "Flights\nTravel insurance (required)\nAdditional spa treatments\nShopping in the souks\nTips and personal spending",

                'route_copy' => 'Everything runs from the riad near Bab Aylane, apart from one day out to the Agafay, less than an hour from the medina.',

                'strip_images' => jj_trip_seed_gallery( [
                    'trip-morocco.jpg',
                    'travel-riad-pool.jpg',
                    'travel-tea.jpg',
                    'btl-reading-pool.jpg',
                    'travel-terrace.jpg',
                    'btl-kindle-beach.jpg',
                ] ),

                // BTL day naming: named chapters rather than Day 1..N.
                'days' => [
                    [
                        'day_name' => 'The Prologue',
                        'location' => 'Palazzo Montefiore',
                        'headline' => 'Arrival, and the door closes',
                        'body'     => 'Arrival at the riad. A welcome gala on the rooftop in "Dust & Gold" neutrals, as the city noise falls away behind you.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-riad-pool.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'travel-terrace.jpg' ),
                    ],
                    [
                        'day_name' => 'Grounding',
                        'location' => 'The courtyard',
                        'headline' => 'An unstructured day',
                        'body'     => 'No itinerary to follow. Just the riad, the courtyard, the hammam, and time to arrive properly.',
                        'image_1'  => (string) jj_trip_seed_image( 'btl-reading-pool.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'travel-tea.jpg' ),
                    ],
                    [
                        'day_name' => 'Secrets of the Souks',
                        'location' => 'The medina',
                        'headline' => 'Out into the noise, briefly',
                        'body'     => 'Le Jardin Secret, a hidden Saadian-era garden, followed by a guided walk through the souks and dinner in the medina.',
                        'image_1'  => (string) jj_trip_seed_image( 'trip-morocco.jpg' ),
                        'image_2'  => '',
                    ],
                    [
                        'day_name' => 'The Agafay',
                        'location' => 'Agafay desert',
                        'headline' => "The chapter's signature day",
                        'body'     => 'A private desert camp from mid-afternoon: camel or quad ride at sunset, hammam, fire dancing, and Gnawa music under the stars.',
                        'image_1'  => (string) jj_trip_seed_image( 'hero-desert.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'trip-morocco.jpg' ),
                    ],
                    [
                        'day_name' => 'The Crescendo',
                        'location' => 'Rooftop & Royal Mansour',
                        'headline' => 'Book club, then tea',
                        'body'     => 'Book club on the rooftop. Farewell afternoon tea at the Royal Mansour, closing the week on a quiet, looked-after note.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-tea.jpg' ),
                        'image_2'  => (string) jj_trip_seed_image( 'btl-kindle-beach.jpg' ),
                    ],
                    [
                        'day_name' => 'The Epilogue',
                        'location' => 'Palazzo Montefiore',
                        'headline' => 'No-rush departure',
                        'body'     => 'Stay by the pool until the last possible moment. Transfers whenever your flight is.',
                        'image_1'  => (string) jj_trip_seed_image( 'travel-riad-pool.jpg' ),
                        'image_2'  => '',
                    ],
                ],

                'trip_notes' => [
                    [
                        'title'    => 'Arrivals & Departures',
                        'body'     => "Fly into Marrakech Menara (RAK). Arrivals from 1pm on day one; departures any time on day six. The riad sits inside the medina near Bab Aylane, so the final approach is on foot — we meet you and handle the bags.",
                        'icon'     => 'plane',
                        'featured' => '1',
                    ],
                    [
                        'title'    => 'Rooms',
                        'body'     => 'Twelve rooms across the riad, a mix of doubles and suites. Solo occupancy and suite upgrades confirmed once the buyout is finalised.',
                        'icon'     => 'bed',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Luggage',
                        'body'     => 'One checked bag plus hand luggage. Medina lanes are narrow, so a hard shell with good wheels earns its keep.',
                        'icon'     => 'luggage',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Visas & passports',
                        'body'     => 'UK passport holders do not need a visa for stays under 90 days. Your passport must have six months validity.',
                        'icon'     => 'passport',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Weather',
                        'body'     => 'March in Marrakech runs 20–24°C by day and cools sharply after dark. The Agafay evening is genuinely cold — bring a proper layer.',
                        'icon'     => 'sun',
                        'featured' => '',
                    ],
                    [
                        'title'    => 'Itinerary changes',
                        'body'     => 'The Agafay day occasionally shifts by 24 hours for weather. Everything else runs from the riad, so the week holds its shape.',
                        'icon'     => 'route',
                        'featured' => '',
                    ],
                ],

                'rating_value' => '',
                'rating_count' => '',
                'reviews'      => [],

                'social_images' => jj_trip_seed_gallery( [
                    'travel-riad-pool.jpg',
                    'trip-morocco.jpg',
                    'travel-tea.jpg',
                    'btl-reading-pool.jpg',
                ] ),
                'social_handle' => 'jaiyejourneys',

                'faqs' => [
                    [
                        'question' => 'Is there a waitlist?',
                        'answer'   => 'Yes. Join to be notified the moment booking opens and to get first access before spaces are shared more widely.',
                    ],
                    [
                        'question' => "What happens if the cohort doesn't reach its minimum?",
                        'answer'   => 'Holding deposits are returned in full. Nothing becomes non-refundable until the retreat is officially green lit.',
                    ],
                    [
                        'question' => 'Do I have to do the reading?',
                        'answer'   => 'No. The book is an invitation, not homework. Plenty of people come for the riad and the quiet.',
                    ],
                    [
                        'question' => 'Can I come solo?',
                        'answer'   => 'Most guests do. Solo, or with the one friend who actually reads.',
                    ],
                    [
                        'question' => 'Am I responsible for my own flights?',
                        'answer'   => 'Yes. We recommend booking as soon as your place is confirmed, and the Ticketing Desk can find the right fare.',
                    ],
                    [
                        'question' => 'Are dietary requirements catered for?',
                        'answer'   => 'Yes. You will receive a preference sheet ahead of the retreat and the riad kitchen is briefed in advance.',
                    ],
                ],

                'enquiry_image' => (string) jj_trip_seed_image( 'btl-reading-pool.jpg' ),
                'enquiry_embed' => 'https://tally.so/r/Gxq2JL',
            ],
        ],

        /* ── Coming soon: future BTL chapters ─────────────────────────
           Previously hardcoded in the "On the Horizon" strip on the Our
           Journeys page. They're now real trips so each has its own
           linkable, shareable waitlist page and its own demand signal
           ahead of green-lighting the villa contract.
           ------------------------------------------------------------ */
        'btl-sintra' => [
            'title' => 'Chapter Two: Sintra',
            'hero'  => 'travel-tuscany.jpg',
            'meta'  => [
                'brand'           => 'btl',
                'status'          => 'coming-soon',
                'destination'     => 'Sintra, Portugal',
                'region'          => 'europe',
                'vibe_tags'       => 'Palaces, Misty hills, Slow',
                'intro_statement' => 'A chapter written in the fog: palaces, pine forest, and the kind of quiet that makes a long book feel short.',
                'welcome_copy'    => "Placeholder premise copy — replace before this page goes out widely.\n\nSintra sits under cloud for much of the year, which is precisely the point. Gardens, palaces, and a cool grey light that makes staying in with a book feel like the correct decision rather than a wasted day.",
                'duration'        => '5 days',
                'meter_pace'      => '25',
                'meter_relaxation'=> '90',
                'meter_culture'   => '80',
                'booking_url'     => 'https://tally.so/r/Gxq2JL',
                'collage_images'  => jj_trip_seed_gallery( [
                    'travel-tuscany.jpg',
                    'travel-terrace.jpg',
                    'btl-reading-pool.jpg',
                ] ),
                'departures'      => [
                    [
                        'year'         => '2027',
                        'dates'        => 'September',
                        'availability' => 'Waitlist open',
                        'status'       => 'limited',
                        'book_url'     => '',
                    ],
                ],
            ],
        ],

        'btl-oaxaca' => [
            'title' => 'Chapter Three: Oaxaca',
            'hero'  => 'travel-sea.jpg',
            'meta'  => [
                'brand'           => 'btl',
                'status'          => 'coming-soon',
                'destination'     => 'Oaxaca, Mexico',
                'region'          => 'north-america',
                'vibe_tags'       => 'Colour, Food, Craft',
                'intro_statement' => 'Warmth, colour and a courtyard to read in, somewhere between the markets and the mezcal.',
                'welcome_copy'    => "Placeholder premise copy — replace before this page goes out widely.\n\nOaxaca is the loudest, most colourful chapter planned so far, built around a courtyard house and a city that takes its food and its craft as seriously as anywhere on earth.",
                'duration'        => '6 days',
                'meter_pace'      => '40',
                'meter_food'      => '95',
                'meter_culture'   => '90',
                'booking_url'     => 'https://tally.so/r/Gxq2JL',
                'collage_images'  => jj_trip_seed_gallery( [
                    'travel-sea.jpg',
                    'travel-tea.jpg',
                    'travel-lobby.jpg',
                ] ),
                'departures'      => [
                    [
                        'year'         => '2028',
                        'dates'        => 'April',
                        'availability' => 'Waitlist open',
                        'status'       => 'limited',
                        'book_url'     => '',
                    ],
                ],
            ],
        ],

        'btl-tuscany' => [
            'title' => 'Chapter Four: Tuscany',
            'hero'  => 'travel-tuscany.jpg',
            'meta'  => [
                'brand'           => 'btl',
                'status'          => 'coming-soon',
                'destination'     => 'Tuscany, Italy',
                'region'          => 'europe',
                'vibe_tags'       => 'Villa, Long lunches, Slow',
                'intro_statement' => 'A villa, a long table, and six days where the only fixed appointment is lunch.',
                'welcome_copy'    => "Placeholder premise copy — replace before this page goes out widely.\n\nThe most indulgent chapter on the map: a private villa, a cook, a pool, and absolutely nowhere to be.",
                'duration'        => '6 days',
                'meter_pace'      => '20',
                'meter_relaxation'=> '95',
                'meter_food'      => '90',
                'booking_url'     => 'https://tally.so/r/Gxq2JL',
                'collage_images'  => jj_trip_seed_gallery( [
                    'travel-tuscany.jpg',
                    'travel-terrace.jpg',
                    'travel-riad-pool.jpg',
                ] ),
                'departures'      => [
                    [
                        'year'         => '2028',
                        'dates'        => 'September',
                        'availability' => 'Waitlist open',
                        'status'       => 'limited',
                        'book_url'     => '',
                    ],
                ],
            ],
        ],

        /* ── Bali stub: fixes the homepage 404 ────────────────────── */
        'jj-bali' => [
            'title' => 'The Islands Edit',
            'hero'  => 'trip-bali.jpg',
            'meta'  => [
                'destination'     => 'Bali, Gilis & Komodo, Indonesia',
                'region'          => 'asia',
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
                'region'          => 'africa',
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
