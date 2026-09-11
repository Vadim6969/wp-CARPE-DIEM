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

/* Мобильная панель каталога: фильтры и сортировка остаются доступны при прокрутке. */
( function () {
	'use strict';

	var filters = document.querySelector( '.filters' );
	var ordering = document.querySelector( '.woocommerce-ordering' );
	if ( ! filters || ! filters.parentNode ) {
		return;
	}
	var summary = filters.querySelector( '.filters__summary' );
	var toggle = null;
	var mounted = false;

	document.addEventListener( 'keydown', function ( e ) {
		if ( filters.open && e.key === 'Escape' ) {
			filters.open = false;
			if ( mounted && toggle ) {
				syncToggle();
				toggle.focus();
			} else if ( summary ) {
				summary.focus();
			}
		}
	} );
	document.addEventListener( 'click', function ( e ) {
		if ( ! mounted && filters.open && ! filters.contains( e.target ) ) {
			filters.open = false;
		}
	} );

	if ( ! ordering || ! ordering.parentNode ) {
		return;
	}

	var select = ordering.querySelector( 'select' );
	if ( ! select ) {
		return;
	}

	var media = window.matchMedia( '(max-width: 699px)' );
	var marker = document.createComment( 'catalog-ordering' );
	var toolbar = document.createElement( 'div' );
	toggle = document.createElement( 'button' );
	var initiallyOpen = filters.open;
	var originalLabels = Array.prototype.map.call( select.options, function ( option ) {
		return option.textContent;
	} );
	var shortLabels = {
		menu_order: 'По умолчанию',
		popularity: 'Популярные',
		rating: 'По рейтингу',
		date: 'Новинки',
		price: 'Сначала дешевле',
		'price-desc': 'Сначала дороже'
	};

	ordering.parentNode.insertBefore( marker, ordering );
	toolbar.className = 'catalog-mobile-toolbar';
	toolbar.setAttribute( 'role', 'group' );
	toolbar.setAttribute( 'aria-label', 'Управление каталогом' );
	toggle.className = 'catalog-mobile-toolbar__filters';
	toggle.type = 'button';
	toggle.setAttribute( 'aria-controls', filters.id );
	select.setAttribute( 'aria-label', 'Сортировка товаров' );
	toolbar.appendChild( toggle );

	function syncToggle() {
		var active = filters.getAttribute( 'data-active' ) === 'true';
		toggle.textContent = 'Фильтры';
		toggle.classList.toggle( 'has-active-filters', active );
		toggle.setAttribute( 'aria-label', active ? 'Фильтры, есть выбранные параметры' : 'Фильтры' );
		toggle.setAttribute( 'aria-expanded', String( filters.open ) );
	}

	function setShortLabels( short ) {
		Array.prototype.forEach.call( select.options, function ( option, index ) {
			option.textContent = short && shortLabels[ option.value ] ? shortLabels[ option.value ] : originalLabels[ index ];
		} );
	}

	function mount() {
		if ( media.matches && ! mounted ) {
			filters.open = false;
			filters.classList.add( 'is-mobile-enhanced' );
			filters.parentNode.insertBefore( toolbar, filters );
			toolbar.appendChild( ordering );
			setShortLabels( true );
			mounted = true;
		} else if ( ! media.matches && mounted ) {
			filters.classList.remove( 'is-mobile-enhanced' );
			filters.open = initiallyOpen;
			marker.parentNode.insertBefore( ordering, marker.nextSibling );
			toolbar.remove();
			setShortLabels( false );
			mounted = false;
		}
		syncToggle();
	}

	toggle.addEventListener( 'click', function () {
		filters.open = ! filters.open;
		if ( filters.open ) {
			window.requestAnimationFrame( function () {
				filters.scrollIntoView( {
					block: 'start',
					behavior: window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 'auto' : 'smooth'
				} );
			} );
		}
		syncToggle();
	} );

	filters.addEventListener( 'toggle', syncToggle );

	if ( media.addEventListener ) {
		media.addEventListener( 'change', mount );
	} else {
		media.addListener( mount );
	}
	mount();
} )();

