/**
 * Trip booking page: keeps prices, the payment plan and the summary in step
 * with the guest's choices. Handles the £ / $ switch without a reload,
 * limits travellers to what the chosen room takes, and offers the payment
 * plan only where it applies. The maths matches jj_booking_quote() in
 * inc/trip-booking.php, which re-checks everything on the server anyway.
 */
( function () {
	'use strict';

	var form = document.querySelector( '.js-booking' );
	if ( ! form ) {
		return;
	}

	var symbols  = { gbp: '£', usd: '$' };
	var dates    = JSON.parse( form.getAttribute( 'data-dates' ) || '[]' );
	var currency = form.getAttribute( 'data-currency' ) || 'gbp';
	var autoFull = false;

	/* Minor units (pence/cents) to "£2,950" or "£816.67". */
	function money( minor ) {
		var whole = minor % 100 === 0;
		return symbols[ currency ] + ( minor / 100 ).toLocaleString( 'en-GB', {
			minimumFractionDigits: whole ? 0 : 2,
			maximumFractionDigits: whole ? 0 : 2
		} );
	}

	/* Equal payments; the last takes any odd pence. */
	function split( balance, count ) {
		if ( count < 1 || balance <= 0 ) {
			return [];
		}
		var each = Math.floor( balance / count );
		var out  = [];
		for ( var i = 0; i < count; i++ ) {
			out.push( each );
		}
		out[ count - 1 ] += balance - each * count;
		return out;
	}

	function checked( name ) {
		return form.querySelector( 'input[name="' + name + '"]:checked' );
	}

	function num( el, attr ) {
		return parseInt( el.getAttribute( attr ), 10 ) || 0;
	}

	function setText( key, text ) {
		var el = form.querySelector( '[data-summary="' + key + '"]' );
		if ( el ) {
			el.textContent = text;
		}
		return el;
	}

	function update() {
		var room = checked( 'room' );
		if ( ! room ) {
			return;
		}
		form.setAttribute( 'data-currency', currency );
		form.querySelector( 'input[name="currency"]' ).value = currency;

		var max = num( room, 'data-max' ) || 1;

		// Travellers: only as many as this room takes.
		var pills = form.querySelectorAll( 'input[name="guests"]' );
		var guest = checked( 'guests' );
		Array.prototype.forEach.call( pills, function ( pill ) {
			var over = parseInt( pill.value, 10 ) > max;
			pill.disabled = over;
			pill.closest( '.booking-pill' ).classList.toggle( 'is-disabled', over );
		} );
		if ( ! guest || guest.disabled ) {
			guest = pills[ Math.min( max, pills.length ) - 1 ];
			guest.checked = true;
		}
		var guests = parseInt( guest.value, 10 );

		var hint = form.querySelector( '.js-booking-guest-hint' );
		if ( hint ) {
			hint.textContent = max > 1
				? 'Prices are per person. This room takes up to ' + max + ' travellers.'
				: 'Prices are per person. This room is for one traveller.';
		}

		var deposit   = num( form, 'data-deposit-' + currency ) * 100 * guests;
		var planTotal = num( room, 'data-plan-' + currency ) * 100 * guests;
		var fullTotal = num( room, 'data-full-' + currency ) * 100 * guests;
		var payments  = deposit > 0 ? split( planTotal - deposit, dates.length ) : [];

		// Payment plan only where there are dates ahead and a balance to spread.
		var planChoice = form.querySelector( '.js-booking-plan' );
		if ( planChoice ) {
			var planInput = planChoice.querySelector( 'input' );
			var fullInput = form.querySelector( 'input[name="payment"][value="full"]' );
			planChoice.hidden  = ! payments.length;
			planInput.disabled = ! payments.length;
			if ( ! payments.length && planInput.checked ) {
				fullInput.checked = true;
				autoFull = true; // Switched for them; switch back when the plan returns.
			} else if ( payments.length && autoFull ) {
				planInput.checked = true;
				autoFull = false;
			}

			var planNote = form.querySelector( '[data-note="plan"]' );
			if ( planNote && payments.length ) {
				planNote.textContent = money( deposit ) + ' deposit today, then ' + payments.length +
					( 1 === payments.length ? ' automatic payment' : ' automatic payments' );
			}
			var fullNote = form.querySelector( '[data-note="full"]' );
			if ( fullNote ) {
				var saving = planTotal - fullTotal;
				fullNote.textContent = money( fullTotal ) + ' today' +
					( saving > 0 ? ', saving ' + money( saving ) : ', nothing more to pay' );
			}
		}

		var payment = checked( 'payment' );
		var onPlan  = payments.length && ( ! payment || 'plan' === payment.value );

		var departure = checked( 'departure' );
		if ( departure ) {
			setText( 'departure', departure.getAttribute( 'data-label' ) );
		}
		setText( 'room', room.getAttribute( 'data-name' ) );
		setText( 'guests', String( guests ) );
		setText( 'total', money( onPlan ? planTotal : fullTotal ) );
		setText( 'due', money( onPlan ? deposit : fullTotal ) );

		var list = form.querySelector( '[data-summary="plan"]' );
		var note = form.querySelector( '[data-summary="plannote"]' );
		if ( list ) {
			list.innerHTML = '';
			if ( onPlan ) {
				payments.forEach( function ( amount, i ) {
					var li = document.createElement( 'li' );
					var d  = document.createElement( 'span' );
					var a  = document.createElement( 'span' );
					d.textContent = dates[ i ];
					a.textContent = money( amount );
					li.appendChild( d );
					li.appendChild( a );
					list.appendChild( li );
				} );
			}
			list.hidden = ! onPlan;
		}
		if ( note ) {
			note.hidden = ! onPlan;
		}

		// The currency switch.
		Array.prototype.forEach.call( form.querySelectorAll( '.js-currency' ), function ( link ) {
			var on = link.getAttribute( 'data-currency' ) === currency;
			link.classList.toggle( 'is-active', on );
			if ( on ) {
				link.setAttribute( 'aria-current', 'true' );
			} else {
				link.removeAttribute( 'aria-current' );
			}
		} );
	}

	form.addEventListener( 'change', function ( e ) {
		if ( 'payment' === e.target.name ) {
			autoFull = false;
		}
		update();
	} );

	form.addEventListener( 'click', function ( e ) {
		var link = e.target.closest( '.js-currency' );
		if ( ! link ) {
			return;
		}
		e.preventDefault();
		currency = link.getAttribute( 'data-currency' );
		update();
	} );

	update();
}() );
