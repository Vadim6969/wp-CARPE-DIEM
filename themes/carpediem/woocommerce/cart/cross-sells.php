<?php
/**
 * «Рекомендуем» под корзиной.
 * Основано на шаблоне WooCommerce (cart/cross-sells.php).
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>
	<section class="section cross-sells">
		<h2 class="section-title">Рекомендуем</h2>

		<div class="products-scroller">
			<ul class="products columns-<?php echo esc_attr( $columns ); ?>">
				<?php foreach ( $cross_sells as $cross_sell ) : ?>
					<?php
					$post_object = get_post( $cross_sell->get_id() );
					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore
					wc_get_template_part( 'content', 'product' );
					?>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endif;
