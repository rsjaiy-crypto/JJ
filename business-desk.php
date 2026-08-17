<?php
/*
 * Template Name: Business Desk
 */

get_header();
?>

<main id="main-content" class="site-main bd-page">

  <!-- ============================================================
       ABOVE THE FOLD
       corporatetransfers.jpg — black SUV in the financial district.
       ============================================================ -->
  <header
    class="bd-hero"
    aria-label="<?php esc_attr_e( 'The Business Desk', 'jaiye-journeys' ); ?>"
  >
    <div class="bd-hero__media" aria-hidden="true">
      <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/corporatetransfers.jpg' ); ?>"
        alt=""
        class="bd-hero__img"
      >
      <div class="bd-hero__overlay"></div>
    </div>

    <div class="container bd-hero__inner">
      <div class="bd-hero__content">
        <h1 class="bd-hero__heading">
          <?php esc_html_e( 'Stop Letting Travel Admin Eat Your Best People\'s Time', 'jaiye-journeys' ); ?>
        </h1>
        <p class="bd-hero__sub">
          <?php esc_html_e( 'Flights, hotels, and every disruption handled by one dedicated point of contact, so your best people spend their time on the work that actually moves your business.', 'jaiye-journeys' ); ?>
        </p>
        <a
          href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
          class="btn btn--plan-trip bd-hero__cta"
        >
          <?php esc_html_e( 'Talk to Us About Your Team', 'jaiye-journeys' ); ?>
        </a>
      </div>
    </div>
  </header><!-- /.bd-hero -->


  <!-- ============================================================
       SECTION 1: THE REALITY
       Cream bg. Text only, no image.
       ============================================================ -->
  <section
    class="bd-reality"
    aria-label="<?php esc_attr_e( 'The Reality', 'jaiye-journeys' ); ?>"
  >
    <div class="container container--narrow">
      <p class="bd-reality__body">
        <?php esc_html_e( 'If you run a venture capital, private equity, or consulting firm, your team is on a plane more than most. The more people travel, the more that goes wrong: missed connections, last-minute schedule changes, or a partner stuck between meetings with no seat booked home.', 'jaiye-journeys' ); ?>
      </p>
      <p class="bd-reality__body">
        <?php esc_html_e( 'At most firms, none of that is actually anyone\'s job. It gets absorbed by whoever is free that week, an Executive Assistant rebooking a flight on top of an already packed calendar, or a Chief of Staff fielding a 9 PM cancellation call. It rarely gets fixed properly because it has never been anyone\'s actual responsibility to fix.', 'jaiye-journeys' ); ?>
      </p>
    </div>
  </section><!-- /.bd-reality -->


  <!-- ============================================================
       SECTION 2: WHAT CHANGES
       VIPhospitality.jpg — marble and velvet lobby.
       ============================================================ -->
  <section
    class="bd-changes"
    aria-label="<?php esc_attr_e( 'What Changes', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="bd-changes__grid">

        <figure class="bd-changes__media">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/VIPhospitality.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Marble and velvet hotel lobby', 'jaiye-journeys' ); ?>"
            class="bd-changes__img"
            loading="lazy"
          >
        </figure>

        <div class="bd-changes__content">
          <h2 class="bd-changes__heading">
            <?php esc_html_e( 'What Changes', 'jaiye-journeys' ); ?>
          </h2>

          <ul class="bd-changes__list" role="list">
            <li>
              <strong><?php esc_html_e( 'One point of contact who knows your team.', 'jaiye-journeys' ); ?></strong>
              <?php esc_html_e( 'No re-briefing every time. You get someone who understands how your business runs.', 'jaiye-journeys' ); ?>
            </li>
            <li>
              <strong><?php esc_html_e( 'Proactive monitoring and duty of care.', 'jaiye-journeys' ); ?></strong>
              <?php esc_html_e( 'Live flight tracking and vetted in-country ground partners mean cancellations, delays, and transfers are handled before they disrupt your traveller\'s day.', 'jaiye-journeys' ); ?>
            </li>
            <li>
              <strong><?php esc_html_e( 'FORA-backed access.', 'jaiye-journeys' ); ?></strong>
              <?php esc_html_e( 'Upgrades, perks, and rates you cannot get self-serve or through a regular agent.', 'jaiye-journeys' ); ?>
            </li>
            <li>
              <strong><?php esc_html_e( 'Zero new software to learn.', 'jaiye-journeys' ); ?></strong>
              <?php esc_html_e( 'No dashboards, rollouts, or whole-team logins. Just one direct relationship.', 'jaiye-journeys' ); ?>
            </li>
            <li>
              <strong><?php esc_html_e( 'Accountable to one person.', 'jaiye-journeys' ); ?></strong>
              <?php esc_html_e( 'You and your travellers always know exactly who to call.', 'jaiye-journeys' ); ?>
            </li>
          </ul>
        </div>

      </div><!-- /.bd-changes__grid -->
    </div><!-- /.container -->
  </section><!-- /.bd-changes -->


  <!-- ============================================================
       SECTION 3: WHY JAIYE JOURNEYS
       howitworks.jpg — courtyard portrait. Includes testimonial.
       ============================================================ -->
  <section
    class="bd-why"
    aria-label="<?php esc_attr_e( 'Why Jaiye Journeys', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="bd-why__grid">

        <figure class="bd-why__media">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/howitworks.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Steph, founder of Jaiye Journeys, in a courtyard', 'jaiye-journeys' ); ?>"
            class="bd-why__img"
            loading="lazy"
          >
        </figure>

        <div class="bd-why__content">
          <h2 class="bd-why__heading">
            <?php esc_html_e( 'Why Jaiye Journeys', 'jaiye-journeys' ); ?>
          </h2>
          <p class="bd-why__body">
            <?php esc_html_e( 'Jaiye Journeys was built by Regina Stephanie Jaiyeola. Hired as Regina, trusted as Steph, the name you\'ll hear once the relationship is running. She has managed the full spectrum of travel disruptions: delayed flights, missed connections, cancelled routes, lost documents, and last-minute rebookings across multiple time zones. Flights are monitored in real time so delays get handled before they become your firm\'s problem.', 'jaiye-journeys' ); ?>
          </p>
          <p class="bd-why__body">
            <?php esc_html_e( 'FORA certification provides supplier access and VIP perks unavailable outside an agency network, supported by hands-on experience managing group travel logistics at scale across 12+ countries through work with Remote Year. But the real difference is simpler than any credential: when something goes wrong for your team, it is not the first time Steph has solved it.', 'jaiye-journeys' ); ?>
          </p>

          <blockquote class="bd-why__quote">
            <p><?php esc_html_e( 'Steph helped me navigate almost everything a traveller considers a nightmare, including a stolen passport in a foreign country where we didn\'t speak the language. We needed an emergency visa and an emergency passport, sorted within 24 to 36 hours.', 'jaiye-journeys' ); ?></p>
            <cite>— Ron S.</cite>
          </blockquote>
        </div>

      </div><!-- /.bd-why__grid -->
    </div><!-- /.container -->
  </section><!-- /.bd-why -->


  <!-- ============================================================
       SECTION 4: HOW IT WORKS
       HEROairportlounge.jpg — dark hotel desk setup with laptop.
       ============================================================ -->
  <section
    class="bd-how"
    aria-label="<?php esc_attr_e( 'How It Works', 'jaiye-journeys' ); ?>"
  >
    <div class="container">
      <div class="bd-how__grid">

        <div class="bd-how__content">
          <h2 class="bd-how__heading">
            <?php esc_html_e( 'How It Works', 'jaiye-journeys' ); ?>
          </h2>

          <ol class="bd-how__steps">
            <li>
              <span class="bd-how__step-num" aria-hidden="true">1</span>
              <div>
                <strong><?php esc_html_e( 'Tell us about your team.', 'jaiye-journeys' ); ?></strong>
                <p><?php esc_html_e( 'Share a few details on team size, travel frequency, and what is currently painful.', 'jaiye-journeys' ); ?></p>
              </div>
            </li>
            <li>
              <span class="bd-how__step-num" aria-hidden="true">2</span>
              <div>
                <strong><?php esc_html_e( 'A short call.', 'jaiye-journeys' ); ?></strong>
                <p><?php esc_html_e( 'We talk through how your team actually travels, not a generic pitch.', 'jaiye-journeys' ); ?></p>
              </div>
            </li>
            <li>
              <span class="bd-how__step-num" aria-hidden="true">3</span>
              <div>
                <strong><?php esc_html_e( 'A tailored retainer.', 'jaiye-journeys' ); ?></strong>
                <p>
                  <?php
                  echo wp_kses(
                    __( 'Scoped to your team and quoted directly, with a <strong>3-month minimum commitment</strong> to ensure we properly learn your team\'s preferences and operating style.', 'jaiye-journeys' ),
                    [ 'strong' => [] ]
                  );
                  ?>
                </p>
              </div>
            </li>
            <li>
              <span class="bd-how__step-num" aria-hidden="true">4</span>
              <div>
                <strong><?php esc_html_e( 'Fast rollout.', 'jaiye-journeys' ); ?></strong>
                <p><?php esc_html_e( 'We start taking travel requests right away while we get to know your travellers\' preferences.', 'jaiye-journeys' ); ?></p>
              </div>
            </li>
          </ol>
        </div>

        <figure class="bd-how__media">
          <img
            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/HEROairportlounge.jpg' ); ?>"
            alt="<?php esc_attr_e( 'Laptop set up at a hotel desk in the evening', 'jaiye-journeys' ); ?>"
            class="bd-how__img"
            loading="lazy"
          >
        </figure>

      </div><!-- /.bd-how__grid -->
    </div><!-- /.container -->
  </section><!-- /.bd-how -->


  <!-- ============================================================
       SECTION 5: FAQ's
       Mobile (<768px): vertical accordion, full-width rows.
       Desktop (768px+): horizontal accordion, width-based expand.
       One panel open at a time. Real buttons, aria-expanded, keyboard operable.
       ============================================================ -->
  <section
    class="bd-faq"
    aria-label="<?php esc_attr_e( 'Frequently Asked Questions', 'jaiye-journeys' ); ?>"
  >
    <div class="container">

      <header class="section-header section-header--center">
        <p class="overline"><?php esc_html_e( 'FAQ\'s', 'jaiye-journeys' ); ?></p>
        <h2 class="section-header__title"><?php esc_html_e( 'Questions, Answered', 'jaiye-journeys' ); ?></h2>
      </header>

      <div class="bd-faq__list">

        <?php
        $faqs = [
          [
            'q' => "Isn't this an extra cost on top of what we already pay someone to do?",
            'a' => 'Most firms are already absorbing this cost invisibly inside existing roles. The hours spent on travel admin internally usually cost more than this retainer, and this comes with specialised access and troubleshooting experience internal roles do not have.',
          ],
          [
            'q' => 'We already use Navan, Concur, or other booking tools. Why would we need this?',
            'a' => 'Self-serve booking tools like Navan and Concur still require your team to spend time searching, comparing, and inputting options. Crucially, software does not handle emergency disruptions when things break down. Jaiye Journeys replaces the software by taking over the manual work and the emergency management completely.',
          ],
          [
            'q' => 'How is this different from a regular travel advisor or travel agent?',
            'a' => 'Standard travel advisors operate transactionally: you call them only when you need something booked. Jaiye Journeys is a retained operational partner. We already know your team, your preferences, and your patterns before a request ever comes in.',
          ],
          [
            'q' => 'How do you keep our sensitive data and payment info secure?',
            'a' => "We do not ask for credit cards or passport copies over text or chat. Payment details are collected once, through FORA's Vault, a PCI DSS Level 1 certified system that tokenises card data the moment it's entered, meaning we never see or store the actual card number. Cards are added through secure, one-time links, and every access is logged.",
          ],
          [
            'q' => 'How do we stay in touch day to day?',
            'a' => 'We plug directly into your team\'s existing workflow. For day-to-day requests, we communicate via your preferred instant messaging platform, whether that is Slack or Microsoft Teams, keeping things fast and informal. For urgent, on-the-road disruptions, we handle things directly with the traveller via text or WhatsApp.',
          ],
          [
            'q' => 'What happens when something goes wrong outside normal hours?',
            'a' => 'Genuine disruptions get handled in real time as they happen, not queued for the next morning. Standard requests are handled within a business day.',
          ],
          [
            'q' => 'What does it cost?',
            'a' => 'It depends entirely on your team\'s size and travel frequency. Share a bit about your setup and we will quote it directly. A 12-person firm and a 45-person firm have fundamentally different needs, which is why there is no generic price list.',
          ],
        ];
        foreach ( $faqs as $i => $faq ) :
          $panel_id   = 'bd-faq-panel-' . $i;
          $trigger_id = 'bd-faq-trigger-' . $i;
        ?>
        <div class="bd-faq__item">
          <h3 class="bd-faq__heading">
            <button
              class="bd-faq__trigger"
              id="<?php echo esc_attr( $trigger_id ); ?>"
              aria-expanded="false"
              aria-controls="<?php echo esc_attr( $panel_id ); ?>"
              type="button"
            >
              <span class="bd-faq__question"><?php echo esc_html( $faq['q'] ); ?></span>
              <span class="bd-faq__icon" aria-hidden="true">+</span>
            </button>
          </h3>
          <div
            class="bd-faq__body"
            id="<?php echo esc_attr( $panel_id ); ?>"
            role="region"
            aria-labelledby="<?php echo esc_attr( $trigger_id ); ?>"
          >
            <div class="bd-faq__body-inner">
              <p class="bd-faq__answer"><?php echo esc_html( $faq['a'] ); ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

      </div><!-- /.bd-faq__list -->
    </div><!-- /.container -->
  </section><!-- /.bd-faq -->

  <script>
  (function () {
    'use strict';

    var items = document.querySelectorAll('.bd-faq__item');
    if (!items.length) return;

    function openItem(item) {
      item.classList.add('is-open');
      item.querySelector('.bd-faq__trigger').setAttribute('aria-expanded', 'true');
      item.querySelector('.bd-faq__icon').textContent = '−';
    }

    function closeItem(item) {
      item.classList.remove('is-open');
      item.querySelector('.bd-faq__trigger').setAttribute('aria-expanded', 'false');
      item.querySelector('.bd-faq__icon').textContent = '+';
    }

    // Pre-expand the first panel on load so visitors immediately see the interaction.
    openItem(items[0]);

    items.forEach(function (item) {
      item.querySelector('.bd-faq__trigger').addEventListener('click', function () {
        if (item.classList.contains('is-open')) {
          closeItem(item);
        } else {
          items.forEach(function (other) {
            if (other !== item) closeItem(other);
          });
          openItem(item);
        }
      });
    });
  })();
  </script>


  <!-- ============================================================
       FINAL CTA
       ============================================================ -->
  <section
    class="bd-final-cta"
    aria-label="<?php esc_attr_e( 'Ready to hand this off', 'jaiye-journeys' ); ?>"
  >
    <div class="container container--narrow">
      <h2 class="bd-final-cta__heading">
        <?php esc_html_e( 'Ready to hand this off?', 'jaiye-journeys' ); ?>
      </h2>
      <p class="bd-final-cta__body">
        <?php esc_html_e( 'Tell us about your team, and we will get back to you with a short call time, not a sales sequence.', 'jaiye-journeys' ); ?>
      </p>
      <a
        href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
        class="btn btn--plan-trip"
      >
        <?php esc_html_e( 'Talk to Us About Your Team', 'jaiye-journeys' ); ?>
      </a>
      <p class="bd-final-cta__note">
        <?php esc_html_e( 'Simple setup designed to save your EA time, not take it.', 'jaiye-journeys' ); ?>
      </p>
    </div>
  </section><!-- /.bd-final-cta -->

