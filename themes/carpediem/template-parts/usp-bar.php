<?php
/**
 * Блок философии бренда. Выводится на всех страницах перед подвалом.
 */
defined( 'ABSPATH' ) || exit;

$items = array(
	array(
		'icon'  => '<path d="M12 2v20M6 8h12"/>',
		'title' => 'Стиль души',
		'text'  => 'Одежда — это отражение того, кем ты являешься.',
	),
	array(
		'icon'  => '<path d="M12 2l1.8 7.2L21 12l-7.2 1.8L12 22l-1.8-8.2L3 12l7.2-2.8z"/><path d="M12 2v20M3 12h18"/>',
		'title' => 'Будь собой',
		'text'  => 'Не подстраивайся под окружающих. Создавай свой собственный стиль.',
	),
	array(
		'icon'  => '<path d="M6 3h12M6 21h12M7 3c0 5 2 6 5 9-3 3-5 4-5 9M17 3c0 5-2 6-5 9 3 3 5 4 5 9"/>',
		'title' => 'Лови момент',
		'text'  => 'Не откладывай жизнь на потом. Живи сейчас.',
	),
	array(
		'icon'  => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.6 2.5 4 5.5 4 9s-1.4 6.5-4 9c-2.6-2.5-4-5.5-4-9s1.4-6.5 4-9"/>',
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
					<?php if ( 0 === $index ) : ?>
						<span class="usp__icon brand-cross brand-cross--classic" aria-hidden="true"></span>
					<?php else : ?>
						<svg class="usp__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
					<?php endif; ?>
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
