<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content">
  <?php esc_html_e( 'Skip to content', 'jaiye-journeys' ); ?>
</a>

<!-- ============================================================
     Site Header
     Mobile: logo left, hamburger right, nav hidden in drawer
     Desktop (768px+): logo left, nav + CTA right, no hamburger
     ============================================================ -->
<header class="site-header" id="site-header" role="banner">
  <div class="container">
    <div class="site-header__inner">

      <!-- Logo ------------------------------------------------>
      <div class="site-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-logo__link" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — <?php esc_attr_e( 'Return to homepage', 'jaiye-journeys' ); ?>">
          <img
            src="/wp-content/themes/jaiye-journeys-theme/assets/images/logo.png"
            alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
            class="site-logo__img"
          >
        </a>
      </div>

      <!-- Nav drawer (mobile hidden, desktop visible) -------->
      <nav
        class="site-nav"
        id="primary-menu"
        aria-label="<?php esc_attr_e( 'Primary navigation', 'jaiye-journeys' ); ?>"
      >
        <?php
        wp_nav_menu( [
            'theme_location' => 'primary',
            'menu_class'     => 'site-nav__list',
            'container'      => false,
            'fallback_cb'    => 'jaiye_fallback_nav_menu',
            'depth'          => 2,
        ] );
        ?>

        <!-- CTA inside nav drawer on mobile -->
        <a
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          class="btn btn--plan-trip site-nav__cta-mobile"
        >
          <?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?>
        </a>
      </nav>

      <!-- CTA button (desktop only) -------------------------->
      <a
        href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
        class="btn btn--plan-trip site-header__cta-desktop"
        aria-label="<?php esc_attr_e( 'Plan My Trip — contact us', 'jaiye-journeys' ); ?>"
      >
        <?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?>
      </a>

      <!-- Hamburger toggle (mobile only) -------------------->
      <button
        class="nav-toggle"
        aria-controls="primary-menu"
        aria-expanded="false"
        aria-label="<?php esc_attr_e( 'Open navigation menu', 'jaiye-journeys' ); ?>"
        type="button"
      >
        <span class="nav-toggle__bar" aria-hidden="true"></span>
        <span class="nav-toggle__bar" aria-hidden="true"></span>
        <span class="nav-toggle__bar" aria-hidden="true"></span>
      </button>

    </div><!-- /.site-header__inner -->
  </div><!-- /.container -->
</header><!-- /#site-header -->

<style>
/* ------------------------------------------------------------------
   Header & Nav component styles
   ------------------------------------------------------------------ */

/* Header shell */
.site-header {
  position: sticky;
  top: 0;
  z-index: var(--z-nav);
  background-color: var(--color-cream);
  border-bottom: 1px solid var(--color-border);
  padding-block: var(--space-4);
}

.site-header__inner {
  display: flex;
  align-items: center;
  gap: var(--space-4);
}

/* Logo */
.site-logo {
  flex-shrink: 0;
  min-width: 0;
  margin-right: auto; /* pushes everything else to the right */
}

.site-logo__link {
  display: block;
  line-height: 0; /* removes inline gap below img */
}

.site-logo__img {
  display: block;
  height: auto;
  width: auto;
  max-width: 120px; /* mobile */
}

/* Plan My Trip CTA */
.btn--plan-trip {
  display: inline-flex;
  align-items: center;
  padding: var(--space-3) var(--space-8);
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: var(--fw-regular);
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #ffffff;
  background-color: var(--color-salmon);
  border: 1px solid var(--color-salmon);
  border-radius: var(--radius-sm);
  white-space: nowrap;
  transition:
    background-color var(--duration-base) var(--ease-out),
    border-color var(--duration-base) var(--ease-out),
    transform var(--duration-fast) var(--ease-out);
}

.btn--plan-trip:hover {
  background-color: color-mix(in srgb, var(--color-salmon) 82%, black);
  border-color: color-mix(in srgb, var(--color-salmon) 82%, black);
  color: #ffffff;
  transform: translateY(-1px);
}

.btn--plan-trip:active {
  transform: translateY(0);
}

/* Desktop CTA: hidden on mobile, shown on tablet+ */
.site-header__cta-desktop {
  display: none;
}

