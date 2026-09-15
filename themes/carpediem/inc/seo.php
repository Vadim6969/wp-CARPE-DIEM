<?php
/**
 * SEO-минимум своими силами: описание, Open Graph, фавикон, noindex служебных страниц.
 *
 * Микроразметку Product/Offer/Breadcrumb даёт сам WooCommerce, канониклы и sitemap.xml — ядро WP.
 * Если понадобится править title/description вручную для каждой страницы — ставим Rank Math,
 * этот файл тогда отключаем.
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

/**
 * Описание страницы: 160 символов без разметки.
 */
function carpediem_meta_description() {
	$text = '';

	if ( is_front_page() ) {
		$text = get_bloginfo( 'description' ) . '. Одежда и аксессуары для тех, кто ценит стиль, свободу и смысл.';
	} elseif ( is_product() ) {
		$product = wc_get_product( get_the_ID() );
		$text    = $product ? ( $product->get_short_description() ?: $product->get_description() ) : '';
	} elseif ( is_product_category() || is_product_tag() || is_category() || is_tag() ) {
		$text = term_description();
	} elseif ( is_singular() ) {
		$post = get_post();
		$text = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	}

	$text = trim( wp_strip_all_tags( strip_shortcodes( (string) $text ) ) );

	if ( '' === $text ) {
		$text = get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' );
	}

	return wp_trim_words( $text, 28, '…' );
}

/**
 * Картинка для соцсетей: фото товара или записи, иначе фирменная заглушка.
 */
function carpediem_share_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		if ( $image ) {
			return $image[0];
		}
	}

	return get_theme_file_uri( 'assets/img/og-default.png' );
}

add_action( 'wp_head', function () {
	$title = wp_get_document_title();
	$desc  = carpediem_meta_description();
	$url   = home_url( add_query_arg( array() ) );
	$image = carpediem_share_image();
	$type  = is_singular() && ! is_front_page() ? 'article' : 'website';

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );

	if ( is_product() ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			printf( '<meta property="product:price:amount" content="%s">' . "\n", esc_attr( $product->get_price() ) );
			printf( '<meta property="product:price:currency" content="%s">' . "\n", esc_attr( get_woocommerce_currency() ) );
		}
	}
}, 5 );

// Корзину, оформление, аккаунт и результаты поиска в индекс не пускаем.
add_filter( 'wp_robots', function ( $robots ) {
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) || is_search() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'] );
	}

	return $robots;
} );
