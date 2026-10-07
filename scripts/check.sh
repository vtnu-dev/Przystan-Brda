#!/usr/bin/env bash
#
# Testy dymne działającej strony (lokalnie albo na produkcji).
# Użycie: scripts/check.sh [BASE_URL]   (domyślnie http://localhost:8810)
#
set -uo pipefail

BASE="${1:-http://localhost:8810}"
BLEDY=0

ok() { printf '  \033[32m✓\033[0m %s\n' "$1"; }
zle() { printf '  \033[31m✗\033[0m %s\n' "$1"; BLEDY=$((BLEDY + 1)); }

kod() { curl -s -o /dev/null -w '%{http_code}' "$BASE$1"; }
sprawdz_kod() {
  local wynik
  wynik=$(kod "$1")
  [[ "$wynik" == "$2" ]] && ok "$1 → $wynik" || zle "$1 → $wynik (oczekiwano $2)"
}
zawiera() {
  local html
  html=$(curl -s "$BASE$1") # najpierw całość: grep -q z pipefail dawałby fałszywe błędy (SIGPIPE)
  grep -q -- "$2" <<<"$html" && ok "$1 zawiera: $3" || zle "$1 nie zawiera: $3"
}

echo "Strona: $BASE"

echo "Adresy"
for adres in / /inwestycje/ /inwestycje/przystan-brda/ /inwestycje/willa-lipowa/ /inwestycje/tarasy-myslecinek/ \
  /mieszkania/ /mieszkania/m-35/ /mieszkania/l-21/ /okolica/ /kontakt/ /ulubione/ /dziennik-budowy/ /polityka-prywatnosci/ \
  /en/ /en/developments/ /en/apartments/ /en/apartments/m35/ /en/neighbourhood/ /en/contact/ /en/favourites/ /en/construction-diary/; do
  sprawdz_kod "$adres" 200
done
# Mapa strony działa na produkcji; lokalnie (WP_ENV=development) Bedrock wyłącza indeksowanie i mapę.
[[ "$BASE" == https://* ]] && sprawdz_kod /wp-sitemap.xml 200
sprawdz_kod /tej-strony-nie-ma/ 404
sprawdz_kod /en/home/ 301

echo "SEO"
zawiera / 'name=.robots. content=.noindex' 'meta robots noindex (strona demo)'
zawiera / 'hreflang="en"' 'hreflang EN'
zawiera /mieszkania/m-35/ '"@type":"Apartment"' 'JSON-LD Apartment'
zawiera /mieszkania/m-35/ '"@type":"Offer"' 'JSON-LD Offer'
zawiera /mieszkania/m-35/ '<link rel="canonical"' 'canonical'
zawiera /mieszkania/m-35/ 'property="og:image"' 'Open Graph'
zawiera /en/ '<html lang="en-GB"' 'lang="en-GB" na wersji EN'
zawiera /en/contact/ 'Sales office' 'tłumaczenia wtyczki i motywu na EN'
zawiera /mieszkania/ '2 inwestycje' 'polskie formy liczby mnogiej'
zawiera /kontakt/ 'poniedziałek-piątek' 'polskie znaki w polach wielowierszowych (preg_split z /u)'
zawiera /okolica/ 'podwyższoną izolacją' 'polskie znaki na liście standardu'

echo "REST API"
zawiera '/wp-json/przystan/v1/mieszkania?status=wolne&pokoje[]=3' '"liczba":' 'wyszukiwarka zwraca JSON'
sprawdz_kod '/wp-json/przystan/v1/mieszkania?pietro=9' 400
sprawdz_kod '/wp-json/przystan/v1/zapytania' 404 # GET nie istnieje, tylko POST
wynik=$(curl -s -X POST "$BASE/wp-json/przystan/v1/zapytania" -d 'imie=Test' -d 'email=test@example.com' -d 'zgoda=1')
grep -q '"ok":false' <<<"$wynik" && ok 'POST bez podpisanego czasu jest odrzucany' || zle "POST bez znacznika czasu: $wynik"

echo "Bezpieczeństwo"
sprawdz_kod /wp-json/wp/v2/users 404
[[ "$(kod /xmlrpc.php)" =~ ^(403|404|405)$ ]] && ok '/xmlrpc.php zablokowany' || zle "/xmlrpc.php → $(kod /xmlrpc.php)"
sprawdz_kod '/?author=1' 301
naglowki=$(curl -s -I "$BASE/")
grep -qi '^permissions-policy:' <<<"$naglowki" && ok 'nagłówek Permissions-Policy' || zle 'brak Permissions-Policy'
if [[ "$BASE" == https://* ]]; then
  grep -qi '^content-security-policy:' <<<"$naglowki" && ok 'nagłówek CSP' || zle 'brak CSP'
  grep -qi '^x-content-type-options: nosniff' <<<"$naglowki" && ok 'nosniff' || zle 'brak nosniff'
fi
grep -q 'name="generator"' <<<"$(curl -s "$BASE/")" && zle 'wersja WordPressa widoczna w kodzie' || ok 'bez meta generator'

echo
if ((BLEDY > 0)); then
  echo "Błędów: $BLEDY"
  exit 1
fi
echo "Wszystko OK"