/* Nav — mobile drawer */
.site-nav {
  position: fixed;
  inset: 0 0 0 30%;  /* slides in from right, leaves 30% visible */
  z-index: var(--z-overlay);
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
  padding: var(--space-16) var(--space-8) var(--space-8);
  background-color: var(--color-forest);
  transform: translateX(100%);
  transition: transform var(--duration-slow) var(--ease-out);
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.site-nav.is-open {
  transform: translateX(0);
}

/* Nav list */
.site-nav__list {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.site-nav__list .menu-item a {
  display: block;
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
  padding-block: var(--space-2);
  border-bottom: 1px solid color-mix(in srgb, var(--color-sage) 30%, transparent);
  transition: color var(--duration-fast) var(--ease-out);
}

.site-nav__list .menu-item a:hover,
.site-nav__list .menu-item.current-menu-item a {
  color: var(--color-salmon);
}

/* Mobile CTA inside drawer */
.site-nav__cta-mobile {
  margin-top: auto;
  align-self: flex-start;
}

/* Overlay behind open drawer */
body.nav-is-open::before {
  content: '';
  position: fixed;
  inset: 0;
  z-index: calc(var(--z-overlay) - 1);
  background-color: color-mix(in srgb, var(--color-deep-green) 70%, transparent);
  backdrop-filter: blur(2px);
}

/* Hamburger button */
.nav-toggle {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 40px;
  height: 40px;
  padding: var(--space-2);
  flex-shrink: 0;
  border-radius: var(--radius-sm);
  transition: background-color var(--duration-fast) var(--ease-out);
}

.nav-toggle:hover {
  background-color: color-mix(in srgb, var(--color-sage) 20%, transparent);
}

.nav-toggle:focus-visible {
  outline: 2px solid var(--color-salmon);
  outline-offset: 3px;
}

.nav-toggle__bar {
  display: block;
  width: 22px;
  height: 1.5px;
  background-color: var(--color-forest);
  border-radius: 2px;
  transition:
    transform var(--duration-base) var(--ease-out),
    opacity var(--duration-base) var(--ease-out);
  transform-origin: center;
}

/* Animate hamburger → X when open */
.nav-toggle[aria-expanded="true"] .nav-toggle__bar:nth-child(1) {
  transform: translateY(6.5px) rotate(45deg);
}

.nav-toggle[aria-expanded="true"] .nav-toggle__bar:nth-child(2) {
  opacity: 0;
  transform: scaleX(0);
}

.nav-toggle[aria-expanded="true"] .nav-toggle__bar:nth-child(3) {
  transform: translateY(-6.5px) rotate(-45deg);
}

/* ---- Tablet and desktop (768px+) --------------------------------- */
@media (min-width: 768px) {

  .site-header {
    padding-block: var(--space-4);
  }

  .site-logo__img {
    max-width: 130px;
  }

  /* Show desktop CTA, hide mobile one */
  .site-header__cta-desktop { display: inline-flex; }
  .site-nav__cta-mobile     { display: none; }

  /* Hide hamburger */
  .nav-toggle { display: none; }

  /* Nav becomes an inline horizontal bar */
  .site-nav {
    position: static;
    flex-direction: row;
    align-items: center;
    gap: 0;
    padding: 0;
    background-color: transparent;
    transform: none;
    overflow: visible;
  }

  .site-nav__list {
    flex-direction: row;
    align-items: center;
    gap: 32px;
  }

  .site-nav__list .menu-item a {
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: var(--fw-regular);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--color-forest);
    padding: var(--space-2) 0;
    border-bottom: none;
    border-radius: var(--radius-sm);
    transition:
      color var(--duration-fast) var(--ease-out),
      background-color var(--duration-fast) var(--ease-out);
  }

  .site-nav__list .menu-item a:hover {
    color: var(--color-salmon);
    background-color: color-mix(in srgb, var(--color-salmon) 8%, transparent);
  }

  .site-nav__list .menu-item.current-menu-item a {
    color: var(--color-salmon);
  }

  /* No overlay needed on desktop */
  body.nav-is-open::before { display: none; }
}

@media (min-width: 1200px) {
  .site-nav__list { gap: 32px; }

  .site-nav__list .menu-item a {
    font-size: 13px;
    padding: var(--space-2) 0;
  }
}
</style>
