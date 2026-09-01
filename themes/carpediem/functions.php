<?php
/**
 * Carpe Diem 2026 — тема магазина.
 */

defined( 'ABSPATH' ) || exit;

const CARPEDIEM_VERSION = '0.2.0';

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
	// ponytail: шрифты с Google CDN. Перед запуском положить .woff2 в assets/fonts/ и раздавать со своего домена.
	wp_enqueue_style(
		'carpediem-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Manrope:wght@200;300;400;500&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'carpediem', get_theme_file_uri( 'assets/css/main.css' ), array( 'carpediem-fonts' ), CARPEDIEM_VERSION );
	wp_enqueue_script( 'carpediem', get_theme_file_uri( 'assets/js/main.js' ), array(), CARPEDIEM_VERSION, true );
} );

add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

// Количество товаров в корзине для шапки (обновляется AJAX-фрагментами Woo).
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	ob_start();
	get_template_part( 'template-parts/cart-count' );
	$fragments['.js-cart-count'] = ob_get_clean();

	return $fragments;
} );

/**
 * Иконки интерфейса. Возвращает готовый inline-SVG.
 */
function carpediem_icon( $name, $size = 16 ) {
	$paths = array(
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>',
		'cart'      => '<path d="M6 8h12l-1 12H7z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1" fill="currentColor" stroke="none"/>',
		'telegram'  => '<path d="M21 5L3 12l5 2 2 5 3-4 5 4z"/><path d="M8 14l10-9"/>',
		'vk'        => '<path d="M4 8h3c.6 3.4 2 5.6 3.5 6.3V8h3v3.7c1.4-.2 2.7-1.7 3.2-3.7h3c-.5 2.3-1.7 3.9-2.9 4.8 1.3.9 2.4 2.3 3 5.2h-3.3c-.5-2-1.6-3.3-3-3.6V18H11C7.4 18 4.8 14.4 4 8z"/>',
	);

	if ( empty( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="icon icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Ссылки на соцсети. Меняются здесь, пока их не нужно править из админки.
 */
function carpediem_socials() {
	return array(
		'instagram' => '#',
		'telegram'  => '#',
		'vk'        => '#',
	);
}
