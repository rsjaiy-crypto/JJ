<?php
/*
 * Template Name: FAQ
 */

get_header();

$faq_categories = [
  [
    'heading' => __( 'The Process', 'jaiye-journeys' ),
    'items'   => [
      [
        'q' => __( 'How does working with Jaiye Journeys actually work?', 'jaiye-journeys' ),
        'a' => __( 'Tell me where you want to go and what matters most. I design the options, you pick your favourite, and the only thing left on your list is showing up.', 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'How far in advance should I get in touch?', 'jaiye-journeys' ),
        'a' => __( 'For group trips and weddings, as early as you can. Six to twelve months out gives us the best shot at the right property and the right rate before someone else books it. Solo and couples travel is more forgiving, though I can move fast if your timeline is tight.', 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'Do I need to know exactly where I want to go?', 'jaiye-journeys' ),
        'a' => __( 'Not even a little. Some clients arrive with a destination already picked. Others just know how they want to feel when they land. Either one is a good place to start.', 'jaiye-journeys' ),
      ],
    ],
  ],
  [
    'heading' => __( 'Booking & Payment', 'jaiye-journeys' ),
    'items'   => [
      [
        'q' => __( 'What do I need to pay to secure my spot?', 'jaiye-journeys' ),
        'a' => __( "A 25% deposit holds your place, and it's non-refundable from the moment it's paid. The rest is due before departure, per your trip's payment schedule.", 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'Can I pay in instalments?', 'jaiye-journeys' ),
        'a' => __( 'Yes, no interest for spreading it out. Life happens, budgets breathe.', 'jaiye-journeys' ),
      ],
      [
        'q'       => __( 'What if I change my mind right after booking?', 'jaiye-journeys' ),
        'a_parts' => [
          __( 'Cancel within 48 hours, and your trip is more than 30 days away, you get your deposit back in full. Full detail is on our', 'jaiye-journeys' ),
          'link',
          __( 'page.', 'jaiye-journeys' ),
        ],
      ],
      [
        'q'       => __( 'What happens if I need to cancel closer to the trip?', 'jaiye-journeys' ),
        'a_parts' => [
          __( "More than 120 days out, you'll get everything back above your deposit, minus a £50 admin fee. Inside 120 days, it's non-refundable, though if we can resell your spot, we'll get as much of your money back to you as possible. Full detail is on our", 'jaiye-journeys' ),
          'link',
          __( 'page.', 'jaiye-journeys' ),
        ],
      ],
    ],
  ],
  [
    'heading' => __( 'Group Trips & Celebrations', 'jaiye-journeys' ),
    'items'   => [
      [
        'q' => __( "What's the minimum group size?", 'jaiye-journeys' ),
        'a' => __( '10 guests.', 'jaiye-journeys' ),
      ],
      [
        'q' => __( "Can I bring a group that isn't one of your announced trips?", 'jaiye-journeys' ),
        'a' => __( "That's most of what we do. Private groups, milestone birthdays, celebrations, all built around your dates and your vision, not our calendar.", 'jaiye-journeys' ),
      ],
      [
        'q' => __( "How do you handle payments when it's a group?", 'jaiye-journeys' ),
        'a' => __( "We always try to get everyone their own payment link. There's more than one way we can take payment depending on the trip, but either way: you get to be a guest at your own trip, not the one chasing your friends for money.", 'jaiye-journeys' ),
      ],
    ],
  ],
  [
    'heading' => __( 'Between the Lines', 'jaiye-journeys' ),
    'items'   => [
      [
        'q' => __( 'What is Between the Lines?', 'jaiye-journeys' ),
        'a' => __( "Our luxury reading retreat brand. Rest, books, and real conversation, at a pace that doesn't leave you needing a holiday from your holiday.", 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'Do I need to be in a book club to join?', 'jaiye-journeys' ),
        'a' => __( 'No. Come as you are. Plenty of guests come through partner communities, but the retreat is open to anyone who wants in.', 'jaiye-journeys' ),
      ],
    ],
  ],
  [
    'heading' => __( 'Other Things You Might Want to Know', 'jaiye-journeys' ),
    'items'   => [
      [
        'q' => __( 'Do you handle flights?', 'jaiye-journeys' ),
        'a' => __( 'Yes, through our Ticketing Desk, direct or on a flexible payment plan if that helps.', 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'What if I have dietary requirements or need accessibility accommodations?', 'jaiye-journeys' ),
        'a' => __( "Tell me at least 30 days out and I'll do everything I can. I can't promise a fully allergen-free kitchen every time, since we're often in shared spaces, but I take it seriously and I'll never leave you guessing.", 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'Do I need travel insurance?', 'jaiye-journeys' ),
        'a' => __( "Yes, it's a condition of booking. Sort it as soon as you confirm your spot so you're covered from day one, not just departure day.", 'jaiye-journeys' ),
      ],
      [
        'q' => __( 'What if you have to cancel a trip?', 'jaiye-journeys' ),
        'a' => __( 'Rare, and only for things genuinely out of our control. If it happens, you get everything back, deposit included, within 14 days.', 'jaiye-journeys' ),
      ],
    ],
  ],
];

$faq_item_index = 0;
?>

<style>
/* ------------------------------------------------------------------
   FAQ page — component styles
   ------------------------------------------------------------------ */

.faq-hero {
  background-color: var(--color-forest);
  padding-block: var(--space-16);
  text-align: center;
}

