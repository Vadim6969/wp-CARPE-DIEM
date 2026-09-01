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

mark='<g stroke="#8e8a83" stroke-width="2" fill="none" stroke-linecap="square">
		<path d="M600 470 610 488 600 506 590 488z"/><path d="M600 506v150"/><path d="M540 552h120"/></g>'

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
		<text x="600" y="640" fill="#cfcac1" font-family="Georgia, serif" font-size="120" text-anchor="middle" letter-spacing="-8">CD</text>
		<text x="600" y="760" fill="#e8e6e3" font-family="Georgia, serif" font-size="46" text-anchor="middle" letter-spacing="6">'"$l1"'</text>
		<text x="600" y="816" fill="#e8e6e3" font-family="Georgia, serif" font-size="46" text-anchor="middle" letter-spacing="6">'"$l2"'</text>
		<text x="600" y="890" fill="#7c7975" font-family="Helvetica, Arial, sans-serif" font-size="20" text-anchor="middle" letter-spacing="10">'"$cat"'</text>
	</svg>'

	# 02 — «крупно»: только знак
	render "$DIR/$slug/02" '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1200" width="1200" height="1200">
		<defs><radialGradient id="r" cx="50%" cy="45%" r="70%">
			<stop offset="0%" stop-color="#24242a"/><stop offset="100%" stop-color="'"$tone"'"/></radialGradient></defs>
		<rect width="1200" height="1200" fill="url(#r)"/>
		<text x="600" y="700" fill="#b4afa7" font-family="Georgia, serif" font-size="300" text-anchor="middle" letter-spacing="-24">CD</text>
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
