<!-- ============================================================
     Site Footer
     Mobile: stacked (links top, contact/social bottom)
     Desktop (768px+): two-column grid
     Background: deep green #081f1c | Text: cream #f5e9db
     ============================================================ -->
<footer class="site-footer" id="site-footer" role="contentinfo">

  <div class="site-footer__main">
    <div class="container">
      <div class="site-footer__grid">

        <!-- Column 1: Brand + navigation links --------------->
        <div class="site-footer__brand">
          <div class="site-footer__logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-footer__logo-link" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — <?php esc_attr_e( 'Return to homepage', 'jaiye-journeys' ); ?>">
              <img
                src="/wp-content/themes/jaiye-journeys-theme/assets/images/logo-white.png"
                alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                class="site-footer__logo-img"
              >
            </a>
          </div>

          <p class="site-footer__tagline">
            <?php esc_html_e( 'Curated travel to enjoy life. We handle the admin so you can focus on the enjoyment.', 'jaiye-journeys' ); ?>
          </p>

          <nav aria-label="<?php esc_attr_e( 'Footer navigation', 'jaiye-journeys' ); ?>">
            <?php
            if ( has_nav_menu( 'footer' ) ) :
                wp_nav_menu( [
                    'theme_location' => 'footer',
                    'menu_class'     => 'site-footer__nav-list',
                    'container'      => false,
                    'depth'          => 1,
                ] );
            else :
            ?>
              <ul class="site-footer__nav-list">
                <?php
                $footer_links = [
                    'our-journeys'      => 'Our Journeys',
                    'between-the-lines' => 'Between the Lines',
                    'about'             => 'About',
                    'contact'           => 'Contact',
                ];
                foreach ( $footer_links as $slug => $label ) :
                    $page = get_page_by_path( $slug );
                    $url  = $page ? get_permalink( $page->ID ) : home_url( '/' . $slug . '/' );
                ?>
                  <li class="menu-item">
                    <a href="<?php echo esc_url( $url ); ?>">
                      <?php echo esc_html( $label ); ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </nav>
        </div><!-- /.site-footer__brand -->

        <!-- Column 2: Contact + social ----------------------->
        <div class="site-footer__contact">
          <p class="site-footer__contact-heading">
            <?php esc_html_e( 'Get in touch', 'jaiye-journeys' ); ?>
          </p>

          <ul class="site-footer__contact-list">
            <li>
              <a href="mailto:hello@jaiyejourneys.com">
                hello@jaiyejourneys.com
              </a>
            </li>
          </ul>

          <div class="site-footer__social">
            <p class="site-footer__social-label">
              <?php esc_html_e( 'Follow our journey', 'jaiye-journeys' ); ?>
            </p>

            <ul class="site-footer__social-list">

              <!-- Instagram -->
              <li>
                <a
                  href="https://www.instagram.com/jaiyejourneys"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="<?php esc_attr_e( 'Jaiye Journeys on Instagram', 'jaiye-journeys' ); ?>"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/>
                  </svg>
                  <span class="sr-only">Instagram</span>
                </a>
              </li>

              <!-- TikTok -->
              <li>
                <a
                  href="https://tiktok.com/@jaiyejourneys1"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="<?php esc_attr_e( 'Jaiye Journeys on TikTok', 'jaiye-journeys' ); ?>"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V9.05a8.16 8.16 0 0 0 4.78 1.52V7.12a4.85 4.85 0 0 1-1.01-.43z"/>
                  </svg>
                  <span class="sr-only">TikTok</span>
                </a>
              </li>

              <!-- LinkedIn -->
              <li>
                <a
                  href="https://linkedin.com/company/jaiyejourneys"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="<?php esc_attr_e( 'Jaiye Journeys on LinkedIn', 'jaiye-journeys' ); ?>"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                    <rect x="2" y="9" width="4" height="12"/>
                    <circle cx="4" cy="4" r="2"/>
                  </svg>
                  <span class="sr-only">LinkedIn</span>
                </a>
              </li>

            </ul><!-- /.site-footer__social-list -->
          </div><!-- /.site-footer__social -->

        </div><!-- /.site-footer__contact -->

      </div><!-- /.site-footer__grid -->
    </div><!-- /.container -->
  </div><!-- /.site-footer__main -->

  <!-- Bottom bar -->
  <div class="site-footer__bottom">
    <div class="container">
      <p class="site-footer__copy">
        &copy; <?php echo esc_html( date( 'Y' ) ); ?>
        Jaiye Journeys.
        <?php esc_html_e( 'All rights reserved.', 'jaiye-journeys' ); ?>
      </p>
      <ul class="site-footer__legal">
        <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'jaiye-journeys' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'jaiye-journeys' ); ?></a></li>
      </ul>
    </div>
  </div><!-- /.site-footer__bottom -->

</footer><!-- /#site-footer -->

<style>
/* ------------------------------------------------------------------
   Footer component styles
   ------------------------------------------------------------------ */

.site-footer {
  background-color: var(--color-deep-green);
  color: var(--color-cream);
}

/* Main grid area */
.site-footer__main {
  padding-block: var(--space-16) var(--space-12);
}

.site-footer__grid {
  display: flex;
  flex-direction: column;
  gap: var(--space-12);
}

/* Column 1 — Brand */
.site-footer__logo-link {
  display: inline-block;
  line-height: 0;
  margin-bottom: var(--space-4);
}

