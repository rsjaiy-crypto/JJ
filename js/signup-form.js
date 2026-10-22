/**
 * Inline email sign-up (Links page). On submit only, sends the person
 * straight to Klaviyo's public client API: first the email marketing
 * subscription to the form's list (the part that matters, and what starts
 * the welcome flow), then their first name on the profile. No Klaviyo
 * script and no cookies, so it works whatever the visitor chose on the
 * cookie banner. Same approach as js/btl-waitlist.js.
 *
 * The form carries data-company (public site ID), data-list and
 * data-source. If Klaviyo refuses, its reason goes to the console, and is
 * shown under the error message to logged-in editors.
 */
( function () {
	'use strict';

	var API      = 'https://a.klaviyo.com/client/';
	var REVISION = '2024-10-15';

	function post( company, endpoint, data ) {
		return fetch( API + endpoint + '?company_id=' + encodeURIComponent( company ), {
			method: 'POST',
			headers: {
				'Content-Type': 'application/vnd.api+json',
				'Accept': 'application/vnd.api+json',
				'revision': REVISION
			},
			body: JSON.stringify( { data: data } )
		} ).then( function ( res ) {
			if ( res.ok ) return;
			return res.text().then( function ( text ) {
				throw new Error( endpoint + ' ' + res.status + ': ' + text.slice( 0, 400 ) );
			} );
		} );
	}

	Array.prototype.forEach.call( document.querySelectorAll( '.js-signup' ), function ( form ) {
		var section  = form.closest( 'section' ) || document;
		var body     = section.querySelector( '.js-signup-body' );
		var done     = section.querySelector( '.js-signup-done' );
		var errorBox = form.querySelector( '.js-signup-error' );
		var detail   = null;
		var submit   = form.querySelector( '.js-signup-submit' );
		var company  = form.getAttribute( 'data-company' );

		function success() {
			body.hidden = true;
			done.hidden = false;
			done.querySelector( '[tabindex="-1"]' ).focus();
		}

		function fail( err ) {
			window.console && console.error( 'Sign-up failed', err );
			if ( document.body.classList.contains( 'logged-in' ) ) {
				if ( ! detail ) {
					detail = document.createElement( 'small' );
					detail.style.display = 'block';
					detail.style.marginTop = '0.5em';
					errorBox.appendChild( detail );
				}
				detail.textContent = 'Only you can see this. Klaviyo said: ' + err.message;
			}
			errorBox.hidden = false;
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

			post( company, 'subscriptions', {
				type: 'subscription',
				attributes: {
					custom_source: form.getAttribute( 'data-source' ),
					profile: {
						data: {
							type: 'profile',
							attributes: {
								email: email
							}
						}
					}
				},
				relationships: {
					list: { data: { type: 'list', id: form.getAttribute( 'data-list' ) } }
				}
			} )
				.then( function () {
					// They're on the list; a name that doesn't save isn't worth
					// an error, so this step only logs a problem.
					return post( company, 'profiles', { type: 'profile', attributes: { email: email, first_name: form.elements.first_name.value } } )
						.catch( function ( err ) {
							window.console && console.error( 'Sign-up name not saved', err );
						} );
				} )
				.then( success, fail )
				.then( function () {
					submit.disabled = false;
					submit.removeAttribute( 'aria-busy' );
				} );
		} );
	} );
} )();