.faq-hero__title {
  font-family: var(--font-heading);
  font-size: var(--text-4xl);
  font-weight: var(--fw-regular);
  color: var(--color-cream);
}

.faq-hero__sub {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  color: var(--color-sage);
  max-width: 48ch;
  margin: var(--space-4) auto 0;
}

.faq-content {
  background-color: var(--color-cream);
  padding-block: var(--section-gap);
}

.faq-category + .faq-category {
  margin-top: var(--space-16);
}

.faq-category__heading {
  font-family: var(--font-heading);
  font-size: var(--text-3xl);
  font-weight: var(--fw-regular);
  color: var(--color-forest);
  margin-bottom: var(--space-6);
}

.faq-item {
  border-bottom: 1px solid var(--color-border);
}

.faq-item__trigger {
  all: unset;
  box-sizing: border-box;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
  width: 100%;
  padding-block: var(--space-5);
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-regular);
  color: var(--color-forest);
  transition: color var(--duration-fast) var(--ease-out);
}

.faq-item__trigger:hover,
.faq-item.is-open .faq-item__trigger {
  color: var(--color-salmon);
}

.faq-item__icon {
  flex-shrink: 0;
  font-size: var(--text-xl);
  line-height: 1;
}

.faq-item__body {
  max-height: 0;
  overflow: hidden;
  transition: max-height var(--duration-base) var(--ease-out);
}

.faq-item__answer {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.8;
  color: var(--color-text);
  padding-bottom: var(--space-5);
}

.faq-item__answer a {
  color: var(--color-forest);
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: color var(--duration-fast) var(--ease-out);
}

.faq-item__answer a:hover {
  color: var(--color-salmon);
}
</style>

<main id="main-content" class="site-main">

  <!-- ============================================================
       1. HERO
       Forest green background, cream heading. No image.
       ============================================================ -->
  <section
    class="faq-hero"
    aria-label="<?php esc_attr_e( 'Frequently asked questions', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <h1 class="faq-hero__title">
        <?php esc_html_e( 'Frequently Asked Questions', 'jaiye-journeys' ); ?>
      </h1>
      <p class="faq-hero__sub">
        <?php esc_html_e( "Everything you'd want to know before you get in touch, and a few things you didn't think to ask.", 'jaiye-journeys' ); ?>
      </p>
    </div>
  </section><!-- /.faq-hero -->


  <!-- ============================================================
       2. FAQ ACCORDION
       Cream background. Grouped by category.
       ============================================================ -->
  <section
    class="faq-content"
    aria-label="<?php esc_attr_e( 'FAQ categories', 'jaiye-journeys' ); ?>"
  >
    <div class="container container--narrow">

      <?php foreach ( $faq_categories as $category ) : ?>
        <div class="faq-category">
          <h2 class="faq-category__heading"><?php echo esc_html( $category['heading'] ); ?></h2>

          <div class="faq-category__list">
            <?php foreach ( $category['items'] as $item ) :
              $faq_item_index++;
              $body_id = 'faq-body-' . $faq_item_index;
            ?>
              <div class="faq-item">
                <button
                  class="faq-item__trigger"
                  type="button"
                  aria-expanded="false"
                  aria-controls="<?php echo esc_attr( $body_id ); ?>"
                >
                  <span class="faq-item__question"><?php echo esc_html( $item['q'] ); ?></span>
                  <span class="faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-item__body" id="<?php echo esc_attr( $body_id ); ?>">
                  <p class="faq-item__answer">
                    <?php if ( isset( $item['a_parts'] ) ) : ?>
                      <?php echo esc_html( $item['a_parts'][0] ); ?>
                      <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'jaiye-journeys' ); ?></a>
                      <?php echo esc_html( $item['a_parts'][2] ); ?>
                    <?php else : ?>
                      <?php echo esc_html( $item['a'] ); ?>
                    <?php endif; ?>
                  </p>
                </div>
              </div><!-- /.faq-item -->
            <?php endforeach; ?>
          </div><!-- /.faq-category__list -->
        </div><!-- /.faq-category -->
      <?php endforeach; ?>

    </div>
  </section><!-- /.faq-content -->

  <script>
  (function () {
    'use strict';

    var items = document.querySelectorAll('.faq-item');
    if (!items.length) return;

    function openItem(item) {
      var trigger = item.querySelector('.faq-item__trigger');
      var body    = item.querySelector('.faq-item__body');
      var icon    = item.querySelector('.faq-item__icon');
      body.style.maxHeight = body.scrollHeight + 'px';
      trigger.setAttribute('aria-expanded', 'true');
      icon.textContent = '−';
      item.classList.add('is-open');
    }

    function closeItem(item) {
      var trigger = item.querySelector('.faq-item__trigger');
      var body    = item.querySelector('.faq-item__body');
      var icon    = item.querySelector('.faq-item__icon');
      body.style.maxHeight = '0';
      trigger.setAttribute('aria-expanded', 'false');
      icon.textContent = '+';
      item.classList.remove('is-open');
    }

    items.forEach(function (item) {
      item.querySelector('.faq-item__trigger').addEventListener('click', function () {
        if (item.classList.contains('is-open')) {
          closeItem(item);
        } else {
          openItem(item);
        }
      });
    });
  })();
  </script>

</main><!-- /#main-content -->

<?php get_footer(); ?>
