<?php
/** Общие настройки витрины; существующий контент служит начальным значением. */
defined( 'ABSPATH' ) || exit;

function carpediem_setting_defaults() {
	return array(
		'announcement' => 'CARPE DIEM',
		'collection' => 'Коллекция 2026',
		'hero_title' => 'Одежда с характером.',
		'hero_text' => 'Для тех, кто выбирает свой путь. Стиль, свобода и смысл — в каждой детали.',
		'hero_button' => 'Смотреть коллекцию',
		'hero_url' => home_url( '/catalog/' ),
		'hero_image' => 0,
		'theme_dark_label' => 'Демон',
		'theme_light_label' => 'Ангел',
		'selection_title' => 'Популярные товары',
		'product_ids' => array(),
		'category_ids' => null,
		'community_title' => 'Спасибо за то, что ты с нами',
		'community_text' => 'Мы объединяем людей, которые ценят стиль, свободу и смысл. Ты — часть нашей истории.',
		'brand_title' => 'Carpe Diem — Style of Soul',
		'brand_text' => 'Каждая вещь — это больше, чем одежда. Это часть пути, которую мы создаём вместе.',
		'brand_image' => 0,
		'lookbook_title' => 'Лукбук сообщества',
		'lookbook_text' => 'CARPE DIEM в жизни — образы людей, которые носят бренд по-своему.',
		'lookbook_image_1' => 0,
		'lookbook_image_2' => 0,
		'lookbook_image_3' => 0,
		'lookbook_image_4' => 0,
		'phone' => '', 'email' => '', 'address' => '', 'hours' => '',
		'telegram' => '', 'vk' => '', 'instagram' => '', 'tiktok' => '',
		'delivery_note' => 'По России и миру. Способ и стоимость — при оформлении.',
		'returns_note' => 'Не подошёл размер? Посмотри условия обмена и возврата.',

		// Тексты каталога и карточки товара.
		'catalog_all_label' => 'Все вещи',
		'catalog_add_to_cart_label' => 'В корзину',
		'catalog_buy_label' => 'Купить',
		'catalog_details_label' => 'Подробнее',
		'product_new_label' => 'Новинка',
		'product_buy_now_label' => 'Купить сейчас',
		'product_size_guide_label' => 'Таблица размеров',
		'product_related_label' => 'С этим товаром покупают',
		'product_delivery_label' => 'Доставка',
		'product_returns_label' => 'Обмен и возврат',
		'product_share_label' => 'Поделиться',
		'product_description_label' => 'Описание',
		'product_materials_label' => 'Материалы и уход',
		'product_material_label' => 'Состав',
		'product_density_label' => 'Плотность',
		'product_sizes_label' => 'Таблица размеров',
		'product_size_note' => 'Все измерения указаны в сантиметрах.',

		// Короткая заявка по телефону.
		'quick_order_button_label' => 'Заказать по телефону',
		'quick_order_title' => 'Заказ по телефону',
		'quick_order_text' => 'Оставьте имя и телефон — мы перезвоним и уточним детали.',
		'quick_order_name_label' => 'Имя',
		'quick_order_phone_label' => 'Телефон',
		'quick_order_consent_label' => 'Согласен на обработку персональных данных',
		'quick_order_submit_label' => 'Отправить заявку',
		'quick_order_cancel_label' => 'Отмена',
		'quick_order_success_title' => 'Готово',
		'quick_order_close_label' => 'Закрыть',
		'quick_order_generic_error' => 'Не получилось отправить. Попробуйте ещё раз.',
		'quick_order_network_error' => 'Сеть недоступна. Попробуйте ещё раз.',

		// Вариант B для короткого checkout: меньше текста, однозначное действие.
		'checkout_intro_eyebrow' => 'Оформление заказа',
		'checkout_intro_title' => 'Без регистрации',
		'checkout_intro_text' => 'Контакты и доставка — на одной странице.',
		'checkout_contact_heading' => 'Контакты и доставка',
		'checkout_order_heading' => 'Ваш заказ',
		'checkout_product_label' => 'Товар',
		'checkout_price_label' => 'Цена',
		'checkout_items_label' => 'Товары',
		'checkout_shipping_label' => 'Доставка',
		'checkout_total_label' => 'К оплате',
		'checkout_place_order_label' => 'Оформить заказ',
		'checkout_consent_label' => 'Согласен на обработку персональных данных',
		'checkout_name_label' => 'Имя',
		'checkout_name_placeholder' => 'Как к вам обращаться',
		'checkout_phone_label' => 'Телефон',
		'checkout_phone_placeholder' => '+7 900 000-00-00',
		'checkout_email_label' => 'Email',
		'checkout_email_placeholder' => 'Для чека и статуса заказа',
		'checkout_city_label' => 'Город',
		'checkout_city_placeholder' => 'Населённый пункт',
		'checkout_address_label' => 'Адрес доставки',
		'checkout_address_placeholder' => 'Улица, дом, квартира',

		// Корзина.
		'cart_order_heading' => 'Ваш заказ',
		'cart_items_label' => 'Товары',
		'cart_shipping_pending_label' => 'Рассчитывается при оформлении',
		'cart_total_label' => 'Итого',
		'cart_checkout_label' => 'Перейти к оформлению',
		'cart_continue_label' => 'Продолжить покупки',
		'cart_payment_note' => 'Способ оплаты можно выбрать при оформлении заказа.',
		'cart_thanks_title' => 'Спасибо за поддержку',
		'cart_thanks_text' => 'Ты часть нашего комьюнити. Вместе мы создаём стиль и смысл.',
	);
}

function carpediem_setting( $key ) {
	$options = get_option( 'carpediem_settings', array() );
	$defaults = carpediem_setting_defaults();
	$value = is_array( $options ) && array_key_exists( $key, $options ) ? $options[ $key ] : ( $defaults[ $key ] ?? '' );
	if ( 'selection_title' === $key && in_array( $value, array( 'Выбор CARPE DIEM', 'Выбор CAPRE DIEM' ), true ) ) {
		return $defaults[ $key ];
	}
	return $value;
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
