#!/usr/bin/env bash
#
# Wdrożenie na CloudPanel (serwer z WP-CLI i Composerem, bez Node).
#  1. lokalnie: build motywu (Vite),
#  2. archiwum: pliki z gita (HEAD) + zbudowane zasoby motywu,
#  3. serwer: rsync do katalogu strony (bez .env, uploads, vendor i pakietów z Composera),
#     composer install --no-dev, języki, cache.
#
# Użycie: scripts/deploy.sh
# Dane serwera w pliku .deploy.env (poza gitem) albo w zmiennych środowiska:
#   HOST=<alias SSH>  USER_STRONY=<użytkownik strony>  CEL=<katalog strony na serwerze>
#
set -euo pipefail

cd "$(dirname "$0")/.."
# shellcheck disable=SC1091
[[ -f .deploy.env ]] && source .deploy.env
HOST="${HOST:?Ustaw HOST (alias SSH serwera) w .deploy.env}"
USER_STRONY="${USER_STRONY:?Ustaw USER_STRONY w .deploy.env}"
CEL="${CEL:?Ustaw CEL (katalog strony) w .deploy.env}"
SSH=(ssh -o ClearAllForwardings=yes "$HOST")

if [[ -n "$(git status --porcelain)" ]]; then
  echo "Najpierw zatwierdź zmiany (git commit): wdrażamy dokładnie to, co jest w HEAD." >&2
  exit 1
fi

echo "› Build motywu"
(cd web/app/themes/przystan && npm ci --no-audit --no-fund --silent && npm run build --silent)

echo "› Archiwum $(git rev-parse --short HEAD)"
ARCH="$(mktemp -t przystan-XXXX).tar.gz"
git archive --format=tar --prefix=wydanie/ HEAD > "${ARCH%.gz}"
tar -rf "${ARCH%.gz}" --transform 's,^,wydanie/,' web/app/themes/przystan/public/build
gzip -f "${ARCH%.gz}"

echo "› Wysyłka"
scp -q -o ClearAllForwardings=yes "$ARCH" "$HOST:/tmp/przystan-wydanie.tar.gz"
rm -f "$ARCH"

"${SSH[@]}" "sudo -u $USER_STRONY -H bash -s" <<SKRYPT
set -euo pipefail
cd "$CEL"
rm -rf /tmp/przystan-wydanie && mkdir -p /tmp/przystan-wydanie
tar -xzf /tmp/przystan-wydanie.tar.gz -C /tmp/przystan-wydanie
rsync -a --delete \
  --exclude='.env' --exclude='/vendor/' --exclude='/web/wp/' --exclude='/web/app/uploads/' \
  --exclude='/web/app/cache/' --exclude='/web/app/languages/' \
  --exclude='/web/app/plugins/advanced-custom-fields/' --exclude='/web/app/plugins/polylang/' \
  --exclude='/web/app/mu-plugins/bedrock-disallow-indexing/' \
  --exclude='/web/app/themes/przystan/vendor/' \
  /tmp/przystan-wydanie/wydanie/ "$CEL/"
rm -rf /tmp/przystan-wydanie

echo "› Composer"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet
(cd web/app/themes/przystan && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet)

echo "› WordPress"
if wp core is-installed 2>/dev/null; then
  wp language core install pl_PL en_GB --quiet || true
  wp language plugin install advanced-custom-fields polylang pl_PL --quiet || true
  wp acorn optimize:clear >/dev/null 2>&1 || true
  wp acorn view:cache >/dev/null 2>&1 || true
  wp cache flush --quiet
  wp rewrite flush --quiet
fi
echo "› Serwer gotowy"
SKRYPT

"${SSH[@]}" "sudo rm -f /tmp/przystan-wydanie.tar.gz"
echo "Wdrożono $(git rev-parse --short HEAD) na $HOST:$CEL"
