<?php
/**
 * Настройки магазина, которые не хочется кликать в админке заново после wp-env destroy.
 * Запуск: wp eval-file wp-content/themes/carpediem/tools/setup-store.php
 */

defined( 'ABSPATH' ) || exit;

/* --- Классические корзина и оформление заказа вместо блочных --- */
$pages = array(
	wc_get_page_id( 'cart' )     => '[woocommerce_cart]',
	wc_get_page_id( 'checkout' ) => '[woocommerce_checkout]',
);
foreach ( $pages as $page_id => $shortcode ) {
	if ( $page_id > 0 && get_post_field( 'post_content', $page_id ) !== $shortcode ) {
		wp_update_post( array( 'ID' => $page_id, 'post_content' => $shortcode ) );
		WP_CLI::log( "Страница #{$page_id} → {$shortcode}" );
	}
}

/* --- Зоны доставки --- */
$zones = array(
	array(
		'name'      => 'Россия',
		'locations' => array( array( 'code' => 'RU', 'type' => 'country' ) ),
		'methods'   => array(
			array( 'id' => 'flat_rate', 'settings' => array( 'title' => 'Доставка по России', 'cost' => '490' ) ),
			array( 'id' => 'free_shipping', 'settings' => array( 'title' => 'Бесплатно от 15 000 ₽', 'requires' => 'min_amount', 'min_amount' => '15000' ) ),
			array( 'id' => 'local_pickup', 'settings' => array( 'title' => 'Самовывоз, Москва', 'cost' => '0' ) ),
		),
	),
	array(
		'name'      => 'Весь мир',
		'locations' => array(),
		'methods'   => array(
			array( 'id' => 'flat_rate', 'settings' => array( 'title' => 'Международная доставка', 'cost' => '2900' ) ),
		),
	),
);

foreach ( $zones as $data ) {
	$zone = null;
	foreach ( WC_Shipping_Zones::get_zones() as $existing ) {
		if ( $existing['zone_name'] === $data['name'] ) {
			$zone = new WC_Shipping_Zone( $existing['zone_id'] );
			break;
		}
	}

	if ( ! $zone ) {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( $data['name'] );
		foreach ( $data['locations'] as $location ) {
			$zone->add_location( $location['code'], $location['type'] );
		}
		$zone->save();
	}

	$present = wp_list_pluck( $zone->get_shipping_methods(), 'id' );

	foreach ( $data['methods'] as $method ) {
		if ( in_array( $method['id'], $present, true ) ) {
			continue;
		}
		$instance_id = $zone->add_shipping_method( $method['id'] );
		update_option( "woocommerce_{$method['id']}_{$instance_id}_settings", array_merge(
			array( 'enabled' => 'yes' ),
			$method['settings']
		) );
		WP_CLI::log( "{$data['name']}: {$method['id']} #{$instance_id}" );
	}
}

/* --- Оплата: пока только при получении, онлайн-касса — отдельной фазой --- */
update_option( 'woocommerce_cod_settings', array(
	'enabled'      => 'yes',
	'title'        => 'Оплата при получении',
	'description'  => 'Наличными или картой курьеру.',
	'instructions' => 'Оплата при получении заказа.',
) );

/* --- Оформление заказа --- */
update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_coupons', 'yes' );
update_option( 'woocommerce_enable_order_comments', 'yes' );
update_option( 'woocommerce_checkout_phone_field', 'required' );
update_option( 'woocommerce_checkout_company_field', 'hidden' );
update_option( 'woocommerce_ship_to_destination', 'billing_only' );

$terms = get_page_by_path( 'oferta' );
if ( ! $terms ) {
	$terms_id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_title'   => 'Публичная оферта',
		'post_name'    => 'oferta',
		'post_status'  => 'publish',
		'post_content' => 'Текст оферты будет здесь.',
	) );
} else {
	$terms_id = $terms->ID;
}
update_option( 'woocommerce_terms_page_id', $terms_id );

/* --- Политика конфиденциальности: страница + системная опция WP --- */
$privacy = get_page_by_path( 'privacy-policy' );
if ( $privacy ) {
	wp_update_post( array( 'ID' => $privacy->ID, 'post_title' => 'Политика конфиденциальности', 'post_status' => 'publish' ) );
	update_option( 'wp_page_for_privacy_policy', $privacy->ID );
}

/* --- Русские тексты на чекауте (Woo сохраняет их в опциях при установке) --- */
update_option( 'woocommerce_checkout_privacy_policy_text', 'Ваши данные будут использованы для обработки заказа и поддержки в рамках [privacy_policy].' );
update_option( 'woocommerce_checkout_terms_and_conditions_checkbox_text', 'Я прочитал(а) и принимаю [terms]' );
update_option( 'woocommerce_registration_privacy_policy_text', 'Ваши данные будут использованы для управления аккаунтом в рамках [privacy_policy].' );

/* --- Письма: тёмная шапка, как на сайте --- */
update_option( 'woocommerce_email_base_color', '#0b0b0c' );
update_option( 'woocommerce_email_background_color', '#f4f2ef' );
update_option( 'woocommerce_email_body_background_color', '#ffffff' );
update_option( 'woocommerce_email_text_color', '#1a1a1c' );
update_option( 'woocommerce_email_from_name', get_bloginfo( 'name' ) );
update_option( 'woocommerce_email_footer_text', 'CARPE DIEM 2026 — Style of Soul{n}{site_url}' );

/* --- Промокод для проверки --- */
if ( ! wc_get_coupon_id_by_code( 'CARPE10' ) ) {
	$coupon = new WC_Coupon();
	$coupon->set_code( 'CARPE10' );
	$coupon->set_discount_type( 'percent' );
	$coupon->set_amount( 10 );
	$coupon->set_description( 'Тестовый промокод −10%' );
	$coupon->save();
	WP_CLI::log( 'Промокод CARPE10 создан' );
}

WP_CLI::success( 'Магазин настроен' );
