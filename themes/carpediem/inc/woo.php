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
// Итоги уже выведены в правой колонке нашего шаблона корзины.
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

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

// Название вариации без «— M, Чёрный»: атрибуты показываем отдельными строками.
add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );

/* ---------- Карточка в сетке ---------- */

// Стандартную кнопку Woo заменяем своей: она учитывает необходимость выбора вариации.
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

// После закрывающей ссылки: кнопка избранного не вложена в ссылку товара.
add_action( 'woocommerce_after_shop_loop_item', function () {
	global $product;
	carpediem_favorite_button( $product->get_id(), true );
	if ( ! $product->is_type( 'variable' ) ) { return; }
	$sizes = array();
	foreach ( $product->get_available_variations( 'objects' ) as $variation ) {
		if ( ! $variation->is_in_stock() || ! $variation->is_purchasable() ) { continue; }
		$slug = $variation->get_attribute( 'pa_size' );
		if ( $slug ) { $sizes[] = $slug; }
	}
	if ( $sizes ) { echo '<p class="loop-sizes"><span class="screen-reader-text">Доступные размеры: </span>' . esc_html( implode( ' · ', array_unique( $sizes ) ) ) . '</p>'; }
}, 15 );

/**
 * Понятное действие в каждой карточке каталога.
 * Простой товар добавляется сразу, для вариативного сначала открывается выбор размера/цвета.
 */
function carpediem_loop_purchase_button() {
	global $product;

	if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
		return;
	}

	$name = wp_strip_all_tags( $product->get_name() );

	if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
		$classes = array( 'button', 'btn', 'btn--primary', 'loop-buy', 'product_type_simple', 'add_to_cart_button' );
		if ( $product->supports( 'ajax_add_to_cart' ) ) {
			$classes[] = 'ajax_add_to_cart';
		}

		printf(
			'<a href="%1$s" data-quantity="1" class="%2$s" data-product_id="%3$d" data-product_sku="%4$s" aria-label="%5$s" rel="nofollow"><span>В корзину</span><span aria-hidden="true">+</span></a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( implode( ' ', $classes ) ),
			absint( $product->get_id() ),
			esc_attr( $product->get_sku() ),
			esc_attr( sprintf( 'Добавить «%s» в корзину', $name ) )
		);
		return;
	}

	$available = $product->is_purchasable() && $product->is_in_stock();
	$url       = $product->get_permalink() . ( $available ? '#product-buy' : '' );
	$label     = $available ? 'Купить' : 'Подробнее';
	$aria      = $available ? sprintf( 'Выбрать параметры и купить «%s»', $name ) : sprintf( 'Подробнее о товаре «%s»', $name );

	printf(
		'<a class="button btn btn--primary loop-buy loop-buy--select" href="%1$s" aria-label="%2$s"><span>%3$s</span><span aria-hidden="true">→</span></a>',
		esc_url( $url ),
		esc_attr( $aria ),
		esc_html( $label )
	);
}
add_action( 'woocommerce_after_shop_loop_item', 'carpediem_loop_purchase_button', 25 );

add_action( 'woocommerce_before_variations_form', function () {
	global $product;
	if ( carpediem_size_table( $product ) ) { echo '<a class="size-guide-link" href="#product-sizes">Таблица размеров ↗</a>'; }
} );

add_action( 'woocommerce_single_product_summary', function () {
	echo '<div class="product-service">';
	foreach ( array( 'delivery' => array( 'Доставка', 'delivery_note' ), 'returns' => array( 'Обмен и возврат', 'returns_note' ) ) as $path => $item ) {
		if ( carpediem_setting( $item[1] ) ) {
			echo '<a href="' . esc_url( home_url( '/' . $path . '/' ) ) . '"><strong>' . esc_html( $item[0] ) . ' ↗</strong><span>' . esc_html( carpediem_setting( $item[1] ) ) . '</span></a>';
		}
	}
	echo '</div>';
}, 32 );

add_action( 'woocommerce_archive_description', function () {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'exclude' => array( get_option( 'default_product_cat' ) ) ) );
	if ( is_wp_error( $terms ) || ! $terms ) { return; }
	echo '<nav class="catalog-categories" aria-label="Категории товаров"><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '"' . ( is_shop() ? ' aria-current="page"' : '' ) . '>Все вещи</a>';
	foreach ( $terms as $term ) {
		echo '<a href="' . esc_url( get_term_link( $term ) ) . '"' . ( is_product_category( $term->slug ) ? ' aria-current="page"' : '' ) . '>' . esc_html( $term->name ) . '</a>';
	}
	echo '</nav>';
}, 20 );

