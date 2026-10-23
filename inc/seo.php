<?php
/**
 * Search and AI readability.
 *
 * - The Between the Lines FAQ copy, kept in one place so the page, its
 *   FAQPage structured data and llms.txt can't drift apart.
 * - Structured data added to Rank Math's own graph (rank_math/json_ld), so
 *   there is one JSON-LD block per page, never two that clash: FAQPage on
 *   the Between the Lines page, an Event per chapter on BTL trip pages.
 *   Each is skipped if Rank Math already outputs that type for the page.
 * - Alt text fallback for Media Library images with none filled in.
 * - /llms.txt, served by the theme only while Rank Math's own llms.txt
 *   module is switched off.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;


/* ── Between the Lines FAQ ───────────────────────────────────────── */

/**
 * The Between the Lines FAQ, as shown on the page and in its structured
 * data, word for word.
 *
 * Prices and dates here are typed copy, not read from the trips. If the
 * Marrakesh price or dates change, change them here too.
 *
 * @return array[] Each [ 'question' => …, 'answer' => … ].
 */
function jj_btl_faqs() {
    return [
        [
            'question' => __( 'What is a reading retreat?', 'jaiye-journeys' ),
            'answer'   => __( 'A reading retreat gives you days set aside for reading, somewhere beautiful, with people who love books as much as you do. Between the Lines runs six-day residencies: long hours with your book, book discussions with other guests, and local culture woven through the week.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'Do I need to be in a book club?', 'jaiye-journeys' ),
            'answer'   => __( 'No. Solo readers come on their own and book clubs come together. You don\'t need to read the same book as anyone else.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'Can I come on my own?', 'jaiye-journeys' ),
            'answer'   => __( 'Yes. Plenty of guests travel solo. You arrive to a group of readers and a host who has handled every detail.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'Where are the retreats?', 'jaiye-journeys' ),
            'answer'   => __( 'Volume 1 takes place in Marrakesh, Morocco, in March 2027. Volume 2 takes place in Sintra, Portugal, in October 2027.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'How long is each residency?', 'jaiye-journeys' ),
            'answer'   => __( 'Six days, from The Prologue (arrival and welcome) to The Epilogue (farewell breakfast and an unhurried departure).', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'What is the No-Rush Policy?', 'jaiye-journeys' ),
            'answer'   => __( 'We hold the whole property for the residency, so you keep your room on your last day. No 10am checkout. Take one last swim and leave when you\'re ready.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'How much does it cost?', 'jaiye-journeys' ),
            'answer'   => __( 'Marrakesh starts from £2,850 per person sharing, or £3,500 for a solo room. People on The List get early access pricing.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'How many guests come?', 'jaiye-journeys' ),
            'answer'   => __( 'Between 10 and 20 readers per Chapter. Each Chapter needs 10 guests to go ahead.', 'jaiye-journeys' ),
        ],
        [
            'question' => __( 'Who hosts?', 'jaiye-journeys' ),
            'answer'   => __( 'Steph, the founder of Jaiye Journeys. She reads close to 300 books a year, spent years as a creative director and producer, and has led group travel across 12+ countries. She\'s on site at every residency.', 'jaiye-journeys' ),
        ],
    ];
}


/* ── Structured data ─────────────────────────────────────────────── */

// Trips (by slug) that get Event structured data: the two Between the Lines
// volumes. Add a slug here when a new volume should have it.
define( 'JJ_EVENT_TRIP_SLUGS', [ 'btl-marrakech', 'sintra-mist-manuscripts' ] );

/**
 * Is Rank Math printing the page's JSON-LD graph?
 *
 * @return bool
 */
function jj_rank_math_schema_active() {
    if ( ! class_exists( '\RankMath\Helper' ) ) {
        return false;
    }
    return \RankMath\Helper::is_module_active( 'rich-snippet' );
}

/**
 * Does the graph already hold an entity of this type?
 *
 * @param array  $data Graph entities.
 * @param string $type Schema type, e.g. "FAQPage".
 * @return bool
 */
function jj_schema_has_type( $data, $type ) {
    foreach ( (array) $data as $entity ) {
        if ( is_array( $entity ) && isset( $entity['@type'] ) && in_array( $type, (array) $entity['@type'], true ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Start and end dates of a departure, read from its free-text Dates and
 * Year fields: "18 – 23 March", "28 March – 2 April", "18 to 23 March".
 *
 * Unlike jj_trip_departure_ts() this never guesses: a row without a day
 * and a month (e.g. just "October") returns null.
 *
 * @param array $departure Departure row.
 * @return array|null [ 'start' => 'Y-m-d', 'end' => 'Y-m-d' or '' ].
 */
function jj_trip_departure_range( $departure ) {
    $year  = isset( $departure['year'] ) ? (int) $departure['year'] : 0;
    $dates = isset( $departure['dates'] ) ? trim( (string) $departure['dates'] ) : '';
    if ( ! $year || '' === $dates ) {
        return null;
    }

    $month_re = '(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*';
    $parts    = preg_split( '/\s*(?:–|—|-|\bto\b)\s*/iu', $dates, 2 );

    $read = function ( $text ) use ( $month_re ) {
        $day   = preg_match( '/\b(\d{1,2})\b/', $text, $d ) ? (int) $d[1] : 0;
        $month = preg_match( '/\b' . $month_re . '\b/i', $text, $m ) ? (int) gmdate( 'n', strtotime( '1 ' . $m[1] . ' 2000' ) ) : 0;
        return [ $day, $month ];
    };

    list( $start_day, $start_month ) = $read( $parts[0] );
    list( $end_day, $end_month )     = isset( $parts[1] ) ? $read( $parts[1] ) : [ 0, 0 ];

    // "18 – 23 March": the month comes after the end day only.
    if ( ! $start_month ) {
        $start_month = $end_month;
    }
    if ( ! $start_day || ! $start_month || ! checkdate( $start_month, $start_day, $year ) ) {
        return null;
    }

    $range = [
        'start' => sprintf( '%04d-%02d-%02d', $year, $start_month, $start_day ),
        'end'   => '',
    ];

    if ( $end_day && $end_month ) {
        // A chapter running over New Year ends in the next year.
        $end_year = $end_month < $start_month ? $year + 1 : $year;
        if ( checkdate( $end_month, $end_day, $end_year ) ) {
            $range['end'] = sprintf( '%04d-%02d-%02d', $end_year, $end_month, $end_day );
        }
    }

    return $range;
}

/**
 * The Between the Lines page's FAQPage entity.
 *
 * @param string $page_id The WebPage entity's @id, to link to, or ''.
 * @return array
 */
function jj_btl_faq_schema( $page_id = '' ) {
    $url    = get_permalink( get_queried_object_id() );
    $entity = [
        '@type'      => 'FAQPage',
        '@id'        => $url . '#faq',
        'url'        => $url,
        'mainEntity' => [],
    ];
    if ( $page_id ) {
        $entity['isPartOf'] = [ '@id' => $page_id ];
    }

    foreach ( jj_btl_faqs() as $faq ) {
        $entity['mainEntity'][] = [
            '@type'          => 'Question',
            'name'           => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq['answer'],
            ],
        ];
    }

    return $entity;
}

/**
 * One Event per chapter (departure) of the trips in JJ_EVENT_TRIP_SLUGS
 * (Marrakesh and Sintra); every other trip gets none.
 *
 * Offers appear only while the trip is On sale, so a Coming soon or
 * Waitlist trip never puts a price in the page source. The location is
 * the city from the trip's Destination field ("Marrakesh, Morocco").
 *
 * @param int $trip_id Trip post ID.
 * @return array[]
 */
function jj_trip_event_schema( $trip_id ) {
    if ( ! in_array( get_post_field( 'post_name', $trip_id ), JJ_EVENT_TRIP_SLUGS, true ) ) {
        return [];
    }

    $url         = get_permalink( $trip_id );
    $status      = jj_trip_public_status( $trip_id );
    $title       = jj_trip_volume_title( $trip_id );
    $volume_name = jj_trip_field( 'volume_name', $trip_id );
    $intro       = jj_trip_field( 'intro_statement', $trip_id );
    $image       = get_the_post_thumbnail_url( $trip_id, 'full' );
    $price       = jj_money_clean( jj_trip_field( 'price_from', $trip_id ) );

    $destination = trim( (string) jj_trip_field( 'destination', $trip_id ) );
    $place_parts = array_map( 'trim', explode( ',', $destination, 2 ) );
    $address     = [ '@type' => 'PostalAddress', 'addressLocality' => $place_parts[0] ];
    if ( ! empty( $place_parts[1] ) ) {
        $address['addressCountry'] = $place_parts[1];
    }

    $events = [];
    foreach ( jj_trip_rows( 'departures', $trip_id ) as $i => $departure ) {
        $range = jj_trip_departure_range( $departure );
        if ( ! $range || ! $destination ) {
            continue;
        }

        $event = [
            '@type'               => 'Event',
            '@id'                 => $url . '#chapter-' . ( $i + 1 ),
            'name'                => sprintf(
                /* translators: 1: volume title, e.g. "Volume 1: Marrakesh", 2: chapter number */
                __( 'Between the Lines %1$s, Chapter %2$d', 'jaiye-journeys' ),
                $title,
                $i + 1
            ),
            'url'                 => $url,
            'startDate'           => $range['start'],
            'eventStatus'         => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location'            => [
                '@type'   => 'Place',
                'name'    => $destination,
                'address' => $address,
            ],
            'organizer'           => [
                '@type' => 'Organization',
                'name'  => 'Jaiye Journeys',
                'url'   => 'https://jaiyejourneys.com',
            ],
        ];
        if ( $range['end'] ) {
            $event['endDate'] = $range['end'];
        }
        if ( $intro ) {
            $event['description'] = $intro;
        } elseif ( $volume_name ) {
            $event['description'] = $volume_name;
        }
        if ( $image ) {
            $event['image'] = $image;
        }

        if ( in_array( $status, [ 'open', 'sold-out' ], true ) && $price ) {
            $full = 'sold-out' === $status
                || ( isset( $departure['status'] ) && 'sold-out' === $departure['status'] )
                || jj_trip_departure_full( $trip_id, $i );

            $event['offers'] = [
                '@type'         => 'Offer',
                'price'         => $price,
                'priceCurrency' => 'GBP',
                'availability'  => $full ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
                'url'           => $url,
            ];
        }

        $events[] = $event;
    }

    return $events;
}

/**
 * Entities this theme adds to the current page.
 *
 * @param array $existing Entities already in the graph, to avoid repeats.
 * @return array Keyed by a unique graph key.
 */
function jj_schema_entities( $existing = [] ) {
    $add = [];

    if ( is_page_template( 'between-the-lines.php' ) && ! jj_schema_has_type( $existing, 'FAQPage' ) ) {
        $page_id = '';
        foreach ( (array) $existing as $entity ) {
            if ( is_array( $entity ) && isset( $entity['@type'], $entity['@id'] ) && in_array( 'WebPage', (array) $entity['@type'], true ) ) {
                $page_id = $entity['@id'];
            }
        }
        $add['jjBtlFaq'] = jj_btl_faq_schema( $page_id );
    }

    if ( is_singular( 'trip' ) && ! jj_is_trip_booking_page() && ! jj_schema_has_type( $existing, 'Event' ) ) {
        foreach ( jj_trip_event_schema( get_queried_object_id() ) as $i => $event ) {
            $add[ 'jjEvent' . $i ] = $event;
        }
    }

    return $add;
}

/**
 * Add our entities to Rank Math's graph. Runs late so everything Rank Math
 * adds itself is already there to check against.
 *
 * @param array $data Graph entities.
 * @return array
 */
function jj_rank_math_json_ld( $data ) {
    return array_merge( (array) $data, jj_schema_entities( $data ) );
}
add_filter( 'rank_math/json_ld', 'jj_rank_math_json_ld', 99999 );

/**
 * Without Rank Math's schema, print the same entities as one block.
 */
function jj_schema_fallback() {
    if ( jj_rank_math_schema_active() ) {
        return;
    }
    $entities = jj_schema_entities();
    if ( ! $entities ) {
        return;
    }
    printf(
        "<script type=\"application/ld+json\">%s</script>\n",
        wp_json_encode( [ '@context' => 'https://schema.org', '@graph' => array_values( $entities ) ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
    );
}
add_action( 'wp_head', 'jj_schema_fallback' );


/* ── Alt text ────────────────────────────────────────────────────── */

/**
 * An image's Alternative Text from the Media Library, or a fallback.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $fallback      Used when the Media Library has none.
 * @return string
 */
function jj_attachment_alt( $attachment_id, $fallback ) {
    $alt = trim( (string) get_post_meta( (int) $attachment_id, '_wp_attachment_image_alt', true ) );
    return '' !== $alt ? $alt : $fallback;
}

/**
 * The post whose featured image is being printed, so its alt text can
 * fall back to that post's title (a trip card shows the trip's name).
 */
function jj_alt_thumbnail_start( $post_id ) {
    $GLOBALS['jj_alt_thumbnail_post'] = $post_id;
}
add_action( 'begin_fetch_post_thumbnail_html', 'jj_alt_thumbnail_start' );

function jj_alt_thumbnail_end() {
    $GLOBALS['jj_alt_thumbnail_post'] = 0;
}
add_action( 'end_fetch_post_thumbnail_html', 'jj_alt_thumbnail_end' );

/**
 * Fill in alt text for Media Library images that have none, so no image
 * goes out with alt="". Uses the image's caption, else a title someone
 * typed (not the file name WordPress copies in on upload), else the name
 * of the post it's the featured image of, else "Photo from" the page it's
 * on. Filling in Alternative Text in the Media Library always wins.
 *
 * @param array   $attr       Image attributes.
 * @param WP_Post $attachment Attachment.
 * @return array
 */
function jj_attachment_alt_fallback( $attr, $attachment ) {
    if ( isset( $attr['alt'] ) && '' !== trim( $attr['alt'] ) ) {
        return $attr;
    }

    $alt = trim( wp_strip_all_tags( (string) wp_get_attachment_caption( $attachment->ID ) ) );

    // A typed title has spaces; one copied from the file name ("riad-pool") doesn't.
    if ( '' === $alt ) {
        $title = trim( $attachment->post_title );
        $file  = pathinfo( (string) wp_get_original_image_path( $attachment->ID ), PATHINFO_FILENAME );
        if ( false !== strpos( $title, ' ' ) && sanitize_title( $title ) !== sanitize_title( $file ) ) {
            $alt = $title;
        }
    }

    $thumb_of = isset( $GLOBALS['jj_alt_thumbnail_post'] ) ? (int) $GLOBALS['jj_alt_thumbnail_post'] : 0;
    if ( '' === $alt && $thumb_of ) {
        $alt = 'trip' === get_post_type( $thumb_of ) ? jj_trip_volume_title( $thumb_of ) : get_the_title( $thumb_of );
    }

    if ( '' === $alt && is_singular() ) {
        $page_id = get_queried_object_id();
        $context = is_singular( 'trip' ) ? jj_trip_volume_title( $page_id ) : get_the_title( $page_id );
        if ( $context ) {
            /* translators: %s: page or trip title */
            $alt = sprintf( __( 'Photo from %s', 'jaiye-journeys' ), $context );
        }
    }

    if ( '' !== $alt ) {
        $attr['alt'] = wp_strip_all_tags( $alt );
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'jj_attachment_alt_fallback', 10, 2 );


/* ── llms.txt ────────────────────────────────────────────────────── */

/**
 * Is Rank Math's own llms.txt module switched on? Then it serves
 * /llms.txt and the theme stays out of the way.
 *
 * @return bool
 */
function jj_rank_math_llms_active() {
    if ( ! class_exists( '\RankMath\Helper' ) ) {
        return false;
    }
    return \RankMath\Helper::is_module_active( 'llms-txt' );
}

/**
 * /llms.txt rewrite. Changing this needs a JJ_TRIP_REWRITE_VERSION bump.
 */
function jj_llms_rewrite() {
    if ( jj_rank_math_llms_active() ) {
        return;
    }
    add_rewrite_rule( '^llms\.txt$', 'index.php?jj_llms=1', 'top' );
}
add_action( 'init', 'jj_llms_rewrite' );

function jj_llms_query_vars( $vars ) {
    $vars[] = 'jj_llms';
    return $vars;
}
add_filter( 'query_vars', 'jj_llms_query_vars' );

/**
 * Serve /llms.txt as plain text. Runs before WordPress's canonical
 * redirect, which would otherwise add a trailing slash.
 */
function jj_llms_serve() {
    if ( ! get_query_var( 'jj_llms' ) || jj_rank_math_llms_active() ) {
        return;
    }
    status_header( 200 );
    header( 'Content-Type: text/plain; charset=utf-8' );
    header( 'X-Robots-Tag: noindex' );
    echo jj_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput -- plain text, not HTML.
    exit;
}
add_action( 'template_redirect', 'jj_llms_serve', 1 );

/**
 * A page's address by slug, from the page itself when it exists.
 *
 * @param string $slug Page slug.
 * @return string
 */
function jj_page_url( $slug ) {
    $page = get_page_by_path( $slug );
    return $page ? get_permalink( $page->ID ) : home_url( '/' . $slug . '/' );
}

/**
 * The llms.txt file (https://llmstxt.org): Markdown with a title, a
 * one-line summary, then sections of links.
 *
 * The volumes are read from the BTL trips, so names, chapters, sales
 * status and the From price match the trip pages. A price shows only
 * while a trip is On sale, as on its page.
 *
 * @return string
 */
function jj_llms_txt() {
    $btl_url     = jj_page_url( 'between-the-lines' );
    $journal_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/journal/' );

    $lines   = [];
    $lines[] = '# Jaiye Journeys';
    $lines[] = '';
    $lines[] = '> Jaiye Journeys plans curated luxury group trips and private celebrations, and runs Between the Lines, its luxury reading retreats.';
    $lines[] = '';
    $lines[] = '## Between the Lines';
    $lines[] = '';
    $lines[] = 'Between the Lines is a series of luxury reading retreats (six-day residencies) by Jaiye Journeys.';
    $lines[] = '';
    $lines[] = '- Founder and host: Steph (Regina Jaiyeola), on site at every residency. She reads close to 300 books a year, spent years as a creative director and producer, and led group travel across 12+ countries with Remote Year.';
    $lines[] = '- Each residency runs six days, from The Prologue (arrivals and welcome) to The Epilogue (farewell breakfast and an unhurried departure).';
    $lines[] = '- No-Rush Policy: we hold the whole property for the residency, so guests keep their room on the last day. No 10am checkout.';
    $lines[] = '- Solo readers and book clubs are both welcome. Guests don\'t need to read the same book.';
    $lines[] = '- 10 guests minimum per Chapter, maximum 16 to 20.';
    $lines[] = '- A volume is a residency in one destination; each chapter is one six-day retreat in that volume.';
    $lines[] = '- Contact: hello@jaiyejourneys.com. Instagram: @BTLRetreats and @JaiyeJourneys. TikTok: @JaiyeJourneys.';
    $lines[] = '';
    $lines[] = '## Retreats';
    $lines[] = '';
    $lines[] = '- [Between the Lines](' . $btl_url . '): what a reading retreat is, every volume, and how to get on The List (the waitlist)';

    $labels = [
        'announced'   => 'Coming soon',
        'coming-soon' => 'Waitlist open',
        'open'        => 'On sale',
        'sold-out'    => 'Sold out',
    ];
    foreach ( jj_btl_trip_ids() as $trip_id ) {
        $name        = jj_trip_volume_title( $trip_id );
        $destination = jj_trip_field( 'destination', $trip_id );
        $volume_name = jj_trip_field( 'volume_name', $trip_id );
        $status      = jj_trip_public_status( $trip_id );

        $about = [];
        if ( $volume_name ) {
            $about[] = '"' . $volume_name . '"';
        }
        // The volume title already names the destination unless it fell
        // back to the post title.
        if ( $destination && false === strpos( $name, $destination ) ) {
            $about[] = $destination;
        }
        $chapters = [];
        foreach ( jj_trip_rows( 'departures', $trip_id ) as $i => $departure ) {
            $when = trim( ( isset( $departure['dates'] ) ? $departure['dates'] : '' ) . ' ' . ( isset( $departure['year'] ) ? $departure['year'] : '' ) );
            if ( $when ) {
                $chapters[] = 'Chapter ' . ( $i + 1 ) . ': ' . $when;
            }
        }
        if ( $chapters ) {
            $about[] = implode( '; ', $chapters );
        }
        $label = isset( $labels[ $status ] ) ? $labels[ $status ] : $labels['open'];
        $price = jj_trip_field( 'price_from', $trip_id );
        if ( 'open' === $status && $price ) {
            $label .= ', from £' . jj_money_display( $price ) . ' per person sharing';
        }
        $about[] = $label;

        $lines[] = '- [' . $name . '](' . get_permalink( $trip_id ) . '): ' . implode( '. ', $about );
    }

    $lines[] = '';
    $lines[] = '## Questions';
    $lines[] = '';
    $lines[] = '- [Between the Lines FAQ](' . $btl_url . '#faq): the questions below, answered on the page';
    foreach ( jj_btl_faqs() as $faq ) {
        $lines[] = '- ' . $faq['question'] . ' ' . $faq['answer'];
    }

    $lines[] = '';
    $lines[] = '## Jaiye Journeys';
    $lines[] = '';
    $lines[] = '- [Our Journeys](' . jj_page_url( 'our-journeys' ) . '): curated group trips';
    $lines[] = '- [Private Groups & Celebrations](' . jj_page_url( 'private-groups-celebrations' ) . '): trips planned for private groups and celebrations';
    $lines[] = '- [The Journal](' . $journal_url . '): articles from Jaiye Journeys';
    $lines[] = '- [About](' . jj_page_url( 'about' ) . '): Steph, the founder';
    $lines[] = '- [Contact](' . jj_page_url( 'contact' ) . '): hello@jaiyejourneys.com';

    return implode( "\n", $lines ) . "\n";
}
