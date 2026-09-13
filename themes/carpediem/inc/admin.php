<?php
/** Рабочее место магазина и поля контента на штатных API WordPress/WooCommerce. */
defined( 'ABSPATH' ) || exit;

function carpediem_admin_sections() {
	return array(
		'hero' => array( 'Первый экран', 'Сразу покажи характер коллекции и предложи перейти к вещам.', array(
			'announcement' => array( 'Строка над шапкой', 'text' ), 'collection' => array( 'Название коллекции', 'text' ),
			'hero_title' => array( 'Заголовок', 'text' ), 'hero_text' => array( 'Короткое описание', 'textarea' ),
			'hero_button' => array( 'Текст кнопки', 'text' ), 'hero_url' => array( 'Ссылка кнопки', 'url' ),
			'hero_image' => array( 'Фотография коллекции', 'image' ),
		) ),
		'selection' => array( 'Товары и категории', 'Собери подборку для главной и расставь категории в нужном порядке.', array(
			'selection_title' => array( 'Заголовок подборки', 'text' ), 'product_ids' => array( 'Товары на главной', 'products' ),
			'category_ids' => array( 'Категории на главной', 'categories' ),
		) ),
		'brand' => array( 'История бренда', 'Короткий текст и фотография из жизни бренда.', array(
			'brand_title' => array( 'Заголовок', 'text' ), 'brand_text' => array( 'Текст', 'textarea' ),
			'brand_image' => array( 'Изображение', 'image' ), 'community_title' => array( 'Заголовок сообщества', 'text' ),
			'community_text' => array( 'Текст сообщества', 'textarea' ),
		) ),
		'contacts' => array( 'Контакты и условия', 'Контакты появятся в подвале и на странице «Контакты». Условия — рядом с покупкой.', array(
			'phone' => array( 'Телефон', 'tel' ), 'email' => array( 'Email', 'email' ),
			'address' => array( 'Адрес', 'textarea' ), 'hours' => array( 'Время работы', 'text' ),
			'telegram' => array( 'Telegram', 'url' ), 'vk' => array( 'ВКонтакте', 'url' ), 'instagram' => array( 'Instagram', 'url' ),
			'delivery_note' => array( 'Коротко о доставке', 'textarea' ), 'returns_note' => array( 'Коротко об обмене и возврате', 'textarea' ),
		) ),
		'catalog_copy' => array( 'Каталог', 'Названия кнопок и фильтров, которые покупатель видит в списке товаров.', array(
			'catalog_all_label' => array( 'Все товары', 'text' ), 'catalog_filters_label' => array( 'Кнопка фильтров', 'text' ),
			'catalog_filter_apply_label' => array( 'Применить фильтры', 'text' ), 'catalog_filter_reset_label' => array( 'Сбросить фильтры', 'text' ),
			'catalog_filter_clear_label' => array( 'Очистить активные фильтры', 'text' ), 'catalog_add_to_cart_label' => array( 'Добавить простой товар', 'text' ),
			'catalog_buy_label' => array( 'Выбрать и купить', 'text' ), 'catalog_details_label' => array( 'Недоступный товар', 'text' ),
		) ),
		'product_copy' => array( 'Карточка товара', 'Основные действия и информационные блоки на странице вещи.', array(
			'product_new_label' => array( 'Бейдж нового товара', 'text' ), 'product_buy_now_label' => array( 'Основная кнопка покупки', 'text' ),
			'product_size_guide_label' => array( 'Ссылка на размеры', 'text' ), 'product_related_label' => array( 'Заголовок рекомендаций', 'text' ),
			'product_delivery_label' => array( 'Ссылка на доставку', 'text' ), 'product_returns_label' => array( 'Ссылка на возврат', 'text' ),
			'product_share_label' => array( 'Поделиться', 'text' ), 'product_description_label' => array( 'Описание', 'text' ),
			'product_materials_label' => array( 'Материалы и уход', 'text' ), 'product_material_label' => array( 'Состав', 'text' ),
			'product_density_label' => array( 'Плотность', 'text' ), 'product_sizes_label' => array( 'Таблица размеров', 'text' ),
			'product_size_note' => array( 'Примечание под размерами', 'text' ),
		) ),
		'quick_order_copy' => array( 'Заказ по телефону', 'Тексты короткой заявки для покупателя, которому удобнее звонок.', array(
			'quick_order_button_label' => array( 'Кнопка на товаре', 'text' ), 'quick_order_title' => array( 'Заголовок окна', 'text' ),
			'quick_order_text' => array( 'Пояснение', 'textarea' ), 'quick_order_name_label' => array( 'Поле имени', 'text' ),
			'quick_order_phone_label' => array( 'Поле телефона', 'text' ), 'quick_order_consent_label' => array( 'Согласие на обработку данных', 'text' ),
			'quick_order_submit_label' => array( 'Отправить заявку', 'text' ), 'quick_order_cancel_label' => array( 'Отмена', 'text' ),
			'quick_order_success_title' => array( 'Заявка отправлена', 'text' ), 'quick_order_close_label' => array( 'Закрыть окно', 'text' ),
			'quick_order_generic_error' => array( 'Ошибка отправки', 'text' ), 'quick_order_network_error' => array( 'Ошибка сети', 'text' ),
		) ),
		'checkout_copy' => array( 'Оформление заказа', 'Короткие и однозначные формулировки для страницы оформления.', array(
			'checkout_intro_eyebrow' => array( 'Подпись вводного блока', 'text' ), 'checkout_intro_title' => array( 'Заголовок вводного блока', 'text' ),
			'checkout_intro_text' => array( 'Пояснение', 'textarea' ), 'checkout_contact_heading' => array( 'Заголовок контактов', 'text' ),
			'checkout_order_heading' => array( 'Заголовок состава заказа', 'text' ), 'checkout_product_label' => array( 'Колонка товара', 'text' ),
			'checkout_price_label' => array( 'Колонка цены', 'text' ), 'checkout_items_label' => array( 'Сумма товаров', 'text' ),
			'checkout_shipping_label' => array( 'Доставка', 'text' ), 'checkout_total_label' => array( 'Итоговая сумма', 'text' ),
			'checkout_place_order_label' => array( 'Основная кнопка', 'text' ), 'checkout_consent_label' => array( 'Согласие на обработку данных', 'text' ),
			'checkout_name_label' => array( 'Поле имени', 'text' ), 'checkout_name_placeholder' => array( 'Подсказка имени', 'text' ),
			'checkout_phone_label' => array( 'Поле телефона', 'text' ), 'checkout_phone_placeholder' => array( 'Подсказка телефона', 'text' ),
			'checkout_email_label' => array( 'Поле email', 'text' ), 'checkout_email_placeholder' => array( 'Подсказка email', 'text' ),
			'checkout_city_label' => array( 'Поле города', 'text' ), 'checkout_city_placeholder' => array( 'Подсказка города', 'text' ),
			'checkout_address_label' => array( 'Поле адреса', 'text' ), 'checkout_address_placeholder' => array( 'Подсказка адреса', 'text' ),
		) ),
		'cart_copy' => array( 'Корзина', 'Тексты итогов и перехода к оформлению заказа.', array(
			'cart_order_heading' => array( 'Заголовок итогов', 'text' ), 'cart_items_label' => array( 'Товары', 'text' ),
			'cart_shipping_pending_label' => array( 'Доставка ещё не рассчитана', 'text' ), 'cart_total_label' => array( 'Итоговая сумма', 'text' ),
			'cart_checkout_label' => array( 'Перейти к оформлению', 'text' ), 'cart_continue_label' => array( 'Продолжить покупки', 'text' ),
			'cart_payment_note' => array( 'Примечание об оплате', 'textarea' ), 'cart_thanks_title' => array( 'Благодарность — заголовок', 'text' ),
			'cart_thanks_text' => array( 'Благодарность — текст', 'textarea' ),
		) ),
	);
}

