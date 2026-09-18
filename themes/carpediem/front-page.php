<?php
/** Главная: бренд, подборка вещей, категории и история. */
defined( 'ABSPATH' ) || exit;
get_header();
$hero_gallery_images = array(
	array(
		'file' => 'assets/img/hero-gallery/garage.jpg',
		'alt'  => 'Модель в чёрной одежде у автомобиля в гараже',
	),
	array(
		'file' => 'assets/img/hero-gallery/passage.jpg',
		'alt'  => 'Модель в тёмной одежде в бетонном переходе',
	),
	array(
		'file' => 'assets/img/hero-gallery/studio.jpg',
		'alt'  => 'Модель в многослойном чёрном образе в студии',
	),
);
$lookbook_images = array();
for ( $lookbook_index = 1; $lookbook_index <= 4; $lookbook_index++ ) {
	$lookbook_images[] = absint( carpediem_setting( 'lookbook_image_' . $lookbook_index ) );
}
?>
<section class="hero hero--editorial">
	<div class="container hero__layout hero__layout--gallery">
		<div class="hero__copy">
			<p class="eyebrow"><span class="eyebrow__cross brand-cross brand-cross--classic" aria-hidden="true"></span><?php echo esc_html( carpediem_setting( 'collection' ) ); ?></p>
			<h1 class="hero__headline"><?php echo esc_html( carpediem_setting( 'hero_title' ) ); ?></h1>
			<p class="hero__description"><?php echo esc_html( carpediem_setting( 'hero_text' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url( carpediem_setting( 'hero_url' ) ?: home_url( '/catalog/' ) ); ?>"><?php echo esc_html( carpediem_setting( 'hero_button' ) ?: 'Смотреть коллекцию' ); ?> <span aria-hidden="true">↗</span></a>
				<a class="text-link" href="#selection">Выбор бренда <span aria-hidden="true">↓</span></a>
			</div>
			<div class="hero__signature"><span>CARPE DIEM</span><span>Style of Soul</span></div>
		</div>
		<section class="hero-gallery js-hero-gallery" aria-label="Фотографии коллекции" aria-roledescription="карусель">
			<div class="hero-gallery__track js-hero-gallery-track" tabindex="0">
				<?php foreach ( $hero_gallery_images as $hero_gallery_index => $hero_gallery_image ) : ?>
					<figure class="hero-gallery__slide">
						<img class="hero-gallery__image" src="<?php echo esc_url( get_theme_file_uri( $hero_gallery_image['file'] ) ); ?>" width="1122" height="1402" alt="<?php echo esc_attr( $hero_gallery_image['alt'] ); ?>" <?php echo 0 === $hero_gallery_index ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"'; ?>>
						<figcaption>CARPE DIEM / <?php echo esc_html( sprintf( '%02d', $hero_gallery_index + 1 ) ); ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
			<div class="hero-gallery__controls">
				<button class="hero-gallery__control" type="button" data-hero-gallery-direction="-1" aria-label="Предыдущая фотография">←</button>
				<button class="hero-gallery__control" type="button" data-hero-gallery-direction="1" aria-label="Следующая фотография">→</button>
			</div>
		</section>
	</div>
</section>

<section class="section home-selection" id="selection">
	<div class="container">
		<div class="section-heading"><div><h2><?php echo esc_html( carpediem_setting( 'selection_title' ) ); ?></h2></div><a class="text-link" href="<?php echo esc_url( home_url( '/catalog/' ) ); ?>">Весь каталог ↗</a></div>
		<div class="woocommerce">
		<?php
		$ids = array_filter( (array) carpediem_setting( 'product_ids' ), function ( $id ) {
			$item = wc_get_product( $id );
			return $item && 'publish' === $item->get_status() && $item->is_visible();
		} );
		$had_minimal_product_loop = array_key_exists( 'carpediem_minimal_product_loop', $GLOBALS );
		$previous_minimal_product_loop = $GLOBALS['carpediem_minimal_product_loop'] ?? null;
		$GLOBALS['carpediem_minimal_product_loop'] = true;
		if ( $ids ) {
			echo do_shortcode( '[products ids="' . implode( ',', array_map( 'absint', $ids ) ) . '" orderby="post__in" columns="4"]' );
		} else {
			echo do_shortcode( '[products limit="4" columns="4" orderby="date" order="DESC" visibility="visible"]' );
		}
		if ( $had_minimal_product_loop ) {
			$GLOBALS['carpediem_minimal_product_loop'] = $previous_minimal_product_loop;
		} else {
			unset( $GLOBALS['carpediem_minimal_product_loop'] );
		}
		?>
		</div>
	</div>
</section>

<?php $cats = carpediem_home_categories(); if ( $cats ) : ?>
<section class="section categories" id="collections">
	<div class="container">
		<div class="section-heading"><div><h2>Категории</h2></div></div>
		<div class="categories__grid">
			<?php foreach ( $cats as $cat ) : $thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true ); ?>
				<a class="cat-card" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
					<span class="cat-card__media">
						<?php if ( $thumb_id ) { echo wp_get_attachment_image( $thumb_id, 'woocommerce_thumbnail', false, array( 'class' => 'cat-card__img', 'loading' => 'lazy' ) ); } else { echo '<span class="cat-card__placeholder" aria-hidden="true">✟</span>'; } ?>
						<span class="cat-card__body">
							<span class="cat-card__name"><?php echo esc_html( $cat->name ); ?></span>
							<span class="cat-card__link">Перейти <span aria-hidden="true">↗</span></span>
						</span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section home-lookbook" id="lookbook">
	<div class="container">
		<div class="section-heading">
			<div><p class="eyebrow">Люди CARPE DIEM</p><h2><?php echo esc_html( carpediem_setting( 'lookbook_title' ) ); ?></h2></div>
			<a class="text-link" href="<?php echo esc_url( home_url( '/lookbook/' ) ); ?>">Весь лукбук ↗</a>
		</div>
		<p class="home-lookbook__intro"><?php echo esc_html( carpediem_setting( 'lookbook_text' ) ); ?></p>
		<div class="lookbook-rail" aria-label="Фотографии клиентов" tabindex="0">
			<?php foreach ( $lookbook_images as $lookbook_offset => $lookbook_image ) : $lookbook_number = $lookbook_offset + 1; ?>
				<figure class="lookbook-card<?php echo $lookbook_image ? ' lookbook-card--photo' : ' lookbook-card--empty'; ?>">
					<?php if ( $lookbook_image ) : ?>
						<?php echo wp_get_attachment_image( $lookbook_image, 'large', false, array( 'class' => 'lookbook-card__image', 'loading' => 'lazy', 'alt' => sprintf( 'Фото клиента CARPE DIEM %d', $lookbook_number ), 'sizes' => '(max-width: 699px) 82vw, 32vw' ) ); ?>
					<?php else : ?>
						<span class="lookbook-card__placeholder" role="img" aria-label="Место для фотографии клиента <?php echo esc_attr( $lookbook_number ); ?>">
							<span>CLIENT FRAME</span><small><?php echo esc_html( sprintf( '%02d', $lookbook_number ) ); ?></small>
						</span>
					<?php endif; ?>
					<figcaption><span>CARPE DIEM / COMMUNITY</span><strong><?php echo esc_html( sprintf( '%02d', $lookbook_number ) ); ?></strong></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<div class="marquee" aria-hidden="true"><div class="marquee__track">
	<?php for ( $i = 0; $i < 2; $i++ ) : ?><span class="marquee__group"><?php for ( $j = 0; $j < 6; $j++ ) : ?><span>Carpe Diem</span><span class="marquee__cross marquee__cross--classic"></span><span>Style of Soul</span><span class="marquee__cross marquee__cross--massive"></span><?php endfor; ?></span><?php endfor; ?>
</div></div>

<section class="community"><div class="container community__inner"><span class="community__symbol brand-cross brand-cross--massive" aria-hidden="true"></span><h2 class="community__title"><?php echo esc_html( carpediem_setting( 'community_title' ) ); ?></h2><p class="community__text"><?php echo nl2br( esc_html( carpediem_setting( 'community_text' ) ) ); ?></p><?php if ( carpediem_setting( 'telegram' ) ) : ?><a class="btn" href="<?php echo esc_url( carpediem_setting( 'telegram' ) ); ?>" target="_blank" rel="noopener">Мы в Telegram ↗</a><?php else : ?><a class="text-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Ближе к бренду ↗</a><?php endif; ?></div></section>
<?php get_footer(); ?>
