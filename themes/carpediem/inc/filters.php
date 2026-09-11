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
add_action( 'woocommerce_before_shop_loop', 'carpediem_filters_bar', 15 );
add_action( 'woocommerce_no_products_found', 'carpediem_filters_bar', 5 );

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
	?>
	<details class="filters" id="catalog-filters" <?php echo carpediem_has_active_filters() ? 'open' : ''; ?>>
		<summary class="filters__summary">
			Фильтры
			<?php if ( carpediem_has_active_filters() ) : ?>
				<span class="filters__badge">включены</span>
			<?php endif; ?>
		</summary>

		<form class="filters__form" method="get" action="<?php echo esc_url( is_wp_error( $action ) ? wc_get_page_permalink( 'shop' ) : $action ); ?>">
			<?php if ( isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ) : ?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( sanitize_key( wp_unslash( $_GET['orderby'] ) ) ); ?>">
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
							<label class="chip">
								<input type="checkbox" name="color[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $filters['color'], true ) ); ?>>
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>

			<fieldset class="filters__group">
				<legend class="filters__legend">Цена, ₽</legend>
				<div class="filters__price">
					<label class="screen-reader-text" for="min_price">Цена от</label>
					<input class="filters__input" type="number" inputmode="numeric" min="0" step="100" id="min_price" name="min_price" placeholder="от" value="<?php echo $filters['min_price'] ? esc_attr( (int) $filters['min_price'] ) : ''; ?>">
					<span class="filters__dash">—</span>
					<label class="screen-reader-text" for="max_price">Цена до</label>
					<input class="filters__input" type="number" inputmode="numeric" min="0" step="100" id="max_price" name="max_price" placeholder="до" value="<?php echo $filters['max_price'] ? esc_attr( (int) $filters['max_price'] ) : ''; ?>">
				</div>
			</fieldset>

			<div class="filters__actions">
				<button class="btn" type="submit">Показать</button>
				<?php if ( carpediem_has_active_filters() ) : ?>
					<a class="filters__reset" href="<?php echo esc_url( is_wp_error( $action ) ? wc_get_page_permalink( 'shop' ) : $action ); ?>">Сбросить</a>
				<?php endif; ?>
			</div>
		</form>
	</details>
	<?php
}
