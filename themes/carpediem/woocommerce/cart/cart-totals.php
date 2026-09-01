<?php
/**
 * Панель «Ваш заказ» справа от таблицы корзины.
 * Основано на шаблоне WooCommerce (cart/cart-totals.php).
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

$cart = WC()->cart;
?>
<div class="cart_totals summary-panel <?php echo $cart->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="summary-panel__title">Ваш заказ</h2>

	<div class="summary-row">
		<span>Товары (<?php echo esc_html( $cart->get_cart_contents_count() ); ?>)</span>
		<span class="summary-row__val"><?php wc_cart_totals_subtotal_html(); ?></span>
	</div>

	<div class="summary-row">
		<span>Доставка</span>
		<span class="summary-row__val">
			<?php if ( $cart->needs_shipping() && $cart->show_shipping() ) : ?>
				<?php wc_cart_totals_shipping_html(); ?>
			<?php else : ?>
				<span class="summary-row__dash">—</span>
			<?php endif; ?>
		</span>
	</div>
	<?php if ( ! ( $cart->needs_shipping() && $cart->show_shipping() ) ) : ?>
		<p class="summary-note">Рассчитывается на следующем шаге</p>
	<?php endif; ?>

	<div class="summary-row">
		<span>Скидка по промокоду</span>
		<span class="summary-row__val">
			<?php if ( $cart->get_coupons() ) : ?>
				<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
					<span class="summary-coupon">
						<?php wc_cart_totals_coupon_html( $coupon ); ?>
					</span>
				<?php endforeach; ?>
			<?php else : ?>
				<span class="summary-row__dash">—</span>
			<?php endif; ?>
		</span>
	</div>

	<?php if ( wc_coupons_enabled() ) : ?>
		<form class="coupon-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<label class="screen-reader-text" for="coupon_code">Промокод</label>
			<input type="text" name="coupon_code" id="coupon_code" class="coupon-form__input" placeholder="Промокод">
			<button type="submit" class="coupon-form__btn" name="apply_coupon" value="Применить">Применить</button>
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
		</form>
	<?php endif; ?>

	<?php foreach ( $cart->get_fees() as $fee ) : ?>
		<div class="summary-row">
			<span><?php echo esc_html( $fee->name ); ?></span>
			<span class="summary-row__val"><?php wc_cart_totals_fee_html( $fee ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

	<div class="summary-total">
		<span>Итого <span class="summary-total__cur"><?php echo esc_html( get_woocommerce_currency() ); ?></span></span>
		<span class="summary-total__val"><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<a class="btn btn--ghost summary-panel__continue" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Продолжить покупки</a>

	<div class="pay-methods">
		<span class="pay-methods__title">Мы принимаем</span>
		<ul class="pay-methods__list">
			<?php foreach ( array( 'Visa', 'MasterCard', 'МИР', 'SBP' ) as $method ) : ?>
				<li><?php echo esc_html( $method ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
