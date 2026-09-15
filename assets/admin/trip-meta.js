/**
 * Trip meta box UI — repeatable rows, media pickers, meter readouts.
 *
 * Vanilla JS apart from the wp.media dependency (jQuery is loaded for
 * wp.media's sake, not used directly here).
 */
( function () {
	'use strict';

	/* ── Repeater rows ───────────────────────────────────────────── */

	function nextIndex( repeater ) {
		// Row indexes only need to be unique within the field, not contiguous —
		// PHP reindexes on save via array_values().
		var rows = repeater.querySelectorAll( ':scope > .jj-trip-repeater__rows > .jj-trip-row' );
		return rows.length;
	}

	function addRow( repeater ) {
		var template = repeater.querySelector( ':scope > .jj-trip-repeater__template' );
		var rowsWrap = repeater.querySelector( ':scope > .jj-trip-repeater__rows' );
		if ( ! template || ! rowsWrap ) return;

		var html = template.innerHTML.replace( /__INDEX__/g, String( nextIndex( repeater ) ) );

		var holder = document.createElement( 'div' );
		holder.innerHTML = html.trim();

		var row = holder.firstElementChild;
		if ( ! row ) return;

		rowsWrap.appendChild( row );
		bindMedia( row );
		bindMeters( row );
		renumber( repeater );

		var firstInput = row.querySelector( 'input[type="text"], textarea' );
		if ( firstInput ) firstInput.focus();
	}

	function removeRow( row ) {
		var repeater = row.closest( '.jj-trip-repeater' );
		row.remove();
		if ( repeater ) renumber( repeater );
	}

	/**
	 * Keep the visible "Day 1, Day 2…" numbering in step with row order.
	 * Purely cosmetic — the saved order is DOM order.
	 */
	function renumber( repeater ) {
		var label = repeater.getAttribute( 'data-row-label' ) || 'Row';
		var rows  = repeater.querySelectorAll( ':scope > .jj-trip-repeater__rows > .jj-trip-row' );

		Array.prototype.forEach.call( rows, function ( row, i ) {
			var el = row.querySelector( '.jj-trip-row__label' );
			if ( el ) el.textContent = label + ' ' + ( i + 1 );
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var addBtn = e.target.closest( '.jj-trip-repeater__add' );
		if ( addBtn ) {
			e.preventDefault();
			addRow( addBtn.closest( '.jj-trip-repeater' ) );
			return;
		}

		var removeBtn = e.target.closest( '.jj-trip-row__remove' );
		if ( removeBtn ) {
			e.preventDefault();
			removeRow( removeBtn.closest( '.jj-trip-row' ) );
		}
	} );

	/* ── Media pickers ───────────────────────────────────────────── */

	function bindMedia( scope ) {
		var fields = ( scope || document ).querySelectorAll( '.jj-trip-media' );

		Array.prototype.forEach.call( fields, function ( field ) {
			if ( field.dataset.jjBound === '1' ) return;
			field.dataset.jjBound = '1';

			var input    = field.querySelector( '.jj-trip-media__input' );
			var preview  = field.querySelector( '.jj-trip-media__preview' );
			var selectEl = field.querySelector( '.jj-trip-media__select' );
			var clearEl  = field.querySelector( '.jj-trip-media__clear' );
			var multiple = field.dataset.multiple === '1';
			var frame    = null;

			selectEl.addEventListener( 'click', function ( e ) {
				e.preventDefault();

				if ( ! window.wp || ! window.wp.media ) return;

				if ( ! frame ) {
					frame = window.wp.media( {
						title: multiple ? 'Select images' : 'Select image',
						multiple: multiple ? 'add' : false,
						library: { type: 'image' }
					} );

					frame.on( 'select', function () {
						var selection = frame.state().get( 'selection' );
						var ids   = [];
						var html  = '';

						selection.each( function ( attachment ) {
							var data = attachment.toJSON();
							ids.push( data.id );

							var src = data.sizes && data.sizes.thumbnail
								? data.sizes.thumbnail.url
								: data.url;
							html += '<img src="' + src + '" alt="">';
						} );

						input.value    = multiple ? ids.join( ',' ) : ( ids[ 0 ] || '' );
						preview.innerHTML = html;
					} );
				}

				frame.open();
			} );

			clearEl.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				input.value       = '';
				preview.innerHTML = '';
			} );
		} );
	}

	/* ── Meter readouts ──────────────────────────────────────────── */

	function bindMeters( scope ) {
		var meters = ( scope || document ).querySelectorAll( '.jj-trip-meter__range' );

		Array.prototype.forEach.call( meters, function ( range ) {
			if ( range.dataset.jjBound === '1' ) return;
			range.dataset.jjBound = '1';

			var out = range.parentNode.querySelector( '.jj-trip-meter__out' );
			if ( ! out ) return;

			range.addEventListener( 'input', function () {
				out.textContent = range.value;
			} );
		} );
	}

	/* ── Init ────────────────────────────────────────────────────── */

	document.addEventListener( 'DOMContentLoaded', function () {
		bindMedia( document );
		bindMeters( document );

		document.querySelectorAll( '.jj-trip-repeater' ).forEach( function ( repeater ) {
			renumber( repeater );
		} );
	} );
} )();