add_action( 'admin_menu', function () {
	if ( ! function_exists( 'WC' ) ) { return; }
	add_menu_page( 'CARPE DIEM — магазин', 'CARPE DIEM', 'manage_woocommerce', 'carpediem', 'carpediem_admin_page', 'dashicons-store', 3 );
} );

add_action( 'admin_init', function () {
	register_setting( 'carpediem', 'carpediem_settings', array( 'type' => 'array', 'sanitize_callback' => 'carpediem_sanitize_settings' ) );
} );
add_filter( 'option_page_capability_carpediem', fn() => 'manage_woocommerce' );

function carpediem_sanitize_settings( $input ) {
	$current = get_option( 'carpediem_settings', array() );
	$current = is_array( $current ) ? $current : array();
	if ( ! is_array( $input ) || ! current_user_can( 'manage_woocommerce' ) ) { return $current; }
	foreach ( carpediem_admin_sections() as $section => $config ) {
		foreach ( $config[2] as $key => $field ) {
			if ( ! array_key_exists( $key, $input ) ) { continue; }
			$value = $input[ $key ] ?? '';
			$type = $field[1];
			if ( in_array( $type, array( 'categories', 'products' ), true ) ) {
				$ids = array_values( array_unique( array_filter( array_map( 'absint', is_array( $value ) ? $value : array() ) ) ) );
				$current[ $key ] = array_slice( $ids, 0, 'products' === $type ? 8 : 30 );
				continue;
			}
			if ( ! is_scalar( $value ) ) { continue; }
			if ( 'image' === $type ) {
				$current[ $key ] = wp_attachment_is_image( absint( $value ) ) ? absint( $value ) : 0;
			} elseif ( 'url' === $type ) {
				$url = esc_url_raw( $value, array( 'http', 'https' ) );
				if ( $value && ! $url ) {
					add_settings_error( 'carpediem', $key, 'Проверь ссылку в поле «' . $field[0] . '».' );
				} else { $current[ $key ] = $url; }
			} elseif ( 'email' === $type ) {
				if ( $value && ! is_email( $value ) ) {
					add_settings_error( 'carpediem', $key, 'Проверь email: контакт сохранён без изменений.' );
				} else { $current[ $key ] = sanitize_email( $value ); }
			} else {
				$current[ $key ] = 'textarea' === $type ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
			}
		}
	}
	return $current;
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	$store = 'toplevel_page_carpediem' === $hook;
	$taxonomy = $screen && in_array( $screen->taxonomy, array( 'product_cat', 'pa_color' ), true );
	$product_editor = $screen && 'product' === $screen->post_type && 'post' === $screen->base;
	if ( ! $store && ! $taxonomy && ! $product_editor ) { return; }
	wp_enqueue_style( 'carpediem-admin', get_theme_file_uri( 'assets/css/admin.css' ), array(), carpediem_asset_version( 'assets/css/admin.css' ) );
	wp_enqueue_script( 'carpediem-admin', get_theme_file_uri( 'assets/js/admin.js' ), array( 'jquery' ), carpediem_asset_version( 'assets/js/admin.js' ), true );
	if ( $store ) {
		wp_enqueue_media();
		wp_enqueue_script( 'wc-enhanced-select' );
		wp_enqueue_style( 'woocommerce_admin_styles' );
	}
}, 30 );

