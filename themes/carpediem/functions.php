<?php
/**
 * Carpe Diem 2026 — тема магазина.
 */

defined( 'ABSPATH' ) || exit;

const CARPEDIEM_VERSION = '0.1.0';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	// WooCommerce + нативная галерея товара (zoom / lightbox / slider).
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary'        => 'Верхнее меню',
		'footer-info'    => 'Футер: Информация',
		'footer-support' => 'Футер: Поддержка',
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'carpediem', get_theme_file_uri( 'assets/css/main.css' ), array(), CARPEDIEM_VERSION );
} );

// Количество товаров в корзине для шапки (обновляется AJAX-фрагментами Woo).
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	ob_start();
	get_template_part( 'template-parts/cart-count' );
	$fragments['.js-cart-count'] = ob_get_clean();

	return $fragments;
} );
