/* Небольшие улучшения штатных форм WordPress. */
( function ( $ ) {
	'use strict';
	var dirty = false;
	var settings = document.querySelector( '.cd-settings-form' );
	function markDirty() {
		dirty = true;
		var status = document.querySelector( '.cd-save-status' );
		if ( status ) { status.textContent = 'Есть несохранённые изменения'; }
	}
	if ( settings ) {
		$( settings ).on( 'input change', markDirty );
		$( '#cd-product_ids' ).on( 'select2:select', function ( e ) {
			var option = e.params.data.element;
			if ( option ) { this.appendChild( option ); $( this ).trigger( 'change.select2' ); }
		} );
		settings.addEventListener( 'submit', function () { dirty = false; } );
		window.addEventListener( 'beforeunload', function ( e ) { if ( dirty ) { e.preventDefault(); e.returnValue = ''; } } );
	}
	function numberRows( editor ) {
		editor.querySelectorAll( 'tbody tr' ).forEach( function ( row, index ) {
			row.querySelectorAll( 'input' ).forEach( function ( input, col ) {
				input.name = 'cd_size[rows][' + index + '][]';
				input.setAttribute( 'aria-label', 'Строка ' + ( index + 1 ) + ', столбец ' + ( col + 1 ) );
			} );
		} );
	}
	document.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( 'button' );
		if ( ! button ) { return; }
		if ( button.matches( '.cd-media-pick' ) ) {
			var media = button.closest( '.cd-media' );
			var frame = wp.media( { title: 'Изображение для сайта', button: { text: 'Использовать изображение' }, library: { type: 'image' }, multiple: false } );
			frame.on( 'select', function () {
				var selected = frame.state().get( 'selection' ).first().toJSON();
				media.querySelector( 'input' ).value = selected.id;
				var image = document.createElement( 'img' );
				image.src = selected.sizes && selected.sizes.medium ? selected.sizes.medium.url : selected.url;
				image.alt = selected.alt || '';
				media.querySelector( '.cd-media__preview' ).replaceChildren( image );
				media.querySelector( '.cd-media-clear' ).hidden = false;
				markDirty();
			} );
			frame.open();
		}
		if ( button.matches( '.cd-media-clear' ) ) {
			var block = button.closest( '.cd-media' );
			block.querySelector( 'input' ).value = 0;
			block.querySelector( '.cd-media__preview' ).replaceChildren();
			button.hidden = true;
			markDirty();
		}
		if ( button.matches( '.cd-move' ) ) {
			var item = button.closest( 'li' );
			var other = button.dataset.direction === 'up' ? item.previousElementSibling : item.nextElementSibling;
			if ( other ) {
				if ( button.dataset.direction === 'up' ) { other.before( item ); } else { other.after( item ); }
				button.focus(); markDirty();
			}
		}
		if ( button.matches( '.cd-row-add' ) ) {
			var editor = button.closest( '.cd-size-editor' );
			var body = editor.querySelector( 'tbody' );
			if ( body.rows.length >= 30 ) { return; }
			var row = body.rows[0].cloneNode( true );
			row.querySelectorAll( 'input' ).forEach( function ( input ) { input.value = ''; } );
			body.appendChild( row ); numberRows( editor ); row.querySelector( 'input' ).focus();
		}
		if ( button.matches( '.cd-row-remove' ) ) {
			var sizeEditor = button.closest( '.cd-size-editor' );
			var current = button.closest( 'tr' );
			if ( sizeEditor.querySelectorAll( 'tbody tr' ).length === 1 ) {
				current.querySelectorAll( 'input' ).forEach( function ( input ) { input.value = ''; } );
			} else { current.remove(); }
			numberRows( sizeEditor );
		}
	} );
} )( jQuery );
