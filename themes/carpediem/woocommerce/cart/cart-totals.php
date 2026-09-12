<?php
/** Итоги корзины. Структура таблицы совместима с доставкой и AJAX WooCommerce.
 * @version 2.3.6
 */
defined( 'ABSPATH' ) || exit;
$cart = WC()->cart;
?>
<div class="cart_totals summary-panel <?php echo $cart->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">
	<?php do_action( 'woocommerce_before_cart_totals' ); ?>
	<h2 class="summary-panel__title"><?php echo esc_html( carpediem_setting( 'cart_order_heading' ) ); ?></h2>
	<table class="summary-table"><tbody>
		<tr><th><?php echo esc_html( carpediem_setting( 'cart_items_label' ) ); ?> (<?php echo esc_html( $cart->get_cart_contents_count() ); ?>)</th><td><?php wc_cart_totals_subtotal_html(); ?></td></tr>
		<?php if ( $cart->needs_shipping() && $cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); wc_cart_totals_shipping_html(); do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
		<?php elseif ( $cart->needs_shipping() ) : ?>
			<tr><th><?php echo esc_html( carpediem_setting( 'checkout_shipping_label' ) ); ?></th><td><?php echo esc_html( carpediem_setting( 'cart_shipping_pending_label' ) ); ?></td></tr>
		<?php endif; ?>
		<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?><tr class="cart-discount"><th><?php wc_cart_totals_coupon_label( $coupon ); ?></th><td><?php wc_cart_totals_coupon_html( $coupon ); ?></td></tr><?php endforeach; ?>
		<?php foreach ( $cart->get_fees() as $fee ) : ?><tr><th><?php echo esc_html( $fee->name ); ?></th><td><?php wc_cart_totals_fee_html( $fee ); ?></td></tr><?php endforeach; ?>
		<?php if ( wc_tax_enabled() && ! $cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( $cart->get_tax_totals() as $tax ) : ?><tr><th><?php echo esc_html( $tax->label ); ?></th><td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td></tr><?php endforeach; ?>
			<?php else : ?><tr><th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th><td><?php wc_cart_totals_taxes_total_html(); ?></td></tr><?php endif; ?>
		<?php endif; ?>
		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
		<tr class="summary-table__total"><th><?php echo esc_html( carpediem_setting( 'cart_total_label' ) ); ?></th><td><?php wc_cart_totals_order_total_html(); ?></td></tr>
		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
	</tbody></table>
	<?php if ( wc_coupons_enabled() ) : ?>
		<details class="coupon-details"><summary>Есть промокод?</summary><form class="coupon-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<label class="screen-reader-text" for="coupon_code">Промокод</label><input type="text" name="coupon_code" id="coupon_code" class="coupon-form__input" placeholder="Промокод">
			<button type="submit" class="coupon-form__btn" name="apply_coupon" value="Применить">Применить</button>
			<input type="hidden" name="woocommerce-cart-nonce" value="<?php echo esc_attr( wp_create_nonce( 'woocommerce-cart' ) ); ?>">
		</form></details>
	<?php endif; ?>
	<div class="wc-proceed-to-checkout"><?php do_action( 'woocommerce_proceed_to_checkout' ); ?></div>
	<a class="text-link summary-panel__continue" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( carpediem_setting( 'cart_continue_label' ) ); ?> ↗</a>
	<p class="summary-payment-note"><?php echo esc_html( carpediem_setting( 'cart_payment_note' ) ); ?></p>
	<?php do_action( 'woocommerce_after_cart_totals' ); ?>
</div>
