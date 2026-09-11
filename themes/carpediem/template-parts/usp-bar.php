<?php
/**
 * Полоса преимуществ. Используется на всех страницах (выводится из footer.php).
 */
defined( 'ABSPATH' ) || exit;

$items = array(
	array(
		'icon'  => '<path d="M12 2v20M6 8h12"/>',
		'title' => 'Стиль и дух',
		'text'  => 'Каждая деталь имеет значение.',
	),
	array(
		'icon'  => '<path d="M12 2l8 3v7c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V5z"/><path d="M12 7v9M8.5 11h7"/>',
		'title' => 'Качество превыше всего',
		'text'  => 'Премиальные материалы и внимание к деталям.',
	),
	array(
		'icon'  => '<path d="M12 2v20M5 7h14M8 12h8"/>',
		'title' => 'Для тех, кто понимает',
		'text'  => 'Мы не следуем трендам. Мы создаём их.',
	),
	array(
		'icon'  => '<path d="M3 7l9-4 9 4v10l-9 4-9-4z"/><path d="M3 7l9 4 9-4M12 11v10"/>',
		'title' => 'Быстрая доставка',
		'text'  => 'По всей России и миру. Надёжно и вовремя.',
	),
);
?>
<section class="usp" aria-label="Преимущества магазина">
	<div class="container usp__grid">
		<?php foreach ( $items as $item ) : ?>
			<div class="usp__item">
				<svg class="usp__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $item['icon']; // phpcs:ignore ?></svg>
				<div>
					<h3 class="usp__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="usp__text"><?php echo esc_html( $item['text'] ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
