( function () {
	'use strict';

	var banner = document.getElementById( 'cookie-consent' );
	if ( ! banner ) return;

	var COOKIE_NAME = 'jj_cookie_consent';
	var COOKIE_DAYS = 365;

	function setCookie( name, value, days ) {
		var expires = new Date();
		expires.setTime( expires.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
		document.cookie = name + '=' + value + '; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
	}

	function dismiss( value ) {
		setCookie( COOKIE_NAME, value, COOKIE_DAYS );

		if ( 'accepted' === value ) {
			window.location.reload();
			return;
		}

		banner.classList.remove( 'is-visible' );

		banner.addEventListener( 'transitionend', function handler() {
			banner.remove();
			banner.removeEventListener( 'transitionend', handler );
		} );
	}

	// Slide up on the next frame so the initial state is guaranteed to paint first.
	window.requestAnimationFrame( function () {
		banner.classList.add( 'is-visible' );
	} );

	var buttons = banner.querySelectorAll( '[data-consent]' );
	buttons.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			dismiss( button.getAttribute( 'data-consent' ) );
		} );
	} );
} )();
