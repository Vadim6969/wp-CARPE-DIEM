<?php defined( 'ABSPATH' ) || exit; get_header(); ?>

<div class="container">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$page_title = function_exists( 'is_account_page' ) && is_account_page()
			? 'Личный кабинет'
			: get_the_title();
		?>
		<article <?php post_class(); ?>>
			<?php if ( function_exists( 'is_cart' ) && is_cart() ) : ?>
				<header class="commerce-page-hero commerce-page-hero--cart">
					<?php carpediem_commerce_brand(); ?>
					<h1 class="entry-title"><?php echo esc_html( $page_title ); ?></h1>
				</header>
			<?php else : ?>
				<h1 class="entry-title"><?php echo esc_html( $page_title ); ?></h1>
			<?php endif; ?>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
