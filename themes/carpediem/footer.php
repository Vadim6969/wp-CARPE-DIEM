<?php defined( 'ABSPATH' ) || exit; ?>
</main>

<?php get_template_part( 'template-parts/usp-bar' ); ?>

<?php
$footer_collection = trim( (string) carpediem_setting( 'collection' ) );
$footer_year       = wp_date( 'Y' );
if ( preg_match( '/\b(20\d{2})\b/u', $footer_collection, $footer_year_match ) ) {
	$footer_year = $footer_year_match[1];
}
$footer_socials = carpediem_socials();
$social_labels  = array(
	'instagram' => 'Instagram',
	'telegram'  => 'Telegram',
	'tiktok'    => 'TikTok',
	'vk'        => 'ВКонтакте',
);
?>

<footer class="site-footer">
	<div class="container site-footer__grid">

		<div class="site-footer__brand">
			<div class="site-footer__logo">
				<?php if ( has_custom_logo() ) : ?>
					<?php echo get_custom_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<span class="site-footer__logo-placeholder" aria-hidden="true">&#10015;</span>
				<?php endif; ?>
			</div>
			<span class="site-footer__name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-footer__year"><?php echo esc_html( $footer_year ); ?></span>
			<?php if ( $footer_socials ) : ?>
				<ul class="socials" aria-label="Социальные сети">
					<?php foreach ( $footer_socials as $slug => $url ) : $social_label = $social_labels[ $slug ] ?? ucfirst( $slug ); ?>
						<li><a class="socials__link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $social_label ); ?>" rel="noopener nofollow" target="_blank">
							<?php echo carpediem_icon( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="site-footer__col" aria-label="Информация">
			<h2 class="site-footer__title">Информация</h2>
			<?php wp_nav_menu( array( 'theme_location' => 'footer-info', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>

		<nav class="site-footer__col" aria-label="Поддержка">
			<h2 class="site-footer__title">Поддержка</h2>
			<?php wp_nav_menu( array( 'theme_location' => 'footer-support', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>

	</div>

	<p class="site-footer__copy">
		© <?php bloginfo( 'name' ); ?> <?php echo esc_html( wp_date( 'Y' ) ); ?>. Все права защищены.
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
