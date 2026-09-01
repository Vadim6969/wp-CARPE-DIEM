<?php
/**
 * Все правки WooCommerce. Правило: сначала хук здесь, копия шаблона в /woocommerce — только если хуком не выходит.
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Общее ---------- */

// Тёмная заглушка вместо светлой картинки Woo (пока нет фото товаров).
add_filter( 'woocommerce_placeholder_img_src', function () {
	return get_theme_file_uri( 'assets/img/placeholder.svg' );
} );

// Woo отдаёт готовый <img> из вложения-заглушки, минуя фильтр src — подменяем разметку целиком.
add_filter( 'woocommerce_placeholder_img', function ( $html, $size ) {
	return sprintf(
		'<img src="%s" alt="" class="woocommerce-placeholder wp-post-image" width="600" height="800" loading="lazy" />',
		esc_url( get_theme_file_uri( 'assets/img/placeholder.svg' ) )
	);
}, 10, 2 );

// Woo-раскладка (float'ы, 48% ширины галереи, колонки чекаута) конфликтует с нашей сеткой — выключаем.
// Оставляем только woocommerce-general.css с базовыми стилями форм и уведомлений.
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'woocommerce-layout' );
	wp_dequeue_style( 'woocommerce-smallscreen' );
}, 20 );

// Каталог: 12 товаров, 4 колонки, без сайдбара.
add_filter( 'loop_shop_per_page', fn() => 12, 20 );
add_filter( 'loop_shop_columns', fn() => 4, 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Свой контейнер вокруг контента Woo вместо дефолтной обёртки.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', function () {
	echo '<div class="container woo-content">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div>';
}, 10 );

// Разделитель хлебных крошек.
add_filter( 'woocommerce_breadcrumb_defaults', function ( $args ) {
	$args['delimiter']   = ' <span class="crumbs__sep">/</span> ';
	$args['wrap_before'] = '<nav class="crumbs">';
	$args['wrap_after']  = '</nav>';

	return $args;
} );

/* ---------- Карточка в сетке ---------- */

// Кнопку «в корзину» из сетки убираем — покупка идёт со страницы товара (нужен размер).
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Над названием — категория, как на макете.
add_action( 'woocommerce_shop_loop_item_title', function () {
	global $product;
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		echo '<span class="loop-card__cat">' . esc_html( $terms[0]->name ) . '</span>';
	}
}, 5 );

