<?php
/**
 * Trip archive — /trips/
 *
 * Without this, /trips/ itself would 404 even once the singles resolve.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" class="site-main">

  <section class="oj-hero" aria-label="<?php esc_attr_e( 'Trips', 'jaiye-journeys' ); ?>">
    <div class="container">
      <div class="oj-hero__content">
        <h1 class="oj-hero__heading"><?php esc_html_e( 'Trips', 'jaiye-journeys' ); ?></h1>
        <p class="oj-hero__sub">
          <?php esc_html_e( 'Every current Jaiye Journeys group trip, in one place.', 'jaiye-journeys' ); ?>
        </p>
      </div>
    </div>
  </section>

  <section class="oj-section" aria-label="<?php esc_attr_e( 'All trips', 'jaiye-journeys' ); ?>">
    <div class="container">

      <?php if ( have_posts() ) : ?>
        <ul class="oj-grid" role="list">
          <?php while ( have_posts() ) : the_post(); ?>
            <li>
              <article class="trip-card">
                <?php if ( has_post_thumbnail() ) : ?>
                  <div class="trip-card__media">
                    <a href="<?php the_permalink(); ?>">
                      <?php
                      the_post_thumbnail( 'medium_large', [
                          'class'   => 'trip-card__img',
                          'loading' => 'lazy',
                      ] );
                      ?>
                    </a>
                  </div>
                <?php endif; ?>

                <div class="trip-card__body">
                  <p class="trip-card__type">
                    <span class="overline"><?php esc_html_e( 'Group Trip', 'jaiye-journeys' ); ?></span>
                  </p>
                  <h2 class="trip-card__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h2>

                  <?php if ( jj_trip_field( 'destination' ) ) : ?>
                    <p class="trip-card__destination"><?php echo esc_html( jj_trip_field( 'destination' ) ); ?></p>
                  <?php endif; ?>

                  <?php if ( jj_trip_field( 'duration' ) ) : ?>
                    <p class="trip-card__dates"><?php echo esc_html( jj_trip_field( 'duration' ) ); ?></p>
                  <?php endif; ?>

                  <a href="<?php the_permalink(); ?>" class="btn btn--secondary btn--sm trip-card__cta">
                    <?php esc_html_e( 'Find Out More', 'jaiye-journeys' ); ?>
                  </a>
                </div>
              </article>
            </li>
          <?php endwhile; ?>
        </ul>

        <?php the_posts_pagination( [ 'mid_size' => 1 ] ); ?>

      <?php else : ?>
        <p><?php esc_html_e( 'No trips are published yet.', 'jaiye-journeys' ); ?></p>
      <?php endif; ?>

    </div>
  </section>

</main>

<?php get_footer(); ?>
