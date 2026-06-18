<?php
/**
 * Default page template.
 * Used for all static pages unless a more specific template applies.
 */

get_header();
?>

<main id="main-content" class="site-main">

  <?php while ( have_posts() ) : the_post();
    $legal_pages  = array( 'privacy-policy', 'terms' );
    $current_slug = get_post_field( 'post_name', get_the_ID() );
    $legal_class  = in_array( $current_slug, $legal_pages ) ? ' page-legal' : '';
  ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' . $legal_class ); ?>>

      <!-- Page header -->
      <header class="page-hero">
        <?php if ( has_post_thumbnail() ) : ?>
          <div class="page-hero__image">
            <?php
            the_post_thumbnail( 'full', [
                'class'   => 'page-hero__img',
                'loading' => 'eager',
                'alt'     => get_the_title(),
            ] );
            ?>
          </div>
        <?php endif; ?>

        <div class="container">
          <div class="page-hero__content">
            <h1 class="page-hero__title"><?php the_title(); ?></h1>
          </div>
        </div>
      </header><!-- /.page-hero -->

      <!-- Page content -->
      <div class="page-content">
        <div class="container container--narrow">
          <div class="entry-content">
            <?php
            the_content();

            wp_link_pages( [
                'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'jaiye-journeys' ) . '"><span class="page-links__label">' . __( 'Pages:', 'jaiye-journeys' ) . '</span>',
                'after'  => '</nav>',
            ] );
            ?>
          </div><!-- /.entry-content -->
        </div><!-- /.container -->
      </div><!-- /.page-content -->

    </article><!-- /.page-article -->

  <?php endwhile; ?>

</main><!-- /#main-content -->

<style>
/* ------------------------------------------------------------------
   Default page template styles
   ------------------------------------------------------------------ */

.page-hero {
  position: relative;
  background-color: var(--color-forest);
}

.page-hero__image {
  position: relative;
  height: clamp(240px, 40vw, 520px);
  overflow: hidden;
}

.page-hero__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  opacity: 0.65;
}

.page-hero__content {
  padding-block: var(--space-12) var(--space-16);
}

/* If there's a featured image, overlay the title */
.page-hero:has(.page-hero__image) .page-hero__content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
}

.page-hero__title {
  font-size: var(--text-4xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
}

/* No image: dark green bg, cream title */
.page-hero:not(:has(.page-hero__image)) {
  background-color: var(--color-forest);
}

.page-hero:not(:has(.page-hero__image)) .page-hero__content {
  padding-block: var(--space-16);
}

/* Content area */
.page-content {
  padding-block: var(--section-gap);
}

.entry-content {
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.8;
  color: var(--color-text);
}

.entry-content > * + * {
  margin-top: var(--space-6);
}

.entry-content h2 { font-size: var(--text-3xl); margin-top: var(--space-12); }
.entry-content h3 { font-size: var(--text-2xl); margin-top: var(--space-10); }
.entry-content h4 { font-size: var(--text-xl);  margin-top: var(--space-8);  }

.entry-content ul,
.entry-content ol {
  padding-left: var(--space-6);
}

.entry-content ul { list-style: disc; }
.entry-content ol { list-style: decimal; }

.entry-content li + li { margin-top: var(--space-2); }

.entry-content img {
  width: 100%;
  height: auto;
  border-radius: var(--radius-md);
}

.entry-content a {
  color: var(--color-forest);
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: color var(--duration-fast) var(--ease-out);
}

.entry-content a:hover {
  color: var(--color-salmon);
}

/* Page navigation links (for paginated pages) */
.page-links {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-2);
  margin-top: var(--space-10);
  padding-top: var(--space-6);
  border-top: 1px solid var(--color-border);
}

.page-links__label {
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--color-text-muted);
}
</style>

<?php get_footer(); ?>