.site-footer__logo-img {
  display: block;
  height: auto;
  width: auto;
  max-width: 160px;
}

.site-footer__wordmark {
  font-family: var(--font-heading);
  font-size: var(--text-xl);
  font-weight: var(--fw-semibold);
  color: var(--color-cream);
  display: inline-block;
  margin-bottom: var(--space-4);
}

.site-footer__wordmark:hover {
  color: var(--color-salmon);
}

.site-footer__tagline {
  font-size: var(--text-sm);
  font-weight: var(--fw-light);
  color: var(--color-sage);
  line-height: 1.7;
  max-width: 36ch;
  margin-bottom: var(--space-8);
}

/* Footer nav list */
.site-footer__nav-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.site-footer__nav-list .menu-item a,
.site-footer__nav-list li a {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--fw-light);
  letter-spacing: 0.04em;
  color: var(--color-cream);
  padding-block: var(--space-1);
  display: inline-block;
  transition: color var(--duration-fast) var(--ease-out);
  position: relative;
}

.site-footer__nav-list .menu-item a::after,
.site-footer__nav-list li a::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 0;
  height: 1px;
  background-color: var(--color-salmon);
  transition: width var(--duration-base) var(--ease-out);
}

.site-footer__nav-list .menu-item a:hover,
.site-footer__nav-list li a:hover {
  color: var(--color-salmon);
}

.site-footer__nav-list .menu-item a:hover::after,
.site-footer__nav-list li a:hover::after {
  width: 100%;
}

/* Column 2 — Contact */
.site-footer__contact-heading {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--color-sage);
  margin-bottom: var(--space-4);
}

.site-footer__contact-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  margin-bottom: var(--space-8);
}

.site-footer__contact-list a {
  font-size: var(--text-sm);
  font-weight: var(--fw-light);
  color: var(--color-cream);
  transition: color var(--duration-fast) var(--ease-out);
}

.site-footer__contact-list a:hover {
  color: var(--color-salmon);
}

/* Social links */
.site-footer__social-label {
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--color-sage);
  margin-bottom: var(--space-4);
}

.site-footer__social-list {
  display: flex;
  align-items: center;
  gap: var(--space-4);
}

.site-footer__social-list a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  color: var(--color-cream);
  border: 1px solid color-mix(in srgb, var(--color-sage) 40%, transparent);
  border-radius: var(--radius-full);
  transition:
    color var(--duration-fast) var(--ease-out),
    border-color var(--duration-fast) var(--ease-out),
    background-color var(--duration-fast) var(--ease-out);
}

.site-footer__social-list a:hover {
  color: var(--color-cream);
  background-color: var(--color-salmon);
  border-color: var(--color-salmon);
}

/* Bottom bar */
.site-footer__bottom {
  border-top: 1px solid color-mix(in srgb, var(--color-sage) 20%, transparent);
  padding-block: var(--space-6);
}

.site-footer__bottom .container {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.site-footer__copy {
  font-size: var(--text-xs);
  font-weight: var(--fw-light);
  color: color-mix(in srgb, var(--color-cream) 60%, transparent);
  max-width: none;
}

.site-footer__legal {
  display: flex;
  gap: var(--space-4);
}

.site-footer__legal a {
  font-size: var(--text-xs);
  font-weight: var(--fw-light);
  letter-spacing: 0.05em;
  color: color-mix(in srgb, var(--color-cream) 60%, transparent);
  transition: color var(--duration-fast) var(--ease-out);
}

.site-footer__legal a:hover {
  color: var(--color-salmon);
}

/* ---- Desktop (768px+) ------------------------------------------- */
@media (min-width: 768px) {

  .site-footer__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-16);
    align-items: center;
  }

  .site-footer__bottom .container {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}

@media (min-width: 1200px) {
  .site-footer__grid {
    grid-template-columns: 3fr 2fr;
    gap: var(--space-24);
  }
}
</style>

<!-- ============================================================
     Nav toggle — vanilla JS, no dependencies
     ============================================================ -->
<script>
(function () {
  'use strict';

  var toggle = document.querySelector('.nav-toggle');
  var nav    = document.getElementById('primary-menu');
  var body   = document.body;

  if (!toggle || !nav) return;

  function openNav() {
    toggle.setAttribute('aria-expanded', 'true');
    toggle.setAttribute('aria-label', 'Close navigation menu');
    nav.classList.add('is-open');
    body.classList.add('nav-is-open');
    // Trap focus within nav on first focusable element
    var firstFocusable = nav.querySelector('a, button');
    if (firstFocusable) firstFocusable.focus();
  }

  function closeNav() {
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open navigation menu');
    nav.classList.remove('is-open');
    body.classList.remove('nav-is-open');
  }

  toggle.addEventListener('click', function () {
    if (toggle.getAttribute('aria-expanded') === 'true') {
      closeNav();
      toggle.focus();
    } else {
      openNav();
    }
  });

  // Close when clicking the overlay (outside the nav)
  body.addEventListener('click', function (e) {
    if (
      nav.classList.contains('is-open') &&
      !nav.contains(e.target) &&
      !toggle.contains(e.target)
    ) {
      closeNav();
    }
  });

  // Close on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && nav.classList.contains('is-open')) {
      closeNav();
      toggle.focus();
    }
  });

  // Close nav if viewport crosses the 768px tablet breakpoint
  var mq = window.matchMedia('(min-width: 768px)');
  mq.addEventListener('change', function (e) {
    if (e.matches) closeNav();
  });
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
