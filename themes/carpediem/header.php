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
<?php
$header_brand = trim( (string) carpediem_setting( 'announcement' ) );
if ( ! $header_brand || 'CARPE DIEM · Style of Soul' === $header_brand ) {
	$header_brand = get_bloginfo( 'name' );
}
?>
<div class="announcement" role="region" aria-label="Название бренда">
	<span class="announcement__cross brand-cross brand-cross--classic" aria-hidden="true"></span>
	<span class="announcement__name"><?php echo esc_html( $header_brand ); ?></span>
	<span class="announcement__cross brand-cross brand-cross--classic" aria-hidden="true"></span>
</div>

<header class="site-header js-header">
	<div class="container site-header__inner">

		<button class="burger js-burger" type="button" aria-expanded="false" aria-controls="site-nav">
			<span></span><span></span><span></span>
			<span class="screen-reader-text">Меню</span>
		</button>
		<a class="site-header__home-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="На главную страницу">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/brand-monogram-line.png' ) ); ?>" width="1024" height="1024" alt="">
		</a>

		<nav class="site-nav js-nav" id="site-nav" aria-label="Основное меню">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
			<div class="mobile-nav-actions">
				<a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>">Личный кабинет ↗</a>
			</div>
		</nav>

		<div class="site-header__actions">
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
