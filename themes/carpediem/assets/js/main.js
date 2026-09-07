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
			if ( ! document.body.classList.contains( 'nav-open' ) ) {
				return;
			}

			if ( e.key === 'Escape' ) {
				burger.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nav-open' );
				burger.focus();
				return;
			}

			// Пока меню открыто, Tab не должен уводить фокус на страницу под ним.
			if ( e.key === 'Tab' ) {
				var stops = [ burger ].concat( Array.prototype.slice.call( nav.querySelectorAll( 'a[href], button' ) ) );
				var first = stops[ 0 ];
				var last = stops[ stops.length - 1 ];

				if ( e.shiftKey && document.activeElement === first ) {
					e.preventDefault();
					last.focus();
				} else if ( ! e.shiftKey && document.activeElement === last ) {
					e.preventDefault();
					first.focus();
				}
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

	var colors = window.carpediemUI ? window.carpediemUI.colors : {};

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
				btn.innerHTML = '<span class="swatch__dot"></span><span class="screen-reader-text"></span>';
				btn.lastChild.textContent = option.textContent;
				btn.firstChild.style.background = colors[ option.value ] || '#888888';
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

/* Делегирование сохраняет обработчики после замены формы WooCommerce. */
( function () {
	'use strict';
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '.woocommerce-cart-form .js-qty' );
		if ( ! btn ) { return; }
		var input = btn.parentNode.querySelector( '.qty__input' );
		var step = Number( input.step ) || 1;
		var next = Number( input.value ) + Number( btn.dataset.step ) * step;
		if ( next < Number( input.min ) || ( input.max && next > Number( input.max ) ) ) { return; }
		input.value = next;
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	} );
	document.addEventListener( 'change', function ( e ) {
		if ( ! e.target.matches( '.woocommerce-cart-form .qty__input' ) || ! e.target.reportValidity() ) { return; }
		var form = e.target.closest( 'form' );
		var update = form.querySelector( '.js-update-cart' );
		update.disabled = false;
		update.click();
	} );
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_cart_totals removed_from_cart', function () {
			window.jQuery( document.body ).trigger( 'wc_fragment_refresh' );
		} );
	}
} )();

/* «Купить в 1 клик»: нативный <dialog> + отправка в admin-ajax. */
( function () {
	'use strict';

	var dialog = document.getElementById( 'one-click' );
	var open = document.querySelector( '.js-one-click-open' );
	if ( ! dialog || ! open ) {
		return;
	}

	var form = dialog.querySelector( '.js-one-click-form' );
	var error = dialog.querySelector( '.js-one-click-error' );
	var submit = dialog.querySelector( '.js-one-click-submit' );

	function showError( text ) {
		error.textContent = text;
		error.hidden = ! text;
	}

	open.addEventListener( 'click', function () {
		// Подхватываем выбранную вариацию, если размер и цвет уже выбраны.
		var variation = document.querySelector( 'input[name="variation_id"]' );
		if ( variation ) {
			form.elements.variation_id.value = variation.value || 0;
		}
		showError( '' );
		dialog.showModal();
	} );

	dialog.querySelector( '.js-one-click-close' ).addEventListener( 'click', function () {
		dialog.close();
	} );

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();

		if ( ! form.reportValidity() ) {
			return;
		}

		var data = new FormData( form );
		data.append( 'action', 'carpediem_one_click' );

		submit.disabled = true;
		showError( '' );

		fetch( dialog.dataset.ajax, { method: 'POST', body: data, credentials: 'same-origin' } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( res ) {
				if ( res && res.success ) {
					form.innerHTML = '<h2 class="one-click__title">Готово</h2><p class="one-click__text">' + res.data.message +
						'</p><button type="button" class="btn js-one-click-done">Закрыть</button>';
					form.querySelector( '.js-one-click-done' ).addEventListener( 'click', function () {
						dialog.close();
						window.location.reload();
					} );
				} else {
					showError( ( res && res.data && res.data.message ) || 'Не получилось отправить. Попробуй ещё раз.' );
					submit.disabled = false;
				}
			} )
			.catch( function () {
				showError( 'Сеть недоступна. Попробуй ещё раз.' );
				submit.disabled = false;
			} );
	} );
} )();

