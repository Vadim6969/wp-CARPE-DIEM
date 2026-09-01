<?php
/**
 * «Купить в 1 клик»: имя + телефон, заказ создаётся сразу, менеджер перезванивает.
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

const CARPEDIEM_ONE_CLICK_LIMIT = 5;    // заказов с одного IP
const CARPEDIEM_ONE_CLICK_WINDOW = HOUR_IN_SECONDS;

// Кнопка под «Добавить в корзину».
add_action( 'wp', function () {
	if ( is_product() ) {
		add_action( 'woocommerce_single_product_summary', 'carpediem_one_click_button', 31 );
	}
} );

function carpediem_one_click_button() {
	echo '<button type="button" class="btn btn--ghost one-click__open js-one-click-open">Купить в 1 клик</button>';
}

// Диалог в подвале страницы товара.
add_action( 'wp_footer', function () {
	if ( ! is_product() ) {
		return;
	}

	$privacy_url = get_privacy_policy_url();
	?>
	<dialog class="one-click" id="one-click" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<form class="one-click__form js-one-click-form" method="dialog">
			<h2 class="one-click__title">Купить в 1 клик</h2>
			<p class="one-click__text">Оставь имя и телефон — перезвоним, подтвердим размер и доставку.</p>

			<label class="one-click__label" for="oc-name">Имя</label>
			<input class="one-click__input" type="text" id="oc-name" name="name" required maxlength="60" autocomplete="name">

			<label class="one-click__label" for="oc-phone">Телефон</label>
			<input class="one-click__input" type="tel" id="oc-phone" name="phone" required maxlength="20" placeholder="+7 900 000-00-00" autocomplete="tel">

			<label class="one-click__consent">
				<input type="checkbox" name="consent" required>
				<span>Согласен на обработку персональных данных<?php if ( $privacy_url ) : ?>
					(<a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener">политика</a>)<?php endif; ?>.</span>
			</label>

			<p class="one-click__error js-one-click-error" role="alert" hidden></p>

			<div class="one-click__actions">
				<button type="submit" class="btn js-one-click-submit" formmethod="dialog">Отправить</button>
				<button type="button" class="one-click__cancel js-one-click-close">Отмена</button>
			</div>

			<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'carpediem_one_click' ) ); ?>">
			<input type="hidden" name="product_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
			<input type="hidden" name="variation_id" value="0">
		</form>
	</dialog>
	<?php
} );

// AJAX: создаём заказ.
add_action( 'wp_ajax_carpediem_one_click', 'carpediem_one_click_handler' );
add_action( 'wp_ajax_nopriv_carpediem_one_click', 'carpediem_one_click_handler' );

/**
 * IP посетителя. За обратным прокси (Cloudflare, nginx) настоящий адрес приходит
 * в X-Forwarded-For; доверяем этому заголовку, только если это разрешено явно —
 * иначе лимит обходится подделкой заголовка.
 * На боевом сервере за прокси добавить в wp-config.php: define( 'CARPEDIEM_TRUST_PROXY', true );
 */
function carpediem_client_ip() {
	if ( defined( 'CARPEDIEM_TRUST_PROXY' ) && CARPEDIEM_TRUST_PROXY && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$chain = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		$ip    = filter_var( trim( reset( $chain ) ), FILTER_VALIDATE_IP );

		if ( $ip ) {
			return $ip;
		}
	}

	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
}

/**
 * Проверка данных заявки. Возвращает текст ошибки или null, если всё в порядке.
 * Вынесено отдельно, чтобы проверялось тестом без HTTP-запроса.
 */
function carpediem_one_click_validate( $name, $phone, $consent ) {
	if ( mb_strlen( trim( $name ) ) < 2 ) {
		return 'Укажи имя.';
	}

	$digits = preg_replace( '/\D+/', '', $phone );
	if ( strlen( $digits ) < 10 || strlen( $digits ) > 15 ) {
		return 'Проверь номер телефона.';
	}

	if ( ! $consent ) {
		return 'Нужно согласие на обработку данных.';
	}

	return null;
}

function carpediem_one_click_handler() {
	check_ajax_referer( 'carpediem_one_click', 'nonce' );

	// Ограничение частоты: не больше CARPEDIEM_ONE_CLICK_LIMIT заявок с IP в час.
	$ip  = carpediem_client_ip();
	$key = 'cd_one_click_' . md5( $ip );
	$hits = (int) get_transient( $key );

	if ( $hits >= CARPEDIEM_ONE_CLICK_LIMIT ) {
		wp_send_json_error( array( 'message' => 'Слишком много заявок. Попробуй позже или позвони нам.' ), 429 );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$consent = ! empty( $_POST['consent'] );

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

	$error = carpediem_one_click_validate( $name, $phone, $consent );
	if ( $error ) {
		wp_send_json_error( array( 'message' => $error ), 400 );
	}

	$product = wc_get_product( $variation_id ? $variation_id : $product_id );

	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		wp_send_json_error( array( 'message' => 'Товар недоступен. Выбери размер и цвет.' ), 400 );
	}

	$order = wc_create_order();
	$order->add_product( $product, 1 );
	$order->set_address(
		array(
			'first_name' => $name,
			'phone'      => $phone,
		),
		'billing'
	);
	$order->set_created_via( 'one-click' );
	$order->update_meta_data( '_carpediem_consent', current_time( 'mysql' ) );
	$order->update_meta_data( '_carpediem_consent_ip', $ip );
	$order->calculate_totals();
	$order->update_status( 'on-hold', 'Заявка «Купить в 1 клик». Перезвонить и подтвердить заказ.' );

	set_transient( $key, $hits + 1, CARPEDIEM_ONE_CLICK_WINDOW );

	wp_send_json_success( array(
		'message' => sprintf( 'Заявка №%d принята. Перезвоним в ближайшее время.', $order->get_order_number() ),
	) );
}
