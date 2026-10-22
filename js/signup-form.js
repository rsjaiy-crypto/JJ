/**
 * Inline email sign-up (Links page). On submit only, sends the person
 * straight to Klaviyo's public client API: first name to their profile,
 * then an email marketing subscription to the form's list. No Klaviyo
 * script and no cookies, so it works whatever the visitor chose on the
 * cookie banner. Same approach as js/btl-waitlist.js.
 *
 * The form carries data-company (public site ID), data-list and
 * data-source.
 */
( function () {
	'use strict';

	var API      = 'https://a.klaviyo.com/client/';
	var REVISION = '2024-10-15';

	Array.prototype.forEach.call( document.querySelectorAll( '.js-signup' ), function ( form ) {
		var section  = form.closest( 'section' ) || document;
		var body     = section.querySelector( '.js-signup-body' );
		var done     = section.querySelector( '.js-signup-done' );
		var errorBox = form.querySelector( '.js-signup-error' );
		var submit   = form.querySelector( '.js-signup-submit' );

		function post( endpoint, data ) {
			return fetch( API + endpoint + '/?company_id=' + encodeURIComponent( form.getAttribute( 'data-company' ) ), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/vnd.api+json',
					'Accept': 'application/vnd.api+json',
					'revision': REVISION
				},
				body: JSON.stringify( { data: data } )
			} ).then( function ( res ) {
				if ( ! res.ok ) throw new Error( endpoint + ' ' + res.status );
			} );
		}

		function success() {
			body.hidden = true;
			done.hidden = false;
			done.querySelector( '[tabindex="-1"]' ).focus();
		}

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			errorBox.hidden = true;

			[ 'first_name', 'email' ].forEach( function ( name ) {
				form.elements[ name ].value = form.elements[ name ].value.trim();
			} );
			if ( ! form.checkValidity() ) {
				form.reportValidity();
				return;
			}

			// A filled honeypot is a bot: thank it and send nothing.
			if ( form.elements.website.value ) {
				success();
				return;
			}

			var email = form.elements.email.value;
			submit.disabled = true;
			submit.setAttribute( 'aria-busy', 'true' );

			post( 'profiles', { type: 'profile', attributes: { email: email, first_name: form.elements.first_name.value } } )
				.then( function () {
					return post( 'subscriptions', {
						type: 'subscription',
						attributes: {
							custom_source: form.getAttribute( 'data-source' ),
							profile: {
								data: {
									type: 'profile',
									attributes: {
										email: email,
										subscriptions: { email: { marketing: { consent: 'SUBSCRIBED' } } }
									}
								}
							}
						},
						relationships: {
							list: { data: { type: 'list', id: form.getAttribute( 'data-list' ) } }
						}
					} );
				} )
				.then( success )
				.catch( function () {
					errorBox.hidden = false;
				} )
				.then( function () {
					submit.disabled = false;
					submit.removeAttribute( 'aria-busy' );
				} );
		} );
	} );
} )();
