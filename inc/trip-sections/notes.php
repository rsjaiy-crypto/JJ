<?php
/**
 * Trip Notes: featured note plus scroll accordion.
 *
 * Shared by the full trip page (single-trip.php) and the waitlist page
 * (inc/trip-coming-soon.php), so both always render the section the same way.
 * Must stay free of prices and booking links: the waitlist page shows it.
 *
 * Expects $notes.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;
?>
  <?php if ( $notes ) : ?>
    <section class="trip-notes" id="trip-notes">
      <div class="container">
        <h2 class="trip-heading js-reveal"><?php esc_html_e( 'Trip Notes', 'jaiye-journeys' ); ?></h2>

        <?php
        // Split out the featured note so it can anchor the left column.
        $featured_note = null;
        $stack_notes   = [];

        foreach ( $notes as $note ) {
            if ( null === $featured_note && ! empty( $note['featured'] ) ) {
                $featured_note = $note;
            } else {
                $stack_notes[] = $note;
            }
        }
        ?>

        <div class="trip-notes__layout<?php echo $featured_note ? '' : ' trip-notes__layout--no-feature'; ?>">

          <?php if ( $featured_note ) : ?>
            <article class="trip-note-feature js-reveal">
              <span class="trip-note-feature__icon" aria-hidden="true">
                <?php echo jj_trip_note_icon( isset( $featured_note['icon'] ) ? $featured_note['icon'] : 'plane' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline SVG. ?>
              </span>
              <?php if ( ! empty( $featured_note['title'] ) ) : ?>
                <h3 class="trip-note-feature__title"><?php echo esc_html( $featured_note['title'] ); ?></h3>
              <?php endif; ?>
              <?php if ( ! empty( $featured_note['body'] ) ) : ?>
                <div class="trip-note-feature__body"><?php echo wp_kses_post( wpautop( $featured_note['body'] ) ); ?></div>
              <?php endif; ?>
            </article>
          <?php endif; ?>

          <?php if ( $stack_notes ) : ?>
            <!--
              Scroll accordion. Defaults to every panel open; trip.js adds
              .is-interactive to switch on collapsing, so this stays readable
              with JS off or under prefers-reduced-motion.
            -->
            <div class="trip-note-stack js-note-stack">
              <?php foreach ( $stack_notes as $index => $note ) : ?>
                <article class="trip-note-item<?php echo 0 === $index ? ' is-open' : ''; ?>">
                  <h3 class="trip-note-item__heading">
                    <button type="button"
                            class="trip-note-item__trigger"
                            aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                            aria-controls="note-panel-<?php echo (int) $index; ?>">
                      <span class="trip-note-item__icon" aria-hidden="true">
                        <?php echo jj_trip_note_icon( isset( $note['icon'] ) ? $note['icon'] : 'route' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline SVG. ?>
                      </span>
                      <span class="trip-note-item__title"><?php echo esc_html( isset( $note['title'] ) ? $note['title'] : '' ); ?></span>
                      <span class="trip-note-item__chevron" aria-hidden="true"></span>
                    </button>
                  </h3>
                  <div class="trip-note-item__panel" id="note-panel-<?php echo (int) $index; ?>">
                    <div class="trip-note-item__panel-inner">
                      <?php if ( ! empty( $note['body'] ) ) : ?>
                        <?php echo wp_kses_post( wpautop( $note['body'] ) ); ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>
  <?php endif; ?>
