#!/usr/bin/env bash
# Фирменные заглушки вместо фотографий: по три «кадра» на товар, 900×1200 (3:4).
# Нужны только пока нет реальных снимков — как появятся, папку photos/ чистим и кладём их.
# Запуск: bash themes/carpediem/tools/make-placeholder-photos.sh

set -eu

DIR="$(cd "$(dirname "$0")/.." && pwd)/photos"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

# слаг | строка 1 | строка 2 | категория | тон фона
ITEMS='
split-camo-thermochromic|ЗИП-ХУДИ SPLIT CAMO|THERMOCHROMIC|ХУДИ|#17161a
hoodie-fck-the-crisis|ХУДИ|F*CK THE CRISIS|ХУДИ|#141418
hoodie-split-camo|ХУДИ|SPLIT CAMO|ХУДИ|#191713
tshirt-bitches-money|ФУТБОЛКА|BITCHES MONEY|ФУТБОЛКИ|#151517
tshirt-no-poverty|ФУТБОЛКА|NO POVERTY|ФУТБОЛКИ|#131316
sweatshirt-fck-the-crisis|СВИТШОТ|F*CK THE CRISIS|СВИТШОТЫ|#16161b
bag-logo-cross-shoulder|СУМКА LOGO CROSS|SHOULDER BAG|СУМКИ|#121215
cap-logo-cross|КЕПКА|LOGO CROSS|АКСЕССУАРЫ|#141416
pendant-cross|ПОДВЕСКА|CROSS PENDANT|АКСЕССУАРЫ|#101013
chain-logo|ЦЕПЬ|LOGO CHAIN|АКСЕССУАРЫ|#111114
'

render() {                       # render <файл-без-расширения> <svg>
	printf '%s' "$2" > "$TMP/frame.svg"
	qlmanage -t -s 1200 -o "$TMP" "$TMP/frame.svg" >/dev/null 2>&1
	sips -c 1200 900 "$TMP/frame.svg.png" --out "$TMP/frame-crop.png" >/dev/null 2>&1
	# В JPEG — как будут приходить настоящие снимки, и в разы легче PNG.
	sips -s format jpeg -s formatOptions 80 "$TMP/frame-crop.png" --out "$1.jpg" >/dev/null 2>&1
	rm -f "$TMP/frame.svg.png" "$TMP/frame-crop.png"
}

mark='<g transform="translate(402 170) scale(2.2)" stroke="#8e8a83" stroke-width="3.1" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M91 22c-8 12-8 24-2 36 7 15 7 27 1 40-7 16-7 29 0 43 7 14 7 27 0 43"/>
		<path d="M89 29c-6-5-10-11-12-18M92 29c6-5 10-11 12-18M88 70c-7-2-13-6-18-12M92 112c8-2 14-6 19-12M88 154c-7-2-13-6-18-12" stroke-width="2.1"/>
		<path d="M88 65c-11-11-28-12-39-3-13 10-17 31-10 48 8 18 26 27 42 20 7-3 12-8 16-15"/>
		<path d="M83 72c-8-5-17-4-23 2-8 8-9 22-4 32 6 11 17 16 28 12" stroke-width="1.8"/>
		<path d="M91 64c9-7 23-8 33-2 15 9 22 26 19 43-3 18-16 31-33 34-7 1-14 0-20-3"/>
		<path d="M99 73c8-4 17-3 23 3 9 8 12 20 9 31-3 11-11 19-22 22" stroke-width="1.8"/>
		<path d="M89 184c-7 7-13 14-18 23M92 184c7 7 13 14 18 23M69 207c8-2 15-1 21 2 6-3 13-4 21-2" stroke-width="2.1"/>
	</g>'

mark_large='<g transform="translate(312 120) scale(3.2)" stroke="#b4afa7" stroke-width="3.1" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M91 22c-8 12-8 24-2 36 7 15 7 27 1 40-7 16-7 29 0 43 7 14 7 27 0 43"/>
		<path d="M89 29c-6-5-10-11-12-18M92 29c6-5 10-11 12-18M88 70c-7-2-13-6-18-12M92 112c8-2 14-6 19-12M88 154c-7-2-13-6-18-12" stroke-width="2.1"/>
		<path d="M88 65c-11-11-28-12-39-3-13 10-17 31-10 48 8 18 26 27 42 20 7-3 12-8 16-15"/>
		<path d="M83 72c-8-5-17-4-23 2-8 8-9 22-4 32 6 11 17 16 28 12" stroke-width="1.8"/>
		<path d="M91 64c9-7 23-8 33-2 15 9 22 26 19 43-3 18-16 31-33 34-7 1-14 0-20-3"/>
		<path d="M99 73c8-4 17-3 23 3 9 8 12 20 9 31-3 11-11 19-22 22" stroke-width="1.8"/>
		<path d="M89 184c-7 7-13 14-18 23M92 184c7 7 13 14 18 23M69 207c8-2 15-1 21 2 6-3 13-4 21-2" stroke-width="2.1"/>
	</g>'

echo "Рисуем заглушки в $DIR"

printf '%s\n' "$ITEMS" | while IFS='|' read -r slug l1 l2 cat tone; do
	[ -z "$slug" ] && continue
	mkdir -p "$DIR/$slug"

	# 01 — «общий план»: название и монограмма
	render "$DIR/$slug/01" '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1200" width="1200" height="1200">
		<defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="1">
			<stop offset="0%" stop-color="'"$tone"'"/><stop offset="100%" stop-color="#08080a"/></linearGradient></defs>
		<rect width="1200" height="1200" fill="url(#g)"/>
		'"$mark"'
		<text x="600" y="760" fill="#e8e6e3" font-family="Georgia, serif" font-size="46" text-anchor="middle" letter-spacing="6">'"$l1"'</text>
		<text x="600" y="816" fill="#e8e6e3" font-family="Georgia, serif" font-size="46" text-anchor="middle" letter-spacing="6">'"$l2"'</text>
		<text x="600" y="890" fill="#7c7975" font-family="Helvetica, Arial, sans-serif" font-size="20" text-anchor="middle" letter-spacing="10">'"$cat"'</text>
	</svg>'

	# 02 — «крупно»: только знак
	render "$DIR/$slug/02" '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1200" width="1200" height="1200">
		<defs><radialGradient id="r" cx="50%" cy="45%" r="70%">
			<stop offset="0%" stop-color="#24242a"/><stop offset="100%" stop-color="'"$tone"'"/></radialGradient></defs>
		<rect width="1200" height="1200" fill="url(#r)"/>
		'"$mark_large"'
		<text x="600" y="800" fill="#6f6c68" font-family="Helvetica, Arial, sans-serif" font-size="18" text-anchor="middle" letter-spacing="12">CARPE DIEM 2026</text>
	</svg>'

	# 03 — «деталь»: орнамент из крестов
	render "$DIR/$slug/03" '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1200" width="1200" height="1200">
		<rect width="1200" height="1200" fill="'"$tone"'"/>
		<g stroke="#3a3a42" stroke-width="2" fill="none">
			<path d="M300 380v160M240 440h120M600 300v260M520 380h160M900 380v160M840 440h120
			         M300 720v160M240 780h120M600 700v260M520 780h160M900 720v160M840 780h120"/>
		</g>
		<text x="600" y="620" fill="#8e8a83" font-family="Georgia, serif" font-size="34" text-anchor="middle" letter-spacing="10">'"$cat"'</text>
	</svg>'

	echo "  $slug — 3 кадра"
done

echo "Готово. Импорт: npx @wordpress/env run cli -- wp eval-file wp-content/themes/carpediem/tools/import-photos.php"
