<?php
/**
 * Блок философии бренда. Выводится на всех страницах перед подвалом.
 */
defined( 'ABSPATH' ) || exit;

$items = array(
	array(
		'image' => 'style-of-soul.jpg',
		'title' => 'Стиль души',
		'text'  => 'Одежда — это отражение того, кем ты являешься.',
	),
	array(
		'image' => 'be-yourself.jpg',
		'title' => 'Будь собой',
		'text'  => 'Не подстраивайся под окружающих. Создавай свой собственный стиль.',
	),
	array(
		'image' => 'seize-the-moment.jpg',
		'title' => 'Лови момент',
		'text'  => 'Не откладывай жизнь на потом. Живи сейчас.',
	),
	array(
		'image' => 'your-story.jpg',
		'title' => 'Твоя история',
		'text'  => 'Каждый человек проходит свой жизненный путь. Мы хотим быть частью твоего пути.',
	),
);
?>
<section class="usp philosophy" aria-labelledby="philosophy-title">
	<div class="container usp__inner">
		<header class="usp__heading">
			<span class="usp__heading-line" aria-hidden="true"></span>
			<h2 id="philosophy-title">Наша философия</h2>
			<span class="usp__heading-line" aria-hidden="true"></span>
		</header>

		<div class="usp__grid">
			<?php foreach ( $items as $index => $item ) : ?>
				<article class="usp__item">
					<span class="usp__icon" aria-hidden="true">
						<img class="usp__icon-image usp__icon-image--<?php echo esc_attr( $index + 1 ); ?>" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/philosophy/' . $item['image'] ) ); ?>" width="128" height="128" loading="lazy" alt="">
					</span>
					<h3 class="usp__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="usp__text"><?php echo esc_html( $item['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="usp__ornament" aria-hidden="true">
			<span></span><b class="brand-cross brand-cross--massive"></b><span></span>
		</div>
	</div>
</section>
