<?php
defined( 'ABSPATH' ) || exit;

$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<span class="js-cart-count">(<?php echo esc_html( $count ); ?>)</span>