// Бейдж «Новинка» на карточке и на странице товара.
function carpediem_new_badge() {
	global $product;
	if ( $product && has_term( 'новинка', 'product_tag', $product->get_id() ) ) {
		echo '<span class="badge-new">Новинка</span>';
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'carpediem_new_badge', 15 );

/* ---------- Страница товара ---------- */

add_action( 'wp', function () {
	if ( ! is_product() ) {
		return;
	}

	// Правая колонка: бейдж → название → цена → форма (цвет/размер/в корзину) → особенности → поделиться.
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

	add_action( 'woocommerce_single_product_summary', 'carpediem_new_badge', 4 );
	add_action( 'woocommerce_single_product_summary', 'carpediem_product_features', 35 );
	add_action( 'woocommerce_single_product_summary', 'carpediem_product_actions', 45 );

	// Короткое описание — подписью под галереей.
	add_action( 'woocommerce_product_thumbnails', 'carpediem_gallery_caption', 20 );

	// Вкладки Woo заменяем на три колонки: описание / материалы и уход / размеры.
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	add_action( 'woocommerce_after_single_product_summary', 'carpediem_product_info', 10 );
	add_action( 'woocommerce_after_single_product_summary', 'carpediem_cross_sells', 20 );
} );

/** Подпись под галереей (короткое описание товара). */
function carpediem_gallery_caption() {
	global $product;
	$text = $product->get_short_description();
	if ( $text ) {
		echo '<p class="gallery-caption"><span aria-hidden="true">&#10015;</span> ' . esc_html( wp_strip_all_tags( $text ) ) . '</p>';
	}
}

/** Список особенностей — из атрибута «Особенности». */
function carpediem_product_features() {
	global $product;
	$values = wc_get_product_terms( $product->get_id(), 'pa_features', array( 'fields' => 'names' ) );
	if ( ! $values ) {
		return;
	}
	echo '<ul class="features">';
	foreach ( $values as $value ) {
		echo '<li><span class="features__cross" aria-hidden="true">&#10015;</span>' . esc_html( $value ) . '</li>';
	}
	echo '</ul>';
}

/** Строка «В избранное / Поделиться». */
function carpediem_product_actions() {
	global $product;
	printf(
		'<div class="prod-actions">
			<button class="prod-actions__btn" type="button" disabled title="Появится позже">%s В избранное</button>
			<a class="prod-actions__btn" href="%s" target="_blank" rel="noopener">%s Поделиться</a>
		</div>',
		'<span aria-hidden="true">&#9825;</span>',
		esc_url( 'https://t.me/share/url?url=' . rawurlencode( get_permalink( $product->get_id() ) ) ),
		'<span aria-hidden="true">&#8599;</span>'
	);
}

/** Три информационные колонки под товаром. */
function carpediem_product_info() {
	get_template_part( 'template-parts/product-info' );
}

/** «С этим товаром покупают» — перекрёстные продажи. */
function carpediem_cross_sells() {
	global $product;
	$ids = $product->get_cross_sell_ids();
	if ( ! $ids ) {
		return;
	}

	$query = new WP_Query( array(
		'post_type'      => 'product',
		'post__in'       => $ids,
		'orderby'        => 'post__in',
		'posts_per_page' => 8,
		'no_found_rows'  => true,
	) );

	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<section class="section cross-sells">
		<div class="container">
			<h2 class="section-title">С этим товаром покупают</h2>
			<div class="products-scroller">
				<ul class="products columns-5">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					?>
				</ul>
			</div>
		</div>
	</section>
	<?php
}

/* ---------- Уход и размеры ---------- */

/** Иконка для значения атрибута «Уход». Нет иконки — вернём пустую строку, текст останется. */
function carpediem_care_icon( $value ) {
	$icons = array(
		'стирка'    => '<path d="M4 7h16v13H4z"/><circle cx="12" cy="14" r="4"/><path d="M4 7l2-3h12l2 3"/>',
		'отбелив'   => '<path d="M12 3l9 17H3z"/>',
		'сушить'    => '<path d="M4 6h16v12H4z"/><path d="M8 6v12"/>',
		'гладить'   => '<path d="M3 16h18l-2-6H8l-5 6z"/><path d="M8 10V7h8"/>',
		'химчистк'  => '<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>',
	);

	foreach ( $icons as $needle => $path ) {
		if ( false !== mb_stripos( $value, $needle ) ) {
			return '<svg class="care__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">' . $path . '</svg>';
		}
	}

	return '';
}

/**
 * Размерная сетка по слагу категории. Пусто — блок не выводится.
 * Значения в сантиметрах, меняются здесь.
 */
function carpediem_size_table( $product ) {
	$tables = array(
		'hoodie'     => array(
			'head' => array( 'Размер', 'Длина', 'Грудь', 'Плечи', 'Рукав' ),
			'rows' => array(
				array( 'XS', 66, 112, 55, 60 ), array( 'S', 68, 116, 57, 61 ), array( 'M', 70, 120, 59, 62 ),
				array( 'L', 72, 124, 61, 63 ), array( 'XL', 74, 128, 63, 64 ), array( 'XXL', 76, 132, 65, 65 ),
			),
		),
		'sweatshirt' => array(
			'head' => array( 'Размер', 'Длина', 'Грудь', 'Плечи', 'Рукав' ),
			'rows' => array(
				array( 'XS', 64, 110, 54, 59 ), array( 'S', 66, 114, 56, 60 ), array( 'M', 68, 118, 58, 61 ),
				array( 'L', 70, 122, 60, 62 ), array( 'XL', 72, 126, 62, 63 ), array( 'XXL', 74, 130, 64, 64 ),
			),
		),
		'tshirt'     => array(
			'head' => array( 'Размер', 'Длина', 'Грудь', 'Плечи' ),
			'rows' => array(
				array( 'XS', 68, 104, 50 ), array( 'S', 70, 108, 52 ), array( 'M', 72, 112, 54 ),
				array( 'L', 74, 116, 56 ), array( 'XL', 76, 120, 58 ), array( 'XXL', 78, 124, 60 ),
			),
		),
	);

	$slugs = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) );

	foreach ( (array) $slugs as $slug ) {
		if ( isset( $tables[ $slug ] ) ) {
			return $tables[ $slug ];
		}
	}

	return null;
}
