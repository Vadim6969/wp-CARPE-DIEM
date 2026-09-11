<?php
/**
 * Фильтры каталога: размер, цвет, цена. Обычная GET-форма, без JS и без плагинов.
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

/**
 * Выбранные фильтры из адреса страницы.
 */
function carpediem_active_filters() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended — это чтение публичных GET-параметров каталога.
	$slugs = function ( $key ) {
		if ( empty( $_GET[ $key ] ) || ! is_array( $_GET[ $key ] ) ) {
			return array();
		}

		return array_values( array_filter( array_map( 'sanitize_title', wp_unslash( $_GET[ $key ] ) ) ) );
	};

	return array(
		'size'      => $slugs( 'size' ),
		'color'     => $slugs( 'color' ),
		'min_price' => isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : 0,
		'max_price' => isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : 0,
	);
	// phpcs:enable
}

/**
 * Есть ли хоть один активный фильтр.
 */
function carpediem_has_active_filters() {
	$f = carpediem_active_filters();

	return $f['size'] || $f['color'] || $f['min_price'] || $f['max_price'];
}

/**
 * Применяем фильтры к запросу каталога.
 */
add_action( 'woocommerce_product_query', function ( $query ) {
	$filters = carpediem_active_filters();

	$tax_query = (array) $query->get( 'tax_query' );

	foreach ( array( 'size' => 'pa_size', 'color' => 'pa_color' ) as $key => $taxonomy ) {
		if ( $filters[ $key ] ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $filters[ $key ],
				'operator' => 'IN',
			);
		}
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query->set( 'tax_query', $tax_query );

	if ( $filters['min_price'] || $filters['max_price'] ) {
		$min = $filters['min_price'] ?: 0;
		$max = $filters['max_price'] ?: PHP_INT_MAX;

		$meta_query   = (array) $query->get( 'meta_query' );
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => array( $min, $max ),
			'compare' => 'BETWEEN',
			'type'    => 'NUMERIC',
		);

		$query->set( 'meta_query', $meta_query );
	}
} );

/**
 * Панель фильтров над сеткой каталога.
 */
// Фильтры, количество товаров и сортировка собраны в одну панель.
function carpediem_catalog_toolbar_open() {
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		echo '<div class="catalog-toolbar">';
	}
}

function carpediem_catalog_toolbar_close() {
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		echo '</div>';
	}
}

function carpediem_empty_catalog_toolbar_open() {
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		echo '<div class="catalog-toolbar catalog-toolbar--empty">';
	}
}

add_action( 'woocommerce_before_shop_loop', 'carpediem_catalog_toolbar_open', 14 );
add_action( 'woocommerce_before_shop_loop', 'carpediem_filters_bar', 15 );
add_action( 'woocommerce_before_shop_loop', 'carpediem_catalog_toolbar_close', 35 );
add_action( 'woocommerce_no_products_found', 'carpediem_empty_catalog_toolbar_open', 4 );
add_action( 'woocommerce_no_products_found', 'carpediem_filters_bar', 5 );
add_action( 'woocommerce_no_products_found', 'carpediem_catalog_toolbar_close', 6 );