/* Переключатель светлой и тёмной темы. Выбор запоминается в localStorage. */
( function () {
	'use strict';

	var buttons = document.querySelectorAll( '.js-theme-toggle' );
	if ( ! buttons.length ) {
		return;
	}

	var root = document.documentElement;

	function apply( theme ) {
		root.setAttribute( 'data-theme', theme );
		buttons.forEach( function ( btn ) { btn.setAttribute( 'aria-pressed', String( theme === 'light' ) ); } );
		try {
			localStorage.setItem( 'cd-theme', theme );
		} catch ( e ) {}
	}

	apply( root.getAttribute( 'data-theme' ) === 'light' ? 'light' : 'dark' );

	buttons.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () { apply( root.getAttribute( 'data-theme' ) === 'light' ? 'dark' : 'light' ); } );
	} );
} )();

/* Избранное: список id в localStorage этого браузера. */
( function () {
	'use strict';

	var KEY = 'cd-favorites';

	function read() {
		try {
			var raw = JSON.parse( localStorage.getItem( KEY ) );
			return Array.isArray( raw ) ? raw.filter( function ( n ) { return typeof n === 'number'; } ) : [];
		} catch ( e ) {
			return [];
		}
	}

	function write( ids ) {
		try {
			localStorage.setItem( KEY, JSON.stringify( ids.slice( 0, 50 ) ) );
		} catch ( e ) {}
	}

	function paint() {
		var ids = read();
		document.querySelectorAll( '.js-fav-toggle' ).forEach( function ( toggle ) {
			var saved = ids.indexOf( Number( toggle.dataset.id ) ) > -1;
			toggle.setAttribute( 'aria-pressed', String( saved ) );
			toggle.setAttribute( 'aria-label', saved ? 'Убрать из избранного' : 'В избранное' );
			toggle.querySelector( '.js-fav-label' ).textContent = saved ? 'В избранном' : 'В избранное';
		} );
	}
	document.addEventListener( 'click', function ( e ) {
		var toggle = e.target.closest( '.js-fav-toggle' );
		if ( ! toggle ) { return; }
		var id = Number( toggle.dataset.id );
		var ids = read();
		var at = ids.indexOf( id );
		if ( at > -1 ) { ids.splice( at, 1 ); } else { ids.unshift( id ); }
		write( ids );
		paint();
		if ( page && at > -1 ) {
			var card = toggle.closest( 'li.product' );
			if ( card ) { card.remove(); }
			empty.hidden = !!list.querySelector( 'li.product' );
		}
	} );
	window.addEventListener( 'storage', function ( e ) { if ( e.key === KEY ) { paint(); } } );
	paint();

	// Страница «Избранное»
	var page = document.querySelector( '.js-favorites' );

	if ( page ) {
		var ids = read();
		var empty = page.querySelector( '.js-favorites-empty' );
		var list = page.querySelector( '.js-favorites-list' );

		if ( ids.length ) {
			var data = new FormData();
			data.append( 'action', 'carpediem_favorites' );
			ids.forEach( function ( value ) {
				data.append( 'ids[]', value );
			} );

			fetch( page.dataset.ajax, { method: 'POST', body: data, credentials: 'same-origin' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( res ) {
					if ( res && res.success && res.data.count ) {
						list.innerHTML = res.data.html;
						empty.hidden = true;
						paint();
					}
				} )
				.catch( function () {} );
		}
	}
} )();

/* Размерная сетка остаётся доступной, даже если покупатель свернул раздел. */
document.addEventListener( 'click', function ( e ) {
	if ( e.target.closest( '.size-guide-link' ) ) {
		var table = document.getElementById( 'product-sizes' );
		if ( table ) { table.open = true; }
	}
} );
