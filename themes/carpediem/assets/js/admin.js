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

	/* Помощник не хранит отдельные данные: он читает и подсвечивает поля WooCommerce. */
	var productGuide = document.querySelector( '[data-cd-product-guide]' );
	if ( productGuide ) {
		var productType = document.querySelector( '#product-type' );
		var progressValue = productGuide.querySelector( '[data-cd-product-progress]' );
		var progressDone = productGuide.querySelector( '[data-cd-product-done]' );
		var updatePending = false;

		function fieldHasValue( selector ) {
			var field = document.querySelector( selector );
			return !! ( field && String( field.value || '' ).trim() );
		}

		function purchaseIsReady() {
			if ( productType && 'variable' === productType.value ) {
				var variationPrices = document.querySelectorAll( '#variable_product_options .variable_regular_price' );
				if ( ! variationPrices.length ) { return 'true' === productGuide.dataset.purchaseReady; }
				return Array.prototype.some.call(
					variationPrices,
					function ( field ) { return String( field.value || '' ).trim(); }
				);
			}
			return fieldHasValue( '#_regular_price' );
		}

		function stepIsReady( key ) {
			if ( 'name' === key ) { return fieldHasValue( '#title' ); }
			if ( 'photo' === key ) {
				var thumbnailId = document.querySelector( '#_thumbnail_id' );
				return !! ( thumbnailId && parseInt( thumbnailId.value, 10 ) > 0 ) || !! document.querySelector( '#set-post-thumbnail img' );
			}
			if ( 'category' === key ) { return !! document.querySelector( '#product_catchecklist input:checked' ); }
			if ( 'purchase' === key ) { return purchaseIsReady(); }
			return false;
		}

		function updateProductGuide() {
			updatePending = false;
			var complete = 0;
			productGuide.querySelectorAll( '[data-cd-product-check]' ).forEach( function ( step ) {
				var ready = stepIsReady( step.dataset.cdProductCheck );
				step.classList.toggle( 'is-complete', ready );
				step.querySelector( '[data-cd-product-status]' ).textContent = ready ? 'Готово' : 'Нужно заполнить';
				if ( ready ) { complete += 1; }
			} );
			progressDone.textContent = complete;
			progressValue.style.width = ( complete / 4 * 100 ) + '%';

			productGuide.querySelectorAll( '[data-cd-product-type]' ).forEach( function ( button ) {
				var selected = productType && button.dataset.cdProductType === productType.value;
				button.classList.toggle( 'is-selected', selected );
				button.setAttribute( 'aria-pressed', selected ? 'true' : 'false' );
			} );

			var purchaseCopy = productGuide.querySelector( '[data-cd-product-step-copy="purchase"]' );
			if ( purchaseCopy ) {
				purchaseCopy.textContent = productType && 'variable' === productType.value
					? 'Добавьте размеры или цвета во вкладке «Атрибуты», создайте вариации и укажите их цены.'
					: 'Укажите обычную цену. Остаток при необходимости задаётся во вкладке «Запасы».';
			}
		}

		function scheduleProductGuideUpdate() {
			if ( updatePending ) { return; }
			updatePending = true;
			window.requestAnimationFrame( updateProductGuide );
		}

		function revealProductSection( key ) {
			var target;
			var focusTarget;
			if ( 'name' === key ) {
				target = focusTarget = document.querySelector( '#title' );
			} else if ( 'photo' === key ) {
				target = document.querySelector( '#postimagediv' );
				focusTarget = document.querySelector( '#set-post-thumbnail' );
			} else if ( 'category' === key ) {
				target = document.querySelector( '#product_catdiv' );
				focusTarget = target && target.querySelector( 'input, a, button' );
			} else if ( 'purchase' === key ) {
				target = document.querySelector( '#woocommerce-product-data' );
				var tabSelector = productType && 'variable' === productType.value ? '.attribute_options a' : '.general_options a';
				var tab = target && target.querySelector( tabSelector );
				if ( tab ) { tab.click(); }
				focusTarget = productType && 'variable' === productType.value ? tab : document.querySelector( '#_regular_price' );
			} else if ( 'publish' === key ) {
				target = document.querySelector( '#submitdiv' );
				focusTarget = document.querySelector( '#publish' );
			}
			if ( ! target ) { return; }
			target.scrollIntoView( {
				behavior: window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 'auto' : 'smooth',
				block: 'center'
			} );
			target.classList.remove( 'cd-product-highlight' );
			window.requestAnimationFrame( function () { target.classList.add( 'cd-product-highlight' ); } );
			window.setTimeout( function () { target.classList.remove( 'cd-product-highlight' ); }, 900 );
			window.setTimeout( function () { if ( focusTarget ) { focusTarget.focus( { preventScroll: true } ); } }, 250 );
		}

		productGuide.addEventListener( 'click', function ( e ) {
			var typeButton = e.target.closest( '[data-cd-product-type]' );
			if ( typeButton && productType && 'true' === productGuide.dataset.isNew ) {
				productType.value = typeButton.dataset.cdProductType;
				$( productType ).trigger( 'change' );
				scheduleProductGuideUpdate();
				return;
			}
			var sectionButton = e.target.closest( '[data-cd-product-section]' );
			if ( sectionButton ) { revealProductSection( sectionButton.dataset.cdProductSection ); }
		} );
		document.addEventListener( 'input', scheduleProductGuideUpdate, true );
		document.addEventListener( 'change', scheduleProductGuideUpdate, true );
		$( document.body ).on( 'woocommerce_variations_loaded woocommerce_variations_added woocommerce_variations_saved', scheduleProductGuideUpdate );

		if ( window.MutationObserver ) {
			var productGuideObserver = new MutationObserver( scheduleProductGuideUpdate );
			document.querySelectorAll( '#postimagediv, #woocommerce-product-data' ).forEach( function ( fieldGroup ) {
				productGuideObserver.observe( fieldGroup, { childList: true, subtree: true } );
			} );
		}
		updateProductGuide();
	}
} )( jQuery );
