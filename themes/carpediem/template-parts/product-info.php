<?php
/**
 * Три информационные колонки под карточкой товара:
 * описание / материалы и уход / таблица размеров. На мобиле — аккордеоны.
 */
defined( 'ABSPATH' ) || exit;

global $product;

$description = $product->get_description();
$material    = wc_get_product_terms( $product->get_id(), 'pa_material', array( 'fields' => 'names' ) );
$density     = wc_get_product_terms( $product->get_id(), 'pa_density', array( 'fields' => 'names' ) );
$care        = wc_get_product_terms( $product->get_id(), 'pa_care', array( 'fields' => 'names' ) );
$sizes       = carpediem_size_table( $product );
$density     = array_filter( (array) $density, fn( $v ) => '—' !== $v );
?>
<section class="section product-info">
	<div class="container product-info__grid">

		<?php if ( $description ) : ?>
			<details class="info-col" open>
				<summary class="info-col__title"><?php echo esc_html( carpediem_setting( 'product_description_label' ) ); ?></summary>
				<div class="info-col__body"><?php echo wpautop( wp_kses_post( $description ) ); ?></div>
			</details>
		<?php endif; ?>

		<?php if ( $material || $care ) : ?>
			<details class="info-col" open>
				<summary class="info-col__title"><?php echo esc_html( carpediem_setting( 'product_materials_label' ) ); ?></summary>
				<div class="info-col__body">
					<?php if ( $material ) : ?>
						<p><?php echo esc_html( carpediem_setting( 'product_material_label' ) ); ?>: <span class="info-col__val"><?php echo esc_html( implode( ', ', $material ) ); ?></span></p>
					<?php endif; ?>
					<?php if ( $density ) : ?>
						<p><?php echo esc_html( carpediem_setting( 'product_density_label' ) ); ?>: <span class="info-col__val"><?php echo esc_html( implode( ', ', $density ) ); ?></span></p>
					<?php endif; ?>
					<?php if ( $care ) : ?>
						<ul class="care">
							<?php foreach ( $care as $rule ) : ?>
								<li><?php echo carpediem_care_icon( $rule ); // phpcs:ignore ?><?php echo esc_html( $rule ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</details>
		<?php endif; ?>

		<?php if ( $sizes ) : ?>
			<details class="info-col" id="product-sizes" open>
				<summary class="info-col__title"><?php echo esc_html( carpediem_setting( 'product_sizes_label' ) ); ?></summary>
				<div class="info-col__body">
					<div class="size-table__scroll" role="region" aria-label="Таблица размеров" tabindex="0">
						<table class="size-table">
							<thead>
								<tr><?php foreach ( $sizes['head'] as $th ) : ?><th><?php echo esc_html( $th ); ?></th><?php endforeach; ?></tr>
							</thead>
							<tbody>
								<?php foreach ( $sizes['rows'] as $row ) : ?>
									<tr><?php foreach ( $row as $td ) : ?><td><?php echo esc_html( $td ); ?></td><?php endforeach; ?></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p class="size-table__note"><?php echo esc_html( carpediem_setting( 'product_size_note' ) ); ?></p>
				</div>
			</details>
		<?php endif; ?>

	</div>
</section>
