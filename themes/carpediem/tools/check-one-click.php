<?php
/**
 * Проверка валидации «купить в 1 клик».
 * Запуск: wp eval-file wp-content/themes/carpediem/tools/check-one-click.php
 */

defined( 'ABSPATH' ) || exit;

$cases = array(
	array( 'Пётр', '+7 900 555-11-22', true, null ),
	array( 'П', '+7 900 555-11-22', true, 'Укажи имя.' ),
	array( '  ', '+7 900 555-11-22', true, 'Укажи имя.' ),
	array( 'Пётр', '12345', true, 'Проверь номер телефона.' ),
	array( 'Пётр', '+7 900 555-11-22-33-44-55', true, 'Проверь номер телефона.' ),
	array( 'Пётр', '89005551122', true, null ),
	array( 'Пётр', '+7 900 555-11-22', false, 'Нужно согласие на обработку данных.' ),
);

foreach ( $cases as $i => $case ) {
	list( $name, $phone, $consent, $expected ) = $case;
	$actual = carpediem_one_click_validate( $name, $phone, $consent );

	if ( $actual !== $expected ) {
		WP_CLI::error( sprintf( 'Кейс %d: ждали %s, получили %s', $i, var_export( $expected, true ), var_export( $actual, true ) ) );
	}
}

WP_CLI::success( sprintf( 'Валидация «в 1 клик»: %d кейсов пройдено', count( $cases ) ) );
