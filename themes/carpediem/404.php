<?php defined( 'ABSPATH' ) || exit; get_header(); ?>

<section class="container error-404">
	<span class="error-404__cross" aria-hidden="true">&#10015;</span>
	<h1 class="error-404__code">404</h1>
	<p class="error-404__text">Такой страницы нет. Возможно, вещь ушла из наличия или ссылка устарела.</p>
	<div class="error-404__actions">
		<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">На главную</a>
		<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
			<a class="btn btn--ghost" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">В каталог</a>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
