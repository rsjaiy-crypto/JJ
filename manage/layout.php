<?php
/**
 * Dashboard shell: header, the screen, and the tab bar (bottom on phones,
 * top on wider screens). Set up in jj_manage_render(); $view and $id come
 * from there. Deliberately not the site's header and footer: no tracking,
 * no marketing chrome, just the tools.
 *
 * @package jaiye-journeys
 */

defined( 'ABSPATH' ) || exit;

require __DIR__ . '/parts.php';

$tabs = [
    'overview' => [ __( 'Overview', 'jaiye-journeys' ), jj_manage_url(), [ 'overview' ] ],
    'bookings' => [ __( 'Bookings', 'jaiye-journeys' ), jj_manage_url( 'bookings' ), [ 'bookings', 'booking' ] ],
    'trips'    => [ __( 'Trips', 'jaiye-journeys' ), jj_manage_url( 'trips' ), [ 'trips', 'trip' ] ],
];
$asset = function ( $path ) {
    return esc_url( get_template_directory_uri() . '/' . $path . '?v=' . filemtime( get_template_directory() . '/' . $path ) );
};
?><!doctype html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="robots" content="noindex, nofollow">
  <title><?php esc_html_e( 'Jaiye Journeys · Manage', 'jaiye-journeys' ); ?></title>
  <meta name="theme-color" content="#093923">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="<?php esc_attr_e( 'JJ Manage', 'jaiye-journeys' ); ?>">
  <link rel="manifest" href="<?php echo $asset( 'manage/manifest.json' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>">
  <link rel="apple-touch-icon" href="<?php echo $asset( 'manage/icon-180.png' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
  <link rel="icon" type="image/png" href="<?php echo $asset( 'manage/icon-192.png' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,600;1,400&family=Jost:wght@300;400;600&display=swap">
  <link rel="stylesheet" href="<?php echo $asset( 'global.css' ); // phpcs:ignore WordPress.Security.EscapeOutput -- design tokens and reset. ?>">
  <link rel="stylesheet" href="<?php echo $asset( 'css/manage.css' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
</head>
<body class="m">
  <header class="m-top">
    <a class="m-top__brand" href="<?php echo esc_url( jj_manage_url() ); ?>">Jaiye Journeys</a>
    <nav class="m-tabs" aria-label="<?php esc_attr_e( 'Dashboard', 'jaiye-journeys' ); ?>">
      <?php foreach ( $tabs as $key => $tab ) : ?>
        <a class="m-tabs__link<?php echo in_array( $view, $tab[2], true ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $tab[1] ); ?>"<?php echo in_array( $view, $tab[2], true ) ? ' aria-current="page"' : ''; ?>>
          <?php echo jj_manage_icon( $key ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed SVG. ?>
          <span><?php echo esc_html( $tab[0] ); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </header>

  <main class="m-main">
    <?php require __DIR__ . '/' . $view . '.php'; ?>
  </main>

  <script src="<?php echo $asset( 'js/manage.js' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"></script>
</body>
</html>
