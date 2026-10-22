/**
 * Between the Lines waitlist pop-up (inc/btl-waitlist-form.php).
 *
 * Opens from any [data-btl-waitlist] link, and on arrival with
 * ?waitlist=btl. On submit it sends the sign-up straight to Klaviyo's
 * public client API: the email marketing subscription to the list first
 * (it triggers the welcome flow), then the profile's names and answers.
 * No Klaviyo script, no cookies, so it works whatever the visitor chose on
 * the cookie banner.
 */
( function () {
	'use strict';

	var dialog = document.getElementById( 'btl-waitlist' );
	if ( ! dialog ) return;

	var form     = dialog.querySelector( '.js-btlw-form' );
	var body     = dialog.querySelector( '.js-btlw-body' );
	var done     = dialog.querySelector( '.js-btlw-done' );
	var errorBox = dialog.querySelector( '.js-btlw-error' );
	var submit   = dialog.querySelector( '.js-btlw-submit' );
	var clubName = dialog.querySelector( '.js-btlw-club-name' );
	var referrer = dialog.querySelector( '.js-btlw-referrer' );
	var refInput = referrer.querySelector( 'input' );
	var opener   = null;

	var API      = 'https://a.klaviyo.com/client/';
	var REVISION = '2024-10-15';

	/* ── Open and close ─────────────────────────────────────────── */

	function open( trigger ) {
		opener = trigger || null;
		if ( dialog.open ) return;
		if ( typeof dialog.showModal === 'function' ) {
			dialog.showModal();
		} else {
			dialog.setAttribute( 'open', '' );
		}
		document.documentElement.classList.add( 'btlw-open' );
		var title = dialog.querySelector( done.hidden ? '#btlw-title' : '.js-btlw-done .btlw__title' );
		if ( title ) title.focus();
	}

	function close() {
		if ( typeof dialog.close === 'function' ) {
			dialog.close();
		} else {
			dialog.removeAttribute( 'open' );
			onClosed();
		}
	}

	function onClosed() {
		document.documentElement.classList.remove( 'btlw-open' );
		if ( opener && opener.focus ) opener.focus();
	}

	dialog.addEventListener( 'close', onClosed );
	dialog.querySelector( '.js-btlw-close' ).addEventListener( 'click', close );

	// Clicking the dimmed area outside the panel closes it.
	dialog.addEventListener( 'click', function ( e ) {
		if ( e.target === dialog ) close();
	} );

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest && e.target.closest( '[data-btl-waitlist]' );
		if ( ! link ) return;
		e.preventDefault();
		open( link );
	} );

	if ( /[?&]waitlist=btl(?:&|$)/.test( window.location.search ) ) {
		open( null );
	}

	/* ── Questions that depend on earlier answers ───────────────── */

	function sync() {
		var club = form.querySelector( '.js-btlw-club:checked' );
		clubName.hidden = ! club || 'Yes' !== club.value;

		var friend = form.querySelector( '.js-btlw-friend' ).checked;
		referrer.hidden   = ! friend;
		refInput.required = friend;
	}
	form.addEventListener( 'change', sync );
	sync();

	/* ── Submit ─────────────────────────────────────────────────── */

	function value( name ) {
		var el = form.elements[ name ];
		if ( ! el ) return '';
		return ( el.value || '' ).trim();
	}

	function showError( message, err ) {
		if ( message ) {
			errorBox.textContent = message;
		} else {
			errorBox.innerHTML = dialog.querySelector( '.js-btlw-error-text' ).innerHTML;
		}
		// Klaviyo's reason, for logged-in editors only.
		if ( err && document.body.classList.contains( 'logged-in' ) ) {
			var detail = document.createElement( 'small' );
			detail.style.display = 'block';
			detail.style.marginTop = '0.5em';
			detail.textContent = 'Only you can see this. Klaviyo said: ' + err.message;
			errorBox.appendChild( detail );
		}
		errorBox.hidden = false;
	}

	function post( endpoint, data ) {
		return fetch( API + endpoint + '?company_id=' + encodeURIComponent( form.getAttribute( 'data-company' ) ), {
			method: 'POST',
			headers: {
				'Content-Type': 'application/vnd.api+json',
				'Accept': 'application/vnd.api+json',
				'revision': REVISION
			},
			body: JSON.stringify( { data: data } )
		} ).then( function ( res ) {
			if ( ! res.ok ) {
				return res.text().then( function ( text ) {
					throw new Error( endpoint + ' ' + res.status + ': ' + text.slice( 0, 400 ) );
				} );
			}
		} );
	}

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();
		errorBox.hidden = true;

		// Trim before checking, so a name of spaces doesn't count.
		[ 'first_name', 'last_name', 'email', 'Whereabouts', 'Book club name', 'Referred by' ].forEach( function ( name ) {
			var el = form.elements[ name ];
			if ( el ) el.value = el.value.trim();
		} );

		if ( ! form.checkValidity() ) {
			form.reportValidity();
			return;
		}

		var email = value( 'email' );

		// A filled honeypot is a bot: thank it and send nothing.
		if ( value( 'website' ) ) {
			success();
			return;
		}

		// Only answered questions are sent, so a blank never overwrites an
		// earlier answer on the same profile. Names and values match the
		// earlier Between the Lines sign-ups in Klaviyo.
		var properties = {
			'Retreat Interest': value( 'BTL Volume interest' )
		};

		var club = form.querySelector( 'input[name="Book club member"]:checked' );
		if ( club ) {
			properties[ 'Part of book club' ] = club.value;
			if ( 'Yes' === club.value && value( 'Book club name' ) ) {
				properties[ 'Book club name' ] = value( 'Book club name' );
			}
		}

		var heard = Array.prototype.map.call( form.querySelectorAll( 'input[name="Heard about us"]:checked' ), function ( el ) {
			return el.value;
		} );
		if ( heard.length ) properties[ 'How did you hear about us' ] = heard.join( ', ' );
		if ( heard.indexOf( 'A friend told me' ) > -1 ) properties[ 'Referred by' ] = value( 'Referred by' );

		// Where they're based goes in Klaviyo's own location fields.
		var location = { country: form.querySelector( 'input[name="Country"]:checked' ).value };
		if ( value( 'Whereabouts' ) ) location.city = value( 'Whereabouts' );

		var profile = { email: email, first_name: value( 'first_name' ), location: location, properties: properties };
		if ( value( 'last_name' ) ) profile.last_name = value( 'last_name' );

		submit.disabled = true;
		submit.setAttribute( 'aria-busy', 'true' );

		// The subscription first: it's what puts them on the list and starts
		// the welcome flow. Then their name and answers on the profile; if only
		// that step fails they're still on the list, so it just logs.
		post( 'subscriptions', {
			type: 'subscription',
			attributes: {
				custom_source: 'Between the Lines waitlist form (website)',
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
				return post( 'profiles', { type: 'profile', attributes: profile } )
					.catch( function ( err ) {
						window.console && console.error( 'Waitlist answers not saved', err );
					} );
			} )
			.then( success, function ( err ) {
				window.console && console.error( 'Waitlist sign-up failed', err );
				showError( null, err );
			} )
			.then( function () {
				submit.disabled = false;
				submit.removeAttribute( 'aria-busy' );
			} );
	} );

	function success() {
		body.hidden = true;
		done.hidden = false;
		done.querySelector( '.btlw__title' ).focus();
	}
} )();
