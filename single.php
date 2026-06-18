<?php get_header(); ?>

<style>
/* ------------------------------------------------------------------
   Single post — component styles (sp-)
   ------------------------------------------------------------------ */

/* ── Hero ── */
.sp-hero {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 70vh;
  min-height: 420px;
  background-color: var(--color-forest);
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  text-align: center;
  overflow: hidden;
}

@media (min-width: 768px) {
  .sp-hero {
    height: 100vh;
  }
}

.sp-hero__overlay {
  position: absolute;
  inset: 0;
  background: rgba(8, 31, 28, 0.45);
}

.sp-hero__inner {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-inline: var(--container-pad);
  max-width: 900px;
}

.sp-hero__category {
  display: inline-block;
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-xs);
  color: var(--color-sage);
  letter-spacing: 0.2em;
  text-transform: uppercase;
  margin-bottom: 16px;
}

.sp-hero__title {
  font-family: var(--font-heading);
  font-weight: 300;
  font-size: var(--text-3xl);
  color: var(--color-cream);
  line-height: 1.15;
  max-width: 800px;
  margin: 0;
}

@media (min-width: 768px) {
  .sp-hero__title {
    font-size: var(--text-4xl);
  }
}

.sp-hero__meta {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-sm);
  color: var(--color-sage);
  margin-top: 24px;
  margin-bottom: 0;
}

/* ── Body ── */
.sp-body {
  background: var(--color-cream);
  padding-block: clamp(64px, 8vw, 96px);
  padding-inline: var(--container-pad);
}

.sp-body__inner {
  display: grid;
  grid-template-columns: 1fr;
  max-width: 720px;
  margin-inline: auto;
}

@media (min-width: 1200px) {
  .sp-body__inner {
    grid-template-columns: 15% 85%;
    max-width: 1080px;
    margin-inline: auto;
  }

  .sp-body__gutter {
    display: block;
  }

  .sp-body__content {
    max-width: 720px;
  }
}

.sp-body__gutter {
  display: none;
}

/* ── Post content typography ── */
.sp-body__content p {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-base);
  color: color-mix(in srgb, var(--color-forest) 85%, transparent);
  line-height: 1.85;
  margin-bottom: 1.5em;
}

.sp-body__content p:first-of-type {
  font-size: var(--text-md);
  color: var(--color-forest);
  line-height: 1.9;
}

.sp-body__content h2 {
  font-family: var(--font-heading);
  font-weight: 400;
  font-size: var(--text-2xl);
  color: var(--color-forest);
  margin-block: 48px 16px;
  line-height: 1.2;
}

.sp-body__content h3 {
  font-family: var(--font-heading);
  font-style: italic;
  font-weight: 400;
  font-size: var(--text-xl);
  color: var(--color-forest);
  margin-block: 36px 12px;
  line-height: 1.25;
}

.sp-body__content blockquote {
  border-left: none;
  border-top: 1px solid var(--color-sage);
  border-bottom: 1px solid var(--color-sage);
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-2xl);
  color: var(--color-forest);
  text-align: center;
  padding-block: 40px;
  margin-block: 48px;
  margin-inline: 0;
}

.sp-body__content blockquote p,
.sp-body__content blockquote p:first-of-type {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-2xl);
  color: var(--color-forest);
  line-height: 1.3;
  margin-bottom: 0;
}

.sp-body__content img {
  width: 100%;
  height: auto;
  display: block;
  margin-block: 48px;
}

.sp-body__content figure {
  margin: 0;
}

.sp-body__content figure img {
  margin-bottom: 0;
}

.sp-body__content figcaption {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-style: italic;
  font-size: var(--text-xs);
  color: var(--color-sage);
  text-align: center;
  margin-top: 8px;
  margin-bottom: 48px;
}

.sp-body__content a {
  color: var(--color-salmon);
  text-decoration: none;
}

.sp-body__content a:hover {
  text-decoration: underline;
}

/* ── Author bio ── */
.sp-author {
  background: var(--color-cream);
  border-top: 1px solid var(--color-sage);
  padding-block: clamp(40px, 5vw, 64px);
  padding-inline: var(--container-pad);
}

.sp-author__inner {
  max-width: 720px;
  margin-inline: auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

@media (min-width: 768px) {
  .sp-author__inner {
    flex-direction: row;
    align-items: flex-start;
    gap: 32px;
  }
}

@media (min-width: 1200px) {
  .sp-author__inner {
    margin-inline-start: calc(15% + var(--container-pad));
    margin-inline-end: auto;
  }
}

.sp-author__avatar {
  flex-shrink: 0;
}

.sp-author__avatar img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
}