/**
 * Понятный маршрут поверх штатного редактора WooCommerce.
 * Ничего не сохраняет самостоятельно и не заменяет поля Woo — только ведёт к ним.
 */
function carpediem_product_editor_guide( $post ) {
	if ( ! $post instanceof WP_Post || 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	$is_new = 'auto-draft' === $post->post_status;
	$product = wc_get_product( $post->ID );
	$has_saved_price = ! $is_new && $product && '' !== $product->get_price();
	$steps = array(
		array( 'name', 'Название', 'Коротко и понятно: тип вещи и название модели.', 'К названию' ),
		array( 'photo', 'Главное фото', 'Вертикальное фото 3:4 лучше всего выглядит в каталоге.', 'Добавить фото' ),
		array( 'category', 'Категория', 'Выберите одну основную категорию: худи, футболки, сумки и т. д.', 'Выбрать категорию' ),
		array( 'purchase', 'Цена и варианты', 'Для вещи с размерами или цветами понадобятся атрибуты и вариации.', 'Настроить продажу' ),
	);
	?>
	<section class="cd-product-guide" data-cd-product-guide data-is-new="<?php echo $is_new ? 'true' : 'false'; ?>" data-purchase-ready="<?php echo $has_saved_price ? 'true' : 'false'; ?>" aria-labelledby="cd-product-guide-title">
		<header class="cd-product-guide__header">
			<div>
				<span class="cd-product-guide__eyebrow">CARPE DIEM / ПОМОЩНИК ТОВАРА</span>
				<h2 id="cd-product-guide-title">Добавьте товар по шагам</h2>
				<p>Заполняйте обычные поля WooCommerce — помощник только показывает порядок и ничего не меняет без вашего действия.</p>
			</div>
			<div class="cd-product-progress" aria-live="polite">
				<strong><span data-cd-product-done>0</span> из <?php echo count( $steps ); ?></strong>
				<span>обязательных шагов</span>
				<i><b data-cd-product-progress></b></i>
			</div>
		</header>

		<?php if ( $is_new ) : ?>
			<div class="cd-product-type-choice" role="group" aria-label="Тип товара">
				<div><strong>Сначала выберите тип</strong><span>Его всегда можно изменить в блоке «Данные товара» до публикации.</span></div>
				<button type="button" class="button" data-cd-product-type="simple"><span aria-hidden="true">1</span> Без размеров и цветов</button>
				<button type="button" class="button" data-cd-product-type="variable"><span aria-hidden="true">S–XL</span> Есть размеры или цвета</button>
			</div>
		<?php endif; ?>

		<ol class="cd-product-steps">
			<?php foreach ( $steps as $index => $step ) : ?>
				<li class="cd-product-step" data-cd-product-check="<?php echo esc_attr( $step[0] ); ?>">
					<span class="cd-product-step__number"><?php echo esc_html( $index + 1 ); ?></span>
					<div><strong><?php echo esc_html( $step[1] ); ?></strong><p data-cd-product-step-copy="<?php echo esc_attr( $step[0] ); ?>"><?php echo esc_html( $step[2] ); ?></p></div>
					<span class="cd-product-step__status" data-cd-product-status>Нужно заполнить</span>
					<button type="button" class="button cd-product-step__action" data-cd-product-section="<?php echo esc_attr( $step[0] ); ?>"><?php echo esc_html( $step[3] ); ?> →</button>
				</li>
			<?php endforeach; ?>
		</ol>

		<footer class="cd-product-guide__footer">
			<p><strong>По желанию:</strong> добавьте подробное описание, галерею, остатки и метку «Новинка».</p>
			<button type="button" class="button button-primary" data-cd-product-section="publish">Проверить и опубликовать →</button>
		</footer>
	</section>
	<?php
}
add_action( 'edit_form_after_title', 'carpediem_product_editor_guide' );

function carpediem_admin_field( $key, $field ) {
	$value = carpediem_setting( $key );
	$name = 'carpediem_settings[' . $key . ']';
	$type = $field[1];
	?>
	<div class="cd-field cd-field--<?php echo esc_attr( $type ); ?>">
		<label for="cd-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea id="cd-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
		<?php elseif ( 'image' === $type ) : ?>
			<div class="cd-media">
				<input type="hidden" id="cd-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo absint( $value ); ?>">
				<div class="cd-media__preview"><?php if ( $value ) { echo wp_get_attachment_image( $value, 'medium' ); } ?></div>
				<button class="button cd-media-pick" type="button">Выбрать изображение</button>
				<button class="button-link cd-media-clear" type="button" <?php echo $value ? '' : 'hidden'; ?>>Убрать</button>
				<p class="description">Фотография вещи или образа. Без изображения используется фирменная монограмма.</p>
			</div>
		<?php elseif ( 'products' === $type ) : ?>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="">
			<select id="cd-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>[]" class="wc-product-search" multiple data-placeholder="Начни вводить название товара…" data-action="woocommerce_json_search_products" data-limit="20" style="width:100%">
				<?php foreach ( (array) $value as $id ) : $item = wc_get_product( $id ); if ( ! $item ) { continue; } ?>
					<option value="<?php echo absint( $id ); ?>" selected><?php echo esc_html( $item->get_name() ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description">До 8 товаров, в порядке выбора. Если список пуст, показываем 4 последние доступные вещи.</p>
		<?php elseif ( 'categories' === $type ) :
			$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array( get_option( 'default_product_cat' ) ) ) );
			$chosen = array_map( fn( $term ) => $term->term_id, carpediem_home_categories() );
			if ( ! is_wp_error( $terms ) ) {
				usort( $terms, function ( $a, $b ) use ( $chosen ) {
					$a_pos = array_search( $a->term_id, $chosen, true ); $b_pos = array_search( $b->term_id, $chosen, true );
					return ( false === $a_pos ? PHP_INT_MAX : $a_pos ) <=> ( false === $b_pos ? PHP_INT_MAX : $b_pos );
				} );
			}
			?>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="">
			<ul class="cd-category-order" id="cd-category_ids">
				<?php foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) : ?>
					<li><label><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo absint( $term->term_id ); ?>" <?php checked( in_array( $term->term_id, $chosen, true ) ); ?>><?php echo esc_html( $term->name ); ?></label>
					<div><button class="button cd-move" type="button" data-direction="up" aria-label="<?php echo esc_attr( 'Поднять: ' . $term->name ); ?>">↑</button> <button class="button cd-move" type="button" data-direction="down" aria-label="<?php echo esc_attr( 'Опустить: ' . $term->name ); ?>">↓</button></div></li>
				<?php endforeach; ?>
			</ul>
			<p class="description">Отметь категории для главной. Стрелки меняют порядок; обложки редактируются в «Товары → Категории».</p>
		<?php else : ?>
			<input id="cd-<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo 'url' === $type ? 'placeholder="https://…"' : ''; ?>>
		<?php endif; ?>
	</div>
	<?php
}