function carpediem_filters_bar() {
	if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
		return;
	}

	$filters = carpediem_active_filters();
	$sizes   = get_terms( array( 'taxonomy' => 'pa_size', 'hide_empty' => true, 'orderby' => 'meta_value_num', 'meta_key' => 'order' ) );
	$colors  = get_terms( array( 'taxonomy' => 'pa_color', 'hide_empty' => true ) );

	if ( is_wp_error( $sizes ) ) {
		$sizes = array();
	}
	if ( is_wp_error( $colors ) ) {
		$colors = array();
	}

	if ( ! $sizes && ! $colors ) {
		return;
	}

	// Сохраняем контекст страницы (категорию, поиск) — форма шлёт GET на текущий адрес.
	$action = is_product_category() || is_product_tag() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' );
	$action = is_wp_error( $action ) ? wc_get_page_permalink( 'shop' ) : $action;
	// phpcs:disable WordPress.Security.NonceVerification.Recommended — сортировка приходит из публичного GET-запроса каталога.
	$orderby = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
	// phpcs:enable

	$filter_url = function ( $remove_key = '', $remove_value = '' ) use ( $action, $filters, $orderby ) {
		$next = $filters;
		if ( 'all' === $remove_key ) {
			$next = array( 'size' => array(), 'color' => array(), 'min_price' => 0, 'max_price' => 0 );
		} elseif ( in_array( $remove_key, array( 'size', 'color' ), true ) ) {
			$next[ $remove_key ] = array_values( array_diff( $next[ $remove_key ], array( $remove_value ) ) );
		} elseif ( in_array( $remove_key, array( 'min_price', 'max_price' ), true ) ) {
			$next[ $remove_key ] = 0;
		}

		$args = array();
		foreach ( array( 'size', 'color' ) as $key ) {
			if ( $next[ $key ] ) { $args[ $key ] = $next[ $key ]; }
		}
		foreach ( array( 'min_price', 'max_price' ) as $key ) {
			if ( $next[ $key ] ) { $args[ $key ] = (int) $next[ $key ]; }
		}
		if ( $orderby ) { $args['orderby'] = $orderby; }

		return add_query_arg( $args, $action );
	};

	$active_tags = array();
	foreach ( array( 'size' => array( 'Размер', $sizes ), 'color' => array( 'Цвет', $colors ) ) as $key => $config ) {
		foreach ( $config[1] as $term ) {
			if ( in_array( $term->slug, $filters[ $key ], true ) ) {
				$active_tags[] = array( $config[0] . ': ' . $term->name, $filter_url( $key, $term->slug ) );
			}
		}
	}
	if ( $filters['min_price'] ) {
		$active_tags[] = array( 'От ' . number_format_i18n( $filters['min_price'], 0 ) . ' ₽', $filter_url( 'min_price' ) );
	}
	if ( $filters['max_price'] ) {
		$active_tags[] = array( 'До ' . number_format_i18n( $filters['max_price'], 0 ) . ' ₽', $filter_url( 'max_price' ) );
	}
	?>
	<details class="filters" id="catalog-filters" <?php echo $active_tags ? 'data-active="true"' : ''; ?>>
		<summary class="filters__summary">
			<span class="filters__summary-icon" aria-hidden="true"><i></i><i></i><i></i></span>
			<span class="filters__summary-title">Фильтры</span>
			<span class="filters__summary-action" aria-hidden="true"></span>
		</summary>

		<form class="filters__form" method="get" action="<?php echo esc_url( $action ); ?>">
			<?php if ( $orderby ) : ?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
			<?php endif; ?>

			<?php if ( $sizes ) : ?>
				<fieldset class="filters__group">
					<legend class="filters__legend">Размер</legend>
					<div class="filters__chips">
						<?php foreach ( $sizes as $term ) : ?>
							<label class="chip">
								<input type="checkbox" name="size[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $filters['size'], true ) ); ?>>
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>

			<?php if ( $colors ) : ?>
				<fieldset class="filters__group">
					<legend class="filters__legend">Цвет</legend>
					<div class="filters__chips">
						<?php foreach ( $colors as $term ) : ?>
							<?php $color_pair = carpediem_term_color( $term ); $color_value = $color_pair[1] ? 'linear-gradient(135deg, ' . $color_pair[0] . ' 50%, ' . $color_pair[1] . ' 50%)' : $color_pair[0]; ?>
							<label class="chip chip--color">
								<input type="checkbox" name="color[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $filters['color'], true ) ); ?>>
								<span><i class="chip__swatch" style="--chip-color: <?php echo esc_attr( $color_value ); ?>" aria-hidden="true"></i><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>

			<fieldset class="filters__group">
				<legend class="filters__legend">Цена, ₽</legend>
				<div class="filters__price">
					<label class="filters__price-field" for="min_price"><span>От</span><input class="filters__input" type="number" inputmode="numeric" min="0" step="100" id="min_price" name="min_price" placeholder="0" value="<?php echo $filters['min_price'] ? esc_attr( (int) $filters['min_price'] ) : ''; ?>"></label>
					<span class="filters__dash">—</span>
					<label class="filters__price-field" for="max_price"><span>До</span><input class="filters__input" type="number" inputmode="numeric" min="0" step="100" id="max_price" name="max_price" placeholder="∞" value="<?php echo $filters['max_price'] ? esc_attr( (int) $filters['max_price'] ) : ''; ?>"></label>
				</div>
			</fieldset>

			<div class="filters__actions">
				<button class="btn btn--primary filters__apply" type="submit">Показать товары</button>
				<?php if ( $active_tags ) : ?>
					<a class="filters__reset" href="<?php echo esc_url( $filter_url( 'all' ) ); ?>">Сбросить всё</a>
				<?php endif; ?>
			</div>
		</form>
	</details>
	<?php if ( $active_tags ) : ?>
		<nav class="filters__active" aria-label="Активные фильтры">
			<?php foreach ( $active_tags as $tag ) : ?>
				<a class="filters__tag" href="<?php echo esc_url( $tag[1] ); ?>"><span><?php echo esc_html( $tag[0] ); ?></span><span class="filters__tag-remove" aria-hidden="true">×</span><span class="screen-reader-text">Удалить фильтр</span></a>
			<?php endforeach; ?>
			<a class="filters__clear" href="<?php echo esc_url( $filter_url( 'all' ) ); ?>">Очистить</a>
		</nav>
	<?php endif; ?>
	<?php
}
