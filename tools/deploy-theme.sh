#!/usr/bin/env bash
# Синхронизирует тему из клона Git в установленный WordPress.
set -euo pipefail

if [ "$#" -ne 1 ]; then
	echo "Использование: bash tools/deploy-theme.sh /путь/к/wordpress" >&2
	exit 1
fi

PROJECT_ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
SOURCE="$PROJECT_ROOT/themes/carpediem/"
WP_ROOT="${1%/}"
TARGET="$WP_ROOT/wp-content/themes/carpediem/"

if [ ! -f "$WP_ROOT/wp-config.php" ] || [ ! -d "$WP_ROOT/wp-content/themes" ]; then
	echo "Не найден WordPress по пути: $WP_ROOT" >&2
	exit 1
fi

if ! command -v rsync >/dev/null 2>&1; then
	echo "Для обновления нужен rsync. Используй установку темы через ZIP из README." >&2
	exit 1
fi

mkdir -p "$TARGET"
rsync -a --delete --exclude '.DS_Store' --exclude '/photos/' "$SOURCE" "$TARGET"

echo "Тема CARPE DIEM обновлена: $TARGET"