</main><!-- /#main-content -->

<style>
/* ------------------------------------------------------------------
   Business Desk page — mobile-first
   ------------------------------------------------------------------ */

.bd-page { background-color: var(--color-cream); }

/* ---- Hero ---------------------------------------------------- */
.bd-hero {
  position: relative;
  display: flex;
  align-items: flex-end;
  min-height: 70svh;
  color: #ffffff;
}

.bd-hero__media {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.bd-hero__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.bd-hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, color-mix(in srgb, var(--color-deep-green) 40%, transparent) 0%, color-mix(in srgb, var(--color-deep-green) 85%, transparent) 100%);
}

.bd-hero__inner {
  position: relative;
  z-index: 1;
  padding-block: var(--space-16) var(--space-12);
}

.bd-hero__content { max-width: 640px; }

.bd-hero__heading {
  font-family: var(--font-heading);
  font-size: var(--text-3xl);
  font-weight: var(--fw-regular);
  line-height: 1.15;
  color: var(--color-cream);
  margin-bottom: var(--space-4);
}

.bd-hero__sub {
  font-family: var(--font-body);
  font-size: var(--text-base);
  font-weight: var(--fw-light);
  line-height: 1.6;
  margin-bottom: var(--space-6);
  color: color-mix(in srgb, #ffffff 90%, transparent);
}

/* ---- Section 1: The Reality ------------------------------------ */
.bd-reality {
  padding-block: var(--section-gap-sm);
}

.bd-reality__body {
  font-family: var(--font-body);
  font-size: var(--text-base);
  line-height: 1.7;
  color: var(--color-forest);
  margin-bottom: var(--space-5);
}

/* ---- Section 2: What Changes ------------------------------------ */
.bd-changes { padding-block: var(--section-gap-sm); }

.bd-changes__grid {
  display: grid;
  gap: var(--space-8);
}

.bd-changes__media { line-height: 0; }

.bd-changes__img {
  width: 100%;
  height: auto;
  border-radius: var(--radius-lg);
}

.bd-changes__heading {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-6);
}