function carpediem_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) { return; }
	$tabs = array( 'overview' => 'Обзор магазина', 'storefront' => 'Оформление сайта', 'copy' => 'Тексты магазина', 'contacts' => 'Контакты и условия', 'guide' => 'Работа с товарами' );
	$section_tabs = array(
		'hero' => 'storefront', 'selection' => 'storefront', 'brand' => 'storefront', 'contacts' => 'contacts',
		'catalog_copy' => 'copy', 'product_copy' => 'copy', 'quick_order_copy' => 'copy', 'checkout_copy' => 'copy', 'cart_copy' => 'copy',
	);
	$tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'overview';
	if ( ! isset( $tabs[ $tab ] ) ) { $tab = 'overview'; }
	?>
	<div class="wrap cd-admin">
		<header class="cd-admin__header"><div><span class="cd-admin__eyebrow">CARPE DIEM / УПРАВЛЕНИЕ МАГАЗИНОМ</span><h1><?php echo esc_html( $tabs[ $tab ] ); ?></h1><p>Вещи, заказы и история бренда — всё под рукой.</p></div><a class="cd-admin__visit" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">Открыть сайт ↗</a></header>
		<nav class="cd-admin__tabs" aria-label="Разделы магазина">
			<?php foreach ( $tabs as $slug => $label ) : ?><a <?php echo $slug === $tab ? 'aria-current="page"' : ''; ?> href="<?php echo esc_url( admin_url( 'admin.php?page=carpediem&tab=' . $slug ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?>
		</nav>
		<?php settings_errors( 'carpediem' ); settings_errors( 'carpediem_settings' ); ?>
		<?php if ( in_array( $tab, array( 'storefront', 'copy', 'contacts' ), true ) ) : ?>
			<form action="options.php" method="post" class="cd-settings-form">
				<?php settings_fields( 'carpediem' ); ?>
				<input type="hidden" name="carpediem_settings[_tab]" value="<?php echo esc_attr( $tab ); ?>">
				<div class="cd-admin__sections">
				<?php foreach ( carpediem_admin_sections() as $section => $config ) : if ( ( $section_tabs[ $section ] ?? 'storefront' ) !== $tab ) { continue; } ?>
					<section class="cd-panel <?php echo 'copy' === $tab ? 'cd-panel--copy' : ''; ?>"><div class="cd-panel__intro"><h2><?php echo esc_html( $config[0] ); ?></h2><p><?php echo esc_html( $config[1] ); ?></p></div><div class="cd-panel__fields"><?php foreach ( $config[2] as $key => $field ) { carpediem_admin_field( $key, $field ); } ?></div></section>
				<?php endforeach; ?>
				</div>
				<div class="cd-savebar"><span class="cd-save-status" role="status">Изменения появятся на сайте после сохранения.</span><?php submit_button( 'Сохранить изменения', 'primary', 'submit', false ); ?></div>
			</form>
		<?php elseif ( 'guide' === $tab ) : carpediem_admin_guide(); ?>
		<?php else : carpediem_admin_overview(); endif; ?>
	</div>
	<?php
}

