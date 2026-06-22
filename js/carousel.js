( function () {
	'use strict';

	var BREAKPOINT = 768; // matches CSS desktop breakpoint
	var INTERVAL   = 5000;

	var section = document.querySelector( '.fp-testimonials' );
	if ( ! section ) return;

	var track = section.querySelector( '.fp-testimonials__track' );
	var cards = Array.from( track.querySelectorAll( '.fp-testimonial' ) );
	if ( cards.length < 2 ) return;

	var current = 0;
	var timer   = null;
	var dots    = [];
	var dotsWrap = null;
	var active  = false; // is carousel mode currently on?

	/* ── dot nav ─────────────────────────────────────────── */

	function buildDots() {
		dotsWrap = document.createElement( 'div' );
		dotsWrap.className = 'fp-testimonials__dots';
		dotsWrap.setAttribute( 'role', 'tablist' );
		dotsWrap.setAttribute( 'aria-label', 'Testimonial navigation' );

		dots = cards.map( function ( _, i ) {
			var btn = document.createElement( 'button' );
			btn.className = 'fp-testimonials__dot';
			btn.setAttribute( 'role', 'tab' );
			btn.setAttribute( 'aria-label', 'Testimonial ' + ( i + 1 ) );
			btn.setAttribute( 'aria-selected', 'false' );
			btn.addEventListener( 'click', function () { pause(); goTo( i ); resume(); } );
			dotsWrap.appendChild( btn );
			return btn;
		} );

		section.querySelector( '.container' ).appendChild( dotsWrap );
	}

	/* ── slide logic ─────────────────────────────────────── */

	function goTo( index ) {
		cards[ current ].classList.remove( 'is-active' );
		dots[ current ].classList.remove( 'is-active' );
		dots[ current ].setAttribute( 'aria-selected', 'false' );

		current = ( index + cards.length ) % cards.length;

		cards[ current ].classList.add( 'is-active' );
		dots[ current ].classList.add( 'is-active' );
		dots[ current ].setAttribute( 'aria-selected', 'true' );
	}

	function next() { goTo( current + 1 ); }

	function pause()  { clearInterval( timer ); }
	function resume() { timer = setInterval( next, INTERVAL ); }

	/* ── carousel on/off ─────────────────────────────────── */

	function setTrackHeight() {
		// Let all cards render, then lock the tallest height
		track.style.minHeight = '';
		var max = 0;
		cards.forEach( function ( c ) {
			// Temporarily make visible to measure
			var prev = c.style.visibility;
			c.style.visibility = 'hidden';
			c.style.opacity    = '0';
			c.style.position   = 'absolute';
			max = Math.max( max, c.offsetHeight );
			c.style.visibility = prev;
			c.style.opacity    = '';
			c.style.position   = '';
		} );
		track.style.minHeight = max + 'px';
	}

	function enableCarousel() {
		if ( active ) return;
		active = true;
		section.classList.add( 'has-carousel' );
		setTrackHeight();
		goTo( 0 );
		resume();
	}

	function disableCarousel() {
		if ( ! active ) return;
		active = false;
		pause();
		section.classList.remove( 'has-carousel' );
		track.style.minHeight = '';
		cards.forEach( function ( c ) { c.classList.remove( 'is-active' ); } );
		dots.forEach( function ( d ) {
			d.classList.remove( 'is-active' );
			d.setAttribute( 'aria-selected', 'false' );
		} );
		current = 0;
	}

	/* ── responsive toggle ───────────────────────────────── */

	var mql = window.matchMedia( '(min-width: ' + BREAKPOINT + 'px)' );

	function onBreakpoint( e ) {
		if ( e.matches ) { enableCarousel(); } else { disableCarousel(); }
	}

	// Init
	buildDots();
	onBreakpoint( mql );

	if ( typeof mql.addEventListener === 'function' ) {
		mql.addEventListener( 'change', onBreakpoint );
	} else {
		mql.addListener( onBreakpoint ); // Safari < 14 fallback
	}

	/* ── pause on hover / focus ──────────────────────────── */

	section.addEventListener( 'mouseenter', pause );
	section.addEventListener( 'mouseleave', function () { if ( active ) resume(); } );
	section.addEventListener( 'focusin',    pause );
	section.addEventListener( 'focusout',   function () { if ( active ) resume(); } );

	/* ── keyboard arrows ─────────────────────────────────── */

	section.addEventListener( 'keydown', function ( e ) {
		if ( ! active ) return;
		if ( e.key === 'ArrowRight' ) { pause(); next();               resume(); }
		if ( e.key === 'ArrowLeft'  ) { pause(); goTo( current - 1 ); resume(); }
	} );

} )();