// Существующее меню сохраняется в WordPress, исправляется только дублирующая ссылка.
add_filter( 'nav_menu_link_attributes', function ( $attrs, $item, $args ) {
	if ( 'primary' === ( $args->theme_location ?? '' ) && 'COLLECTIONS' === strtoupper( $item->title ) && untrailingslashit( (string) wp_parse_url( $item->url, PHP_URL_PATH ) ) === untrailingslashit( (string) wp_parse_url( home_url( '/catalog/' ), PHP_URL_PATH ) ) ) {
		$attrs['href'] = home_url( carpediem_home_categories() ? '/#collections' : '/#selection' );
	}
	return $attrs;
}, 10, 3 );

/* ---------- Страница товара ---------- */

// Якорь для кнопки «Купить» из каталога — сразу к выбору параметров и действиям.
add_action( 'woocommerce_before_add_to_cart_form', function () {
	echo '<span id="product-buy" class="product-buy-anchor" aria-hidden="true"></span>';
}, 5 );

/**
 * «Купить сейчас» оформляет выбранный вариант без промежуточного визита в корзину.
 * Сама корзина не очищается: уже выбранные покупателем товары остаются в заказе.
 */
function carpediem_buy_now_button() {
	global $product;

	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}

	$action   = add_query_arg( 'buy-now', '1', $product->get_permalink() );
	$disabled = $product->is_type( 'variable' ) ? ' disabled aria-disabled="true"' : '';

	printf(
		'<button type="submit" name="add-to-cart" value="%1$d" formaction="%2$s" class="button alt btn btn--primary carpediem-buy-now"%3$s>Купить сейчас</button>',
		absint( $product->get_id() ),
		esc_url( $action ),
		$disabled // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- статический набор атрибутов.
	);
}
add_action( 'woocommerce_before_add_to_cart_button', 'carpediem_buy_now_button', 5 );

add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	$buy_now = isset( $_REQUEST['buy-now'] ) ? wc_clean( wp_unslash( $_REQUEST['buy-now'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- флаг навигации, данные не меняет.

	if ( '1' === $buy_now ) {
		wc_clear_notices();
		return wc_get_checkout_url();
	}

	return $url;
} );

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

	echo '<div class="prod-actions">';
	carpediem_favorite_button( $product->get_id() );
	printf(
		'<a class="prod-actions__btn" href="%s" target="_blank" rel="noopener"><span aria-hidden="true">&#8599;</span> Поделиться</a>',
		esc_url( 'https://t.me/share/url?url=' . rawurlencode( get_permalink( $product->get_id() ) ) )
	);
	echo '</div>';
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

/* ---------- Оформление заказа ---------- */

// Короткое пояснение перед формой: checkout остаётся гостевым и помещается на одной странице.
add_action( 'woocommerce_before_checkout_form', function () {
	if ( is_wc_endpoint_url( 'order-received' ) || ! WC()->cart ) {
		return;
	}
	?>
	<section class="checkout-fast-intro" aria-label="Быстрое оформление заказа">
		<div>
			<span class="checkout-fast-intro__eyebrow">Быстрое оформление</span>
			<strong>Без регистрации</strong>
			<p>Контакты, доставка и подтверждение — на одной странице.</p>
		</div>
		<a href="#order_review"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?> шт. · <?php echo wp_kses_post( WC()->cart->get_total() ); ?></a>
	</section>
	<?php
}, 5 );

// Согласие на обработку персональных данных (152-ФЗ) — обязательная галочка.
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	$privacy_url = get_privacy_policy_url();
	$label       = '<span class="consent-row__copy">Согласен на обработку персональных данных';

	if ( $privacy_url ) {
		$label .= sprintf( ' (<a href="%s" target="_blank" rel="noopener">политика конфиденциальности</a>)', esc_url( $privacy_url ) );
	}
	$label .= '</span>';

	// Оставляем только поля, без которых нельзя связаться с покупателем и доставить заказ.
	foreach ( array( 'billing_last_name', 'billing_company', 'billing_address_2', 'billing_state', 'billing_postcode' ) as $key ) {
		unset( $fields['billing'][ $key ] );
	}
	unset( $fields['order']['order_comments'] );

	$fields['billing']['billing_country']['type']     = 'hidden';
	$fields['billing']['billing_country']['label']    = '';
	$fields['billing']['billing_country']['class']    = array( 'checkout-hidden-field' );
	$fields['billing']['billing_country']['default']  = 'RU';
	$fields['billing']['billing_country']['required'] = false;
	$fields['billing']['billing_country']['priority'] = 5;

	$fields['billing']['billing_first_name']['label']       = 'Имя';
	$fields['billing']['billing_first_name']['placeholder'] = 'Как к вам обращаться';
	$fields['billing']['billing_first_name']['priority']    = 10;

	$fields['billing']['billing_phone']['label']       = 'Телефон';
	$fields['billing']['billing_phone']['placeholder'] = '+7 900 000-00-00';
	$fields['billing']['billing_phone']['priority']    = 20;

	$fields['billing']['billing_email']['label']       = 'Email';
	$fields['billing']['billing_email']['placeholder'] = 'Для чека и статуса заказа';
	$fields['billing']['billing_email']['required']    = false;
	$fields['billing']['billing_email']['priority']    = 30;

	$fields['billing']['billing_city']['label']       = 'Город';
	$fields['billing']['billing_city']['placeholder'] = 'Населённый пункт';
	$fields['billing']['billing_city']['priority']    = 40;

	$fields['billing']['billing_address_1']['label']       = 'Адрес доставки';
	$fields['billing']['billing_address_1']['placeholder'] = 'Улица, дом, квартира';
	$fields['billing']['billing_address_1']['priority']    = 50;

	$fields['billing']['carpediem_consent'] = array(
		'type'     => 'checkbox',
		'label'    => $label,
		'required' => true,
		'class'    => array( 'form-row-wide', 'consent-row' ),
		'priority' => 90,
	);

	// Телефон обязателен, компания не нужна — это же настроено и в опциях Woo.
	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['required'] = true;
	}

	return $fields;
} );