function carpediem_admin_overview() {
	$orders_url = admin_url( 'admin.php?page=wc-orders' );
	$cards = array(
		array( 'В обработке', wc_orders_count( 'processing' ), $orders_url . '&status=wc-processing', 'Заказы, которые нужно собрать' ),
		array( 'На удержании', wc_orders_count( 'on-hold' ), $orders_url . '&status=wc-on-hold', 'Проверь оплату и заявки в один клик' ),
		array( 'Ожидают оплаты', wc_orders_count( 'pending' ), $orders_url . '&status=wc-pending', 'Заказы с незавершённой оплатой' ),
	);
	?>
	<div class="cd-stats"><?php foreach ( $cards as $card ) : ?><a class="cd-stat" href="<?php echo esc_url( $card[2] ); ?>"><span><?php echo esc_html( $card[0] ); ?></span><strong><?php echo absint( $card[1] ); ?></strong><small><?php echo esc_html( $card[3] ); ?> ↗</small></a><?php endforeach; ?></div>
	<div class="cd-dashboard-grid"><section class="cd-panel cd-panel--stack"><h2>Последние заказы</h2><p>Открой заказ, чтобы посмотреть состав, связаться с покупателем и обновить статус.</p>
		<div class="cd-table-scroll"><table class="widefat striped"><thead><tr><th>Заказ</th><th>Дата</th><th>Статус</th><th>Источник</th><th>Сумма</th></tr></thead><tbody>
		<?php $orders = wc_get_orders( array( 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC' ) ); foreach ( $orders as $order ) : ?>
		<tr><td><a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">#<?php echo esc_html( $order->get_order_number() ); ?></a></td><td><?php echo esc_html( $order->get_date_created() ? wc_format_datetime( $order->get_date_created(), 'd.m.Y' ) : '—' ); ?></td><td><span class="cd-status"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span></td><td><?php echo 'one-click' === $order->get_created_via() ? 'В один клик' : 'Магазин'; ?></td><td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td></tr>
		<?php endforeach; if ( ! $orders ) : ?><tr><td colspan="5">Заказов пока нет. Здесь появятся первые покупки и заявки.</td></tr><?php endif; ?>
		</tbody></table></div><a href="<?php echo esc_url( $orders_url ); ?>">Все заказы →</a>
	</section><section class="cd-panel cd-panel--stack"><h2>Быстрые действия</h2><div class="cd-shortcuts">
		<?php foreach ( array( 'Добавить товар по шагам' => 'post-new.php?post_type=product', 'Товары и цены' => 'edit.php?post_type=product', 'Остатки и наличие' => 'admin.php?page=wc-reports&tab=stock', 'Категории и обложки' => 'edit-tags.php?taxonomy=product_cat&post_type=product', 'Промокоды' => 'edit.php?post_type=shop_coupon', 'Доставка' => 'admin.php?page=wc-settings&tab=shipping', 'Способы оплаты' => 'admin.php?page=wc-settings&tab=checkout' ) as $label => $url ) : ?><a href="<?php echo esc_url( admin_url( $url ) ); ?>"><?php echo esc_html( $label ); ?><span>↗</span></a><?php endforeach; ?>
	</div></section></div>
	<?php
}

function carpediem_admin_guide() {
	$items = array(
		array( '01', 'Фотографии и описание', 'В карточке товара добавь основное изображение, галерею и описание. Вертикальные фотографии 3:4 смотрятся в каталоге лучше всего. Короткое описание выводится под галереей.', 'post-new.php?post_type=product', 'Добавить товар' ),
		array( '02', 'Размеры, цвета и остатки', 'В «Данные товара → Атрибуты» выбери размер и цвет, затем создай вариации. Для каждой вариации укажи цену и остаток. Состав, плотность, уход и особенности заполняются в тех же атрибутах.', 'edit.php?post_type=product&page=product_attributes', 'Открыть атрибуты' ),
		array( '03', 'Размерные таблицы', 'Открой категорию и заполни таблицу мерок. Она появится на страницах товаров этой категории. Пустой столбец не выводится; пустая таблица скрывает блок.', 'edit-tags.php?taxonomy=product_cat&post_type=product', 'Настроить таблицы' ),
		array( '04', 'Оттенки на витрине', 'В значениях атрибута «Цвет» укажи HEX-код. Для комбинированного цвета можно задать второй оттенок. Кнопки выбора обновятся на сайте после сохранения.', 'edit-tags.php?taxonomy=pa_color&post_type=product', 'Настроить цвета' ),
		array( '05', 'Новинки и рекомендации', 'Метка «новинка» добавляет бейдж. Подборка главной настраивается в «Оформление сайта». Блок «С этим товаром покупают» берёт товары из «Сопутствующие → Кросселлы».', 'admin.php?page=carpediem&tab=storefront', 'Собрать подборку' ),
	);
	?><div class="cd-guide"><?php foreach ( $items as $item ) : ?><section class="cd-panel cd-panel--stack"><span class="cd-guide__number"><?php echo esc_html( $item[0] ); ?></span><h2><?php echo esc_html( $item[1] ); ?></h2><p><?php echo esc_html( $item[2] ); ?></p><a href="<?php echo esc_url( admin_url( $item[3] ) ); ?>"><?php echo esc_html( $item[4] ); ?> →</a></section><?php endforeach; ?></div><?php
}

// Ссылка на рабочее место доступна сразу после входа в стандартную консоль.
add_action( 'wp_dashboard_setup', function () {
	if ( ! current_user_can( 'manage_woocommerce' ) ) { return; }
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	wp_add_dashboard_widget( 'carpediem_dashboard', 'CARPE DIEM — управление магазином', function () {
		echo '<p>Заказы, оформление витрины, контакты и работа с товарами.</p><p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=carpediem' ) ) . '">Открыть панель магазина</a></p>';
	} );
} );

