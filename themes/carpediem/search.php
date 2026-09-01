<?php
/**
 * Результаты поиска: товары — сеткой карточек, остальное — списком страниц.
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

get_header();

$product_ids = array();
$other_posts = array();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		if ( 'product' === get_post_type() ) {
			$product_ids[] = get_the_ID();
		} else {
			$other_posts[] = get_the_ID();
		}
	}
}
?>

<div class="container search-page">

	<h1 class="search-page__title">Поиск: «<?php echo esc_html( get_search_query() ); ?>»</h1>

	<form class="search-page__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="search-again">Что ищем</label>
		<input class="search-page__input" type="search" id="search-again" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Название вещи, категория, материал">
		<button class="btn" type="submit">Найти</button>
	</form>

	<?php if ( ! $product_ids && ! $other_posts ) : ?>

		<p class="search-page__empty">Ничего не нашли. Попробуйте другое слово или загляните в <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">каталог</a>.</p>

	<?php else : ?>

		<?php if ( $product_ids ) : ?>
			<section class="section search-page__products">
				<h2 class="section-title">Товары (<?php echo count( $product_ids ); ?>)</h2>

				<?php
				$products = new WP_Query( array(
					'post_type'      => 'product',
					'post__in'       => $product_ids,
					'orderby'        => 'post__in',
					'posts_per_page' => count( $product_ids ),
					'no_found_rows'  => true,
				) );

				if ( $products->have_posts() ) :
					?>
					<div class="woocommerce">
					<ul class="products columns-4">
						<?php
						while ( $products->have_posts() ) {
							$products->the_post();
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
						?>
					</ul>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php if ( $other_posts ) : ?>
			<section class="section search-page__pages">
				<h2 class="section-title">Страницы</h2>
				<ul class="search-list">
					<?php foreach ( $other_posts as $post_id ) : ?>
						<li class="search-list__item">
							<a class="search-list__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
							<p class="search-list__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) ), 24, '…' ) ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
