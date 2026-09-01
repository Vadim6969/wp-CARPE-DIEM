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
