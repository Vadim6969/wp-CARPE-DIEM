<?php
/** Страница «Коллекции»: отдельный каталог направлений бренда. */
defined( 'ABSPATH' ) || exit;
get_header();
$collections = carpediem_home_categories();
?>

<section class="section collections-page">
	<div class="container">
		<header class="collections-page__heading">
			<p class="eyebrow"><span class="eyebrow__cross brand-cross brand-cross--classic" aria-hidden="true"></span>CARPE DIEM</p>
			<h1>Коллекции</h1>
		</header>

		<?php if ( $collections ) : ?>
			<div class="categories__grid collections-page__grid">
				<?php foreach ( $collections as $collection ) : $thumbnail_id = get_term_meta( $collection->term_id, 'thumbnail_id', true ); ?>
					<a class="cat-card" href="<?php echo esc_url( get_term_link( $collection ) ); ?>">
						<span class="cat-card__media">
							<?php if ( $thumbnail_id ) : ?>
								<?php echo wp_get_attachment_image( $thumbnail_id, 'woocommerce_thumbnail', false, array( 'class' => 'cat-card__img', 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="cat-card__placeholder" aria-hidden="true">✟</span>
							<?php endif; ?>
							<span class="cat-card__body">
								<span class="cat-card__name"><?php echo esc_html( $collection->name ); ?></span>
								<span class="cat-card__link">Перейти <span aria-hidden="true">↗</span></span>
							</span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="collections-page__empty">Коллекции скоро появятся.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
