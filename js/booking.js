/**
 * Trip booking page: keeps prices, the payment plan and the summary in step
 * with the guest's choices. Handles the £ / $ switch without a reload,
 * limits travellers to what the chosen room takes, and offers the payment
 * plan only where it applies. The maths matches jj_booking_quote() in
 * inc/trip-booking.php, which re-checks everything on the server anyway.
 */

/* Confirmation: one burst of confetti in the brand colours. */
( function () {
	'use strict';

	if ( ! document.querySelector( '.js-booking-celebrate' ) ||
		window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var root    = getComputedStyle( document.documentElement );
	var colours = [ '--color-salmon', '--color-sage', '--color-forest', '--color-cream' ].map( function ( name ) {
		return root.getPropertyValue( name ).trim();
	} ).filter( Boolean );

	var canvas = document.createElement( 'canvas' );
	var ctx    = canvas.getContext( '2d' );
	var ratio  = window.devicePixelRatio || 1;
	var width  = window.innerWidth;
	var height = window.innerHeight;
	var pieces = [];
	var start  = null;

	canvas.setAttribute( 'aria-hidden', 'true' );
	canvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:9999';
	canvas.width  = width * ratio;
	canvas.height = height * ratio;
	ctx.scale( ratio, ratio );
	document.body.appendChild( canvas );

	/* Two bursts, from the lower left and lower right, arcing inwards. */
	for ( var i = 0; i < 160; i++ ) {
		var left  = i % 2 === 0;
		var angle = ( left ? -60 : -120 ) + ( Math.random() * 30 - 15 );
		var speed = height * ( 0.022 + Math.random() * 0.012 );
		pieces.push( {
			x: left ? 0 : width,
			y: height * 0.85,
			vx: Math.cos( angle * Math.PI / 180 ) * speed,
			vy: Math.sin( angle * Math.PI / 180 ) * speed,
			w: 6 + Math.random() * 6,
			h: 4 + Math.random() * 4,
			spin: Math.random() * Math.PI,
			turn: Math.random() * 0.3 - 0.15,
			colour: colours[ i % colours.length ] || '#e48168'
		} );
	}

	function frame( now ) {
		if ( start === null ) {
			start = now;
		}
		var elapsed = now - start;
		ctx.clearRect( 0, 0, width, height );
		ctx.globalAlpha = elapsed > 3200 ? Math.max( 0, 1 - ( elapsed - 3200 ) / 800 ) : 1;

		pieces.forEach( function ( p ) {
			p.vy += 0.35;
			p.vx *= 0.985;
			p.vy *= 0.985;
			p.x += p.vx;
			p.y += p.vy;
			p.spin += p.turn;
			ctx.save();
			ctx.translate( p.x, p.y );
			ctx.rotate( p.spin );
			ctx.scale( 1, Math.cos( p.spin * 2 ) );
			ctx.fillStyle = p.colour;
			ctx.fillRect( -p.w / 2, -p.h / 2, p.w, p.h );
			ctx.restore();
		} );

		if ( elapsed < 4000 ) {
			window.requestAnimationFrame( frame );
		} else {
			canvas.remove();
		}
	}

	window.requestAnimationFrame( frame );
}() );

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

	/* Discount code: what Stripe said about it, and the message to show. */
	var codeBox   = form.querySelector( '.js-booking-code' );
	var discount  = null;
	var codeState = { text: '', cls: '' };

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

		// A code comes off the plan balance, so the deposit stays the same.
		// Matches jj_booking_discount_amount().
		var off = 0;
		if ( onPlan && discount ) {
			off = discount.percent ? Math.round( planTotal * discount.percent / 100 ) : discount.amount;
			off = Math.max( 0, Math.min( off, planTotal - deposit - dates.length ) );
			payments = split( planTotal - deposit - off, dates.length );
		}
		showCode( onPlan );

		var departure = checked( 'departure' );
		if ( departure ) {
			setText( 'departure', departure.getAttribute( 'data-label' ) );
		}
		setText( 'room', room.getAttribute( 'data-name' ) );
		setText( 'guests', String( guests ) );
		setText( 'total', money( onPlan ? planTotal - off : fullTotal ) );
		setText( 'discount', off ? '−' + money( off ) : '' ).parentNode.hidden = ! off;
		setText( 'discountlabel', off ? 'Discount (' + discount.code + ')' : 'Discount' );
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

	/* The code box: on for the payment plan, with Stripe's answer below it. */
	function showCode( onPlan ) {
		if ( ! codeBox ) {
			return;
		}
		var input   = codeBox.querySelector( 'input' );
		var message = codeBox.querySelector( '.js-booking-code-message' );
		var state   = onPlan && codeState.text ? codeState : { text: 'Discount codes apply to the payment plan.', cls: '' };
		input.disabled = ! onPlan;
		codeBox.querySelector( '.js-booking-code-apply' ).disabled = ! onPlan;
		message.textContent = state.text;
		message.className = 'booking-code__message js-booking-code-message' + ( state.cls ? ' ' + state.cls : '' );
	}

	/* Ask the site (which asks Stripe) about the code typed. */
	function applyCode() {
		var input = codeBox.querySelector( 'input' );
		var code  = input.value.trim();
		discount  = null;
		if ( ! code ) {
			codeState = { text: '', cls: '' };
			update();
			return;
		}

		var url = codeBox.getAttribute( 'data-endpoint' ) +
			( codeBox.getAttribute( 'data-endpoint' ).indexOf( '?' ) === -1 ? '?' : '&' ) +
			'trip=' + encodeURIComponent( codeBox.getAttribute( 'data-trip' ) ) +
			'&currency=' + encodeURIComponent( currency ) +
			'&code=' + encodeURIComponent( code );
		codeState = { text: 'Checking your code…', cls: '' };
		update();

		window.fetch( url, { credentials: 'same-origin' } )
			.then( function ( response ) {
				return response.json().then( function ( data ) {
					return { ok: response.ok, data: data };
				} );
			} )
			.then( function ( result ) {
				if ( result.ok && result.data.code ) {
					discount = result.data;
					input.value = discount.code;
					codeState = { text: discount.code + ': ' + discount.label + ' your payment plan.', cls: 'is-ok' };
				} else {
					codeState = { text: result.data.message || 'Sorry, that code isn’t recognised.', cls: 'is-error' };
				}
			} )
			.catch( function () {
				codeState = { text: 'We couldn’t check that code just now. Please try again.', cls: 'is-error' };
			} )
			.then( update );
	}

	if ( codeBox ) {
		codeBox.querySelector( '.js-booking-code-apply' ).addEventListener( 'click', applyCode );
		codeBox.querySelector( 'input' ).addEventListener( 'keydown', function ( e ) {
			if ( 'Enter' === e.key ) {
				e.preventDefault(); // Apply the code rather than submit the booking.
				applyCode();
			}
		} );
		codeBox.querySelector( 'input' ).addEventListener( 'input', function ( e ) {
			if ( discount && e.target.value.trim().toUpperCase() !== discount.code ) {
				discount  = null;
				codeState = { text: '', cls: '' };
				update();
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
		if ( discount ) {
			applyCode(); // A £ amount off doesn't carry over to $.
		} else {
			update();
		}
	} );

	update();
}() );
