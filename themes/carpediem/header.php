<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container site-header__inner">
		<nav class="site-header__nav">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>

		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="site-header__logo-name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-header__logo-year">2026</span>
		</a>

		<div class="site-header__actions">
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Аккаунт</a>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">Корзина <?php get_template_part( 'template-parts/cart-count' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>

<main class="site-main" id="content">
