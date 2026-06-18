<?php
/*
 * Template Name: Contact
 */

get_header();
?>

<style>
/* ------------------------------------------------------------------
   Contact page — component styles
   ------------------------------------------------------------------ */

.ct-intro {
  background: var(--color-cream);
  text-align: center;
  padding-block: clamp(64px, 8vw, 96px);
}

.ct-intro__line {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-2xl);
  color: var(--color-forest);
  margin-bottom: 48px;
}

.ct-form {
  background: var(--color-cream);
  padding-inline: var(--container-pad);
}

.ct-form__inner {
  max-width: 860px;
  margin-inline: auto;
}

.ct-form-wrapper {
  width: 100%;
  padding-bottom: clamp(48px, 6vw, 80px);
}

.ct-cta {
  background: var(--color-deep-green);
  text-align: center;
  padding-block: clamp(48px, 6vw, 72px);
  padding-inline: var(--container-pad);
}

.ct-cta__heading {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  color: var(--color-cream);
  margin-bottom: 12px;
}

.ct-cta__body {
  font-family: var(--font-body);
  font-weight: var(--fw-light);
  font-size: var(--text-base);
  color: var(--color-sage);
  margin-bottom: 32px;
}

.ct-image-banner {
  width: 100%;
  height: 50vh;
  min-height: 300px;
  background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/travel-sea.jpg');
  background-size: cover;
  background-position: center center;
  background-attachment: fixed;
}
</style>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. INTRO STRIP
       Cream background. Single centred italic line above the form.
       ============================================================ -->
  <section
    class="ct-intro"
    aria-label="<?php esc_attr_e( 'Contact introduction', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <p class="ct-intro__line">
        <?php esc_html_e( 'Every great trip starts with a conversation.', 'jaiye-journeys' ); ?>
      </p>
    </div>
  </section><!-- /.ct-intro -->


  <!-- ============================================================
       2. FORM
       Cream background, continuous with intro strip. Tally embed.
       ============================================================ -->
  <section
    class="ct-form"
    aria-label="<?php esc_attr_e( 'Contact form', 'jaiye-journeys' ); ?>"
  >
    <div class="ct-form__inner">
      <div class="ct-form-wrapper">
        <iframe
          data-tally-src="https://tally.so/embed/q4kbKO?alignLeft=1&hideTitle=1&transparentBackground=1&dynamicHeight=1"
          loading="lazy"
          width="100%"
          height="2654"
          frameborder="0"
          marginheight="0"
          marginwidth="0"
          title="Contact Page Form"
        ></iframe>
        <script>var d=document,w="https://tally.so/widgets/embed.js",v=function(){"undefined"!=typeof Tally?Tally.loadEmbeds():d.querySelectorAll("iframe[data-tally-src]:not([src])").forEach((function(e){e.src=e.dataset.tallySrc}))};if("undefined"!=typeof Tally)v();else if(d.querySelector('script[src="'+w+'"]')==null){var s=d.createElement("script");s.src=w,s.onload=v,s.onerror=v,d.body.appendChild(s);}</script>
      </div>
    </div>
  </section><!-- /.ct-form -->


  <!-- ============================================================
       3. IMAGE BANNER
       Full-width parallax image between form and CTA.
       ============================================================ -->
  <div class="ct-image-banner" role="presentation"></div>


  <!-- ============================================================
       4. CTA STRIP
       Deep green background. Links through to Our Journeys page.
       ============================================================ -->
  <section
    class="ct-cta"
    aria-label="<?php esc_attr_e( 'Explore our journeys', 'jaiye-journeys' ); ?>"
  >
    <h2 class="ct-cta__heading">
      <?php esc_html_e( 'Not ready to enquire just yet?', 'jaiye-journeys' ); ?>
    </h2>
    <p class="ct-cta__body">
      <?php esc_html_e( 'Browse our upcoming trips and get a feel for what\'s possible.', 'jaiye-journeys' ); ?>
    </p>
    <a
      href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>"
      class="btn btn--ghost"
    >
      <?php esc_html_e( 'Explore Our Journeys', 'jaiye-journeys' ); ?>
    </a>
  </section><!-- /.ct-cta -->

</main><!-- /#main-content -->

<?php get_footer(); ?>
