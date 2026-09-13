<?php
/** Главная: бренд, подборка вещей, категории и история. */
defined( 'ABSPATH' ) || exit;
get_header();
$hero_image = carpediem_setting( 'hero_image' );
$brand_image = carpediem_setting( 'brand_image' );
?>
<section class="hero hero--editorial">
	<div class="container hero__layout<?php echo $hero_image ? '' : ' hero__layout--text-only'; ?>">
		<div class="hero__copy">
			<p class="eyebrow"><span class="eyebrow__cross" aria-hidden="true">✟</span> <?php echo esc_html( carpediem_setting( 'collection' ) ); ?></p>
			<h1 class="hero__headline"><?php echo esc_html( carpediem_setting( 'hero_title' ) ); ?></h1>
			<p class="hero__description"><?php echo esc_html( carpediem_setting( 'hero_text' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url( carpediem_setting( 'hero_url' ) ?: home_url( '/catalog/' ) ); ?>"><?php echo esc_html( carpediem_setting( 'hero_button' ) ?: 'Смотреть коллекцию' ); ?> <span aria-hidden="true">↗</span></a>
				<a class="text-link" href="#selection">Выбор бренда <span aria-hidden="true">↓</span></a>
			</div>
			<div class="hero__signature"><span>CARPE DIEM</span><span>Style of Soul</span></div>
		</div>
		<?php if ( ! $hero_image ) : ?>
			<div class="hero__ghost" aria-hidden="true"><span>CARPE</span><span>DIEM</span></div>
		<?php endif; ?>
		<?php if ( $hero_image ) : ?>
			<div class="hero__art hero__art--photo">
				<?php echo wp_get_attachment_image( $hero_image, 'large', false, array( 'class' => 'hero__image', 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '(max-width: 899px) 100vw, 50vw' ) ); ?>
				<span class="hero__art-label">CARPE DIEM / <?php echo esc_html( carpediem_setting( 'collection' ) ); ?></span>
				<span class="hero__art-cross" aria-hidden="true">✟</span>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="section home-selection" id="selection">
	<div class="container">
		<div class="section-heading"><div><p class="eyebrow">Вещи со смыслом</p><h2><?php echo esc_html( carpediem_setting( 'selection_title' ) ); ?></h2></div><a class="text-link" href="<?php echo esc_url( home_url( '/catalog/' ) ); ?>">Весь каталог ↗</a></div>
		<div class="woocommerce">
		<?php
		$ids = array_filter( (array) carpediem_setting( 'product_ids' ), function ( $id ) {
			$item = wc_get_product( $id );
			return $item && 'publish' === $item->get_status() && $item->is_visible();
		} );
		if ( $ids ) {
			echo do_shortcode( '[products ids="' . implode( ',', array_map( 'absint', $ids ) ) . '" orderby="post__in" columns="4"]' );
		} else {
			echo do_shortcode( '[products limit="4" columns="4" orderby="date" order="DESC" visibility="visible"]' );
		}
		?>
		</div>
	</div>
</section>

<?php $cats = carpediem_home_categories(); if ( $cats ) : ?>
<section class="section categories" id="collections">
	<div class="container">
		<div class="section-heading"><div><p class="eyebrow">Собери свой образ</p><h2>Категории</h2></div><span class="section-heading__note">Твой стиль. Твой выбор.</span></div>
		<div class="categories__grid">
			<?php foreach ( $cats as $i => $cat ) : $thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true ); ?>
				<a class="cat-card" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
					<span class="cat-card__media"><?php if ( $thumb_id ) { echo wp_get_attachment_image( $thumb_id, 'woocommerce_thumbnail', false, array( 'class' => 'cat-card__img', 'loading' => 'lazy' ) ); } else { echo '<span class="cat-card__placeholder" aria-hidden="true">✟</span>'; } ?></span>
					<span class="cat-card__body"><span class="cat-card__number"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><span class="cat-card__name"><?php echo esc_html( $cat->name ); ?></span><span class="cat-card__arrow" aria-hidden="true">↗</span></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<div class="marquee" aria-hidden="true"><div class="marquee__track">
	<?php for ( $i = 0; $i < 2; $i++ ) : ?><span class="marquee__group"><?php for ( $j = 0; $j < 6; $j++ ) : ?><span>Carpe Diem</span><span class="marquee__cross">✟</span><span>Style of Soul</span><span class="marquee__cross">✟</span><?php endfor; ?></span><?php endfor; ?>
</div></div>

<section class="section brand">
	<div class="container"><div class="brand__inner<?php echo $brand_image ? '' : ' brand__inner--text-only'; ?>">
		<?php if ( $brand_image ) : ?><div class="brand__media"><?php echo wp_get_attachment_image( $brand_image, 'large', false, array( 'class' => 'brand__image', 'loading' => 'lazy' ) ); ?></div><?php endif; ?>
		<div class="brand__body"><p class="eyebrow">Больше, чем одежда</p><h2 class="brand__title"><?php echo esc_html( carpediem_setting( 'brand_title' ) ); ?></h2><p class="brand__text"><?php echo nl2br( esc_html( carpediem_setting( 'brand_text' ) ) ); ?></p><a class="text-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">История бренда ↗</a></div>
	</div></div>
</section>

<section class="community"><div class="container community__inner"><span class="community__symbol" aria-hidden="true">✟</span><h2 class="community__title"><?php echo esc_html( carpediem_setting( 'community_title' ) ); ?></h2><p class="community__text"><?php echo nl2br( esc_html( carpediem_setting( 'community_text' ) ) ); ?></p><?php if ( carpediem_setting( 'telegram' ) ) : ?><a class="btn" href="<?php echo esc_url( carpediem_setting( 'telegram' ) ); ?>" target="_blank" rel="noopener">Мы в Telegram ↗</a><?php else : ?><a class="text-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Ближе к бренду ↗</a><?php endif; ?></div></section>
<?php get_footer(); ?>
