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
check /collections/
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

# Страница доставки не должна возвращать прежние условия оплаты при получении.
delivery_markup=$(curl -s -L "$BASE/delivery/")
if ! printf '%s' "$delivery_markup" | grep -q 'СДЭК и Почтой России' || ! printf '%s' "$delivery_markup" | grep -q 'сервис «Долями»' || ! printf '%s' "$delivery_markup" | grep -q 'защищённую страницу оплаты'; then
	echo "  ✗ на странице доставки нет актуальных условий доставки и оплаты"
	FAILED=1
else
	printf '  ✓ %-46s\n' "условия доставки и оплаты обновлены"
fi

# Инструкция по снятию мерок должна содержать новые пояснения и помощь с выбором размера.
size_guide_markup=$(curl -s -L "$BASE/size-guide/")
if ! printf '%s' "$size_guide_markup" | grep -q 'на ровной поверхности' || ! printf '%s' "$size_guide_markup" | grep -q 'полученное значение на 2' || ! printf '%s' "$size_guide_markup" | grep -q 'НЕ УВЕРЕНЫ В РАЗМЕРЕ?'; then
	echo "  ✗ на странице таблицы размеров нет актуальной инструкции по меркам"
	FAILED=1
else
	printf '  ✓ %-46s\n' "инструкция по снятию мерок обновлена"
fi

# Страница и ссылка в подвале должны быть названы по её содержанию, а не общей таблицей.
home_markup=$(curl -s -L "$BASE/")
footer_size_guide_link="href=\"${BASE}/size-guide/\">Как снять мерки</a>"
if ! printf '%s' "$size_guide_markup" | grep -Fq '<h1 class="entry-title">Как снять мерки</h1>' || ! printf '%s' "$home_markup" | grep -Fq "$footer_size_guide_link"; then
	echo "  ✗ страница и ссылка в подвале не переименованы в «Как снять мерки»"
	FAILED=1
else
	printf '  ✓ %-46s\n' "страница и ссылка в подвале переименованы"
fi

# Страница бренда должна содержать актуальный манифест и его ключевые разделы.
about_markup=$(curl -s -L "$BASE/about/")
if ! printf '%s' "$about_markup" | grep -q 'не откладывает жизнь на потом' || ! printf '%s' "$about_markup" | grep -q 'НАША ФИЛОСОФИЯ' || ! printf '%s' "$about_markup" | grep -q 'МЫ ВСЕ ОДИНАКОВЫЕ В ОДНОМ'; then
	echo "  ✗ на странице «О нас» нет актуального манифеста бренда"
	FAILED=1
else
	printf '  ✓ %-46s\n' "манифест бренда на странице «О нас» обновлён"
fi

# Пустое избранное и страница лукбука должны использовать актуальные тексты.
favorites_markup=$(curl -s -L "$BASE/favorites/")
if ! printf '%s' "$favorites_markup" | grep -q 'Добавьте в избранное то, что вам понравилось' || ! printf '%s' "$favorites_markup" | grep -q 'Перейти в каталог →'; then
	echo "  ✗ на странице избранного нет актуального пустого состояния"
	FAILED=1
else
	printf '  ✓ %-46s\n' "пустое состояние избранного обновлено"
fi

lookbook_markup=$(curl -s -L "$BASE/lookbook/")
if ! printf '%s' "$lookbook_markup" | grep -q 'как ты создаешь свой стиль' || ! printf '%s' "$lookbook_markup" | grep -q 'следующая фотография здесь — твоя' || ! printf '%s' "$lookbook_markup" | grep -q '@carpediem.department' || ! printf '%s' "$lookbook_markup" | grep -q 'Становись частью нашей с тобой истории.'; then
	echo "  ✗ на странице LOOKBOOK нет актуального текста сообщества"
	FAILED=1
else
	printf '  ✓ %-46s\n' "текст страницы LOOKBOOK обновлён"
fi

# Главная подборка должна быть компактной: только фото, название и цена.
selection_markup=$(curl -s -L "$BASE/")
if ! printf '%s' "$selection_markup" | grep -q 'home-selection' || ! printf '%s' "$selection_markup" | grep -q 'woocommerce-loop-product__title' || ! printf '%s' "$selection_markup" | grep -q 'class="price"' || printf '%s' "$selection_markup" | grep -q 'loop-card__cat\|loop-sizes\|loop-buy\|loop-favorite'; then
	echo "  ✗ популярные товары содержат лишние элементы"
	FAILED=1
else
	printf '  ✓ %-46s\n' "популярные товары показывают фото, название и цену"
fi

# Отдельная страница коллекций и пункт верхнего меню ведут к тем же категориям.
collections_markup=$(curl -s -L "$BASE/collections/")
if ! printf '%s' "$collections_markup" | grep -q 'collections-page' || ! printf '%s' "$collections_markup" | grep -q 'cat-card'; then
	echo "  ✗ на странице коллекций нет карточек категорий"
	FAILED=1
