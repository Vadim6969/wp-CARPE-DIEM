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
<?php if ( carpediem_setting( 'announcement' ) ) : ?><div class="announcement" role="region" aria-label="Объявление магазина"><?php echo esc_html( carpediem_setting( 'announcement' ) ); ?></div><?php endif; ?>

<header class="site-header js-header">
	<div class="container site-header__inner">

		<button class="burger js-burger" type="button" aria-expanded="false" aria-controls="site-nav">
			<span></span><span></span><span></span>
			<span class="screen-reader-text">Меню</span>
		</button>

		<nav class="site-nav js-nav" id="site-nav" aria-label="Основное меню">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
			<div class="mobile-nav-actions">
				<a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>">Личный кабинет ↗</a>
				<div class="mobile-nav-actions__theme"><span>Тема</span><?php carpediem_theme_toggle( 'theme-toggle--mobile' ); ?></div>
			</div>
		</nav>

		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="site-logo__cross">&#10015;</span>
			<span class="site-logo__name"><?php bloginfo( 'name' ); ?></span>
			<span class="site-logo__cross">&#10015;</span>
			<span class="site-logo__year">Style of Soul</span>
		</a>

		<div class="site-header__actions">
			<?php carpediem_theme_toggle(); ?>

			<details class="hsearch">
				<summary aria-label="Поиск"><?php echo carpediem_icon( 'search' ); // phpcs:ignore ?><span>Поиск</span></summary>
				<div class="hsearch__panel"><?php get_search_form(); ?></div>
			</details>

			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="hlink hlink--account" aria-label="Личный кабинет" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
					<?php echo carpediem_icon( 'user' ); // phpcs:ignore ?><span>Аккаунт</span>
				</a>
				<a class="hlink hlink--favorites" aria-label="Избранное" href="<?php echo esc_url( home_url( '/favorites/' ) ); ?>"><?php echo carpediem_icon( 'heart' ); ?><span class="screen-reader-text">Избранное</span></a>
				<a class="hlink hlink--cart" aria-label="Корзина" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php echo carpediem_icon( 'cart' ); // phpcs:ignore ?><span>Корзина</span> <?php get_template_part( 'template-parts/cart-count' ); ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</header>

<main class="site-main" id="content">
