<?php
/**
 * Carpe Diem 2026 — тема магазина.
 */

defined( 'ABSPATH' ) || exit;

const CARPEDIEM_VERSION = '0.4.0';

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
		'https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500&display=swap',
		array(),
		null
	);
	// Версия по времени правки файла: не думаем о кэше ни в разработке, ни при деплое.
	wp_enqueue_style( 'carpediem', get_theme_file_uri( 'assets/css/main.css' ), array( 'carpediem-fonts' ), carpediem_asset_version( 'assets/css/main.css' ) );
	wp_enqueue_script( 'carpediem', get_theme_file_uri( 'assets/js/main.js' ), array( 'jquery' ), carpediem_asset_version( 'assets/js/main.js' ), true );
	wp_localize_script( 'carpediem', 'carpediemUI', array(
		'colors' => carpediem_color_swatches(),
		'quickOrderSuccessTitle' => carpediem_setting( 'quick_order_success_title' ),
		'quickOrderCloseLabel' => carpediem_setting( 'quick_order_close_label' ),
		'quickOrderGenericError' => carpediem_setting( 'quick_order_generic_error' ),
		'quickOrderNetworkError' => carpediem_setting( 'quick_order_network_error' ),
	) );
	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
} );

add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

	// Тема применяется до первой отрисовки, иначе при светлой теме мелькнёт тёмный фон.
	echo '<script>(function(){try{var t=localStorage.getItem("cd-theme");if(t==="light"||t==="dark"){document.documentElement.setAttribute("data-theme",t);}}catch(e){}})();</script>' . "\n";
}, 1 );

// Скрипты эмодзи WP на этом сайте не нужны — минус два запроса на каждой странице.
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
} );

// Количество товаров в корзине для шапки (обновляется AJAX-фрагментами Woo).
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	ob_start();
	get_template_part( 'template-parts/cart-count' );
	$fragments['.js-cart-count'] = ob_get_clean();

	return $fragments;
} );

require get_theme_file_path( 'inc/settings.php' );
require get_theme_file_path( 'inc/admin.php' );
require get_theme_file_path( 'inc/woo.php' );
require get_theme_file_path( 'inc/one-click.php' );
require get_theme_file_path( 'inc/favorites.php' );
require get_theme_file_path( 'inc/seo.php' );

/**
 * Версия ассета = время последней правки файла.
 */
function carpediem_asset_version( $relative_path ) {
	$file = get_theme_file_path( $relative_path );

	return file_exists( $file ) ? (string) filemtime( $file ) : CARPEDIEM_VERSION;
}

/**
 * Иконки интерфейса. Возвращает готовый inline-SVG.
 */
function carpediem_icon( $name, $size = 16 ) {
	$paths = array(
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
		'sun'       => '<circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19"/>',
		'moon'      => '<path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/>',
		'demon'     => '<path d="M5 4c.2 3.6 1.6 5.6 4.1 6.6M19 4c-.2 3.6-1.6 5.6-4.1 6.6"/><path d="M6.5 7.2C4.3 9.5 4 13.8 5.8 17A7.1 7.1 0 0 0 12 20.5a7.1 7.1 0 0 0 6.2-3.5c1.8-3.2 1.5-7.5-.7-9.8"/><path d="M9 14.5c.8.7 1.8 1 3 1s2.2-.3 3-1"/>',
		'angel'     => '<ellipse cx="12" cy="5" rx="5.5" ry="2.2"/><path d="M12 8v12M8.5 11.5 12 8l3.5 3.5M7 20l5-5 5 5"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>',
		'cart'      => '<path d="M6 8h12l-1 12H7z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'heart'     => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1" fill="currentColor" stroke="none"/>',
		'telegram'  => '<path d="M21 5L3 12l5 2 2 5 3-4 5 4z"/><path d="M8 14l10-9"/>',
		'tiktok'    => '<path d="M14 4v10.5a4.5 4.5 0 1 1-3.8-4.45"/><path d="M14 4c.7 2.5 2.2 4 5 4.5"/>',
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
 * Доступный переключатель двух визуальных тем. Оба экземпляра в шапке
 * синхронизируются через main.js и сохраняют прежние значения dark/light.
 */
function carpediem_theme_toggle( $modifier = '' ) {
	$dark  = carpediem_setting( 'theme_dark_label' ) ?: 'Демон';
	$light = carpediem_setting( 'theme_light_label' ) ?: 'Ангел';
	$class = trim( 'theme-toggle js-theme-toggle ' . $modifier );
	?>
	<button class="<?php echo esc_attr( $class ); ?>" type="button" role="switch" aria-checked="false" aria-label="<?php echo esc_attr( sprintf( 'Включить тему «%s»', $light ) ); ?>" data-dark-label="<?php echo esc_attr( $dark ); ?>" data-light-label="<?php echo esc_attr( $light ); ?>">
		<span class="theme-toggle__track" aria-hidden="true">
			<span class="theme-toggle__choice theme-toggle__choice--demon"><?php echo carpediem_icon( 'demon', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span class="theme-toggle__choice theme-toggle__choice--angel"><?php echo carpediem_icon( 'angel', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span class="theme-toggle__thumb"></span>
		</span>
		<span class="screen-reader-text js-theme-status"><?php echo esc_html( sprintf( 'Тема «%s»', $dark ) ); ?></span>
	</button>
	<?php
}

/** Ссылки на соцсети, заполненные в панели магазина. */
function carpediem_socials() {
	return array_filter( array(
		'instagram' => carpediem_setting( 'instagram' ),
		'telegram'  => carpediem_setting( 'telegram' ),
		'tiktok'    => carpediem_setting( 'tiktok' ),
		'vk'        => carpediem_setting( 'vk' ),
	) );
}

/** Убираем из футера устаревшие ссылки, сохраняя меню редактируемым для будущих документов. */
add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
	if ( 'footer-support' !== ( $args->theme_location ?? '' ) ) {
		return $items;
	}

	$hidden_slugs = array( 'faq', 'care', 'partnership' );

	return array_values( array_filter( $items, function ( $item ) use ( $hidden_slugs ) {
		$path = untrailingslashit( (string) wp_parse_url( $item->url, PHP_URL_PATH ) );
		return ! in_array( basename( $path ), $hidden_slugs, true );
	} ) );
}, 10, 2 );