elif ! printf '%s' "$collections_markup" | grep -Fq "${BASE}/collections/"; then
	echo "  ✗ пункт верхнего меню не ведёт на страницу коллекций"
	FAILED=1
else
	printf '  ✓ %-46s\n' "страница коллекций и ссылка меню работают"
fi

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

# Карточка товара объясняет выбор вариации, не предлагает заказ по телефону и упрощает рекомендации.
product_markup=$(curl -s -L "$BASE/product/split-camo-thermochromic/")
open_info_count=$(printf '%s' "$product_markup" | grep -oE '<details class="info-col"[^>]* open' | wc -l | tr -d ' ')
if ! printf '%s' "$product_markup" | grep -q 'commerce-page-hero--product' || ! printf '%s' "$product_markup" | grep -q 'product-selection-note'; then
	echo "  ✗ на карточке товара нет брендовой плашки или подсказки выбора"
	FAILED=1
elif [ "$open_info_count" -lt 3 ]; then
	echo "  ✗ информационные блоки товара не открыты по умолчанию"
	FAILED=1
elif printf '%s' "$product_markup" | grep -qE 'one-click|quick_order'; then
	echo "  ✗ на карточке товара остался заказ по телефону"
	FAILED=1
elif printf '%s' "$product_markup" | grep -qE 'loop-buy|loop-sizes|loop-favorite|loop-card__cat'; then
	echo "  ✗ в рекомендациях товара остались лишние действия"
	FAILED=1
elif ! printf '%s' "$product_markup" | grep -Fq "class=\"size-guide-link\" href=\"${BASE}/size-guide/\" target=\"_blank\" rel=\"noopener\">Как снять мерки"; then
	echo "  ✗ в карточке товара нет ссылки «Как снять мерки» в новой вкладке"
	FAILED=1
elif [[ "$product_markup" != *'size-table__note'*'size-guide-link'* ]]; then
	echo "  ✗ ссылка «Как снять мерки» расположена не под таблицей размеров"
	FAILED=1
else
	printf '  ✓ %-46s\n' "карточка товара и рекомендации упрощены"
fi

# Заголовок корзины использует ту же брендовую атмосферную плашку.
cart_markup=$(curl -s -L "$BASE/cart/")
if ! printf '%s' "$cart_markup" | grep -q 'commerce-page-hero--cart'; then
	echo "  ✗ в корзине нет брендовой плашки"
	FAILED=1
else
	printf '  ✓ %-46s\n' "брендовая плашка корзины на месте"
fi

# Полоса перед футером должна рассказывать о философии бренда, а не о сервисных преимуществах.
philosophy_markup=$(curl -s -L "$BASE/")
if ! printf '%s' "$philosophy_markup" | grep -q 'class="usp philosophy"' || ! printf '%s' "$philosophy_markup" | grep -q 'Наша философия'; then
	echo "  ✗ перед подвалом нет блока философии бренда"
	FAILED=1
else
	printf '  ✓ %-46s\n' "философия бренда перед подвалом"
fi

# Главная использует фотоподборку, металлический логотип и кресты 01/08.
branding_markup=$(curl -s -L "$BASE/")
if ! printf '%s' "$branding_markup" | grep -q 'favicon.png' || ! printf '%s' "$branding_markup" | grep -q 'hero-gallery' || ! printf '%s' "$branding_markup" | grep -q 'hero-gallery/garage.jpg' || ! printf '%s' "$branding_markup" | grep -q 'brand-monogram-line.png' || ! printf '%s' "$branding_markup" | grep -q 'brand-logo-metallic.png' || ! printf '%s' "$branding_markup" | grep -q 'marquee__cross--classic' || ! printf '%s' "$branding_markup" | grep -q 'marquee__cross--massive' || ! printf '%s' "$branding_markup" | grep -q 'site-header__home-logo' || [ "$(printf '%s' "$branding_markup" | grep -o 'announcement__cross brand-cross brand-cross--classic' | wc -l | tr -d '[:space:]')" -ne 2 ]; then
	echo "  ✗ шапка, фотоподборка и брендовые знаки отображаются не полностью"
	FAILED=1
else
	printf '  ✓ %-46s\n' "шапка, фотоподборка, логотип и кресты подключены"
fi

# На гостевой странице аккаунта доступны обе операции, но показывается только выбранная форма.
account_markup=$(curl -s -L "$BASE/my-account/")
if ! printf '%s' "$account_markup" | grep -q 'account-auth-switcher' || ! printf '%s' "$account_markup" | grep -q 'woocommerce-form-register'; then
	echo "  ✗ в личном кабинете нет переключателя входа и регистрации"
	FAILED=1
elif ! printf '%s' "$account_markup" | grep -q 'id="reg_password"'; then
	echo "  ✗ покупатель не может задать пароль при регистрации"
	FAILED=1
else
	printf '  ✓ %-46s\n' "вход, регистрация и свой пароль доступны"
fi

if [ "$FAILED" = "0" ]; then
	echo "Всё в порядке."
else
	echo "Есть проблемы — смотри строки с ✗."
fi

exit $FAILED
