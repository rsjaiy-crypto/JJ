/**
 * Our Journeys hub — client-side filtering and sorting.
 *
 * Every trip is already in the DOM, so filtering is instant with no requests.
 * That's the right trade at this catalogue size; if the trip count ever runs
 * into the hundreds this should move server-side with pagination.
 *
 * Progressive enhancement: with JS off the controls do nothing and all trips
 * stay visible.
 */
( function () {
	'use strict';

	var root = document.querySelector( '.js-jfilter' );
	var grid = document.querySelector( '.js-jfilter-grid' );
	if ( ! root || ! grid ) return;

	var cards    = Array.prototype.slice.call( grid.querySelectorAll( '.jcard' ) );
	var empty    = document.querySelector( '.js-jfilter-empty' );
	var status   = root.querySelector( '.jfilter__status' );
	var resetBtn = root.querySelector( '.js-jfilter-reset' );
	if ( ! cards.length ) return;

	var state = {
		brand:  'all',
		region: 'all',
		pace:   'all',
		sort:   'soonest'
	};

	/* ── Filtering ───────────────────────────────────────────────── */

	function matches( card ) {
		if ( state.brand !== 'all' && card.dataset.brand !== state.brand ) return false;
		if ( state.region !== 'all' && card.dataset.region !== state.region ) return false;
		if ( state.pace !== 'all' && card.dataset.pace !== state.pace ) return false;
		return true;
	}

	/* ── Sorting ─────────────────────────────────────────────────── */

	function sortCards( list ) {
		var sorted = list.slice();

		sorted.sort( function ( a, b ) {
			switch ( state.sort ) {

				case 'price-asc':
				case 'price-desc':
					var pa = parseInt( a.dataset.price, 10 ) || 0;
					var pb = parseInt( b.dataset.price, 10 ) || 0;

					// Trips with no price yet always sit at the end, whichever
					// direction we're sorting — a £0 trip isn't "cheapest".
					if ( pa === 0 && pb === 0 ) return 0;
					if ( pa === 0 ) return 1;
					if ( pb === 0 ) return -1;

					return state.sort === 'price-asc' ? pa - pb : pb - pa;

				case 'az':
					return ( a.dataset.title || '' ).localeCompare( b.dataset.title || '' );

				case 'soonest':
				default:
					var ya = parseInt( a.dataset.year, 10 ) || 0;
					var yb = parseInt( b.dataset.year, 10 ) || 0;

					// Undated (coming soon) trips go last rather than first.
					if ( ya === 0 && yb === 0 ) {
						return ( a.dataset.title || '' ).localeCompare( b.dataset.title || '' );
					}
					if ( ya === 0 ) return 1;
					if ( yb === 0 ) return -1;

					return ya - yb;
			}
		} );

		return sorted;
	}

	/* ── Apply ───────────────────────────────────────────────────── */

	function apply() {
		var visible = [];

		cards.forEach( function ( card ) {
			var show = matches( card );
			card.hidden = ! show;
			if ( show ) visible.push( card );
		} );

		// Reorder only the visible cards; hidden ones keep their place.
		sortCards( visible ).forEach( function ( card ) {
			grid.appendChild( card );
		} );

		if ( empty ) {
			empty.hidden = visible.length > 0;
		}

		if ( status ) {
			status.textContent = visible.length === cards.length
				? ''
				: visible.length + ' of ' + cards.length + ' journeys';
		}

		var filtering = state.brand !== 'all' || state.region !== 'all' || state.pace !== 'all';
		if ( resetBtn ) {
			resetBtn.hidden = ! filtering;
		}
	}

	/* ── Controls ────────────────────────────────────────────────── */

	root.addEventListener( 'click', function ( e ) {
		var chip = e.target.closest( '.jchip' );
		if ( ! chip ) return;

		var group = chip.parentNode.querySelectorAll( '.jchip' );
		Array.prototype.forEach.call( group, function ( c ) {
			var isActive = c === chip;
			c.classList.toggle( 'is-active', isActive );
			c.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
		} );

		state[ chip.dataset.filter ] = chip.dataset.value;
		apply();
	} );

	root.addEventListener( 'change', function ( e ) {
		var select = e.target.closest( '.jfilter__select' );
		if ( ! select ) return;

		if ( select.hasAttribute( 'data-sort' ) ) {
			state.sort = select.value;
		} else {
			state[ select.dataset.filter ] = select.value;
		}

		apply();
	} );

	if ( resetBtn ) {
		resetBtn.addEventListener( 'click', function () {
			state.brand  = 'all';
			state.region = 'all';
			state.pace   = 'all';

			root.querySelectorAll( '.jchip' ).forEach( function ( c ) {
				var isAll = c.dataset.value === 'all';
				c.classList.toggle( 'is-active', isAll );
				c.setAttribute( 'aria-pressed', isAll ? 'true' : 'false' );
			} );

			root.querySelectorAll( '.jfilter__select' ).forEach( function ( s ) {
				if ( ! s.hasAttribute( 'data-sort' ) ) s.value = 'all';
			} );

			apply();
		} );
	}

	/* ── Deep links: /our-journeys/#reading-retreats ─────────────── */

	function applyHash() {
		var map = {
			'#reading-retreats': 'btl',
			'#group-trips':      'jj-edit'
		};

		var brand = map[ window.location.hash ];
		if ( ! brand ) return;

		var chip = root.querySelector( '.jchip[data-value="' + brand + '"]' );
		if ( chip ) chip.click();
	}

	applyHash();
	window.addEventListener( 'hashchange', applyHash );

	apply();
} )();
