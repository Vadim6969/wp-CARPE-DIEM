# CARPE DIEM 2026 — локальная разработка

WordPress + WooCommerce в Docker через [`@wordpress/env`](https://www.npmjs.com/package/@wordpress/env).
Код в репозитории — только тема `themes/carpediem` (монтируется в `wp-content/themes/carpediem`).

## Запуск

```bash
npx @wordpress/env start
```

Сайт: http://localhost:8888 · Админка: http://localhost:8888/wp-admin (`admin` / `password`)

Другие команды: `stop`, `destroy` (снести БД), `run cli -- wp <команда>` (WP-CLI).

## Наполнение с нуля

После `destroy` восстановить магазин можно тремя скриптами (порядок важен):

```bash
npx @wordpress/env run cli -- wp eval-file wp-content/themes/carpediem/tools/setup-store.php
npx @wordpress/env run cli -- wp eval-file wp-content/themes/carpediem/tools/seed-demo.php
npx @wordpress/env run cli -- wp eval-file wp-content/themes/carpediem/tools/seed-content.php
```

- `setup-store.php` — классические корзина и чекаут, зоны доставки, оплата при получении, промокод `CARPE10`, тексты про ПДн, настройки писем.
- `seed-demo.php` — атрибуты и 10 демо-товаров с вариациями и cross-sells.
- `seed-content.php` — тексты служебных страниц.

Проверка логики «купить в 1 клик»:

```bash
npx @wordpress/env run cli -- wp eval-file wp-content/themes/carpediem/tools/check-one-click.php
```

## Если npx падает с EACCES

В `~/.npm` лежат файлы, принадлежащие root (старый баг npm). Одноразовый фикс (нужен пароль):

```bash
sudo chown -R $(id -u):$(id -g) ~/.npm
```

## Что уже сделано

Локаль `ru_RU` · валюта RUB (`12 990 ₽`) · ЧПУ `/%postname%/` · тема `carpediem` активна ·
главная, каталог, карточка товара, корзина, чекаут, «купить в 1 клик» ·
доставка (Россия 490 ₽, бесплатно от 15 000 ₽, самовывоз, мир 2 900 ₽) · промокоды ·
согласие на обработку ПДн с фиксацией даты и IP · SEO-мета, Open Graph, фавикон · тексты страниц.

Фото товаров — тёмная заглушка с монограммой. Загружаются в админке, код менять не нужно.

## Осталось до запуска

1. **Онлайн-оплата** (ЮKassa) — нужны реквизиты юрлица и договор.
2. **SMTP** — в контейнере нет почтового сервера, письма не уходят. На хостинге настроить отправку с домена.
3. **Шрифты** — сейчас с Google CDN, перед запуском положить `.woff2` в `assets/fonts/` (см. `ponytail:` в `functions.php`).
4. **Аналитика** — Яндекс.Метрика, нужен номер счётчика.
5. **Фото и финальные тексты**, включая юридические (оферта, политика — сейчас черновики).
6. **Хостинг**: перенос, HTTPS, бэкапы, кэш на стороне сервера.
