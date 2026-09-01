<?php
/**
 * Демо-наполнение магазина («рыба»): атрибуты + товары с вариациями.
 * Запуск: npx @wordpress/env run cli -- wp eval-file wp-content/themes/carpediem/tools/seed-demo.php
 * Повторный запуск обновляет уже созданные товары (ищем по slug).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Латинские слаги для значений с кириллицей: иначе в адресе фильтра
 * получается %d1%87%d1%91... вместо читаемого ?color[]=black.
 */
function cd_seed_term_slug( $value ) {
	$map = array(
		'Чёрный'               => 'black',
		'Чёрный / Коричневый'  => 'black-brown',
		'Графит'               => 'graphite',
		'Серебро'              => 'silver',
		'ONE SIZE'             => 'one-size',
	);

	return isset( $map[ $value ] ) ? $map[ $value ] : '';
}

/** Создаёт глобальный атрибут и его значения, возвращает имя таксономии. */
function cd_seed_attribute( $label, $slug, $values ) {
	$taxonomy = wc_attribute_taxonomy_name( $slug );

	if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) {
		wc_create_attribute( array(
			'name'         => $label,
			'slug'         => $slug,
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => false,
		) );
	}

	if ( ! taxonomy_exists( $taxonomy ) ) {
		register_taxonomy( $taxonomy, 'product', array( 'hierarchical' => false, 'show_ui' => false ) );
	}

	$position = 0;
	foreach ( $values as $value ) {
		$term      = term_exists( $value, $taxonomy );
		$term_slug = cd_seed_term_slug( $value );

		if ( ! $term ) {
			$term = wp_insert_term( $value, $taxonomy, $term_slug ? array( 'slug' => $term_slug ) : array() );
		} elseif ( $term_slug ) {
			// Чиним слаг у уже созданных значений.
			$existing = get_term( (int) $term['term_id'], $taxonomy );
			if ( $existing && $existing->slug !== $term_slug ) {
				wp_update_term( (int) $term['term_id'], $taxonomy, array( 'slug' => $term_slug ) );
			}
		}
		// Woo сортирует значения атрибута по этому мета-полю, иначе размеры идут по алфавиту.
		if ( ! is_wp_error( $term ) ) {
			++$position;
			// Разные версии Woo читают то одно поле, то другое — пишем оба.
			update_term_meta( (int) $term['term_id'], 'order', $position );
			update_term_meta( (int) $term['term_id'], 'order_' . $taxonomy, $position );
		}
	}

	return $taxonomy;
}

/** Объект атрибута товара. */
function cd_seed_product_attribute( $taxonomy, $values, $variation = false, $position = 0 ) {
	$attribute = new WC_Product_Attribute();
	$attribute->set_id( wc_attribute_taxonomy_id_by_name( str_replace( 'pa_', '', $taxonomy ) ) );
	$attribute->set_name( $taxonomy );
	$attribute->set_options( array_map( function ( $v ) use ( $taxonomy ) {
		$term = get_term_by( 'name', $v, $taxonomy );
		return $term ? $term->term_id : 0;
	}, $values ) );
	$attribute->set_position( $position );
	$attribute->set_visible( ! $variation );
	$attribute->set_variation( $variation );

	return $attribute;
}

$sizes  = array( 'XS', 'S', 'M', 'L', 'XL', 'XXL' );
$tax_size     = cd_seed_attribute( 'Размер', 'size', array_merge( $sizes, array( 'ONE SIZE' ) ) );
$tax_color    = cd_seed_attribute( 'Цвет', 'color', array( 'Чёрный', 'Чёрный / Коричневый', 'Графит', 'Серебро' ) );
$tax_features = cd_seed_attribute( 'Особенности', 'features', array(
	'Термохромная ткань Premium', 'Камуфляжный паттерн', 'Вышивка логотипа', 'Split-дизайн',
	'Балаклава встроена в капюшон', 'Металлическая молния с крестом', 'Оверсайз-крой',
	'Плотный хлопок', 'Принт водной основы', 'Съёмный ремень', 'Ювелирная сталь',
) );
$tax_material = cd_seed_attribute( 'Состав', 'material', array( '100% Cotton Premium', '95% Cotton, 5% Elastane', 'Нейлон 900D', 'Ювелирная сталь 316L' ) );
$tax_density  = cd_seed_attribute( 'Плотность', 'density', array( '190 GSM', '260 GSM', '380 GSM', '—' ) );
$tax_care     = cd_seed_attribute( 'Уход', 'care', array(
	'Ручная или деликатная стирка до 30°C', 'Не отбеливать', 'Сушить в тени',
	'Не гладить принт и вышивку', 'Не сдавать в химчистку',
) );

$apparel_care = array( 'Ручная или деликатная стирка до 30°C', 'Не отбеливать', 'Сушить в тени', 'Не гладить принт и вышивку' );

