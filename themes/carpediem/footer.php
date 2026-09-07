<?php defined( 'ABSPATH' ) || exit; ?>
</main>

<?php get_template_part( 'template-parts/usp-bar' ); ?>

<footer class="site-footer">
	<div class="container site-footer__grid">

		<div class="site-footer__brand">
			<span class="site-footer__name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-footer__year"><?php echo esc_html( carpediem_setting( 'collection' ) ); ?></span>
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
			<h2 class="site-footer__title">На связи</h2>
			<?php $socials = carpediem_socials(); ?>
			<?php get_template_part( 'template-parts/contacts' ); ?>
			<?php if ( ! $socials && ! carpediem_setting( 'email' ) && ! carpediem_setting( 'phone' ) ) : ?><a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>">Связаться с нами ↗</a><?php endif; ?>
			<?php if ( $socials ) : ?><ul class="socials">
				<?php foreach ( $socials as $slug => $url ) : ?>
					<li><a class="socials__link" href="<?php echo esc_url( $url ); ?>" rel="noopener nofollow" target="_blank">
						<?php echo carpediem_icon( $slug ); // phpcs:ignore ?>
						<span class="screen-reader-text"><?php echo esc_html( $slug ); ?></span>
					</a></li>
				<?php endforeach; ?>
			</ul><?php endif; ?>
		</div>

	</div>

	<p class="site-footer__copy">
		© <?php bloginfo( 'name' ); ?> <?php echo esc_html( wp_date( 'Y' ) ); ?>. Все права защищены.
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
