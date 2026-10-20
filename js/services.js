/**
 * Homepage services: an accordion on phones. Tapping a title opens its
 * description and "Find out more" link, closing any other open one.
 * Tablet and desktop keep the plain grid. Without JS every service shows.
 */
( function () {
	'use strict';

	var list = document.querySelector( '.js-services' );
	if ( ! list ) {
		return;
	}

	var phone   = window.matchMedia( '(max-width: 767px)' );
	var buttons = Array.from( list.querySelectorAll( '.fp-service-tile__toggle' ) );

	function setOpen( button, open ) {
		button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		button.closest( '.fp-service-tile' ).classList.toggle( 'is-open', open );
	}

	function apply() {
		list.classList.toggle( 'is-accordion', phone.matches );
		buttons.forEach( function ( button ) {
			button.hidden = ! phone.matches;
		} );
	}

	buttons.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			var open = button.getAttribute( 'aria-expanded' ) !== 'true';
			buttons.forEach( function ( other ) {
				setOpen( other, other === button && open );
			} );
		} );
	} );

	phone.addEventListener( 'change', apply );
	apply();
}() );
