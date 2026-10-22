/**
 * Links page: the Upcoming Trips carousel moves to the next trip every few
 * seconds and loops, so the link buttons below stay near the top. It stops
 * for good once someone swipes, taps a dot or presses pause; it waits while
 * a pointer or keyboard focus is on it, while it's off screen or the tab is
 * hidden; and it never moves on its own for people who prefer reduced motion.
 */
( function () {
	'use strict';

	var root = document.querySelector( '.js-links-carousel' );
	if ( ! root ) return;

	var track = root.querySelector( '.js-links-track' );
	var items = track.children;
	var dots  = root.querySelectorAll( '.js-links-dot' );
	var pause = root.querySelector( '.js-links-pause' );
	if ( items.length < 2 ) return;

	var DELAY   = 4500;
	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var stopped = reduced;   // the visitor (or their settings) turned it off
	var held    = false;     // pointer or focus on it, for now
	var onView  = true;
	var timer   = null;
	var moving  = false;     // our own scroll, not the visitor's

	function current() {
		var left = track.scrollLeft;
		var best = 0;
		for ( var i = 0; i < items.length; i++ ) {
			if ( Math.abs( items[ i ].offsetLeft - track.offsetLeft - left - parseFloat( getComputedStyle( track ).paddingLeft ) ) <
				Math.abs( items[ best ].offsetLeft - track.offsetLeft - left - parseFloat( getComputedStyle( track ).paddingLeft ) ) ) {
				best = i;
			}
		}
		// At the far end the last card can't snap to the start: count it.
		if ( left + track.clientWidth >= track.scrollWidth - 2 ) best = items.length - 1;
		return best;
	}

	function go( i ) {
		var pad = parseFloat( getComputedStyle( track ).paddingLeft );
		moving = true;
		track.scrollTo( { left: items[ i ].offsetLeft - track.offsetLeft - pad, behavior: reduced ? 'auto' : 'smooth' } );
		window.setTimeout( function () { moving = false; }, 700 );
	}

	function mark() {
		var c = current();
		Array.prototype.forEach.call( dots, function ( dot, i ) {
			if ( i === c ) {
				dot.setAttribute( 'aria-current', 'true' );
			} else {
				dot.removeAttribute( 'aria-current' );
			}
		} );
	}

	function tick() {
		if ( stopped || held || ! onView || document.hidden ) return;
		go( ( current() + 1 ) % items.length );
	}

	function stop() {
		stopped = true;
		if ( pause ) {
			pause.classList.add( 'is-paused' );
			pause.setAttribute( 'aria-label', pause.getAttribute( 'data-label-play' ) );
		}
	}

	function start() {
		stopped = false;
		if ( pause ) {
			pause.classList.remove( 'is-paused' );
			pause.setAttribute( 'aria-label', pause.getAttribute( 'data-label-pause' ) );
		}
	}

	track.addEventListener( 'scroll', function () {
		window.requestAnimationFrame( mark );
	}, { passive: true } );

	// Swiping or scrolling by hand means they're in charge now.
	[ 'touchstart', 'wheel' ].forEach( function ( type ) {
		track.addEventListener( type, function () {
			if ( ! moving ) stop();
		}, { passive: true } );
	} );

	root.addEventListener( 'mouseenter', function () { held = true; } );
	root.addEventListener( 'mouseleave', function () { held = false; } );
	root.addEventListener( 'focusin', function () { held = true; } );
	root.addEventListener( 'focusout', function () { held = false; } );

	Array.prototype.forEach.call( dots, function ( dot, i ) {
		dot.addEventListener( 'click', function () {
			stop();
			go( i );
		} );
	} );

	if ( pause && ! reduced ) {
		pause.hidden = false;
		pause.addEventListener( 'click', function () {
			if ( stopped ) {
				start();
			} else {
				stop();
			}
		} );
	}

	if ( 'IntersectionObserver' in window ) {
		new IntersectionObserver( function ( entries ) {
			onView = entries[ 0 ].isIntersecting;
		}, { threshold: 0.5 } ).observe( track );
	}

	timer = window.setInterval( tick, DELAY );
} )();
