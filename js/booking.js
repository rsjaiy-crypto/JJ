/**
 * Trip booking page: keeps the summary in step with the guest's choices,
 * limits travellers to what the chosen room takes, and hides the deposit
 * option for rooms without one. The form works without this; it only
 * makes the totals live. Prices are re-checked on the server regardless.
 */
( function () {
	'use strict';

	var form = document.querySelector( '.js-booking' );
	if ( ! form ) {
		return;
	}

	var rate     = parseFloat( form.getAttribute( 'data-rate' ) ) || 0;
	var autoFull = false;

	function money( n ) {
		return '£' + Math.round( n ).toLocaleString( 'en-GB' );
	}

	function checked( name ) {
		return form.querySelector( 'input[name="' + name + '"]:checked' );
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

		var price   = parseInt( room.getAttribute( 'data-price' ), 10 ) || 0;
		var deposit = parseInt( room.getAttribute( 'data-deposit' ), 10 ) || 0;
		var max     = parseInt( room.getAttribute( 'data-max' ), 10 ) || 1;

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

		// Deposit only where the room has one.
		var depositChoice = form.querySelector( '.js-booking-deposit' );
		if ( depositChoice ) {
			var depositInput = depositChoice.querySelector( 'input' );
			depositChoice.hidden = ! deposit;
			depositInput.disabled = ! deposit;
			if ( ! deposit && depositInput.checked ) {
				// Switched for them, so switch back if they pick a room with a deposit.
				form.querySelector( 'input[name="payment"][value="full"]' ).checked = true;
				autoFull = true;
			} else if ( deposit && autoFull ) {
				depositInput.checked = true;
				autoFull = false;
			}
		}
		var payment   = checked( 'payment' );
		var byDeposit = deposit && ( ! payment || 'deposit' === payment.value );

		var total = price * guests;
		var due   = ( byDeposit ? deposit : price ) * guests;

		var departure = checked( 'departure' );
		if ( departure ) {
			setText( 'departure', departure.getAttribute( 'data-label' ) );
		}
		setText( 'room', room.getAttribute( 'data-name' ) );
		setText( 'guests', String( guests ) );
		setText( 'total', money( total ) );
		setText( 'due', money( due ) );

		var balance = setText( 'balance', 'Balance of ' + money( total - due ) + ' due later' );
		if ( balance ) {
			balance.hidden = ! byDeposit;
		}

		// Same rounding as the trip page's estimate: nearest $50.
		var fx = form.querySelector( '[data-summary="fx"]' );
		if ( fx && rate > 0 ) {
			fx.textContent = 'Approx. $' + ( Math.round( due * rate / 50 ) * 50 ).toLocaleString( 'en-US' ) + ' USD today';
			fx.hidden = false;
		}
	}

	form.addEventListener( 'change', function ( e ) {
		if ( 'payment' === e.target.name ) {
			autoFull = false;
		}
		update();
	} );
	update();
}() );
