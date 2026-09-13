<?php
/**
 * Органичная готическая монограмма CARPE DIEM.
 * Один непрерывный знак без рамки и геометрического герба — одинаково
 * читается в тёмной и светлой темах и остаётся резким на малых размерах.
 */
defined( 'ABSPATH' ) || exit;

$class = isset( $args['class'] ) ? $args['class'] : '';
$gradient_id = 'cd-silver-' . wp_unique_id();
?>
<svg class="monogram <?php echo esc_attr( $class ); ?>" viewBox="0 0 180 220" fill="none" aria-hidden="true" focusable="false">
	<defs>
		<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>" x1="0" y1="0" x2="0" y2="1">
			<stop offset="0%" stop-color="var(--mark-1)" stop-opacity=".98"/>
			<stop offset="52%" stop-color="var(--mark-2)" stop-opacity=".92"/>
			<stop offset="100%" stop-color="var(--mark-3)" stop-opacity=".92"/>
		</linearGradient>
	</defs>

	<g stroke="url(#<?php echo esc_attr( $gradient_id ); ?>)" stroke-width="3.1" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<!-- flowing thorned stem -->
		<path d="M91 22c-8 12-8 24-2 36 7 15 7 27 1 40-7 16-7 29 0 43 7 14 7 27 0 43"/>
		<path d="M89 29c-6-5-10-11-12-18M92 29c6-5 10-11 12-18" stroke-width="2.1"/>
		<path d="M88 70c-7-2-13-6-18-12M92 112c8-2 14-6 19-12M88 154c-7-2-13-6-18-12" stroke-width="2.1"/>

		<!-- hand-inked C -->
		<path d="M88 65c-11-11-28-12-39-3-13 10-17 31-10 48 8 18 26 27 42 20 7-3 12-8 16-15"/>
		<path d="M83 72c-8-5-17-4-23 2-8 8-9 22-4 32 6 11 17 16 28 12" stroke-width="1.8"/>

		<!-- hand-inked D -->
		<path d="M91 64c9-7 23-8 33-2 15 9 22 26 19 43-3 18-16 31-33 34-7 1-14 0-20-3"/>
		<path d="M99 73c8-4 17-3 23 3 9 8 12 20 9 31-3 11-11 19-22 22" stroke-width="1.8"/>

		<!-- tapered calligraphic tails -->
		<path d="M89 184c-7 7-13 14-18 23M92 184c7 7 13 14 18 23" stroke-width="2.1"/>
		<path d="M69 207c8-2 15-1 21 2 6-3 13-4 21-2" stroke-width="1.6"/>
	</g>
</svg>