/** Редактор мерок прямо в категории. Храним структурированные строки, без HTML/CSV. */
function carpediem_size_table_fields( $term = null ) {
	$table = $term instanceof WP_Term ? carpediem_category_size_table( $term ) : null;
	$head = array_pad( $table['head'] ?? array( 'Размер', 'Длина', 'Грудь', 'Плечи', 'Рукав' ), 5, '' );
	$rows = $table['rows'] ?? array( array_fill( 0, 5, '' ) );
	wp_nonce_field( 'carpediem_term', 'carpediem_term_nonce' );
	?><div class="cd-size-editor"><p>Мерки в сантиметрах. Пустые столбцы и строки не выводятся на сайте.</p><div class="cd-table-scroll"><table><thead><tr>
	<?php foreach ( $head as $col => $label ) : ?><th><input aria-label="<?php echo esc_attr( 'Название столбца ' . ( $col + 1 ) ); ?>" name="cd_size[head][]" value="<?php echo esc_attr( $label ); ?>"></th><?php endforeach; ?><th></th></tr></thead><tbody>
	<?php foreach ( $rows as $index => $row ) : ?><tr><?php foreach ( array_pad( $row, 5, '' ) as $col => $value ) : ?><td><input aria-label="<?php echo esc_attr( 'Строка ' . ( $index + 1 ) . ', столбец ' . ( $col + 1 ) ); ?>" name="cd_size[rows][<?php echo absint( $index ); ?>][]" value="<?php echo esc_attr( $value ); ?>"></td><?php endforeach; ?><td><button class="button cd-row-remove" type="button" aria-label="Удалить строку">×</button></td></tr><?php endforeach; ?>
	</tbody></table></div><button class="button cd-row-add" type="button">Добавить размер</button></div><?php
}
add_action( 'product_cat_edit_form_fields', function ( $term ) {
	echo '<tr><th>Размерная таблица</th><td>'; carpediem_size_table_fields( $term ); echo '</td></tr>';
} );
add_action( 'product_cat_add_form_fields', function () {
	echo '<div class="form-field"><h3>Размерная таблица</h3>'; carpediem_size_table_fields(); echo '</div>';
} );

