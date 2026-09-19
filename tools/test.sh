#!/usr/bin/env bash
# phpRS 3 - kouřový test: čistá instalace do dočasné kopie a průchod hlavními stránkami.
# Spouští se lokálně i v GitHub Actions. Databázi bere z proměnných prostředí:
#   DB_HOST (127.0.0.1) DB_PORT (3306) DB_NAME (phprs3_test) DB_USER (root) DB_PASS (prázdné) PORT (8099)
# Databáze DB_NAME se při testu SMAŽE a vytvoří znovu.
set -euo pipefail

KOREN="$(cd "$(dirname "$0")/.." && pwd)"
DB_HOST="${DB_HOST:-127.0.0.1}"; DB_PORT="${DB_PORT:-3306}"; DB_NAME="${DB_NAME:-phprs3_test}"; DB_USER="${DB_USER:-root}"; DB_PASS="${DB_PASS:-}"; PORT="${PORT:-8099}"
PRACE="$(mktemp -d)"; JAR="$PRACE/cookies.txt"; B="http://127.0.0.1:$PORT"; CHYB=0
uklid() { [ -n "${SERVER_PID:-}" ] && kill "$SERVER_PID" 2>/dev/null || true; rm -rf "$PRACE"; }
trap uklid EXIT

echo "== syntaxe PHP"
find "$KOREN" -name '*.php' -not -path '*/.git/*' -not -path '*/dist/*' -print0 | xargs -0 -n1 php -l > /dev/null

echo "== čistá databáze a kopie projektu"
MYSQL=(mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER"); [ -n "$DB_PASS" ] && MYSQL+=(-p"$DB_PASS")
"${MYSQL[@]}" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci"
mkdir "$PRACE/web" && (cd "$KOREN" && git ls-files -z --cached --others --exclude-standard | tar --null -T - -cf - | tar -xf - -C "$PRACE/web")
mkdir -p "$PRACE/web/media" "$PRACE/web/storage/log" "$PRACE/web/storage/cache"
(cd "$PRACE/web" && exec php -S "127.0.0.1:$PORT" system/dev-router.php > "$PRACE/server.log" 2>&1) & SERVER_PID=$!
for i in $(seq 1 30); do curl -s -o /dev/null "$B/install.php" && break; sleep 0.3; done

over() { # over <popis> <očekávaný kód> <adresa> [hledaný text]
  local kod; kod=$(curl -s -b "$JAR" -c "$JAR" -A "Mozilla/5.0 test" -o "$PRACE/odpoved" -w '%{http_code}' "$B$3")
  if [ "$kod" != "$2" ] || grep -qE 'Fatal error|Warning:|Deprecated:|Notice:' "$PRACE/odpoved" || { [ -n "${4:-}" ] && ! grep -q "$4" "$PRACE/odpoved"; }; then
    echo "  CHYBA  $1 ($3): kód $kod, čekal jsem $2${4:+, text „$4“}"; CHYB=$((CHYB+1))
  else echo "  ok     $1"; fi
}

echo "== instalace"
HESLO="Test-$(date +%s)-heslo"
curl -s -o "$PRACE/odpoved" -X POST "$B/install.php" --data-urlencode "db_host=$DB_HOST" -d "db_port=$DB_PORT" -d "db_name=$DB_NAME" -d "db_user=$DB_USER" --data-urlencode "db_password=$DB_PASS" -d db_prefix=rs_ \
  --data-urlencode "nazev_webu=Testovací magazín" -d user=admin -d jmeno=Tester -d email= --data-urlencode "password=$HESLO" --data-urlencode "password2=$HESLO" -d prostredi=2026 -d layout=classic-newspaper
grep -q "Hotovo, magazín běží" "$PRACE/odpoved" || { echo "  CHYBA  instalace selhala"; sed 's/<[^>]*>//g' "$PRACE/odpoved" | grep -v '^\s*$' | head -20; exit 1; }
echo "  ok     instalace"

echo "== web"
over "hlavní stránka" 200 / "Testovací magazín"
over "článek" 200 /clanek/vitejte-v-phprs-3 "Vítejte"
over "rubrika" 200 /rubrika/aktuality
over "hledání" 200 "/hledani?q=phpRS"
for u in /rss.xml /feed.json /sitemap.xml /sitemap-news.xml /robots.txt /llms.txt /clanek/vitejte-v-phprs-3.md; do over "$u" 200 "$u"; done
over "neexistující stránka" 404 /tohle-neexistuje
over "system/ není přístupný" 403 /system/sql/schema.sql
over "config.php není přístupný" 403 /config.php
for l in default modern-magazine; do
  "${MYSQL[@]}" "$DB_NAME" -e "UPDATE rs_config SET hodnota='$l' WHERE promenna='layout'"; over "šablona $l" 200 /; over "šablona $l – článek" 200 /clanek/vitejte-v-phprs-3
done

echo "== administrace"
over "bez přihlášení je jen login" 200 /admin.php "Heslo"
TOKEN=$(grep -o 'name="_csrf" value="[a-f0-9]*"' "$PRACE/odpoved" | head -1 | sed 's/.*value="//;s/"//')
kod=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' -X POST "$B/admin.php" -d "_csrf=$TOKEN" -d user=admin -d password=spatne-heslo-123); [ "$kod" = 401 ] && echo "  ok     špatné heslo odmítnuto" || { echo "  CHYBA  špatné heslo: $kod"; CHYB=$((CHYB+1)); }
kod=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' -X POST "$B/admin.php" -d user=admin --data-urlencode "password=$HESLO"); [ "$kod" = 400 ] && echo "  ok     POST bez CSRF odmítnut" || { echo "  CHYBA  CSRF: $kod"; CHYB=$((CHYB+1)); }
curl -s -b "$JAR" -c "$JAR" -o /dev/null -X POST "$B/admin.php" -d "_csrf=$TOKEN" -d user=admin --data-urlencode "password=$HESLO"
"${MYSQL[@]}" "$DB_NAME" -e "INSERT INTO rs_config VALUES ('rozsireni','novinky,komentare,ankety,statistika,presmerovani,reklama') ON DUPLICATE KEY UPDATE hodnota=VALUES(hodnota)"
over "přehled" 200 /admin.php "Přehled"
for m in clanky "clanky&akce=novy" "clanky&akce=kalendar" intergal topic stranky news comment ankety stat reklama vzhled "bloky&schema=1" users presmerovani protokol; do over "modul $m" 200 "/admin.php?modul=$m"; done
for z in zakladni vzhled seo mereni cookies rozsireni zalohy stav; do over "nastavení/$z" 200 "/admin.php?modul=config&zalozka=$z"; done
over "neznámý modul" 403 "/admin.php?modul=neexistuje"
over "vizuální editor bloků" 200 "/?upravit=1" "rs-nastaveni"
kod=$(curl -s -o "$PRACE/odpoved" -w '%{http_code}' "$B/?upravit=1"); grep -q "rs-nastaveni" "$PRACE/odpoved" && { echo "  CHYBA  vizuální editor je vidět bez přihlášení"; CHYB=$((CHYB+1)); } || echo "  ok     vizuální editor jen pro přihlášené"

if [ -s "$PRACE/web/storage/log/chyby.log" ]; then echo "== záznam chyb aplikace:"; cat "$PRACE/web/storage/log/chyby.log"; CHYB=$((CHYB+1)); fi
echo; [ "$CHYB" -eq 0 ] && echo "VŠE V POŘÁDKU" || { echo "NALEZENO CHYB: $CHYB"; exit 1; }
