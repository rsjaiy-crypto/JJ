/**
 * "Get on The List" links with data-klaviyo-form open that Klaviyo form in
 * a pop-up. Klaviyo only loads for visitors who accepted cookies (see
 * jaiye_klaviyo_tracking() in functions.php); for everyone else the link
 * works as normal and opens its sign-up page.
 */
( function () {
	'use strict';

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest && e.target.closest( '[data-klaviyo-form]' );
		if ( ! link || ! window.klaviyo ) return;

		e.preventDefault();
		window._klOnsite = window._klOnsite || [];
		window._klOnsite.push( [ 'openForm', link.getAttribute( 'data-klaviyo-form' ) ] );
	} );
} )();
