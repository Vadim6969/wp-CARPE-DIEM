#!/usr/bin/env bash
# Быстрая проверка сайта: все ключевые страницы отвечают 200 и без PHP-ошибок.
# Запуск: bash themes/carpediem/tools/smoke.sh [базовый-адрес]

set -u

BASE="${1:-http://localhost:8888}"
FAILED=0

check() {
	local path="$1" want="${2:-200}"
	local body code problems

	body=$(curl -s -L "$BASE$path")
	code=$(curl -s -o /dev/null -w '%{http_code}' "$BASE$path")
	problems=$(printf '%s' "$body" | grep -ciE 'fatal error|parse error|Warning: |Notice: ')

	if [ "$code" != "$want" ] || [ "$problems" != "0" ]; then
		printf '  ✗ %-46s код=%s (ждали %s) php-ошибок=%s\n' "$path" "$code" "$want" "$problems"
		FAILED=1
	else
		printf '  ✓ %-46s код=%s\n' "$path" "$code"
	fi
}

echo "Проверяем $BASE"

check /
check /catalog/
check /product-category/hoodie/
check /product-category/tshirt/
check /product/split-camo-thermochromic/
check /cart/
check /favorites/
check /my-account/
check "/?s=%D1%85%D1%83%D0%B4%D0%B8"
check /about/
check /contacts/
check /delivery/
check /returns/
check /size-guide/
check /oferta/
check /privacy-policy/
check /wp-sitemap.xml
check /no-such-page/ 404

# Каталог не должен опустеть: проверяем, что карточки на месте.
count=$(curl -s -L "$BASE/catalog/" | grep -c 'woocommerce-loop-product__title')
if [ "$count" -lt 1 ]; then
	echo "  ✗ в каталоге нет ни одного товара"
	FAILED=1
else
	printf '  ✓ %-46s товаров на странице=%s\n' "каталог не пустой" "$count"
fi

# В упрощённом каталоге не должно быть фильтров, сортировки и лишних действий в карточке.
catalog_markup=$(curl -s -L "$BASE/catalog/")
if printf '%s' "$catalog_markup" | grep -qE 'class="filters|woocommerce-ordering|loop-buy|loop-sizes|loop-favorite|loop-card__cat'; then
	echo "  ✗ в упрощённом каталоге остались фильтры, сортировка или лишние элементы карточек"
	FAILED=1
else
	printf '  ✓ %-46s\n' "каталог без фильтров и лишних действий"
fi

if [ "$FAILED" = "0" ]; then
	echo "Всё в порядке."
else
	echo "Есть проблемы — смотри строки с ✗."
fi

exit $FAILED