function carpediem_save_category_table( $term_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) || ! isset( $_POST['carpediem_term_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['carpediem_term_nonce'] ) ), 'carpediem_term' ) ) { return; }
	$data = isset( $_POST['cd_size'] ) && is_array( $_POST['cd_size'] ) ? wp_unslash( $_POST['cd_size'] ) : array();
	$head = array(); $indexes = array();
	foreach ( array_slice( (array) ( $data['head'] ?? array() ), 0, 5 ) as $i => $label ) {
		$label = is_scalar( $label ) ? sanitize_text_field( $label ) : '';
		if ( '' !== $label ) { $head[] = $label; $indexes[] = $i; }
	}
	$rows = array();
	foreach ( array_slice( (array) ( $data['rows'] ?? array() ), 0, 30 ) as $row ) {
		if ( ! is_array( $row ) ) { continue; }
		$clean = array_map( fn( $i ) => isset( $row[$i] ) && is_scalar( $row[$i] ) ? sanitize_text_field( $row[$i] ) : '', $indexes );
		if ( array_filter( $clean, fn( $cell ) => '' !== $cell ) ) { $rows[] = $clean; }
	}
	update_term_meta( $term_id, 'carpediem_size_table', $head && $rows ? array( 'head' => $head, 'rows' => $rows ) : array() );
}
add_action( 'edited_product_cat', 'carpediem_save_category_table' );
add_action( 'created_product_cat', 'carpediem_save_category_table' );

function carpediem_color_fields( $term = null ) {
	$pair = $term instanceof WP_Term ? carpediem_term_color( $term ) : array( '#888888', '' );
	wp_nonce_field( 'carpediem_term', 'carpediem_term_nonce' );
	?><div class="cd-color-fields"><label>Основной оттенок <input type="color" name="cd_color" value="<?php echo esc_attr( $pair[0] ); ?>"></label><label>Второй оттенок (необязательно) <input type="text" name="cd_color_secondary" placeholder="#6b4a2f" pattern="#[a-fA-F0-9]{6}" value="<?php echo esc_attr( $pair[1] ); ?>"></label><p class="description">Оставь второе поле пустым для однотонного цвета.</p></div><?php
}
add_action( 'pa_color_edit_form_fields', function ( $term ) { echo '<tr><th>Цвет на витрине</th><td>'; carpediem_color_fields( $term ); echo '</td></tr>'; } );
add_action( 'pa_color_add_form_fields', function () { echo '<div class="form-field"><h3>Цвет на витрине</h3>'; carpediem_color_fields(); echo '</div>'; } );
function carpediem_save_color( $term_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) || ! isset( $_POST['carpediem_term_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['carpediem_term_nonce'] ) ), 'carpediem_term' ) ) { return; }
	foreach ( array( 'cd_color' => 'carpediem_color', 'cd_color_secondary' => 'carpediem_color_secondary' ) as $input => $meta ) {
		if ( isset( $_POST[ $input ] ) && is_string( $_POST[ $input ] ) ) { update_term_meta( $term_id, $meta, sanitize_hex_color( wp_unslash( $_POST[ $input ] ) ) ?: '' ); }
	}
}
add_action( 'edited_pa_color', 'carpediem_save_color' );
add_action( 'created_pa_color', 'carpediem_save_color' );