.sp-author__name {
  font-family: var(--font-body);
  font-weight: var(--fw-medium);
  font-size: var(--text-base);
  color: var(--color-forest);
  margin-bottom: 8px;
}

.sp-author__bio {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-sm);
  color: var(--color-sage);
  margin-bottom: 16px;
  line-height: 1.7;
}

.sp-author__links {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.sp-author__link {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-xs);
  color: var(--color-salmon);
  text-transform: uppercase;
  letter-spacing: 0.12em;
  text-decoration: none;
}

.sp-author__link:hover {
  text-decoration: underline;
}

/* ── CTA strip ── */
.sp-cta {
  background: var(--color-deep-green);
  text-align: center;
  padding-block: clamp(48px, 6vw, 72px);
  padding-inline: var(--container-pad);
}

.sp-cta__heading {
  font-family: var(--font-heading);
  font-style: italic;
  font-weight: 400;
  font-size: var(--text-2xl);
  color: var(--color-cream);
  margin-bottom: 12px;
}

.sp-cta__body {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-base);
  color: var(--color-sage);
  max-width: 480px;
  margin-inline: auto;
  margin-bottom: 32px;
  line-height: 1.7;
}
</style>

<main id="main-content" class="site-main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <!-- ============================================================
       1. HERO
       Full-viewport image with overlay, category, title, meta.
       ============================================================ -->
  <?php
    $hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $hero_style = $hero_image
      ? 'background-image: url(' . esc_url( $hero_image ) . ');'
      : '';
    $categories = get_the_category();
  ?>
  <section
    class="sp-hero"
    style="<?php echo esc_attr( $hero_style ); ?>"
    aria-label="<?php esc_attr_e( 'Post hero', 'jaiye-journeys' ); ?>"
  >
    <div class="sp-hero__overlay" aria-hidden="true"></div>
    <div class="sp-hero__inner">

      <?php if ( $categories ) : ?>
        <span class="sp-hero__category">
          <?php echo esc_html( $categories[0]->name ); ?>
        </span>
      <?php endif; ?>

      <h1 class="sp-hero__title">
        <?php the_title(); ?>
      </h1>

      <p class="sp-hero__meta">
        <?php echo esc_html( get_the_date( 'j F Y' ) ); ?> &middot; Written by Regina Jaiy
      </p>

    </div>
  </section><!-- /.sp-hero -->


  <!-- ============================================================
       2. POST BODY
       Cream bg. Narrow reading column with decorative left gutter.
       ============================================================ -->
  <section
    class="sp-body"
    aria-label="<?php esc_attr_e( 'Post content', 'jaiye-journeys' ); ?>"
  >
    <div class="sp-body__inner">
      <div class="sp-body__gutter" aria-hidden="true"></div>
      <div class="sp-body__content">
        <?php the_content(); ?>
      </div>
    </div>
  </section><!-- /.sp-body -->


  <!-- ============================================================
       3. AUTHOR BIO
       Cream bg, bordered top, avatar + name + bio + links.
       ============================================================ -->
  <section
    class="sp-author"
    aria-label="<?php esc_attr_e( 'About the author', 'jaiye-journeys' ); ?>"
  >
    <div class="sp-author__inner">

      <div class="sp-author__avatar">
        <?php echo get_avatar( get_the_author_meta( 'ID' ), 80, '', 'Regina Jaiy' ); ?>
      </div>

      <div class="sp-author__details">
        <p class="sp-author__name">Regina Jaiy</p>
        <p class="sp-author__bio">Travel advisor, Creative Director, and founder of Jaiye Journeys. FORA&#8209;certified. Based in London.</p>
        <div class="sp-author__links">
          <a
            href="https://instagram.com/reginajaiy"
            class="sp-author__link"
            target="_blank"
            rel="noopener noreferrer"
          >Follow on Instagram</a>
          <a
            href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"
            class="sp-author__link"
          >Read More Posts</a>
        </div>
      </div>

    </div>
  </section><!-- /.sp-author -->

<?php endwhile; endif; ?>


  <!-- ============================================================
       4. CTA STRIP
       Deep green bg, italic heading, salmon button → /contact/.
       ============================================================ -->
  <section
    class="sp-cta"
    aria-label="<?php esc_attr_e( 'Plan your trip', 'jaiye-journeys' ); ?>"
  >
    <h2 class="sp-cta__heading">Ready to make the trip real?</h2>
    <p class="sp-cta__body">From the page to the destination. Let&#8217;s plan your next journey.</p>
    <a
      href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
      class="btn"
    ><?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?></a>
  </section><!-- /.sp-cta -->

</main><!-- /#main-content -->

<?php get_footer(); ?>
