<?php
/**
 * Импорт фотографий товаров из папки темы в медиатеку.
 *
 * Структура: themes/carpediem/photos/<слаг-товара>/01.jpg, 02.jpg, …
 * Первый файл по алфавиту становится главным фото, остальные — галереей.
 * Повторный запуск пропускает уже загруженные файлы (сверяем по имени в мета-поле).
 *
 * Запуск: wp eval-file wp-content/themes/carpediem/tools/import-photos.php
 *
 * @package carpediem
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$root = get_theme_file_path( 'photos' );

if ( ! is_dir( $root ) ) {
	WP_CLI::error( "Нет папки {$root}. Создайте её и положите фото по подпапкам со слагами товаров." );
}

$allowed  = array( 'jpg', 'jpeg', 'png', 'webp' );
$imported = 0;
$skipped  = 0;

foreach ( glob( $root . '/*', GLOB_ONLYDIR ) as $dir ) {
	$slug    = basename( $dir );
	$product = get_page_by_path( $slug, OBJECT, 'product' );

	if ( ! $product ) {
		WP_CLI::warning( "Товар со слагом «{$slug}» не найден — папка пропущена" );
		continue;
	}

	$files = array_values( array_filter( glob( $dir . '/*' ), function ( $file ) use ( $allowed ) {
		return in_array( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ), $allowed, true );
	} ) );

	sort( $files, SORT_NATURAL );

	if ( ! $files ) {
		continue;
	}

	$attachment_ids = array();

	foreach ( $files as $file ) {
		$source = $slug . '/' . basename( $file );

		// Уже загружали этот файл — берём существующее вложение.
		$existing = get_posts( array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_carpediem_source',   // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $source,               // phpcs:ignore WordPress.DB.SlowDBQuery
		) );

		if ( $existing ) {
			$attachment_ids[] = (int) $existing[0];
			++$skipped;
			continue;
		}

		// media_handle_sideload перемещает файл, поэтому работаем с копией.
		$tmp = wp_tempnam( basename( $file ) );
		copy( $file, $tmp );

		$attachment_id = media_handle_sideload(
			array(
				'name'     => basename( $file ),
				'tmp_name' => $tmp,
			),
			$product->ID,
			$product->post_title
		);

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
			WP_CLI::warning( "{$source}: " . $attachment_id->get_error_message() );
			continue;
		}

		update_post_meta( $attachment_id, '_carpediem_source', $source );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $product->post_title );

		$attachment_ids[] = (int) $attachment_id;
		++$imported;
	}

	if ( ! $attachment_ids ) {
		continue;
	}

	$featured = array_shift( $attachment_ids );
	set_post_thumbnail( $product->ID, $featured );
	update_post_meta( $product->ID, '_product_image_gallery', implode( ',', $attachment_ids ) );

	WP_CLI::log( sprintf( '%s: главное #%d, в галерее %d', $slug, $featured, count( $attachment_ids ) ) );
}

/* --- Обложки категорий: берём главное фото первого товара категории --- */
$covers = 0;

foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true ) ) as $term ) {
	if ( get_term_meta( $term->term_id, 'thumbnail_id', true ) ) {
		continue;
	}

	$products = get_posts( array(
		'post_type'      => 'product',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'tax_query'      => array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $term->term_id ) ),
	) );

	if ( ! $products ) {
		continue;
	}

	$thumb = get_post_thumbnail_id( $products[0] );

	if ( $thumb ) {
		update_term_meta( $term->term_id, 'thumbnail_id', $thumb );
		WP_CLI::log( "категория «{$term->name}»: обложка #{$thumb}" );
		++$covers;
	}
}

WP_CLI::success( "Загружено файлов: {$imported}, пропущено (уже были): {$skipped}, обложек категорий: {$covers}" );
