/**
 * Single trip template behaviour.
 *
 * Vanilla JS, no dependencies. Every motion effect checks
 * prefers-reduced-motion and degrades to an instant final state rather than
 * being removed, so nothing becomes invisible or unreachable.
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function prefersReduced() {
		return reduceMotion.matches;
	}

	/* ── Header height, used for scroll offsets ──────────────────── */

	function headerOffset() {
		var header = document.getElementById( 'site-header' );
		var subnav = document.getElementById( 'trip-subnav' );

		var h = header ? header.getBoundingClientRect().height : 0;

		// The sub-nav only sticks at >= 768px; below that it scrolls away.
		if ( subnav && window.innerWidth >= 768 ) {
			h += subnav.getBoundingClientRect().height;
		}

		return h;
	}

	/* ── 1. Reveal on scroll ─────────────────────────────────────── */

	function initReveals() {
		var targets = document.querySelectorAll( '.js-reveal' );
		if ( ! targets.length ) return;

		// No IntersectionObserver (or reduced motion): show everything now.
		if ( ! ( 'IntersectionObserver' in window ) || prefersReduced() ) {
			Array.prototype.forEach.call( targets, function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) return;
				entry.target.classList.add( 'is-visible' );
				observer.unobserve( entry.target );
			} );
		}, {
			rootMargin: '0px 0px -12% 0px',
			threshold: 0.1
		} );

		Array.prototype.forEach.call( targets, function ( el ) {
			observer.observe( el );
		} );
	}

	/* ── 2. Heading underlines + meter fills ─────────────────────── */

	function initAnimatedElements() {
		var headings = document.querySelectorAll( '.trip-heading' );
		var meters   = document.querySelectorAll( '.js-meter-fill' );
		var all      = [].concat(
			Array.prototype.slice.call( headings ),
			Array.prototype.slice.call( meters )
		);

		if ( ! all.length ) return;

		if ( ! ( 'IntersectionObserver' in window ) || prefersReduced() ) {
			all.forEach( function ( el ) { el.classList.add( 'is-visible' ); } );
			return;
		}

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) return;
				entry.target.classList.add( 'is-visible' );
				observer.unobserve( entry.target );
			} );
		}, { threshold: 0.35 } );

		all.forEach( function ( el ) { observer.observe( el ); } );
	}

	/* ── 3. Transparent → solid header ───────────────────────────── */

	function initHeaderState() {
		var header   = document.getElementById( 'site-header' );
		var sentinel = document.querySelector( '.trip-hero__sentinel' );
		if ( ! header ) return;

		if ( ! sentinel || ! ( 'IntersectionObserver' in window ) ) {
			header.classList.add( 'is-solid' );
			return;
		}

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				header.classList.toggle( 'is-solid', ! entry.isIntersecting );
			} );
		}, { threshold: 0 } );

		observer.observe( sentinel );
	}

	/* ── 4. Smooth anchor scrolling with header offset ───────────── */

	function initSmoothScroll() {
		document.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( '.trip-subnav__link, a[href^="#"]' );
			if ( ! link ) return;

			var href = link.getAttribute( 'href' );
			if ( ! href || href === '#' || href.charAt( 0 ) !== '#' ) return;

			var target = document.getElementById( href.slice( 1 ) );
			if ( ! target ) return;

			e.preventDefault();

			var top = target.getBoundingClientRect().top
				+ window.pageYOffset
				- headerOffset()
				- 12;

			window.scrollTo( {
				top: top,
				behavior: prefersReduced() ? 'auto' : 'smooth'
			} );

			// Move focus for keyboard and screen reader users, without
			// letting the browser re-scroll and undo our offset.
			target.setAttribute( 'tabindex', '-1' );
			target.focus( { preventScroll: true } );
		} );
	}

	/* ── 5. Sub-nav current-section highlighting ─────────────────── */

	function initScrollSpy() {
		var links = document.querySelectorAll( '.trip-subnav__link' );
		if ( ! links.length || ! ( 'IntersectionObserver' in window ) ) return;

		var map = {};
		var sections = [];

		Array.prototype.forEach.call( links, function ( link ) {
			var id = link.getAttribute( 'href' );
			if ( ! id || id.charAt( 0 ) !== '#' ) return;

			var section = document.getElementById( id.slice( 1 ) );
			if ( ! section ) return;

			map[ section.id ] = link;
			sections.push( section );
		} );

		if ( ! sections.length ) return;

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				var link = map[ entry.target.id ];
				if ( ! link ) return;

				if ( entry.isIntersecting ) {
					Array.prototype.forEach.call( links, function ( l ) {
						l.classList.remove( 'is-current' );
					} );
					link.classList.add( 'is-current' );
				}
			} );
		}, {
			rootMargin: '-45% 0px -45% 0px',
			threshold: 0
		} );

		sections.forEach( function ( section ) { observer.observe( section ); } );
	}

	/* ── 6. Collage parallax ─────────────────────────────────────── */

	function initParallax() {
		var items = document.querySelectorAll( '.js-parallax' );
		if ( ! items.length || prefersReduced() ) return;

		// Parallax on small screens costs more than it adds.
		if ( window.innerWidth < 768 ) return;

		var ticking = false;

		function update() {
			var viewportH = window.innerHeight;

			Array.prototype.forEach.call( items, function ( item ) {
				var rect = item.getBoundingClientRect();

				// Skip anything well outside the viewport.
				if ( rect.bottom < -200 || rect.top > viewportH + 200 ) return;

				var speed = parseFloat( item.getAttribute( 'data-parallax-speed' ) ) || 0;

				// Distance of this element's centre from the viewport centre.
				var offset = ( rect.top + rect.height / 2 ) - viewportH / 2;

				item.style.transform = 'translate3d(0,' + ( offset * speed ).toFixed( 2 ) + 'px,0)';
			} );

			ticking = false;
		}

		function onScroll() {
			if ( ticking ) return;
			ticking = true;
			window.requestAnimationFrame( update );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll, { passive: true } );
		update();
	}

	/* ── 7. Tabs (day by day + booking year tabs) ────────────────── */

	function initTabs( tabSelector ) {
		var tabs = document.querySelectorAll( tabSelector );
		if ( ! tabs.length ) return;

		function activate( tab ) {
			var group = tab.parentNode;
			var groupTabs = group.querySelectorAll( tabSelector );

			Array.prototype.forEach.call( groupTabs, function ( t ) {
				var isActive = t === tab;
				var panel    = document.getElementById( t.getAttribute( 'aria-controls' ) );

				t.classList.toggle( 'is-active', isActive );
				t.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				t.setAttribute( 'tabindex', isActive ? '0' : '-1' );

				if ( panel ) {
					panel.classList.toggle( 'is-active', isActive );
					panel.hidden = ! isActive;
				}
			} );
		}

		Array.prototype.forEach.call( tabs, function ( tab ) {
			tab.addEventListener( 'click', function () {
				activate( tab );
			} );

			// Roving focus, per the WAI-ARIA tabs pattern.
			tab.addEventListener( 'keydown', function ( e ) {
				if ( e.key !== 'ArrowRight' && e.key !== 'ArrowLeft' ) return;

				var group     = tab.parentNode;
				var groupTabs = Array.prototype.slice.call( group.querySelectorAll( tabSelector ) );
				var index     = groupTabs.indexOf( tab );
				var next      = e.key === 'ArrowRight' ? index + 1 : index - 1;

				if ( next < 0 ) next = groupTabs.length - 1;
				if ( next >= groupTabs.length ) next = 0;

				e.preventDefault();
				activate( groupTabs[ next ] );
				groupTabs[ next ].focus();
			} );
		} );
	}

	/* ── 8. Accordion height transitions ─────────────────────────── */

	function initAccordions() {
		var accordions = document.querySelectorAll( '.js-accordion' );

		Array.prototype.forEach.call( accordions, function ( details ) {
			var summary = details.querySelector( '.trip-accordion__summary' );
			var body    = details.querySelector( '.trip-accordion__body' );
			if ( ! summary || ! body ) return;

			summary.addEventListener( 'click', function ( e ) {
				if ( prefersReduced() ) return; // Let the browser just toggle it.

				e.preventDefault();

				if ( details.open ) {
					// Collapse: measure, then animate to 0.
					body.style.maxHeight = body.scrollHeight + 'px';
					details.classList.add( 'is-animating' );

					window.requestAnimationFrame( function () {
						body.style.maxHeight = '0px';
					} );

					window.setTimeout( function () {
						details.open = false;
						details.classList.remove( 'is-animating' );
						body.style.maxHeight = '';
					}, 250 );
				} else {
					// Expand: open first so scrollHeight is measurable.
					details.open = true;
					body.style.maxHeight = '0px';
					details.classList.add( 'is-animating' );

					window.requestAnimationFrame( function () {
						body.style.maxHeight = body.scrollHeight + 'px';
					} );

					window.setTimeout( function () {
						details.classList.remove( 'is-animating' );
						// Clearing max-height lets the panel reflow freely
						// afterwards (e.g. on resize or font load).
						body.style.maxHeight = '';
					}, 250 );
				}
			} );
		} );
	}

	/* ── 9. Carousels ────────────────────────────────────────────── */

	function initCarousels() {
		var carousels = document.querySelectorAll( '.js-carousel' );

		Array.prototype.forEach.call( carousels, function ( carousel ) {
			var track = carousel.querySelector( '.trip-carousel__track' );
			var prev  = carousel.querySelector( '.js-carousel-prev' );
			var next  = carousel.querySelector( '.js-carousel-next' );
			if ( ! track ) return;

			var slides = track.children;
			if ( ! slides.length ) return;

			var index = 0;

			function slideStep() {
				if ( slides.length < 2 ) return slides[ 0 ].getBoundingClientRect().width;

				// Measure the real gap rather than assuming a token value.
				var a = slides[ 0 ].getBoundingClientRect();
				var b = slides[ 1 ].getBoundingClientRect();
				return b.left - a.left;
			}

			function maxIndex() {
				var viewport = carousel.querySelector( '.trip-carousel__viewport' );
				if ( ! viewport ) return slides.length - 1;

				var visible = Math.max( 1, Math.floor( viewport.getBoundingClientRect().width / slideStep() ) );
				return Math.max( 0, slides.length - visible );
			}

			function update() {
				index = Math.max( 0, Math.min( index, maxIndex() ) );
				track.style.transform = 'translate3d(-' + ( index * slideStep() ) + 'px,0,0)';

				if ( prev ) prev.disabled = index <= 0;
				if ( next ) next.disabled = index >= maxIndex();
			}

			if ( prev ) {
				prev.addEventListener( 'click', function () {
					index -= 1;
					update();
				} );
			}

			if ( next ) {
				next.addEventListener( 'click', function () {
					index += 1;
					update();
				} );
			}

			window.addEventListener( 'resize', update, { passive: true } );
			update();
		} );
	}

	/* ── 10. Mobile booking bar ──────────────────────────────────── */

	function initMobileBar() {
		var bar = document.getElementById( 'trip-mobile-bar' );
		if ( ! bar ) return;

		var hero = document.querySelector( '.trip-hero' );
		if ( ! hero || ! ( 'IntersectionObserver' in window ) ) {
			bar.classList.add( 'is-visible' );
			return;
		}

		// Show it once the hero (and its own CTA) is out of the way.
		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				bar.classList.toggle( 'is-visible', ! entry.isIntersecting );
			} );
		}, { threshold: 0 } );

		observer.observe( hero );
	}

	/* ── Init ────────────────────────────────────────────────────── */

	function init() {
		initReveals();
		initAnimatedElements();
		initHeaderState();
		initSmoothScroll();
		initScrollSpy();
		initParallax();
		initTabs( '.trip-days__tab' );
		initTabs( '.trip-booking__tab' );
		initAccordions();
		initCarousels();
		initMobileBar();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
