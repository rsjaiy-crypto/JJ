<?php
/**
 * Journal listing: heading, category links, post grid and pagination.
 * Shared by home.php, archive.php and index.php.
 *
 * Expects $jl_overline, $jl_title, $jl_intro (HTML) and, optionally,
 * $jl_cta_btl (true to close with the Between the Lines call to action).
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

$jl_topics = get_categories( [
    'hide_empty' => true,
    'exclude'    => [ (int) get_option( 'default_category' ) ],
] );
$jl_paged  = max( 1, (int) get_query_var( 'paged' ) );
?>

<main id="main-content" class="site-main jl">

  <header class="jl-hero">
    <div class="container">
      <?php if ( $jl_overline ) : ?>
        <span class="overline jl-hero__overline"><?php echo esc_html( $jl_overline ); ?></span>
      <?php endif; ?>
      <h1 class="jl-hero__title"><?php echo esc_html( $jl_title ); ?></h1>
      <?php if ( $jl_intro ) : ?>
        <div class="jl-hero__intro"><?php echo wp_kses_post( $jl_intro ); ?></div>
      <?php endif; ?>

      <?php if ( count( $jl_topics ) > 1 && ( is_home() || is_category() ) ) : ?>
        <nav class="jl-topics" aria-label="<?php esc_attr_e( 'Journal topics', 'jaiye-journeys' ); ?>">
          <a href="<?php echo esc_url( jj_journal_url() ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'jaiye-journeys' ); ?></a>
          <?php foreach ( $jl_topics as $jl_topic ) : ?>
            <a href="<?php echo esc_url( get_category_link( $jl_topic ) ); ?>"<?php echo is_category( $jl_topic->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $jl_topic->name ); ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
    </div>
  </header>

  <section class="jl-list" aria-label="<?php esc_attr_e( 'Articles', 'jaiye-journeys' ); ?>">
    <div class="container">
      <?php if ( have_posts() ) : ?>
        <div class="jl-grid">
          <?php
          $jl_i = 0;
          while ( have_posts() ) :
              the_post();
              // The newest post on the Journal's first page runs wide.
              jj_journal_card( get_the_ID(), 'h2', ( is_home() && 1 === $jl_paged && 0 === $jl_i ) ? 'feature' : '' );
              $jl_i++;
          endwhile;
          ?>
        </div>

        <?php
        the_posts_pagination( [
            'mid_size'           => 1,
            'prev_text'          => __( 'Newer', 'jaiye-journeys' ),
            'next_text'          => __( 'Older', 'jaiye-journeys' ),
            'screen_reader_text' => __( 'More articles', 'jaiye-journeys' ),
        ] );
        ?>
      <?php else : ?>
        <p class="jl-empty"><?php esc_html_e( 'New stories are on their way.', 'jaiye-journeys' ); ?></p>
      <?php endif; ?>
    </div>
  </section>

  <?php if ( ! empty( $jl_cta_btl ) ) : ?>
    <?php require get_template_directory() . '/inc/journal-cta.php'; ?>
  <?php endif; ?>

</main>