/* Доступное состояние покупки и компактная sticky-кнопка на мобильной карточке товара. */
( function () {
	'use strict';

	var addButton = document.querySelector( '.single-product .single_add_to_cart_button' );
	if ( ! addButton ) {
		return;
	}

	var media = window.matchMedia( '(max-width: 699px)' );
	var header = document.querySelector( '.js-header' );
	var endSection = document.querySelector( '.usp' );
	var purchaseForm = addButton.closest( 'form' );
	var productNameSource = document.querySelector( '.single-product .product_title' );
	var summaryPrice = document.querySelector( '.single-product .summary > .price' );
	var variationWrap = document.querySelector( '.single_variation_wrap' );
	var bar = document.createElement( 'aside' );
	var meta = document.createElement( 'span' );
	var name = document.createElement( 'span' );
	var price = document.createElement( 'strong' );
	var stickyButton = document.createElement( 'button' );
	var scheduled = false;

	bar.className = 'sticky-purchase';
	bar.hidden = true;
	bar.setAttribute( 'aria-label', 'Быстрая покупка' );
	meta.className = 'sticky-purchase__meta';
	name.className = 'sticky-purchase__name';
	price.className = 'sticky-purchase__price';
	price.setAttribute( 'aria-live', 'polite' );
	stickyButton.className = 'btn btn--primary sticky-purchase__button';
	stickyButton.type = 'button';
	stickyButton.textContent = addButton.textContent.trim();
	name.textContent = productNameSource ? productNameSource.textContent.trim() : document.title;
	meta.appendChild( name );
	meta.appendChild( price );
	bar.appendChild( meta );
	bar.appendChild( stickyButton );
	document.body.appendChild( bar );

	function isUnavailable() {
		return addButton.classList.contains( 'disabled' ) || addButton.classList.contains( 'wc-variation-selection-needed' );
	}

	function syncPrice() {
		var variationPrice = document.querySelector( '.single_variation .woocommerce-variation-price .price' );
		var source = variationPrice && variationPrice.textContent.trim() ? variationPrice : summaryPrice;
		price.textContent = source ? source.textContent.trim().replace( /\s+/g, ' ' ) : '';
	}

	function sync() {
		scheduled = false;
		var unavailable = isUnavailable();
		var headerBottom = header ? header.getBoundingClientRect().bottom : 0;
		var passedMainButton = addButton.getBoundingClientRect().bottom <= headerBottom + 8;
		var reachedEnd = endSection && endSection.getBoundingClientRect().top < window.innerHeight;
		var dialogOpen = Boolean( document.querySelector( 'dialog[open]' ) );
		var visible = media.matches && ! unavailable && passedMainButton && ! reachedEnd && ! dialogOpen;

		addButton.disabled = unavailable;
		addButton.setAttribute( 'aria-disabled', String( unavailable ) );
		stickyButton.disabled = unavailable;
		bar.hidden = ! visible;
		document.body.classList.toggle( 'sticky-purchase-visible', visible );
		syncPrice();
	}

	function scheduleSync() {
		if ( scheduled ) {
			return;
		}
		scheduled = true;
		window.requestAnimationFrame( sync );
	}

	stickyButton.addEventListener( 'click', function () {
		if ( ! stickyButton.disabled ) {
			addButton.click();
		}
	} );
	window.addEventListener( 'scroll', scheduleSync, { passive: true } );
	window.addEventListener( 'resize', scheduleSync );
	if ( purchaseForm ) {
		purchaseForm.addEventListener( 'change', scheduleSync );
	}

	new MutationObserver( scheduleSync ).observe( addButton, { attributes: true, attributeFilter: [ 'class' ] } );
	if ( variationWrap ) {
		new MutationObserver( scheduleSync ).observe( variationWrap, { childList: true, subtree: true } );
	}
	Array.prototype.forEach.call( document.querySelectorAll( 'dialog' ), function ( dialog ) {
		new MutationObserver( scheduleSync ).observe( dialog, { attributes: true, attributeFilter: [ 'open' ] } );
	} );

	if ( window.jQuery && purchaseForm ) {
		window.jQuery( purchaseForm ).on( 'found_variation show_variation hide_variation reset_data woocommerce_variation_has_changed', scheduleSync );
	}
	if ( media.addEventListener ) {
		media.addEventListener( 'change', scheduleSync );
	} else {
		media.addListener( scheduleSync );
	}
	sync();
} )();

/* Для PhotoSwipe достаточно aria-haspopup и доступного имени; aria-controls даёт ложный axe incomplete. */
( function () {
	'use strict';
	var attributeObserver;
	var discoveryObserver;
	var bind = function () {
		var trigger = document.querySelector( '.woocommerce-product-gallery__trigger' );
		if ( ! trigger ) {
			return false;
		}
		var cleanControls = function () {
			if ( trigger.hasAttribute( 'aria-controls' ) ) {
				trigger.removeAttribute( 'aria-controls' );
			}
		};
		if ( ! attributeObserver ) {
			attributeObserver = new MutationObserver( cleanControls );
			attributeObserver.observe( trigger, { attributes: true, attributeFilter: [ 'aria-controls' ] } );
		}
		cleanControls();
		return true;
	};

	if ( ! bind() ) {
		discoveryObserver = new MutationObserver( function () {
			if ( bind() ) {
				discoveryObserver.disconnect();
			}
		} );
		discoveryObserver.observe( document.body, { childList: true, subtree: true } );
	}
	window.addEventListener( 'load', bind );
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
