<?php
/*
 * Template Name: About
 */

get_header();

$partners = [
  'partner-Virtuoso.png'                          => 'Virtuoso',
  'partner-Four-Seasons.png'                      => 'Four Seasons',
  'partner-rosewood.png'                          => 'Rosewood Hotels',
  'partner-The_Leading_Hotels-of-the-World.png'   => 'The Leading Hotels of the World',
  'partner-Oetker-Collection.png'                 => 'Oetker Collection',
  'partner-dorchester-collection.png'             => 'Dorchester Collection',
  'partner-belmond-bellini-club.png'              => 'Belmond Bellini Club',
  'partner-one-only.png'                          => 'One&Only Resorts',
  'partner-Design-Hotels.png'                     => 'Design Hotels',
  'partner-Hyatt-Prive.png'                       => 'Hyatt Privé',
  'partner-hilton-impresario.png'                 => 'Hilton Impresario',
  'partner-InterContinental-Hotels-Group-Logo.png'=> 'InterContinental Hotels Group',
  'partner-jumeirah.png'                          => 'Jumeirah',
  'partner-Standard-Hotels.png'                   => 'Standard Hotels',
  'partner-firmdale.png'                          => 'Firmdale Hotels',
  'partner-slh.png'                               => 'Small Luxury Hotels of the World',
  'partner-preferred-hotels-and-resorts.png'      => 'Preferred Hotels & Resorts',
  'partner-relais.png'                            => 'Relais & Châteaux',
  'partner-Fontenille.png'                        => 'Fontenille',
  'partner-COOLROOMS.png'                         => 'Coolrooms',
  'partner-Club1897.png'                          => 'Club 1897',
  'partner-Couture.png'                           => 'Couture Hotels',
  'partner-Omni.png'                              => 'Omni Hotels',
  'partner-RF.Knights.Black.png'                  => 'RF Knights',
  'partner-fan-club.png'                          => 'Fan Club',
  'partner-mercer.png'                            => 'Mercer Collection',
  'partner-pennclub.png'                          => 'Penn Club',
];
?>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. HERO
       Full viewport height. steph.jpg background, object-position
       center top so her face is visible. 50% dark overlay.
       ============================================================ -->
  <section
    class="ab-hero"
    aria-label="<?php esc_attr_e( 'About Steph, founder of Jaiye Journeys', 'jaiye-journeys' ); ?>"
  >
    <div class="ab-hero__media" aria-hidden="true">
      <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph-hero.jpg' ); ?>"
        alt=""
        class="ab-hero__bg-img"
        loading="eager"
        decoding="async"
        fetchpriority="high"
      >
      <div class="ab-hero__overlay"></div>
    </div>

    <div class="container ab-hero__inner">
      <div class="ab-hero__content">
        <h1 class="ab-hero__heading">
          <?php esc_html_e( "Hey, I'm Steph.", 'jaiye-journeys' ); ?>
        </h1>
        <p class="ab-hero__sub">
          <?php esc_html_e( 'Founder, curator, and the person who handles everything so you don\'t have to.', 'jaiye-journeys' ); ?>
        </p>
      </div>
    </div>
  </section><!-- /.ab-hero -->


  <!-- ============================================================
       2. INTRO STRIP
       Cream background.
       Desktop: 2-column — 60% pull quote | 40% full-bleed image.
       Mobile: stacked, image below quote.
       ============================================================ -->
  <section
    class="ab-intro"
    aria-label="<?php esc_attr_e( 'Introduction', 'jaiye-journeys' ); ?>"
  >
    <div class="ab-intro__quote-col">
      <blockquote class="ab-intro__quote">
        <?php esc_html_e( 'Jaiye means enjoy life. That is not a brand name. It is a life philosophy.', 'jaiye-journeys' ); ?>
      </blockquote>
      <p class="ab-intro__body">
        <?php esc_html_e( 'I am Regina Stephanie Jaiyeola, known professionally as Regina Jaiy, known personally as Steph. I am a Nigerian-British creative, traveller, and the founder of Jaiye Journeys. I built this for people who love to travel but hate the logistics. People like me.', 'jaiye-journeys' ); ?>
      </p>
    </div>

    <div class="ab-intro__image-col" aria-hidden="true">
      <img
        src="/wp-content/themes/jaiye-journeys-theme/assets/images/steph-intro.jpg?v=2"
        alt=""
        class="ab-intro__img"
        loading="lazy"
      >
    </div>
  </section><!-- /.ab-intro -->


  <!-- ============================================================
       3. STORY SECTION
       Forest green background, cream text. Single column, centred.
       ============================================================ -->
  <section
    class="ab-story"
    aria-label="<?php esc_attr_e( 'My story', 'jaiye-journeys' ); ?>"
  >

    <div class="ab-story__media" aria-hidden="true">
      <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph-story.jpg' ); ?>"
        alt=""
        class="ab-story__img"
        loading="lazy"
      >
    </div>

    <div class="ab-story__content">
      <h2 class="ab-story__heading">
        <?php esc_html_e( 'My Story', 'jaiye-journeys' ); ?>
      </h2>
      <div class="ab-story__text">
        <p>
          <?php esc_html_e( 'I have always been a traveller. Growing up in a Nigerian family where movement was part of life, I learned early that travel is not just about the destination, it is about what it does to you. The way it shifts your perspective, expands your world, and reminds you what actually matters.', 'jaiye-journeys' ); ?>
        </p>
        <p>
          <?php esc_html_e( 'My career took me through over a decade in media and creative production. As Fashion Editor and Creative Director at GUAP Magazine, I directed national campaigns for Nike and Adidas, produced shoots across multiple countries, and learned that the details are everything. The flow of a day, the atmosphere of a space, the quality of what people experience - these things are not accidents. They are designed.', 'jaiye-journeys' ); ?>
        </p>
        <p>
          <?php esc_html_e( 'From GUAP I moved into media planning at Hearst UK, working across some of the most iconic titles in British publishing - Cosmopolitan, Elle, Harper\'s Bazaar, Elle Decoration, and Women\'s Health. Managing campaigns at that scale taught me how budgets work, how brands think, and how to connect the right message with the right audience. It also gave me an understanding of the commercial side of storytelling that most creative directors never get.', 'jaiye-journeys' ); ?>
        </p>
        <p>
          <?php esc_html_e( 'Then came Remote Year: twelve countries, one year, and the most intensive education in group travel logistics I could have asked for. Managing the human complexity of moving a community of people across the world taught me things no course could. It also confirmed something I had suspected for a while: I am very good at this, and I genuinely love it.', 'jaiye-journeys' ); ?>
        </p>
        <p>
          <?php esc_html_e( 'Jaiye Journeys was born out of a simple belief. Travel should be a joy from the very first step of planning, not a source of overwhelm. I handle the work so you can focus on the enjoyment. That is the whole thing.', 'jaiye-journeys' ); ?>
        </p>
      </div>
    </div>

  </section><!-- /.ab-story -->


  <!-- ============================================================
       4. CREDENTIALS STRIP
       Cream background. 2×2 on mobile, 4-in-a-row on desktop.
       ============================================================ -->
  <section
    class="ab-credentials"
    aria-label="<?php esc_attr_e( 'The producer behind the journey', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <h2 class="ab-credentials__heading">
        <?php esc_html_e( 'The Producer Behind the Journey', 'jaiye-journeys' ); ?>
      </h2>

      <ul class="ab-credentials__grid" role="list">

        <li class="ab-credential">
          <span class="ab-credential__number" aria-hidden="true">01</span>
          <h3 class="ab-credential__title">
            <?php esc_html_e( 'Creative Direction', 'jaiye-journeys' ); ?>
          </h3>
          <p class="ab-credential__detail">
            <?php esc_html_e( 'Every Jaiye Journey is editorially considered. My background as Fashion Editor and Creative Director at GUAP Magazine means the details are never left to chance.', 'jaiye-journeys' ); ?>
          </p>
        </li>

        <li class="ab-credential">
          <span class="ab-credential__number" aria-hidden="true">02</span>
          <h3 class="ab-credential__title">
            <?php esc_html_e( 'Global Logistics', 'jaiye-journeys' ); ?>
          </h3>
          <p class="ab-credential__detail">
            <?php esc_html_e( 'Remote Year Community Leader across 12+ countries. Managing the human complexity of group travel is not new to me.', 'jaiye-journeys' ); ?>
          </p>
        </li>

        <li class="ab-credential">
          <span class="ab-credential__number" aria-hidden="true">03</span>
          <h3 class="ab-credential__title">
            <?php esc_html_e( 'Industry Access', 'jaiye-journeys' ); ?>
          </h3>
          <p class="ab-credential__detail">
            <?php esc_html_e( 'Certified FORA travel advisor. My clients access VIP perks, supplier partnerships, and upgrades simply not available when booking direct.', 'jaiye-journeys' ); ?>
          </p>
        </li>

        <li class="ab-credential">
          <span class="ab-credential__number" aria-hidden="true">04</span>
          <h3 class="ab-credential__title">
            <?php esc_html_e( 'Cultural Fluency', 'jaiye-journeys' ); ?>
          </h3>
          <p class="ab-credential__detail">
            <?php esc_html_e( 'Nigerian-British, globally travelled, and deeply invested in authentic local experiences over tourist packages.', 'jaiye-journeys' ); ?>
          </p>
        </li>

      </ul>
    </div>
  </section><!-- /.ab-credentials -->


  <!-- ============================================================
       5. FORA SECTION
       Deep green background. Centred.
       ============================================================ -->
  <section
    class="ab-fora"
    aria-label="<?php esc_attr_e( 'Powered by FORA', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="ab-fora__inner">

        <p class="overline ab-fora__eyebrow">
          <?php esc_html_e( 'Powered By', 'jaiye-journeys' ); ?>
        </p>

        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Fora-Vector.png' ); ?>"
          alt=""
          class="ab-fora__monogram"
          loading="lazy"
          aria-hidden="true"
        >

        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/fora-badge.png' ); ?>"
          alt="<?php esc_attr_e( 'FORA travel advisor network badge', 'jaiye-journeys' ); ?>"
          class="ab-fora__logo"
          loading="lazy"
        >

        <p class="ab-fora__body">
          <?php esc_html_e( 'Jaiye Journeys is powered by FORA, the world\'s fastest-growing travel advisor network. Being a certified FORA advisor means my clients access VIP perks, room upgrades, complimentary amenities, and supplier commissions that are simply not available when booking direct.', 'jaiye-journeys' ); ?>
        </p>

      </div>
    </div>
  </section><!-- /.ab-fora -->


  <!-- ============================================================
       6. PARTNERS STRIP
       Cream background. Auto-scrolling logo marquee.
       Logos duplicated in the track for seamless CSS loop.
       ============================================================ -->
  <section
    class="ab-partners"
    aria-label="<?php esc_attr_e( 'Our hotel and travel partners', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <h2 class="ab-partners__heading">
        <?php esc_html_e( 'Our Partners', 'jaiye-journeys' ); ?>
      </h2>
      <p class="ab-partners__sub">
        <?php esc_html_e( 'Through FORA, Jaiye Journeys clients access some of the world\'s most exceptional hotel partnerships.', 'jaiye-journeys' ); ?>
      </p>
    </div>

    <div class="ab-partners__marquee">
      <div class="ab-partners__track" aria-hidden="true">

        <?php
        // First pass — semantic logos
        foreach ( $partners as $file => $name ) :
          $src = get_template_directory_uri() . '/assets/images/' . $file;
        ?>
          <img
            src="<?php echo esc_url( $src ); ?>"
            alt="<?php echo esc_attr( $name ); ?>"
            class="ab-partners__logo"
            loading="lazy"
          >
        <?php endforeach; ?>

        <?php
        // Second pass — duplicate for seamless loop
        foreach ( $partners as $file => $name ) :
          $src = get_template_directory_uri() . '/assets/images/' . $file;
        ?>
          <img
            src="<?php echo esc_url( $src ); ?>"
            alt=""
            class="ab-partners__logo"
            loading="lazy"
          >
        <?php endforeach; ?>

      </div>
    </div>
  </section><!-- /.ab-partners -->


  <!-- ============================================================
       7. AROUND THE WORLD IN NUMBERS
       Cream background. 2×2 stat grid on mobile, 4-col on desktop.
       ============================================================ -->
  <section
    class="ab-stats"
    aria-label="<?php esc_attr_e( 'Around the world in numbers', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <p class="overline ab-stats__overline">
        <?php esc_html_e( 'By the numbers', 'jaiye-journeys' ); ?>
      </p>
      <h2 class="ab-stats__heading">
        <?php esc_html_e( 'A life lived in motion.', 'jaiye-journeys' ); ?>
      </h2>

      <ul class="ab-stats__grid" role="list">

        <li class="ab-stat">
          <span class="ab-stat__number">27</span>
          <span class="ab-stat__label"><?php esc_html_e( 'Countries Visited', 'jaiye-journeys' ); ?></span>
          <span class="ab-stat__desc"><?php esc_html_e( 'and counting', 'jaiye-journeys' ); ?></span>
        </li>

        <li class="ab-stat">
          <span class="ab-stat__number">293</span>
          <span class="ab-stat__label"><?php esc_html_e( 'Books Read in a Year', 'jaiye-journeys' ); ?></span>
          <span class="ab-stat__desc"><?php esc_html_e( '147 in 2026 and it\'s only May', 'jaiye-journeys' ); ?></span>
        </li>

        <li class="ab-stat">
          <span class="ab-stat__number">84+</span>
          <span class="ab-stat__label"><?php esc_html_e( 'Travellers Hosted', 'jaiye-journeys' ); ?></span>
          <span class="ab-stat__desc"><?php esc_html_e( 'across 5 continents', 'jaiye-journeys' ); ?></span>
        </li>

        <li class="ab-stat">
          <span class="ab-stat__number">3</span>
          <span class="ab-stat__label"><?php esc_html_e( 'Destinations on Repeat', 'jaiye-journeys' ); ?></span>
          <span class="ab-stat__desc"><?php esc_html_e( 'Indonesia. Peru. Colombia.', 'jaiye-journeys' ); ?></span>
        </li>

      </ul>
    </div>
  </section><!-- /.ab-stats -->


  <!-- ============================================================
       8. IN GOOD COMPANY
       Forest green background. Polaroid collage.
       Mobile: horizontal scroll strip. Desktop: overlapping row.
       ============================================================ -->
  <section
    class="ab-collage"
    aria-label="<?php esc_attr_e( 'In good company', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <p class="overline ab-collage__overline">
        <?php esc_html_e( 'The world through my lens', 'jaiye-journeys' ); ?>
      </p>
      <h2 class="ab-collage__heading">
        <?php esc_html_e( 'Every trip leaves a mark.', 'jaiye-journeys' ); ?>
      </h2>
    </div>

    <div class="ab-collage__strip" aria-hidden="true">

      <!-- card 1 -->
      <div class="ab-collage__card" style="--card-transform: rotate(-5deg) translateY(10px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph-desert-quad.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 2 -->
      <div class="ab-collage__card" style="--card-transform: rotate(3deg) translateY(-15px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph-water.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 3 -->
      <div class="ab-collage__card" style="--card-transform: rotate(-2deg) translateY(20px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/steph-marrakech.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 4 -->
      <div class="ab-collage__card" style="--card-transform: rotate(6deg) translateY(-8px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-tuscany.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 5 -->
      <div class="ab-collage__card" style="--card-transform: rotate(-4deg) translateY(15px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-bali-pool.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 6 -->
      <div class="ab-collage__card" style="--card-transform: rotate(2deg) translateY(-20px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-tea.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 7 -->
      <div class="ab-collage__card" style="--card-transform: rotate(-7deg) translateY(5px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-lobby.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 8 -->
      <div class="ab-collage__card" style="--card-transform: rotate(4deg) translateY(-20px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-riad-pool.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 9 -->
      <div class="ab-collage__card" style="--card-transform: rotate(-2deg) translateY(15px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-terrace.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 10 -->
      <div class="ab-collage__card" style="--card-transform: rotate(8deg) translateY(-10px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-yacht.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 11 -->
      <div class="ab-collage__card" style="--card-transform: rotate(-5deg) translateY(20px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/travel-sea.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

      <!-- card 12 -->
      <div class="ab-collage__card" style="--card-transform: rotate(3deg) translateY(-15px)">
        <img
          src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trip-bahia.jpg' ); ?>"
          alt="" class="ab-collage__img" loading="lazy"
        >
      </div>

    </div>
  </section><!-- /.ab-collage -->


  <!-- ============================================================
       9. THE STEPH EDIT
       Cream background. Q&A accordion. First item open on load.
       ============================================================ -->
  <section
    class="ab-qa"
    aria-label="<?php esc_attr_e( 'The Steph Edit', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <p class="overline ab-qa__overline">
        <?php esc_html_e( 'A little more about me', 'jaiye-journeys' ); ?>
      </p>
      <h2 class="ab-qa__heading">
        <?php esc_html_e( 'Things you didn\'t ask but I\'m telling you anyway.', 'jaiye-journeys' ); ?>
      </h2>

      <div class="ab-qa__list">

        <div class="ab-qa__item">
          <button class="ab-qa__trigger" aria-expanded="false" type="button">
            <span class="ab-qa__question"><?php esc_html_e( 'What are your top three destinations and why?', 'jaiye-journeys' ); ?></span>
            <span class="ab-qa__icon" aria-hidden="true">+</span>
          </button>
          <div class="ab-qa__body">
            <p class="ab-qa__answer"><?php esc_html_e( 'Indonesia for the pace and the beauty -- I keep going back and finding new reasons to stay longer. Peru for the food, the altitude, and the fact that nothing prepares you for Cusco. Colombia for the energy, the colour, and the coffee. I could talk about all three indefinitely.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="ab-qa__item">
          <button class="ab-qa__trigger" aria-expanded="false" type="button">
            <span class="ab-qa__question"><?php esc_html_e( 'What is your favourite way to explore a new city?', 'jaiye-journeys' ); ?></span>
            <span class="ab-qa__icon" aria-hidden="true">+</span>
          </button>
          <div class="ab-qa__body">
            <p class="ab-qa__answer"><?php esc_html_e( 'On foot first, always. Get completely lost, find a cafe, sit down, watch people. Then come back the next day and actually find the things I had been walking towards.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="ab-qa__item">
          <button class="ab-qa__trigger" aria-expanded="false" type="button">
            <span class="ab-qa__question"><?php esc_html_e( 'What song is always on your travel playlist?', 'jaiye-journeys' ); ?></span>
            <span class="ab-qa__icon" aria-hidden="true">+</span>
          </button>
          <div class="ab-qa__body">
            <p class="ab-qa__answer"><?php esc_html_e( 'Golden by Jill Scott. Without fail. It sets the tone for every arrival.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="ab-qa__item">
          <button class="ab-qa__trigger" aria-expanded="false" type="button">
            <span class="ab-qa__question"><?php esc_html_e( 'What do you always pack that other people think is unnecessary?', 'jaiye-journeys' ); ?></span>
            <span class="ab-qa__icon" aria-hidden="true">+</span>
          </button>
          <div class="ab-qa__body">
            <p class="ab-qa__answer"><?php esc_html_e( 'An actual book. Several, actually. People always have opinions about the weight until they see me reading by a pool while everyone else is on their phones.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="ab-qa__item">
          <button class="ab-qa__trigger" aria-expanded="false" type="button">
            <span class="ab-qa__question"><?php esc_html_e( 'What is your travel non-negotiable?', 'jaiye-journeys' ); ?></span>
            <span class="ab-qa__icon" aria-hidden="true">+</span>
          </button>
          <div class="ab-qa__body">
            <p class="ab-qa__answer"><?php esc_html_e( 'A good breakfast. Not necessarily expensive -- just intentional. The best mornings abroad start slowly, with something local and unhurried.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

        <div class="ab-qa__item">
          <button class="ab-qa__trigger" aria-expanded="false" type="button">
            <span class="ab-qa__question"><?php esc_html_e( 'What would your dream itinerary look like?', 'jaiye-journeys' ); ?></span>
            <span class="ab-qa__icon" aria-hidden="true">+</span>
          </button>
          <div class="ab-qa__body">
            <p class="ab-qa__answer"><?php esc_html_e( 'Two weeks. Start in Bali for five days of genuine stillness. Then Colombia for the energy -- Cartagena and then Medellín. End in Lisbon with absolutely nothing planned. A book, a pastel de nata, and nowhere to be.', 'jaiye-journeys' ); ?></p>
          </div>
        </div>

      </div><!-- /.ab-qa__list -->

    </div>
  </section><!-- /.ab-qa -->

  <script>
  (function () {
    'use strict';

    var items = document.querySelectorAll('.ab-qa__item');
    if (!items.length) return;

    function openItem(item) {
      var trigger = item.querySelector('.ab-qa__trigger');
      var body    = item.querySelector('.ab-qa__body');
      var icon    = item.querySelector('.ab-qa__icon');
      body.style.maxHeight  = body.scrollHeight + 'px';
      trigger.setAttribute('aria-expanded', 'true');
      icon.textContent = '−';
      item.classList.add('is-open');
    }

    function closeItem(item) {
      var trigger = item.querySelector('.ab-qa__trigger');
      var body    = item.querySelector('.ab-qa__body');
      var icon    = item.querySelector('.ab-qa__icon');
      body.style.maxHeight = '0';
      trigger.setAttribute('aria-expanded', 'false');
      icon.textContent = '+';
      item.classList.remove('is-open');
    }

    // Open first item on load
    openItem(items[0]);

    items.forEach(function (item) {
      item.querySelector('.ab-qa__trigger').addEventListener('click', function () {
        if (item.classList.contains('is-open')) {
          closeItem(item);
        } else {
          items.forEach(function (other) {
            if (other !== item && other.classList.contains('is-open')) closeItem(other);
          });
          openItem(item);
        }
      });
    });
  })();
  </script>


  <!-- ============================================================
       10. TESTIMONIAL
       Sage background. Single large centred quote.
       ============================================================ -->
  <section
    class="ab-testimonial"
    aria-label="<?php esc_attr_e( 'Client testimonial', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="ab-testimonial__inner">

        <figure class="ab-testimonial__figure">
          <blockquote class="ab-testimonial__quote">
            <p>
              <?php esc_html_e( 'Steph gave me the feeling that I could truly enjoy my holiday because everything was taken care of. Even when things go awry, Steph made sure the experience was seamless. And besides all that, she was an absolute joy to be around.', 'jaiye-journeys' ); ?>
            </p>
          </blockquote>
          <figcaption class="ab-testimonial__cite">
            <?php esc_html_e( 'Anca B, Vancouver, Canada', 'jaiye-journeys' ); ?>
          </figcaption>
        </figure>

      </div>
    </div>
  </section><!-- /.ab-testimonial -->


  <!-- ============================================================
       11. CTA STRIP
       Forest green background, cream text. Two buttons.
       ============================================================ -->
  <section
    class="ab-cta"
    aria-label="<?php esc_attr_e( 'Plan your next journey', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="ab-cta__inner">

        <h2 class="ab-cta__heading">
          <?php esc_html_e( 'Ready to plan your next journey?', 'jaiye-journeys' ); ?>
        </h2>
        <p class="ab-cta__sub">
          <?php esc_html_e( 'Tell me where you want to go. I will handle everything else.', 'jaiye-journeys' ); ?>
        </p>

        <div class="ab-cta__buttons">
          <a
            href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
            class="btn btn--ghost"
          >
            <?php esc_html_e( 'Plan My Trip', 'jaiye-journeys' ); ?>
          </a>
          <a
            href="<?php echo esc_url( home_url( '/our-journeys/' ) ); ?>"
            class="btn btn--ghost btn--ghost-outline"
          >
            <?php esc_html_e( 'View Our Journeys', 'jaiye-journeys' ); ?>
          </a>
        </div>

      </div>
    </div>
  </section><!-- /.ab-cta -->

</main><!-- /#main-content -->

<?php get_footer(); ?>