// Примечание к заказу убрано из короткой формы вместе с пустым блоком «Детали».
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
add_filter( 'default_checkout_billing_country', fn() => 'RU' );

// Woo переводит этот заголовок как «Оплата и доставка», хотя блок теперь содержит контакты и адрес.
add_filter( 'gettext', function ( $translation, $text, $domain ) {
	$billing_heading = in_array( $text, array( 'Billing & Shipping', 'Billing &amp; Shipping' ), true ) || 'Оплата и доставка' === $translation;
	if ( 'woocommerce' === $domain && is_checkout() && $billing_heading ) {
		return 'Контакты и доставка';
	}

	return $translation;
}, 10, 3 );

// Фиксируем факт согласия в заказе: дата и IP — это и есть доказательство по 152-ФЗ.
add_action( 'woocommerce_checkout_create_order', function ( $order, $data ) {
	if ( ! $order->get_billing_country() ) {
		$order->set_billing_country( 'RU' );
	}

	if ( ! empty( $data['carpediem_consent'] ) ) {
		$order->update_meta_data( '_carpediem_consent', current_time( 'mysql' ) );
		$order->update_meta_data( '_carpediem_consent_ip', carpediem_client_ip() );
	}
}, 10, 2 );

// Показываем это в админке рядом с адресом покупателя.
add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
	$date = $order->get_meta( '_carpediem_consent' );
	if ( $date ) {
		printf(
			'<p><strong>Согласие на обработку ПДн:</strong> %s (IP %s)</p>',
			esc_html( $date ),
			esc_html( $order->get_meta( '_carpediem_consent_ip' ) )
		);
	}
} );

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
function carpediem_default_size_tables() {
	return array(
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

}

function carpediem_category_size_table( $term ) {
	if ( metadata_exists( 'term', $term->term_id, 'carpediem_size_table' ) ) {
		return get_term_meta( $term->term_id, 'carpediem_size_table', true ) ?: null;
	}
	return carpediem_default_size_tables()[ $term->slug ] ?? null;
}

function carpediem_size_table( $product ) {
	$terms = wp_get_post_terms( $product->get_id(), 'product_cat' );
	if ( is_wp_error( $terms ) ) {
		return null;
	}

	foreach ( $terms as $term ) {
		$table = carpediem_category_size_table( $term );
		if ( $table ) {
			return $table;
		}
	}

	return null;
}
