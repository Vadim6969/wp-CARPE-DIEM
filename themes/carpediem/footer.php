<?php defined( 'ABSPATH' ) || exit; ?>
</main>

<?php get_template_part( 'template-parts/usp-bar' ); ?>

<footer class="site-footer">
	<div class="container site-footer__grid">

		<div class="site-footer__brand">
			<span class="site-footer__name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-footer__year">2026</span>
			<span class="site-footer__cross">&#10015;</span>
			<span class="site-footer__tagline"><?php bloginfo( 'description' ); ?></span>
		</div>

		<nav class="site-footer__col" aria-label="Информация">
			<h2 class="site-footer__title">Информация</h2>
			<?php wp_nav_menu( array( 'theme_location' => 'footer-info', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>

		<nav class="site-footer__col" aria-label="Поддержка">
			<h2 class="site-footer__title">Поддержка</h2>
			<?php wp_nav_menu( array( 'theme_location' => 'footer-support', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>

		<div class="site-footer__col">
			<h2 class="site-footer__title">Социальные сети</h2>
			<ul class="socials">
				<?php foreach ( carpediem_socials() as $slug => $url ) : ?>
					<li><a class="socials__link" href="<?php echo esc_url( $url ); ?>" rel="noopener nofollow" target="_blank">
						<?php echo carpediem_icon( $slug ); // phpcs:ignore ?>
						<span class="screen-reader-text"><?php echo esc_html( $slug ); ?></span>
					</a></li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>

	<p class="site-footer__copy">
		© <?php bloginfo( 'name' ); ?> <?php echo esc_html( wp_date( 'Y' ) ); ?>. Все права защищены.
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
