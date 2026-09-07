<?php
/**
 * Избранное без плагина и без базы: список id живёт в localStorage браузера,
 * страница «Избранное» подгружает карточки по этим id.
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

/**
 * Кнопка на странице товара (подменяет заглушку в prod-actions).
 */
function carpediem_favorite_button( $product_id, $compact = false ) {
	printf(
		'<button class="prod-actions__btn js-fav-toggle %2$s" type="button" data-id="%1$d" aria-pressed="false" aria-label="В избранное">
			<span class="js-fav-icon" aria-hidden="true">%4$s</span> <span class="js-fav-label %3$s">В избранное</span>
		</button>',
		(int) $product_id,
		$compact ? 'loop-favorite' : '',
		$compact ? 'screen-reader-text' : '',
		carpediem_icon( 'heart', 20 )
	);
}

/**
 * Страница «Избранное»: контейнер, который наполняет JS.
 */
add_shortcode( 'carpediem_favorites', function () {
	ob_start();
	?>
	<div class="woocommerce favorites js-favorites" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<p class="favorites__empty js-favorites-empty">
			Здесь пока пусто. Нажмите «В избранное» на странице вещи — список сохранится в этом браузере.
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Перейти в каталог</a>
		</p>
		<div class="js-favorites-list"></div>
	</div>
	<?php

	return ob_get_clean();
} );

// Страница избранного шире колонки текста — на ней сетка карточек.
add_filter( 'body_class', function ( $classes ) {
	if ( is_page( 'favorites' ) ) {
		$classes[] = 'page-favorites';
	}

	return $classes;
} );

/**
 * AJAX: отдаём карточки товаров по списку id.
 * Данные публичные (каталог), поэтому проверяем только сами id.
 */
add_action( 'wp_ajax_carpediem_favorites', 'carpediem_favorites_handler' );
add_action( 'wp_ajax_nopriv_carpediem_favorites', 'carpediem_favorites_handler' );

function carpediem_favorites_handler() {
	$ids = isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ? array_map( 'absint', wp_unslash( $_POST['ids'] ) ) : array();
	$ids = array_slice( array_filter( array_unique( $ids ) ), 0, 50 );

	if ( ! $ids ) {
		wp_send_json_success( array( 'html' => '', 'count' => 0 ) );
	}

	$query = new WP_Query( array(
		'post_type'           => 'product',
		'post__in'            => $ids,
		'orderby'             => 'post__in',
		'posts_per_page'      => count( $ids ),
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'exclude-from-catalog',
				'operator' => 'NOT IN',
			),
		),
	) );

	ob_start();

	if ( $query->have_posts() ) {
		echo '<ul class="products columns-4">';
		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}
		echo '</ul>';
		wp_reset_postdata();
	}

	wp_send_json_success( array(
		'html'  => ob_get_clean(),
		'count' => $query->post_count,
	) );
}
