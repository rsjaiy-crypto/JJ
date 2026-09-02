<?php
/**
 * 404 template.
 * Served whenever WordPress can't match a URL to a real page or post.
 */

status_header( 404 );

get_header();
?>

<main id="main-content" class="site-main">

  <section class="error-404">
    <div class="container container--narrow">
      <div class="error-404__content">

        <h1 class="error-404__title">
          <?php esc_html_e( 'This page took a wrong turn.', 'jaiye-journeys' ); ?>
        </h1>

        <p class="error-404__body">
          <?php esc_html_e( 'Even the best-planned trips have a detour. The page you\'re looking for doesn\'t exist, or has moved somewhere new.', 'jaiye-journeys' ); ?>
        </p>

        <div class="error-404__actions">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--accent">
            <?php esc_html_e( 'Take Me Home', 'jaiye-journeys' ); ?>
          </a>
          <a href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>" class="btn btn--secondary">
            <?php esc_html_e( 'Explore Our Journeys', 'jaiye-journeys' ); ?>
          </a>
        </div>

      </div>
    </div>
  </section><!-- /.error-404 -->

</main><!-- /#main-content -->

<style>
/* ------------------------------------------------------------------
   404 template styles
   ------------------------------------------------------------------ */

.error-404 {
  background-color: var(--color-cream);
  padding-block: var(--section-gap);
  min-height: 50vh;
  display: flex;
  align-items: center;
}

.error-404__content {
  text-align: center;
}

.error-404__title {
  font-size: var(--text-3xl);
  font-weight: var(--fw-regular);
  color: var(--color-forest);
}

.error-404__body {
  margin-top: var(--space-6);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  color: var(--color-text);
}

.error-404__actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: var(--space-4);
  margin-top: var(--space-10);
}
</style>

<?php get_footer(); ?>
