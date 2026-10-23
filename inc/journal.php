<?php
/**
 * The Journal (the blog): shared helpers for home.php, archive.php,
 * index.php, single.php and the "From The Journal" block on the Between the
 * Lines page.
 *
 * The Journal lives at whichever page is set as the Posts page (Settings →
 * Reading), expected to be /journal/. Posts in the "Reading Retreats"
 * category (slug reading-retreats) get the Between the Lines call to action.
 *
 * Styles: css/journal.css.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

// Category whose posts point readers to Between the Lines.
define( 'JJ_JOURNAL_BTL_CATEGORY', 'reading-retreats' );

/**
 * Has a Posts page been set? Until then there's no Journal to link to.
 *
 * @return bool
 */
function jj_journal_ready() {
    return (bool) get_option( 'page_for_posts' );
}

/**
 * The Journal's address.
 *
 * @return string
 */
function jj_journal_url() {
    $page_id = (int) get_option( 'page_for_posts' );
    return $page_id ? get_permalink( $page_id ) : home_url( '/journal/' );
}

/**
 * Is this a Journal screen (posts index, a post, or a post archive)?
 *
 * @return bool
 */
function jj_is_journal_view() {
    if ( is_post_type_archive( 'trip' ) ) {
        return false;
    }
    return is_home() || is_singular( 'post' ) || is_archive() || is_search();
}

/**
 * Reading time, e.g. "4 min read", at 200 words a minute.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function jj_reading_time( $post_id ) {
    $words   = str_word_count( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) ) );
    $minutes = max( 1, (int) ceil( $words / 200 ) );

    /* translators: %d: minutes */
    return sprintf( _n( '%d min read', '%d min read', $minutes, 'jaiye-journeys' ), $minutes );
}

/**
 * The category a post is labelled with: Rank Math's primary category when
 * set, else its first category. "Uncategorized" is never shown.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function jj_post_category( $post_id ) {
    $primary = (int) get_post_meta( $post_id, 'rank_math_primary_category', true );
    if ( $primary ) {
        $term = get_term( $primary, 'category' );
        if ( $term && ! is_wp_error( $term ) ) {
            return $term;
        }
    }

    foreach ( get_the_category( $post_id ) as $term ) {
        if ( (int) get_option( 'default_category' ) !== (int) $term->term_id ) {
            return $term;
        }
    }
    return null;
}

/**
 * Is this post about the reading retreats?
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function jj_is_btl_post( $post_id ) {
    return has_category( JJ_JOURNAL_BTL_CATEGORY, $post_id );
}

/**
 * Print one post card.
 *
 * @param int    $post_id Post ID.
 * @param string $heading Heading tag for the title: h2 on listings, h3 under a section heading.
 * @param string $modifier Optional class modifier, e.g. "feature".
 */
function jj_journal_card( $post_id, $heading = 'h2', $modifier = '' ) {
    $heading  = in_array( $heading, [ 'h2', 'h3' ], true ) ? $heading : 'h2';
    $category = jj_post_category( $post_id );
    $thumb    = get_post_thumbnail_id( $post_id );
    ?>
    <article class="jl-card<?php echo $modifier ? ' jl-card--' . esc_attr( $modifier ) : ''; ?>">
      <?php if ( $thumb ) : ?>
        <div class="jl-card__media">
          <?php
          echo wp_get_attachment_image( $thumb, 'large', false, [
              'class'   => 'jl-card__img',
              'loading' => 'lazy',
              'alt'     => jj_attachment_alt( $thumb, get_the_title( $post_id ) ),
          ] );
          ?>
        </div>
      <?php endif; ?>
      <div class="jl-card__body">
        <p class="jl-meta">
          <?php if ( $category ) : ?>
            <span class="jl-meta__category"><?php echo esc_html( $category->name ); ?></span>
            <span aria-hidden="true">&middot;</span>
          <?php endif; ?>
          <span><?php echo esc_html( jj_reading_time( $post_id ) ); ?></span>
        </p>
        <<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput -- whitelisted above. ?> class="jl-card__title">
          <a class="jl-card__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
        </<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
        <?php if ( has_excerpt( $post_id ) || get_post_field( 'post_content', $post_id ) ) : ?>
          <p class="jl-card__excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ); ?></p>
        <?php endif; ?>
        <span class="jl-card__cta" aria-hidden="true"><?php esc_html_e( 'Read the story', 'jaiye-journeys' ); ?></span>
      </div>
    </article>
    <?php
}

/**
 * Published posts in the same category, newest first.
 *
 * @param int $post_id Post ID.
 * @param int $count   How many.
 * @return int[]
 */
function jj_related_post_ids( $post_id, $count = 3 ) {
    $category = jj_post_category( $post_id );
    if ( ! $category ) {
        return [];
    }
    return get_posts( [
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => $count,
        'cat'                 => $category->term_id,
        'post__not_in'        => [ $post_id ],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'fields'              => 'ids',
    ] );
}


/* ── Navigation ──────────────────────────────────────────────────── */

/**
 * Add "Journal" to the primary and footer menus built in Appearance →
 * Menus, before Contact, unless the menu already links to it. Only once a
 * Posts page is set, so the link never leads to a missing page. (The
 * fallback menus in functions.php and footer.php add it themselves.)
 *
 * @param array    $items Menu item objects.
 * @param stdClass $args  wp_nav_menu() arguments.
 * @return array
 */
function jj_journal_menu_item( $items, $args ) {
    if ( ! jj_journal_ready() || ! in_array( $args->theme_location, [ 'primary', 'footer' ], true ) ) {
        return $items;
    }

    $url     = jj_journal_url();
    $page_id = (int) get_option( 'page_for_posts' );
    foreach ( $items as $item ) {
        if ( (int) $item->object_id === $page_id && 'page' === $item->object ) {
            return $items;
        }
        if ( untrailingslashit( $item->url ) === untrailingslashit( $url ) ) {
            return $items;
        }
    }

    $current = is_home() || is_singular( 'post' ) || is_category() || is_tag();
    $journal = (object) [
        'ID'               => 'journal',
        'db_id'            => 0,
        'menu_item_parent' => '0',
        'object_id'        => $page_id,
        'object'           => 'page',
        'type'             => 'post_type',
        'title'            => __( 'Journal', 'jaiye-journeys' ),
        'url'              => $url,
        'target'           => '',
        'attr_title'       => '',
        'description'      => '',
        'xfn'              => '',
        'current'          => $current,
        'classes'          => array_filter( [ 'menu-item', 'menu-item-journal', $current ? 'current-menu-item' : '' ] ),
    ];

    // Before Contact, else at the end.
    $position = count( $items );
    foreach ( array_values( $items ) as $i => $item ) {
        if ( '0' === (string) $item->menu_item_parent && false !== strpos( $item->url, '/contact' ) ) {
            $position = $i;
            break;
        }
    }
    $items = array_values( $items );
    array_splice( $items, $position, 0, [ $journal ] );

    return $items;
}
add_filter( 'wp_nav_menu_objects', 'jj_journal_menu_item', 10, 2 );


/* ── Assets ──────────────────────────────────────────────────────── */

function jj_journal_assets() {
    if ( ! jj_is_journal_view() && ! is_page_template( 'between-the-lines.php' ) ) {
        return;
    }
    wp_enqueue_style(
        'jaiye-journal',
        get_template_directory_uri() . '/css/journal.css',
        [ 'jaiye-global' ],
        filemtime( get_template_directory() . '/css/journal.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'jj_journal_assets' );
