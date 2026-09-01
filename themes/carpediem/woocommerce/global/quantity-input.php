<?php
/**
 * Поле количества с кнопками − / +.
 * Основано на шаблоне WooCommerce (global/quantity-input.php).
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

/* translators: %s: Quantity. */
$label = ! empty( $args['product_name'] ) ? sprintf( 'Количество: %s', wp_strip_all_tags( $args['product_name'] ) ) : 'Количество';
?>
<div class="quantity qty">
	<button type="button" class="qty__btn js-qty" data-step="-1" aria-label="Уменьшить количество">&minus;</button>

	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $label ); ?></label>
	<input
		type="number"
		id="<?php echo esc_attr( $input_id ); ?>"
		class="input-text qty__input text"
		name="<?php echo esc_attr( $input_name ); ?>"
		value="<?php echo esc_attr( $input_value ); ?>"
		aria-label="Количество товара"
		size="4"
		min="<?php echo esc_attr( $min_value ); ?>"
		<?php echo $max_value ? 'max="' . esc_attr( $max_value ) . '"' : ''; ?>
		<?php echo ! empty( $step ) ? 'step="' . esc_attr( $step ) . '"' : ''; ?>
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		inputmode="<?php echo esc_attr( $inputmode ); ?>"
		autocomplete="<?php echo esc_attr( isset( $autocomplete ) ? $autocomplete : 'on' ); ?>"
	/>

	<button type="button" class="qty__btn js-qty" data-step="1" aria-label="Увеличить количество">&plus;</button>
</div>
