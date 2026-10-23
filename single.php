<?php
/**
 * Single Journal post.
 *
 * Header (category, title, byline, reading time), featured image, body,
 * author note, a closing call to action (Between the Lines for Reading
 * Retreats posts, Contact for the rest) and up to three related posts from
 * the same category.
 *
 * Helpers: inc/journal.php. Styles: css/journal.css.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" class="site-main jp">

<?php
while ( have_posts() ) :
    the_post();

    $jp_id       = get_the_ID();
    $jp_category = jj_post_category( $jp_id );
    $jp_thumb    = get_post_thumbnail_id( $jp_id );
    $jl_cta_btl  = jj_is_btl_post( $jp_id );
    ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="jp-header">
      <div class="container">
        <?php if ( $jp_category ) : ?>
          <p class="overline jp-header__category">
            <a href="<?php echo esc_url( get_category_link( $jp_category ) ); ?>"><?php echo esc_html( $jp_category->name ); ?></a>
          </p>
        <?php endif; ?>

        <h1 class="jp-header__title"><?php the_title(); ?></h1>

        <p class="jl-meta">
          <span><?php esc_html_e( 'By Steph', 'jaiye-journeys' ); ?></span>
          <span aria-hidden="true">&middot;</span>
          <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
          <span aria-hidden="true">&middot;</span>
          <span><?php echo esc_html( jj_reading_time( $jp_id ) ); ?></span>
        </p>
      </div>
    </header>

    <?php if ( $jp_thumb ) : ?>
      <figure class="jp-figure">
        <div class="container">
          <div class="jp-figure__inner">
            <?php
            echo wp_get_attachment_image( $jp_thumb, 'full', false, [
                'class'         => 'jp-figure__img',
                'loading'       => 'eager',
                'fetchpriority' => 'high',
                'alt'           => jj_attachment_alt( $jp_thumb, get_the_title() ),
            ] );
            ?>
            <?php $jp_caption = wp_get_attachment_caption( $jp_thumb ); ?>
            <?php if ( $jp_caption ) : ?>
              <figcaption class="jp-figure__caption"><?php echo esc_html( $jp_caption ); ?></figcaption>
            <?php endif; ?>
          </div>
        </div>
      </figure>
    <?php endif; ?>

    <div class="jp-body">
      <div class="container container--narrow">
        <div class="jp-content">
          <?php the_content(); ?>
        </div>

        <?php
        wp_link_pages( [
            'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Pages of this article', 'jaiye-journeys' ) . '">',
            'after'  => '</nav>',
        ] );
        ?>

        <aside class="jp-author" aria-label="<?php esc_attr_e( 'About the author', 'jaiye-journeys' ); ?>">
          <p class="jp-author__name"><?php esc_html_e( 'Written by Steph', 'jaiye-journeys' ); ?></p>
          <p class="jp-author__bio">
            <?php esc_html_e( 'Steph is the founder of Jaiye Journeys and the host of Between the Lines. She reads close to 300 books a year, spent years as a creative director and producer, and has led group travel across 12+ countries.', 'jaiye-journeys' ); ?>
          </p>
          <a class="jp-author__link" href="<?php echo esc_url( jj_page_url( 'about' ) ); ?>"><?php esc_html_e( 'More about Steph', 'jaiye-journeys' ); ?></a>
        </aside>
      </div>
    </div>

  </article>

    <?php require get_template_directory() . '/inc/journal-cta.php'; ?>

    <?php $jp_related = jj_related_post_ids( $jp_id, 3 ); ?>
    <?php if ( $jp_related ) : ?>
      <section class="jl-related" aria-labelledby="jl-related-title">
        <div class="container">
          <span class="overline jl-related__overline"><?php esc_html_e( 'Keep reading', 'jaiye-journeys' ); ?></span>
          <h2 class="jl-related__title" id="jl-related-title">
            <?php
            /* translators: %s: category name */
            printf( esc_html__( 'More from %s', 'jaiye-journeys' ), esc_html( $jp_category->name ) );
            ?>
          </h2>
          <div class="jl-grid">
            <?php foreach ( $jp_related as $jp_related_id ) : ?>
              <?php jj_journal_card( $jp_related_id, 'h3' ); ?>
            <?php endforeach; ?>
          </div>
          <p class="jl-related__more">
            <a href="<?php echo esc_url( jj_journal_ready() ? jj_journal_url() : get_category_link( $jp_category ) ); ?>" class="btn btn--secondary">
              <?php esc_html_e( 'Back to The Journal', 'jaiye-journeys' ); ?>
            </a>
          </p>
        </div>
      </section>
    <?php endif; ?>

<?php endwhile; ?>

</main>

<?php get_footer(); ?>
