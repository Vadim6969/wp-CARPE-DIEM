/* Carpe Diem — минимум интерактива: мобильное меню и «прилипшая» шапка. */
( function () {
	'use strict';

	var header = document.querySelector( '.js-header' );
	var burger = document.querySelector( '.js-burger' );
	var nav = document.querySelector( '.js-nav' );

	if ( burger && nav ) {
		burger.addEventListener( 'click', function () {
			var open = burger.getAttribute( 'aria-expanded' ) === 'true';
			burger.setAttribute( 'aria-expanded', String( ! open ) );
			document.body.classList.toggle( 'nav-open', ! open );
		} );

		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) {
				burger.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nav-open' );
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && document.body.classList.contains( 'nav-open' ) ) {
				burger.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nav-open' );
				burger.focus();
			}
		} );
	}

	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 24 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}
} )();

/* Свотчи вариаций: кнопки размеров и цветов поверх нативных select'ов Woo. */
( function () {
	'use strict';

	var form = document.querySelector( '.variations_form' );
	if ( ! form ) {
		return;
	}

	var COLORS = {
		'чёрный': '#141416',
		'чёрный / коричневый': 'linear-gradient(135deg, #141416 50%, #6b4a2f 50%)',
		'графит': '#3a3a40',
		'серебро': '#c9c9cf'
	};

	var groups = [];

	form.querySelectorAll( '.variations select' ).forEach( function ( select ) {
		var isColor = ( select.getAttribute( 'data-attribute_name' ) || select.name ) === 'attribute_pa_color';
		var wrap = document.createElement( 'div' );
		wrap.className = 'swatches' + ( isColor ? ' swatches--color' : '' );

		Array.prototype.forEach.call( select.options, function ( option ) {
			if ( ! option.value ) {
				return;
			}
			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'swatch';
			btn.dataset.value = option.value;
			btn.setAttribute( 'aria-pressed', 'false' );

			if ( isColor ) {
				btn.title = option.textContent;
				btn.innerHTML = '<span class="swatch__dot"></span><span class="screen-reader-text">' + option.textContent + '</span>';
				btn.firstChild.style.background = COLORS[ option.textContent.trim().toLowerCase() ] || '#26262b';
			} else {
				btn.textContent = option.textContent;
			}

			btn.addEventListener( 'click', function () {
				select.value = option.value;
				select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			} );

			wrap.appendChild( btn );
		} );

		select.classList.add( 'is-swatched' );
		select.parentNode.appendChild( wrap );
		groups.push( { select: select, wrap: wrap } );
	} );

	function sync() {
		groups.forEach( function ( group ) {
			var available = Array.prototype.map.call( group.select.options, function ( o ) { return o.value; } );
			group.wrap.querySelectorAll( '.swatch' ).forEach( function ( btn ) {
				var ok = available.indexOf( btn.dataset.value ) > -1;
				btn.classList.toggle( 'is-disabled', ! ok );
				btn.disabled = ! ok;
				var active = group.select.value === btn.dataset.value;
				btn.classList.toggle( 'is-active', active );
				btn.setAttribute( 'aria-pressed', String( active ) );
			} );
		} );
	}

	form.addEventListener( 'change', sync );

	// Woo перерисовывает доступные значения через jQuery-события.
	if ( window.jQuery ) {
		window.jQuery( form ).on( 'woocommerce_update_variation_values reset_data check_variations', sync );
	}

	sync();
} )();