$products = array(
	array(
		'slug' => 'split-camo-thermochromic', 'name' => 'Зип-худи Split Camo Thermochromic',
		'price' => 12990, 'cat' => 'hoodie', 'new' => true,
		'short' => 'Термохромный эффект: меняет цвет при тепле.',
		'desc'  => 'Зип-худи Split Camo Thermochromic — это сочетание технологии, функциональности и уличной эстетики. Ткань реагирует на тепло тела и рук, постепенно проявляя камуфляжный узор. Встроенная балаклава, вышивка логотипа и металлические элементы подчёркивают характер и стиль.',
		'colors' => array( 'Чёрный', 'Чёрный / Коричневый' ), 'sizes' => $sizes,
		'features' => array( 'Термохромная ткань Premium', 'Камуфляжный паттерн', 'Вышивка логотипа', 'Split-дизайн', 'Балаклава встроена в капюшон', 'Металлическая молния с крестом' ),
		'material' => '100% Cotton Premium', 'density' => '380 GSM', 'care' => $apparel_care,
	),
	array(
		'slug' => 'hoodie-fck-the-crisis', 'name' => 'Худи F*ck the Crisis',
		'price' => 12990, 'cat' => 'hoodie', 'new' => true,
		'short' => 'Оверсайз-худи с принтом водной основы.',
		'desc'  => 'Классический оверсайз-силуэт, плотное петлевое полотно и принт водной основы, который не «дубеет» после стирки.',
		'colors' => array( 'Чёрный' ), 'sizes' => $sizes,
		'features' => array( 'Оверсайз-крой', 'Принт водной основы', 'Вышивка логотипа' ),
		'material' => '100% Cotton Premium', 'density' => '380 GSM', 'care' => $apparel_care,
	),
	array(
		'slug' => 'hoodie-split-camo', 'name' => 'Худи Split Camo',
		'price' => 12990, 'cat' => 'hoodie',
		'short' => 'Split-дизайн и камуфляжный паттерн.',
		'desc'  => 'Худи со split-конструкцией: две половины кроя из разных полотен, собранные по центральной оси.',
		'colors' => array( 'Чёрный / Коричневый' ), 'sizes' => $sizes,
		'features' => array( 'Split-дизайн', 'Камуфляжный паттерн', 'Вышивка логотипа' ),
		'material' => '100% Cotton Premium', 'density' => '380 GSM', 'care' => $apparel_care,
	),
	array(
		'slug' => 'tshirt-bitches-money', 'name' => 'Футболка Bitches Money',
		'price' => 4990, 'cat' => 'tshirt',
		'short' => 'Плотный хлопок, прямой крой.',
		'desc'  => 'Футболка из плотного хлопка с крупным шелкографическим принтом. Держит форму после стирок.',
		'colors' => array( 'Чёрный' ), 'sizes' => $sizes,
		'features' => array( 'Плотный хлопок', 'Принт водной основы', 'Оверсайз-крой' ),
		'material' => '100% Cotton Premium', 'density' => '260 GSM', 'care' => $apparel_care,
	),
	array(
		'slug' => 'tshirt-no-poverty', 'name' => 'Футболка No Poverty',
		'price' => 4990, 'cat' => 'tshirt', 'new' => true,
		'short' => 'No poverty, no death, only love.',
		'desc'  => 'Минималистичная футболка с текстовым манифестом на спине.',
		'colors' => array( 'Графит' ), 'sizes' => $sizes,
		'features' => array( 'Плотный хлопок', 'Принт водной основы' ),
		'material' => '100% Cotton Premium', 'density' => '260 GSM', 'care' => $apparel_care,
	),
	array(
		'slug' => 'sweatshirt-fck-the-crisis', 'name' => 'Свитшот F*ck the Crisis',
		'price' => 9990, 'cat' => 'sweatshirt',
		'short' => 'Петлевой хлопок, посадка оверсайз.',
		'desc'  => 'Свитшот с широкими рукавами и заниженной линией плеча.',
		'colors' => array( 'Чёрный' ), 'sizes' => $sizes,
		'features' => array( 'Оверсайз-крой', 'Вышивка логотипа' ),
		'material' => '95% Cotton, 5% Elastane', 'density' => '380 GSM', 'care' => $apparel_care,
	),
	array(
		'slug' => 'bag-logo-cross-shoulder', 'name' => 'Сумка Logo Cross Shoulder Bag',
		'price' => 7990, 'cat' => 'bags',
		'short' => 'Нейлон 900D, съёмный ремень.',
		'desc'  => 'Компактная сумка через плечо с усиленным дном и металлической фурнитурой.',
		'colors' => array( 'Чёрный' ), 'sizes' => array( 'ONE SIZE' ),
		'features' => array( 'Съёмный ремень', 'Вышивка логотипа' ),
		'material' => 'Нейлон 900D', 'density' => '—', 'care' => array( 'Не сдавать в химчистку', 'Сушить в тени' ),
	),
	array(
		'slug' => 'cap-logo-cross', 'name' => 'Кепка Logo Cross',
		'price' => 6990, 'cat' => 'accessories',
		'short' => 'Регулируемая застёжка.',
		'desc'  => 'Кепка из плотного хлопка с вышивкой логотипа на фронтальной панели.',
		'colors' => array( 'Чёрный' ), 'sizes' => array( 'ONE SIZE' ),
		'features' => array( 'Вышивка логотипа', 'Плотный хлопок' ),
		'material' => '100% Cotton Premium', 'density' => '260 GSM', 'care' => array( 'Ручная или деликатная стирка до 30°C', 'Сушить в тени' ),
	),
	array(
		'slug' => 'pendant-cross', 'name' => 'Подвеска Cross Pendant',
		'price' => 2490, 'cat' => 'accessories', 'new' => true,
		'short' => 'Ювелирная сталь, цепь в комплекте.',
		'desc'  => 'Подвеска-крест из ювелирной стали с матовой полировкой. Не темнеет и не вызывает аллергии.',
		'colors' => array( 'Серебро' ), 'sizes' => array( 'ONE SIZE' ),
		'features' => array( 'Ювелирная сталь' ),
		'material' => 'Ювелирная сталь 316L', 'density' => '—', 'care' => array( 'Не сдавать в химчистку' ),
	),
	array(
		'slug' => 'chain-logo', 'name' => 'Цепь Logo Chain',
		'price' => 3990, 'cat' => 'accessories',
		'short' => 'Плетение «якорь», 60 см.',
		'desc'  => 'Массивная цепь из ювелирной стали с фирменным замком-крестом.',
		'colors' => array( 'Серебро' ), 'sizes' => array( 'ONE SIZE' ),
		'features' => array( 'Ювелирная сталь' ),
		'material' => 'Ювелирная сталь 316L', 'density' => '—', 'care' => array( 'Не сдавать в химчистку' ),
	),
);

