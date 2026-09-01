<?php
/**
 * Главная страница.
 * Тексты пока в шаблоне — выносим в опции, когда заказчик захочет править сам.
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>

<section class="hero">
	<div class="hero__bg" aria-hidden="true">
		<svg class="hero__bolt hero__bolt--left" viewBox="0 0 100 400" fill="none">
			<path d="M66 0 46 92 60 100 40 210 54 218 26 400" stroke="rgba(255,255,255,.8)" stroke-width="1.3"/>
			<path d="M52 104 76 168" stroke="rgba(255,255,255,.4)" stroke-width=".9"/>
		</svg>
		<svg class="hero__bolt hero__bolt--right" viewBox="0 0 100 400" fill="none">
			<path d="M34 0 54 92 40 100 60 210 46 218 74 400" stroke="rgba(255,255,255,.8)" stroke-width="1.3"/>
			<path d="M48 104 24 168" stroke="rgba(255,255,255,.4)" stroke-width=".9"/>
		</svg>
	</div>
	<div class="container hero__inner">
		<?php get_template_part( 'template-parts/monogram', null, array( 'class' => 'hero__mark' ) ); ?>
		<h1 class="hero__title"><?php bloginfo( 'name' ); ?></h1>
		<p class="hero__year">2026</p>
		<span class="hero__cross">&#10015;</span>
	</div>
</section>

<section class="community">
	<span class="community__mark community__mark--left">&#43; Unity</span>
	<span class="community__mark community__mark--right">Freedom &#43;</span>
	<div class="container community__inner">
		<h2 class="community__title">Спасибо за то,<br>что ты с нами</h2>
		<p class="community__sub">Thank you for joining our community</p>
		<p class="community__text">
			Ты стал частью чего-то большего.<br>
			Мы не просто бренд — мы объединение людей,<br>
			которые ценят стиль, свободу и смысл.
		</p>
		<a class="btn" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Читать дальше <span class="btn__cross">&#10015;</span></a>
	</div>
</section>

<div class="marquee" aria-hidden="true">
	<div class="marquee__track">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<span class="marquee__group">
				<?php for ( $j = 0; $j < 6; $j++ ) : ?>
					<span>Carpe Diem</span><span class="marquee__cross">&#10015;</span><span>Style of Soul</span><span class="marquee__cross">&#10015;</span>
				<?php endfor; ?>
			</span>
		<?php endfor; ?>
	</div>
</div>

<?php
$cats = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
	'orderby'    => 'id', // порядок создания категорий = порядок в сетке
	'number'     => 6,
) );

if ( ! is_wp_error( $cats ) && $cats ) : ?>
	<section class="section categories">
		<div class="container">
			<h2 class="section-title">Категории</h2>
			<div class="categories__grid">
				<?php foreach ( $cats as $cat ) :
					$thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
					?>
					<a class="cat-card" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
						<span class="cat-card__media">
							<?php if ( $thumb_id ) : ?>
								<?php echo wp_get_attachment_image( $thumb_id, 'large', false, array( 'class' => 'cat-card__img', 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="cat-card__placeholder" aria-hidden="true">&#10015;</span>
							<?php endif; ?>
						</span>
						<span class="cat-card__body">
							<span class="cat-card__name"><?php echo esc_html( $cat->name ); ?></span>
							<span class="cat-card__link">Перейти <span class="btn__cross">&#10015;</span></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section brand">
	<div class="container brand__inner">
		<div class="brand__media">
			<?php get_template_part( 'template-parts/monogram', null, array( 'class' => 'brand__mark' ) ); ?>
		</div>
		<div class="brand__body">
			<h2 class="brand__title">Carpe Diem —<br>Style of Soul</h2>
			<p class="brand__text">
				Каждая вещь — это больше, чем одежда.<br>
				Это часть пути, которую мы создаём вместе.
			</p>
			<a class="btn" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Узнать нашу историю <span class="btn__cross">&#10015;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
