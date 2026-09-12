<?php
/**
 * Монограмма CD — декоративный знак бренда.
 * Буквы — текстом в шрифте заголовков: читаемо и без ручных кривых.
 */
defined( 'ABSPATH' ) || exit;

$class = isset( $args['class'] ) ? $args['class'] : '';
?>
<svg class="monogram <?php echo esc_attr( $class ); ?>" viewBox="0 0 140 180" fill="none" aria-hidden="true" focusable="false">
	<defs>
		<linearGradient id="cd-silver" x1="0" y1="0" x2="0" y2="1">
			<stop offset="0%" stop-color="var(--mark-1)" stop-opacity=".95"/>
			<stop offset="50%" stop-color="var(--mark-2)" stop-opacity=".9"/>
			<stop offset="100%" stop-color="var(--mark-3)" stop-opacity=".9"/>
		</linearGradient>
	</defs>

	<g stroke="url(#cd-silver)" stroke-width="1.6" fill="none" stroke-linecap="square">
		<path d="M70 6 76 17 70 28 64 17z"/>
		<path d="M70 28V150"/>
		<path d="M70 174l-6-12 6-12 6 12z"/>
		<path d="M28 52h84"/>
		<path d="M22 52l6-7 6 7-6 7zM118 52l-6-7-6 7 6 7z"/>
	</g>

	<text x="70" y="122" fill="url(#cd-silver)"
		font-family="Manrope, -apple-system, 'Segoe UI', sans-serif"
		font-size="86" font-weight="400" letter-spacing="-10"
		text-anchor="middle">CD</text>
</svg>
