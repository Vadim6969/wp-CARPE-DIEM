<?php defined( 'ABSPATH' ) || exit; ?>
</main>

<footer class="site-footer">
	<div class="container site-footer__inner">
		<div class="site-footer__brand">
			<span class="site-footer__name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-footer__year">2026</span>
		</div>
		<nav class="site-footer__col">
			<h2 class="site-footer__title">Информация</h2>
			<?php wp_nav_menu( array( 'theme_location' => 'footer-info', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>
		<nav class="site-footer__col">
			<h2 class="site-footer__title">Поддержка</h2>
			<?php wp_nav_menu( array( 'theme_location' => 'footer-support', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>
	</div>
	<p class="site-footer__copy">© <?php bloginfo( 'name' ); ?> <?php echo esc_html( date( 'Y' ) ); ?>. Все права защищены.</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