.bd-changes__list {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
}

.bd-changes__list li {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  line-height: 1.65;
  color: var(--color-forest);
  padding-left: var(--space-5);
  border-left: 2px solid var(--color-salmon);
}

.bd-changes__list strong {
  display: block;
  font-family: var(--font-heading);
  font-size: var(--text-md);
  font-weight: var(--fw-semibold);
  color: var(--color-deep-green);
  margin-bottom: var(--space-1);
}

/* ---- Section 3: Why Jaiye Journeys ------------------------------ */
.bd-why { padding-block: var(--section-gap-sm); background-color: #ffffff; }

.bd-why__grid {
  display: grid;
  gap: var(--space-8);
}

.bd-why__media { line-height: 0; }

.bd-why__img {
  width: 100%;
  height: auto;
  border-radius: var(--radius-lg);
}

.bd-why__heading {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-5);
}

.bd-why__body {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  line-height: 1.7;
  color: var(--color-forest);
  margin-bottom: var(--space-4);
}

.bd-why__quote {
  margin: var(--space-6) 0 0;
  padding-left: var(--space-5);
  border-left: 2px solid var(--color-salmon);
}

.bd-why__quote p {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: var(--text-md);
  line-height: 1.5;
  color: var(--color-deep-green);
  margin-bottom: var(--space-2);
}

