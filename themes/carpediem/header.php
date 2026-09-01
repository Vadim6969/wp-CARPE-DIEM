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

<a class="skip-link" href="#content">Перейти к содержимому</a>

<header class="site-header js-header">
	<div class="container site-header__inner">

		<button class="burger js-burger" type="button" aria-expanded="false" aria-controls="site-nav">
			<span></span><span></span><span></span>
			<span class="screen-reader-text">Меню</span>
		</button>

		<nav class="site-nav js-nav" id="site-nav" aria-label="Основное меню">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
		</nav>

		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="site-logo__cross">&#10015;</span>
			<span class="site-logo__name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-logo__cross">&#10015;</span>
			<span class="site-logo__year">2026</span>
		</a>

		<div class="site-header__actions">
			<button class="theme-toggle js-theme-toggle" type="button" aria-label="Переключить тему" title="Светлая / тёмная тема">
				<span class="theme-toggle__sun"><?php echo carpediem_icon( 'sun' ); // phpcs:ignore ?></span>
				<span class="theme-toggle__moon"><?php echo carpediem_icon( 'moon' ); // phpcs:ignore ?></span>
			</button>

			<details class="hsearch">
				<summary><?php echo carpediem_icon( 'search' ); // phpcs:ignore ?><span>Поиск</span></summary>
				<div class="hsearch__panel"><?php get_search_form(); ?></div>
			</details>

			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="hlink" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
					<?php echo carpediem_icon( 'user' ); // phpcs:ignore ?><span>Аккаунт</span>
				</a>
				<a class="hlink" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php echo carpediem_icon( 'cart' ); // phpcs:ignore ?><span>Корзина</span> <?php get_template_part( 'template-parts/cart-count' ); ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</header>

<main class="site-main" id="content">
