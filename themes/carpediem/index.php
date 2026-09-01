<?php defined( 'ABSPATH' ) || exit; get_header(); ?>

<div class="container">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<div class="entry-content"><?php is_singular() ? the_content() : the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p>Записей не найдено.</p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