$created = array();

foreach ( $products as $data ) {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'product' );
	$product  = $existing ? new WC_Product_Variable( $existing->ID ) : new WC_Product_Variable();

	$product->set_name( $data['name'] );
	$product->set_slug( $data['slug'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_short_description( $data['short'] );
	$product->set_description( $data['desc'] );
	$product->set_category_ids( array( get_term_by( 'slug', $data['cat'], 'product_cat' )->term_id ) );

	$attributes = array(
		cd_seed_product_attribute( $tax_size, $data['sizes'], true, 0 ),
		cd_seed_product_attribute( $tax_color, $data['colors'], true, 1 ),
		cd_seed_product_attribute( $tax_features, $data['features'], false, 2 ),
		cd_seed_product_attribute( $tax_material, array( $data['material'] ), false, 3 ),
		cd_seed_product_attribute( $tax_density, array( $data['density'] ), false, 4 ),
		cd_seed_product_attribute( $tax_care, $data['care'], false, 5 ),
	);
	$product->set_attributes( $attributes );
	$product_id = $product->save();

	if ( ! empty( $data['new'] ) ) {
		wp_set_object_terms( $product_id, 'новинка', 'product_tag' );
	}

	// Вариации: размер × цвет.
	foreach ( $product->get_children() as $child_id ) {
		wp_delete_post( $child_id, true );
	}

	foreach ( $data['sizes'] as $size ) {
		foreach ( $data['colors'] as $color ) {
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_attributes( array(
				$tax_size  => get_term_by( 'name', $size, $tax_size )->slug,
				$tax_color => get_term_by( 'name', $color, $tax_color )->slug,
			) );
			$variation->set_regular_price( $data['price'] );
			$variation->set_manage_stock( true );
			$variation->set_stock_quantity( 10 );
			$variation->set_stock_status( 'instock' );
			$variation->save();
		}
	}

	WC_Product_Variable::sync( $product_id );
	$created[ $data['slug'] ] = $product_id;
	WP_CLI::log( sprintf( '%s → #%d', $data['name'], $product_id ) );
}

// Перекрёстные продажи для блока «С этим товаром покупают» / «Рекомендуем».
$cross = array(
	'split-camo-thermochromic'  => array( 'tshirt-bitches-money', 'bag-logo-cross-shoulder', 'cap-logo-cross', 'pendant-cross', 'sweatshirt-fck-the-crisis' ),
	'hoodie-fck-the-crisis'     => array( 'tshirt-no-poverty', 'pendant-cross', 'cap-logo-cross', 'bag-logo-cross-shoulder' ),
	'tshirt-bitches-money'      => array( 'hoodie-fck-the-crisis', 'chain-logo', 'cap-logo-cross' ),
	'bag-logo-cross-shoulder'   => array( 'pendant-cross', 'cap-logo-cross', 'chain-logo' ),
	'pendant-cross'             => array( 'chain-logo', 'cap-logo-cross', 'tshirt-no-poverty' ),
);

foreach ( $cross as $slug => $slugs ) {
	if ( empty( $created[ $slug ] ) ) {
		continue;
	}
	$ids = array_values( array_filter( array_map( function ( $s ) use ( $created ) {
		return isset( $created[ $s ] ) ? $created[ $s ] : null;
	}, $slugs ) ) );

	$p = wc_get_product( $created[ $slug ] );
	$p->set_cross_sell_ids( $ids );
	$p->save();
}

WP_CLI::success( 'Демо-товары созданы: ' . count( $created ) );
