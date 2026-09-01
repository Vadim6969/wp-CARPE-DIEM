# CARPE DIEM 2026 — локальная разработка

WordPress + WooCommerce в Docker через [`@wordpress/env`](https://www.npmjs.com/package/@wordpress/env).
Код в репозитории — только тема `themes/carpediem` (монтируется в `wp-content/themes/carpediem`).

## Запуск

```bash
npx @wordpress/env start
```

Сайт: http://localhost:8888 · Админка: http://localhost:8888/wp-admin (`admin` / `password`)

Другие команды: `stop`, `destroy` (снести БД), `run cli -- wp <команда>` (WP-CLI).

## Если npx падает с EACCES

В `~/.npm` лежат файлы, принадлежащие root (старый баг npm). Одноразовый фикс (нужен пароль):

```bash
sudo chown -R $(id -u):$(id -g) ~/.npm
```

Либо разово подставлять свой кэш: `npm_config_cache=./.npm-cache npx @wordpress/env start`

## Что уже настроено

Локаль `ru_RU` · валюта RUB (`12 990 ₽`) · склад Москва · ЧПУ `/%postname%/` · тема `carpediem` активна ·
страницы: Главная (front page), Каталог, Корзина, Оформление заказа, Мой аккаунт.
В базе есть один тестовый товар «Тестовый худи» — удалить, когда появятся настоящие.