.bd-why__quote cite {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-style: normal;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--color-forest);
}

/* ---- Section 4: How It Works ------------------------------------ */
.bd-how { padding-block: var(--section-gap-sm); }

.bd-how__grid {
  display: grid;
  gap: var(--space-8);
}

.bd-how__heading {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-6);
}

.bd-how__steps {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.bd-how__steps li {
  display: flex;
  gap: var(--space-4);
}

.bd-how__step-num {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: var(--radius-full);
  background-color: var(--color-forest);
  color: var(--color-cream);
  font-family: var(--font-heading);
  font-size: var(--text-base);
}

.bd-how__steps strong {
  display: block;
  font-family: var(--font-heading);
  font-size: var(--text-md);
  font-weight: var(--fw-semibold);
  color: var(--color-deep-green);
  margin-bottom: var(--space-1);
}

.bd-how__steps p {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  line-height: 1.65;
  color: var(--color-forest);
}

.bd-how__media { line-height: 0; }

.bd-how__img {
  width: 100%;
  height: auto;
  border-radius: var(--radius-lg);
}

/* ---- Section 5: FAQ's — mobile-first vertical accordion --------- */
.bd-faq { padding-block: var(--section-gap-sm); }

.bd-faq__list {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.bd-faq__item {
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background-color: var(--color-cream);
  transition:
    border-color var(--duration-base) var(--ease-out),
    background-color var(--duration-base) var(--ease-out);
}

.bd-faq__item.is-open {
  border-color: var(--color-salmon);
  background-color: var(--color-sage);
}

.bd-faq__heading { margin: 0; }

.bd-faq__trigger {
  all: unset;
  box-sizing: border-box;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-3);
  width: 100%;
  padding: var(--space-4) var(--space-5);
}

.bd-faq__trigger:focus-visible {
  outline: 2px solid var(--color-salmon);
  outline-offset: 2px;
  border-radius: var(--radius-sm);
}

.bd-faq__question {
  font-family: var(--font-body);
  font-weight: var(--fw-regular);
  font-size: var(--text-md);
  color: var(--color-deep-green);
}

.bd-faq__icon {
  flex-shrink: 0;
  font-family: var(--font-body);
  font-size: var(--text-lg);
  line-height: 1;
  color: var(--color-forest);
  transition: transform var(--duration-base) var(--ease-out);
}

.bd-faq__item.is-open .bd-faq__icon { transform: rotate(45deg); }

.bd-faq__body {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows var(--duration-base) var(--ease-out);
}

.bd-faq__item.is-open .bd-faq__body { grid-template-rows: 1fr; }

.bd-faq__body-inner { overflow: hidden; }

.bd-faq__answer {
  margin: 0;
  padding: 0 var(--space-5) var(--space-5);
  font-family: var(--font-body);
  font-size: var(--text-sm);
  line-height: 1.65;
  color: var(--color-forest);
}

/* ---- Final CTA ---------------------------------------------------- */
.bd-final-cta {
  padding-block: var(--section-gap-sm);
  text-align: center;
}

.bd-final-cta__heading {
  font-family: var(--font-heading);
  font-size: var(--text-2xl);
  font-weight: var(--fw-regular);
  color: var(--color-deep-green);
  margin-bottom: var(--space-4);
}

.bd-final-cta__body {
  font-family: var(--font-body);
  font-size: var(--text-base);
  line-height: 1.6;
  color: var(--color-forest);
  margin-bottom: var(--space-6);
}

.bd-final-cta__note {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-style: italic;
  color: var(--color-forest);
  margin-top: var(--space-5);
}

/* ==================================================================
   Tablet and desktop (768px+)
   ================================================================== */
@media (min-width: 768px) {

  .bd-hero { min-height: 85svh; }

  .bd-hero__heading { font-size: var(--text-4xl); }
  .bd-hero__sub { font-size: var(--text-md); max-width: 520px; }

  .bd-changes__grid,
  .bd-why__grid,
  .bd-how__grid {
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: var(--space-12);
  }

  .bd-how__grid { grid-template-columns: 1fr 1fr; }
  .bd-how__content { order: 1; }
  .bd-how__media { order: 2; }

  /* ---- FAQ: horizontal accordion, width-based expand -------------- */
  .bd-faq__list {
    flex-direction: row;
    align-items: stretch;
    gap: var(--space-3);
    height: 440px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .bd-faq__item {
    flex: 0 0 120px;
    width: 120px;
    overflow: hidden;
    transition:
      flex-basis var(--duration-base) var(--ease-out),
      width var(--duration-base) var(--ease-out),
      border-color var(--duration-base) var(--ease-out),
      background-color var(--duration-base) var(--ease-out);
  }

  .bd-faq__item.is-open {
    flex: 0 0 360px;
    width: 360px;
  }

  .bd-faq__heading {
    height: 100%;
  }

  .bd-faq__item.is-open .bd-faq__heading {
    height: auto;
  }

  .bd-faq__trigger {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: var(--space-6) var(--space-4);
  }

  .bd-faq__item.is-open .bd-faq__trigger {
    align-items: flex-start;
    justify-content: flex-start;
    height: auto;
  }

  .bd-faq__question {
    text-align: center;
    font-size: var(--text-sm);
    line-height: 1.35;
  }

  .bd-faq__item.is-open .bd-faq__question {
    text-align: left;
    width: 100%;
    font-size: var(--text-md);
  }

  .bd-faq__icon { display: none; }

  .bd-faq__body {
    display: block;
    width: 100%;
    opacity: 0;
    transition: opacity var(--duration-fast) var(--ease-out);
  }

  .bd-faq__item.is-open .bd-faq__body {
    opacity: 1;
    transition-delay: 150ms;
  }

  .bd-faq__body-inner {
    overflow-y: auto;
    max-height: 100%;
  }

  .bd-faq__answer { padding: var(--space-4) var(--space-4) var(--space-6); }
}

@media (min-width: 1024px) {
  .bd-hero__heading { font-size: var(--text-5xl); }
}
</style>

<?php get_footer(); ?>
