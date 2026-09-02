<?php
/**
 * Template Name: Health & Safety
 */

get_header();
?>

<main id="main-content" class="site-main">

  <article class="page-article page-legal">

    <!-- Page header -->
    <header class="page-hero">
      <div class="container">
        <div class="page-hero__content">
          <h1 class="page-hero__title">
            <?php esc_html_e( 'Health & Safety', 'jaiye-journeys' ); ?>
          </h1>
        </div>
      </div>
    </header><!-- /.page-hero -->

    <!-- Page content -->
    <div class="page-content">
      <div class="container container--narrow">
        <div class="entry-content">

          <p><em><?php esc_html_e( 'Jaiye Journeys & Between the Lines', 'jaiye-journeys' ); ?></em></p>

          <p>
            <?php
            printf(
                /* translators: %s: link to the Terms & Conditions page */
                esc_html__( 'This document sets out our approach to health, safety, and personal risk on Jaiye Journeys and Between the Lines trips. It forms part of your booking agreement alongside our %s, and exists because trips vary widely, a reading retreat in Marrakech and a group trip with a yacht day carry different considerations, and guests deserve to know exactly what to expect before they travel.', 'jaiye-journeys' ),
                '<a href="' . esc_url( home_url( '/terms/' ) ) . '">' . esc_html__( 'Terms & Conditions', 'jaiye-journeys' ) . '</a>'
            );
            ?>
          </p>

          <h2><?php esc_html_e( '1. Health & Safety', 'jaiye-journeys' ); ?></h2>
          <p>
            <?php esc_html_e( 'Please follow all health and safety guidance given by us, venue staff, and activity providers throughout your trip. Not following reasonable safety instructions may result in being asked to sit out an activity or, in serious cases, being asked to leave the trip without a refund.', 'jaiye-journeys' ); ?>
          </p>

          <h2><?php esc_html_e( '2. Medical Information', 'jaiye-journeys' ); ?></h2>
          <p>
            <?php esc_html_e( 'By booking, you confirm you are medically and physically fit to take part in your trip and any activities you choose to join. Please tell us about any medical condition, allergy, dietary requirement, or mobility limitation that might reasonably affect your participation before you travel, and let us know if anything changes between booking and departure. We\'ll always do our best to accommodate reasonable requests, though we can\'t guarantee every venue or supplier will be able to. Please bring enough of any medication you need for the full trip.', 'jaiye-journeys' ); ?>
          </p>

          <h2><?php esc_html_e( '3. Participation & Assumption of Risk', 'jaiye-journeys' ); ?></h2>
          <p>
            <?php esc_html_e( 'Our trips are designed to be restorative, not risky, but some optional activities carry an inherent element of risk (for example, swimming, hiking, or boat travel). Taking part in any activity is entirely voluntary, and by choosing to join in, you accept responsibility for deciding whether it\'s right for your own health and ability. We ask all guests to follow reasonable instructions from hosts, crew, guides, and activity providers. We\'re not responsible for injury arising from a guest not following reasonable safety guidance, or from risks inherent to the activity itself, except where caused by our negligence.', 'jaiye-journeys' ); ?>
          </p>

          <h2><?php esc_html_e( '4. Water Activities', 'jaiye-journeys' ); ?></h2>
          <p>
            <?php esc_html_e( 'Where a trip includes access to the sea, a pool, or other water-based activity, taking part is entirely voluntary and at your own risk. Please follow all safety guidance given by crew, instructors, or hosts. We may ask a guest not to take part in a water-based activity where we reasonably believe it presents a safety risk to them or others.', 'jaiye-journeys' ); ?>
          </p>

          <h2><?php esc_html_e( '5. Alcohol', 'jaiye-journeys' ); ?></h2>
          <p>
            <?php esc_html_e( 'Some trips include wine tastings, welcome drinks, or other moments where alcohol is offered. Drinking is entirely optional, and you\'re responsible for your own choices and behaviour. We reserve the right to stop serving alcohol to, or remove from an activity, any guest whose behaviour becomes unsafe or disruptive to others.', 'jaiye-journeys' ); ?>
          </p>

          <h2><?php esc_html_e( '6. Independent Time', 'jaiye-journeys' ); ?></h2>
          <p>
            <?php esc_html_e( 'Where your itinerary includes unstructured free time, anything you choose to do independently, outside organised activities, is entirely at your own risk. We\'re not responsible for incidents, injuries, or losses that happen while you\'re exploring on your own.', 'jaiye-journeys' ); ?>
          </p>

          <p>
            <?php
            printf(
                /* translators: %s: mailto link */
                esc_html__( 'These provisions form part of our Terms & Conditions. Please read both before you travel. Questions are always welcome at %s.', 'jaiye-journeys' ),
                '<a href="mailto:hello@jaiyejourneys.com">hello@jaiyejourneys.com</a>'
            );
            ?>
          </p>

        </div><!-- /.entry-content -->
      </div><!-- /.container -->
    </div><!-- /.page-content -->

  </article><!-- /.page-article -->

</main><!-- /#main-content -->

<style>
/* ------------------------------------------------------------------
   Health & Safety template styles
   Mirrors page.php's legal-page treatment (used for Privacy Policy
   and Terms) so this page reads as part of the same set.
   ------------------------------------------------------------------ */

.page-hero {
  position: relative;
  background-color: var(--color-forest);
}

.page-hero__content {
  padding-block: var(--space-16);
}

.page-hero__title {
  font-size: var(--text-4xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
}

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
</style>

<?php get_footer(); ?>
