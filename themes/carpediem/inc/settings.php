<?php
/** Общие настройки витрины; существующий контент служит начальным значением. */
defined( 'ABSPATH' ) || exit;

function carpediem_setting_defaults() {
	return array(
		'announcement' => 'CARPE DIEM · Style of Soul',
		'collection' => 'Коллекция 2026',
		'hero_title' => 'Одежда с характером.',
		'hero_text' => 'Для тех, кто выбирает свой путь. Стиль, свобода и смысл — в каждой детали.',
		'hero_button' => 'Смотреть коллекцию',
		'hero_url' => home_url( '/catalog/' ),
		'hero_image' => 0,
		'selection_title' => 'Выбор CARPE DIEM',
		'product_ids' => array(),
		'category_ids' => null,
		'community_title' => 'Спасибо за то, что ты с нами',
		'community_text' => 'Мы объединяем людей, которые ценят стиль, свободу и смысл. Ты — часть нашей истории.',
		'brand_title' => 'Carpe Diem — Style of Soul',
		'brand_text' => 'Каждая вещь — это больше, чем одежда. Это часть пути, которую мы создаём вместе.',
		'brand_image' => 0,
		'phone' => '', 'email' => '', 'address' => '', 'hours' => '',
		'telegram' => '', 'vk' => '', 'instagram' => '',
		'delivery_note' => 'По России и миру. Способ и стоимость — при оформлении.',
		'returns_note' => 'Не подошёл размер? Посмотри условия обмена и возврата.',
	);
}

function carpediem_setting( $key ) {
	$options = get_option( 'carpediem_settings', array() );
	$defaults = carpediem_setting_defaults();
	return is_array( $options ) && array_key_exists( $key, $options ) ? $options[ $key ] : ( $defaults[ $key ] ?? '' );
}

function carpediem_home_categories() {
	$ids = carpediem_setting( 'category_ids' );
	if ( array() === $ids ) {
		return array();
	}
	$args = array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array( get_option( 'default_product_cat' ) ), 'orderby' => 'id' );
	if ( is_array( $ids ) ) {
		$args['include'] = $ids;
		$args['orderby'] = 'include';
	}
	$terms = get_terms( $args );
	return is_wp_error( $terms ) ? array() : $terms;
}

/** Цвет задаётся в атрибуте WooCommerce и одинаково отображается на всей витрине. */
function carpediem_term_color( $term ) {
	$defaults = array( 'black' => array( '#141416', '' ), 'black-brown' => array( '#141416', '#6b4a2f' ), 'graphite' => array( '#3a3a40', '' ), 'silver' => array( '#c9c9cf', '' ) );
	$fallback = $defaults[ $term->slug ] ?? array( '#888888', '' );
	return array(
		get_term_meta( $term->term_id, 'carpediem_color', true ) ?: $fallback[0],
		metadata_exists( 'term', $term->term_id, 'carpediem_color_secondary' ) ? get_term_meta( $term->term_id, 'carpediem_color_secondary', true ) : $fallback[1],
	);
}

function carpediem_color_swatches() {
	$terms = get_terms( array( 'taxonomy' => 'pa_color', 'hide_empty' => false ) );
	$colors = array();
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$pair = carpediem_term_color( $term );
			$colors[ $term->slug ] = $pair[1] ? 'linear-gradient(135deg, ' . $pair[0] . ' 50%, ' . $pair[1] . ' 50%)' : $pair[0];
		}
	}
	return $colors;
}

/** Контакты дополняют текст страницы, не заменяя отредактированное владельцем содержимое. */
add_filter( 'the_content', function ( $content ) {
	if ( ! is_page( 'contacts' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	ob_start();
	get_template_part( 'template-parts/contacts' );
	return ob_get_clean() . $content;
} );
